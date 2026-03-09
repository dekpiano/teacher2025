<?php

namespace App\Controllers;

use App\Models\PerformanceEvaluationModel;
use CodeIgniter\HTTP\ResponseInterface;

class PerformanceEvaluationController extends BaseController
{
    protected $evaluationModel;
    protected $db;
    protected $db_personnel;
    protected $db_skj;
    protected $session;

    public function __construct()
    {
        $this->session = session();
        if (!$this->session->get('isLoggedIn')) {
            service('response')->redirect(base_url('login'))->send();
            exit;
        }

        helper(['url', 'text', 'form']);
        $this->evaluationModel = new PerformanceEvaluationModel();
        $this->db = db_connect();
        $this->db_personnel = db_connect('personnel');
        $this->db_skj = db_connect('skj');
    }

    /**
     * Get current Thai Fiscal Year and Round
     */
    private function getCurrentFiscalInfo()
    {
        $month = (int)date('n');
        $year = (int)date('Y') + 543;

        if ($month >= 10) {
            $fiscalYear = $year + 1;
            $round = 1; // Oct - Mar (Part of the new FY)
        } else if ($month <= 3) {
            $fiscalYear = $year;
            $round = 1; // Oct - Mar
        } else {
            $fiscalYear = $year;
            $round = 2; // Apr - Sep
        }

        return ['year' => $fiscalYear, 'round' => $round];
    }

    public function index($year = null, $round = null)
    {
        if (!$this->_checkPermission()) {
            return redirect()->to('home')->with('error', 'ระบบนี้เฉพาะตำแหน่งครูผู้ช่วยขึ้นไปเท่านั้น');
        }
        $fiscalInfo = $this->getCurrentFiscalInfo();
        $year = $year ?? $fiscalInfo['year'];
        $round = $round ?? $fiscalInfo['round'];

        $person_id = $this->session->get('person_id');
        
        $data['title'] = "การประเมินผลการปฏิบัติงานข้าราชการหรือพนักงานครูและบุคลากรทางการศึกษาองค์กรปกครองส่วนท้องถิ่น สายงานการสอน ตำแหน่งครู";
        $data['current_year'] = $year;
        $data['current_round'] = $round;
        $data['evaluation'] = $this->evaluationModel->getEvaluation($person_id, $year, $round);
        
        // History of evaluations for this teacher
        $data['history'] = $this->evaluationModel->where('eva_teacher_id', $person_id)
                                                ->orderBy('eva_year', 'DESC')
                                                ->orderBy('eva_round', 'DESC')
                                                ->findAll();

        return view('teacher/evaluation/evaluation_main', $data);
    }

