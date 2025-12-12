<?php

namespace App\Models;

use CodeIgniter\Model;

class DesirableAssessmentModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tb_evalu_desirable_detail';
    protected $primaryKey       = 'detail_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['student_id', 'item_id', 'score', 'term', 'academic_year', 'evaluator_id', 'evaluated_at'];

    public function getTeacherClasses(string $teacherId, string $academicYear)
    {
        // This function can be reused as it is
        $db = db_connect();
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
        // This function can be reused as it is
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
        $allItems = $this->db->table('tb_evalu_desirable_item')
                             ->where('is_active', 1)
                             ->orderBy('parent_id', 'ASC')
                             ->orderBy('item_order', 'ASC')
                             ->get()
                             ->getResultArray();

        $structuredItems = [];
        $subItems = [];

        // Separate main items and sub-items
        foreach ($allItems as $item) {
            if ($item['parent_id'] == 0) {
                $item['sub_items'] = []; // Initialize sub_items array
                $structuredItems[$item['item_id']] = $item;
            } else {
                $subItems[] = $item;
            }
        }

        // Assign sub-items to their parents
        foreach ($subItems as $subItem) {
            if (isset($structuredItems[$subItem['parent_id']])) {
                $structuredItems[$subItem['parent_id']]['sub_items'][] = $subItem;
            }
        }

        return array_values($structuredItems); // Return as a simple array
    }

    public function saveEvaluation(array $data)
    {
        $studentIds = array_keys($data['scores']);

        if (empty($studentIds)) {
            return true;
        }

        $this->db->transStart();

        // Delete old records
        $this->db->table($this->table)
           ->whereIn('student_id', $studentIds)
           ->where('academic_year', $data['academicYear'])
           ->where('term', $data['term'])
           ->delete();

        // Insert new records
        foreach ($data['scores'] as $studentId => $items) {
            foreach ($items as $itemId => $score) {
                if (is_numeric($score) && $score !== '') {
                    $this->insert([
                        'student_id' => $studentId,
                        'item_id' => $itemId,
                        'score' => $score,
                        'term' => $data['term'],
                        'academic_year' => $data['academicYear'],
                        'evaluator_id' => $data['evaluatorId'],
                        'evaluated_at' => date('Y-m-d H:i:s')
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

        $query = $this->db->table($this->table)
                      ->whereIn('student_id', $studentIds)
                      ->where('academic_year', $academicYear)
                      ->where('term', $term)
                      ->get()
                      ->getResultArray();

        $evaluations = [];
        foreach ($query as $row) {
            $evaluations[$row['student_id']][$row['item_id']] = $row['score'];
        }

        return $evaluations;
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
        $db = db_connect('default');
        $result = $db->table('tb_admin_rloes')
                       ->select('admin_rloes_userid, admin_rloes_academic_position')
                       ->where('admin_rloes_academic_position', $position)
                       ->get()
                       ->getRowArray();
        return $result; // Returns an array with userid and position, or null
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
        $assessedCount = $this->db->table('tb_evalu_desirable_detail')
                                  ->select('student_id')
                                  ->whereIn('student_id', $studentIds)
                                  ->where('academic_year', $academicYear)
                                  ->where('term', $term)
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

    public function getAssessmentAcademicYearAndTerm()
    {
        $db = db_connect(); // Default DB
        $result = $db->table('tb_register_onoff')
                       ->select('onoff_year, onoff_detail')
                       ->where('onoff_name', 'DesirableCharacteristics')
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
                       ->where('onoff_name', 'DesirableCharacteristics')
                       ->get()
                       ->getRowArray();

        return $result ? $result['onoff_status'] : 'off'; // Default to 'off' if not found
    }

}
