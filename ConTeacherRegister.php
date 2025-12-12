<?php
error_reporting(-1);
ini_set('display_errors', 1);

defined('BASEPATH') OR exit('No direct script access allowed');

class ConTeacherRegister extends CI_Controller {
	public function __construct() {
		parent::__construct();
		
		if (empty($this->session->userdata('fullname')) && !$this->session->userdata('status') == 'admin') {      
			redirect('welcome','refresh');
		}
        $this->DBpersonnel = $this->load->database('personnel', TRUE); 
        $this->DBaffairs = $this->load->database('affairs', TRUE);
        $this->CheckHomeVisitManager = $this->DBaffairs->select('homevisit_set_id,homevisit_set_manager')->where('homevisit_set_id',1)->get('tb_homevisit_setting')->first_row();
    }

    function check_grade($sum) {
        if (($sum > 100) || ($sum < 0)) {
             $grade = "ไม่สามารถคิดเกรดได้ คะแนนเกิน";
        } else if (($sum >= 79.5) && ($sum <= 100)) {
             $grade = 4;
        } else if (($sum >= 74.5) && ($sum <= 79.4)) {
             $grade = 3.5;
        } else if (($sum >= 69.5) && ($sum <= 74.4)) {
             $grade = 3;
        } else if (($sum >= 64.5) && ($sum <= 69.4)) {
             $grade = 2.5;
        } else if (($sum >= 59.5) && ($sum <= 64.4)) {
             $grade = 2;
        } else if (($sum >= 54.5) && ($sum <= 59.4)) {
             $grade = 1.5;
        } else if (($sum >= 49.5) && ($sum <= 54.4)) {
             $grade = 1;
        } else if ($sum <= 49.4) {
             $grade = 0;
        }
        return $grade;
    }

