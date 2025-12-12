<?php

namespace App\Models;

use CodeIgniter\Model;

class HomeroomModel extends Model
{
    protected $DBGroup          = 'affairs';
    protected $table            = 'tb_checkhomeroom';
    protected $primaryKey       = 'chk_home_id';
    protected $allowedFields    = [
        'chk_home_date', 'chk_home_time', 'chk_home_teacher', 'chk_home_room',
        'chk_home_ma', 'chk_home_khad', 'chk_home_la', 'chk_home_sahy',
        'chk_home_kid', 'chk_home_hnee', 'chk_home_term', 'chk_home_yaer'
    ];

    /**
     * Fetches student details for a given status on a specific record.
     * 
     * @param int $recordId The ID of the attendance record.
     * @param string $statusField The field name for the status (e.g., 'chk_home_khad').
     * @return array An array of student objects.
     */
    public function getStudentsByStatus(int $recordId, string $statusField): array
    {
        $record = $this->find($recordId);

        if (!$record || empty($record[$statusField])) {
            return [];
        }

        $studentCodes = explode('|', $record[$statusField]);

        if (empty($studentCodes)) {
            return [];
        }

        $dbDefault = db_connect(); // Connect to the default database for students table
        $students = $dbDefault->table('tb_students')
                                ->select('StudentNumber, StudentClass, StudentCode, StudentPrefix, StudentFirstName, StudentLastName')
                                ->whereIn('StudentCode', $studentCodes)
                                ->get()
                                ->getResult();

        return $students;
    }
}
