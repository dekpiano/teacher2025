<?php

namespace App\Models;

use CodeIgniter\Model;

class ReadingAssessmentModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tb_evalu_raw_detail';
    protected $primaryKey       = 'EvaluationID';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['StudentID', 'ItemID', 'Score', 'Term', 'AcademicYear', 'EvaluatorID', 'DateEvaluated'];

    public function getTeacherClasses(string $teacherId, string $academicYear)
    {
        $db = db_connect(); // tb_regclass is in default database
        return $db->table('tb_regclass')
                        ->select('Reg_Class')
                        ->where('class_teacher', $teacherId)
                        ->where('Reg_Year', $academicYear)
                        ->distinct()
                        ->get()
                        ->getResultArray();
    }

    public function getStudentsByHomeroomClass(string $className, string $academicYear)
    {
        $db = db_connect();
        return $db->table('tb_students')
            ->where('StudentClass', 'ม.'.$className)
            ->where('StudentStatus', '1/ปกติ')
            ->orderBy('StudentNumber', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getAssessmentItems()
    {
        return $this->db->table('tb_evalu_raw_item')
                        ->orderBy('ItemID', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    public function getStudentDetails(string $studentId)
    {
        $db = db_connect(); // tb_students is in default database
        return $db->table('tb_students')
                        ->where('StudentID', $studentId)
                        ->get()
                        ->getRowArray();
    }

    public function saveEvaluation(array $data)
    {
        $studentIds = array_keys($data['scores']);

        if (empty($studentIds)) {
            return true;
        }

        $this->db->transStart();

        // Delete old records for the students being updated
        $this->db->table('tb_evalu_raw_detail')
           ->whereIn('StudentID', $studentIds)
           ->where('AcademicYear', $data['academicYear'])
           ->where('Term', $data['term'])
           ->delete();

        // Insert new records
        foreach ($data['scores'] as $studentId => $items) {
            foreach ($items as $itemId => $score) {
                // Only insert if a score was actually entered
                if (is_numeric($score) && $score !== '') {
                    $this->db->table('tb_evalu_raw_detail')->insert([
                        'StudentID' => $studentId,
                        'ItemID' => $itemId,
                        'Score' => $score,
                        'Term' => $data['term'],
                        'AcademicYear' => $data['academicYear'],
                        'EvaluatorID' => $data['evaluatorId'],
                        'DateEvaluated' => date('Y-m-d')
                    ]);
                }
            }
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    public function getEvaluationsForClass(array $studentIds, string $academicYear, string $term)
    {
        if (empty($studentIds)) {
            return [];
        }

        $query = $this->db->table('tb_evalu_raw_detail')
                      ->whereIn('StudentID', $studentIds)
                      ->where('AcademicYear', $academicYear)
                      ->where('Term', $term)
                      ->get()
                      ->getResultArray();

        $evaluations = [];
        foreach ($query as $row) {
            $evaluations[$row['StudentID']][$row['ItemID']] = $row['Score'];
        }

        return $evaluations;
    }

    public function getLatestSchoolYear()
    {
        $db = db_connect(); // Default DB
        $allEntries = $db->table('tb_schoolyear')->select('schyear_year')->get()->getResultArray();

        if (empty($allEntries)) {
            // Fallback to current Buddhist year if table is empty
            return ['year' => date('Y') + 543, 'term' => '1'];
        }

        usort($allEntries, function($a, $b) {
            list($termA, $yearA) = explode('/', $a['schyear_year']);
            list($termB, $yearB) = explode('/', $b['schyear_year']);

            if ($yearB != $yearA) {
                return $yearB <=> $yearA; // Sort by year descending
            }
            return $termB <=> $termA; // Then by term descending
        });

        $latestEntry = $allEntries[0]['schyear_year'];
        $parts = explode('/', $latestEntry);
        
        $term = $parts[0] ?? '1';
        $year = $parts[1] ?? date('Y') + 543;

        return ['year' => $year, 'term' => $term];
    }

    public function getAssessmentStatusForClass($className, $academicYear, $term)
    {
        // Get all student IDs for the class from the tb_students table
        $db = db_connect();
        $studentIdsQuery = $db->table('tb_students')
                                ->select('StudentID')
                                ->where('StudentClass', 'ม.'.$className)
                                ->where('StudentStatus', '1/ปกติ') // To count only active students
                                ->get()
                                ->getResultArray();

        if (empty($studentIdsQuery)) {
            return ['total' => 0, 'assessed' => 0];
        }
        $studentIds = array_column($studentIdsQuery, 'StudentID');
        $totalStudents = count($studentIds);

        // Count how many of these students have at least one entry in the detail table
        $assessedCount = $this->db->table('tb_evalu_raw_detail')
                                  ->select('StudentID')
                                  ->whereIn('StudentID', $studentIds)
                                  ->where('AcademicYear', $academicYear)
                                  ->where('Term', $term)
                                  ->distinct()
                                  ->countAllResults();

        return ['total' => $totalStudents, 'assessed' => $assessedCount];
    }

    public function getGradeLevelHead(string $grade, string $academicYear)
    {
        $db = db_connect();
        $result = $db->table('tb_regclass')
                       ->where('Reg_Year', $academicYear)
                       ->where('Reg_Class', $grade)
                       ->get()
                       ->getRowArray();
        return $result ? $result['class_teacher'] : null;
    }

    public function getPersonnelFullName(string $pers_id)
    {
        $db = db_connect('personnel'); // Connect to the personnel database
        return $db->table('tb_personnel')
                    ->select('pers_prefix, pers_firstname, pers_lastname')
                    ->where('pers_id', $pers_id)
                    ->get()
                    ->getRowArray();
    }

    public function getHomeroomTeachersByClassAndYear(string $className, string $academicYear)
    {
        $db = db_connect('default'); // Assuming 'default' is the DBGroup for skjacth_academic
        return $db->table('tb_regclass')
                    ->select('class_teacher')
                    ->where('Reg_Class', $className)
                    ->where('Reg_Year', $academicYear)
                    ->get()->getResultArray();
    }

    public function getAdminPersonnelInfoByPosition(string $position)
    {
        $db = db_connect('default'); // Assuming 'default' is the DBGroup for skjacth_academic
        $result = $db->table('tb_admin_rloes')
                       ->select('admin_rloes_userid, admin_rloes_academic_position')
                       ->where('admin_rloes_academic_position', $position)
                       ->get()
                       ->getRowArray();
        return $result; // Returns an array with userid and position, or null
    }

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getAssessmentAcademicYearAndTerm()
    {
        $db = db_connect(); // Default DB
        $result = $db->table('tb_register_onoff')
                       ->select('onoff_year, onoff_detail')
                       ->where('onoff_name', 'ReadingWritingLearning')
                       ->get()
                       ->getRowArray();

        if ($result) {
            $parts = explode('/', $result['onoff_year']);
            $term = $parts[0] ?? '1';
            $year = $parts[1] ?? date('Y') + 543;
            return ['year' => $year, 'term' => $term];
        }

        // Fallback to current Buddhist year and term '1' if no record found
        return ['year' => date('Y') + 543, 'term' => '1'];
    }

        public function getAssessmentOnOffStatus()

        {

            $db = db_connect(); // Default DB

            $result = $db->table('tb_register_onoff')

                           ->select('onoff_status')

                           ->where('onoff_name', 'ReadingWritingLearning')

                           ->get()

                           ->getRowArray();

    

            return $result ? $result['onoff_status'] : 'off'; // Default to 'off' if not found

        }

    

        /*

        This model will be used to interact with the following tables:

        - tb_evalu_raw_item

        - tb_evalu_raw_detail

        - t_final_result

        - t_class

        - tb_students (for student info)

        - tb_personnel (for teacher info)

        */

    }