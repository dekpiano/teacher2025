<?php

namespace App\Controllers\Assessment;

use App\Controllers\BaseController;

class ControllerSaveScore extends BaseController
{
    protected $db; // Declare the $db property
    protected $personnelDb;
    protected $session; // Declare to avoid PHP 8.2+ dynamic property deprecation

    public function __construct()
    {
        $this->db = \Config\Database::connect(); // Initialize the database connection
        $this->personnelDb = \Config\Database::connect('personnel');
        $this->session = \Config\Services::session(); // Initialize the session service
    }

    private function compareRegisterYears(string $a, string $b): int
    {
        [$termA, $yearA] = array_pad(explode('/', $a, 2), 2, '0');
        [$termB, $yearB] = array_pad(explode('/', $b, 2), 2, '0');
        $yearComparison = (int) $yearB <=> (int) $yearA;
        return $yearComparison !== 0 ? $yearComparison : ((int) $termB <=> (int) $termA);
    }

    private function getCurrentSchoolYear(): string
    {
        $years = $this->db->table('tb_schoolyear')->select('schyear_year')->get()->getResultArray();
        if (empty($years)) {
            return '';
        }

        usort($years, fn ($a, $b) => $this->compareRegisterYears((string) $a['schyear_year'], (string) $b['schyear_year']));
        return (string) $years[0]['schyear_year'];
    }

    private function getTeacherSchoolYears(string $teacherId): array
    {
        if ($teacherId === '') {
            return [];
        }

        $rows = $this->db->table('tb_register')->select('RegisterYear')->where('TeacherID', $teacherId)
            ->groupBy('RegisterYear')->get()->getResultArray();
        $years = array_values(array_unique(array_filter(array_map(static fn ($row) => (string) ($row['RegisterYear'] ?? ''), $rows))));
        usort($years, fn ($a, $b) => $this->compareRegisterYears($a, $b));
        return $years;
    }

    private function isAcademicAccessManager(?string $personId = null): bool
    {
        $personId = $personId ?? (string) $this->session->get('person_id');
        if ($personId === '') {
            return false;
        }

        return $this->db->table('tb_admin_rloes')->where('admin_rloes_userid', $personId)
            ->groupStart()
                ->like('admin_rloes_academic_position', 'งานวัดและประเมินผล')
                ->orLike('admin_rloes_academic_position', 'งานวัดผล')
                ->orLike('admin_rloes_academic_position', 'รองผู้อำนวยการฝ่ายวิชาการ')
                ->orLike('admin_rloes_academic_position', 'รองวิชาการ')
            ->groupEnd()->countAllResults() > 0;
    }

    private function hasTeacherPeriodRegistration(string $teacherId, string $registerYear, ?string $subjectId = null): bool
    {
        $builder = $this->db->table('tb_register')->where('TeacherID', $teacherId)->where('RegisterYear', $registerYear);
        if ($subjectId !== null) {
            $builder->where('SubjectID', $subjectId);
        }
        return $builder->countAllResults() > 0;
    }

    private function canEditRegisterPeriod(string $teacherId, string $registerYear): bool
    {
        $scoreRecordingStatus = $this->db->table('tb_register_onoff')->select('onoff_status')
            ->where('onoff_id', 10)->get()->getRowArray();
        if (empty($scoreRecordingStatus['onoff_status']) || $scoreRecordingStatus['onoff_status'] === 'off') {
            return false;
        }

        if ($registerYear !== '' && $registerYear === $this->getCurrentSchoolYear()) {
            return true;
        }

        return $this->db->table('tb_register_score_access')->where('teacher_id', $teacherId)
            ->where('register_year', $registerYear)->where('is_enabled', 1)->countAllResults() > 0;
    }

    private function rejectHistoricalWrite()
    {
        return $this->response->setStatusCode(403)->setJSON([
            'status' => 'error',
            'message' => 'ปีการศึกษานี้ถูกล็อกไว้สำหรับดูข้อมูลเท่านั้น หรือสิทธิ์แก้ไขถูกเพิกถอนแล้ว',
        ]);
    }

