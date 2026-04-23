<?php

namespace App\Controllers;

use App\Models\TrainingModel;
use App\Models\PortfolioDocumentModel;
use CodeIgniter\HTTP\ResponseInterface;

class PortfolioController extends BaseController
{
    protected $trainingModel;
    protected $docModel;
    protected $db_personnel;
    protected $session;

    public function __construct()
    {
        $this->session = session();
        if (!$this->session->get('isLoggedIn')) {
            service('response')->redirect(base_url('login'))->send();
            exit;
        }

        $this->trainingModel = new TrainingModel();
        $this->docModel = new PortfolioDocumentModel();
        $this->db_personnel = db_connect('personnel');
        helper(['url', 'form', 'text', 'date']);
    }

    public function index()
    {
        // Track page visit
        helper('recent_pages');
        track_recent_page('portfolio', 'ประวัติการอบรมและผลงาน', 'bi-person-workspace');

        $person_id = $this->session->get('person_id');
        
        // Handle Fiscal Year & Round Filter
        $currentYear = (int)date('Y') + 543;
        $currentMonth = (int)date('n');
        
        // Default FY & Round
        $defaultFiscalYear = ($currentMonth >= 10) ? $currentYear + 1 : $currentYear;
        $defaultRound = ($currentMonth >= 10 || $currentMonth <= 3) ? 1 : 2;
        $defaultFilter = "{$defaultFiscalYear}-{$defaultRound}";
        
        $selectedFilter = $this->request->getGet('filter') ?: $defaultFilter;
        list($selectedYear, $selectedRound) = explode('-', $selectedFilter);
        $selectedYear = (int)$selectedYear;
        $selectedRound = (int)$selectedRound;
        
        // Calculate Date Range
        $adYear = $selectedYear - 543;
        if ($selectedRound == 1) {
            $startDate = ($adYear - 1) . "-10-01";
            $endDate = $adYear . "-03-31";
            $dateLabel = "1 ต.ค. " . ($selectedYear - 1) . " - 31 มี.ค. " . $selectedYear;
        } else {
            $startDate = $adYear . "-04-01";
            $endDate = $adYear . "-09-30";
            $dateLabel = "1 เม.ย. " . $selectedYear . " - 30 ก.ย. " . $selectedYear;
        }

        $data['title'] = "แฟ้มสะสมผลงานและประวัติการอบรม";
        $data['selectedFilter'] = $selectedFilter;
        $data['dateLabel'] = $dateLabel;
        $data['selectedRound'] = $selectedRound;
        $data['selectedYear'] = $selectedYear;
        
        // Fetch All Dates to Determine Available Fiscal Years
        $trainingDates = $this->trainingModel->select('train_start_date as d')->where('pers_id', $person_id)->findAll();
        $docDates = $this->docModel->select('doc_date as d')->where('pers_id', $person_id)->findAll();
        
        $uniqueRounds = [];
        // Add current FY-Round by default
        $uniqueRounds[$defaultFilter] = true;
        
        foreach (array_merge($trainingDates, $docDates) as $row) {
            $d = $row['d'] ?? null;
            if (!$d) continue;
            
            $time = strtotime($d);
            $y = (int)date('Y', $time) + 543;
            $m = (int)date('n', $time);
            
            $fy = ($m >= 10) ? $y + 1 : $y;
            $rnd = ($m >= 10 || $m <= 3) ? 1 : 2;
            $uniqueRounds["{$fy}-{$rnd}"] = true;
        }
        
        // Convert to sorted filter options
        $data['filterOptions'] = [];
        $keys = array_keys($uniqueRounds);
        rsort($keys);
        
        foreach ($keys as $key) {
            list($fy, $rnd) = explode('-', $key);
            $fy = (int)$fy;
            if ($rnd == 1) {
                $lbl = "ปีงบประมาณ {$fy} ครั้งที่ 1 (1 ต.ค. " . ($fy - 1) . " - 31 มี.ค. {$fy})";
            } else {
                $lbl = "ปีงบประมาณ {$fy} ครั้งที่ 2 (1 เม.ย. {$fy} - 30 ก.ย. {$fy})";
            }
            $data['filterOptions'][] = ['value' => $key, 'label' => $lbl];
        }

        // Fetch Training History (Filtered)
        $data['trainings'] = $this->trainingModel
            ->where('pers_id', $person_id)
            ->where('train_start_date >=', $startDate)
            ->where('train_start_date <=', $endDate)
            ->orderBy('train_start_date', 'DESC')
            ->findAll();
            
        // Fetch Academic Work & Images (Filtered)
        $data['documents'] = $this->docModel
            ->where('pers_id', $person_id)
            ->whereIn('doc_category', ['ผลงานวิชาการ', 'รูปภาพกิจกรรม'])
            ->where('doc_date >=', $startDate)
            ->where('doc_date <=', $endDate)
            ->orderBy('doc_date', 'DESC')
            ->findAll();

        $db_skj = db_connect('skj');
        
        // Fetch User Info (Position, etc.)
        $user = $this->db_personnel->table('tb_personnel')
            ->select('tb_personnel.*, ' . $db_skj->database . '.tb_position.posi_name')
            ->join($db_skj->database . '.tb_position', $db_skj->database . '.tb_position.posi_id = tb_personnel.pers_position', 'left')
            ->where('pers_id', $person_id)
            ->get()->getRow();
        
        $data['user_info'] = $user;
        
        // Remote Server Base URL
        $data['remote_base_url'] = "https://skj.nsnpao.go.th/uploads";

        return view('teacher/portfolio/index', $data);
    }

