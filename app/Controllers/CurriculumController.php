<?php

namespace App\Controllers;

use App\Models\CurriculumModel;
use CodeIgniter\HTTP\ResponseInterface;

class CurriculumController extends BaseController
{
    protected $curriculumModel;
    protected $db;
    protected $db_personnel;
    protected $db_skj;
    protected $session;
    protected $setup;

    public function __construct()
    {
        $this->session = session();
        if (!$this->session->get('isLoggedIn')) {
            // Using service redirection
            service('response')->redirect(base_url('login'))->send();
            exit;
        }

        $this->curriculumModel = new CurriculumModel();
        $this->db = db_connect();
        $this->db_personnel = db_connect('personnel');
        $this->db_skj = db_connect('skj');
        $this->setup = $this->db->table('tb_send_plan_setup')->get()->getRow();
    }

    public function index($year = null, $term = null)
    {
        $data['title'] = "ส่งแผนการสอน";
        $data['CheckHomeVisitManager'] = null; // This was from another DB, might need to fetch if used
        $data['OnOff'] = [$this->setup];

        // Determine current year and term if not provided in URL
        if ($year === null || $term === null) {
            // Get the latest year/term from the plan table
            $latestPlan = $this->curriculumModel
                                ->select('seplan_year, seplan_term')
                                ->orderBy('seplan_year', 'DESC')
                                ->orderBy('seplan_term', 'DESC')
                                ->first();
            
            if ($latestPlan) {
                $year = $latestPlan['seplan_year'];
                $term = $latestPlan['seplan_term'];
            } else {
                // Fallback if no plans exist at all
                $year = date('Y') + 543; // Buddhist year
                $term = 1;
            }
        }

        $data['year'] = $year;
        $data['term'] = $term;
        $person_id = $this->session->get('person_id');
        $data['person_id'] = $person_id;

        $data['plan'] = $this->curriculumModel
                                       ->select('tb_send_plan.*, tb_send_plan_type.type_name')
                                       ->join('tb_send_plan_type', 'tb_send_plan_type.type_id = tb_send_plan.seplan_typeplan_id', 'left')
                                       ->where('seplan_usersend', $person_id)
                                       ->where('seplan_year', $year)
                                       ->where('seplan_term', $term)
                                       ->get()->getResult();

        $data['planNew'] = $this->curriculumModel
                                          ->select('seplan_coursecode, seplan_namesubject, seplan_gradelevel, seplan_typesubject, seplan_year, seplan_term, MAX(seplan_is_main_subject) as seplan_is_main_subject') 
                                          ->join('tb_send_plan_type', 'tb_send_plan_type.type_id = tb_send_plan.seplan_typeplan_id', 'left')
                                          ->where('seplan_usersend', $person_id)
                                          ->where('seplan_year', $year)
                                          ->where('seplan_term', $term)
                                          ->groupBy(['seplan_coursecode', 'seplan_namesubject', 'seplan_gradelevel', 'seplan_typesubject', 'seplan_year', 'seplan_term'])
                                          ->get()->getResult();

        $data['CheckYearPlan'] = $this->curriculumModel->select('seplan_year,seplan_term')
                                                ->distinct()
                                                ->get()->getResult();

        // Fetch active plan types for the view
        $data['activePlanTypes'] = $this->db->table('tb_send_plan_type')
                                            ->select('type_name')
                                            ->where('is_active', 1)
                                            ->orderBy('type_id', 'ASC') // Or by type_name, depending on desired order
                                            ->get()->getResultArray();

        return view('teacher/curriculum/plan_main', $data);
    }

    public function sendPlan()
    {
        $data['OnOff'] = [$this->setup];

        $tiemstart = $this->setup->seplanset_startdate;
        $tiemEnd = $this->setup->seplanset_enddate;
        $timeNow = date('Y-m-d H:i:s');

        if ($tiemstart < $timeNow && $tiemEnd > $timeNow && $this->setup->seplanset_status == "on") {
            return view('teacher/curriculum/plan_send', $data);
        } else {
            $this->session->setFlashdata('warning', "<h2>ระบบปิดอยู่ </h2><br>ยังไม่ถึงกำหนดส่งงาน หรือ เกินกำหนดส่งงาน<br>ติดต่อหัวงานหลักสูตร");
            return redirect()->to('curriculum');
        }
    }

