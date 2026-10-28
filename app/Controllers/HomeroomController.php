<?php

namespace App\Controllers;

use App\Models\HomeroomModel;

class HomeroomController extends BaseController
{
    protected $homeroomModel;

    public function __construct()
    {
        $this->homeroomModel = new HomeroomModel();
    }

    /**
     * ดึงข้อมูลครูที่ปรึกษาและห้องเรียน
     */
    private function getTeacherHomeroomContext()
    {
        $session = session();
        $personId = $session->get('person_id');
        $current = $this->homeroomModel->getCurrentYearAndTerm();
        
        $teacher = $this->homeroomModel->getTeacherHomeroomInfo($personId, $current['year']);
        
        return [
            'teacher' => $teacher,
            'year' => $current['year'],
            'term' => $current['term'],
            'year_full' => $current['full']
        ];
    }

    /**
     * หน้าแรก / แดชบอร์ดภาพรวมระบบครูที่ปรึกษา
     */
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        helper('recent_pages');
        track_recent_page('homeroom', 'ระบบครูที่ปรึกษา', 'bi-house-heart-fill');

        $ctx = $this->getTeacherHomeroomContext();
        $teacher = $ctx['teacher'];

        if (!$teacher || empty($teacher->Reg_Class)) {
            $data = [
                'title' => 'ระบบครูที่ปรึกษา',
                'teacher' => $teacher,
                'no_class' => true,
                'year' => $ctx['year'],
                'term' => $ctx['term']
            ];
            return view('teacher/homeroom/CheckHomeRoomMain', $data);
        }

        // หากเป็นหัวหน้าระดับ (1-6)
        if (in_array($teacher->Reg_Class, ["1", "2", "3", "4", "5", "6"])) {
            return redirect()->to('homeroom/dashboard/' . date('d-m-Y'));
        }

        $className = $teacher->Reg_Class;
        $cleanClass = str_replace('ม.', '', $className);

        // ดึงนักเรียนในห้อง
        $students = $this->homeroomModel->getStudentsInClass($cleanClass);
        $totalStudents = count($students);

        // ดึงบันทึกการเช็คชื่อล่าสุด
        $latestRecord = $this->homeroomModel->where([
            'chk_home_room' => $cleanClass,
            'chk_home_yaer' => $ctx['year'],
            'chk_home_term' => $ctx['term']
        ])->orderBy('chk_home_date', 'DESC')->first();

        // ตรวจสอบว่าวันนี้เช็คชื่อหรือยัง
        $todayRecord = $this->homeroomModel->where([
            'chk_home_room' => $cleanClass,
            'chk_home_date' => date('Y-m-d')
        ])->first();

        // ประวัติการเช็คชื่อ 10 วันล่าสุด
        $recentHistory = $this->homeroomModel->getAttendanceHistory($cleanClass, $ctx['year'], $ctx['term'], 10);

        // คำนวณสถิติเพศ
        $boyCount = 0;
        $girlCount = 0;
        foreach ($students as $s) {
            if (in_array($s->StudentPrefix, ['นาย', 'เด็กชาย', 'ด.ช.'])) {
                $boyCount++;
            } else {
                $girlCount++;
            }
        }

        // ดึงครูที่ปรึกษาร่วม
        $coAdvisors = $this->homeroomModel->getCoAdvisorsByClass($cleanClass, $ctx['year']);

        $data = [
            'title' => 'ระบบครูที่ปรึกษา (ม.' . $cleanClass . ')',
            'teacher' => $teacher,
            'coAdvisors' => $coAdvisors,
            'className' => $cleanClass,
            'year' => $ctx['year'],
            'term' => $ctx['term'],
            'students' => $students,
            'totalStudents' => $totalStudents,
            'boyCount' => $boyCount,
            'girlCount' => $girlCount,
            'latestRecord' => $latestRecord,
            'todayRecord' => $todayRecord,
            'recentHistory' => $recentHistory
        ];

