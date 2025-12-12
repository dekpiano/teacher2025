<?php

namespace App\Controllers;

use App\Models\ClubModel;
use CodeIgniter\Controller;

class ClubController extends BaseController
{
    protected $clubModel;
    protected $currentAcademicYear;
    protected $currentTerm;

    public function __construct()
    {
        $this->clubModel = new ClubModel();
        $this->_loadAcademicYear();
    }

    private function _loadAcademicYear()
    {
        $db = db_connect();
        $club_onoff = $db->table('tb_club_onoff')
                         ->select('c_onoff_year, c_onoff_term')
                         ->orderBy('c_onoff_id', 'DESC')
                         ->get()
                         ->getRow();
        
        if ($club_onoff) {
            $this->currentAcademicYear = $club_onoff->c_onoff_year;
            $this->currentTerm = $club_onoff->c_onoff_term;
        } else {
            $this->currentAcademicYear = null;
            $this->currentTerm = null;
        }
    }

    private function _getClubStatus(): string
    {
        $clubOnOffSettings = $this->clubModel->getClubOnOffSettings($this->currentAcademicYear, $this->currentTerm);
        $currentDate = date('Y-m-d');

        if ($clubOnOffSettings) {
            $regisStart = $clubOnOffSettings->c_onoff_regisstart;
            $regisEnd = $clubOnOffSettings->c_onoff_regisend;

            if ($currentDate < $regisStart) {
                return 'pending'; // ยังไม่เปิดรับสมัคร
            } elseif ($currentDate >= $regisStart && $currentDate <= $regisEnd) {
                return 'open'; // เปิดรับสมัคร
            } else {
                return 'closed'; // ปิดรับสมัคร
            }
        }
        return 'closed'; // Default to closed if no settings found
    }

    private function getTeacherId()
    {
        $session = session();
        // Assuming 'person_id' is stored in the session after login
        return $session->get('person_id');
    }

    private function isClubAdvisor(string $teacherId, object $club): bool
    {
        if (!$club) {
            return false;
        }
        $advisors = explode('|', $club->club_faculty_advisor);
        return in_array($teacherId, $advisors);
    }

    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        if (!$teacherId) {
            // Handle case where teacher ID is not found in session
            session()->setFlashdata('error', 'ไม่พบข้อมูลครูผู้สอน กรุณาเข้าสู่ระบบใหม่');
            return redirect()->to('login');
        }

        $data['title'] = "ชุมนุมที่ปรึกษา";
        $data['clubs'] = $this->clubModel->getClubsByTeacher($teacherId, $this->currentAcademicYear, $this->currentTerm);
        $data['currentAcademicYear'] = $this->currentAcademicYear;
        $data['currentTerm'] = $this->currentTerm;

        $clubOnOffSettings = $this->clubModel->getClubOnOffSettings($this->currentAcademicYear, $this->currentTerm);
        $data['registrationStartDate'] = $clubOnOffSettings ? $clubOnOffSettings->c_onoff_regisstart : null;
        $data['registrationEndDate'] = $clubOnOffSettings ? $clubOnOffSettings->c_onoff_regisend : null;

        // Check if the teacher has already created a club for the current year and term
        $hasClubForCurrentYear = false;
        if (!empty($data['clubs'])) { // Corrected condition: check if clubs array is NOT empty
            foreach ($data['clubs'] as $club) {
                if ($club->club_year == $this->currentAcademicYear && $club->club_trem == $this->currentTerm) {
                    $hasClubForCurrentYear = true;
                    break;
                }
            }
        }
        $data['hasClubForCurrentYear'] = $hasClubForCurrentYear;