    public function insertPlan()
    {
        $post = $this->request->getPost();
        $seplan_coursecode = $post['seplan_coursecode'] ?? null;
        $seplan_usersend = $this->session->get('person_id');

        $currentYear = $this->setup->seplanset_year ?? null;
        $currentTerm = $this->setup->seplanset_term ?? null;

        // Check if plan already exists
        $existingPlan = $this->curriculumModel->where('seplan_coursecode', $seplan_coursecode)
                                        ->where('seplan_usersend', $seplan_usersend)
                                        ->where('seplan_year', $currentYear)
                                        ->where('seplan_term', $currentTerm)
                                        ->first();

        if ($existingPlan) {
            return $this->response->setJSON(['status' => 'duplicate', 'msg' => 'มีข้อมูลรายวิชานี้อยู่แล้ว']);
        }

        // Fetch plan types from the database
        $planTypes = $this->db->table('tb_send_plan_type')
                              ->select('type_id, type_name')
                              ->where('is_active', 1) // Only active types
                              ->get()->getResultArray();

        $insertData = [];
        $comment = nl2br(htmlentities($post['seplan_sendcomment'] ?? '', ENT_QUOTES, 'UTF-8'));

        foreach ($planTypes as $type) {
            $insertData[] = [
                'seplan_namesubject'  => $post['seplan_namesubject'] ?? null,
                'seplan_coursecode'   => $seplan_coursecode,
                'seplan_typesubject'  => $post['seplan_typesubject'] ?? null,
                'seplan_year'         => $currentYear,
                'seplan_term'         => $currentTerm,
                'seplan_usersend'     => $seplan_usersend,
                'seplan_learning'     => $this->session->get('pers_learning'),
                'seplan_status1'      => "รอตรวจ",
                'seplan_status2'      => "รอตรวจ",
                'seplan_sendcomment'  => $comment,
                'seplan_gradelevel'   => $post['seplan_gradelevel'] ?? null,
                'seplan_typeplan'     => $type['type_name'], // Keep old column for now
                'seplan_typeplan_id'  => $type['type_id'],   // New column
                'seplan_createdate'   => date('Y-m-d H:i:s'), // บันทึกวันที่ส่ง
            ];
        }

        if ($this->curriculumModel->insertBatch($insertData)) {
             $firstId = $this->curriculumModel->getInsertID();
             $json = $this->db->table('tb_send_plan')
                                     ->select('seplan_ID, seplan_coursecode, seplan_namesubject, seplan_gradelevel, seplan_typesubject, seplan_year, seplan_term, seplan_usersend, seplan_learning, tb_personnel.pers_id, tb_personnel.pers_prefix, tb_personnel.pers_firstname, tb_personnel.pers_lastname, tb_personnel.pers_learning')
                                     ->join($this->db_personnel->database . '.tb_personnel', 'tb_personnel.pers_id = tb_send_plan.seplan_usersend')
                                     ->where('seplan_coursecode', $seplan_coursecode)
                                     ->where('seplan_usersend', $seplan_usersend)
                                     ->where('seplan_year', $currentYear)
                                     ->where('seplan_term', $currentTerm)
                                     ->groupBy(['seplan_ID', 'seplan_coursecode', 'seplan_namesubject', 'seplan_gradelevel', 'seplan_typesubject', 'seplan_year', 'seplan_term', 'seplan_usersend', 'seplan_learning', 'tb_personnel.pers_id', 'tb_personnel.pers_prefix', 'tb_personnel.pers_firstname', 'tb_personnel.pers_lastname', 'tb_personnel.pers_learning'])
                                     ->get()->getRowArray();

            return $this->response->setJSON(['status' => 'success', 'msg' => 'OK', 'data' => $json]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'ไม่สามารถบันทึกข้อมูลได้']);
        }
    }

    public function editPlan($id)
    {
        $data['title'] = "แก้ไขงาน";
        $data['plan'] = $this->curriculumModel->find($id);
        $data['OnOff'] = [$this->setup];
        
        $data['pers'] = $this->db_personnel->table('tb_personnel')
                                    ->select('pers_prefix,pers_firstname,pers_lastname,pers_id,pers_position,pers_learning')
                                    ->where('pers_position >=', 'posi_003')
                                    ->where('pers_position <=', 'posi_006')
                                    ->orderBy('pers_learning')
                                    ->get()->getResult();

        return view('teacher/curriculum/plan_edit', $data);
    }

    /**
     * Handles file upload to a remote server and updates plan details.
     *
     * @return ResponseInterface
     */
    public function updatePlan()
    {
        // Set CORS headers for AJAX requests
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200);
        }

        $rules = [
            'seplan_file' => [
                'rules' => 'ext_in[seplan_file,doc,docx,pdf]',
                'errors' => [
                    'ext_in' => 'อนุญาตเฉพาะไฟล์ .doc, .docx, .pdf เท่านั้น'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()['seplan_file']]);
        }

        $post = $this->request->getPost();
        $seplan_ID = $post['seplan_ID'] ?? null;

        if (!$seplan_ID) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบ ID แผน']);
        }

        $plan = $this->curriculumModel->find($seplan_ID);
        if (!$plan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบแผนที่ต้องการแก้ไข']);
        }

        $textToStore = nl2br(htmlentities($post['seplan_sendcomment'] ?? '', ENT_QUOTES, 'UTF-8'));
        $file = $this->request->getFile('seplan_file');

        $updateData = [
            'seplan_sendcomment' => $textToStore,
        ];

        // Handle file upload only if a file is provided
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $folderYear = $this->setup->seplanset_year ?? date('Y') + 543;
            $folderTerm = $this->setup->seplanset_term ?? 1;
            $uploadBasePath = 'academic/teacher/course/plan';

            // Use data from the database record for consistency
            $remoteUploadPath = "{$uploadBasePath}/{$folderYear}/{$folderTerm}/{$plan['seplan_namesubject']}/";
            $originalName = "{$plan['seplan_namesubject']}_{$plan['seplan_typeplan']}_{$plan['seplan_usersend']}." . $file->getExtension();

            // Delete old file before uploading new one
            if (!empty($plan['seplan_file'])) {
                $oldRemoteFilePath = "{$uploadBasePath}/{$plan['seplan_year']}/{$plan['seplan_term']}/{$plan['seplan_namesubject']}/{$plan['seplan_file']}";
                $this->_deleteFileFromServer($oldRemoteFilePath);
            }

            // Upload the new file
            $uploadResult = $this->_uploadFileToServer($file, $remoteUploadPath, $originalName);
            if ($uploadResult['status'] !== 'success') {
                return $this->response->setJSON($uploadResult);
            }
            $updateData['seplan_file'] = $uploadResult['filename'];
        }

        // Update the database
        if ($this->curriculumModel->update($seplan_ID, $updateData)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'อัปเดตแผนสำเร็จ']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'อัปเดตข้อมูลไม่สำเร็จ']);
        }
    }

    public function setMainSubject()
    {
        // Get the raw JSON input
        $input = $this->request->getJSON(true); // true to return as associative array

        $courseCode = $input['courseCode'] ?? null;
        $year = $input['year'] ?? null;
        $term = $input['term'] ?? null;
        $personId = $input['person_id'] ?? null; // Get person_id from input, not session, as it's sent

        // Fallback to session for personId if not in input (though it should be sent)
        if (!$personId) {
            $personId = $this->session->get('person_id');
        }

        if (!$courseCode || !$year || !$term || !$personId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน (Missing data)']);
        }

        if ($this->curriculumModel->setTeacherMainSubject($personId, $courseCode, $year, $term)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'ตั้งค่าวิชาหลักสำเร็จ']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถตั้งค่าวิชาหลักได้']);
        }
    }

    public function downloadPlanFile($seplanID)
    {
        if (!$seplanID) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invalid Plan ID.');
        }

        $plan = $this->curriculumModel->find($seplanID);

        if (!$plan || empty($plan['seplan_file'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File record not found or file not specified.');
        }

        // Construct the full URL to the file.
        $baseFileUrl = env('upload.server.baseurl');
        if (!$baseFileUrl) {
            throw new \RuntimeException('The upload.server.baseurl is not defined in the .env file.');
        }

        $fileUrl = rtrim($baseFileUrl, '/') . '/' . $plan['seplan_year'] . '/' . $plan['seplan_term'] . '/' . rawurlencode($plan['seplan_namesubject']) . '/' . rawurlencode($plan['seplan_file']);


        // Fetch the file content from the URL.
        // Use error suppression to handle potential 404s gracefully.
        $fileData = @file_get_contents($fileUrl);

        if ($fileData === false) {
            // Throw a more informative error if the file could not be fetched.
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Could not retrieve file from URL: ' . esc($fileUrl));
        }

        // Use the response->download() method to force download.
        // The first parameter is the desired filename for the user.
        // The second parameter is the file data.
        return $this->response->download($plan['seplan_file'], $fileData);
    }

    /**
     * Helper function to upload a file to the remote server via upload.php.
     */
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

    /**
     * Helper function to delete a file from the remote server via delete.php.
     */
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


    public function deletePlan($id = null)
    {
        if ($id === null) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบ ID แผนที่ต้องการลบ']);
        }

        $plan = $this->curriculumModel->find($id);

        if (!$plan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบแผนที่ต้องการลบ']);
        }

        // Delete the associated file from server if it exists
        if (!empty($plan['seplan_file'])) {
            $uploadBasePath = 'academic/teacher/course/plan';
            $folderYear = $plan['seplan_year'];
            $folderTerm = $plan['seplan_term'];
            $remoteFilePath = "{$uploadBasePath}/{$folderYear}/{$folderTerm}/{$plan['seplan_namesubject']}/{$plan['seplan_file']}";
            $this->_deleteFileFromServer($remoteFilePath);
        }

        // Delete the record from the database
        if ($this->curriculumModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'ลบแผนสำเร็จ']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ลบแผนไม่สำเร็จ']);
        }
    }

    public function loadPlan($year = null, $term = null, $selectedTeacher = 'All')
    {
        $data['title'] = "ดาวน์โหลดแผนการสอน";
        $data['CheckHomeVisitManager'] = null; // Fetch if needed
        $data['OnOff'] = [$this->setup];

        // Get current year/term if not provided
        if ($year === null || $term === null) {
            // Get the latest year/term from the plan table
            $latestPlan = $this->curriculumModel
                                ->select('seplan_year, seplan_term')
                                ->orderBy('seplan_year', 'DESC')
                                ->orderBy('seplan_term', 'DESC')
                                ->first();
            
            if ($latestPlan) {
                $year = $latestPlan['seplan_year'];
                $term = $latestPlan['seplan_term'];
            } else {
                // Fallback if no plans exist at all
                $year = date('Y') + 543; // Buddhist year
                $term = 1;
            }
        }

        $data['current_year'] = $year;
        $data['current_term'] = $term;

        $CheckLearning = $this->db_personnel->table('tb_personnel')
                                     ->select('pers_learning')
                                     ->where('pers_id', $this->session->get('person_id'))
                                     ->get()->getRow();

        $data['SelTeacher'] = $this->db_personnel->table('tb_personnel')
                                         ->select('pers_id,pers_prefix,pers_firstname,pers_lastname')
                                         ->where('pers_learning', $CheckLearning->pers_learning)
                                         ->get()->getResult();

        $builder = $this->curriculumModel
                                   ->select('tb_send_plan.*, tb_send_plan_type.type_name') // Added type_name
                                   ->join('tb_send_plan_type', 'tb_send_plan_type.type_id = tb_send_plan.seplan_typeplan_id', 'left') // Added join
                                   ->where('seplan_learning', $CheckLearning->pers_learning)
                                   ->where('seplan_year', $year)
                                   ->where('seplan_term', $term);

        if ($selectedTeacher !== "All") {
            $builder->where('seplan_usersend', $selectedTeacher);
        }
        $data['plan'] = $builder->get()->getResult();

        $planNewBuilder = $this->curriculumModel
                                          ->select('tb_send_plan.*, tb_send_plan_type.type_name') // Added type_name
                                          ->join('tb_send_plan_type', 'tb_send_plan_type.type_id = tb_send_plan.seplan_typeplan_id', 'left') // Added join
                                          ->where('seplan_learning', $CheckLearning->pers_learning)
                                          ->where('seplan_year', $year)
                                          ->where('seplan_term', $term);
        if ($selectedTeacher !== "All") {
            $planNewBuilder->where('seplan_usersend', $selectedTeacher);
        }
        $data['planNew'] = $planNewBuilder->groupBy('seplan_coursecode')
                                          ->groupBy('seplan_year')
                                          ->groupBy('seplan_term')
                                          ->get()->getResult();

        $data['CheckTeach'] = $selectedTeacher;

        $data['CheckYear'] = $this->curriculumModel->select('seplan_year,seplan_term')
                                            ->distinct()
                                            ->get()->getResult();

        // Fetch active plan types for the view
        $data['activePlanTypes'] = $this->db->table('tb_send_plan_type')
                                            ->select('type_name')
                                            ->where('is_active', 1)
                                            ->orderBy('type_id', 'ASC')
                                            ->get()->getResultArray();

        return view('teacher/curriculum/plan_loadplan', $data);
    }

    private function beautifyFilename($filename)
    {
        // reduce consecutive characters
        $filename = preg_replace(['/ +/', '/_+/', '/-+/'], '-', $filename);
        $filename = preg_replace(['/-*\.-*/', '/\.{2,}/'], '.', $filename);
        // lowercase for windows/unix interoperability
        $filename = mb_strtolower($filename, mb_detect_encoding($filename));
        // ".file-name.-" becomes "file-name"
        $filename = trim($filename, '.-');
        return $filename;
    }

    public function downloadPlanZip($userId = null)
    {
        if ($userId === null) {
            return redirect()->back()->with('error', 'ไม่พบ ID ครูผู้สอน');
        }

        $dataFiles = $this->db->table('tb_send_plan')
            ->select('seplan_usersend, seplan_file, seplan_year, seplan_term, seplan_namesubject')
            ->where('seplan_usersend', $userId)
            ->where('seplan_file !=', '') // Only include records with actual files
            ->get()->getResult();

        $teacherInfo = $this->db_personnel->table('tb_personnel')
            ->select('pers_id, pers_prefix, pers_firstname, pers_lastname')
            ->where('pers_id', $userId)
            ->get()->getRow();

        if (!$teacherInfo) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลครูผู้สอน');
        }

        $zip = new \ZipArchive();
        $zipFileName = $this->beautifyFilename($teacherInfo->pers_prefix . $teacherInfo->pers_firstname . '_' . $teacherInfo->pers_lastname) . '.zip';
        $zipFilePath = WRITEPATH . 'uploads/' . $zipFileName; // Use WRITEPATH for temporary files

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            return redirect()->back()->with('error', 'ไม่สามารถสร้างไฟล์ Zip ได้');
        }

        $baseFileUrl = env('upload.server.baseurl');
        if (!$baseFileUrl) {
            // Close zip and delete file before redirecting
            $zip->close();
            unlink($zipFilePath);
            return redirect()->back()->with('error', 'The upload.server.baseurl is not defined in the .env file.');
        }

        $hasFiles = false;
        foreach ($dataFiles as $fileRecord) {
            $fileUrl = rtrim($baseFileUrl, '/') . '/' . $fileRecord->seplan_year . '/' . $fileRecord->seplan_term . '/' . rawurlencode($fileRecord->seplan_namesubject) . '/' . rawurlencode($fileRecord->seplan_file);

            $fileData = @file_get_contents($fileUrl);

            if ($fileData !== false) {
                $hasFiles = true;
                // Add file to zip with a structured path
                $zipPath = $fileRecord->seplan_year . '/' . $fileRecord->seplan_term . '/' . $fileRecord->seplan_namesubject . '/' . $fileRecord->seplan_file;
                $zip->addFromString($zipPath, $fileData);
            } else {
                log_message('error', 'Could not download file for zipping: ' . $fileUrl);
            }
        }

        $zip->close();

        if (!$hasFiles || !file_exists($zipFilePath) || filesize($zipFilePath) === 0) {
            if (file_exists($zipFilePath)) {
                unlink($zipFilePath);
            }
            return redirect()->back()->with('error', 'ไม่พบไฟล์ให้ดาวน์โหลด หรือไม่สามารถสร้างไฟล์ Zip ได้');
        }

        return $this->response->download($zipFilePath, null)->setFileName($zipFileName);
    }

    public function downloadPlan()
    {
        $data['title'] = "ดาวน์โหลดแผน";
        $data['CheckHomeVisitManager'] = null; // Fetch if needed
        $data['OnOff'] = [$this->setup];

        $data['teacher'] = $this->db_personnel->table('tb_personnel')
                                     ->select('pers_id,pers_prefix,pers_firstname,pers_lastname,pers_groupleade,pers_learning')
                                     ->where('pers_learning !=', '')
                                     ->get()->getResult();

        return view('teacher/curriculum/plan_download', $data);
    }

    public function checkPlan($idLear = null)
    {
        $data['title'] = "ตรวจสอบงาน";
        $data['OnOff'] = [$this->setup];
        $data['lean'] = $this->db_skj->table('tb_learning')->get()->getResult();
        $data['IDlear'] = $idLear;

        $data['planNew'] = $this->db->table('tb_send_plan')
                                ->select('tb_send_plan.*, tb_personnel.pers_id, tb_personnel.pers_prefix, tb_personnel.pers_firstname, tb_personnel.pers_lastname, tb_send_plan_type.type_name') // Added type_name
                                ->join($this->db_personnel->database . '.tb_personnel', 'tb_personnel.pers_id = tb_send_plan.seplan_usersend')
                                ->join('tb_send_plan_type', 'tb_send_plan_type.type_id = tb_send_plan.seplan_typeplan_id', 'left') // Added join
                                ->where('seplan_learning', $idLear)
                                ->groupBy(['seplan_coursecode', 'pers_id'])
                                ->get()->getResult();

        $data['checkplan'] = $this->db->table('tb_send_plan')
                                ->select('tb_send_plan.*, tb_send_plan_type.type_name') // Added type_name
                                ->join('tb_send_plan_type', 'tb_send_plan_type.type_id = tb_send_plan.seplan_typeplan_id', 'left') // Added join
                                ->where('seplan_learning', $idLear)
                                ->get()->getResult();

        // Fetch active plan types for the view
        $data['activePlanTypes'] = $this->db->table('tb_send_plan_type')
                                            ->select('type_name')
                                            ->where('is_active', 1)
                                            ->orderBy('type_id', 'ASC')
                                            ->get()->getResultArray();

        return view('teacher/curriculum/plan_check', $data);
    }

    public function checkPlanLear($idLear = null)
    {
        $data['title'] = "ตรวจสอบงานตามกลุ่มสาระการเรียนรู้";
        $data['lean'] = $this->db_skj->table('tb_learning')->where('lear_id', $idLear)->get()->getResult();
        $data['IDlear'] = $idLear;
        $data['OnOff'] = [$this->setup];

        $check_guide = $this->session->get('person_id');

        if ($check_guide == "pers_052") {
            // Specific logic for pers_052 (Guidance)
            $data['planNew'] = $this->db->table('tb_send_plan')
                                    ->select('tb_send_plan.*, tb_personnel.pers_id, tb_personnel.pers_prefix, tb_personnel.pers_firstname, tb_personnel.pers_lastname')
                                    ->join($this->db_personnel->database . '.tb_personnel', 'tb_personnel.pers_id = tb_send_plan.seplan_usersend')
                                    ->like('seplan_namesubject', "แนะแนว")
                                    ->groupBy(['seplan_coursecode', 'pers_id'])
                                    ->get()->getResult();
            $data['checkplan'] = $this->db->table('tb_send_plan')
                                    ->like('seplan_namesubject', "แนะแนว")
                                    ->get()->getResult();
        } else {
            $data['techer'] = $this->db_personnel->table('tb_personnel')
                                        ->select('pers_id,pers_prefix,pers_firstname,pers_lastname,pers_learning,pers_img')
                                        ->where('pers_learning', $idLear)
                                        ->where('pers_status', 'กำลังใช้งาน')
                                        ->get()->getResult();
        }

        return view('teacher/curriculum/plan_check_lear', $data);
    }

    public function checkPlanLearTeacher($idLear = null, $idTech = null, $year = null, $term = null)
    {
        $data['title'] = "ตรวจสอบงานตามกลุ่มสาระการเรียนรู้";
        $data['lean'] = $this->db_skj->table('tb_learning')->where('lear_id', $idLear)->get()->getResult();
        $data['IDlear'] = $idLear;
        $data['OnOff'] = [$this->setup];

        $check_guide = $this->session->get('person_id');

        if ($check_guide == "pers_052") {
            // Logic for pers_052 is handled in checkPlanLear, redirect or show specific view if needed
            return $this->checkPlanLear($idLear);
        }
        
        if ($year === null || $term === null) {
            // Get the latest year/term from the plan table
            $latestPlan = $this->curriculumModel
                                ->select('seplan_year, seplan_term')
                                ->orderBy('seplan_year', 'DESC')
                                ->orderBy('seplan_term', 'DESC')
                                ->first();
            
            if ($latestPlan) {
                $year = $latestPlan['seplan_year'];
                $term = $latestPlan['seplan_term'];
            } else {
                // Fallback if no plans exist at all
                $year = date('Y') + 543; // Buddhist year
                $term = 1;
            }
        }

        $queryYear = $year;
        $queryTerm = $term;

        $data['planNew'] = $this->db->table('tb_send_plan')
                                ->select('tb_send_plan.seplan_term, tb_send_plan.seplan_year, tb_send_plan.seplan_coursecode, tb_send_plan.seplan_namesubject, tb_send_plan.seplan_typesubject, tb_send_plan.seplan_gradelevel, tb_send_plan.seplan_learning, tb_personnel.pers_id, tb_personnel.pers_prefix, tb_personnel.pers_firstname, tb_personnel.pers_lastname, tb_send_plan_type.type_name') // Added type_name
                                ->join($this->db_personnel->database . '.tb_personnel', 'tb_personnel.pers_id = tb_send_plan.seplan_usersend')
                                ->join('tb_send_plan_type', 'tb_send_plan_type.type_id = tb_send_plan.seplan_typeplan_id', 'left') // Added join
                                ->where('seplan_learning', $idLear)
                                ->where('pers_id', $idTech)
                                ->where('seplan_year', $queryYear)
                                ->where('seplan_term', $queryTerm)
                                ->groupBy(['seplan_coursecode', 'pers_id'])
                                ->get()->getResult();
        $data['checkplan'] = $this->db->table('tb_send_plan')
                                ->select('tb_send_plan.*, tb_send_plan_type.type_name') // Added type_name
                                ->join('tb_send_plan_type', 'tb_send_plan_type.type_id = tb_send_plan.seplan_typeplan_id', 'left') // Added join
                                ->where('seplan_learning', $idLear)
                                ->where('seplan_usersend', $idTech)
                                ->where('seplan_year', $queryYear)
                                ->where('seplan_term', $queryTerm)
                                ->get()->getResult();

        $data['CheckYear'] = $this->db->table('tb_send_plan')
                                ->select('seplan_year,seplan_term')
                                ->distinct()
                                ->orderBy('seplan_year', 'desc')
                                ->orderBy('seplan_term', 'desc')
                                ->get()->getResult();

        // Fetch active plan types for the view
        $data['activePlanTypes'] = $this->db->table('tb_send_plan_type')
                                            ->select('type_name')
                                            ->where('is_active', 1)
                                            ->orderBy('type_id', 'ASC')
                                            ->get()->getResultArray();

        return view('teacher/curriculum/plan_check_lear_techer', $data);
    }

    public function uploadPlan()
    {
        $post = $this->request->getPost();
        $yearFromPost = $post['Year'] ?? null;

        if (!$yearFromPost) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลปีการศึกษา']);
        }

        $exp = explode('/', $yearFromPost);
        $seplanTerm = $exp[0];
        $seplanYear = $exp[1];

        $dbAcademic = db_connect('academic');
        $CheckRegisPlan = $dbAcademic->table('tb_register')
                                     ->select('tb_register.SubjectCode, tb_subjects.SubjectName, tb_subjects.SubjectClass, tb_subjects.SubjectType, tb_register.TeacherID, tb_personnel.pers_learning, tb_subjects.SubjectYear')
                                     ->join('tb_subjects', 'tb_subjects.SubjectCode = tb_register.SubjectCode')
                                     ->join($this->db_personnel->database . '.tb_personnel', 'tb_personnel.pers_id = tb_register.TeacherID')
                                     ->where('tb_register.RegisterYear', $yearFromPost)
                                     ->where('tb_register.TeacherID IS NOT NULL')
                                     ->where('tb_register.TeacherID !=', '')
                                     ->distinct()
                                     ->get()->getResult();

        // Fetch plan types from the database once
        $planTypes = $this->db->table('tb_send_plan_type')
                              ->select('type_id, type_name')
                              ->where('is_active', 1)
                              ->get()->getResultArray();

        $response = [];
        $allInsertData = [];

        foreach ($CheckRegisPlan as $value) {
            $existing = $this->curriculumModel
                ->where('seplan_year', $seplanYear)
                ->where('seplan_term', $seplanTerm)
                ->where('seplan_coursecode', $value->SubjectCode)
                ->first();

            if ($existing) {
                $response[] = ['status' => 'duplicate', 'subject' => $value->SubjectCode];
            } else {
                // Use fetched plan types
                $Class = explode('.', $value->SubjectClass);
                $Type = explode('/', $value->SubjectType);

                foreach ($planTypes as $type) { // Loop through fetched types
                    $allInsertData[] = [
                        'seplan_namesubject'  => $value->SubjectName,
                        'seplan_coursecode'   => $value->SubjectCode,
                        'seplan_typesubject'  => $Type[1] ?? 'เพิ่มเติม',
                        'seplan_year'         => $seplanYear,
                        'seplan_term'         => $seplanTerm,
                        'seplan_usersend'     => $value->TeacherID,
                        'seplan_learning'     => $value->pers_learning,
                        'seplan_status1'      => "รอตรวจ",
                        'seplan_status2'      => "รอตรวจ",
                        'seplan_gradelevel'   => $Class[1] ?? 'ไม่ระบุ',
                        'seplan_typeplan'     => $type['type_name'], // Old column
                        'seplan_typeplan_id'  => $type['type_id'],   // New column
                        'seplan_createdate'   => date('Y-m-d H:i:s'), // บันทึกวันที่ส่ง
                    ];
                }
                $response[] = ['status' => 'prepared', 'subject' => $value->SubjectCode];
            }
        }

        if (!empty($allInsertData)) {
            $this->curriculumModel->insertBatch($allInsertData);
            // Change status for response
            foreach ($response as &$res) {
                if ($res['status'] === 'prepared') {
                    $res['status'] = 'inserted';
                }
            }
        }

        return $this->response->setJSON($response);
    }

    public function settingTeacher()
    {
        $data['title'] = "ตั้งค่าครูผู้สอน";
        $data['OnOff'] = [$this->setup];

        $data['pers'] = $this->db_personnel->table('tb_personnel')
                                    ->select('pers_prefix,pers_firstname,pers_lastname,pers_id,pers_position,pers_learning')
                                    ->where('pers_position >=', 'posi_003')
                                    ->where('pers_position <=', 'posi_006')
                                    ->orderBy('pers_learning')
                                    ->get()->getResult();

        $postYear = $this->request->getPost('Year');
        if (empty($postYear)) {
            // Get the latest year/term from the plan table
            $latestPlan = $this->curriculumModel
                                ->select('seplan_year, seplan_term')
                                ->orderBy('seplan_year', 'DESC')
                                ->orderBy('seplan_term', 'DESC')
                                ->first();
            
            if ($latestPlan) {
                $data['year'] = $latestPlan['seplan_year'];
                $data['term'] = $latestPlan['seplan_term'];
            } else {
                // Fallback if no plans exist at all
                $data['year'] = date('Y') + 543; // Buddhist year
                $data['term'] = 1;
            }
        } else {
            $CheckYear = explode('/', $postYear);
            $data['term'] = $CheckYear[0];
            $data['year'] = $CheckYear[1];
        }

        $data['CheckSelectYear'] = $this->db->table('tb_send_plan')
                                        ->select('seplan_year,seplan_term')
                                        ->distinct()
                                        ->orderBy('seplan_year', 'desc')
                                        ->orderBy('seplan_term', 'desc')
                                        ->get()->getResult();

        $data['Plan'] = $this->db->table('tb_send_plan')
                            ->select('seplan_coursecode, seplan_namesubject, seplan_gradelevel, seplan_typesubject, seplan_year, seplan_term, seplan_usersend, seplan_learning, tb_personnel.pers_id, tb_personnel.pers_prefix, tb_personnel.pers_firstname, tb_personnel.pers_lastname, tb_personnel.pers_learning') 
                            ->join($this->db_personnel->database . '.tb_personnel', 'tb_personnel.pers_id = tb_send_plan.seplan_usersend', 'LEFT')
                            ->join('tb_send_plan_type', 'tb_send_plan_type.type_id = tb_send_plan.seplan_typeplan_id', 'left') // Added join
                            ->where('seplan_year', $data['year'])
                            ->where('seplan_term', $data['term'])
                            ->groupBy(['seplan_coursecode', 'seplan_namesubject', 'seplan_gradelevel', 'seplan_typesubject', 'seplan_year', 'seplan_term', 'seplan_usersend', 'seplan_learning', 'pers_id', 'pers_prefix', 'pers_firstname', 'pers_lastname', 'pers_learning'])
                            ->get()->getResult();

        return view('teacher/curriculum/plan_setting_teacher', $data);
    }

    public function settingTeacherEdit()
    {
        $post = $this->request->getPost();
        $planCode = $post['PlanCode'] ?? null;
        $planYear = $post['PlanYear'] ?? null;
        $planTerm = $post['PlanTerm'] ?? null;

        $json = $this->curriculumModel->select('seplan_namesubject, seplan_coursecode, seplan_gradelevel, seplan_typesubject, seplan_year, seplan_term, seplan_usersend')
                                ->where('seplan_coursecode', $planCode)
                                ->where('seplan_year', $planYear)
                                ->where('seplan_term', $planTerm)
                                ->first();
        
        return $this->response->setJSON($json);
    }

    public function settingTeacherUpdate()
    {
        $post = $this->request->getPost();

        $data = [
            'seplan_namesubject'   => $post['up_seplan_namesubject'] ?? null,
            'seplan_gradelevel'    => $post['up_seplan_gradelevel'] ?? null,
            'seplan_typesubject'   => $post['up_seplan_typesubject'] ?? null,
            'seplan_usersend'      => $post['up_seplan_usersend'] ?? null,
        ];
        $courseCode = $post['up_seplan_coursecode'] ?? null;
        $year = $post['up_seplan_year'] ?? null;
        $term = $post['up_seplan_term'] ?? null;

        $result = $this->curriculumModel->where('seplan_coursecode', $courseCode)
                                        ->where('seplan_year', $year)
                                        ->where('seplan_term', $term)
                                        ->set($data)
                                        ->update();
        
        return $this->response->setJSON(['success' => $result]);
    }

    public function settingTeacherDelete()
    {
        $post = $this->request->getPost();
        $delPlanCode = $post['DelPlanCode'] ?? null;
        $delPlanTerm = $post['DelPlanTerm'] ?? null;
        $delPlanYear = $post['DelPlanYear'] ?? null;

        // First, find all records to delete files
        $plansToDelete = $this->curriculumModel->where('seplan_coursecode', $delPlanCode)
                                               ->where('seplan_term', $delPlanTerm)
                                               ->where('seplan_year', $delPlanYear)
                                               ->findAll();
        
        $uploadBasePath = 'academic/teacher/course/plan';
        foreach ($plansToDelete as $plan) {
             if (!empty($plan['seplan_file'])) {
                $remoteFilePath = "{$uploadBasePath}/{$plan['seplan_year']}/{$plan['seplan_term']}/{$plan['seplan_namesubject']}/{$plan['seplan_file']}";
                $this->_deleteFileFromServer($remoteFilePath);
            }
        }

        // Then, delete all records from DB
        $result = $this->curriculumModel->where('seplan_coursecode', $delPlanCode)
                                        ->where('seplan_term', $delPlanTerm)
                                        ->where('seplan_year', $delPlanYear)
                                        ->delete();

        return $this->response->setJSON(['success' => $result]);
    }


    public function checkPlanHead()
    {
        $data['title'] = "ตรวจสอบแผนการสอน (เลือกครู)";

        $user_learning_group = $this->session->get('pers_learning');

        $data['teachers'] = $this->db_personnel->table('tb_personnel')
            ->select('pers_id, pers_prefix, pers_firstname, pers_lastname, pers_img')
            ->where('pers_learning', $user_learning_group)
            ->where('pers_status', 'กำลังใช้งาน')
            ->orderBy('pers_firstname', 'ASC')
            ->get()->getResult();

            //print_r($data['teachers']); exit;
        return view('teacher/curriculum/plan_check_head_teachers', $data);
    }

    public function checkPlanHeadDetail($teacher_id = null, $year = null, $term = null)
    {
        if ($teacher_id === null) {
            return redirect()->to('curriculum/check-plan-head')->with('error', 'ไม่ได้ระบุรหัสครู');
        }

        $data['teacher_info'] = $this->db_personnel->table('tb_personnel')
            ->select('pers_id, pers_prefix, pers_firstname, pers_lastname')
            ->where('pers_id', $teacher_id)
            ->get()->getRow();

        $data['title'] = "ตรวจสอบแผนการสอน: " . $data['teacher_info']->pers_firstname;

        // Determine current year and term if not provided
        if ($year === null || $term === null) {
            // Get the latest year/term from the plan table
            $latestPlan = $this->curriculumModel
                                ->select('seplan_year, seplan_term')
                                ->orderBy('seplan_year', 'DESC')
                                ->orderBy('seplan_term', 'DESC')
                                ->first();
            
            if ($latestPlan) {
                $year = $latestPlan['seplan_year'];
                $term = $latestPlan['seplan_term'];
            } else {
                // Fallback if no plans exist at all
                $year = date('Y') + 543; // Buddhist year
                $term = 1;
            }
        }

        $data['current_year'] = $year;
        $data['current_term'] = $term;
        $data['upload_base_url'] = env('upload.server.baseurl');

        $data['CheckYear'] = $this->curriculumModel->select('seplan_year,seplan_term')
            ->distinct()
            ->orderBy('seplan_year', 'desc')
            ->orderBy('seplan_term', 'desc')
            ->get()->getResult();

        $data['plans'] = $this->curriculumModel
            ->select('tb_send_plan.seplan_ID, tb_send_plan.seplan_coursecode, tb_send_plan.seplan_namesubject, tb_send_plan.seplan_gradelevel, tb_send_plan.seplan_typesubject, tb_send_plan.seplan_year, tb_send_plan.seplan_term, tb_send_plan.seplan_usersend, tb_send_plan.seplan_learning, tb_send_plan.seplan_file, tb_send_plan.seplan_status1, tb_send_plan.seplan_status2, tb_send_plan.seplan_typeplan_id, u.pers_firstname, u.pers_lastname') 
            ->join($this->db_personnel->database . '.tb_personnel as u', 'u.pers_id = tb_send_plan.seplan_usersend')
            ->join('tb_send_plan_type', 'tb_send_plan_type.type_id = tb_send_plan.seplan_typeplan_id', 'left') // Added join
            ->where('tb_send_plan.seplan_usersend', $teacher_id)
            ->where('tb_send_plan.seplan_year', $year)
            ->where('tb_send_plan.seplan_term', $term)
            ->groupBy(['tb_send_plan.seplan_ID', 'tb_send_plan.seplan_coursecode', 'tb_send_plan.seplan_namesubject', 'tb_send_plan.seplan_gradelevel', 'tb_send_plan.seplan_typesubject', 'tb_send_plan.seplan_year', 'tb_send_plan.seplan_term', 'tb_send_plan.seplan_usersend', 'tb_send_plan.seplan_learning', 'tb_send_plan.seplan_file', 'tb_send_plan.seplan_status1', 'tb_send_plan.seplan_status2', 'tb_send_plan.seplan_typeplan_id', 'u.pers_firstname', 'u.pers_lastname'])
            ->orderBy('tb_send_plan.seplan_namesubject', 'ASC')
            ->get()->getResult();

        // Fetch active plan types for the view
        $data['activePlanTypes'] = $this->db->table('tb_send_plan_type')
                                            ->select('type_name')
                                            ->where('is_active', 1)
                                            ->orderBy('type_id', 'ASC')
                                            ->get()->getResultArray();

        return view('teacher/curriculum/plan_check_head_detail', $data);
    }

    public function update_status1()
    {
        if ($this->request->isAJAX()) {
            $plan_id = $this->request->getPost('plan_id');
            $status = $this->request->getPost('status');

            $result = $this->curriculumModel->update($plan_id, ['seplan_status1' => $status]);

            if ($result) {
                return $this->response->setJSON(['success' => true]);
            }
        }
        return $this->response->setJSON(['success' => false]);
    }

    public function get_comment()
    {
        if ($this->request->isAJAX()) {
            $plan_id = $this->request->getPost('plan_id');
            $comment_type = $this->request->getPost('comment_type');
            $field = ($comment_type == 1) ? 'seplan_comment1' : 'seplan_comment2';

            $plan = $this->curriculumModel->find($plan_id);

            if ($plan) {
                return $this->response->setJSON(['comment' => $plan[$field]]);
            }
        }
        return $this->response->setJSON(['comment' => '']);
    }

    public function save_comment()
    {
        if ($this->request->isAJAX()) {
            $plan_id = $this->request->getPost('plan_id');
            $comment_type = $this->request->getPost('comment_type');
            $comment = $this->request->getPost('comment');
            $field = ($comment_type == 1) ? 'seplan_comment1' : 'seplan_comment2';

            $result = $this->curriculumModel->update($plan_id, [$field => $comment]);

            if ($result) {
                return $this->response->setJSON(['success' => true]);
            }
        }
        return $this->response->setJSON(['success' => false]);
    }

    public function settingPlan()
    {
        $data['title'] = "ตั้งค่า";
        $data['OnOff'] = [$this->setup];
        $data['SetPlan'] = [$this->setup];

        return view('teacher/curriculum/plan_setting_plan', $data);
    }

    public function settingUpdatePlan()
    {
        $post = $this->request->getPost();

        $dateS = str_replace('/', '-', $post['seplanset_startdate']);
        $startDate = date('Y-m-d H:i:s', strtotime($dateS));
        $dateE = str_replace('/', '-', $post['seplanset_enddate']);
        $endDate = date('Y-m-d H:i:s', strtotime($dateE));

        $data = [
            'seplanset_startdate' => $startDate,
            'seplanset_enddate'   => $endDate,
            'seplanset_usersetup' => $this->session->get('person_id'),
            'seplanset_year'      => $post['seplanset_year'] ?? null,
            'seplanset_term'      => $post['seplan_term'] ?? null,
            'seplan_status'    => $post['seplanset_status'] ?? 'off',
        ];

        $result = $this->db->table('tb_send_plan_setup')->where('seplanset_ID', 1)->update($data);

        if ($result) {
            $this->session->setFlashdata(['status' => 'success', 'msg' => 'YES', 'messge' => 'ตั้งค่าสำเร็จ']);
        } else {
            $this->session->setFlashdata(['status' => 'error', 'msg' => 'YES', 'messge' => 'ตั้งค่าไม่สำเร็จ']);
        }
        return redirect()->to('curriculum/setting-plan');
    }
}
