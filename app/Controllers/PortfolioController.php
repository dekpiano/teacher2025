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
        
        $db_academic = db_connect();
        $compDates = $db_academic->table('tb_competitions')
            ->select('comp_date as d')
            ->where('comp_status', 'อนุมัติแล้ว')
            ->where("comp_teacher_ids LIKE '%\"{$person_id}\"%'")
            ->get()->getResultArray();
        
        $uniqueRounds = [];
        // Add current FY-Round by default
        $uniqueRounds[$defaultFilter] = true;
        
        foreach (array_merge($trainingDates, $docDates, $compDates) as $row) {
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
        $documentsList = $this->docModel
            ->where('pers_id', $person_id)
            ->whereIn('doc_category', ['ผลงานวิชาการ', 'รูปภาพกิจกรรม'])
            ->where('doc_date >=', $startDate)
            ->where('doc_date <=', $endDate)
            ->orderBy('doc_date', 'DESC')
            ->findAll();

        $documentsMapped = [];
        foreach ($documentsList as $doc) {
            $documentsMapped[] = [
                'id'             => $doc['id'],
                'doc_category'   => $doc['doc_category'],
                'doc_title'      => $doc['doc_title'],
                'doc_date'       => $doc['doc_date'],
                'doc_note'       => $doc['doc_note'],
                'file_name'      => $doc['file_name'],
                'file_path'      => $doc['file_path'],
                'file_size'      => $doc['file_size'],
                'is_competition' => false
            ];
        }

        // Fetch competitions from skjacth_academic.tb_competitions where teacher ID is present
        $compList = $db_academic->table('tb_competitions')
            ->where('comp_date >=', $startDate)
            ->where('comp_date <=', $endDate)
            ->where('comp_status', 'อนุมัติแล้ว')
            ->where("comp_teacher_ids LIKE '%\"{$person_id}\"%'")
            ->orderBy('comp_date', 'DESC')
            ->get()->getResult();

        foreach ($compList as $comp) {
            $certs = json_decode($comp->comp_certificate_files, true) ?: [];
            $imgs = json_decode($comp->comp_images, true) ?: [];
            $filePath = '';
            if (!empty($certs)) {
                $filePath = "https://skj.nsnpao.go.th/uploads/academic/competitions/certificates/" . $certs[0];
            } elseif (!empty($imgs)) {
                $filePath = "https://skj.nsnpao.go.th/uploads/academic/competitions/images/" . $imgs[0];
            }

            $awards = json_decode($comp->comp_awards, true) ?: [];
            $awardText = !empty($awards) ? implode(', ', $awards) : 'ไม่มีการระบุรางวัล';

            $documentsMapped[] = [
                'id'             => $comp->comp_id,
                'doc_category'   => 'ผลงานการแข่งขัน (งานวิชาการ)',
                'doc_title'      => $comp->comp_name . ' - ' . $comp->comp_activity,
                'doc_date'       => $comp->comp_date,
                'doc_note'       => "รางวัล: " . $awardText . "\nระดับ: " . $comp->comp_level . "\nสถานที่: " . ($comp->comp_location ?: '-'),
                'file_name'      => basename($filePath),
                'file_path'      => $filePath,
                'file_size'      => 0,
                'is_competition' => true
            ];
        }

        // Sort merged array by date in descending order
        usort($documentsMapped, function($a, $b) {
            return strtotime($b['doc_date']) - strtotime($a['doc_date']);
        });

        $data['documents'] = $documentsMapped;

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
            $oldTraining = $this->trainingModel->find($post['id']);
            if ($oldTraining && !empty($oldTraining['train_certificate']) && !empty($data['train_certificate']) && $oldTraining['train_certificate'] !== $data['train_certificate']) {
                $remotePath = "personnel/teacher/training/{$oldTraining['pers_id']}/{$oldTraining['train_certificate']}";
                $this->_deleteFileFromServer($remotePath);
            }
            if ($this->trainingModel->update($post['id'], $data) === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่สามารถแก้ไขข้อมูลการอบรมได้: ' . implode(', ', $this->trainingModel->errors() ?: ['เกิดข้อผิดพลาดของระบบ'])
                ]);
            }
            $msg = 'แก้ไขข้อมูลการอบรมสำเร็จ';
        } else {
            if ($this->trainingModel->insert($data) === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่สามารถบันทึกข้อมูลการอบรมได้: ' . implode(', ', $this->trainingModel->errors() ?: ['เกิดข้อผิดพลาดของระบบ'])
                ]);
            }
            $msg = 'เพิ่มข้อมูลการอบรมสำเร็จ';
        }

        $filter = $this->_calculateFilter($data['train_start_date']);
        return $this->response->setJSON(['status' => 'success', 'message' => $msg, 'filter' => $filter]);
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
            $oldDoc = $this->docModel->find($post['id']);
            if ($oldDoc && !empty($oldDoc['file_name']) && !empty($data['file_name']) && $oldDoc['file_name'] !== $data['file_name']) {
                $this->_deleteFileFromServer($oldDoc['file_path']);
            }
            if ($this->docModel->update($post['id'], $data) === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่สามารถแก้ไขข้อมูลผลงานได้: ' . implode(', ', $this->docModel->errors() ?: ['เกิดข้อผิดพลาดของระบบ'])
                ]);
            }
            $msg = 'แก้ไขข้อมูลผลงานสำเร็จ';
        } else {
            if ($this->docModel->insert($data) === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่สามารถบันทึกข้อมูลผลงานได้: ' . implode(', ', $this->docModel->errors() ?: ['เกิดข้อผิดพลาดของระบบ'])
                ]);
            }
            $msg = 'เพิ่มข้อมูลผลงานสำเร็จ';
        }

        $filter = $this->_calculateFilter($data['doc_date']);
        return $this->response->setJSON(['status' => 'success', 'message' => $msg, 'filter' => $filter]);
    }

    public function getCompetitionDetail($id)
    {
        $db_academic = db_connect();
        $comp = $db_academic->table('tb_competitions')->where('comp_id', $id)->get()->getRow();
        if (!$comp) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูล'], 404);
        }

        // Fetch students
        $students = [];
        $studentIds = json_decode($comp->comp_student_ids, true) ?: [];
        if (!empty($studentIds)) {
            $students = $db_academic->table('tb_students')
                ->select('StudentID, StudentCode, StudentPrefix, StudentFirstName, StudentLastName, StudentClass, StudentNumber')
                ->whereIn('StudentID', $studentIds)
                ->get()
                ->getResult();
        }

        // Fetch teachers
        $teachers = [];
        $teacherIds = json_decode($comp->comp_teacher_ids, true) ?: [];
        if (!empty($teacherIds)) {
            $teachers = $this->db_personnel->table('tb_personnel')
                ->select('pers_id, pers_prefix, pers_firstname, pers_lastname, pers_img')
                ->whereIn('pers_id', $teacherIds)
                ->get()
                ->getResult();
        }

        // Translate date to Thai format
        $thaiDate = '';
        if ($comp->comp_date) {
            $date = strtotime($comp->comp_date);
            $day = date('j', $date);
            $months = ["", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
            $month = $months[date('n', $date)];
            $year = date('Y', $date) + 543;
            $thaiDate = "$day $month $year";
        }

        return $this->response->setJSON([
            'status'    => 'success',
            'comp'      => $comp,
            'thaiDate'  => $thaiDate,
            'students'  => $students,
            'teachers'  => $teachers,
            'awards'    => json_decode($comp->comp_awards) ?: [],
            'certs'     => json_decode($comp->comp_certificate_files) ?: [],
            'images'    => json_decode($comp->comp_images) ?: []
        ]);
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
                'path'         => $post['path'],
                'filename'     => $post['filename'],
                'chunk_index'  => $post['chunk'],
                'total_chunks' => $post['chunks'],
                'file'         => new \CURLFile($file->getTempName(), $file->getMimeType(), $post['filename'])
            ];

            $headers = [
                'X-Auth-Token' => env('upload.server.token') ?: 'Dekpiano2025!!'
            ];

            $response = $client->post($uploadUrl, [
                'multipart' => $postData,
                'headers' => $headers,
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
            
            $headers = [
                'X-Auth-Token' => env('upload.server.token') ?: 'Dekpiano2025!!'
            ];

            $response = $client->post($uploadUrl, [
                'multipart' => $postData, 
                'headers' => $headers,
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
            $headers = [
                'Content-Type' => 'application/json',
                'X-Auth-Token' => env('upload.server.token') ?: 'Dekpiano2025!!'
            ];
            $client->setBody($jsonData)->post($deleteUrl, [
                'headers' => $headers,
                'http_errors' => false
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function _calculateFilter($dateStr)
    {
        if (empty($dateStr)) return null;
        $time = strtotime($dateStr);
        if ($time === false) return null;
        
        $y = (int)date('Y', $time) + 543;
        $m = (int)date('n', $time);
        
        $fy = ($m >= 10) ? $y + 1 : $y;
        $rnd = ($m >= 10 || $m <= 3) ? 1 : 2;
        return "{$fy}-{$rnd}";
    }
}