        return view('teacher/homeroom/CheckHomeRoomMain', $data);
    }

    /**
     * โมดูลที่ 1: ข้อมูลนักเรียนในที่ปรึกษา
     */
    public function students()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        helper('recent_pages');
        track_recent_page('homeroom/students', 'ข้อมูลนักเรียนในที่ปรึกษา', 'bi-people-fill');

        $ctx = $this->getTeacherHomeroomContext();
        $teacher = $ctx['teacher'];

        if (!$teacher || empty($teacher->Reg_Class)) {
            return redirect()->to('homeroom')->with('error', 'ไม่พบข้อมูลห้องเรียนที่ปรึกษาของท่าน');
        }

        $cleanClass = str_replace('ม.', '', $teacher->Reg_Class);
        $statusFilter = $this->request->getGet('status') ?? '1/ปกติ';

        $students = $this->homeroomModel->getStudentsInClass($cleanClass, $statusFilter);
        $coAdvisors = $this->homeroomModel->getCoAdvisorsByClass($cleanClass, $ctx['year']);

        $data = [
            'title' => 'ข้อมูลนักเรียนในที่ปรึกษา ชั้น ม.' . $cleanClass,
            'teacher' => $teacher,
            'coAdvisors' => $coAdvisors,
            'className' => $cleanClass,
            'year' => $ctx['year'],
            'term' => $ctx['term'],
            'students' => $students,
            'statusFilter' => $statusFilter,
            'totalCount' => count($students)
        ];

        return view('teacher/homeroom/students', $data);
    }

    /**
     * โมดูลที่ 1: ดึงข้อมูลโปรไฟล์นักเรียนรายบุคคล (AJAX / Modal)
     */
    public function studentDetail($studentCode)
    {
        $student = $this->homeroomModel->getStudentProfile($studentCode);

        if (!$student) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลนักเรียน'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $student
        ]);
    }

    /**
     * โมดูลที่ 2: หน้าเช็คชื่อโฮมรูมประจำวัน
     */
    public function add($targetDate = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        helper('recent_pages');
        track_recent_page('homeroom/add', 'เช็คชื่อโฮมรูม', 'bi-calendar-check-fill');

        $ctx = $this->getTeacherHomeroomContext();
        $teacher = $ctx['teacher'];

        if (!$teacher || empty($teacher->Reg_Class)) {
            return redirect()->to('homeroom')->with('error', 'ไม่พบข้อมูลห้องเรียนที่ปรึกษาของท่าน');
        }

        $cleanClass = str_replace('ม.', '', $teacher->Reg_Class);
        $selectedDate = $targetDate ? date('Y-m-d', strtotime($targetDate)) : date('Y-m-d');

        // ดึงรายชื่อนักเรียน
        $students = $this->homeroomModel->getStudentsInClass($cleanClass);

        // ค้นหาว่าในวันที่เลือก เคยมีบันทึกหรือยัง
        $existingRecord = $this->homeroomModel->where([
            'chk_home_date' => $selectedDate,
            'chk_home_room' => $cleanClass
        ])->first();

        $actionUrl = site_url('homeroom/insert');
        $buttonLabel = "บันทึกการเช็คชื่อ";

        if ($existingRecord) {
            $actionUrl = site_url('homeroom/update/' . $existingRecord['chk_home_id']);
            $buttonLabel = "อัปเดตข้อมูลการเช็คชื่อ";
        }

        $data = [
            'title' => 'เช็คชื่อโฮมรูม ม.' . $cleanClass,
            'teacher' => $teacher,
            'className' => $cleanClass,
            'year' => $ctx['year'],
            'term' => $ctx['term'],
            'selectedDate' => $selectedDate,
            'students' => $students,
            'existingRecord' => $existingRecord,
            'actionUrl' => $actionUrl,
            'buttonLabel' => $buttonLabel
        ];

        return view('teacher/homeroom/CheckHomeRoomAdd', $data);
    }

    /**
     * บันทึกการเช็คชื่อ (Insert)
     */
    public function insert()
    {
        $postData = $this->request->getPost();
        $dbData = $this->processAttendanceFormData($postData);

        if ($this->homeroomModel->insert($dbData)) {
            session()->setFlashdata('success', 'บันทึกการเช็คชื่อโฮมรูมเรียบร้อยแล้ว');
        } else {
            session()->setFlashdata('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
        }

        return redirect()->to('homeroom/add/' . ($dbData['chk_home_date'] ?? date('Y-m-d')));
    }

    /**
     * อัปเดตการเช็คชื่อ (Update)
     */
    public function update($id)
    {
        $postData = $this->request->getPost();
        $dbData = $this->processAttendanceFormData($postData);

        if ($this->homeroomModel->update($id, $dbData)) {
            session()->setFlashdata('success', 'อัปเดตข้อมูลการเช็คชื่อเรียบร้อยแล้ว');
        } else {
            session()->setFlashdata('error', 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล');
        }

        return redirect()->to('homeroom/add/' . ($dbData['chk_home_date'] ?? date('Y-m-d')));
    }

    /**
     * จัดรูปแบบข้อมูลฟอร์มเช็คชื่อก่อนบันทึกลงฐานข้อมูล
     */
    private function processAttendanceFormData(array $postData): array
    {
        $statusData = $postData['status'] ?? [];
        $attendance = [
            'ma' => [], 'khad' => [], 'la' => [],
            'sahy' => [], 'kid' => [], 'hnee' => []
        ];

        $statusMap = [
            'มา' => 'ma', 'ขาด' => 'khad', 'ลา' => 'la',
            'สาย' => 'sahy', 'กิจกรรม' => 'kid', 'หนี' => 'hnee',
            'ma' => 'ma', 'khad' => 'khad', 'la' => 'la',
            'sahy' => 'sahy', 'kid' => 'kid', 'hnee' => 'hnee'
        ];

        foreach ($statusData as $studentCode => $status) {
            $normalizedStatus = $statusMap[$status] ?? 'ma';
            $attendance[$normalizedStatus][] = $studentCode;
        }

        $dbData = [];
        foreach ($attendance as $key => $codes) {
            $dbData['chk_home_' . $key] = implode('|', $codes);
        }

        $checkDate = !empty($postData['chk_home_date']) ? $postData['chk_home_date'] : date('Y-m-d');
        $ctx = $this->getTeacherHomeroomContext();

        $dbData['chk_home_date'] = $checkDate;
        $dbData['chk_home_time'] = date('H:i:s');
        $dbData['chk_home_teacher'] = $postData['chk_home_teacher'] ?? session()->get('person_id');
        $dbData['chk_home_room'] = str_replace('ม.', '', $postData['chk_home_room'] ?? '');
        $dbData['chk_home_term'] = $postData['chk_home_term'] ?? $ctx['term'];
        $dbData['chk_home_yaer'] = $postData['chk_home_yaer'] ?? $ctx['year'];

        return $dbData;
    }

    /**
     * โมดูลที่ 2: ประวัติการเช็คชื่อย้อนหลังและสถิติรายบุคคล
     */
    public function history()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        helper('recent_pages');
        track_recent_page('homeroom/history', 'ประวัติการเช็คชื่อ & สถิติ', 'bi-clock-history');

        $ctx = $this->getTeacherHomeroomContext();
        $teacher = $ctx['teacher'];

        if (!$teacher || empty($teacher->Reg_Class)) {
            return redirect()->to('homeroom')->with('error', 'ไม่พบข้อมูลห้องเรียนที่ปรึกษาของท่าน');
        }

        $cleanClass = str_replace('ม.', '', $teacher->Reg_Class);

        $students = $this->homeroomModel->getStudentsInClass($cleanClass);
        $history = $this->homeroomModel->getAttendanceHistory($cleanClass, $ctx['year'], $ctx['term'], 120);
        $studentSummary = $this->homeroomModel->getStudentAttendanceSummary($cleanClass, $ctx['year'], $ctx['term'], $students);

        $data = [
            'title' => 'ประวัติการเช็คชื่อ & สถิติการมาเรียน ม.' . $cleanClass,
            'teacher' => $teacher,
            'className' => $cleanClass,
            'year' => $ctx['year'],
            'term' => $ctx['term'],
            'students' => $students,
            'history' => $history,
            'studentSummary' => $studentSummary,
            'totalDays' => count($history)
        ];

        return view('teacher/homeroom/history', $data);
    }

    /**
     * โมดูลที่ 4: ติดตามผลการเรียน & กิจกรรมพัฒนาผู้เรียน
     */
    public function academicTracking()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        helper('recent_pages');
        track_recent_page('homeroom/academic-tracking', 'ติดตามผลการเรียน & กิจกรรม', 'bi-mortarboard-fill');

        $ctx = $this->getTeacherHomeroomContext();
        $teacher = $ctx['teacher'];

        if (!$teacher || empty($teacher->Reg_Class)) {
            return redirect()->to('homeroom')->with('error', 'ไม่พบข้อมูลห้องเรียนที่ปรึกษาของท่าน');
        }

        $cleanClass = str_replace('ม.', '', $teacher->Reg_Class);

        $selectedYear = $this->request->getGet('year') ?? $ctx['year'];
        $selectedTerm = $this->request->getGet('term') ?? $ctx['term'];

        // ดึงข้อมูลผลการเรียนตรงจาก skjacth_academic.tb_register
        $academicData = $this->homeroomModel->getAcademicTrackingData($cleanClass, $selectedYear, $selectedTerm);
        
        // ดึงข้อมูลกิจกรรม (ชุมนุม)
        $activitiesData = $this->homeroomModel->getActivitiesTrackingData($academicData['students'], $selectedYear, $selectedTerm);
        $schoolYears = $this->homeroomModel->getAllSchoolYears();

        $data = [
            'title' => 'ติดตามผลการเรียน & กิจกรรม ม.' . $cleanClass,
            'teacher' => $teacher,
            'className' => $cleanClass,
            'year' => $selectedYear,
            'term' => $selectedTerm,
            'schoolYears' => $schoolYears,
            'students' => $academicData['students'],
            'subjects' => $academicData['subjects'],
            'atRiskCount' => $academicData['at_risk_count'],
            'avgGpa' => $academicData['total_gpa_avg'],
            'activities' => $activitiesData
        ];

        return view('teacher/homeroom/academic_tracking', $data);
    }

    /**
     * แดชบอร์ดภาพรวมสำหรับหัวหน้าระดับชั้น
     */
    public function dashboard($key = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $key = $key ?? date('d-m-Y');
        $dateForDb = date('Y-m-d', strtotime($key));

        $db_affairs = db_connect('affairs');
        $db = db_connect('default');

        $ctx = $this->getTeacherHomeroomContext();
        $teacher = $ctx['teacher'];

        $data['title'] = "แดชบอร์ดโฮมรูมประจำระดับชั้น";
        $data['teacher'] = $teacher;
        $data['current_date'] = $key;
        $data['year'] = $ctx['year'];
        $data['term'] = $ctx['term'];

        $gradeLevel = $teacher ? $teacher->Reg_Class : '1';

        $records = $db_affairs->table('tb_checkhomeroom')
            ->where('chk_home_date', $dateForDb)
            ->like('chk_home_room', $gradeLevel, 'after')
            ->orderBy('chk_home_room', 'ASC')
            ->get()
            ->getResult();

        $all = [];
        foreach ($records as $record) {
            $classStats = [
                'Room' => $record->chk_home_room,
                'Khad' => ['Boy' => 0, 'Girl' => 0],
                'La' => ['Boy' => 0, 'Girl' => 0],
                'Sahy' => ['Boy' => 0, 'Girl' => 0],
                'Kid' => ['Boy' => 0, 'Girl' => 0],
                'Hnee' => ['Boy' => 0, 'Girl' => 0],
            ];

            $statuses = ['khad', 'la', 'sahy', 'kid', 'hnee'];
            foreach ($statuses as $status) {
                $field = 'chk_home_' . $status;
                if (!empty($record->$field)) {
                    $studentCodes = explode('|', $record->$field);
                    $students = $db->table('tb_students')->select('StudentPrefix')->whereIn('StudentCode', $studentCodes)->get()->getResult();
                    foreach ($students as $student) {
                        if (in_array($student->StudentPrefix, ["นาย", "เด็กชาย", "ด.ช."])) {
                            $classStats[ucfirst($status)]['Boy']++;
                        } else {
                            $classStats[ucfirst($status)]['Girl']++;
                        }
                    }
                }
            }
            $all[] = $classStats;
        }

        $data['all'] = $all;
        $data['showHR'] = $records;

        return view('teacher/homeroom/CheckHomeRoomDashboard', $data);
    }
}