    public function SaveScoreMain(){      
        $data['title']  = "หน้าบันทึกผลการเรียนหลัก";
        $data['teacher'] = $this->DBpersonnel->select('pers_id,pers_img')->where('pers_id',$this->session->userdata('login_id'))->get('tb_personnel')->result();
        $data['OnOff'] = $this->db->select('*')->get('tb_send_plan_setup')->result();
        $schoolyear = $this->db->select('*')->get('tb_schoolyear')->result();
        $data['check_subject'] = $this->db->select('
                                    tb_register.SubjectID,
                                    tb_register.RegisterYear,
                                    tb_register.RegisterClass,
                                    tb_register.TeacherID,
                                    tb_subjects.SubjectName,
                                    tb_subjects.SubjectCode,
                                    tb_subjects.SubjectID,
                                    tb_subjects.SubjectUnit,
                                    tb_subjects.SubjectHour
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->where('TeacherID',$this->session->userdata('login_id'))
                                ->where('tb_register.RegisterYear',$schoolyear[0]->schyear_year)
                                ->where('tb_subjects.SubjectYear',$schoolyear[0]->schyear_year)
                                ->group_by('tb_register.SubjectID')
                                ->order_by('tb_register.RegisterClass')
                                ->get()->result();
        $data['onoff'] = $this->db->where('onoff_id',6)->get('tb_register_onoff')->result();                        
        //echo '<pre>'; print_r($data['onoff']);exit();
        
        $this->load->view('teacher/layout/header_teacher.php',$data);
        $this->load->view('teacher/layout/navbar_teaher.php');
        $this->load->view('teacher/register/SaveScore/SaveScoreMain.php');
        $this->load->view('teacher/layout/footer_teacher.php');        
    }

    public function SaveScoreAdd($term,$yaer,$subject,$room){      
        $data['title']  = "บันทึกผลการเรียน";
        $data['teacher'] = $this->DBpersonnel->select('pers_id,pers_img')->where('pers_id',$this->session->userdata('login_id'))->get('tb_personnel')->result();
        $data['OnOff'] = $this->db->select('*')->get('tb_send_plan_setup')->result();
       
        
        $data['check_room'] = $this->db->select('
                                    tb_students.StudentClass,
                                    tb_register.RegisterYear
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('TeacherID',$this->session->userdata('login_id'))
                                ->where('RegisterYear',$term.'/'.$yaer)
                                ->where('tb_register.SubjectID',($subject))
                                // ->where('tb_students.StudentClass','ม.6/3')
                                ->order_by('tb_students.StudentClass','ASC')
                                ->group_by('tb_students.StudentClass')
                                ->get()->result();
        
      
        if($room == "all"){  
        $data['check_student'] = $this->db->select('
                                    tb_register.SubjectID,
                                    tb_register.RegisterYear,
                                    tb_register.RegisterClass,
                                    tb_register.Score100,
                                    tb_register.TeacherID,
                                    tb_subjects.SubjectName,
                                    tb_subjects.SubjectCode,
                                    tb_register.StudyTime,
                                    tb_subjects.SubjectID,
                                    tb_subjects.SubjectUnit,
                                    tb_subjects.SubjectHour,
                                    tb_subjects.SubjectYear,
                                    tb_students.StudentID,
                                    tb_students.StudentPrefix,
                                    tb_students.StudentFirstName,
                                    tb_students.StudentLastName,
                                    tb_students.StudentNumber,
                                    tb_students.StudentClass,
                                    tb_students.StudentCode,
                                    tb_students.StudentStatus,
                                    tb_students.StudentBehavior,
                                    tb_register.Grade_Type
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('tb_register.TeacherID',$this->session->userdata('login_id'))
                                ->where('tb_register.RegisterYear',$term.'/'.$yaer)
                                ->where('tb_subjects.SubjectYear',$term.'/'.$yaer)
                                ->where('tb_register.SubjectID',($subject))
                                ->order_by('tb_students.StudentClass','ASC')
                                ->order_by('tb_students.StudentNumber','ASC')
                                ->get()->result();
       
                               
       
        }else{
            $sub_checkroom = explode('-',$room);
            $sub_room = $sub_checkroom[0].'/'.$sub_checkroom[1];
            $data['check_student'] = $this->db->select('
                                    tb_register.SubjectID,
                                    tb_register.RegisterYear,
                                    tb_register.RegisterClass,
                                    tb_register.Score100,
                                    tb_register.TeacherID,
                                    tb_register.StudyTime,
                                    tb_subjects.SubjectName,
                                    tb_subjects.SubjectCode,
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
                                    tb_register.Grade_Type
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('TeacherID',$this->session->userdata('login_id'))
                                ->where('tb_register.RegisterYear',$term.'/'.$yaer)
                                ->where('tb_subjects.SubjectYear',$term.'/'.$yaer)
                                ->where('tb_register.SubjectID',($subject))
                                ->where('tb_students.StudentClass','ม.'.$sub_room)
                                ->order_by('tb_students.StudentClass','ASC')
                                ->order_by('tb_students.StudentNumber','ASC')
                                ->get()->result();

        }

        $check_idSubject = $this->db->where('SubjectID',($subject))->where('SubjectYear',$term.'/'.$yaer)->get('tb_subjects')->row();
        //echo '<pre>';print_r($subject); exit();
        $data['set_score'] = $this->db->where('regscore_subjectID',$check_idSubject->SubjectID)->get('tb_register_score')->result();
        $data['onoff_savescore'] = $this->db->where('onoff_id >=',2)->where('onoff_id <=',5)->get('tb_register_onoff')->result();   
       
        
        $this->load->view('teacher/layout/header_teacher.php',$data);
        $this->load->view('teacher/layout/navbar_teaher.php');
        $this->load->view('teacher/register/SaveScore/SaveScoreAdd.php');
        $this->load->view('teacher/layout/footer_teacher.php');        
    }

    public function insert_score(){ 

        $TimeNum = $this->input->post('TimeNum');

        foreach ($this->input->post('StudentID') as $num => $value) {
           
            $study_time = $this->input->post('study_time');
            
            if((($TimeNum*80)/100) > $study_time[$num]){
                $Grade = "มส";
            }else{
                if(in_array("ร",$this->input->post($value))){
                    $Grade = "ร";
                }else{
                    $Grade = $this->check_grade(array_sum($this->input->post($value)));
                }
            }
            
            // The unique key for the record
            $key = array(
                'StudentID' => $value,
                'SubjectID' => $this->input->post('SubjectID'), 
                'RegisterYear' => $this->input->post('RegisterYear')
            );

            // The data to be saved
            $data = array(
                'Score100' => implode("|",$this->input->post($value)),
                'Grade'  => $Grade,
                'StudyTime' => $study_time[$num],
                'Grade_UpdateTime' => date('Y-m-d H:i:s')
            );

            // Check if a record already exists
            $this->db->where($key);
            $query = $this->db->get('tb_register');

            if ($query->num_rows() > 0) {
                // If it exists, update it
                $this->db->update('tb_register', $data, $key);
            } else {
                // If it doesn't exist, insert a new record
                $this->db->insert('tb_register', array_merge($key, $data));
            }
        }
        echo 1;
    }

    public function autosave_score() {
        // Get data from POST request
        $studentID = $this->input->post('StudentID');
        $subjectID = $this->input->post('SubjectID');
        $registerYear = $this->input->post('RegisterYear');
        $study_time = $this->input->post('study_time');
        $scores = $this->input->post('scores'); // Expecting an array of scores
        $timeNum = $this->input->post('TimeNum');
    
        // Basic validation
        if (empty($studentID) || empty($subjectID) || empty($registerYear)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required identifiers.']);
            return;
        }
    
        // Grade calculation logic (same as in insert_score)
        if ((($timeNum * 80) / 100) > $study_time) {
            $grade = "มส";
        } else {
            if (is_array($scores) && in_array("ร", $scores)) {
                $grade = "ร";
            } else {
                $grade = $this->check_grade(array_sum($scores));
            }
        }
    
        // The unique key for the record
        $key = array(
            'StudentID' => $studentID,
            'SubjectID' => $subjectID,
            'RegisterYear' => $registerYear
        );
    
        // The data to be saved
        $data = array(
            'Score100' => implode("|", $scores),
            'Grade' => $grade,
            'StudyTime' => $study_time,
            'Grade_UpdateTime' => date('Y-m-d H:i:s')
        );
    
        // UPSERT logic
        $this->db->where($key);
        $query = $this->db->get('tb_register');
    
        if ($query->num_rows() > 0) {
            $this->db->update('tb_register', $data, $key);
        } else {
            $this->db->insert('tb_register', array_merge($key, $data));
        }
    
        // Check for errors and send response
        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Score saved.']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Score up-to-date.']);
        }
    }

    public function insert_score_repeat(){ 

        $CheckRepeat = $this->db->select('onoff_detail,onoff_year')->where('onoff_id',7)->get('tb_register_onoff')->result();
        $TimeNum = $this->input->post('TimeNum');
        $Grade_Type = '';
        //print_r($this->input->post());exit();
        foreach ($this->input->post('StudentID') as $num => $value) {
           //print_r($this->input->post('TimeNum'));
            // print_r($this->input->post('SubjectCode'));
            $study_time = $this->input->post('study_time');
            // print_r(); exit();
            if((($TimeNum*80)/100) > $study_time[$num]){
                $Grade = "มส";
                $Grade_Type = $CheckRepeat[0]->onoff_detail;
                $RepeatStatus = "ไม่ผ่าน";
                $RepeatYear = $CheckRepeat[0]->onoff_year;
            }else{
                if(in_array("ร",$this->input->post($value))){
                    $Grade = "ร";
                    $Grade_Type = $CheckRepeat[0]->onoff_detail;
                    $RepeatStatus = "ไม่ผ่าน";
                    $RepeatYear = $CheckRepeat[0]->onoff_year;
                }else{
                    $GradeCheck = $this->check_grade(array_sum($this->input->post($value)));
                    if($GradeCheck > 0){
                        $Grade = $GradeCheck;
                        $RepeatStatus = "ผ่าน";
                        $Grade_Type = $CheckRepeat[0]->onoff_detail;
                        $RepeatYear = $CheckRepeat[0]->onoff_year;
                    }else{
                        $Grade = 0;
                        $Grade_Type = $CheckRepeat[0]->onoff_detail;
                        $RepeatStatus = "ไม่ผ่าน";
                        $RepeatYear = $CheckRepeat[0]->onoff_year;
                        // กำลังจะดึงข้อมูลเรียนซ้ำมา
                    }
                }
            }  

            $key = array('StudentID' => $value,'SubjectID' => $this->input->post('SubjectID'));
           $data = array('Score100' => implode("|",$this->input->post($value)),'Grade'  => $Grade,'StudyTime' => $study_time[$num],'Grade_UpdateTime' => date('Y-m-d H:i:s'),'Grade_Type' => $Grade_Type,'RepeatStatus' => $RepeatStatus,'RepeatYear' => $RepeatYear,'RepeatConfirm' => $this->session->userdata('login_id'));
           //echo print_r($key);
           echo $this->db->update('tb_register',$data,$key);
        }
        
        
    }

    public function setting_score($key){      
       
        $list = array('before_middle','test_midterm','after_midterm','final_exam');
        $score = array('before_middle_score','test_midterm_score','after_midterm_score','final_exam_score');
        if($key == "form_insert_score"){
            
            for ($i=0; $i <= 3 ; $i++) { 
                $data = array('regscore_subjectID' => $this->input->post("regscore_subjectID"),
                'regscore_namework' => $this->input->post($list[$i]),
                'regscore_score' => $this->input->post($score[$i]) ); 
                $this->db->insert('tb_register_score',$data);           
            }
            echo 1;
        }elseif($key == "form_update_score"){
           
            for ($i=0; $i <= 3 ; $i++) { 
                $data = array(
                    'regscore_score' => $this->input->post($score[$i]) 
                ); 
                $uplist = array('regscore_namework' => $this->input->post($list[$i]),
                              'regscore_subjectID' =>$this->input->post("regscore_subjectID"));
                $this->db->update('tb_register_score',$data,$uplist);           
            }
            echo 1;
        }
       
    }
    
    public function edit_score(){  
        $edit_score = $this->db->where('regscore_subjectID',$this->input->post('subid'))->get('tb_register_score')->result();
        if($edit_score){
            echo json_encode($edit_score);
        }else{
            echo 0;
        }
        
    }

    public function checkroom_report(){
        $check_room = $this->db->select('
                                    tb_students.StudentClass,
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('TeacherID',$this->session->userdata('login_id'))
                                ->where('RegisterYear',$this->input->post('report_yaer'))
                                ->where('tb_register.SubjectID',$this->input->post('report_subject'))
                                // ->where('tb_students.StudentClass','ม.6/3')
                                ->order_by('tb_students.StudentClass','ASC')
                                ->group_by('tb_students.StudentClass')
                                ->get()->result();

        echo json_encode($check_room);                    

    }

    public function report_pt(){ 
        //$path = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
		require  '/librarie_skj/mpdf/vendor/autoload.php';
        
        $live_mpdf = new \Mpdf\Mpdf(
            array(
                'format' => 'A4',
                'mode' => 'utf-8',
                'default_font' => 'thsarabun',
                'default_font_size' => 16
            )
        );

        if($this->input->post('select_print') == "all"){
            $data['CheckPrint'] = "all";
            $data['re_subjuct'] = $this->db
                            ->where('SubjectYear',$this->input->post('report_RegisterYear'))
                            ->where('SubjectID',$this->input->post('report_SubjectID'))
                            ->get('tb_subjects')->result();
            $data['re_room'] = $data['re_subjuct'][0]->SubjectClass; 
            $data['re_teacher'] = "";
            $data['set_score'] = $this->db->where('regscore_subjectID',$data['re_subjuct'][0]->SubjectID)->get('tb_register_score')->result();

            $data['check_level'] = $this->db->select('
                                    tb_students.StudentClass
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('TeacherID',$this->session->userdata('login_id'))
                                ->where('RegisterYear',$this->input->post('report_RegisterYear'))
                                ->where('tb_subjects.SubjectYear',$this->input->post('report_RegisterYear'))
                                ->where('tb_register.SubjectID',$this->input->post('report_SubjectID'))
                                ->order_by('tb_students.StudentClass','ASC')
                                ->order_by('tb_students.StudentNumber','ASC')
                                ->group_by('tb_students.StudentClass')
                                ->get()->result();

                                

            foreach ($data['check_level'] as $key => $value_level) {

                $data['check_student'] = $this->db->select('
                                    tb_register.SubjectID,
                                    tb_register.RegisterYear,
                                    tb_register.RegisterClass,
                                    tb_register.Score100,
                                    tb_register.Grade,
                                    tb_register.TeacherID,
                                    tb_register.StudyTime,
                                    tb_subjects.SubjectName,
                                    tb_subjects.SubjectCode,
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
                                    tb_register.Grade_Type
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('TeacherID',$this->session->userdata('login_id'))
                                ->where('RegisterYear',$this->input->post('report_RegisterYear'))
                                ->where('tb_subjects.SubjectYear',$this->input->post('report_RegisterYear'))
                                ->where('tb_register.SubjectID',$this->input->post('report_SubjectID'))
                                ->order_by('tb_students.StudentClass','ASC')
                                ->order_by('tb_students.StudentNumber','ASC')
                                ->get()->result();

            $data['re_room'] = $value_level->StudentClass;
            $data['check_student1'] = $this->db->select('
                                    tb_register.SubjectID,
                                    tb_register.RegisterYear,
                                    tb_register.RegisterClass,
                                    tb_register.Score100,
                                    tb_register.Grade,
                                    tb_register.TeacherID,
                                    tb_register.StudyTime,
                                    tb_subjects.SubjectName,
                                    tb_subjects.SubjectCode,
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
                                    tb_register.Grade_Type
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('TeacherID',$this->session->userdata('login_id'))
                                ->where('StudentClass',$value_level->StudentClass)
                                ->where('RegisterYear',$this->input->post('report_RegisterYear'))
                                ->where('tb_subjects.SubjectYear',$this->input->post('report_RegisterYear'))
                                ->where('tb_register.SubjectID',$this->input->post('report_SubjectID'))
                                ->order_by('tb_students.StudentClass','ASC')
                                ->order_by('tb_students.StudentNumber','ASC')
                                ->get()->result();
 //echo "<pre>";print_r($data['check_student']);
         
                                if($key == 0){
                                    $data['test'] = $this->input->post('report_RegisterYear'); //true
                                    $ReportFront = $this->load->view('teacher/register/SaveScore/report/ReportFront',$data,true);        
                                    $live_mpdf->WriteHTML($ReportFront);  
                                }
                               
                                $live_mpdf->AddPage(); 

                                $ReportSummary = $this->load->view('teacher/register/SaveScore/report/ReportSummary',$data,true); 
                                $live_mpdf->WriteHTML($ReportSummary);
                                
           }

                                $live_mpdf->Output('filename.pdf', \Mpdf\Output\Destination::INLINE);
          
        }else{
            $data['CheckPrint'] = "";
             $data['re_subjuct'] = $this->db
                            ->where('SubjectYear',$this->input->post('report_RegisterYear'))
                            ->where('SubjectID',$this->input->post('report_SubjectID'))
                            ->get('tb_subjects')->result();
            //echo "<pre>";print_r($data['re_subjuct']); exit();
            $data['re_room'] = $this->input->post('select_print');
            $sub_room = explode(".",$this->input->post('select_print'));
            $sub_Year =  explode("/",$this->input->post('report_RegisterYear'));

            $data['re_teacher'] = $this->db->select('skjacth_personnel.tb_personnel.pers_id,
                                                    skjacth_academic.tb_regclass.Reg_Class,
                                                    skjacth_academic.tb_regclass.Reg_Year,
                                                    skjacth_personnel.tb_personnel.pers_prefix,
                                                    skjacth_personnel.tb_personnel.pers_firstname,
                                                    skjacth_personnel.tb_personnel.pers_lastname')
                                ->from('tb_regclass')
                                ->join('skjacth_personnel.tb_personnel','skjacth_personnel.tb_personnel.pers_id = skjacth_academic.tb_regclass.class_teacher','left')
                                ->where('Reg_Year',$sub_Year[1])
                                ->where('Reg_Class',$sub_room[1])
                                ->get()->result(); 
          

        $data['set_score'] = $this->db->where('regscore_subjectID',$data['re_subjuct'][0]->SubjectID)->get('tb_register_score')->result();
        
        $data['check_student'] = $this->db->select('
                                    tb_register.SubjectID,
                                    tb_register.RegisterYear,
                                    tb_register.RegisterClass,
                                    tb_register.Score100,
                                    tb_register.Grade,
                                    tb_register.TeacherID,
                                    tb_register.StudyTime,
                                    tb_subjects.SubjectName,
                                    tb_subjects.SubjectCode,
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
                                    tb_register.Grade_Type
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('TeacherID',$this->session->userdata('login_id'))
                                ->where('tb_register.RegisterYear',$this->input->post('report_RegisterYear'))
                                ->where('tb_subjects.SubjectYear',$this->input->post('report_RegisterYear'))
                                ->where('tb_register.SubjectID',$this->input->post('report_SubjectID'))
                                ->where('tb_students.StudentClass',$this->input->post('select_print'))
                                ->order_by('tb_students.StudentClass','ASC')
                                ->order_by('tb_students.StudentNumber','ASC')
                                ->get()->result();
           
                                $data['test'] = $this->input->post('report_RegisterYear'); //true
                                $ReportFront = $this->load->view('teacher/register/SaveScore/report/ReportFront',$data,true);        
                                $live_mpdf->WriteHTML($ReportFront);
                        
                                $live_mpdf->AddPage(); 
                                $ReportSummary = $this->load->view('teacher/register/SaveScore/report/ReportSummary',$data,true); 
                                $live_mpdf->WriteHTML($ReportSummary);
                                $live_mpdf->Output('filename.pdf', \Mpdf\Output\Destination::INLINE);
        }

       // echo '<pre>';print_r($data['check_student']); exit();

    }


    public function LearnRepeatMain(){
        $data['title']  = "หน้าหลักบันทึกผลการเรียน (ซ้ำ)";
        $data['teacher'] = $this->DBpersonnel->select('pers_id,pers_img')->where('pers_id',$this->session->userdata('login_id'))->get('tb_personnel')->result();
        $data['OnOff'] = $this->db->select('*')->get('tb_send_plan_setup')->result();
        $register_onoff = $this->db->select('*')->where('onoff_id',7)->get('tb_register_onoff')->result();
        $schoolyear = $this->db->select('*')->get('tb_schoolyear')->result();
        $data['check_subject'] = $this->db->select('
                                    tb_register.SubjectID,
                                    tb_register.RegisterYear,
                                    tb_register.RegisterClass,
                                    tb_register.TeacherID,
                                    tb_register.Grade_Type,
                                    tb_subjects.SubjectName,
                                    tb_subjects.SubjectCode,
                                    tb_subjects.SubjectID,
                                    tb_subjects.SubjectUnit,
                                    tb_subjects.SubjectHour,
                                    tb_register.RepeatYear
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                //->where('TeacherID',$this->session->userdata('login_id'))
                                //->where('tb_register.Grade_Type !=',"")
                                ->where('tb_register.RepeatYear',$register_onoff[0]->onoff_year)
                                ->where('tb_register.Grade_Type',$register_onoff[0]->onoff_detail)
                                ->where('tb_register.RepeatTeacher',$this->session->userdata('login_id'))
                                //->where('tb_subjects.SubjectYear',$register_onoff[0]->onoff_year)
                                //->where('tb_register.RegisterYear',$register_onoff[0]->onoff_year)
                                ->group_by('tb_register.SubjectID')   
                                //->group_by('tb_subjects.SubjectName')
                                //->group_by('tb_register.RegisterYear')
                                ->order_by('tb_register.RegisterYear','ASC')
                                ->get()->result();
        $data['onoff'] = $this->db->where('onoff_id',7)->get('tb_register_onoff')->result();                        
        //echo '<pre>'; print_r($data['check_subject']);exit();
        
        $this->load->view('teacher/layout/header_teacher.php',$data);
        $this->load->view('teacher/layout/navbar_teaher.php');
        $this->load->view('teacher/register/LearnRepeat/LearnRepeatMain.php');
        $this->load->view('teacher/layout/footer_teacher.php');   
    }

    public function LearnRepeatAdd($term,$yaer,$subject,$room){      
        $data['title']  = "หน้าบันทึกผลการเรียน (ซ้ำ)";
        $data['teacher'] = $this->DBpersonnel->select('pers_id,pers_img')->where('pers_id',$this->session->userdata('login_id'))->get('tb_personnel')->result();
        $data['OnOff'] = $this->db->select('*')->get('tb_send_plan_setup')->result();
        $data['onoff'] = $this->db->where('onoff_id',7)->get('tb_register_onoff')->result();
       
        
        $data['check_room'] = $this->db->select('
                                    tb_students.StudentClass,
                                    tb_register.RegisterYear
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('TeacherID',$this->session->userdata('login_id'))
                                ->where('RegisterYear',$term.'/'.$yaer)
                                ->where('tb_register.SubjectID',($subject))
                                //->where('tb_students.StudentClass','ม.6/3')
                                ->order_by('tb_students.StudentClass','ASC')
                                ->group_by('tb_students.StudentClass')
                                ->get()->result();
        
      
        if($room == "all"){  
        $data['check_student'] = $this->db->select('
                                    tb_register.SubjectID,
                                    tb_register.RegisterYear,
                                    tb_register.RegisterClass,
                                    tb_register.Grade,
                                    tb_register.Score100,
                                    tb_register.TeacherID,
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
                                    tb_register.Grade_Type
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID','LEFT')
                                //->where('TeacherID',$this->session->userdata('login_id'))
                                ->where('tb_register.RepeatTeacher',$this->session->userdata('login_id'))
                                //->where('tb_register.RegisterYear',$term.'/'.$yaer)
                                ->where('tb_subjects.SubjectYear',$term.'/'.$yaer)
                                ->where('tb_register.SubjectID',($subject))
                                ->where('tb_students.StudentBehavior !=','จำหน่าย')                                
                                ->where('tb_register.Grade_Type',$data['onoff'][0]->onoff_detail)
                                ->where('tb_register.RepeatYear',$data['onoff'][0]->onoff_year)
                                ->order_by('tb_students.StudentClass','ASC')
                                ->order_by('tb_students.StudentNumber','ASC')
                                ->get()->result();
                               //echo '<pre>'; print_r($data['check_student']); exit();
       
        }else{
            $sub_checkroom = explode('-',$room);
            $sub_room = $sub_checkroom[0].'/'.$sub_checkroom[1];
            $data['check_student'] = $this->db->select('
                                    tb_register.SubjectID,
                                    tb_register.RegisterYear,
                                    tb_register.RegisterClass,
                                    tb_register.Score100,
                                    tb_register.TeacherID,
                                    tb_register.StudyTime,
                                    tb_subjects.SubjectName,
                                    tb_subjects.SubjectCode,
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
                                    tb_students.StudentBehavior
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('TeacherID',$this->session->userdata('login_id'))
                                ->where('RegisterYear',$term.'/'.$yaer)
                                ->where('tb_register.SubjectID',($subject))
                                ->where('tb_students.StudentClass','ม.'.$sub_room)
                                ->where('tb_students.StudentBehavior !=','จำหน่าย')
                                ->order_by('tb_students.StudentClass','ASC')
                                ->order_by('tb_students.StudentNumber','ASC')
                                ->get()->result();

        }

        $check_idSubject = $this->db->where('SubjectID',($subject))->where('SubjectYear',$term.'/'.$yaer)->get('tb_subjects')->row();
      
        $data['set_score'] = $this->db->where('regscore_subjectID',$check_idSubject->SubjectID)->get('tb_register_score')->result();
        $data['onoff_savescore'] = $this->db->where('onoff_id >=',2)->where('onoff_id <=',5)->get('tb_register_onoff')->result();   
       
        
        $this->load->view('teacher/layout/header_teacher.php',$data);
        $this->load->view('teacher/layout/navbar_teaher.php');
        $this->load->view('teacher/register/LearnRepeat/LearnRepeatAdd.php');
        $this->load->view('teacher/layout/footer_teacher.php');        
    }

    public function ReportLearnRepeat(){
        
      
        $path = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
		require $path . '/librarie_skj/mpdf/vendor/autoload.php';
        
        $live_mpdf = new \Mpdf\Mpdf(
            array(
                'format' => 'A4',
                'mode' => 'utf-8',
                'default_font' => 'thsarabun',
                'default_font_size' => 16
            )
        );

        if($this->input->post('select_print') == "all"){
           
            $data['re_subjuct'] = $this->db
                            ->where('SubjectYear',$this->input->post('report_RegisterYear'))
                            ->where('SubjectID',$this->input->post('report_SubjectID'))
                            ->get('tb_subjects')->result();
            $data['CheckRepeat'] = $this->db->select('onoff_detail,onoff_year')->where('onoff_name','เรียนซ้ำ')->get('tb_register_onoff')->result();  
           
            $data['re_room'] = $data['re_subjuct'][0]->SubjectClass; 
            $data['re_teacher'] = "";
            $data['set_score'] = $this->db->where('regscore_subjectID',$data['re_subjuct'][0]->SubjectID)->get('tb_register_score')->result();

            $data['check_Level'] = $this->db->select('                                   
                                    tb_students.StudentClass
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('RepeatTeacher',$this->session->userdata('login_id'))
                                //->where('RegisterYear',$this->input->post('report_RegisterYear'))                                
                                ->where('tb_subjects.SubjectYear',$this->input->post('report_RegisterYear'))
                                ->where('tb_register.SubjectID',$this->input->post('report_SubjectID'))
                                ->where('tb_students.StudentBehavior !=','จำหน่าย')
                                ->order_by('tb_students.StudentClass','ASC')
                                ->order_by('tb_students.StudentNumber','ASC')
                                ->group_by('StudentClass')
                                ->get()->result();

            //echo '<pre>'; print_r($data['check_Level']);exit();    

            foreach ($data['check_Level'] as $key => $v_check_Level) {
       
            $data['check_student'] = $this->db->select('
                                    tb_register.SubjectID,
                                    tb_register.RegisterYear,
                                    tb_register.RegisterClass,
                                    tb_register.Score100,
                                    tb_register.Grade,
                                    tb_register.TeacherID,
                                    tb_register.StudyTime,
                                    tb_subjects.SubjectName,
                                    tb_subjects.SubjectCode,
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
                                    tb_register.Grade_Type
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('tb_register.RepeatTeacher',$this->session->userdata('login_id'))
                                //->where('RegisterYear',$this->input->post('report_RegisterYear'))                                
                                ->where('tb_subjects.SubjectYear',$this->input->post('report_RegisterYear'))
                                ->where('tb_register.SubjectID',$this->input->post('report_SubjectID'))
                                //->where('tb_students.StudentClass',$v_check_Level->StudentClass)
                                ->where('tb_students.StudentBehavior !=','จำหน่าย')
                                ->order_by('tb_students.StudentClass','ASC')
                                ->order_by('tb_students.StudentNumber','ASC')
                                ->get()->result();

                $data['check_student1'] = $this->db->select('
                                    tb_register.SubjectID,
                                    tb_register.RegisterYear,
                                    tb_register.RegisterClass,
                                    tb_register.Score100,
                                    tb_register.Grade,
                                    tb_register.TeacherID,
                                    tb_register.StudyTime,
                                    tb_subjects.SubjectName,
                                    tb_subjects.SubjectCode,
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
                                    tb_register.Grade_Type
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('tb_register.RepeatTeacher',$this->session->userdata('login_id'))
                                //->where('RegisterYear',$this->input->post('report_RegisterYear'))       
                                //->where('tb_register.RepeatYear',$this->input->post('report_RegisterYear'))                         
                                ->where('tb_subjects.SubjectYear',$this->input->post('report_RegisterYear'))
                                ->where('tb_register.SubjectID',$this->input->post('report_SubjectID'))
                                ->where('tb_students.StudentClass',$v_check_Level->StudentClass)
                                ->where('tb_students.StudentBehavior !=','จำหน่าย')
                                ->order_by('tb_students.StudentClass','ASC')
                                ->order_by('tb_students.StudentNumber','ASC')
                                ->get()->result();

                                //echo '<pre>'; print_r($data['check_student']);exit();    
          
           if($key == 0){
                $live_mpdf->SetTitle('รายงาน ปถ.05:เรียนซ้ำ');

                $data['test'] = $this->input->post('report_RegisterYear'); //true
                $ReportFront = $this->load->view('teacher/register/LearnRepeat/Report/ReportLearnRepeatFront',$data,true);        
                $live_mpdf->WriteHTML($ReportFront);
           }   
                $live_mpdf->AddPage(); 
                $ReportSummary = $this->load->view('teacher/register/LearnRepeat/Report/ReportLearnRepeatSummary',$data,true); 
                $live_mpdf->WriteHTML($ReportSummary);

            }
                $live_mpdf->Output('เรียนซ้ำ.pdf', \Mpdf\Output\Destination::INLINE);
        }else{
             $data['re_subjuct'] = $this->db
                            ->where('SubjectYear',$this->input->post('report_RegisterYear'))
                            ->where('SubjectID',$this->input->post('report_SubjectID'))
                            ->get('tb_subjects')->result();
            //echo "<pre>";print_r($data['re_subjuct']); exit();
            $data['re_room'] = $this->input->post('select_print');
            $sub_room = explode(".",$this->input->post('select_print'));
            $sub_Year =  explode("/",$this->input->post('report_RegisterYear'));

            $data['re_teacher'] = $this->db->select('skjacth_personnel.tb_personnel.pers_id,
                                                    skjacth_academic.tb_regclass.Reg_Class,
                                                    skjacth_academic.tb_regclass.Reg_Year,
                                                    skjacth_personnel.tb_personnel.pers_prefix,
                                                    skjacth_personnel.tb_personnel.pers_firstname,
                                                    skjacth_personnel.tb_personnel.pers_lastname')
                                ->from('tb_regclass')
                                ->join('skjacth_personnel.tb_personnel','skjacth_personnel.tb_personnel.pers_id = skjacth_academic.tb_regclass.class_teacher','left')
                                ->where('Reg_Year',@$sub_Year[1])
                                ->where('Reg_Class',@$sub_room[1])
                                ->get()->result(); 
          

        $data['set_score'] = $this->db->where('regscore_subjectID',@$data['re_subjuct'][0]->SubjectID)->get('tb_register_score')->result();
        
        $data['check_student'] = $this->db->select('
                                    tb_register.SubjectID,
                                    tb_register.RegisterYear,
                                    tb_register.RegisterClass,
                                    tb_register.Score100,
                                    tb_register.Grade,
                                    tb_register.TeacherID,
                                    tb_register.StudyTime,
                                    tb_subjects.SubjectName,
                                    tb_subjects.SubjectCode,
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
                                    tb_students.StudentBehavior
                                ')
                                ->from('tb_register')
                                ->join('tb_subjects','tb_subjects.SubjectID = tb_register.SubjectID')
                                ->join('tb_students','tb_students.StudentID = tb_register.StudentID')
                                ->where('TeacherID',$this->session->userdata('login_id'))
                                ->where('tb_register.RegisterYear',$this->input->post('report_RegisterYear'))
                                ->where('tb_subjects.SubjectYear',$this->input->post('report_RegisterYear'))
                                ->where('tb_register.SubjectID',$this->input->post('report_SubjectID'))
                                ->where('tb_students.StudentClass',$this->input->post('select_print'))
                                ->order_by('tb_students.StudentClass','ASC')
                                ->order_by('tb_students.StudentNumber','ASC')
                                ->get()->result();
           
        //echo '<pre>';print_r($data['check_student']); exit();
        }


       

    }

}


?>