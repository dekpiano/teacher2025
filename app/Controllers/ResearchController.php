<?php

namespace App\Controllers;

use App\Models\ResearchModel;
use CodeIgniter\HTTP\ResponseInterface;

class ResearchController extends BaseController
{
    protected $researchModel;
    protected $db;
    protected $session;
    protected $setup;

    public function __construct()
    {
        $this->session = session();
        if (!$this->session->get('isLoggedIn')) {
            service('response')->redirect(base_url('login'))->send();
            exit;
        }

        $this->researchModel = new ResearchModel();
        $this->db = db_connect();
        $this->setup = $this->db->table('tb_send_research_setup')->get()->getRow();
    }

    public function index($year = null, $term = null)
    {
        $data['title'] = "ส่งงานวิจัยในชั้นเรียน";
        $data['setup'] = $this->setup; // Pass setup data to the view

        if (!$this->setup) {
            $this->session->setFlashdata('error', 'ยังไม่ได้ตั้งค่าระบบส่งงานวิจัย กรุณาติดต่อผู้ดูแล');
            $data['research'] = [];
            $data['CheckYearResearch'] = [];
            return view('teacher/research/research_main', $data);
        }

        // Determine current year and term if not provided in URL
        if ($year === null || $term === null) {
            $year = $this->setup->seres_setup_year;
            $term = $this->setup->seres_setup_term;
        }

        $data['year'] = $year;
        $data['term'] = $term;
        $person_id = $this->session->get('person_id');
        $data['person_id'] = $person_id;

        $data['research'] = $this->researchModel
                                       ->where('seres_usersend', $person_id)
                                       ->where('seres_year', $year)
                                       ->where('seres_term', $term)
                                       ->findAll();

        $data['CheckYearResearch'] = [$this->setup];
        $data['setup'] = $this->setup;
        return view('teacher/research/research_main', $data);
    }

    public function sendResearch()
    {

        if (!$this->setup) {
            $this->session->setFlashdata('error', 'ยังไม่ได้ตั้งค่าระบบส่งงานวิจัย กรุณาติดต่อผู้ดูแล');
            return redirect()->to('research');
        }

        $data['title'] = "ส่งงานวิจัยในชั้นเรียน";
        $data['OnOff'] = [$this->setup];
       
        $tiemstart = strtotime($this->setup->seres_setup_startdate);
        $tiemEnd = strtotime($this->setup->seres_setup_enddate);
        $timeNow = time();
        $is_system_on = ($tiemstart < $timeNow && $tiemEnd > $timeNow && $this->setup->seres_setup_status == "on");
        
        if ($is_system_on) {
            return view('teacher/research/research_send', $data);
        } else {
            $this->session->setFlashdata('warning', "<h2>ระบบปิดอยู่ </h2><br>ยังไม่ถึงกำหนดส่งงาน หรือ เกินกำหนดส่งงาน<br>ติดต่อหัวหน้างานหลักสูตร");
            return redirect()->to('research');
        }
    }

