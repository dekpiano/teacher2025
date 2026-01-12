<?php

namespace App\Controllers;

use App\Models\LeaveRequestModel;
use App\Models\LeaveTypeModel;
use CodeIgniter\Controller;

class LeaveController extends BaseController
{
    protected $leaveRequestModel;
    protected $leaveTypeModel;

    public function __construct()
    {
        $this->leaveRequestModel = new LeaveRequestModel();
        $this->leaveTypeModel = new LeaveTypeModel();
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
}
