<?php

namespace App\Controllers;

use App\Models\PaAgreementModel;
use CodeIgniter\HTTP\ResponseInterface;

class PaAgreementController extends BaseController
{
    protected $paModel;
    protected $configModel;
    protected $db;
    protected $db_personnel;
    protected $session;

    public function __construct()
    {
        helper(['url', 'text', 'form']);
        $this->session = session();
        if (!$this->session->get('isLoggedIn')) {
            service('response')->redirect(base_url('login'))->send();
            exit;
        }

        $this->paModel = new \App\Models\PaAgreementModel();
        $this->configModel = new \App\Models\PaAgreementConfigModel();
        $this->db = db_connect();
        $this->db_personnel = db_connect('personnel');
    }

    /**
     * Check PA submission window status for a given fiscal year
     */
    private function getSubmissionStatus($year)
    {
        $config = $this->configModel->getConfigByYear($year);
        $now = date('Y-m-d H:i:s');
        
        $isOpen = false;
        $statusCode = 'no_config';
        $statusText = 'ระบบเปิดรับเอกสาร';
        $badgeClass = 'bg-label-success';
        $message = '';

        if ($config) {
            $manualOpen = ((int)$config['conf_status'] === 1);
            $hasStart = !empty($config['conf_start_datetime']);
            $hasEnd = !empty($config['conf_end_datetime']);

            if (!$manualOpen) {
                $isOpen = false;
                $statusCode = 'closed_manually';
                $statusText = 'ปิดรับเอกสารชั่วคราว';
                $badgeClass = 'bg-label-secondary';
                $message = 'ระบบปิดรับการส่งเอกสารชั่วคราวโดยผู้ดูแลระบบ';
            } elseif ($hasStart && $now < $config['conf_start_datetime']) {
                $isOpen = false;
                $statusCode = 'not_started';
                $statusText = 'ยังไม่ถึงกำหนดเวลาส่ง';
                $badgeClass = 'bg-label-warning';
                $startFormatted = date('d/m/', strtotime($config['conf_start_datetime'])) . (date('Y', strtotime($config['conf_start_datetime'])) + 543) . ' เวลา ' . date('H:i', strtotime($config['conf_start_datetime'])) . ' น.';
                $message = 'ยังไม่ถึงกำหนดเวลาเปิดรับเอกสาร (เริ่มเปิดระบบวันที่ ' . $startFormatted . ')';
            } elseif ($hasEnd && $now > $config['conf_end_datetime']) {
                $isOpen = false;
                $statusCode = 'expired';
                $statusText = 'สิ้นสุดกำหนดเวลาส่งแล้ว';
                $badgeClass = 'bg-label-danger';
                $endFormatted = date('d/m/', strtotime($config['conf_end_datetime'])) . (date('Y', strtotime($config['conf_end_datetime'])) + 543) . ' เวลา ' . date('H:i', strtotime($config['conf_end_datetime'])) . ' น.';
                $message = 'หมดเวลาการส่งเอกสารสำหรับปีงบประมาณนี้แล้ว (สิ้นสุดเมื่อวันที่ ' . $endFormatted . ')';
            } else {
                $isOpen = true;
                $statusCode = 'open';
                $statusText = 'ระบบเปิดรับเอกสาร';
                $badgeClass = 'bg-label-success';
                if ($hasEnd) {
                    $endFormatted = date('d/m/', strtotime($config['conf_end_datetime'])) . (date('Y', strtotime($config['conf_end_datetime'])) + 543) . ' เวลา ' . date('H:i', strtotime($config['conf_end_datetime'])) . ' น.';
                    $message = 'เปิดรับเอกสารถึงวันที่ ' . $endFormatted;
                }
            }
        } else {
            // Default: if no config row exists, consider open
            $isOpen = true;
            $statusCode = 'open';
            $statusText = 'ระบบเปิดรับเอกสาร';
            $badgeClass = 'bg-label-success';
        }

        // Remaining time text if open and end date is set
        $remainingText = '';
        if ($isOpen && !empty($config['conf_end_datetime'])) {
            $diff = strtotime($config['conf_end_datetime']) - strtotime($now);
            if ($diff > 0) {
                $days = floor($diff / 86400);
                $hours = floor(($diff % 86400) / 3600);
                $mins = floor(($diff % 3600) / 60);
                if ($days > 0) {
                    $remainingText = "เหลือเวลาอีก {$days} วัน {$hours} ชั่วโมง";
                } elseif ($hours > 0) {
                    $remainingText = "เหลือเวลาอีก {$hours} ชั่วโมง {$mins} นาที";
                } else {
                    $remainingText = "เหลือเวลาอีก {$mins} นาที";
                }
            }
        }

        return [
            'is_open'        => $isOpen,
            'status_code'    => $statusCode,
            'status_text'    => $statusText,
            'badge_class'    => $badgeClass,
            'message'        => $message,
            'remaining_text' => $remainingText,
            'config'         => $config,
            'note'           => $config['conf_note'] ?? null,
            'start_datetime' => $config['conf_start_datetime'] ?? null,
            'end_datetime'   => $config['conf_end_datetime'] ?? null,
            'start_thai'     => !empty($config['conf_start_datetime']) ? date('d/m/', strtotime($config['conf_start_datetime'])) . (date('Y', strtotime($config['conf_start_datetime'])) + 543) . ' เวลา ' . date('H:i', strtotime($config['conf_start_datetime'])) . ' น.' : null,
            'end_thai'       => !empty($config['conf_end_datetime']) ? date('d/m/', strtotime($config['conf_end_datetime'])) . (date('Y', strtotime($config['conf_end_datetime'])) + 543) . ' เวลา ' . date('H:i', strtotime($config['conf_end_datetime'])) . ' น.' : null,
        ];
    }