    public function saveTraining()
    {
        $post = $this->request->getPost();
        $person_id = $this->session->get('person_id');
        
        $data = [
            'pers_id'          => $person_id,
            'train_name'       => $post['train_name'],
            'train_location'   => $post['train_location'],
            'train_start_date' => $this->_formatDate($post['train_start_date']),
            'train_end_date'   => $this->_formatDate($post['train_end_date']),
            'train_hours'      => $post['train_hours'],
        ];

        // Handle Certificate Upload
        $fileReady = $this->request->getPost('file_name_ready');
        if (!empty($fileReady)) {
            $data['train_certificate'] = $fileReady;
        } else {
            $file = $this->request->getFile('train_certificate');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $uploadPath = "personnel/teacher/training/{$person_id}";
                $safeName = "Cert_" . time() . "." . $file->getExtension();
                $uploadResult = $this->_uploadFileToServer($file, $uploadPath, $safeName);
                
                if ($uploadResult['status'] === 'success') {
                    $data['train_certificate'] = $uploadResult['filename'];
                } else {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'อัปโหลดเกียรติบัตรไม่สำเร็จ: ' . $uploadResult['message']]);
                }
            }
        }

        if (!empty($post['id'])) {
            $this->trainingModel->update($post['id'], $data);
            $msg = 'แก้ไขข้อมูลการอบรมสำเร็จ';
        } else {
            $this->trainingModel->insert($data);
            $msg = 'เพิ่มข้อมูลการอบรมสำเร็จ';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    public function saveDocument()
    {
        $post = $this->request->getPost();
        $person_id = $this->session->get('person_id');
        
        $data = [
            'pers_id'      => $person_id,
            'doc_category' => $post['doc_category'],
            'doc_title'    => $post['doc_title'],
            'doc_date'     => $this->_formatDate($post['doc_date']),
            'doc_note'     => $post['doc_note'],
            'uploaded_by'  => $person_id
        ];

        $file = $this->request->getFile('portfolio_file');
        // Handle Portfolio File Upload
        $fileReady = $this->request->getPost('file_name_ready');
        if (!empty($fileReady)) {
            $data['file_name'] = $fileReady;
            $data['file_path'] = "personnel/teacher/portfolio/{$person_id}/{$fileReady}";
            $data['file_type'] = $this->request->getPost('file_type');
            $data['file_size'] = $this->request->getPost('file_size');
        } else {
            $file = $this->request->getFile('portfolio_file');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $uploadPath = "personnel/teacher/portfolio/{$person_id}";
                $safeName = "Port_" . time() . "." . $file->getExtension();
                $uploadResult = $this->_uploadFileToServer($file, $uploadPath, $safeName);
                
                if ($uploadResult['status'] === 'success') {
                    $data['file_name'] = $uploadResult['filename'];
                    $data['file_path'] = $uploadPath . "/" . $uploadResult['filename'];
                    $data['file_type'] = $file->getMimeType();
                    $data['file_size'] = $file->getSize();
                } else {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'อัปโหลดไฟล์ผลงานไม่สำเร็จ: ' . $uploadResult['message']]);
                }
            }
        }

        if (!empty($post['id'])) {
            $this->docModel->update($post['id'], $data);
            $msg = 'แก้ไขข้อมูลผลงานสำเร็จ';
        } else {
            $this->docModel->insert($data);
            $msg = 'เพิ่มข้อมูลผลงานสำเร็จ';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    public function deleteTraining($id)
    {
        $training = $this->trainingModel->find($id);
        if ($training && $training['pers_id'] == $this->session->get('person_id')) {
            if (!empty($training['train_certificate'])) {
                $remotePath = "personnel/teacher/training/{$training['pers_id']}/{$training['train_certificate']}";
                $this->_deleteFileFromServer($remotePath);
            }
            $this->trainingModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'ลบข้อมูลการอบรมสำเร็จ']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลหรือคุณไม่มีสิทธิ์']);
    }

    public function deleteDocument($id)
    {
        $doc = $this->docModel->find($id);
        if ($doc && $doc['pers_id'] == $this->session->get('person_id')) {
            if (!empty($doc['file_name'])) {
                $this->_deleteFileFromServer($doc['file_path']);
            }
            $this->docModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'ลบข้อมูลผลงานสำเร็จ']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลหรือคุณไม่มีสิทธิ์']);
    }

    public function uploadChunk()
    {
        // Set CORS headers for AJAX requests
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200);
        }

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

    private function _formatDate($dateStr)
    {
        if (empty($dateStr)) return null;
        // Assume dd-mm-yyyy or yyyy-mm-dd
        return date('Y-m-d', strtotime($dateStr));
    }

    private function _uploadFileToServer($file, $remotePath, $originalName)
    {
        $uploadUrl = env('upload.server.url');
        if (!$uploadUrl) {
            return ['status' => 'error', 'message' => 'ไม่พบ URL สำหรับอัปโหลดใน .env'];
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
            $bodyRaw = $response->getBody();
            $body = json_decode($bodyRaw, true);

            if ($statusCode === 200 && isset($body['status']) && $body['status'] === 'success') {
                return ['status' => 'success', 'filename' => $body['filename']];
            }

            $errorMsg = $body['message'] ?? 'เกิดข้อผิดพลาดไม่ทราบสาเหตุ (Status: ' . $statusCode . ')';
            if ($body === null) {
                $errorMsg = 'Server ตอบกลับไม่ใช่ JSON: ' . substr(strip_tags($bodyRaw), 0, 100);
            }
            
            return ['status' => 'error', 'message' => $errorMsg];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'CURL Error: ' . $e->getMessage()];
        }
    }

    private function _deleteFileFromServer($remoteFilePath)
    {
        $deleteUrl = env('upload.server.delete.url');
        try {
            $client = \Config\Services::curlrequest();
            $jsonData = json_encode(['path' => dirname($remoteFilePath), 'files' => [basename($remoteFilePath)]]);
            $client->setBody($jsonData)->post($deleteUrl, ['headers' => ['Content-Type' => 'application/json'], 'http_errors' => false]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
