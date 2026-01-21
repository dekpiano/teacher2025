<?php

namespace App\Controllers;

use App\Models\LeaveRequestModel;
use App\Models\LeaveTypeModel;
use CodeIgniter\Controller;

class LeaveController extends BaseController
{
    protected $leaveRequestModel;
    protected $leaveTypeModel;
    protected $db;

    public function __construct()
    {
        $this->leaveRequestModel = new LeaveRequestModel();
        $this->leaveTypeModel = new LeaveTypeModel();
        $this->db = \Config\Database::connect('personnel');
    }

    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        // Track page visit
        helper('recent_pages');
        track_recent_page('leave', 'ระบบการลา', 'bi-calendar-check');

        $pers_id = $session->get('person_id');
        $leaveTypes = $this->leaveTypeModel->where('leave_type_status', 'active')->findAll();
        
        // Get active leave year
        $db_personnel = \Config\Database::connect('personnel');
        $activeYear = $db_personnel->table('tb_leave_years')
            ->where('ly_status', 'active')
            ->get()
            ->getRow();

        // Calculate leave summary for each type
        $leaveSummary = [];
        foreach ($leaveTypes as $type) {
            $used = $this->leaveRequestModel->getUsedDays($pers_id, $type['leave_type_id']);
            $leaveSummary[] = [
                'type_id' => $type['leave_type_id'],
                'type_name' => $type['leave_type_name'],
                'quota' => (float)$type['leave_type_quota'],
                'used' => $used,
                'remaining' => (float)$type['leave_type_quota'] - $used,
            ];
        }

        $data = [
            'title' => 'ระบบการลา',
            'leaves' => $this->leaveRequestModel->getCombinedLeaveHistory($pers_id),
            'leaveTypes' => $leaveTypes,
            'leaveSummary' => $leaveSummary,
            'lateCount' => $this->leaveRequestModel->getLateCount($pers_id),
            'lateDetails' => $this->leaveRequestModel->getLateDetails($pers_id),
            'activeYear' => $activeYear,
        ];

