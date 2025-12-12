<?php

namespace App\Controllers\Assessment;

use App\Controllers\BaseController;

class ControllerSaveScoreRepeat extends BaseController
{
    protected $db; // Declare the $db property
    protected $personnelDb;

    public function __construct()
    {
        $this->db = \Config\Database::connect(); // Initialize the database connection
        $this->personnelDb = \Config\Database::connect('personnel');
        $this->session = \Config\Services::session(); // Initialize the session service
        
        // Check if user is logged in
        if (!$this->session->get('isLoggedIn')) {
            // Redirect to login page if not logged in
            header('Location: ' . base_url('login'));
            exit();
        }
    }

    public function normal()
    {
        $data['title']  = "หน้าหลักบันทึกผลการเรียน (ซ้ำ)";
        $data['teacher'] = $this->personnelDb->table('tb_personnel')->select('pers_id,pers_img')->where('pers_id',$this->session->get('person_id'))->get()->getResult();
        $data['OnOff'] = $this->db->table('tb_send_plan_setup')->select('*')->get()->getResult();
        $register_onoff = $this->db->table('tb_register_onoff')->select('*')->where('onoff_id',7)->get()->getResult();
        $data['check_subject'] = $this->db->table('tb_register')
                                ->select('
                                    tb_register.SubjectID,
                                    tb_register.RegisterYear,
                                    tb_register.RegisterClass,
                                    tb_register.RepeatTeacher,
                                    tb_register.Grade_Type,
                                    tb_subjects.SubjectName,
                                    tb_subjects.SubjectCode,
                                    tb_subjects.SubjectID,
                                    tb_subjects.SubjectUnit,
                                    tb_subjects.SubjectHour,
                                    tb_register.RepeatYear
                                ')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                //->where('TeacherID',$this->session->get('person_id'))
                                //->where('tb_register.Grade_Type !=',"")
                                ->where('tb_register.RepeatYear',$register_onoff[0]->onoff_year)
                                ->where('tb_register.Grade_Type',$register_onoff[0]->onoff_detail)
                                ->where('tb_register.RepeatTeacher',$this->session->get('person_id'))
                                //->where('tb_subjects.SubjectYear',$register_onoff[0]->onoff_year)
                                //->where('tb_register.RegisterYear',$register_onoff[0]->onoff_year)
                                ->groupBy('tb_register.SubjectID')
                                ->groupBy('tb_register.RegisterClass')
                                ->groupBy('tb_register.RegisterYear')
                                ->groupBy('tb_register.RepeatTeacher')
                                ->groupBy('tb_register.Grade_Type')
                                ->groupBy('tb_register.RepeatYear')   
                                //->groupBy('tb_subjects.SubjectName')
                                //->groupBy('tb_register.RegisterYear')
                                ->orderBy('tb_register.RegisterYear','ASC')
                                ->get()->getResult();
        $data['onoff'] = $this->db->table('tb_register_onoff')->where('onoff_id',7)->get()->getResult(); 
        
        // Debug: Check session data
        if (empty($this->session->get('person_id'))) {
            log_message('error', 'Session person_id is empty. Full session data: ' . json_encode($this->session->get()));
            // Redirect to login if session is empty
            return redirect()->to('login')->with('error', 'กรุณาเข้าสู่ระบบใหม่อีกครั้ง');
        }
        
        return view('teacher/register/LearnRepeat/LearnRepeatMain', $data);
    }

    public function saveScoreRepeatAdd($subject, $room = 'all')
    {
        $data['title']  = "หน้าบันทึกผลการเรียน (ซ้ำ)";
        $data['teacher'] = $this->personnelDb->table('tb_personnel')->select('pers_id,pers_prefix,pers_firstname,pers_lastname,pers_img')->where('pers_id',$this->session->get('person_id'))->get()->getResult();
        $data['OnOff'] = $this->db->table('tb_send_plan_setup')->select('*')->get()->getResult();
        $data['onoff'] = $this->db->table('tb_register_onoff')->where('onoff_id',7)->get()->getResult();
       
        // Add a check here
        $onoff_detail = '';
        $onoff_year = '';
        if (!empty($data['onoff']) && isset($data['onoff'][0])) {
            $onoff_detail = $data['onoff'][0]->onoff_detail;
            $onoff_year = $data['onoff'][0]->onoff_year;
        }
        
        $data['check_room'] = $this->db->table('tb_register')
                                ->select('
                                    tb_students.StudentClass,
                                    tb_register.RegisterYear
                                ')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('tb_register.RepeatTeacher',$this->session->get('person_id'))
                                ->where('tb_register.RepeatYear',$onoff_year)
                                ->where('tb_register.SubjectID',($subject))
                                //->where('tb_students.StudentClass','ม.6/3')
                                ->orderBy('tb_students.StudentClass','ASC')
                                ->groupBy('tb_students.StudentClass')
                                ->groupBy('tb_register.RegisterYear')
                                ->get()->getResult();
        
      
        // if($room == "all"){  
        $builder = $this->db->table('tb_register')
                                ->select('
                                    tb_register.SubjectID,
                                    tb_register.RegisterYear,
                                    tb_register.RegisterClass,
                                    tb_register.Grade,
                                    tb_register.Score100,
                                    tb_register.RepeatTeacher,
                                    tb_subjects.SubjectName,
                                    tb_subjects.SubjectCode,
                                    tb_register.StudyTime,
                                    tb_subjects.SubjectID,
                                    tb_subjects.SubjectUnit,
                                    tb_subjects.SubjectHour,
                                    tb_students.StudentID,
                                    tb_students.StudentPrefix,
                                    tb_students.StudentFirstName,
                                    tb_students.StudentLastName,
                                    tb_students.StudentNumber,
                                    tb_students.StudentClass,
                                    tb_students.StudentCode,
                                    tb_students.StudentStatus,
                                    tb_students.StudentBehavior,
                                    tb_register.Grade_Type,
                                    tb_register.RepeatStatus,
                                    tb_register.RepeatYear
                                ')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID','LEFT')
                                ->where('tb_register.RepeatTeacher',$this->session->get('person_id'))
                                ->where('tb_register.RepeatYear',$onoff_year)
                                ->where('tb_register.SubjectID',($subject))
                                ->where('tb_students.StudentBehavior !=','จำหน่าย')                                
                                ->where('tb_register.Grade_Type',$onoff_detail)
                                ->orderBy('tb_students.StudentClass','ASC')
                                ->orderBy('tb_students.StudentNumber','ASC');

        if ($room != "all") {
             $builder->where('tb_students.StudentClass', $room);
        }

        $data['check_student'] = $builder->get()->getResult();

        //print_r($data['check_student']);exit();

                                $check_idSubject = $this->db->table('tb_subjects')->where('SubjectID', $subject)->get()->getRow();
                                if($check_idSubject){
                                    $data['set_score'] = $this->db->table('tb_register_score')->where('regscore_subjectID', $check_idSubject->SubjectID)->get()->getResult();
                                } else {
                                    $data['set_score'] = [];
                                }
        $data['onoff_savescore'] = $this->db->table('tb_register_onoff')->where('onoff_id >=', 2)->where('onoff_id <=', 5)->get()->getResult();

                               return view('teacher/register/LearnRepeat/LearnRepeatAdd', $data);
    }
    


    // AJAX Methods ported from ConTeacherRegister
    
    public function check_grade($sum) {
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
                $this->db->table('skjacth_academic.tb_register_score')->insert($data);
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
                $this->db->table('skjacth_academic.tb_register_score')->update($data, $where);
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
        $edit_score = $this->db->table('skjacth_academic.tb_register_score')->where('regscore_subjectID', $subjectId)->get()->getResult();
        
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

         $CheckRepeat = $this->db->table('tb_register_onoff')->select('onoff_detail,onoff_year')->where('onoff_id',7)->get()->getResult();
        $studentIDs = $this->request->getPost('StudentID');
        $timeNum = $this->request->getPost('TimeNum');
        $study_times = $this->request->getPost('study_time');

        foreach ($studentIDs as $num => $studentID) {
            $scores = $this->request->getPost($studentID); // Array of scores for this student
            $study_time = $study_times[$num];
            $grade = '';
            $RepeatStatus = ''; // Initialize
            $Grade_Type = ''; // Initialize
            $RepeatYear = ''; // Initialize

            if ((($timeNum * 80) / 100) > $study_time) {
                $grade = "มส";
                $Grade_Type = $CheckRepeat[0]->onoff_detail;
                $RepeatStatus = "ไม่ผ่าน";
                $RepeatYear = $CheckRepeat[0]->onoff_year;
            } else {
                if (is_array($scores) && in_array("ร", $scores)) {
                    $grade = "ร";
                    $Grade_Type = $CheckRepeat[0]->onoff_detail;
                    $RepeatStatus = "ไม่ผ่าน";
                    $RepeatYear = $CheckRepeat[0]->onoff_year;
                } else {
                    $grade = $this->check_grade(array_sum($scores));
                    if($grade > 0){
                            $RepeatStatus = "ผ่าน";
                            $Grade_Type = $CheckRepeat[0]->onoff_detail;
                            $RepeatYear = $CheckRepeat[0]->onoff_year;
                        }else{
                            $Grade_Type = $CheckRepeat[0]->onoff_detail;
                            $RepeatStatus = "ไม่ผ่าน";
                            $RepeatYear = $CheckRepeat[0]->onoff_year;
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
                'Grade_UpdateTime' => date('Y-m-d H:i:s'),
                'Grade_Type' => $Grade_Type, // Add this
                'RepeatStatus' => $RepeatStatus, // Add this
                'RepeatYear' => $RepeatYear // Add this
            ];

            $builder = $this->db->table('skjacth_academic.tb_register');
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
}

    public function autosaveScore() {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }
        $CheckRepeat = $this->db->table('tb_register_onoff')->select('onoff_detail,onoff_year')->where('onoff_id',7)->get()->getResult();
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
        $RepeatStatus = ''; // Initialize
        $Grade_Type = ''; // Initialize
        $RepeatYear = ''; // Initialize

        if ((($timeNum * 80) / 100) > $study_time) {
            $grade = "มส";
            $Grade_Type = $CheckRepeat[0]->onoff_detail; // Set Grade_Type for "มส"
            $RepeatStatus = "ไม่ผ่าน"; // Set RepeatStatus for "มส"
            $RepeatYear = $CheckRepeat[0]->onoff_year; // Set RepeatYear for "มส"
        } else {
            if (is_array($scores) && in_array("ร", $scores)) {
                $grade = "ร";
                $Grade_Type = $CheckRepeat[0]->onoff_detail; // Set Grade_Type for "ร"
                $RepeatStatus = "ไม่ผ่าน"; // Set RepeatStatus for "ร"
                $RepeatYear = $CheckRepeat[0]->onoff_year; // Set RepeatYear for "ร"
            } else {
                $numeric_scores = array_filter($scores, 'is_numeric');
                $grade = $this->check_grade(array_sum($numeric_scores));
                if($grade > 0){
                        $RepeatStatus = "ผ่าน";
                        $Grade_Type = $CheckRepeat[0]->onoff_detail;
                        $RepeatYear = $CheckRepeat[0]->onoff_year;
                    }else{
                        $Grade_Type = $CheckRepeat[0]->onoff_detail;
                        $RepeatStatus = "ไม่ผ่าน";
                        $RepeatYear = $CheckRepeat[0]->onoff_year;
                        
                    }
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
            'Grade_UpdateTime' => date('Y-m-d H:i:s'),
            'Grade_Type' => $Grade_Type, // Add this
            'RepeatStatus' => $RepeatStatus, // Add this
            'RepeatYear' => $RepeatYear // Add this
        ];

        $builder = $this->db->table('skjacth_academic.tb_register');
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

        $check_room = $this->db->table('skjacth_academic.tb_register')
            ->select('skjacth_academic.tb_students.StudentClass')
            ->join('skjacth_academic.tb_subjects', 'skjacth_academic.tb_subjects.SubjectID = skjacth_academic.tb_register.SubjectID')
            ->join('skjacth_academic.tb_students', 'skjacth_academic.tb_students.StudentID = skjacth_academic.tb_register.StudentID')
            ->where('skjacth_academic.tb_register.TeacherID', $loginId)
            ->where('skjacth_academic.tb_register.RegisterYear', $this->request->getPost('report_yaer'))
            ->where('skjacth_academic.tb_register.SubjectID', $this->request->getPost('report_subject'))
            ->groupBy('skjacth_academic.tb_students.StudentClass')
            ->orderBy('skjacth_academic.tb_students.StudentClass', 'ASC')
            ->get()->getResult();

        return $this->response->setJSON($check_room);
    }

    public function ReportLearnRepeat()
    {
    
        if (file_exists(SHARED_LIB_PATH . '/mpdf/vendor/autoload.php')) {
            require SHARED_LIB_PATH . '/mpdf/vendor/autoload.php';
        } else {
            $path = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
            require $path . '/librarie_skj/mpdf/vendor/autoload.php';
        }
       // require SHARED_LIB_PATH . '/mpdf/vendor/autoload.php';
		
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
        $subject = $this->db->table('skjacth_academic.tb_subjects')
            ->where('SubjectYear', $reportRegisterYear)
            ->where('SubjectID', $reportSubjectID)
            ->get()->getRow();

        if (!$subject) {
            return "Error: Subject not found.";
        }
        $data['re_subjuct'] = [$subject];

        // Fetch score settings
        $data['set_score'] = $this->db->table('skjacth_academic.tb_register_score')
            ->where('regscore_subjectID', $subject->SubjectID)
            ->get()->getResult();
        // Fetch CheckRepeat data
        $data['CheckRepeat'] = $this->db->table('tb_register_onoff')->select('onoff_detail,onoff_year')->where('onoff_id',7)->get()->getResult();

        // Base query for student data
        $baseStudentQuery = function ($class = null) use ($loginId, $reportRegisterYear, $reportSubjectID) {
            $builder = $this->db->table('skjacth_academic.tb_register');
            $builder->select('
                    skjacth_academic.tb_register.SubjectID, skjacth_academic.tb_register.RegisterYear, skjacth_academic.tb_register.RegisterClass,
                    skjacth_academic.tb_register.Score100, skjacth_academic.tb_register.Grade, skjacth_academic.tb_register.TeacherID,
                    skjacth_academic.tb_register.StudyTime, skjacth_academic.tb_subjects.SubjectName, skjacth_academic.tb_subjects.SubjectCode,
                    skjacth_academic.tb_subjects.SubjectID, skjacth_academic.tb_subjects.SubjectUnit, skjacth_academic.tb_subjects.SubjectHour,
                    skjacth_academic.tb_students.StudentID, skjacth_academic.tb_students.StudentPrefix, skjacth_academic.tb_students.StudentFirstName,
                    skjacth_academic.tb_students.StudentLastName, skjacth_academic.tb_students.StudentNumber, skjacth_academic.tb_students.StudentClass,
                    skjacth_academic.tb_students.StudentCode, skjacth_academic.tb_students.StudentStatus, skjacth_academic.tb_students.StudentBehavior,
                    skjacth_academic.tb_register.Grade_Type,
                    skjacth_academic.tb_register.RepeatStatus
                ')
                ->join('skjacth_academic.tb_subjects', 'skjacth_academic.tb_subjects.SubjectID = skjacth_academic.tb_register.SubjectID')
                ->join('skjacth_academic.tb_students', 'skjacth_academic.tb_students.StudentID = skjacth_academic.tb_register.StudentID')
                ->where('skjacth_academic.tb_register.TeacherID', $loginId)
                ->where('skjacth_academic.tb_register.RegisterYear', $reportRegisterYear)
                ->where('skjacth_academic.tb_subjects.SubjectYear', $reportRegisterYear)
                ->where('skjacth_academic.tb_register.SubjectID', $reportSubjectID)
                ->orderBy('skjacth_academic.tb_students.StudentClass', 'ASC')
                ->orderBy('skjacth_academic.tb_students.StudentNumber', 'ASC');

            if ($class) {
                $builder->where('skjacth_academic.tb_students.StudentClass', $class);
            }

            return $builder->get()->getResult();
        };



        if ($selectPrint == "all") {
            $data['CheckPrint'] = "all";
            $data['re_room'] = $subject->SubjectClass;
            $data['re_teacher'] = "";

            $levels = $this->db->table('skjacth_academic.tb_register')
                ->select('skjacth_academic.tb_students.StudentClass')
                ->join('skjacth_academic.tb_subjects', 'skjacth_academic.tb_subjects.SubjectID = skjacth_academic.tb_register.SubjectID')
                ->join('skjacth_academic.tb_students', 'skjacth_academic.tb_students.StudentID = skjacth_academic.tb_register.StudentID')
                ->where('skjacth_academic.tb_register.TeacherID', $loginId)
                ->where('skjacth_academic.tb_register.RegisterYear', $reportRegisterYear)
                ->where('skjacth_academic.tb_subjects.SubjectYear', $reportRegisterYear)
                ->where('skjacth_academic.tb_register.SubjectID', $reportSubjectID)
                ->groupBy('skjacth_academic.tb_students.StudentClass')
                ->orderBy('skjacth_academic.tb_students.StudentClass', 'ASC')
                ->get()->getResult();

            foreach ($levels as $key => $level) {
                $data['check_student1'] = $baseStudentQuery($level->StudentClass);
                $data['re_room'] = $level->StudentClass;

                if ($key == 0) {
                    $data['check_student'] = $data['check_student1'];
                    $data['test'] = $reportRegisterYear;
                    $reportFront = view('teacher/register/LearnRepeat/report/ReportLearnRepeatFront', $data);
                    $mpdf->WriteHTML($reportFront);
                }

                $mpdf->AddPage();
                $reportSummary = view('teacher/register/LearnRepeat/report/ReportLearnRepeatSummary', $data);
                $mpdf->WriteHTML($reportSummary);
            }
        } else {
            $data['CheckPrint'] = "";
            $data['re_room'] = $selectPrint;

            $sub_Year = explode("/", $reportRegisterYear);
            $sub_room = explode(".", $selectPrint);
            if (isset($sub_Year[1]) && isset($sub_room[1])) {
                 $data['re_teacher'] = $this->db->table('skjacth_academic.tb_regclass')
                    ->select('
                        skjacth_personnel.tb_personnel.pers_id,
                        skjacth_academic.tb_regclass.Reg_Class,
                        skjacth_academic.tb_regclass.Reg_Year,
                        skjacth_personnel.tb_personnel.pers_prefix,
                        skjacth_personnel.tb_personnel.pers_firstname,
                        skjacth_personnel.tb_personnel.pers_lastname
                    ')
                    ->join('skjacth_personnel.tb_personnel', 'skjacth_personnel.tb_personnel.pers_id = skjacth_academic.tb_regclass.class_teacher', 'left')
                    ->where('Reg_Year', $sub_Year[1])
                    ->where('Reg_Class', $sub_room[1])
                    ->get()->getResult();
            } else {
                $data['re_teacher'] = [];
            }

            $data['check_student'] = $baseStudentQuery($selectPrint);
            $data['test'] = $reportRegisterYear;
            $data['check_student1'] = $data['check_student']; // Ensure check_student1 is set for summary report

            $reportFront = view('teacher/register/LearnRepeat/report/ReportLearnRepeatFront', $data);
            $mpdf->WriteHTML($reportFront);

            $mpdf->AddPage();
            $reportSummary = view('teacher/register/LearnRepeat/report/ReportLearnRepeatSummary', $data);
            $mpdf->WriteHTML($reportSummary);
        }

        $this->response->setHeader('Content-Type', 'application/pdf');
        return $mpdf->Output('filename.pdf', \Mpdf\Output\Destination::INLINE);
    }

}