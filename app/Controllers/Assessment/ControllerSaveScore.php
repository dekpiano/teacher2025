<?php

namespace App\Controllers\Assessment;

use App\Controllers\BaseController;

class ControllerSaveScore extends BaseController
{
    protected $db; // Declare the $db property
    protected $personnelDb;

    public function __construct()
    {
        $this->db = \Config\Database::connect(); // Initialize the database connection
        $this->personnelDb = \Config\Database::connect('personnel');
        $this->session = \Config\Services::session(); // Initialize the session service
    }

    public function normal()
    {
        $data = [];
        $data['title'] = "หน้าบันทึกผลการเรียนหลัก";

        // Get current school year
        $schoolyear = $this->db->table('tb_schoolyear')->get()->getRow();
        $currentSchoolYear = $schoolyear ? $schoolyear->schyear_year : null;

        // Get onoff status
        $data['onoff'] = $this->db->table('tb_register_onoff')->where('onoff_id', 6)->get()->getResult();

        // Get check_subject
        $loginId = $this->session->get('person_id'); 

        if ($loginId && $currentSchoolYear) {
            $data['check_subject'] = $this->db->table('tb_register')
                ->select('
                    tb_register.SubjectID,
                    tb_register.RegisterYear,
                    GROUP_CONCAT(DISTINCT tb_register.RegisterClass ORDER BY tb_register.RegisterClass SEPARATOR ", ") as RegisterClasses,
                    tb_register.TeacherID,
                    tb_subjects.SubjectName,
                    tb_subjects.SubjectCode,
                    tb_subjects.SubjectID,
                    tb_subjects.SubjectUnit,
                    tb_subjects.SubjectHour
                ')
                ->join('tb_subjects', 'tb_subjects.SubjectID = tb_register.SubjectID')
                ->where('tb_register.TeacherID', $loginId)
                ->where('tb_register.RegisterYear', $currentSchoolYear)
                ->where('tb_subjects.SubjectYear', $currentSchoolYear)
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
            $sub_checkroom = explode('-', $room);
            $sub_room_class = 'ม.' . $sub_checkroom[0] . '/' . $sub_checkroom[1];
            $query->where('tb_students.StudentClass', $sub_room_class);
        }

        $data['check_student'] = $query->orderBy('tb_students.StudentClass', 'ASC')
                                     ->orderBy('tb_students.StudentNumber', 'ASC')
                                     ->get()->getResult();

        $check_idSubject = $this->db->table('tb_subjects')->where('SubjectID', $subject)->where('SubjectYear', $RegisterYear)->get()->getRow();
        if($check_idSubject){
            $data['set_score'] = $this->db->table('tb_register_score')->where('regscore_subjectID', $check_idSubject->SubjectID)->get()->getResult();
        } else {
            $data['set_score'] = [];
        }
        
        $data['onoff_savescore'] = $this->db->table('tb_register_onoff')->where('onoff_id >=', 2)->where('onoff_id <=', 5)->get()->getResult();
        $data['uri'] = service('uri');

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
        
        if ($key == "form_insert_score") {
            for ($i = 0; $i <= 3; $i++) {
                $data = [
                    'regscore_subjectID' => $subjectID,
                    'regscore_namework' => $this->request->getPost($list[$i]),
                    'regscore_score' => $this->request->getPost($score_fields[$i])
                ];
                $this->db->table('tb_register_score')->insert($data);
            }
            return $this->response->setJSON(['status' => 'success', 'message' => 'Insert successful']);
        } elseif ($key == "form_update_score") {
            for ($i = 0; $i <= 3; $i++) {
                $data = [
                    'regscore_score' => $this->request->getPost($score_fields[$i])
                ];
                $where = [
                    'regscore_namework' => $this->request->getPost($list[$i]),
                    'regscore_subjectID' => $subjectID
                ];
                $this->db->table('tb_register_score')->update($data, $where);
            }
            return $this->response->setJSON(['status' => 'success', 'message' => 'Update successful']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid key']);
    }

    public function editScore(){
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }
        $subjectId = $this->request->getPost('subid');
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
    
       if (file_exists(SHARED_LIB_PATH . '/mpdf/vendor/autoload.php')) {
            require SHARED_LIB_PATH . '/mpdf/vendor/autoload.php';
        } else {
            $path = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
            require $path . '/librarie_skj/mpdf/vendor/autoload.php';
        }
		//require SHARED_LIB_PATH . '/mpdf/vendor/autoload.php';
        // The manual require is removed. Composer handles autoloading.
        $mpdf = new \Mpdf\Mpdf([
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

        // Fetch teacher data for report
        $data['teacher_data'] = $this->personnelDb->table('tb_personnel')
            ->select('pers_prefix, pers_firstname, pers_lastname')
            ->where('pers_id', $loginId)
            ->get()->getRow();

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
            $data['re_room'] = $subject->SubjectClass;
            $data['re_teacher'] = "";

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

            foreach ($levels as $key => $level) {
                $data['check_student1'] = $baseStudentQuery($level->StudentClass);
                $data['re_room'] = $level->StudentClass;

                if ($key == 0) {
                    $data['check_student'] = $data['check_student1'];
                    $data['test'] = $reportRegisterYear;
                    $reportFront = view('teacher/register/SaveScore/report/ReportFront', $data);
                    $mpdf->WriteHTML($reportFront);
                }

                $mpdf->AddPage();
                $reportSummary = view('teacher/register/SaveScore/report/ReportSummary', $data);
                $mpdf->WriteHTML($reportSummary);
            }
        } else {
            $data['CheckPrint'] = "";
            $data['re_room'] = $selectPrint;

            $sub_Year = explode("/", $reportRegisterYear);
            $sub_room = explode(".", $selectPrint);
            if (isset($sub_Year[1]) && isset($sub_room[1])) {
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
                    ->where('Reg_Year', $sub_Year[1])
                    ->where('Reg_Class', $sub_room[1])
                    ->get()->getResult();
            } else {
                $data['re_teacher'] = [];
            }

            $data['check_student'] = $baseStudentQuery($selectPrint);
            $data['test'] = $reportRegisterYear;

            $reportFront = view('teacher/register/SaveScore/report/ReportFront', $data);
            $mpdf->WriteHTML($reportFront);

            $mpdf->AddPage();
            $reportSummary = view('teacher/register/SaveScore/report/ReportSummary', $data);
            $mpdf->WriteHTML($reportSummary);
        }

        $this->response->setHeader('Content-Type', 'application/pdf');
        return $mpdf->Output('filename.pdf', \Mpdf\Output\Destination::INLINE);
    }

}