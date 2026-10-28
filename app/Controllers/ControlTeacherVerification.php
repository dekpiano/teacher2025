<?php

namespace App\Controllers;

use App\Models\LoginModel;
use App\Libraries\GoogleWorkspaceService;

class ControlTeacherVerification extends BaseController
{
    public function index()
    {
        $data['title'] = "รีเซ็ตรหัสผ่านครูและบุคลากร";
        $googleService = new GoogleWorkspaceService();
        $data['googleApiConfigured'] = $googleService->isConfigured();
        return view('EmailVerification/teacher_verify_index', $data);
    }

    public function guide()
    {
        return view('guide_index');
    }

    /**
     * Reset Google Workspace email password for teacher using teacher's school email
     */
    public function resetPassword()
    {
        $emailInput = trim($this->request->getPost('email') ?? '');
        $phoneInput = trim($this->request->getPost('phone') ?? '');

        if (empty($emailInput)) {
            return $this->response->setJSON([
                'status' => 0,
                'message' => 'กรุณากรอกอีเมลโรงเรียน (@skj.ac.th) ให้ถูกต้อง'
            ]);
        }

        // Clean up input phone number (keep digits only)
        $cleanPhoneInput = preg_replace('/[^0-9]/', '', $phoneInput);
        if (empty($cleanPhoneInput) || strlen($cleanPhoneInput) < 9) {
            return $this->response->setJSON([
                'status' => 0,
                'message' => 'กรุณากรอกเบอร์โทรศัพท์ที่ลงทะเบียนไว้ในระบบให้ถูกต้อง (9-10 หลัก)'
            ]);
        }

        // Auto append domain if user only enters prefix username
        if (strpos($emailInput, '@') === false) {
            $emailInput .= '@skj.ac.th';
        }

        $dbPersonnel = \Config\Database::connect('personnel');
        $dbSkj = \Config\Database::connect('skj');

        $teacher = $dbPersonnel->table('tb_personnel')
            ->select('tb_personnel.*, ' . $dbSkj->database . '.tb_position.posi_name as position_name')
            ->join($dbSkj->database . '.tb_position', $dbSkj->database . '.tb_position.posi_id = tb_personnel.pers_position', 'left')
            ->where('tb_personnel.pers_username', $emailInput)
            ->get()
            ->getRowArray();

        if (!$teacher) {
            return $this->response->setJSON([
                'status' => 0,
                'message' => 'ไม่พบข้อมูลครู/บุคลากรที่ใช้อีเมล ' . $emailInput . ' ในระบบ กรุณาตรวจสอบอีกครั้ง'
            ]);
        }

        // Check Phone Number Match (strip non-digits for comparison)
        $teacherPhone = preg_replace('/[^0-9]/', '', (string) ($teacher['pers_phone'] ?? ''));
        if (empty($teacherPhone) || $teacherPhone !== $cleanPhoneInput) {
            return $this->response->setJSON([
                'status' => 0,
                'message' => 'เบอร์โทรศัพท์ไม่ตรงกับข้อมูลในระบบ กรุณาตรวจสอบเบอร์โทรศัพท์ของคุณหรือติดต่อผู้ดูแลระบบ'
            ]);
        }

        // Check teacher status: only active status ("กำลังใช้งาน") can proceed
        $userStatus = (string) ($teacher['pers_status'] ?? '');
        if ($userStatus !== 'กำลังใช้งาน') {
            $statusDesc = !empty($userStatus) ? $userStatus : 'พ้นสภาพ / ไม่ได้ปฏิบัติงาน';
            return $this->response->setJSON([
                'status' => 0,
                'message' => 'ไม่สามารถดำเนินการได้ เนื่องจากบัญชีผู้ใช้ของคุณมีสถานะ "' . $statusDesc . '"'
            ]);
        }

        $email = $teacher['pers_username'];

        // Generate strong random password conforming to Google Workspace policy
        $newPassword = 'Skj@' . substr(str_shuffle('23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ'), 0, 6);

        // Update password in Google Workspace Directory API first
        $googleService = new GoogleWorkspaceService();
        if (!$googleService->isConfigured()) {
            return $this->response->setJSON([
                'status' => 0,
                'message' => 'ระบบยังไม่พร้อมเชื่อมต่อกับ Google Workspace: ' . ($googleService->getLastError() ?: 'ไม่พบคีย์ Service Account')
            ]);
        }

        $googleResult = $googleService->updateUserPassword($email, $newPassword, false);
        if (!$googleResult['success']) {
            return $this->response->setJSON([
                'status' => 0,
                'message' => 'ไม่สามารถเปลี่ยนรหัสผ่านใน Google Workspace ได้: ' . ($googleResult['message'] ?? 'ข้อผิดพลาดจาก Google API'),
                'google_status' => $googleResult
            ]);
        }

        // Only update database when Google Workspace operation succeeds
        $dbPersonnel->table('tb_personnel')
            ->where('pers_id', $teacher['pers_id'])
            ->update([
                'pers_password' => $newPassword,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        $fullName = trim(($teacher['pers_prefix'] ?? '') . ($teacher['pers_firstname'] ?? '') . ' ' . ($teacher['pers_lastname'] ?? ''));
        $positionName = !empty($teacher['position_name']) ? $teacher['position_name'] : ($teacher['pers_position'] ?: 'ครูผู้สอน');

        return $this->response->setJSON([
            'status' => 1,
            'message' => 'รีเซ็ตรหัสผ่านอีเมลครูและบุคลากรสำเร็จ!',
            'data' => [
                'person_name' => $fullName ?: 'ครู/บุคลากร',
                'position' => $positionName,
                'email' => $email,
                'new_password' => $newPassword,
                'google_status' => $googleResult
            ]
        ]);
    }
}