    public function upload()
    {
        if (!$this->_checkPermission()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์ใช้งานระบบนี้']);
        }

        $post = $this->request->getPost();
        $isChunked = !empty($post['eva_file_name_ready']);

        $rules = [
            'eva_year'  => 'required',
            'eva_round' => 'required',
        ];

        if (!$isChunked) {
            $rules['eva_file'] = [
                'rules' => 'uploaded[eva_file]|ext_in[eva_file,pdf]|max_size[eva_file,20480]',
                'errors' => [
                    'uploaded' => 'กรุณาเลือกไฟล์ PDF',
                    'ext_in'   => 'อนุญาตเฉพาะไฟล์ PDF เท่านั้น',
                    'max_size' => 'ไฟล์ต้องมีขนาดไม่เกิน 20MB'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => implode(', ', $this->validator->getErrors())
            ]);
        }

        $teacherId = $this->session->get('person_id');
        $year = $post['eva_year'];
        $round = $post['eva_round'];

        // Check if already exists
        $existing = $this->evaluationModel->getEvaluation($teacherId, $year, $round);
        
        $uploadBasePath = 'academic/teacher/evaluation';
        $remoteUploadPath = "{$uploadBasePath}/{$year}/{$round}";
        
        $insertData = [
            'eva_teacher_id' => $teacherId,
            'eva_year'       => $year,
            'eva_round'      => $round,
            'eva_status'     => 'ส่งแล้ว'
        ];

        // If file was pre-uploaded via chunks
        if (!empty($post['eva_file_name_ready'])) {
            // Delete old file if updating
            if ($existing && !empty($existing['eva_file'])) {
                $oldPath = "{$remoteUploadPath}/{$existing['eva_file']}";
                $this->_deleteFileFromServer($oldPath);
            }
            $insertData['eva_file'] = $post['eva_file_name_ready'];
        } 
        // Standard single-file upload (backup)
        else {
            $file = $this->request->getFile('eva_file');
            // Use teacher name or ID as part of filename
            $safeName = "PA_{$year}_{$round}_{$teacherId}_" . time() . ".pdf";

            $uploadResult = $this->_uploadFileToServer($file, $remoteUploadPath, $safeName);

            if ($uploadResult['status'] !== 'success') {
                return $this->response->setJSON($uploadResult);
            }

            // Delete old file if updating
            if ($existing && !empty($existing['eva_file'])) {
                $oldPath = "{$remoteUploadPath}/{$existing['eva_file']}";
                $this->_deleteFileFromServer($oldPath);
            }
            $insertData['eva_file'] = $uploadResult['filename'];
        }

        if ($existing) {
            $result = $this->evaluationModel->update($existing['eva_id'], $insertData);
        } else {
            $result = $this->evaluationModel->insert($insertData);
        }

        if ($result) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'อัปโหลดไฟล์สำเร็จ']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถบันทึกข้อมูลลงฐานข้อมูลได้ (อาจยังไม่ได้สร้างตาราง)']);
        }
    }

    /**
     * Proxies file chunks from browser to the remote upload server
     */
    public function uploadChunk()
    {
        $file = $this->request->getFile('file');
        $post = $this->request->getPost();
        
        $uploadUrl = env('upload.server.url');
        $client = \Config\Services::curlrequest();

        try {
            $postData = [
                'path'     => $post['path'],
                'filename' => $post['filename'],
                'chunk'    => $post['chunk'],
                'chunks'   => $post['chunks'],
                'file'     => new \CURLFile($file->getTempName(), $file->getMimeType(), $post['filename'])
            ];

            $response = $client->post($uploadUrl, [
                'multipart' => $postData,
                'http_errors' => false
            ]);

            return $this->response->setStatusCode($response->getStatusCode())->setBody($response->getBody());
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function _checkPermission()
    {
        $teacherId = $this->session->get('person_id');
        if (empty($teacherId)) return false;

        $user = $this->db_personnel->table('tb_personnel')
            ->select('tb_personnel.*, ' . $this->db_skj->database . '.tb_position.posi_name')
            ->join($this->db_skj->database . '.tb_position', $this->db_skj->database . '.tb_position.posi_id = tb_personnel.pers_position', 'left')
            ->where('pers_id', $teacherId)
            ->get()
            ->getRowArray();

        if (!$user) return false;

        $position = $user['posi_name'] ?? '';
        
        // Allowed positions: Assistant Teacher and above (Thai names)
        $allowedPositions = [
            'ครูผู้ช่วย',
            'ครู',
            'ครูชำนาญการ',
            'ครูชำนาญการพิเศษ',
            'ครูเชี่ยวชาญ',
            'ครูเชี่ยวชาญพิเศษ',
            'ผู้อำนวยการโรงเรียน',
            'รองผู้อำนวยการโรงเรียน',
            'ผู้อำนวยการสถานศึกษา',
            'รองผู้อำนวยการสถานศึกษา'
        ];

        // If user is admin (usually ID 'admin' or something)
        if ($this->session->get('person_id') === 'admin') return true;

        foreach ($allowedPositions as $allowed) {
            if (mb_strpos($position, $allowed) !== false) {
                return true;
            }
        }

        return false;
    }

    private function _uploadFileToServer($file, $remotePath, $originalName)
    {
        $uploadUrl = env('upload.server.url');
        if (!$uploadUrl) {
            return ['status' => 'error', 'message' => 'Upload server URL is not configured in .env file.'];
        }

        try {
            $client = \Config\Services::curlrequest();
            $postData = [
                'path' => $remotePath,
                'file' => new \CURLFile($file->getTempName(), $file->getMimeType(), $originalName)
            ];

            $response = $client->post($uploadUrl, [
                'multipart' => $postData,
                'http_errors' => false
            ]);

            $statusCode = $response->getStatusCode();
            $rawBody = $response->getBody();
            $body = json_decode($rawBody, true);

            if ($statusCode === 200 && isset($body['status']) && $body['status'] === 'success') {
                return ['status' => 'success', 'filename' => $body['filename']];
            } else {
                if ($body === null) {
                    $errorMessage = "Server error (Status: {$statusCode}). Response: " . substr(strip_tags($rawBody), 0, 100);
                } else {
                    $errorMessage = $body['message'] ?? 'An unknown error occurred during file upload.';
                }
                return ['status' => 'error', 'message' => "ไม่สามารถอัปโหลดไฟล์ได้: {$errorMessage}"];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Upload Error: ' . $e->getMessage()];
        }
    }

    private function _deleteFileFromServer($remoteFilePath)
    {
        $deleteUrl = env('upload.server.delete.url');
        if (!$deleteUrl) return false;

        try {
            $client = \Config\Services::curlrequest();
            $jsonData = json_encode([
                'path' => dirname($remoteFilePath),
                'files' => [basename($remoteFilePath)]
            ]);
            $client->setBody($jsonData)->post($deleteUrl, [
                'headers' => ['Content-Type' => 'application/json'],
                'http_errors' => false
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
