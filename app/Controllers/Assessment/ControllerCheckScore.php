<?php

namespace App\Controllers\Assessment;

use App\Controllers\BaseController;
use App\Models\AssessmentModel;

class ControllerCheckScore extends BaseController
{
    protected $db;
    protected $db_personnel;
    protected $assessmentModel;
    protected $session;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->db_personnel = \Config\Database::connect('personnel');
        $this->assessmentModel = new AssessmentModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        return redirect()->to('assessment/save-score-normal');
    }

    /**
     * Head of Department: List teachers to check their scores
     */
    public function checkScoreHead()
    {
        $data['title'] = "ตรวจสอบการบันทึกคะแนน (เลือกครู)";
        
        $user_learning_group = $this->session->get('pers_learning');
        
        $teachers = $this->db_personnel->table('tb_personnel')
            ->select('pers_id, pers_prefix, pers_firstname, pers_lastname, pers_img')
            ->where('pers_learning', $user_learning_group)
            ->where('pers_status', 'กำลังใช้งาน')
            ->orderBy('pers_firstname', 'ASC')
            ->get()->getResult();

        foreach ($teachers as &$teacher) {
            $teacher->classes = $this->db->table('tb_register')
                ->select('RegisterClass')
                ->distinct()
                ->where('TeacherID', $teacher->pers_id)
                ->orderBy('LENGTH(RegisterClass)', 'ASC')
                ->orderBy('RegisterClass', 'ASC')
                ->get()->getResult();
        }
        $data['teachers'] = $teachers;

        return view('teacher/assessment/check_score_head_teachers', $data);
    }

    /**
     * Head of Department: Detail of scores for a specific teacher
     */
    public function checkScoreHeadDetail($teacher_id = null, $year_term = null)
    {
        if ($teacher_id === null) {
            return redirect()->to('assessment/check-score-head')->with('error', 'ไม่ได้ระบุรหัสครู');
        }

        $data['teacher_info'] = $this->db_personnel->table('tb_personnel')
            ->select('pers_id, pers_prefix, pers_firstname, pers_lastname, pers_img')
            ->where('pers_id', $teacher_id)
            ->get()->getRow();

        $data['title'] = "สรุปการบันทึกคะแนน: " . $data['teacher_info']->pers_firstname;

        // Fetch and Sort ALL available years/terms from tb_register first
        $checkYearRaw = $this->db->table('tb_register')
            ->select('RegisterYear')
            ->distinct()
            ->get()->getResultArray();
            
        $data['CheckYear'] = [];
        foreach($checkYearRaw as $y) {
            $p = explode('/', $y['RegisterYear']);
            if (count($p) == 2) {
                $data['CheckYear'][] = [
                    'RegisterYear' => $y['RegisterYear'],
                    'term' => $p[0],
                    'year' => $p[1]
                ];
            }
        }

        // Sort: Year DESC, Term DESC to get the true latest at index 0
        usort($data['CheckYear'], function($a, $b) {
            if ($a['year'] != $b['year']) {
                return (int)$b['year'] - (int)$a['year'];
            }
            return (int)$b['term'] - (int)$a['term'];
        });

        // Determine current year and term
        if ($year_term) {
            $parts = explode('-', $year_term);
            if (count($parts) == 2) {
                $year = $parts[0];
                $term = $parts[1];
            } else {
                $year = $data['CheckYear'][0]['year'] ?? (date('Y') + 543);
                $term = $data['CheckYear'][0]['term'] ?? 1;
            }
        } else {
            // Default to the first (latest) year in our sorted list
            $year = $data['CheckYear'][0]['year'] ?? (date('Y') + 543);
            $term = $data['CheckYear'][0]['term'] ?? 1;
        }

        $data['current_year'] = $year;
        $data['current_term'] = $term;
        $full_year_term = $term . '/' . $year;

        // Get subjects for this teacher
        $subjects = $this->db->table('tb_register')
            ->select('
                tb_register.SubjectID,
                tb_subjects.SubjectName,
                tb_subjects.SubjectCode,
                tb_register.RegisterYear
            ')
            ->join('tb_subjects', 'tb_subjects.SubjectID = tb_register.SubjectID')
            ->where('tb_register.TeacherID', $teacher_id)
            ->where('tb_register.RegisterYear', $full_year_term)
            ->groupBy('tb_register.SubjectID')
            ->orderBy('tb_subjects.SubjectCode', 'ASC')
            ->get()->getResult();

        foreach ($subjects as &$subject) {
            // Fetch classes for this subject
            $classListArr = $this->db->table('tb_register')
                ->select('RegisterClass')
                ->distinct()
                ->where('TeacherID', $teacher_id)
                ->where('SubjectID', $subject->SubjectID)
                ->where('RegisterYear', $full_year_term)
                ->orderBy('LENGTH(RegisterClass)', 'ASC')
                ->orderBy('RegisterClass', 'ASC')
                ->get()->getResultArray();
            
            $subject->classes = implode(', ', array_column($classListArr, 'RegisterClass'));

            // Fetch all student records for this specific subject to analyze Score100
            $records = $this->db->table('tb_register')
                ->select('tb_register.Score100, tb_register.Grade')
                ->join('tb_students', 'tb_students.StudentID = tb_register.StudentID')
                ->where('tb_register.TeacherID', $teacher_id)
                ->where('tb_register.SubjectID', $subject->SubjectID)
                ->where('tb_register.RegisterYear', $full_year_term)
                ->where('tb_students.StudentBehavior !=', 'จำหน่าย')
                ->get()->getResult();

            $total = count($records);
            $part1 = 0; // ก่อนกลางภาค
            $part2 = 0; // กลางภาค
            $part3 = 0; // หลังกลางภาค
            $part4 = 0; // ปลายภาค
            $graded = 0;

            foreach ($records as $rec) {
                if ($rec->Grade != "" && $rec->Grade !== null) $graded++;
                
                if (!empty($rec->Score100)) {
                    $s = explode('|', $rec->Score100);
                    if (isset($s[0]) && $s[0] !== '') $part1++;
                    if (isset($s[1]) && $s[1] !== '') $part2++;
                    if (isset($s[2]) && $s[2] !== '') $part3++;
                    if (isset($s[3]) && $s[3] !== '') $part4++;
                }
            }

            $subject->total_students = $total;
            $subject->graded_students = $graded;
            $subject->progress = [
                'p1' => ($total > 0) ? round(($part1 / $total) * 100) : 0,
                'p2' => ($total > 0) ? round(($part2 / $total) * 100) : 0,
                'p3' => ($total > 0) ? round(($part3 / $total) * 100) : 0,
                'p4' => ($total > 0) ? round(($part4 / $total) * 100) : 0,
                'p1_count' => $part1,
                'p2_count' => $part2,
                'p3_count' => $part3,
                'p4_count' => $part4,
            ];
        }

        $data['subjects'] = $subjects;
        $data['year_term'] = $year . '-' . $term;

        return view('teacher/assessment/check_score_head_detail', $data);
    }

    /**
     * Head of Department: Individual student scores for a subject
     */
    public function checkScoreHeadStudentDetail($teacher_id, $subject_id, $class, $year_term)
    {
        $parts = explode('-', $year_term);
        $term = $parts[1] ?? 1;
        $year = $parts[0] ?? '';
        $full_year_term = $term . '/' . $year;

        $data['teacher_info'] = $this->db_personnel->table('tb_personnel')
            ->select('pers_id, pers_prefix, pers_firstname, pers_lastname')
            ->where('pers_id', $teacher_id)
            ->get()->getRow();

        $data['subject_info'] = $this->db->table('tb_subjects')
            ->where('SubjectID', $subject_id)
            ->get()->getRow();
            
        $data['class'] = $class;
        $data['year_term'] = $year_term;
        $data['title'] = "คะแนนนักเรียนรายบุคคล";

        // Fetch score settings (names and max scores)
        $data['score_settings'] = $this->db->table('tb_register_score')
            ->where('regscore_subjectID', $subject_id)
            ->orderBy('regscore_ID', 'ASC')
            ->get()->getResult();

        // Fetch students and their Score100
        $data['students'] = $this->db->table('tb_register')
            ->select('tb_register.StudentID, tb_students.StudentClass as RegisterClass, tb_register.Score100, tb_register.Grade, tb_students.StudentPrefix, tb_students.StudentFirstName, tb_students.StudentLastName, tb_students.StudentNumber')
            ->join('tb_students', 'tb_students.StudentID = tb_register.StudentID')
            ->where('tb_register.TeacherID', $teacher_id)
            ->where('tb_register.SubjectID', $subject_id)
            ->where('tb_register.RegisterYear', $full_year_term)
            ->where('tb_students.StudentBehavior !=', 'จำหน่าย')
            ->orderBy('LENGTH(tb_students.StudentClass)', 'ASC')
            ->orderBy('tb_students.StudentClass', 'ASC')
            ->orderBy('tb_students.StudentNumber', 'ASC')
            ->get()->getResult();

        return view('teacher/assessment/check_score_head_student_detail', $data);
    }

    /**
     * AJAX: Get student scores for modal
     */
    public function ajaxGetStudentScores()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $teacher_id = $this->request->getPost('teacher_id');
        $subject_id = $this->request->getPost('subject_id');
        $class = $this->request->getPost('class');
        $year_term = $this->request->getPost('year_term');

        $parts = explode('-', $year_term);
        $term = $parts[1] ?? 1;
        $year = $parts[0] ?? '';
        $full_year_term = $term . '/' . $year;

        $query = $this->db->table('tb_register')
            ->select('tb_register.StudentID, tb_students.StudentClass as RegisterClass, tb_register.Score100, tb_register.Grade, tb_students.StudentPrefix, tb_students.StudentFirstName, tb_students.StudentLastName, tb_students.StudentNumber')
            ->join('tb_students', 'tb_students.StudentID = tb_register.StudentID')
            ->where('tb_register.TeacherID', $teacher_id)
            ->where('tb_register.SubjectID', $subject_id)
            ->where('tb_register.RegisterYear', $full_year_term)
            ->where('tb_students.StudentBehavior !=', 'จำหน่าย');

        if ($class && $class !== 'all') {
            $query->where('tb_students.StudentClass', $class);
        }

        $students = $query->orderBy('LENGTH(tb_students.StudentClass)', 'ASC')
            ->orderBy('tb_students.StudentClass', 'ASC')
            ->orderBy('tb_students.StudentNumber', 'ASC')
            ->get()->getResult();

        return $this->response->setJSON($students);
    }
}
