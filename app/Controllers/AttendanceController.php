<?php

namespace App\Controllers;

use App\Models\AttendanceModel;

class AttendanceController extends BaseController
{
    protected $attendanceModel;

    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
    }

    private function requireLogin()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }
        return $session;
    }

    /**
     * หน้าเช็คชื่อหลัก
     */
    public function index()
    {
        $session = $this->requireLogin();
        if ($session instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $session;
        }

        $persId = $session->get('person_id');

        $todayRecord = $this->attendanceModel->getTodayRecord($persId);
        $locationSettings = $this->attendanceModel->getLocationSettings();

        // กำหนดสถานะ
        $status = 'none'; // ยังไม่เช็ค
        if ($todayRecord) {
            $status = $todayRecord['check_out'] ? 'completed' : 'checked_in';
        }

        $data = [
            'title'             => 'SKJ Check-In',
            'todayRecord'       => $todayRecord,
            'status'            => $status,
            'locationSettings'  => $locationSettings,
            'isSystemActive'    => $locationSettings ? (bool)$locationSettings->is_active : true,
            'currentTime'       => date('H:i:s'),
            'currentDate'       => date('Y-m-d'),
        ];

        return view('teacher/attendance/index', $data);
    }

    /**
     * บันทึกเช็คชื่อเข้า (AJAX)
     */
    public function checkInsert()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'คำขอไม่ถูกต้อง']);
        }

        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
        }

        $persId   = $session->get('person_id');
        $lat      = $this->request->getPost('lat');
        $lng      = $this->request->getPost('lng');
        $selfie   = $this->request->getPost('selfie');
        $today    = date('Y-m-d');

        // ตรวสอบว่าเช็คเข้าแล้วหรือยัง
        $existing = $this->attendanceModel->getTodayRecord($persId);
        if ($existing && $existing['check_in']) {
            return $this->response->setJSON(['success' => false, 'message' => 'คุณได้เช็คชื่อเข้าแล้ววันนี้']);
        }

        // ตรวจสอบตำแหน่ง GPS
        $locationSettings = $this->attendanceModel->getLocationSettings();
        if ($locationSettings && !$locationSettings->is_active) {
            return $this->response->setJSON(['success' => false, 'message' => 'ขณะนี้โรงเรียนได้ปิดระบบบันทึกเวลางานออนไลน์ชั่วคราว']);
        }

        if ($locationSettings) {
            $distance = $this->attendanceModel->calculateDistance(
                (float) $lat,
                (float) $lng,
                (float) $locationSettings->lat,
                (float) $locationSettings->lng
            );

            if ($locationSettings->radius_m > 0 && $distance > $locationSettings->radius_m) {
                return $this->response->setJSON([
                    'success'  => false,
                    'message'  => 'คุณอยู่นอกพื้นที่โรงเรียน (ระยะห่าง ' . round($distance) . ' เมตร)',
                ]);
            }
        }

        // บันทึกรูป selfie
        $photoPath = null;
        if ($selfie) {
            $photoPath = $this->saveSelfie($selfie, $persId, 'checkin');
            if (!$photoPath) {
                return $this->response->setJSON(['success' => false, 'message' => 'ไม่สามารถบันทึกรูปได้']);
            }
        }

        // กำหนดสถานะ (มาสาย/ปกติ)
        $status = 'ปกติ';
        $currentTime = date('H:i:s');
        if ($locationSettings && $currentTime > $locationSettings->check_in_end) {
            $status = 'มาสาย';
        }

        $data = [
            'pers_id'         => $persId,
            'att_date'        => $today,
            'check_in'        => $currentTime,
            'check_in_lat'    => $lat,
            'check_in_lng'    => $lng,
            'check_in_photo'  => $photoPath,
            'status'          => $status,
        ];

        if ($existing) {
            // มี record อยู่แล้ว แต่ยังไม่ได้เช็คเข้า (กรณีพิเศษ)
            $this->attendanceModel->update($existing['att_id'], $data);
        } else {
            $this->attendanceModel->insertCheckIn($data);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $status === 'มาสาย'
                ? 'บันทึกเช็คชื่อเข้าสำเร็จ (มาสาย)'
                : 'บันทึกเช็คชื่อเข้าสำเร็จ',
            'status'       => $status,
            'time'         => date('H:i:s'),
            'photo'        => $photoPath,
        ]);
    }

    /**
     * บันทึกเช็คชื่อออก (AJAX)
     */
    public function checkOut()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'คำขอไม่ถูกต้อง']);
        }

        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
        }

        $persId = $session->get('person_id');
        $lat    = $this->request->getPost('lat');
        $lng    = $this->request->getPost('lng');
        $selfie = $this->request->getPost('selfie');

        // ตรวจสอบว่ามีการเช็คเข้าก่อนหรือไม่
        $existing = $this->attendanceModel->getTodayRecord($persId);
        if (!$existing) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'คุณยังไม่ได้เช็คชื่อเข้า กรุณาเช็คชื่อเข้าก่อน',
            ]);
        }

        if ($existing['check_out']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'คุณได้เช็คชื่อออกแล้ววันนี้',
            ]);
        }

        // ตรวจสอบตำแหน่ง GPS
        $locationSettings = $this->attendanceModel->getLocationSettings();
        if ($locationSettings && !$locationSettings->is_active) {
            return $this->response->setJSON(['success' => false, 'message' => 'ขณะนี้โรงเรียนได้ปิดระบบบันทึกเวลางานออนไลน์ชั่วคราว']);
        }

        if ($locationSettings) {
            $distance = $this->attendanceModel->calculateDistance(
                (float) $lat,
                (float) $lng,
                (float) $locationSettings->lat,
                (float) $locationSettings->lng
            );

            if ($locationSettings->radius_m > 0 && $distance > $locationSettings->radius_m) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'คุณอยู่นอกพื้นที่โรงเรียน (ระยะห่าง ' . round($distance) . ' เมตร)',
                ]);
            }
        }

        // บันทึกรูป selfie
        $photoPath = null;
        if ($selfie) {
            $photoPath = $this->saveSelfie($selfie, $persId, 'checkout');
            if (!$photoPath) {
                return $this->response->setJSON(['success' => false, 'message' => 'ไม่สามารถบันทึกรูปได้']);
            }
        }

        $currentTime = date('H:i:s');

        $data = [
            'check_out'       => $currentTime,
            'check_out_lat'   => $lat,
            'check_out_lng'   => $lng,
            'check_out_photo' => $photoPath,
        ];

        $this->attendanceModel->updateCheckOut($existing['att_id'], $data);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'บันทึกเช็คชื่อออกสำเร็จ',
            'time'    => date('H:i:s'),
            'photo'   => $photoPath,
        ]);
    }

    /**
     * หน้าประวัติเช็คชื่อ
     */
    public function history()
    {
        $session = $this->requireLogin();
        if ($session instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $session;
        }

        $persId = $session->get('person_id');
        $month  = $this->request->getGet('month') ?? date('m');
        $year   = $this->request->getGet('year')  ?? date('Y');

        $records = $this->attendanceModel->getHistoryDetailed($persId, $month, $year);
        $summary = $this->attendanceModel->getMonthlySummary($persId, $month, $year);

        // สร้างรายการเดือนไทย
        $months = [
            '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม',
            '04' => 'เมษายน', '05' => 'พฤษภาคม', '06' => 'มิถุนายน',
            '07' => 'กรกฎาคม', '08' => 'สิงหาคม', '09' => 'กันยายน',
            '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม',
        ];

        $data = [
            'title'   => 'ประวัติการเช็คชื่อ',
            'records' => $records,
            'summary' => $summary,
            'month'   => $month,
            'year'    => $year,
            'months'  => $months,
        ];

        return view('teacher/attendance/history', $data);
    }

    /**
     * หน้ารายงานสรุปรายเดือน
     */
    public function monthlyReport()
    {
        $session = $this->requireLogin();
        if ($session instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $session;
        }

        $persId = $session->get('person_id');
        $month  = $this->request->getGet('month') ?? date('m');
        $year   = $this->request->getGet('year')  ?? date('Y');

        $months = [
            '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม',
            '04' => 'เมษายน', '05' => 'พฤษภาคม', '06' => 'มิถุนายน',
            '07' => 'กรกฎาคม', '08' => 'สิงหาคม', '09' => 'กันยายน',
            '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม',
        ];

        $summary = $this->attendanceModel->getMonthlySummary($persId, $month, $year);
        $records = $this->attendanceModel->getHistoryDetailed($persId, $month, $year);

        $data = [
            'title'   => 'รายงานสรุปรายเดือน',
            'summary' => $summary,
            'records' => $records,
            'month'   => $month,
            'year'    => $year,
            'months'  => $months,
            'fullname'=> $session->get('fullname'),
        ];

        return view('teacher/attendance/report', $data);
    }

    /**
     * บันทึกรูป selfie จาก base64
     */
    private function saveSelfie(string $base64Image, string $persId, string $type): ?string
    {
        // ลบ header "data:image/jpeg;base64,"
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        $imageData = base64_decode($imageData);

        if ($imageData === false) {
            return null;
        }

        // สร้าง path สำหรับเก็บรูป: uploads/selfies/YYYY/MM/
        $year  = date('Y');
        $month = date('m');
        $dir   = ROOTPATH . 'uploads/selfies/' . $year . '/' . $month . '/';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filename = $type . '_' . $persId . '_' . date('Ymd_His') . '.jpg';
        $filepath = $dir . $filename;

        if (file_put_contents($filepath, $imageData)) {
            return base_url('uploads/selfies/' . $year . '/' . $month . '/' . $filename);
        }

        return null;
    }
}