    public function insertResearch()
    {
        if (!$this->setup) {
             return $this->response->setJSON(['status' => 'error', 'message' => 'ยังไม่ได้ตั้งค่าระบบส่งงานวิจัย กรุณาติดต่อผู้ดูแล']);
        }

        $rules = [
            'seres_research_name' => 'required',
            'seres_namesubject'   => 'required',
            'seres_coursecode'    => 'required',
            'seres_gradelevel'    => 'required',
            'seres_file' => [
                'rules' => 'uploaded[seres_file]|ext_in[seres_file,pdf]',
                'errors' => [
                    'uploaded' => 'กรุณาเลือกไฟล์งานวิจัย',
                    'ext_in' => 'อนุญาตเฉพาะไฟล์ PDF เท่านั้น'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            // Return first error message for simplicity or all errors
            return $this->response->setJSON(['status' => 'error', 'message' => implode(', ', $errors), 'errors' => $errors]);
        }

        $post = $this->request->getPost();
        $file = $this->request->getFile('seres_file');

        $seres_coursecode = $post['seres_coursecode'] ?? null;
        $seres_usersend = $this->session->get('person_id');
        $seres_year = $this->setup->seres_setup_year;
        $seres_term = $this->setup->seres_setup_term;

        // Check if research for this course/user/year/term already exists
        $existingResearch = $this->researchModel->where('seres_coursecode', $seres_coursecode)
                                        ->where('seres_usersend', $seres_usersend)
                                        ->where('seres_year', $seres_year)
                                        ->where('seres_term', $seres_term)
                                        ->first();

        if ($existingResearch) {
             return $this->response->setJSON(['status' => 'error', 'message' => 'มีข้อมูลงานวิจัยรายวิชานี้อยู่แล้วสำหรับภาคเรียนและปีการศึกษานี้']);
        }

        $insertData = [
            'seres_research_name' => $post['seres_research_name'] ?? null,
            'seres_namesubject'   => $post['seres_namesubject'] ?? null,
            'seres_coursecode'    => $seres_coursecode,
            'seres_gradelevel'    => $post['seres_gradelevel'] ?? null,
            'seres_sendcomment'   => nl2br(htmlentities($post['seres_sendcomment'] ?? '', ENT_QUOTES, 'UTF-8')),
            'seres_usersend'      => $seres_usersend,
            'seres_learning'      => $this->session->get('pers_learning'),
            'seres_year'          => $this->setup->seres_setup_year,
            'seres_term'          => $this->setup->seres_setup_term,
            'seres_status'        => 'ส่งแล้ว', // Default status
        ];

        if ($file->isValid() && !$file->hasMoved()) {
            $uploadBasePath = 'academic/teacher/research';
            $originalName = url_title($post['seres_research_name'], '-', true) . '_' . $seres_usersend . '.' . $file->getExtension();
            $remoteUploadPath = "{$uploadBasePath}/{$seres_year}/{$seres_term}/";

            $uploadResult = $this->_uploadFileToServer($file, $remoteUploadPath, $originalName);
            if ($uploadResult['status'] !== 'success') {
                 return $this->response->setJSON(['status' => 'error', 'message' => $uploadResult['message']]);
            }
            $insertData['seres_file'] = $uploadResult['filename'];
        }

        if ($this->researchModel->insert($insertData)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'ส่งงานวิจัยสำเร็จ']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถบันทึกข้อมูลงานวิจัยได้']);
        }
    }

    public function editResearch($id)
    {
        $data['title'] = "แก้ไขงานวิจัย";
        $data['research'] = $this->researchModel->find($id);
        if (!$data['research']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('ไม่พบงานวิจัยที่ต้องการแก้ไข');
        }
        return view('teacher/research/research_edit', $data);
    }

    public function updateResearch()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200);
        }

        $rules = [
            'seres_file' => [
                'rules' => 'ext_in[seres_file,pdf]',
                'errors' => [
                    'ext_in' => 'อนุญาตเฉพาะไฟล์ PDF เท่านั้น'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()['seres_file']]);
        }

        $post = $this->request->getPost();
        $seres_ID = $post['seres_ID'] ?? null;

        if (!$seres_ID) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบ ID งานวิจัย']);
        }

        $research = $this->researchModel->find($seres_ID);
        if (!$research) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบงานวิจัยที่ต้องการแก้ไข']);
        }

        $updateData = [
            'seres_research_name' => $post['seres_research_name'] ?? $research['seres_research_name'],
            'seres_namesubject'   => $post['seres_namesubject'] ?? $research['seres_namesubject'],
            'seres_coursecode'    => $post['seres_coursecode'] ?? $research['seres_coursecode'],
            'seres_gradelevel'    => $post['seres_gradelevel'] ?? $research['seres_gradelevel'],
            'seres_sendcomment'   => nl2br(htmlentities($post['seres_sendcomment'] ?? $research['seres_sendcomment'], ENT_QUOTES, 'UTF-8')),
            // Status should not be updated by the sender directly, only via review process
            // 'seres_status'        => $post['seres_status'] ?? $research['seres_status'],
        ];

        $file = $this->request->getFile('seres_file');

        // Handle file upload only if a file is provided
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $folderYear = $research['seres_year'];
            $folderTerm = $research['seres_term'];
            // Adjust upload path for research
            $uploadBasePath = 'academic/teacher/research';  

            // Create a filename that includes research name and user ID for uniqueness
            $originalName = url_title($research['seres_research_name'], '-', true) . '_' . $research['seres_usersend'] . '.' . $file->getExtension();
            $remoteUploadPath = "{$uploadBasePath}/{$folderYear}/{$folderTerm}/";
            
            // Delete old file before uploading new one
            if (!empty($research['seres_file'])) {
                $oldRemoteFilePath = "{$uploadBasePath}/{$research['seres_year']}/{$research['seres_term']}/{$research['seres_file']}";
                $this->_deleteFileFromServer($oldRemoteFilePath);
            }

            // Upload the new file
            $uploadResult = $this->_uploadFileToServer($file, $remoteUploadPath, $originalName);
            if ($uploadResult['status'] !== 'success') {
                return $this->response->setJSON($uploadResult);
            }
            $updateData['seres_file'] = $uploadResult['filename'];
        }

        if ($this->researchModel->update($seres_ID, $updateData)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'อัปเดตงานวิจัยสำเร็จ']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'อัปเดตข้อมูลไม่สำเร็จ']);
        }
    }

    public function deleteResearch($id = null)
    {
        if ($id === null) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบ ID งานวิจัยที่ต้องการลบ']);
        }

        $research = $this->researchModel->find($id);

        if (!$research) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบงานวิจัยที่ต้องการลบ']);
        }

        // Delete the associated file from server if it exists
        if (!empty($research['seres_file'])) {
            $uploadBasePath = 'academic/teacher/research'; 
            $folderYear = $research['seres_year'];
            $folderTerm = $research['seres_term'];
            $remoteFilePath = "{$uploadBasePath}/{$folderYear}/{$folderTerm}/{$research['seres_file']}";
            $this->_deleteFileFromServer($remoteFilePath);
        }

        // Delete the record from the database
        if ($this->researchModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'ลบงานวิจัยสำเร็จ']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ลบงานวิจัยไม่สำเร็จ']);
        }
    }

    public function loadResearch($year = null, $term = null)
    {
        $data['title'] = "ดาวน์โหลดงานวิจัยในชั้นเรียน";

        // Get current year/term if not provided
        if ($year === null || $term === null) {
            // Get the latest year/term from the research table
            $latestResearch = $this->researchModel
                                ->select('seres_year, seres_term')
                                ->orderBy('seres_year', 'DESC')
                                ->orderBy('seres_term', 'DESC')
                                ->first();
            
            if ($latestResearch) {
                $year = $latestResearch['seres_year'];
                $term = $latestResearch['seres_term'];
            } else {
                // Fallback if no research exists at all
                $year = date('Y') + 543; // Buddhist year
                $term = 1;
            }
        }

        $data['current_year'] = $year;
        $data['current_term'] = $term;

        $user_learning_group = $this->session->get('pers_learning');

        $data['research'] = $this->researchModel
                                   ->where('seres_learning', $user_learning_group)
                                   ->where('seres_year', $year)
                                   ->where('seres_term', $term)
                                   ->findAll();

        $data['CheckYear'] = $this->researchModel->select('seres_year, seres_term')
                                            ->distinct()
                                            ->orderBy('seres_year', 'desc')
                                            ->orderBy('seres_term', 'desc')
                                            ->get()->getResult();

        return view('teacher/research/research_load', $data);
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
                'http_errors' => false // Prevent exceptions on 4xx/5xx
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($statusCode === 200 && isset($body['status']) && $body['status'] === 'success') {
                return [
                    'status' => 'success',
                    'filename' => $body['filename']
                ];
            } else {
                $errorMessage = $body['message'] ?? 'An unknown error occurred during file upload.';
                log_message('error', "File upload failed: {$errorMessage} (Status: {$statusCode})");
                return ['status' => 'error', 'message' => "ไม่สามารถอัปโหลดไฟล์ได้: {$errorMessage}"];
            }
        } catch (\Exception $e) {
            log_message('error', 'cURL Error during upload: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Upload Error: ' . $e->getMessage()];
        }
    }

    private function _deleteFileFromServer($remoteFilePath)
    {
        $deleteUrl = env('upload.server.delete.url');
        if (!$deleteUrl) {
            log_message('error', 'Delete server URL is not configured in .env file.');
            return false;
        }

        try {
            $client = \Config\Services::curlrequest();

            $path = dirname($remoteFilePath);
            $filename = basename($remoteFilePath);

            $jsonData = json_encode([
                'path' => $path,
                'files' => [$filename]
            ]);

            $response = $client->setBody($jsonData)->post($deleteUrl, [
                'headers' => ['Content-Type' => 'application/json'],
                'http_errors' => false
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($statusCode === 200 && isset($body['status']) && ($body['status'] === 'success' || $body['status'] === 'partial_success')) {
                if (!empty($body['failed'])) {
                    log_message('warning', 'Some files failed to delete on remote server: ' . json_encode($body['failed']));
                }
                return true;
            } else {
                $errorMessage = $body['message'] ?? 'An unknown error occurred during file deletion.';
                log_message('error', "File deletion failed: {$errorMessage} (Status: {$statusCode})");
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', 'cURL Error during deletion: ' . $e->getMessage());
            return false;
        }
    }

    public function settingResearch()
    {
        $data['title'] = "ตั้งค่าการส่งงานวิจัย";
        $data['setup'] = $this->setup;
        return view('teacher/research/research_setting', $data);
    }

    public function settingUpdateResearch()
    {
        $post = $this->request->getPost();

        $dateS = str_replace('/', '-', $post['seres_setup_startdate']);
        $startDate = date('Y-m-d H:i:s', strtotime($dateS));
        $dateE = str_replace('/', '-', $post['seres_setup_enddate']);
        $endDate = date('Y-m-d H:i:s', strtotime($dateE));

        $data = [
            'seres_setup_startdate' => $startDate,
            'seres_setup_enddate'   => $endDate,
            'seres_setup_usersetup' => $this->session->get('person_id'),
            'seres_setup_year'      => $post['seres_setup_year'] ?? null,
            'seres_setup_term'      => $post['seres_setup_term'] ?? null,
            'seres_setup_status'    => $post['seres_setup_status'] ?? 'off',
        ];

        $result = $this->db->table('tb_send_research_setup')->where('seres_setup_ID', 1)->update($data);

        if ($result) {
            $this->session->setFlashdata(['status' => 'success', 'msg' => 'YES', 'messge' => 'ตั้งค่าสำเร็จ']);
        } else {
            $this->session->setFlashdata(['status' => 'error', 'msg' => 'YES', 'messge' => 'ตั้งค่าไม่สำเร็จ']);
        }
        return redirect()->to('research/setting');
    }
}
