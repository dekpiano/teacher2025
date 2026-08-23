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

    public function seedHolidaysNow()
    {
        try {
            $db_personnel = \Config\Database::connect('personnel');
            
            // Create table
            $db_personnel->query("
                CREATE TABLE IF NOT EXISTS `tb_holidays` (
                    `holiday_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `holiday_date` DATE NOT NULL,
                    `holiday_name` VARCHAR(150) NOT NULL,
                    `holiday_type` ENUM('public', 'religious', 'special', 'school') DEFAULT 'public',
                    `holiday_year` INT(4) NOT NULL,
                    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`holiday_id`),
                    UNIQUE KEY `idx_holiday_date` (`holiday_date`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // Seed holidays
            $this->leaveRequestModel->seedInitialHolidays($db_personnel);

            $rows = $db_personnel->table('tb_holidays')->countAllResults();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'บันทึกข้อมูลวันหยุดลง tb_holidays เรียบร้อยแล้ว',
                'total_holidays_in_db' => $rows
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
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
        $db_personnel = \Config\Database::connect('personnel');

        // ตรวจสอบและสร้าง/Seed ข้อมูลวันหยุดลง tb_holidays อัตโนมัติ (ล่วงหน้า 5+ ปี)
        $this->leaveRequestModel->getThaiPublicHolidays(date('Y'));

        $leaveTypes = $this->leaveTypeModel->where('leave_type_status', 'active')->findAll();
        
        // Get all leave years for filter dropdown
        $leaveYears = $db_personnel->table('tb_leave_years')
            ->orderBy('ly_name', 'DESC')
            ->get()
            ->getResultArray();

        // Selected year from filter (or default to current active year)
        $selectedYearId = $this->request->getGet('year_id');
        $activeYear = null;

        if ($selectedYearId) {
            $activeYear = $db_personnel->table('tb_leave_years')
                ->where('ly_id', $selectedYearId)
                ->get()
                ->getRow();
        }

        if (!$activeYear) {
            $activeYear = $this->leaveRequestModel->getOrCreateActiveLeaveYear();
            $selectedYearId = $activeYear->ly_id ?? null;
        }

        // Calculate leave summary for each type based on selected year
        $leaveSummary = [];
        $totalAllQuota = 0;
        $totalAllUsed = 0;
        $totalAllRemaining = 0;

        foreach ($leaveTypes as $type) {
            $used = $this->leaveRequestModel->getUsedDays($pers_id, $type['leave_type_id'], $selectedYearId);
            $quota = (float)$type['leave_type_quota'];
            $remaining = $quota - $used;

            $totalAllQuota += $quota;
            $totalAllUsed += $used;
            $totalAllRemaining += $remaining;

            $leaveSummary[] = [
                'type_id' => $type['leave_type_id'],
                'type_name' => $type['leave_type_name'],
                'quota' => $quota,
                'used' => $used,
                'remaining' => $remaining,
            ];
        }

        // โควตารวมทั้งปีงบประมาณ = 46 วัน (23 วัน x 2 รอบ)
        $totalAllQuota = 46.0;
        $totalAllRemaining = max(0, $totalAllQuota - $totalAllUsed);

        // Get teacher profile for form prefill
        $teacher = $db_personnel->table('tb_personnel')
            ->where('pers_id', $pers_id)
            ->get()
            ->getRowArray();

        // Selected round from filter (รอบที่ 1 หรือ รอบที่ 2 หรือ ค่าว่างคือรอบปัจจุบัน)
        $selectedRound = $this->request->getGet('round') ?: null;

        // ดึงข้อมูลการลาและโควตาประจำรอบ 6 เดือน (สูงสุด 23 วัน/รอบ)
        $termLeaveInfo = $this->leaveRequestModel->getUsedDaysInTerm($pers_id, null, $activeYear, $selectedRound);

        $data = [
            'title' => 'ระบบการลา',
            'leaves' => $this->leaveRequestModel->getCombinedLeaveHistory($pers_id, $selectedYearId, $selectedRound),
            'leaveTypes' => $leaveTypes,
            'leaveSummary' => $leaveSummary,
            'totalAllQuota' => $totalAllQuota,
            'totalAllUsed' => $totalAllUsed,
            'totalAllRemaining' => $totalAllRemaining,
            'termLeaveInfo' => $termLeaveInfo,
            'lateCount' => $this->leaveRequestModel->getLateCount($pers_id, $selectedYearId),
            'lateDetails' => $this->leaveRequestModel->getLateDetails($pers_id, $selectedYearId),
            'activeYear' => $activeYear,
            'leaveYears' => $leaveYears,
            'selectedYearId' => $selectedYearId,
            'selectedRound' => $selectedRound,
            'teacher' => $teacher,
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
        $rawStartDate = $this->request->getPost('leave_start_date');
        $rawEndDate = $this->request->getPost('leave_end_date');
        $leavePeriod = $this->request->getPost('leave_period') ?: 'full';
        
        $startDate = $this->leaveRequestModel->parseThaiDateToStandard($rawStartDate);
        $endDate = $this->leaveRequestModel->parseThaiDateToStandard($rawEndDate);

        // คำนวณวันลาจริง (ไม่นับเสาร์-อาทิตย์ และวันหยุดนักขัตฤกษ์)
        $totalDays = $this->leaveRequestModel->calculateWorkingDays($startDate, $endDate, $leavePeriod);

        // 1. ตรวจสอบโควตาประเภทการลา
        $type = $this->leaveTypeModel->find($leaveTypeId);
        if ($type) {
            $used = $this->leaveRequestModel->getUsedDays($session->get('person_id'), $leaveTypeId);
            if (($used + $totalDays) > $type['leave_type_quota']) {
                return redirect()->back()->withInput()->with('error', 'คุณครูมีวันลาคงเหลือไม่พอสำหรับการลาครั้งนี้ (ใช้ไปแล้ว ' . $used . '/' . $type['leave_type_quota'] . ' วัน)');
            }
        }

        // 2. ตรวจสอบกฎโควตาสูงสุด 23 วัน/ภาคเรียน
        $termInfo = $this->leaveRequestModel->getUsedDaysInTerm($session->get('person_id'), $startDate);
        if (($termInfo['used_in_term'] + $totalDays) > $termInfo['max_quota']) {
            return redirect()->back()->withInput()->with('error', 'ไม่สามารถส่งใบลาได้ เนื่องจากยอดวันลารวมใน ' . $termInfo['term_info']['term_name'] . ' จะเกินโควตาสูงสุด 23 วัน (ปัจจุบันลาไปแล้ว ' . $termInfo['used_in_term'] . ' วัน, ขอลารวม ' . ($termInfo['used_in_term'] + $totalDays) . ' วัน)');
        }

        $file = $this->request->getFile('leave_file');
        $fileName = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/leaves', $fileName);
        }

        // Ensure columns exist in tb_leave_requests
        $fields = $this->db->getFieldNames('tb_leave_requests');
        if (!in_array('leave_contact_address', $fields)) {
            $this->db->query("ALTER TABLE tb_leave_requests ADD COLUMN leave_contact_address TEXT NULL AFTER leave_period");
        }
        if (!in_array('leave_contact_phone', $fields)) {
            $this->db->query("ALTER TABLE tb_leave_requests ADD COLUMN leave_contact_phone VARCHAR(50) NULL AFTER leave_contact_address");
        }

        $saveData = [
            'pers_id' => $session->get('person_id'),
            'leave_type_id' => $leaveTypeId,
            'leave_topic' => $this->request->getPost('leave_topic'),
            'leave_detail' => $this->request->getPost('leave_detail'),
            'leave_start_date' => $startDate,
            'leave_end_date' => $endDate,
            'leave_total_days' => $totalDays,
            'leave_period' => $leavePeriod,
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
        $rawStartDate = $this->request->getPost('leave_start_date');
        $rawEndDate = $this->request->getPost('leave_end_date');
        $leavePeriod = $this->request->getPost('leave_period') ?: 'full';

        $startDate = $this->leaveRequestModel->parseThaiDateToStandard($rawStartDate);
        $endDate = $this->leaveRequestModel->parseThaiDateToStandard($rawEndDate);

        $type = $this->leaveTypeModel->find($leaveTypeId);
        if (!$type) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบประเภทการลา']);
        }

        $used = $this->leaveRequestModel->getUsedDays($session->get('person_id'), $leaveTypeId);
        
        // คำนวณวันลาจริง (ไม่นับเสาร์-อาทิตย์ และวันหยุดนักขัตฤกษ์)
        $totalDays = $this->leaveRequestModel->calculateWorkingDays($startDate, $endDate, $leavePeriod);

        $quota = (float)$type['leave_type_quota'];
        $remaining = $quota - $used;
        $canLeave = ($totalDays <= 0) ? true : (($used + $totalDays) <= $quota);

        // ตรวจสอบโควตาประจำเทอม (สูงสุด 23 วัน)
        $termCheck = $this->leaveRequestModel->getUsedDaysInTerm($session->get('person_id'), $startDate);
        $termExceeded = false;
        $termMessage = '';
        if ($totalDays > 0 && ($termCheck['used_in_term'] + $totalDays) > $termCheck['max_quota']) {
            $canLeave = false;
            $termExceeded = true;
            $termMessage = 'วันลารวมใน ' . $termCheck['term_info']['term_name'] . ' จะเกินโควตาสูงสุด 23 วัน/เทอม (ใช้ไปแล้ว ' . $termCheck['used_in_term'] . ' วัน)';
        }

        return $this->response->setJSON([
            'status' => 'success',
            'quota' => $quota,
            'used' => $used,
            'remaining' => $remaining,
            'request_days' => $totalDays,
            'can_leave' => $canLeave,
            'term_check' => [
                'term_name' => $termCheck['term_info']['term_name'],
                'used_in_term' => $termCheck['used_in_term'],
                'remaining_in_term' => $termCheck['remaining_in_term'],
                'max_quota' => $termCheck['max_quota'],
                'term_exceeded' => $termExceeded,
                'term_message' => $termMessage,
            ],
            'debug' => [
                'pers_id' => $session->get('person_id'),
                'leave_type_id' => $leaveTypeId,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    public function filterData()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $pers_id = $session->get('person_id');
        $db_personnel = \Config\Database::connect('personnel');

        $selectedYearId = $this->request->getGet('year_id');
        $selectedRound = $this->request->getGet('round') ?: null;

        $activeYear = null;
        if ($selectedYearId) {
            $activeYear = $db_personnel->table('tb_leave_years')->where('ly_id', $selectedYearId)->get()->getRow();
        }
        if (!$activeYear) {
            $activeYear = $this->leaveRequestModel->getOrCreateActiveLeaveYear();
            $selectedYearId = $activeYear->ly_id ?? null;
        }

        $leaveTypes = $this->leaveTypeModel->findAll();
        $leaveSummary = [];
        $totalAllQuota = 46.0;
        $totalAllUsed = 0;

        foreach ($leaveTypes as $type) {
            $used = $this->leaveRequestModel->getUsedDays($pers_id, $type['leave_type_id'], $selectedYearId);
            $quota = (float)$type['leave_type_quota'];
            $remaining = $quota - $used;

            $totalAllUsed += $used;

            $leaveSummary[] = [
                'type_id' => $type['leave_type_id'],
                'type_name' => $type['leave_type_name'],
                'quota' => $quota,
                'used' => $used,
                'remaining' => $remaining,
            ];
        }

        $totalAllRemaining = max(0, $totalAllQuota - $totalAllUsed);
        $termLeaveInfo = $this->leaveRequestModel->getUsedDaysInTerm($pers_id, null, $activeYear, $selectedRound);
        $leaves = $this->leaveRequestModel->getCombinedLeaveHistory($pers_id, $selectedYearId, $selectedRound);
        $lateCount = $this->leaveRequestModel->getLateCount($pers_id, $selectedYearId);

        return $this->response->setJSON([
            'status' => 'success',
            'activeYear' => $activeYear,
            'leaveSummary' => $leaveSummary,
            'totalAllQuota' => $totalAllQuota,
            'totalAllUsed' => $totalAllUsed,
            'totalAllRemaining' => $totalAllRemaining,
            'termLeaveInfo' => $termLeaveInfo,
            'leaves' => $leaves,
            'lateCount' => $lateCount,
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

        // Get personnel info
        $db_personnel = \Config\Database::connect('personnel');
        $db_skj = \Config\Database::connect('skj');
        $personnel = $db_personnel->table('tb_personnel')
            ->where('pers_id', $leave['pers_id'])
            ->get()
            ->getRowArray();

        // Get position name and learning group name separately for safety
        if ($personnel) {
            if (!empty($personnel['pers_position'])) {
                $pos = $db_skj->table('tb_position')->where('posi_id', $personnel['pers_position'])->get()->getRowArray();
                $personnel['posi_name'] = $pos['posi_name'] ?? 'ครู';
            } else {
                $personnel['posi_name'] = 'ครู';
            }

            if (!empty($personnel['pers_learning'])) {
                $lear = $db_skj->table('tb_learning')->where('lear_id', $personnel['pers_learning'])->get()->getRowArray();
                $personnel['lear_namethai'] = $lear['lear_namethai'] ?? '';
            } else {
                $personnel['lear_namethai'] = '';
            }
        }

        // ดึงข้อมูลผู้ตรวจสอบ (เจ้าหน้าที่ที่อนุมัติ จากคอลัมน์ approved_by)
        $approver = null;
        if (!empty($leave['approved_by'])) {
            $approver = $db_personnel->table('tb_personnel')
                ->where('pers_id', $leave['approved_by'])
                ->get()
                ->getRowArray();
            
            if ($approver) {
                $pos = $db_skj->table('tb_position')
                    ->where('posi_id', $approver['pers_position'])
                    ->get()
                    ->getRowArray();
                $approver['posi_name'] = $pos['posi_name'] ?? 'เจ้าหน้าที่';
            }
        }

        // ดึงข้อมูล รองผู้อำนวยการฝ่ายบริหารงานบุคคล (ความเห็นผู้บังคับบัญชา)
        $deputyDirector = null;
        $deputyRole = $db_personnel->table('tb_admin_rloes')
            ->whereIn('admin_rloes_nanetype', ['รองผู้อำนวยการบริหารงานบุคคลกร', 'รองผู้อำนวยการฝ่ายบริหารงานบุคคล', 'รองผู้อำนวยการโรงเรียน', 'รองผู้อำนวยการสถานศึกษา'])
            ->get()
            ->getRowArray();

        if ($deputyRole && !empty($deputyRole['admin_rloes_userid'])) {
            $deputyDirector = $db_personnel->table('tb_personnel')
                ->where('pers_id', $deputyRole['admin_rloes_userid'])
                ->get()
                ->getRowArray();
            if ($deputyDirector) {
                $deputyDirector['role_position'] = $deputyRole['admin_rloes_academic_position'] ?? 'รองผู้อำนวยการสถานศึกษา';
            }
        }

        // ดึงข้อมูล ผู้อำนวยการสถานศึกษา (คำสั่ง)
        $director = null;
        $directorRole = $db_personnel->table('tb_admin_rloes')
            ->whereIn('admin_rloes_nanetype', ['ผู้อำนวยการโรงเรียน', 'ผู้อำนวยการสถานศึกษา'])
            ->get()
            ->getRowArray();

        if ($directorRole && !empty($directorRole['admin_rloes_userid'])) {
            $director = $db_personnel->table('tb_personnel')
                ->where('pers_id', $directorRole['admin_rloes_userid'])
                ->get()
                ->getRowArray();
            if ($director) {
                $director['role_position'] = $directorRole['admin_rloes_academic_position'] ?? 'ผู้อำนวยการสถานศึกษา โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์';
            }
        }

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

        // Variables
        $createdDate = strtotime($leave['created_at']);
        $startDate = strtotime($leave['leave_start_date']);
        $endDate = strtotime($leave['leave_end_date']);
        $fullName = ($personnel['pers_prefix'] ?? '') . ($personnel['pers_firstname'] ?? '') . ' ' . ($personnel['pers_lastname'] ?? '');
        $position = $personnel['posi_name'] ?? 'ครู';
        $groupName = !empty($personnel['lear_namethai']) ? 'กลุ่มสาระการเรียนรู้' . $personnel['lear_namethai'] : 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์';
        $contactAddress = $leave['leave_contact_address'] ?? $personnel['pers_address'] ?? '';
        $contactPhone = $leave['leave_contact_phone'] ?? $personnel['pers_phone'] ?? '';

        $data = [
            'title' => 'ใบลา - ' . $leave['leave_topic'],
            'leave' => $leave,
            'personnel' => $personnel,
            'fullName' => $fullName,
            'position' => $position,
            'groupName' => $groupName,
            'contactAddress' => $contactAddress,
            'contactPhone' => $contactPhone,
            'lastLeave' => $lastLeave,
            'leaveStats' => $leaveStats,
            'approver' => $approver,
            'deputyDirector' => $deputyDirector,
            'director' => $director,
            'createdDate' => $createdDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'thaiMonths' => $thaiMonths,
        ];

        return view('teacher/leave/print_view', $data);
    }
}

