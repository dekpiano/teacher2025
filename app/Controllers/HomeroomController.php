<?php

namespace App\Controllers;

use App\Models\HomeroomModel;

class HomeroomController extends BaseController
{
    private function getTeacherInfo()
    {
        $db = db_connect();
        $db_personnel = db_connect('personnel');
        $session = session();

        $schoolYearResult = $db->table('tb_schoolyear')->select('schyear_year')->where('schyear_id', 1)->get()->getRow();
        $yearParts = explode('/', $schoolYearResult->schyear_year);
        $currentYear = $yearParts[1];

        $teacherInfo = $db->table('tb_regclass')
                          ->select('skjacth_personnel.tb_personnel.pers_prefix, skjacth_personnel.tb_personnel.pers_firstname, skjacth_personnel.tb_personnel.pers_lastname, skjacth_personnel.tb_personnel.pers_id, skjacth_academic.tb_regclass.Reg_Year, skjacth_academic.tb_regclass.Reg_Class')
                          ->join($db_personnel->database . '.tb_personnel', 'skjacth_personnel.tb_personnel.pers_id = skjacth_academic.tb_regclass.class_teacher')
                          ->where('pers_id', $session->get('person_id'))
                          ->where('Reg_Year', $currentYear)
                          ->get()->getRow();
        return $teacherInfo;
    }

    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $homeroomModel = new HomeroomModel();
        $db = db_connect(); // Default DB

        $data['title'] = "หน้าแรกโฮมรูม";
        $data['teacher'] = $this->getTeacherInfo();

        // Redirect if user is a grade-level head teacher (logic from old controller)
        if (in_array($data['teacher']->Reg_Class, ["1", "2", "3", "4", "5", "6"])) {
            return redirect()->to('homeroom/dashboard/' . date('d-m-Y'));
        }

        // Get latest homeroom check for the specific class
        $checkIf = [
            'chk_home_term' => '1', // This might need to be dynamic later
            'chk_home_yaer' => '2566', // This might need to be dynamic later
            'chk_home_room' => $data['teacher']->Reg_Class
        ];
        $data['ChkHomeRoom'] = $homeroomModel->where($checkIf)->orderBy('chk_home_date', 'DESC')->first();

        // --- Logic to calculate statistics by gender ---
        $statuses = ['Ma', 'Khad', 'La', 'Sahy', 'Kid', 'Hnee'];
        $stats = [];

        foreach ($statuses as $status) {
            $stats['Boy' . $status] = 0;
            $stats['Girl' . $status] = 0;
        }

        if ($data['ChkHomeRoom']) {
            foreach ($statuses as $status) {
                $field = 'chk_home_' . strtolower($status);
                $studentCodes = explode('|', $data['ChkHomeRoom'][$field]);

                if (!empty($studentCodes[0])) {
                    $students = $db->table('tb_students')->select('StudentPrefix')->whereIn('StudentCode', $studentCodes)->get()->getResult();
                    foreach ($students as $student) {
                        if ($student->StudentPrefix == "นาย" || $student->StudentPrefix == "เด็กชาย") {
                            $stats['Boy' . $status]++;
                        } elseif ($student->StudentPrefix == "นางสาว" || $student->StudentPrefix == "เด็กหญิง") {
                            $stats['Girl' . $status]++;
                        }
                    }
                }
            }
        }
        
        $data = array_merge($data, $stats);

