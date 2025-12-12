<?php

namespace App\Models;

use CodeIgniter\Model;

class AssessmentModel extends Model
{
    protected $DBGroup = 'default'; // Default to academic database
    protected $table = 'tb_register_score';
    protected $primaryKey = 'score_id';
    protected $allowedFields = [
        'score_student_id', 'score_subject_id', 'score_term', 'score_year',
        'score_class', 'score_teacher_id', 'score_point', 'score_type',
        'score_status', 'score_date', 'score_time', 'score_comment'
    ];

    public function getTeacherSubjects($teacherId, $schoolyear)
    {
        $db = db_connect();

        $builder = $db->table('tb_register')
                      ->select([
                          'tb_register.SubjectID',
                          'tb_register.RegisterYear',
                          'tb_register.RegisterClass',
                          'tb_register.TeacherID',
                          'tb_subjects.SubjectName',
                          'tb_subjects.SubjectCode',
                          'tb_subjects.SubjectUnit',
                          'tb_subjects.SubjectHour'
                      ])
                      ->join('tb_subjects', 'tb_subjects.SubjectID = tb_register.SubjectID')
                      ->where('tb_register.TeacherID', $teacherId)
                      ->where('tb_register.RegisterYear', $schoolyear)
                      ->where('tb_subjects.SubjectYear', $schoolyear)
                      ->groupBy('tb_register.SubjectID')
                      ->groupBy('tb_register.RegisterClass')
                      ->groupBy('tb_register.RegisterYear')
                      ->groupBy('tb_register.TeacherID')
                      ->orderBy('tb_register.RegisterClass');

        return $builder->get()->getResult();
    }

    // Method to get students for a specific class
    public function getStudentsByClass($class, $status = '1/ปกติ')
    {
        $dbAcademic = db_connect();
        return $dbAcademic->table('tb_students')
                           ->where('StudentClass', $class)
                           ->where('StudentStatus', $status)
                           ->orderBy('StudentNumber', 'asc')
                           ->get()
                           ->getResult();
    }

    public function getScoreSettings($subjectId)
    {
        $dbAcademic = db_connect();
        return $dbAcademic->table('tb_register_score')
                           ->where('regscore_subjectID', $subjectId)
                           ->orderBy('regscore_ID', 'asc')
                           ->get()
                           ->getResult();
    }

    // Method to get existing scores for students in a subject
    public function getExistingScores($subjectId, $year, $term, $class = null)
    {
        $dbAcademic = db_connect();
        $builder = $dbAcademic->table('tb_register_score')
                               ->where('score_subject_id', $subjectId)
                               ->where('score_year', $year)
                                                              ->where('score_term', $term);
        
        if ($class !== null) {
            $builder->where('score_class', $class);
        }

        return $builder->get()->getResult();
    }

    // Method to get on/off status for score saving
    public function getOnOffStatus()
    {
        $db = db_connect(); // Default DB
        return $db->table('tb_send_plan_setup')->get()->getResult();
    }

    // Insert and Update are handled by the base Model class

    public function getStudentsForSubject($teacherId, $subjectId, $registerYear, $room = 'all')
    {
        $db = db_connect();
        $builder = $db->table('tb_register')
                      ->select([
                          'tb_register.SubjectID',
                          'tb_register.RegisterYear',
                          'tb_register.RegisterClass',
                          'tb_register.Score100',
                          'tb_register.TeacherID',
                          'tb_subjects.SubjectName',
                          'tb_subjects.SubjectCode',
                          'tb_register.StudyTime',
                          'tb_subjects.SubjectID',
                          'tb_subjects.SubjectUnit',
                          'tb_subjects.SubjectHour',
                          'tb_subjects.SubjectYear',
                          'tb_students.StudentID',
                          'tb_students.StudentPrefix',
                          'tb_students.StudentFirstName',
                          'tb_students.StudentLastName',
                          'tb_students.StudentNumber',
                          'tb_students.StudentClass',
                          'tb_students.StudentCode',
                          'tb_students.StudentStatus',
                          'tb_students.StudentBehavior',
                          'tb_register.Grade_Type'
                      ])
                      ->join('tb_subjects', 'tb_subjects.SubjectID = tb_register.SubjectID')
                      ->join('tb_students', 'tb_students.StudentID = tb_register.StudentID')
                      ->where('tb_register.TeacherID', $teacherId)
                      ->where('tb_register.RegisterYear', $registerYear)
                      ->where('tb_subjects.SubjectYear', $registerYear)
                      ->where('tb_register.SubjectID', $subjectId)
                      ->orderBy('tb_students.StudentClass', 'ASC')
                      ->orderBy('tb_students.StudentNumber', 'ASC');

        if ($room != 'all') {
            $sub_checkroom = explode('-', $room);
            $sub_room = $sub_checkroom[0].'/'.$sub_checkroom[1];
            $builder->where('tb_students.StudentClass', 'ม.'.$sub_room);
        }

        return $builder->get()->getResult();
    }
}