    public function normal()
    {
        // Track page visit
        helper('recent_pages');
        track_recent_page('assessment/save-score-normal', 'บันทึกผลการเรียน', 'bi-clipboard-data');

        $data = [];
        $data['title'] = "หน้าบันทึกผลการเรียนหลัก";

        $currentSchoolYear = $this->getCurrentSchoolYear();
        $data['current_school_year'] = $currentSchoolYear ?? '';

        // Get onoff status
        $data['onoff'] = $this->db->table('tb_register_onoff')->where('onoff_id', 10)->get()->getResult();

        // Get check_subject
        $loginId = $this->session->get('person_id'); 
        $availableSchoolYears = $this->getTeacherSchoolYears((string) $loginId);
        if ($currentSchoolYear !== '' && !in_array($currentSchoolYear, $availableSchoolYears, true)) {
            $availableSchoolYears[] = $currentSchoolYear;
            usort($availableSchoolYears, fn ($a, $b) => $this->compareRegisterYears($a, $b));
        }
        // Use a dedicated filter parameter so stale school_year query strings do not override the system default.
        $requestedSchoolYear = (string) $this->request->getGet('score_period');
        $selectedSchoolYear = in_array($requestedSchoolYear, $availableSchoolYears, true)
            ? $requestedSchoolYear
            : ($currentSchoolYear !== '' ? $currentSchoolYear : ($availableSchoolYears[0] ?? ''));
        $data['available_school_years'] = $availableSchoolYears;
        $data['selected_school_year'] = $selectedSchoolYear;
        $data['selected_is_historical'] = $selectedSchoolYear !== '' && $selectedSchoolYear !== $currentSchoolYear;
        $data['selected_is_read_only'] = $selectedSchoolYear !== '' && !$this->canEditRegisterPeriod((string) $loginId, $selectedSchoolYear);
        $data['can_manage_historical_access'] = $this->isAcademicAccessManager((string) $loginId);

        if ($loginId && $selectedSchoolYear) {
            $data['check_subject'] = $this->db->table('tb_register')
                ->select('
                    tb_register.SubjectID,
                    tb_register.RegisterYear,
                    GROUP_CONCAT(DISTINCT tb_register.RegisterClass ORDER BY tb_register.RegisterClass SEPARATOR ", ") as RegisterClasses,
                    tb_register.TeacherID,
                    tb_subjects.SubjectName,
                    tb_subjects.SubjectCode,
                    tb_subjects.SubjectUnit,
                    tb_subjects.SubjectHour,
                    (SELECT COUNT(*) FROM tb_register_score WHERE regscore_subjectID = tb_register.SubjectID) as score_settings_count
                ')
                ->join('tb_subjects', 'tb_subjects.SubjectID = tb_register.SubjectID')
                ->where('tb_register.TeacherID', $loginId)
                ->where('tb_register.RegisterYear', $selectedSchoolYear)
                ->groupBy('tb_register.SubjectID')
                ->groupBy('tb_register.RegisterYear')
                ->groupBy('tb_register.TeacherID')
                ->orderBy('tb_subjects.SubjectCode')
                ->get()
                ->getResult();
        } else {
            $data['check_subject'] = []; // No subjects if login_id or school year is missing
        }
        
        $data['teacher'] = $this->personnelDb->table('tb_personnel')->select('pers_id,pers_img')->where('pers_id', $loginId)->get()->getResult();
        $data['OnOff'] = $this->db->table('tb_send_plan_setup')->get()->getResult();

        return view('teacher/register/SaveScore/SaveScoreMain', $data);
    }

    public function saveScoreAdd($year, $term, $subject, $room)
    {
        $data['title'] = "บันทึกผลการเรียน";
        $RegisterYear = $year . '/' . $term;

        $loginId = $this->session->get('person_id');
        if (!$loginId || !$this->hasTeacherPeriodRegistration((string) $loginId, $RegisterYear, (string) $subject)) {
            return $this->response->setStatusCode(404)->setBody('ไม่พบรายวิชานี้ในรายการที่คุณรับผิดชอบ');
        }
        $data['is_historical_period'] = $RegisterYear !== $this->getCurrentSchoolYear();
        $data['is_read_only'] = !$this->canEditRegisterPeriod((string) $loginId, $RegisterYear);
        $data['register_year'] = $RegisterYear;
        $data['teacher'] = $this->personnelDb->table('tb_personnel')->select('pers_id,pers_img')->where('pers_id', $loginId)->get()->getResult();
        $data['OnOff'] = $this->db->table('tb_send_plan_setup')->get()->getResult();

        $data['check_room'] = $this->db->table('tb_register')
            ->select('tb_students.StudentClass, tb_register.RegisterYear')
            ->join('tb_subjects', 'tb_subjects.SubjectID = tb_register.SubjectID')
            ->join('tb_students', 'tb_students.StudentID = tb_register.StudentID')
            ->where('tb_register.TeacherID', $loginId)
            ->where('tb_register.RegisterYear', $RegisterYear)
            ->where('tb_register.SubjectID', $subject)
            ->groupBy('tb_students.StudentClass')
            ->groupBy('tb_register.RegisterYear')
            ->orderBy('tb_students.StudentClass', 'ASC')
            ->get()->getResult();

        $query = $this->db->table('tb_register')
            ->select('
                tb_register.SubjectID, tb_register.RegisterYear, tb_register.RegisterClass,
                tb_register.Score100, tb_register.TeacherID, tb_register.StudyTime,
                tb_register.Grade_Type,
                tb_subjects.SubjectName, tb_subjects.SubjectCode, tb_subjects.SubjectUnit,
                tb_subjects.SubjectHour, tb_subjects.SubjectYear,
                tb_students.StudentID, tb_students.StudentPrefix, tb_students.StudentFirstName,
                tb_students.StudentLastName, tb_students.StudentNumber, tb_students.StudentClass,
                tb_students.StudentCode, tb_students.StudentStatus, tb_students.StudentBehavior
            ')
            ->join('tb_subjects', 'tb_subjects.SubjectID = tb_register.SubjectID')
            ->join('tb_students', 'tb_students.StudentID = tb_register.StudentID')
            ->where('tb_register.TeacherID', $loginId)
            ->where('tb_register.RegisterYear', $RegisterYear)
            ->where('tb_subjects.SubjectYear', $RegisterYear)
            ->where('tb_register.SubjectID', $subject);

        if ($room != "all") {
            $sub_checkroom = explode('-', $room ?? '');
            $room_val = $sub_checkroom[1] ?? ($sub_checkroom[0] ?? '');
            $sub_room_class = 'ม.' . ($sub_checkroom[0] ?? '') . '/' . ($sub_checkroom[1] ?? '');
            $query->where('tb_students.StudentClass', $sub_room_class);
        }

        $data['check_student'] = $query->orderBy('tb_students.StudentClass', 'ASC')
                                     ->orderBy('tb_students.StudentNumber', 'ASC')
                                     ->get()->getResult();

        // Safe Fallback: If check_student is empty, try to get subject info separately for the header
        if (empty($data['check_student'])) {
            $data['subject_info'] = $this->db->table('tb_subjects')
                                           ->where('SubjectID', $subject)
                                           ->get()
                                           ->getRow();
            $data['register_year'] = $RegisterYear;
        }

        $check_idSubject = $this->db->table('tb_subjects')->where('SubjectID', $subject)->where('SubjectYear', $RegisterYear)->get()->getRow();
        if($check_idSubject){
            $data['set_score'] = $this->db->table('tb_register_score')->where('regscore_subjectID', $check_idSubject->SubjectID)->get()->getResult();
        } else {
            $data['set_score'] = [];
        }
        
        $data['onoff_savescore'] = $this->db->table('tb_register_onoff')->where('onoff_id >=', 2)->where('onoff_id <=', 5)->get()->getResult();
        $data['uri'] = service('uri');
        $data['Room'] = $room;

        return view('teacher/register/SaveScore/SaveScoreAdd', $data);
    }

    // AJAX Methods ported from ConTeacherRegister
    
    private function check_grade($sum) {
        if (($sum > 100) || ($sum < 0)) {
             return "ไม่สามารถคิดเกรดได้ คะแนนเกิน";
        } else if (($sum >= 79.5) && ($sum <= 100)) {
             return 4;
        } else if (($sum >= 74.5) && ($sum <= 79.4)) {
             return 3.5;
        } else if (($sum >= 69.5) && ($sum <= 74.4)) {
             return 3;
        } else if (($sum >= 64.5) && ($sum <= 69.4)) {
             return 2.5;
        } else if (($sum >= 59.5) && ($sum <= 64.4)) {
             return 2;
        } else if (($sum >= 54.5) && ($sum <= 59.4)) {
             return 1.5;
        } else if (($sum >= 49.5) && ($sum <= 54.4)) {
             return 1;
        } else if ($sum <= 49.4) {
             return 0;
        }
        return '';
    }

    public function settingScore($key){
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $list = ['before_middle', 'test_midterm', 'after_midterm', 'final_exam'];
        $score_fields = ['before_middle_score', 'test_midterm_score', 'after_midterm_score', 'final_exam_score'];
        $subjectID = $this->request->getPost("regscore_subjectID");
        $registerYear = trim((string) $this->request->getPost('register_year'));
        $teacherId = (string) $this->session->get('person_id');
        
        if (!$subjectID || !$registerYear || !$this->hasTeacherPeriodRegistration($teacherId, $registerYear, (string) $subjectID)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'ไม่พบรายวิชาที่คุณมีสิทธิ์จัดการ']);
        }
        if (!$this->canEditRegisterPeriod($teacherId, $registerYear)) {
            return $this->rejectHistoricalWrite();
        }

        try {
            $this->db->transStart();
            
            // ลบข้อมูลการตั้งค่าเดิมออกก่อน เพื่อป้องกันกรณีบันทึกซ้ำซ้อน
            $this->db->table('tb_register_score')->where('regscore_subjectID', $subjectID)->delete();

            for ($i = 0; $i <= 3; $i++) {
                $data = [
                    'regscore_subjectID' => $subjectID,
                    'regscore_namework' => $this->request->getPost($list[$i]),
                    'regscore_score' => $this->request->getPost($score_fields[$i])
                ];
                $this->db->table('tb_register_score')->insert($data);
            }
            
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                 return $this->response->setJSON(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (DB Transaction Failed)']);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'บันทึกสัดส่วนคะแนนเรียบร้อยแล้ว']);
            
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    public function editScore(){
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }
        $teacherId = (string) $this->session->get('person_id');
        $subjectId = $this->request->getPost('subid');
        if (!$subjectId || !$this->db->table('tb_register')->where('TeacherID', $teacherId)->where('SubjectID', $subjectId)->countAllResults()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'ไม่มีสิทธิ์ดูการตั้งค่ารายวิชานี้']);
        }
        $edit_score = $this->db->table('tb_register_score')->where('regscore_subjectID', $subjectId)->get()->getResult();
        
        if ($edit_score) {
            return $this->response->setJSON($edit_score);
        } else {
            return $this->response->setJSON(['status' => 'not_found']);
        }
    }