        return view('teacher/homeroom/CheckHomeRoomMain', $data);
    }

    public function add()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $homeroomModel = new HomeroomModel();
        $db = db_connect();

        $data['title'] = "เช็คชื่อโฮมรูม";
        $data['teacher'] = $this->getTeacherInfo();

        $data['studentAdd'] = $db->table('tb_students')
                                   ->where('StudentClass', 'ม.' . $data['teacher']->Reg_Class)
                                   ->where('StudentStatus', '1/ปกติ')
                                   ->orderBy('StudentNumber', 'asc')
                                   ->get()
                                   ->getResult();

        $existingRecord = $homeroomModel->where('chk_home_date', date('Y-m-d'))
                                        ->where('chk_home_room', $data['teacher']->Reg_Class)
                                        ->first();

        if ($existingRecord) {
            $data['Action'] = site_url('homeroom/update/' . $existingRecord['chk_home_id']);
            $data['ButtonName'] = "อัพเดตข้อมูล";
            $data['existingRecord'] = $existingRecord;
        } else {
            $data['Action'] = site_url('homeroom/insert');
            $data['ButtonName'] = "บันทึกข้อมูล";
        }

        return view('teacher/homeroom/CheckHomeRoomAdd', $data);
    }

    private function processAttendanceData($postData)
    {
        $statusData = $postData['status'] ?? [];
        $attendance = [
            'ma' => [], 'khad' => [], 'la' => [],
            'sahy' => [], 'kid' => [], 'hnee' => []
        ];

        $statusMap = [
            'มา' => 'ma', 'ขาด' => 'khad', 'ลา' => 'la',
            'สาย' => 'sahy', 'กิจกรรม' => 'kid', 'หนี' => 'hnee'
        ];

        foreach ($statusData as $studentCode => $status) {
            if (isset($statusMap[$status])) {
                $attendance[$statusMap[$status]][] = $studentCode;
            }
        }

        $dbData = [];
        foreach ($attendance as $key => $codes) {
            $dbData['chk_home_' . $key] = implode('|', $codes);
        }

        $dbData['chk_home_date'] = date('Y-m-d');
        $dbData['chk_home_time'] = date('H:i:s');
        $dbData['chk_home_teacher'] = $postData['chk_home_teacher'];
        $dbData['chk_home_room'] = $postData['chk_home_room'];
        // These should probably come from a form/config
        $dbData['chk_home_term'] = '1'; 
        $dbData['chk_home_yaer'] = '2566';

        return $dbData;
    }

    public function insert()
    {
        $homeroomModel = new HomeroomModel();
        $data = $this->processAttendanceData($this->request->getPost());

        if ($homeroomModel->insert($data)) {
            session()->setFlashdata('success', 'บันทึกข้อมูลสำเร็จ');
        } else {
            session()->setFlashdata('error', 'บันทึกข้อมูลไม่สำเร็จ');
        }
        return redirect()->to('homeroom/add');
    }

    public function update($id)
    {
        $homeroomModel = new HomeroomModel();
        $data = $this->processAttendanceData($this->request->getPost());

        if ($homeroomModel->update($id, $data)) {
            session()->setFlashdata('success', 'อัพเดตข้อมูลสำเร็จ');
        } else {
            session()->setFlashdata('error', 'อัพเดตข้อมูลไม่สำเร็จ');
        }
        return redirect()->to('homeroom/add');
    }

    public function dashboard($key = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $key = $key ?? date('d-m-Y');
        $dateForDb = date('Y-m-d', strtotime($key));

        $db_affairs = db_connect('affairs');
        $db = db_connect();

        $data['title'] = "แดชบอร์ดโฮมรูม";
        $data['teacher'] = $this->getTeacherInfo();
        $data['current_date'] = $key;

        $gradeLevel = $data['teacher']->Reg_Class;

        $data['showHR'] = $db_affairs->table('tb_checkhomeroom')
                                     ->where('chk_home_date', $dateForDb)
                                     ->like('chk_home_room', $gradeLevel, 'after')
                                     ->orderBy('chk_home_room', 'ASC')
                                     ->get()
                                     ->getResult();

        // This logic is complex and inefficient, migrating as-is first.
        $all = [];
        foreach ($data['showHR'] as $record) {
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
                        if ($student->StudentPrefix == "นาย" || $student->StudentPrefix == "เด็กชาย") {
                            $classStats[ucfirst($status)]['Boy']++;
                        } elseif ($student->StudentPrefix == "นางสาว" || $student->StudentPrefix == "เด็กหญิง") {
                            $classStats[ucfirst($status)]['Girl']++;
                        }
                    }
                }
            }
            $all[] = $classStats;
        }
        $data['all'] = $all;

        return view('teacher/homeroom/CheckHomeRoomDashboard', $data);
    }
}
