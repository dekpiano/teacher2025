<?php

namespace App\Controllers;

use App\Models\ClubModel;

class ScoutController extends ClubController
{
    protected string $routePrefix = 'scout';
    protected bool $isScout = true;

    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        // Track page visit
        helper('recent_pages');
        track_recent_page('scout', 'กิจกรรมลูกเสือ - เนตรนารี', 'bi-compass');

        $teacherId = $this->getTeacherId();
        if (!$teacherId) {
            session()->setFlashdata('error', 'ไม่พบข้อมูลครูผู้สอน กรุณาเข้าสู่ระบบใหม่');
            return redirect()->to('login');
        }

        $data['title'] = "กิจกรรมลูกเสือ - เนตรนารี";
        $data['clubs'] = $this->clubModel->getClubsByTeacher($teacherId, $this->currentAcademicYear, $this->currentTerm, 'scout');
        $data['currentAcademicYear'] = $this->currentAcademicYear;
        $data['currentTerm'] = $this->currentTerm;
        $data['routePrefix'] = 'scout';
        $data['isScout'] = true;

        // ดึงข้อมูลผู้กำกับร่วมของแต่ละกอง
        if (!empty($data['clubs'])) {
            foreach ($data['clubs'] as $troop) {
                $troop->advisors_list = $this->clubModel->getAdvisorsDetails($troop->club_faculty_advisor);
            }
        }

        return view('teacher/scout/index', $data);
    }
}