    public function insertScore(){
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $studentIDs = $this->request->getPost('StudentID');
        $timeNum = $this->request->getPost('TimeNum');
        $study_times = $this->request->getPost('study_time');
        $teacherId = (string) $this->session->get('person_id');
        $subjectId = (string) $this->request->getPost('SubjectID');
        $registerYear = trim((string) $this->request->getPost('RegisterYear'));

        if (!is_array($studentIDs) || empty($studentIDs) || !$subjectId || !$registerYear
            || !$this->hasTeacherPeriodRegistration($teacherId, $registerYear, $subjectId)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'ไม่พบรายวิชาที่คุณมีสิทธิ์จัดการ']);
        }
        if (!$this->canEditRegisterPeriod($teacherId, $registerYear)) {
            return $this->rejectHistoricalWrite();
        }
        $registeredStudents = $this->db->table('tb_register')->where('TeacherID', $teacherId)->where('RegisterYear', $registerYear)
            ->where('SubjectID', $subjectId)->whereIn('StudentID', $studentIDs)->countAllResults();
        if ($registeredStudents < count(array_unique($studentIDs))) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'มีนักเรียนที่ไม่ได้อยู่ในรายวิชานี้']);
        }

        foreach ($studentIDs as $num => $studentID) {
            $scores = $this->request->getPost($studentID); // Array of scores for this student
            $study_time = $study_times[$num];
            $grade = '';

            if ((($timeNum * 80) / 100) > $study_time) {
                $grade = "มส";
            } else {
                if (is_array($scores) && in_array("ร", $scores)) {
                    $grade = "ร";
                } else {
                    $grade = $this->check_grade(array_sum($scores));
                }
            }

            $key = [
                'StudentID' => $studentID,
                'SubjectID' => $this->request->getPost('SubjectID'),
                'RegisterYear' => $this->request->getPost('RegisterYear')
            ];

            $data = [
                'Score100' => implode("|", $scores),
                'Grade' => $grade,
                'StudyTime' => $study_time,
                'Grade_UpdateTime' => date('Y-m-d H:i:s')
            ];

            $builder = $this->db->table('tb_register');
            $builder->where($key);
            $query = $builder->get();

            if ($query->getNumRows() > 0) {
                $builder->update($data, $key);
            } else {
                $builder->insert(array_merge($key, $data));
            }
        }
        return $this->response->setJSON(['status' => 'success']);
    }

    public function autosaveScore() {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $studentID = $this->request->getPost('StudentID');
        $subjectID = $this->request->getPost('SubjectID');
        $registerYear = $this->request->getPost('RegisterYear');
        $study_time = $this->request->getPost('study_time');
        $scores = $this->request->getPost('scores');
        $timeNum = $this->request->getPost('TimeNum');

        if (empty($studentID) || empty($subjectID) || empty($registerYear)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Missing required identifiers.']);
        }
        $teacherId = (string) $this->session->get('person_id');
        if (!$this->hasTeacherPeriodRegistration($teacherId, $registerYear, $subjectID)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'ไม่พบรายวิชาที่คุณมีสิทธิ์จัดการ']);
        }
        if (!$this->canEditRegisterPeriod($teacherId, $registerYear)) {
            return $this->rejectHistoricalWrite();
        }
        if (!$this->db->table('tb_register')->where('TeacherID', $teacherId)->where('RegisterYear', $registerYear)
            ->where('SubjectID', $subjectID)->where('StudentID', $studentID)->countAllResults()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'ไม่มีสิทธิ์แก้ไขข้อมูลนักเรียนนี้']);
        }

        $grade = '';
        if ((($timeNum * 80) / 100) > $study_time) {
            $grade = "มส";
        } else {
            if (is_array($scores) && in_array("ร", $scores)) {
                $grade = "ร";
            } else {
                $numeric_scores = array_filter($scores, 'is_numeric');
                $grade = $this->check_grade(array_sum($numeric_scores));
            }
        }

        $key = [
            'StudentID' => $studentID,
            'SubjectID' => $subjectID,
            'RegisterYear' => $registerYear
        ];

        $data = [
            'Score100' => implode("|", $scores),
            'Grade' => $grade,
            'StudyTime' => $study_time,
            'Grade_UpdateTime' => date('Y-m-d H:i:s')
        ];

        $builder = $this->db->table('tb_register');
        $builder->where($key);
        $query = $builder->get();

        if ($query->getNumRows() > 0) {
            $builder->update($data, $key);
        } else {
            $builder->insert(array_merge($key, $data));
        }

        if ($this->db->affectedRows() > 0) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Score saved.']);
        } else {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Score up-to-date.']);
        }
    }

    public function historicalScoreAccess()
    {
        if (!$this->isAcademicAccessManager()) {
            return $this->response->setStatusCode(403)->setBody('คุณไม่มีสิทธิ์จัดการการปลดล็อกคะแนนย้อนหลัง');
        }

        $currentYear = $this->getCurrentSchoolYear();
        $pairs = $this->db->table('tb_register')->select('TeacherID, RegisterYear')
            ->where('RegisterYear !=', $currentYear)->groupBy(['TeacherID', 'RegisterYear'])
            ->orderBy('RegisterYear', 'DESC')->get()->getResultArray();
        usort($pairs, function ($a, $b) {
            $periodComparison = $this->compareRegisterYears((string) $a['RegisterYear'], (string) $b['RegisterYear']);
            return $periodComparison !== 0 ? $periodComparison : strcmp((string) $a['TeacherID'], (string) $b['TeacherID']);
        });

        $teacherIds = array_values(array_unique(array_map(static fn ($pair) => (string) $pair['TeacherID'], $pairs)));
        $teacherNames = [];
        if (!empty($teacherIds)) {
            $teachers = $this->personnelDb->table('tb_personnel')->select('pers_id, pers_prefix, pers_firstname, pers_lastname')
                ->whereIn('pers_id', $teacherIds)->get()->getResultArray();
            foreach ($teachers as $teacher) {
                $teacherNames[(string) $teacher['pers_id']] = trim(($teacher['pers_prefix'] ?? '') . ($teacher['pers_firstname'] ?? '') . ' ' . ($teacher['pers_lastname'] ?? ''));
            }
        }

        $grants = $this->db->table('tb_register_score_access')->get()->getResultArray();
        $grantStates = [];
        foreach ($grants as $grant) {
            $grantStates[$grant['teacher_id'] . '|' . $grant['register_year']] = $grant;
        }

        $data = [
            'title' => 'จัดการสิทธิ์แก้ไขคะแนนย้อนหลัง',
            'pairs' => $pairs,
            'teacher_names' => $teacherNames,
            'grant_states' => $grantStates,
        ];
        return view('teacher/register/SaveScore/HistoricalAccess', $data);
    }

    public function updateHistoricalScoreAccess()
    {
        if (!$this->isAcademicAccessManager()) {
            return $this->response->setStatusCode(403)->setBody('คุณไม่มีสิทธิ์จัดการการปลดล็อกคะแนนย้อนหลัง');
        }

        $teacherId = trim((string) $this->request->getPost('teacher_id'));
        $registerYear = trim((string) $this->request->getPost('register_year'));
        $enabled = (string) $this->request->getPost('enabled') === '1';
        if ($teacherId === '' || $registerYear === '' || $registerYear === $this->getCurrentSchoolYear()
            || !$this->hasTeacherPeriodRegistration($teacherId, $registerYear)) {
            return redirect()->to(site_url('assessment/historical-score-access'))->with('error', 'ข้อมูลครูหรือปีการศึกษาไม่ถูกต้อง');
        }

        $actorId = (string) $this->session->get('person_id');
        $builder = $this->db->table('tb_register_score_access');
        $existing = $builder->where('teacher_id', $teacherId)->where('register_year', $registerYear)->get()->getRowArray();
        $now = date('Y-m-d H:i:s');

        if ($enabled) {
            $values = ['is_enabled' => 1, 'granted_by' => $actorId, 'granted_at' => $now, 'revoked_by' => null, 'revoked_at' => null];
        } else {
            $values = ['is_enabled' => 0, 'revoked_by' => $actorId, 'revoked_at' => $now];
        }

        if ($existing) {
            $builder->where('teacher_id', $teacherId)->where('register_year', $registerYear)->update($values);
        } elseif ($enabled) {
            $builder->insert(array_merge(['teacher_id' => $teacherId, 'register_year' => $registerYear], $values));
        }

        return redirect()->to(site_url('assessment/historical-score-access'))->with('message', $enabled
            ? 'ปลดล็อกให้แก้ไขคะแนนสำหรับครูและปีการศึกษาที่เลือกแล้ว'
            : 'เพิกถอนสิทธิ์แก้ไขคะแนนย้อนหลังแล้ว');
    }

    public function checkroomReport(){
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }
        $loginId = $this->session->get('person_id');

        $check_room = $this->db->table('tb_register')
            ->select('tb_students.StudentClass')
            ->join('tb_subjects', 'tb_subjects.SubjectID = tb_register.SubjectID')
            ->join('tb_students', 'tb_students.StudentID = tb_register.StudentID')
            ->where('tb_register.TeacherID', $loginId)
            ->where('tb_register.RegisterYear', $this->request->getPost('report_yaer'))
            ->where('tb_register.SubjectID', $this->request->getPost('report_subject'))
            ->groupBy('tb_students.StudentClass')
            ->orderBy('tb_students.StudentClass', 'ASC')
            ->get()->getResult();

        return $this->response->setJSON($check_room);
    }

    public function ReportLearnNormal()
    {
    
        // mPDF is now loaded via Composer autoloader
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => WRITEPATH . 'cache',
            'fontDir' => array_merge($fontDirs, [
                ROOTPATH . 'vendor/mpdf/mpdf/ttfonts',
            ]),
            'fontdata' => $fontData + [
                'thsarabun' => [
                    'R' => 'THSarabunNew.ttf',
                    'B' => 'THSarabunNew Bold.ttf',
                    'I' => 'THSarabunNew Italic.ttf',
                    'BI' => 'THSarabunNew BoldItalic.ttf',
                ]
            ],
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 16,
            'default_font' => 'thsarabun'
        ]);

        $selectPrint = $this->request->getPost('select_print');
        $reportRegisterYear = $this->request->getPost('report_RegisterYear');
        $reportSubjectID = $this->request->getPost('report_SubjectID');
        $loginId = $this->session->get('person_id');

        // Fetch subject details
        $subject = $this->db->table('tb_subjects')
            ->where('SubjectYear', $reportRegisterYear)
            ->where('SubjectID', $reportSubjectID)
            ->get()->getRow();

        if (!$subject) {
            return "Error: Subject not found.";
        }
        $data['re_subjuct'] = [$subject];

        // Fetch score settings
        $data['set_score'] = $this->db->table('tb_register_score')
            ->where('regscore_subjectID', $subject->SubjectID)
            ->get()->getResult();

        // ดึงข้อมูลครูประจำวิชาจาก tb_register
        $mainTeacher = $this->db->table('tb_register')
            ->select('TeacherID')
            ->where('SubjectID', $reportSubjectID)
            ->where('RegisterYear', $reportRegisterYear)
            ->where('TeacherID', $loginId)
            ->where('TeacherID !=', '')
            ->limit(1)
            ->get()->getRow();

        $displayTeacherId = ($mainTeacher && !empty($mainTeacher->TeacherID)) ? $mainTeacher->TeacherID : $loginId;

        // Fetch teacher data for report
        $data['teacher_data'] = $this->personnelDb->table('tb_personnel')
            ->select('pers_prefix, pers_firstname, pers_lastname')
            ->where('pers_id', $displayTeacherId)
            ->get()->getRow();

        // Check if teacher_data is still null (fallback to empty object to prevent view errors)
        if (!$data['teacher_data']) {
            $data['teacher_data'] = (object)[
                'pers_prefix' => '',
                'pers_firstname' => 'ไม่พบข้อมูลครู',
                'pers_lastname' => ''
            ];
        }

        // Base query for student data
        $baseStudentQuery = function ($class = null) use ($loginId, $reportRegisterYear, $reportSubjectID) {
            $builder = $this->db->table('tb_register');
            $builder->select('
                    tb_register.SubjectID, tb_register.RegisterYear, tb_register.RegisterClass,
                    tb_register.Score100, tb_register.Grade, tb_register.TeacherID,
                    tb_register.StudyTime, tb_subjects.SubjectName, tb_subjects.SubjectCode,
                    tb_subjects.SubjectID, tb_subjects.SubjectUnit, tb_subjects.SubjectHour,
                    tb_students.StudentID, tb_students.StudentPrefix, tb_students.StudentFirstName,
                    tb_students.StudentLastName, tb_students.StudentNumber, tb_students.StudentClass,
                    tb_students.StudentCode, tb_students.StudentStatus, tb_students.StudentBehavior,
                    tb_register.Grade_Type
                ')
                ->join('tb_subjects', 'tb_subjects.SubjectID = tb_register.SubjectID')
                ->join('tb_students', 'tb_students.StudentID = tb_register.StudentID')
                ->where('tb_register.TeacherID', $loginId)
                ->where('tb_register.RegisterYear', $reportRegisterYear)
                ->where('tb_subjects.SubjectYear', $reportRegisterYear)
                ->where('tb_register.SubjectID', $reportSubjectID)
                ->orderBy('tb_students.StudentClass', 'ASC')
                ->orderBy('tb_students.StudentNumber', 'ASC');

            if ($class) {
                $builder->where('tb_students.StudentClass', $class);
            }

            return $builder->get()->getResult();
        };

        if ($selectPrint == "all") {
            $data['CheckPrint'] = "all";
            $data['re_room'] = "ทุกห้อง";
            $data['re_teacher'] = "";

            // Fetch all students for the cover page statistics (Total stats across all rooms)
            $all_students = $baseStudentQuery();
            $data['check_student'] = $all_students;
            $data['test'] = $reportRegisterYear;

            // Generate Front Page (once for all)
            $reportFront = view('teacher/register/SaveScore/report/ReportFront', $data);
            $mpdf->WriteHTML($reportFront);

            // Get list of rooms
            $levels = $this->db->table('tb_register')
                ->select('tb_students.StudentClass')
                ->join('tb_subjects', 'tb_subjects.SubjectID = tb_register.SubjectID')
                ->join('tb_students', 'tb_students.StudentID = tb_register.StudentID')
                ->where('tb_register.TeacherID', $loginId)
                ->where('tb_register.RegisterYear', $reportRegisterYear)
                ->where('tb_subjects.SubjectYear', $reportRegisterYear)
                ->where('tb_register.SubjectID', $reportSubjectID)
                ->groupBy('tb_students.StudentClass')
                ->orderBy('tb_students.StudentClass', 'ASC')
                ->get()->getResult();

            foreach ($levels as $level) {
                $data['check_student1'] = $baseStudentQuery($level->StudentClass);
                $data['re_room'] = $level->StudentClass;

                $mpdf->AddPage();
                $reportSummary = view('teacher/register/SaveScore/report/ReportSummary', $data);
                $mpdf->WriteHTML($reportSummary);
            }
        } else {
            $data['CheckPrint'] = "single"; // Mark as single room for logic if needed
            $data['re_room'] = $selectPrint;

            $sub_Year = explode("/", $reportRegisterYear ?? '');
            $year_for_db = $sub_Year[1] ?? ($sub_Year[0] ?? '');
            
            $sub_room = explode(".", $selectPrint ?? '');
            $room_for_db = $sub_room[1] ?? ($sub_room[0] ?? '');

            if ($year_for_db != '' && $room_for_db != '') {
                 $data['re_teacher'] = $this->db->table('tb_regclass')
                    ->select('
                        skjacth_personnel.tb_personnel.pers_id,
                        tb_regclass.Reg_Class,
                        tb_regclass.Reg_Year,
                        skjacth_personnel.tb_personnel.pers_prefix,
                        skjacth_personnel.tb_personnel.pers_firstname,
                        skjacth_personnel.tb_personnel.pers_lastname
                    ')
                    ->join('skjacth_personnel.tb_personnel', 'skjacth_personnel.tb_personnel.pers_id = tb_regclass.class_teacher', 'left')
                    ->where('Reg_Year', $year_for_db)
                    ->where('Reg_Class', $room_for_db)
                    ->get()->getResult();
            } else {
                $data['re_teacher'] = [];
            }

            // Fetch students only for this specific room
            $room_students = $baseStudentQuery($selectPrint);
            $data['check_student'] = $room_students;
            $data['check_student1'] = $room_students; // For summary page compatibility
            $data['test'] = $reportRegisterYear;

            // Generate Front Page for single room
            $reportFront = view('teacher/register/SaveScore/report/ReportFront', $data);
            $mpdf->WriteHTML($reportFront);

            // Append Summary Page
            $mpdf->AddPage();
            $reportSummary = view('teacher/register/SaveScore/report/ReportSummary', $data);
            $mpdf->WriteHTML($reportSummary);
        }

        $this->response->setHeader('Content-Type', 'application/pdf');
        return $mpdf->Output('filename.pdf', \Mpdf\Output\Destination::INLINE);
    }

}