        return view('teacher/leave/index', $data);
    }

    public function create()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $validation = \Config\Services::validation();
        $rules = [
            'leave_type_id' => 'required',
            'leave_topic' => 'required',
            'leave_start_date' => 'required',
            'leave_end_date' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $leaveTypeId = $this->request->getPost('leave_type_id');
        $startDate = $this->request->getPost('leave_start_date');
        $endDate = $this->request->getPost('leave_end_date');
        
        // Simple day calculation
        $diff = strtotime($endDate) - strtotime($startDate);
        $totalDays = round($diff / (60 * 60 * 24)) + 1;

        // Check quota
        $type = $this->leaveTypeModel->find($leaveTypeId);
        if ($type) {
            $used = $this->leaveRequestModel->getUsedDays($session->get('person_id'), $leaveTypeId);
            if (($used + $totalDays) > $type['leave_type_quota']) {
                return redirect()->back()->withInput()->with('error', 'คุณครูมีวันลาคงไม่พอ (ใช้ไปแล้ว ' . $used . '/' . $type['leave_type_quota'] . ' วัน)');
            }
        }

        $file = $this->request->getFile('leave_file');
        $fileName = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/leaves', $fileName);
        }

        $saveData = [
            'pers_id' => $session->get('person_id'),
            'leave_type_id' => $leaveTypeId,
            'leave_topic' => $this->request->getPost('leave_topic'),
            'leave_detail' => $this->request->getPost('leave_detail'),
            'leave_start_date' => $startDate,
            'leave_end_date' => $endDate,
            'leave_total_days' => $totalDays,
            'leave_period' => $this->request->getPost('leave_period'),
            'leave_contact_address' => $this->request->getPost('leave_contact_address'),
            'leave_contact_phone' => $this->request->getPost('leave_contact_phone'),
            'leave_file' => $fileName,
            'leave_status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->leaveRequestModel->insert($saveData);

        // Send Email Notification
        $this->sendLeaveNotificationEmail($saveData);

        return redirect()->to('leave')->with('success', 'ส่งใบลาเรียบร้อยแล้ว');
    }

    public function delete($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $leave = $this->leaveRequestModel->find($id);
        if ($leave && $leave['pers_id'] == $session->get('person_id') && $leave['leave_status'] == 'pending') {
            $this->leaveRequestModel->delete($id);
            return redirect()->to('leave')->with('success', 'ยกเลิกใบลาเรียบร้อยแล้ว');
        }

        return redirect()->to('leave')->with('error', 'ไม่สามารถยกเลิกใบลาได้');
    }

    public function check_quota()
    {
        $session = session();
        $leaveTypeId = $this->request->getPost('leave_type_id');
        $startDate = $this->request->getPost('leave_start_date');
        $endDate = $this->request->getPost('leave_end_date');

        $type = $this->leaveTypeModel->find($leaveTypeId);
        if (!$type) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบประเภทการลา']);
        }

        $used = $this->leaveRequestModel->getUsedDays($session->get('person_id'), $leaveTypeId);
        $totalDays = 0;
        if ($startDate && $endDate && strtotime($endDate) >= strtotime($startDate)) {
            $diff = strtotime($endDate) - strtotime($startDate);
            $totalDays = round($diff / (60 * 60 * 24)) + 1;
        }

        $quota = (float)$type['leave_type_quota'];
        $remaining = $quota - $used;
        $canLeave = ($totalDays <= 0) ? true : (($used + $totalDays) <= $quota);

        return $this->response->setJSON([
            'status' => 'success',
            'quota' => $quota,
            'used' => $used,
            'remaining' => $remaining,
            'request_days' => $totalDays,
            'can_leave' => $canLeave,
            'debug' => [
                'pers_id' => $session->get('person_id'),
                'leave_type_id' => $leaveTypeId,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    private function sendLeaveNotificationEmail($leaveData)
    {
        // Don't send real emails on localhost/development
        if (ENVIRONMENT === 'development') {
            log_message('info', 'Leave Notification (Dev Mode): Skipping email send for ' . $leaveData['leave_topic']);
            return true; 
        }

        $db_personnel = \Config\Database::connect('personnel');
        
        // Find approvers (e.g., HR Manager or Principal)
        $approvers = $db_personnel->table('tb_admin_rloes')
            ->select('tb_personnel.pers_username, tb_personnel.pers_firstname, tb_personnel.pers_lastname')
            ->join('tb_personnel', 'tb_personnel.pers_id = tb_admin_rloes.admin_rloes_userid')
            ->whereIn('admin_rloes_nanetype', ['รองผู้อำนวยการบริหารงานบุคคลกร', 'ผู้อำนวยการโรงเรียน'])
            ->get()
            ->getResultArray();

        if (empty($approvers)) {
            log_message('error', 'Leave Notification: No approvers found in tb_admin_rloes');
            return false;
        }

        $email = \Config\Services::email();
        $senderName = session()->get('fullname');

        // Configure Mail Type to HTML for professionalism
        $email->setMailType('html');

        foreach ($approvers as $approver) {
            try {
                $email->clear();
                $email->setFrom('noreply@skj.ac.th', 'ระบบการลาออนไลน์ SKJ');
                $email->setTo($approver['pers_username']);
                $email->setSubject('แจ้งเตือนใบลาใหม่: ' . $senderName);
                
                $message = "
                <div style='font-family: sans-serif; line-height: 1.6;'>
                    <h2>เรียน " . $approver['pers_firstname'] . " " . $approver['pers_lastname'] . "</h2>
                    <p>มีการส่งใบลาใหม่ในระบบงานครู โดยมีรายละเอียดดังนี้:</p>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr><td style='width: 120px; font-weight: bold;'>ผู้ลา:</td><td>" . $senderName . "</td></tr>
                        <tr><td style='font-weight: bold;'>เรื่อง:</td><td>" . $leaveData['leave_topic'] . "</td></tr>
                        <tr><td style='font-weight: bold;'>วันที่ลา:</td><td>" . date('d/m/Y', strtotime($leaveData['leave_start_date'])) . " ถึง " . date('d/m/Y', strtotime($leaveData['leave_end_date'])) . "</td></tr>
                        <tr><td style='font-weight: bold;'>จำนวนวัน:</td><td>" . $leaveData['leave_total_days'] . " วัน</td></tr>
                    </table>
                    <p style='margin-top: 20px;'>
                        <a href='" . site_url('admin/leave') . "' style='background-color: #696cff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>ดูรายละเอียดและอนุมัติ</a>
                    </p>
                    <hr>
                    <small style='color: #888;'>นี่คืออีเมลแจ้งเตือนอัตโนมัติ กรุณาอย่าตอบกลับ</small>
                </div>";

                $email->setMessage($message);
                
                if (!$email->send()) {
                    log_message('error', 'Email Send Failed: ' . $email->printDebugger(['headers']));
                }
            } catch (\Exception $e) {
                log_message('error', 'Email Exception: ' . $e->getMessage());
            }
        }
        return true;
    }

    /**
     * Print leave request as PDF using mPDF with PDF Template
     * ใช้ไฟล์ form-la.pdf เป็น template แล้วเขียนข้อมูลทับลงไป
     */
    public function printPdf($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        // Get leave request with type
        $leave = $this->db->table('tb_leave_requests')
            ->select('tb_leave_requests.*, tb_leave_types.leave_type_name')
            ->join('tb_leave_types', 'tb_leave_types.leave_type_id = tb_leave_requests.leave_type_id')
            ->where('leave_id', $id)
            ->get()
            ->getRowArray();

        if (!$leave || $leave['pers_id'] != $session->get('person_id')) {
            return redirect()->to('leave')->with('error', 'ไม่พบข้อมูลใบลา');
        }

        // Get personnel info with position name from skj database
        $db_personnel = \Config\Database::connect('personnel');
        $db_skj = \Config\Database::connect('skj');
        $personnel = $db_personnel->table('tb_personnel')
            ->select('tb_personnel.*, ' . $db_skj->database . '.tb_position.posi_name')
            ->join($db_skj->database . '.tb_position', $db_skj->database . '.tb_position.posi_id = tb_personnel.pers_position', 'left')
            ->where('pers_id', $leave['pers_id'])
            ->get()
            ->getRowArray();

        // ดึงข้อมูลการลาครั้งสุดท้าย (ลาอะไรก็ได้ก่อนหน้านี้)
        $lastLeave = $this->db->table('tb_leave_requests')
            ->select('tb_leave_requests.*, tb_leave_types.leave_type_name')
            ->join('tb_leave_types', 'tb_leave_types.leave_type_id = tb_leave_requests.leave_type_id')
            ->where('pers_id', $leave['pers_id'])
            ->where('leave_id <', $leave['leave_id'])
            ->orderBy('leave_id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        // Get leave types for statistics
        $leaveTypes = $this->leaveTypeModel->where('leave_type_status', 'active')->findAll();
        $leaveStats = [];
        foreach ($leaveTypes as $type) {
            $usedBefore = $this->leaveRequestModel->getUsedDays($leave['pers_id'], $type['leave_type_id']);
            if ($type['leave_type_id'] == $leave['leave_type_id']) {
                $usedBefore = max(0, $usedBefore - $leave['leave_total_days']);
            }
            $leaveStats[$type['leave_type_name']] = [
                'used_before' => $usedBefore,
                'current' => ($type['leave_type_id'] == $leave['leave_type_id']) ? $leave['leave_total_days'] : 0,
            ];
        }

        // Thai months array
        $thaiMonths = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
            5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
            9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
        ];

        // Load TCPDF + FPDI from SHARED_LIB_PATH
        require_once SHARED_LIB_PATH . '/tcpdf/vendor/autoload.php';

        // Create PDF instance using FPDI
        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi('P', 'mm', 'A4', true, 'UTF-8', false);
        
        $pdf->SetCreator('SKJ Leave System');
        $pdf->SetAuthor('SKJ');
        $pdf->SetTitle('ใบลา - ' . $leave['leave_topic']);
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        
        // Add page and import Template
        $pdf->AddPage();
        $templatePath = ROOTPATH . 'uploads/personnel/form-la.pdf';
        if (file_exists($templatePath)) {
            $pdf->setSourceFile($templatePath);
            $tplId = $pdf->importPage(1);
            $pdf->useTemplate($tplId, 0, 0, 210, 297, true);
        }

        // Add TH Sarabun New Font
        $fontPath = SHARED_LIB_PATH . '/mpdf/vendor/mpdf/mpdf/ttfonts/THSarabunNew.ttf';
        $fontPathBold = SHARED_LIB_PATH . '/mpdf/vendor/mpdf/mpdf/ttfonts/THSarabunNew Bold.ttf';
        
        if (file_exists($fontPath)) {
            $fontname = \TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
            if (file_exists($fontPathBold)) {
                $fontnameBold = \TCPDF_FONTS::addTTFfont($fontPathBold, 'TrueTypeUnicode', '', 96);
            }
        }

        // Set Thai Font - TH Sarabun New (ฟอนต์ราชการไทย)
        $thaiFont = $fontname ?? 'freeserif';
        $pdf->SetFont($thaiFont, '', 16);

        // Variables
        $createdDate = strtotime($leave['created_at']);
        $startDate = strtotime($leave['leave_start_date']);
        $endDate = strtotime($leave['leave_end_date']);
        $fullName = ($personnel['pers_prefix'] ?? '') . ($personnel['pers_firstname'] ?? '') . ' ' . ($personnel['pers_lastname'] ?? '');
        
        // ดึงชื่อตำแหน่งจากข้อมูลที่ JOIN มา
        $position = $personnel['posi_name'] ?? 'ครู';
        
        $department = 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์';
        $contactAddress = $leave['leave_contact_address'] ?? $personnel['pers_address'] ?? '';
        $contactPhone = $leave['leave_contact_phone'] ?? $personnel['pers_phone'] ?? '';

        // --- Start Writing Data (Fine-Tuned ตาม Grid) ---

        // เขียนที่ (โรงเรียน...) - Y≈32
        $pdf->SetXY(118, 38); $pdf->Cell(70, 6, 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์', 0, 0, 'C');

        // วันที่เขียนใบลา (ขวาบน) - Y≈40
        $pdf->SetXY(108, 46); $pdf->Cell(15, 6, date('j', $createdDate), 0, 0, 'C');
        $pdf->SetXY(130, 46); $pdf->Cell(32, 6, $thaiMonths[date('n', $createdDate)], 0, 0, 'C');
        $pdf->SetXY(170, 46); $pdf->Cell(15, 6, (date('Y', $createdDate) + 543), 0, 0, 'C');

        // เรื่อง - Y≈52
        $pdf->SetXY(33, 53); $pdf->Cell(150, 6, $leave['leave_topic'], 0, 0, 'L');

        // ข้าพเจ้า / ตำแหน่ง / สังกัด - Y≈70
        $pdf->SetXY(45, 68); $pdf->Cell(62, 6, $fullName, 0, 0, 'L');
        $pdf->SetXY(114, 68); $pdf->Cell(40, 6, $position, 0, 0, 'L');
        $pdf->SetXY(160, 68); $pdf->Cell(28, 6, 'กองการศึกษา ฯ', 0, 0, 'L');

        // ประเภทการลา (Checkboxes) - ลาป่วย Y≈82, ลากิจ Y≈91, ลาคลอด Y≈100
        $pdf->SetFont($thaiFont, 'B', 18);
        if ($leave['leave_type_name'] == 'ลาป่วย') {
            $pdf->SetXY(36, 76); $pdf->Cell(5, 5, '/', 0, 0, 'C');
            $pdf->SetFont($thaiFont, '', 16);
            $pdf->SetXY(75, 76); $pdf->Cell(120, 6, $leave['leave_detail'], 0, 0, 'L');
        } elseif ($leave['leave_type_name'] == 'ลากิจส่วนตัว') {
            $pdf->SetXY(36, 84); $pdf->Cell(5, 5, '/', 0, 0, 'C');
            $pdf->SetFont($thaiFont, '', 16);
            $pdf->SetXY(75, 84); $pdf->Cell(120, 6, $leave['leave_detail'], 0, 0, 'L');
        } elseif ($leave['leave_type_name'] == 'ลาคลอดบุตร') {
            $pdf->SetXY(36, 92); $pdf->Cell(5, 5, '/', 0, 0, 'C');
        }

        // ระยะเวลาลา (ตั้งแต่ - ถึง) - Y≈110
        $pdf->SetFont($thaiFont, '', 16);
        $pdf->SetXY(36, 103); $pdf->Cell(15, 6, date('j', $startDate), 0, 0, 'C');
        $pdf->SetXY(40, 103); $pdf->Cell(30, 6, $thaiMonths[date('n', $startDate)], 0, 0, 'C');
        $pdf->SetXY(60, 103); $pdf->Cell(15, 6, (date('Y', $startDate) + 543), 0, 0, 'C');
        $pdf->SetXY(95, 103); $pdf->Cell(15, 6, date('j', $endDate), 0, 0, 'C');
        $pdf->SetXY(100, 103); $pdf->Cell(30, 6, $thaiMonths[date('n', $endDate)], 0, 0, 'C');
        $pdf->SetXY(120, 103); $pdf->Cell(15, 6, (date('Y', $endDate) + 543), 0, 0, 'C');
        
        // กำหนด, จำนวนวัน - Y≈119
        $pdf->SetXY(165, 103); $pdf->Cell(15, 6, number_format($leave['leave_total_days'], 1), 0, 0, 'C');

        // ล่าสุุด (ประวัติการลาครั้งสุดท้าย) - ปรับพิกัดเบื้องต้นให้หนีข้อมูลติดต่อ
        if ($lastLeave) {
            $pdf->SetFont($thaiFont, 'B', 18);
            if ($lastLeave['leave_type_name'] == 'ลาป่วย') { $pdf->SetXY(45, 111); $pdf->Cell(5, 5, '/', 0, 0, 'C'); }
            elseif ($lastLeave['leave_type_name'] == 'ลากิจส่วนตัว') { $pdf->SetXY(57, 111); $pdf->Cell(5, 5, '/', 0, 0, 'C'); }
            elseif ($lastLeave['leave_type_name'] == 'ลาคลอดบุตร') { $pdf->SetXY(80, 111); $pdf->Cell(5, 5, '/', 0, 0, 'C'); }

            $pdf->SetFont($thaiFont, '', 16);
            $lStart = strtotime($lastLeave['leave_start_date']);
            $lEnd = strtotime($lastLeave['leave_end_date']);
            
            // ตั้งแต่วันที่ (ครั้งสุดท้าย) - Y≈111
            $lastLeaveStr = date('j', $lStart) . ' ' . $thaiMonths[date('n', $lStart)] . ' ' . (date('Y', $lStart) + 543);
            $pdf->SetXY(140, 111); $pdf->Cell(95, 6, $lastLeaveStr, 0, 0, 'L');
            $lastLeaveStr = date('j', $lEnd) . ' ' . $thaiMonths[date('n', $lEnd)] . ' ' . (date('Y', $lEnd) + 543);
            $pdf->SetXY(33, 118); $pdf->Cell(95, 6, $lastLeaveStr, 0, 0, 'L');
            // จำนวนวัน - Y≈118 (แถวเดียวกับเบอร์โทร แต่อยู่คนละฝั่ง)
            $pdf->SetXY(89, 118); $pdf->Cell(15, 6, number_format($lastLeave['leave_total_days'], 1), 0, 0, 'C');
        }

        // ข้อมูลติดต่อ - เบอร์โทร Y≈128, ที่อยู่ Y≈137
        $pdf->SetFont($thaiFont, '', 16);
        $pdf->SetXY(153, 118); $pdf->Cell(45, 6, $contactPhone, 0, 0, 'C');
        $pdf->SetXY(30, 125); $pdf->Cell(170, 6, $contactAddress, 0, 0, 'L');

        // ลงชื่อผู้ลา (ขวาล่าง) - Y≈155, Y≈163
        $pdf->SetXY(113, 148); $pdf->Cell(65, 6, $fullName , 0, 0, 'C');
        $pdf->SetXY(113, 156); $pdf->Cell(65, 6, $position, 0, 0, 'C');

        // --- ตารางสถิติ (ล่างซ้าย) - ป่วย Y≈185, กิจ Y≈195, คลอด Y≈205 ---
        $pdf->SetFont($thaiFont, '', 14);
        // ป่วย - Y≈185
        $pdf->SetXY(41, 187); $pdf->Cell(20, 6, number_format($leaveStats['ลาป่วย']['used_before'], 1), 0, 0, 'C');
        $pdf->SetXY(63, 187); $pdf->Cell(20, 6, ($leave['leave_type_name']=='ลาป่วย' ? number_format($leave['leave_total_days'], 1) : '-'), 0, 0, 'C');
        $pdf->SetXY(85, 187); $pdf->Cell(20, 6, number_format($leaveStats['ลาป่วย']['used_before'] + ($leave['leave_type_name']=='ลาป่วย' ? $leave['leave_total_days'] : 0), 1), 0, 0, 'C');
        // กิจ - Y≈195
        $pdf->SetXY(41, 195); $pdf->Cell(20, 6, number_format($leaveStats['ลากิจส่วนตัว']['used_before'], 1), 0, 0, 'C');
        $pdf->SetXY(63, 195); $pdf->Cell(20, 6, ($leave['leave_type_name']=='ลากิจส่วนตัว' ? number_format($leave['leave_total_days'], 1) : '-'), 0, 0, 'C');
        $pdf->SetXY(85, 195); $pdf->Cell(20, 6, number_format($leaveStats['ลากิจส่วนตัว']['used_before'] + ($leave['leave_type_name']=='ลากิจส่วนตัว' ? $leave['leave_total_days'] : 0), 1), 0, 0, 'C');
        // คลอด - Y≈205
        $pdf->SetXY(41, 203); $pdf->Cell(20, 6, number_format($leaveStats['ลาคลอดบุตร']['used_before'], 1), 0, 0, 'C');
        $pdf->SetXY(63, 203); $pdf->Cell(20, 6, ($leave['leave_type_name']=='ลาคลอดบุตร' ? number_format($leave['leave_total_days'], 1) : '-'), 0, 0, 'C');
        $pdf->SetXY(85, 203); $pdf->Cell(20, 6, number_format($leaveStats['ลาคลอดบุตร']['used_before'] + ($leave['leave_type_name']=='ลาคลอดบุตร' ? $leave['leave_total_days'] : 0), 1), 0, 0, 'C');

        // Output
        $this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output('ใบลา_' . $leave['leave_id'] . '.pdf', 'I');
        exit;

        // Output
        $this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output('ใบลา_' . $leave['leave_id'] . '.pdf', 'I');
        exit;
    }
}