        return view('teacher/club/index', $data);
    }

    public function create()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        if (!$teacherId) {
            session()->setFlashdata('error', 'เซสชั่นหมดอายุหรือไม่พบข้อมูลครู');
            return redirect()->to('login');
        }

        $data = [
            'club_name' => $this->request->getPost('club_name'),
            'club_description' => $this->request->getPost('club_description'),
            'club_max_participants' => $this->request->getPost('club_max_participants'),
            'club_level' => $this->request->getPost('club_level'),
            'club_faculty_advisor' => $teacherId,
            'club_year' => $this->currentAcademicYear,
            'club_trem' => $this->currentTerm,
            'club_status' => $this->_getClubStatus(), // Determine status dynamically
            'club_established_date' => date('Y-m-d'),
        ];

        if ($this->clubModel->insert($data)) {
            session()->setFlashdata('success', 'สร้างชุมนุมใหม่สำเร็จ');
        } else {
            session()->setFlashdata('error', 'ไม่สามารถสร้างชุมนุมได้');
        }

        return redirect()->to('club');
    }

    public function update($clubId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);

        // Verify ownership
        if (!$this->isClubAdvisor($teacherId, $club)) {
            session()->setFlashdata('error', 'ไม่พบชุมนุมหรือคุณไม่มีสิทธิ์แก้ไข');
            return redirect()->to('club');
        }

        $data = [
            'club_name' => $this->request->getPost('club_name'),
            'club_description' => $this->request->getPost('club_description'),
            'club_max_participants' => $this->request->getPost('club_max_participants'),
            'club_status' => $this->request->getPost('club_status'),
            'club_level' => $this->request->getPost('club_level'),
        ];

        if ($this->clubModel->update($clubId, $data)) {
            session()->setFlashdata('success', 'อัปเดตข้อมูลชุมนุมสำเร็จ');
        } else {
            session()->setFlashdata('error', 'ไม่สามารถอัปเดตข้อมูลชุมนุมได้');
        }

        return redirect()->to('club/manage/' . $clubId);
    }

    public function updateMemberRole($clubId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);

        // Verify ownership
        if (!$this->isClubAdvisor($teacherId, $club)) {
            session()->setFlashdata('error', 'ไม่พบชุมนุมหรือคุณไม่มีสิทธิ์จัดการชุมนุมนี้');
            return redirect()->to('club');
        }

        $studentId = $this->request->getPost('student_id');
        $newRole = $this->request->getPost('member_role');

        if ($this->clubModel->updateMemberRole($clubId, $studentId, $newRole)) {
            session()->setFlashdata('success', 'อัปเดตบทบาทสมาชิกสำเร็จ');
        } else {
            session()->setFlashdata('error', 'ไม่สามารถอัปเดตบทบาทสมาชิกได้');
        }

        return redirect()->to('club/manage/' . $clubId);
    }

    public function removeMember($clubId, $studentId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);

        if (!$this->isClubAdvisor($teacherId, $club)) {
            session()->setFlashdata('error', 'ไม่พบชุมนุมหรือคุณไม่มีสิทธิ์จัดการชุมนุมนี้');
            return redirect()->to('club');
        }

        if ($this->clubModel->removeMember($clubId, $studentId)) {
            session()->setFlashdata('success', 'ลบสมาชิกออกจากชุมนุมสำเร็จ');
        } else {
            session()->setFlashdata('error', 'ไม่สามารถลบสมาชิกออกจากชุมนุมได้');
        }

        return redirect()->to('club/manage/' . $clubId);
    }

    public function manage($clubId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        if (!$teacherId) {
            session()->setFlashdata('error', 'ไม่พบข้อมูลครูผู้สอน กรุณาเข้าสู่ระบบใหม่');
            return redirect()->to('login');
        }

        $club = $this->clubModel->find($clubId);

        if (!$this->isClubAdvisor($teacherId, $club)) {
            session()->setFlashdata('error', 'ไม่พบชุมนุมหรือคุณไม่มีสิทธิ์จัดการชุมนุมนี้');
            return redirect()->to('club');
        }

        $data['title'] = "จัดการชุมนุม: " . $club->club_name;
        $data['club'] = $club;
        $data['members'] = $this->clubModel->getClubMembers($clubId);

        return view('teacher/club/manage', $data);
    }

    // --- Attendance Recording (Part D) ---

    public function showSchedule($clubId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);

        if (!$this->isClubAdvisor($teacherId, $club)) {
            session()->setFlashdata('error', 'ไม่พบชุมนุมหรือคุณไม่มีสิทธิ์จัดการชุมนุมนี้');
            return redirect()->to('club');
        }

        $data['title'] = "ตารางกิจกรรมชุมนุม: " . $club->club_name;
        $data['club'] = $club;
        $data['schedules'] = $this->clubModel->getSchedulesByYear($club->club_year, $club->club_trem, $clubId);

        // Add attendance status to each schedule
        foreach ($data['schedules'] as $schedule) {
            $schedule->attendance_recorded = $this->clubModel->hasAttendanceRecorded($schedule->tcs_schedule_id);
        }

        return view('teacher/club/schedule', $data);
    }

    public function createSchedule($clubId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);

        if (!$this->isClubAdvisor($teacherId, $club)) {
            session()->setFlashdata('error', 'ไม่พบชุมนุมหรือคุณไม่มีสิทธิ์จัดการชุมนุมนี้');
            return redirect()->to('club');
        }

        $data = [
            'tcs_academic_year' => $this->currentAcademicYear . '/' . $this->currentTerm,
            'tcs_start_date' => $this->request->getPost('schedule_date'),
            'tcs_week_number' => $this->request->getPost('schedule_title'),
            'tcs_week_status' => 'เปิด',
        ];

        if ($this->clubModel->insertSchedule($data)) {
            session()->setFlashdata('success', 'สร้างตารางกิจกรรมสำเร็จ');
        } else {
            session()->setFlashdata('error', 'ไม่สามารถสร้างตารางกิจกรรมได้');
        }

        return redirect()->to('club/schedule/' . $clubId);
    }

    public function saveActivity($clubId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);

        if (!$this->isClubAdvisor($teacherId, $club)) {
            session()->setFlashdata('error', 'ไม่พบชุมนุมหรือคุณไม่มีสิทธิ์จัดการชุมนุมนี้');
            return redirect()->to('club');
        }

        $data = [
            'act_club_id' => $clubId,
            'act_date' => $this->request->getPost('activity_date'),
            'act_name' => $this->request->getPost('activity_name'),
            'act_description' => $this->request->getPost('activity_description'),
            'act_location' => $this->request->getPost('activity_location'),
            'act_start_time' => $this->request->getPost('activity_start_time'),
            'act_end_time' => $this->request->getPost('activity_end_time'),
            'act_number_of_periods' => $this->request->getPost('act_number_of_periods'),
        ];

        // Basic validation
        if (empty($data['act_date']) || empty($data['act_name'])) {
            session()->setFlashdata('error', 'กรุณากรอกข้อมูลกิจกรรมให้ครบถ้วน');
            return redirect()->to('club/schedule/' . $clubId);
        }

        if ($this->clubModel->upsertActivity($data)) {
            session()->setFlashdata('success', 'บันทึกข้อมูลกิจกรรมสำเร็จ');
        } else {
            session()->setFlashdata('error', 'ไม่สามารถบันทึกข้อมูลกิจกรรมได้');
        }

        return redirect()->to('club/schedule/' . $clubId);
    }


    public function recordAttendance($clubId, $scheduleId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);
        $schedule = $this->clubModel->findSchedule($scheduleId);

        if (!$this->isClubAdvisor($teacherId, $club) || !$schedule) {
            session()->setFlashdata('error', 'ไม่พบข้อมูลหรือคุณไม่มีสิทธิ์จัดการ');
            return redirect()->to('club');
        }

        $data['title'] = "บันทึกการเข้าเรียนชุมนุม : " . $club->club_name . " - สัปดาห์ที่ " . $schedule->tcs_week_number;
        $data['club'] = $club;
        $data['schedule'] = $schedule;
        $data['members'] = $this->clubModel->getClubMembers($clubId);

        // Fetch existing attendance for this schedule
        $existingAttendance = [];
        $record = $this->clubModel->getAttendanceBySchedule($scheduleId);        
        $data['hasAttendanceRecord'] = !empty($record);
        if (!empty($record)) {
            // The model returns an array of records, but we expect only one
            $record = $record[0]; 
            $statusMap = [
                'มา' => !empty($record->tcra_ma) ? explode(',', $record->tcra_ma) : [],
                'ขาด' => !empty($record->tcra_khad) ? explode(',', $record->tcra_khad) : [],
                'ลาป่วย' => !empty($record->tcra_rapwy) ? explode(',', $record->tcra_rapwy) : [],
                'ลากิจ' => !empty($record->tcra_rakic) ? explode(',', $record->tcra_rakic) : [],
                'กิจกรรม' => !empty($record->tcra_kickrrm) ? explode(',', $record->tcra_kickrrm) : [],
            ];

            foreach ($statusMap as $status => $studentIds) {
                foreach ($studentIds as $studentId) {
                    $existingAttendance[$studentId] = $status;
                }
            }
        }
        $data['existingAttendance'] = $existingAttendance;

        return view('teacher/club/record_attendance', $data);
    }

    public function saveAttendance($clubId, $scheduleId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);
        $schedule = $this->clubModel->findSchedule($scheduleId);

        if (!$this->isClubAdvisor($teacherId, $club) || !$schedule) {
            session()->setFlashdata('error', 'ไม่พบข้อมูลหรือคุณไม่มีสิทธิ์จัดการ');
            return redirect()->to('club');
        }

        $attendanceData = $this->request->getPost('attendance'); // Array of student_id => status

        if (!empty($attendanceData)) {
            // Categorize students by status
            $statusGroups = [
                'มา' => [],
                'ขาด' => [],
                'ลาป่วย' => [],
                'ลากิจ' => [],
                'กิจกรรม' => [],
            ];

            foreach ($attendanceData as $studentId => $status) {
                if (array_key_exists($status, $statusGroups)) {
                    $statusGroups[$status][] = $studentId;
                }
            }

            // Prepare data for the model
            $dataToSave = [
                'tcra_club_id' => $clubId,
                'trca_schedule_id' => $scheduleId,
                'tcra_teac_id' => $teacherId,
                'tcra_ma' => implode(',', $statusGroups['มา']),
                'tcra_khad' => implode(',', $statusGroups['ขาด']),
                'tcra_rapwy' => implode(',', $statusGroups['ลาป่วย']),
                'tcra_rakic' => implode(',', $statusGroups['ลากิจ']),
                'tcra_kickrrm' => implode(',', $statusGroups['กิจกรรม']),
            ];

            if ($this->clubModel->saveScheduleAttendance($dataToSave)) {
                session()->setFlashdata('success', "บันทึกการเข้าเรียนสำเร็จ");
            } else {
                session()->setFlashdata('error', 'ไม่สามารถบันทึกข้อมูลการเข้าเรียนได้');
            }
        } else {
            session()->setFlashdata('error', 'ไม่พบข้อมูลการเข้าเรียนที่จะบันทึก');
        }

        return redirect()->to('club/recordAttendance/' . $clubId . '/' . $scheduleId);
    }

    // --- Activity Reports (Part E) ---

    public function showActivities($clubId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);

        if (!$this->isClubAdvisor($teacherId, $club)) {
            session()->setFlashdata('error', 'ไม่พบชุมนุมหรือคุณไม่มีสิทธิ์จัดการชุมนุมนี้');
            return redirect()->to('club');
        }

        $data['title'] = "รายงานการบันทึกเวลาเรียน: " . $club->club_name;
        $data['club'] = $club;
        $data['members'] = $this->clubModel->getClubMembers($clubId);
        
        $schedules = $this->clubModel->getSchedulesByYear($club->club_year, $club->club_trem, $clubId);
        
        // Group schedules by month
        $schedulesByMonth = [];
        foreach ($schedules as $schedule) {
            if ($schedule->tcs_start_date != '0000-00-00') {
                $month = date('Y-m', strtotime($schedule->tcs_start_date));
                $schedulesByMonth[$month][] = $schedule;
            }
        }
        ksort($schedulesByMonth);
        $data['schedulesByMonth'] = $schedulesByMonth;
        $data['schedules'] = $schedules;

        // Fetch all attendance records for the schedules
        $scheduleIds = array_map(function($s) { return $s->tcs_schedule_id; }, $schedules);
        $allAttendance = [];
        if (!empty($scheduleIds)) {
            $allAttendance = $this->clubModel->getAttendanceByScheduleIds($scheduleIds);
        }
        
        // Create a map for easy lookup: [student_id][schedule_id] => status
        $attendanceMap = [];
        foreach ($allAttendance as $record) {
            $statusMap = [
                'มา' => !empty($record->tcra_ma) ? explode(',', $record->tcra_ma) : [],
                'ขาด' => !empty($record->tcra_khad) ? explode(',', $record->tcra_khad) : [],
                'ลาป่วย' => !empty($record->tcra_rapwy) ? explode(',', $record->tcra_rapwy) : [],
                'ลากิจ' => !empty($record->tcra_rakic) ? explode(',', $record->tcra_rakic) : [],
                'กิจกรรม' => !empty($record->tcra_kickrrm) ? explode(',', $record->tcra_kickrrm) : [],
            ];

            foreach ($statusMap as $status => $studentIds) {
                foreach ($studentIds as $studentId) {
                    $attendanceMap[$studentId][$record->trca_schedule_id] = $status;
                }
            }
        }
        $data['attendanceMap'] = $attendanceMap;

        // --- Fetch data for Objectives Report ---
        $data['club_objectives'] = $this->clubModel->getObjectivesByClub($clubId);
       
        $progressData = $this->clubModel->getClubStudentProgress($clubId);
        
        // Create a map for easy lookup: [student_id][objective_id] => true/false
        $objectiveProgressMap = [];
        if (!empty($progressData)) {
            foreach ($progressData as $studentId => $objectives) {
                foreach ($objectives as $objectiveId => $status) {
                    // Assuming status '1' means passed
                    $objectiveProgressMap[$studentId][$objectiveId] = (isset($status->status) && $status->status == 1);
                }
            }
        }
        $data['objectiveProgressMap'] = $objectiveProgressMap;

        return view('teacher/club/activities', $data);
    }

    public function printActivitiesReport($clubId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);

        if (!$this->isClubAdvisor($teacherId, $club)) {
            session()->setFlashdata('error', 'ไม่พบชุมนุมหรือคุณไม่มีสิทธิ์จัดการชุมนุมนี้');
            return redirect()->to('club');
        }

        $data['club'] = $club;
        $data['members'] = $this->clubModel->getClubMembers($clubId);
        
        $schedules = $this->clubModel->getSchedulesByYear($club->club_year, $club->club_trem, $clubId);
        
        // Group schedules by month
        $schedulesByMonth = [];
        foreach ($schedules as $schedule) {
            if ($schedule->tcs_start_date != '0000-00-00') {
                $month = date('Y-m', strtotime($schedule->tcs_start_date));
                $schedulesByMonth[$month][] = $schedule;
            }
        }
        ksort($schedulesByMonth);
        $data['schedulesByMonth'] = $schedulesByMonth;
        $data['schedules'] = $schedules;

        // Fetch all attendance records for the schedules
        $scheduleIds = array_map(function($s) { return $s->tcs_schedule_id; }, $schedules);
        $allAttendance = [];
        if (!empty($scheduleIds)) {
            $allAttendance = $this->clubModel->getAttendanceByScheduleIds($scheduleIds);
        }
        
        // Create a map for easy lookup: [student_id][schedule_id] => status
        $attendanceMap = [];
        foreach ($allAttendance as $record) {
            $statusMap = [
                'มา' => !empty($record->tcra_ma) ? explode(',', $record->tcra_ma) : [],
                'ขาด' => !empty($record->tcra_khad) ? explode(',', $record->tcra_khad) : [],
                'ลาป่วย' => !empty($record->tcra_rapwy) ? explode(',', $record->tcra_rapwy) : [],
                'ลากิจ' => !empty($record->tcra_rakic) ? explode(',', $record->tcra_rakic) : [],
                'กิจกรรม' => !empty($record->tcra_kickrrm) ? explode(',', $record->tcra_kickrrm) : [],
            ];

            foreach ($statusMap as $status => $studentIds) {
                foreach ($studentIds as $studentId) {
                    $attendanceMap[$studentId][$record->trca_schedule_id] = $status;
                }
            }
        }
        $data['attendanceMap'] = $attendanceMap;

        // --- Fetch data for Objectives Report ---
        $data['club_objectives'] = $this->clubModel->getObjectivesByClub($clubId);
        $progressData = $this->clubModel->getClubStudentProgress($clubId);
        
        // Create a map for easy lookup: [student_id][objective_id] => true/false
        $objectiveProgressMap = [];
        if (!empty($progressData)) {
            foreach ($progressData as $studentId => $objectives) {
                foreach ($objectives as $objectiveId => $status) {
                    // Assuming status '1' means passed
                    $objectiveProgressMap[$studentId][$objectiveId] = (isset($status->status) && $status->status == 1);
                }
            }
        }
        $data['objectiveProgressMap'] = $objectiveProgressMap;

        // --- Calculate Summary for Activity Participation ---
        $totalPassedStudents = 0;
        $totalFailedStudents = 0;
        $totalLongAbsenceStudents = 0; // Placeholder for now
        $totalDismissedStudents = 0; // Placeholder for now

        $totalPossibleSchedules = 0;
        foreach ($schedulesByMonth as $schedulesInMonth) {
            $totalPossibleSchedules += count($schedulesInMonth);
        }
        $totalObjectivesCount = count($data['club_objectives']);

        foreach ($data['members'] as $member) {
            $studentId = $member->StudentID;
            $totalPresent = 0;
            foreach ($schedules as $schedule) {
                $status = $attendanceMap[$studentId][$schedule->tcs_schedule_id] ?? '-';
                if ($status === 'มา') {
                    $totalPresent++;
                }
            }

            $attendancePercentage = ($totalPossibleSchedules > 0) ? ($totalPresent / $totalPossibleSchedules) * 100 : 0;

            $passedAllObjectives = true;
            if ($totalObjectivesCount > 0) {
                $passedCount = 0;
                foreach ($data['club_objectives'] as $objective) {
                    if (isset($objectiveProgressMap[$studentId][$objective->objective_id]) && $objectiveProgressMap[$studentId][$objective->objective_id]) {
                        $passedCount++;
                    }
                }
                if ($passedCount !== $totalObjectivesCount) {
                    $passedAllObjectives = false;
                }
            } else {
                // If no objectives are defined, consider them passed by default for objective criteria
                $passedAllObjectives = true;
            }

            // Determine overall status
            if ($attendancePercentage >= 80 && $passedAllObjectives) {
                $totalPassedStudents++;
            } else {
                $totalFailedStudents++;
            }

            // Basic derivation for long absence and dismissed (can be refined with user input)
            if ($attendancePercentage < 50 && $attendancePercentage > 0) {
                $totalLongAbsenceStudents++;
            } elseif ($attendancePercentage == 0) {
                $totalDismissedStudents++;
            }
        }

        $data['summaryParticipation'] = [
            'totalStudents' => count($data['members']),
            'passed' => $totalPassedStudents,
            'failed' => $totalFailedStudents,
            'longAbsence' => $totalLongAbsenceStudents,
            'dismissed' => $totalDismissedStudents,
        ];

        // --- Fetch Signatory Names for Page 4 ---
        $advisorIds = explode('|', $club->club_faculty_advisor);
        $advisorNames = [];
        foreach ($advisorIds as $id) {
            if (empty(trim($id))) continue;
            $info = $this->clubModel->getPersonnelFullName($id);
            if ($info) {
                $advisorNames[] = $info['pers_prefix'] . $info['pers_firstname'] . ' ' . $info['pers_lastname'];
            }
        }

        // ผู้ดูแลกิจกรรม (All advisors)
        if (!empty($advisorNames)) {
            $data['evaluatorName'] = implode(', ', $advisorNames);
        } else {
            // Fallback to current teacher if no advisors are listed
            $teacherInfo = $this->clubModel->getPersonnelFullName($teacherId);
            $data['evaluatorName'] = $teacherInfo ? $teacherInfo['pers_prefix'] . $teacherInfo['pers_firstname'] . ' ' . $teacherInfo['pers_lastname'] : '...........................................';
        }

        // หัวหน้ากิจกรรม (First advisor)
        if (!empty($advisorNames)) {
            $data['activityHeadName'] = $advisorNames[0];
        } else {
            // Fallback to the same as evaluator if list is empty
            $data['activityHeadName'] = $data['evaluatorName'];
        }

        // หัวหน้างานกิจกรรมพัฒนาผู้เรียน
        $activityDevHeadId = $this->clubModel->getAdminPersonnelIdByRoleName('งานกิจกรรมพัฒนาผู้เรียน');
        $activityDevHeadInfo = $activityDevHeadId ? $this->clubModel->getPersonnelFullName($activityDevHeadId) : null;
        $data['activityDevHeadName'] = $activityDevHeadInfo ? $activityDevHeadInfo['pers_prefix'] . $activityDevHeadInfo['pers_firstname'] . ' ' . $activityDevHeadInfo['pers_lastname'] : '...........................................';

        // หัวหน้าฝ่ายวัดผล
        $measurementHeadId = $this->clubModel->getAdminPersonnelIdByRoleName('หัวหน้าวิชาการ');
        $measurementHeadInfo = $measurementHeadId ? $this->clubModel->getPersonnelFullName($measurementHeadId) : null;
        $data['measurementHeadName'] = $measurementHeadInfo ? $measurementHeadInfo['pers_prefix'] . $measurementHeadInfo['pers_firstname'] . ' ' . $measurementHeadInfo['pers_lastname'] : '...........................................';

        // รองผู้อำนวยการฝ่ายวิชาการ
        $deputyDirectorAcademicId = $this->clubModel->getAdminPersonnelIdByRoleName('รองวิชาการ');
        $deputyDirectorAcademicInfo = $deputyDirectorAcademicId ? $this->clubModel->getPersonnelFullName($deputyDirectorAcademicId) : null;
        $data['deputyDirectorAcademicName'] = $deputyDirectorAcademicInfo ? $deputyDirectorAcademicInfo['pers_prefix'] . $deputyDirectorAcademicInfo['pers_firstname'] . ' ' . $deputyDirectorAcademicInfo['pers_lastname'] : '...........................................';

        // ผู้อำนวยการสถานศึกษา
        $directorId = $this->clubModel->getAdminPersonnelIdByRoleName('ผู้บริหาร');
        $directorInfo = $directorId ? $this->clubModel->getPersonnelFullName($directorId) : null;
        $data['directorName'] = $directorInfo ? $directorInfo['pers_prefix'] . $directorInfo['pers_firstname'] . ' ' . $directorInfo['pers_lastname'] : '...........................................';

        // Fetch activities for the new page
        $data['activities'] = $this->clubModel->getActivitiesByClub($clubId);

        return view('teacher/club/print_activities', $data);
    }

    public function createActivity($clubId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);

        if (!$this->isClubAdvisor($teacherId, $club)) {
            session()->setFlashdata('error', 'ไม่พบชุมนุมหรือคุณไม่มีสิทธิ์จัดการชุมนุมนี้');
            return redirect()->to('club');
        }

        $data = [
            'act_club_id' => $clubId,
            'activity_date' => $this->request->getPost('activity_date'),
            'activity_title' => $this->request->getPost('activity_title'),
            'activity_description' => $this->request->getPost('activity_description'),
            // 'activity_image' => handle file upload later
        ];

        if ($this->clubModel->insertActivity($data)) {
            session()->setFlashdata('success', 'สร้างกิจกรรมสำเร็จ');
        } else {
            session()->setFlashdata('error', 'ไม่สามารถสร้างกิจกรรมได้');
        }

        return redirect()->to('club/activities/' . $clubId);
    }

    public function editActivity($clubId, $activityId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);
        $activity = $this->clubModel->findActivity($activityId);

        if (!$this->isClubAdvisor($teacherId, $club) || !$activity || $activity->club_id != $clubId) {
            session()->setFlashdata('error', 'ไม่พบข้อมูลหรือคุณไม่มีสิทธิ์จัดการ');
            return redirect()->to('club');
        }

        $data['title'] = "แก้ไขกิจกรรม: " . $activity->activity_title;
        $data['club'] = $club;
        $data['activity'] = $activity;

        return view('teacher/club/activities', $data); // Will use the same view with modal
    }

    public function updateActivity($clubId, $activityId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);
        $activity = $this->clubModel->findActivity($activityId);

        if (!$this->isClubAdvisor($teacherId, $club) || !$activity || $activity->club_id != $clubId) {
            session()->setFlashdata('error', 'ไม่พบข้อมูลหรือคุณไม่มีสิทธิ์จัดการ');
            return redirect()->to('club');
        }

        $data = [
            'activity_date' => $this->request->getPost('activity_date'),
            'activity_title' => $this->request->getPost('activity_title'),
            'activity_description' => $this->request->getPost('activity_description'),
            // 'activity_image' => handle file upload later
        ];

        if ($this->clubModel->updateActivity($activityId, $data)) {
            session()->setFlashdata('success', 'อัปเดตกิจกรรมสำเร็จ');
        } else {
            session()->setFlashdata('error', 'ไม่สามารถอัปเดตกิจกรรมได้');
        }

        return redirect()->to('club/activities/' . $clubId);
    }

    public function deleteActivity($clubId, $activityId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);
        $activity = $this->clubModel->findActivity($activityId);

        if (!$this->isClubAdvisor($teacherId, $club) || !$activity || $activity->club_id != $clubId) {
            session()->setFlashdata('error', 'ไม่พบข้อมูลหรือคุณไม่มีสิทธิ์จัดการ');
            return redirect()->to('club');
        }

        if ($this->clubModel->deleteActivity($activityId)) {
            session()->setFlashdata('success', 'ลบกิจกรรมสำเร็จ');
        } else {
            session()->setFlashdata('error', 'ไม่สามารถลบกิจกรรมได้');
        }

        return redirect()->to('club/activities/' . $clubId);
    }

    public function objectives($clubId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);

        if (!$this->isClubAdvisor($teacherId, $club)) {
            session()->setFlashdata('error', 'ไม่พบชุมนุมหรือคุณไม่มีสิทธิ์จัดการชุมนุมนี้');
            return redirect()->to('club');
        }

        $data['title'] = "จุดประสงค์กิจกรรม: " . $club->club_name;
        $data['club'] = $club;
        $data['members'] = $this->clubModel->getClubMembers($clubId);
        $data['objectives'] = $this->clubModel->getObjectivesByClub($clubId);
        $data['progress'] = $this->clubModel->getClubStudentProgress($clubId);

        return view('teacher/club/objectives', $data);
    }

    public function saveObjectives($clubId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);

        if (!$this->isClubAdvisor($teacherId, $club)) {
            session()->setFlashdata('error', 'ไม่พบชุมนุมหรือคุณไม่มีสิทธิ์จัดการชุมนุมนี้');
            return redirect()->to('club');
        }

        $progressData = $this->request->getPost('progress');
        
        if ($this->clubModel->saveStudentProgress($clubId, $teacherId, $progressData)) {
            session()->setFlashdata('success', 'บันทึกข้อมูลจุดประสงค์กิจกรรมสำเร็จ');
        } else {
            session()->setFlashdata('error', 'ไม่สามารถบันทึกข้อมูลได้');
        }

        return redirect()->to('club/objectives/' . $clubId);
    }

    public function saveObjectiveDefinition($clubId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);

        if (!$this->isClubAdvisor($teacherId, $club)) {
            session()->setFlashdata('error', 'ไม่พบชุมนุมหรือคุณไม่มีสิทธิ์จัดการชุมนุมนี้');
            return redirect()->to('club');
        }

        $objectiveId = $this->request->getPost('objective_id');
        $objectiveName = $this->request->getPost('objective_name');
        $objectiveDescription = $this->request->getPost('objective_description');
        $objectiveOrder = $this->request->getPost('objective_order');

        $data = [
            'club_id' => $clubId,
            'objective_name' => $objectiveName,
            'objective_description' => $objectiveDescription,
            'objective_order' => $objectiveOrder,
            'created_by' => $teacherId, // Assuming created_by is the current teacher
        ];

        if (empty($objectiveId)) {
            // Add new objective
            if ($this->clubModel->addObjective($data)) {
                session()->setFlashdata('success', 'เพิ่มจุดประสงค์สำเร็จ');
            } else {
                session()->setFlashdata('error', 'ไม่สามารถเพิ่มจุดประสงค์ได้');
            }
        } else {
            // Update existing objective
            if ($this->clubModel->updateObjective($objectiveId, $data)) {
                session()->setFlashdata('success', 'แก้ไขจุดประสงค์สำเร็จ');
            } else {
                session()->setFlashdata('error', 'ไม่สามารถแก้ไขจุดประสงค์ได้');
            }
        }

        return redirect()->to('club/objectives/' . $clubId);
    }

    public function deleteObjective($clubId, $objectiveId)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $teacherId = $this->getTeacherId();
        $club = $this->clubModel->find($clubId);

        if (!$this->isClubAdvisor($teacherId, $club)) {
            session()->setFlashdata('error', 'ไม่พบชุมนุมหรือคุณไม่มีสิทธิ์จัดการชุมนุมนี้');
            return redirect()->to('club');
        }

        if ($this->clubModel->deleteObjective($objectiveId)) {
            session()->setFlashdata('success', 'ลบจุดประสงค์สำเร็จ');
        } else {
            session()->setFlashdata('error', 'ไม่สามารถลบจุดประสงค์ได้');
        }

        return redirect()->to('club/objectives/' . $clubId);
    }
}