    /**
     * Determine current fiscal year
     */
    private function getCurrentFiscalYear()
    {
        $month = (int)date('n');
        $year = (int)date('Y') + 543;
        if ($month >= 10) {
            return $year + 1;
        }
        return $year;
    }

    /**
     * Check if user is a Government Teacher (ข้าราชการครู)
     */
    private function _checkPermission()
    {
        $teacherId = $this->session->get('person_id');
        if (empty($teacherId)) return false;

        $db_skj = db_connect('skj');
        $user = $this->db_personnel->table('tb_personnel')
            ->select('tb_personnel.*, ' . $db_skj->database . '.tb_position.posi_name')
            ->join($db_skj->database . '.tb_position', $db_skj->database . '.tb_position.posi_id = tb_personnel.pers_position', 'left')
            ->where('pers_id', $teacherId)
            ->get()
            ->getRowArray();

        if (!$user) return false;

        $position = $user['posi_name'] ?? '';
        
        // Allowed positions: Government Teachers (ข้าราชการครู)
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

        return in_array($position, $allowedPositions);
    }

    /**
     * Main PA Agreement View
     */
    public function index($year = null)
    {
        if (!$this->_checkPermission()) {
            return redirect()->to('home')->with('error_gov_teacher_only', 'ระบบข้อตกลงในการพัฒนางาน (PA) นี้ อนุญาตให้ใช้งานเฉพาะ "ข้าราชการครู" เท่านั้น');
        }

        $currentYear = $year ? (int)$year : $this->getCurrentFiscalYear();
        $personId = $this->session->get('person_id');

        $data['title'] = "การประเมินผลการพัฒนางานตามข้อตกลง (PA)";
        $data['current_year'] = $currentYear;
        $data['agreement'] = $this->paModel->getAgreement($personId, $currentYear);
        $data['history'] = $this->paModel->getHistory($personId);
        $data['system_config'] = $this->getSubmissionStatus($currentYear);

        return view('teacher/pa_agreement/pa_agreement_main', $data);
    }

    /**
     * Upload / Save endpoint
     */
    public function upload()
    {
        $post = $this->request->getPost();
        $teacherId = $this->session->get('person_id');
        $year = $post['pa_year'] ?? $this->getCurrentFiscalYear();

        // Check if submission is open
        $systemConfig = $this->getSubmissionStatus($year);
        if (!$systemConfig['is_open']) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ขออภัย ไม่สามารถส่งงานได้ เนื่องจากระบบปิดรับเอกสารแล้ว (' . $systemConfig['status_text'] . ')'
            ]);
        }

        $existing = $this->paModel->getAgreement($teacherId, $year);

        $insertData = [
            'pa_teacher_id' => $teacherId,
            'pa_year'       => $year,
        ];

        // 1. Presentation Link
        if (isset($post['pa_presentation_link'])) {
            $insertData['pa_presentation_link'] = trim($post['pa_presentation_link']);
        }

        // 1.5 Presentation File (from chunked upload completion)
        if (!empty($post['uploaded_presentation_filename'])) {
            if ($existing && !empty($existing['pa_file_presentation']) && $existing['pa_file_presentation'] !== $post['uploaded_presentation_filename']) {
                $this->_deleteFileFromServer("personnel/teacher/pa_agreement/{$year}/presentation/{$existing['pa_file_presentation']}");
            }
            $insertData['pa_file_presentation'] = $post['uploaded_presentation_filename'];
        }

        // 2. Lesson Plan File (from chunked upload completion or standard post)
        if (!empty($post['uploaded_lesson_plan_filename'])) {
            if ($existing && !empty($existing['pa_file_lesson_plan']) && $existing['pa_file_lesson_plan'] !== $post['uploaded_lesson_plan_filename']) {
                $this->_deleteFileFromServer("personnel/teacher/pa_agreement/{$year}/lesson_plan/{$existing['pa_file_lesson_plan']}");
            }
            $insertData['pa_file_lesson_plan'] = $post['uploaded_lesson_plan_filename'];
        }

        // 3. PA1 File (from chunked upload completion or standard post)
        if (!empty($post['uploaded_pa1_filename'])) {
            if ($existing && !empty($existing['pa_file_pa1']) && $existing['pa_file_pa1'] !== $post['uploaded_pa1_filename']) {
                $this->_deleteFileFromServer("personnel/teacher/pa_agreement/{$year}/pa1/{$existing['pa_file_pa1']}");
            }
            $insertData['pa_file_pa1'] = $post['uploaded_pa1_filename'];
        }

        if ($existing) {
            $result = $this->paModel->update($existing['pa_id'], $insertData);
        } else {
            $result = $this->paModel->insert($insertData);
        }

        if ($result) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
        }
    }

    /**
     * Proxies file chunks from browser to the remote upload server
     */
    public function uploadChunk()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200);
        }

        $file = $this->request->getFile('file');
        $post = $this->request->getPost();
        
        $path = $post['path'] ?? '';
        $chunkYear = null;
        if (preg_match('/pa_agreement\/(\d{4})/', $path, $matches)) {
            $chunkYear = $matches[1];
        }
        $chunkYear = $chunkYear ?: $this->getCurrentFiscalYear();

        // Check submission window
        $windowStatus = $this->getSubmissionStatus($chunkYear);
        if (!$windowStatus['is_open']) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ขออภัย ไม่สามารถอัปโหลดไฟล์ได้ เนื่องจากระบบปิดรับเอกสารแล้ว (' . $windowStatus['status_text'] . ')'
            ]);
        }

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
                'http_errors' => false,
                'timeout' => 120,
                'connect_timeout' => 30,
                'verify' => false,
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
                ]
            ]);

            return $this->response->setContentType('application/json')
                                  ->setStatusCode($response->getStatusCode())
                                  ->setBody($response->getBody());
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete an item (link or specific file)
     */
    public function deleteItem()
    {
        $id = $this->request->getPost('id');
        $type = $this->request->getPost('type'); // 'presentation', 'lesson_plan', 'pa1', or 'all'
        
        $agreement = $this->paModel->find($id);
        if (!$agreement) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลที่ต้องการลบ']);
        }

        // Security check
        if ($agreement['pa_teacher_id'] !== $this->session->get('person_id') && $this->session->get('person_id') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่มีสิทธิ์ในการลบข้อมูลนี้']);
        }

        $year = $agreement['pa_year'];

        // Check if submission is open
        $windowStatus = $this->getSubmissionStatus($year);
        if (!$windowStatus['is_open'] && $this->session->get('person_id') !== 'admin') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ขออภัย ไม่สามารถลบข้อมูลได้ เนื่องจากระบบปิดรับเอกสารแล้ว (' . $windowStatus['status_text'] . ')'
            ]);
        }

        if ($type === 'presentation') {
            $this->paModel->update($id, ['pa_presentation_link' => null]);
            return $this->response->setJSON(['status' => 'success', 'message' => 'ลบลิ้งก์นำเสนอเรียบร้อย']);
        }

        if ($type === 'presentation_file') {
            if (!empty($agreement['pa_file_presentation'])) {
                $this->_deleteFileFromServer("personnel/teacher/pa_agreement/{$year}/presentation/{$agreement['pa_file_presentation']}");
            }
            $this->paModel->update($id, ['pa_file_presentation' => null]);
            return $this->response->setJSON(['status' => 'success', 'message' => 'ลบไฟล์สื่อนำเสนอเรียบร้อย']);
        }

        if ($type === 'lesson_plan') {
            if (!empty($agreement['pa_file_lesson_plan'])) {
                $this->_deleteFileFromServer("personnel/teacher/pa_agreement/{$year}/lesson_plan/{$agreement['pa_file_lesson_plan']}");
            }
            $this->paModel->update($id, ['pa_file_lesson_plan' => null]);
            return $this->response->setJSON(['status' => 'success', 'message' => 'ลบไฟล์แผนการจัดการเรียนรู้เรียบร้อย']);
        }

        if ($type === 'pa1') {
            if (!empty($agreement['pa_file_pa1'])) {
                $this->_deleteFileFromServer("personnel/teacher/pa_agreement/{$year}/pa1/{$agreement['pa_file_pa1']}");
            }
            $this->paModel->update($id, ['pa_file_pa1' => null]);
            return $this->response->setJSON(['status' => 'success', 'message' => 'ลบไฟล์บันทึกข้อตกลง PA1 เรียบร้อย']);
        }

        if ($type === 'all') {
            if (!empty($agreement['pa_file_presentation'])) {
                $this->_deleteFileFromServer("personnel/teacher/pa_agreement/{$year}/presentation/{$agreement['pa_file_presentation']}");
            }
            if (!empty($agreement['pa_file_lesson_plan'])) {
                $this->_deleteFileFromServer("personnel/teacher/pa_agreement/{$year}/lesson_plan/{$agreement['pa_file_lesson_plan']}");
            }
            if (!empty($agreement['pa_file_pa1'])) {
                $this->_deleteFileFromServer("personnel/teacher/pa_agreement/{$year}/pa1/{$agreement['pa_file_pa1']}");
            }
            $this->paModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'ลบข้อมูลรายการนี้ทั้งหมดเรียบร้อย']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'รูปแบบการลบไม่ถูกต้อง']);
    }

    /**
     * Delete file from remote storage server via delete.php
     */
    private function _deleteFileFromServer($remoteFilePath)
    {
        $deleteUrl = env('upload.server.delete.url');
        if (empty($deleteUrl)) {
            log_message('error', 'Delete server URL is not configured in .env file.');
            return false;
        }

        try {
            $client = \Config\Services::curlrequest();

            $path = dirname($remoteFilePath);
            $filename = basename($remoteFilePath);

            $jsonData = json_encode([
                'path'  => str_replace('\\', '/', $path),
                'files' => [$filename]
            ]);

            $headers = [
                'Content-Type' => 'application/json',
                'X-Auth-Token' => env('upload.server.token') ?: 'Dekpiano2025!!'
            ];

            $response = $client->setBody($jsonData)->post($deleteUrl, [
                'headers'     => $headers,
                'http_errors' => false
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($statusCode === 200 && isset($body['status']) && ($body['status'] === 'success' || $body['status'] === 'partial_success')) {
                return true;
            }

            log_message('error', 'Delete server error response: ' . $response->getBody());
            return false;
        } catch (\Exception $e) {
            log_message('error', "Failed to delete remote file: {$remoteFilePath}. Error: " . $e->getMessage());
            return false;
        }
    }
}
