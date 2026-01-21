<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaveRequestModel extends Model
{
    protected $DBGroup          = 'personnel';
    protected $table            = 'tb_leave_requests';
    protected $primaryKey       = 'leave_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pers_id',
        'leave_type_id',
        'leave_topic',
        'leave_detail',
        'leave_start_date',
        'leave_end_date',
        'leave_total_days',
        'leave_period',
        'leave_contact_address',  // ที่อยู่ที่ติดต่อได้ระหว่างลา
        'leave_contact_phone',    // เบอร์โทรศัพท์ติดต่อ
        'leave_file',
        'leave_status',
        'leave_comment',
        'approved_by',
        'approved_at',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getLeaveWithTypes($pers_id = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('tb_leave_requests.*, tb_leave_types.leave_type_name');
        $builder->join('tb_leave_types', 'tb_leave_types.leave_type_id = tb_leave_requests.leave_type_id');
        if ($pers_id) {
            $builder->where('tb_leave_requests.pers_id', $pers_id);
        }
        $builder->orderBy('tb_leave_requests.leave_start_date', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function getCombinedLeaveHistory($pers_id)
    {
        $db_personnel = \Config\Database::connect('personnel');
        
        // Get active leave year
        $activeYear = $db_personnel->table('tb_leave_years')
            ->where('ly_status', 'active')
            ->get()
            ->getRow();

        $result = [];

        // Get from tb_leave_requests (new system - self-requested)
        if ($activeYear) {
            $builder = $this->db->table($this->table);
            $builder->select('tb_leave_requests.*, tb_leave_types.leave_type_name, "self" as source');
            $builder->join('tb_leave_types', 'tb_leave_types.leave_type_id = tb_leave_requests.leave_type_id');
            $builder->where('tb_leave_requests.pers_id', $pers_id);
            $builder->where('tb_leave_requests.leave_start_date >=', $activeYear->ly_start_date);
            $builder->where('tb_leave_requests.leave_start_date <=', $activeYear->ly_end_date);
            $builder->orderBy('tb_leave_requests.leave_start_date', 'DESC');
            
            $selfLeaves = $builder->get()->getResultArray();
            foreach ($selfLeaves as $leave) {
                $result[] = [
                    'id' => $leave['leave_id'],
                    'type_name' => $leave['leave_type_name'],
                    'topic' => $leave['leave_topic'],
                    'detail' => $leave['leave_detail'],
                    'start_date' => $leave['leave_start_date'],
                    'end_date' => $leave['leave_end_date'],
                    'total_days' => $leave['leave_total_days'],
                    'status' => $leave['leave_status'],
                    'created_at' => $leave['created_at'],
                    'source' => 'self',
                    'can_cancel' => ($leave['leave_status'] == 'pending'),
                ];
            }
        }

        // Get from tb_personnel_leave (legacy - staff entered)
        if ($activeYear) {
            $legacyBuilder = $db_personnel->table('tb_personnel_leave');
            $legacyBuilder->where('pers_id', $pers_id);
            $legacyBuilder->where('leave_start_date >=', $activeYear->ly_start_date);
            $legacyBuilder->where('leave_start_date <=', $activeYear->ly_end_date);
            $legacyBuilder->orderBy('leave_start_date', 'DESC');
            
            $staffLeaves = $legacyBuilder->get()->getResultArray();
            foreach ($staffLeaves as $leave) {
                // Calculate actual days from dates
                $startDate = strtotime($leave['leave_start_date']);
                $endDate = strtotime($leave['leave_end_date']);
                $actualDays = round(($endDate - $startDate) / (60 * 60 * 24)) + 1;
                
                $result[] = [
                    'id' => $leave['id'],
                    'type_name' => $leave['leave_type'],
                    'topic' => $leave['leave_type'],
                    'detail' => $leave['leave_note'],
                    'start_date' => $leave['leave_start_date'],
                    'end_date' => $leave['leave_end_date'],
                    'total_days' => $actualDays,
                    'status' => 'approved', // Legacy data is considered approved
                    'created_at' => $leave['created_at'],
                    'source' => 'staff',
                    'can_cancel' => false,
                ];
            }
        }

        // Sort by start_date DESC
        usort($result, function($a, $b) {
            return strtotime($b['start_date']) - strtotime($a['start_date']);
        });

        return $result;
    }

    public function getUsedDays($pers_id, $leave_type_id)
    {
        $db_personnel = \Config\Database::connect('personnel');
        
        // Get active leave year
        $activeYear = $db_personnel->table('tb_leave_years')
            ->where('ly_status', 'active')
            ->get()
            ->getRow();

        if (!$activeYear) return 0;

        // Get leave type name for matching with legacy table
        $leaveType = $db_personnel->table('tb_leave_types')
            ->where('leave_type_id', $leave_type_id)
            ->get()
            ->getRow();
        
        $leaveTypeName = $leaveType ? $leaveType->leave_type_name : '';

        // Count from tb_leave_requests (new system)
        $builder = $this->db->table($this->table);
        $builder->selectSum('leave_total_days');
        $builder->where('pers_id', $pers_id);
        $builder->where('leave_type_id', $leave_type_id);
        
        // Only count leaves that are approved or pending (not rejected or cancelled)
        $builder->whereIn('leave_status', ['pending', 'approved']);
        
        // Standardize date comparison for the active leave year
        $builder->groupStart()
            ->where('leave_start_date >=', $activeYear->ly_start_date)
            ->where('leave_start_date <=', $activeYear->ly_end_date)
        ->groupEnd();
        
        $result = $builder->get()->getRow();
        $usedFromNewSystem = $result && $result->leave_total_days ? (float)$result->leave_total_days : 0;

        // Count from tb_personnel_leave (legacy data entered by staff)
        // Calculate actual days from dates instead of using stored leave_days
        $legacyBuilder = $db_personnel->table('tb_personnel_leave');
        $legacyBuilder->select('leave_start_date, leave_end_date');
        $legacyBuilder->where('pers_id', $pers_id);
        $legacyBuilder->where('leave_type', $leaveTypeName);
        $legacyBuilder->groupStart()
            ->where('leave_start_date >=', $activeYear->ly_start_date)
            ->where('leave_start_date <=', $activeYear->ly_end_date)
        ->groupEnd();
        
        $legacyLeaves = $legacyBuilder->get()->getResultArray();
        $usedFromLegacy = 0;
        foreach ($legacyLeaves as $leave) {
            $startDate = strtotime($leave['leave_start_date']);
            $endDate = strtotime($leave['leave_end_date']);
            $usedFromLegacy += round(($endDate - $startDate) / (60 * 60 * 24)) + 1;
        }

        return $usedFromNewSystem + $usedFromLegacy;
    }

    /**
     * Get count of late arrivals for the active leave year
     */
    public function getLateCount($pers_id)
    {
        $db_personnel = \Config\Database::connect('personnel');
        
        // Get active leave year
        $activeYear = $db_personnel->table('tb_leave_years')
            ->where('ly_status', 'active')
            ->get()
            ->getRow();

        if (!$activeYear) return 0;

        return $db_personnel->table('tb_personnel_attendance')
            ->where('att_person_id', $pers_id)
            ->where('att_status', 'สาย')
            ->where('att_date >=', $activeYear->ly_start_date)
            ->where('att_date <=', $activeYear->ly_end_date)
            ->countAllResults();
    }

    /**
     * Get specific dates of late arrivals for the active leave year
     */
    public function getLateDetails($pers_id)
    {
        $db_personnel = \Config\Database::connect('personnel');
        
        // Get active leave year
        $activeYear = $db_personnel->table('tb_leave_years')
            ->where('ly_status', 'active')
            ->get()
            ->getRow();

        if (!$activeYear) return [];

        return $db_personnel->table('tb_personnel_attendance')
            ->where('att_person_id', $pers_id)
            ->where('att_status', 'สาย')
            ->where('att_date >=', $activeYear->ly_start_date)
            ->where('att_date <=', $activeYear->ly_end_date)
            ->orderBy('att_date', 'DESC')
            ->get()
            ->getResultArray();
    }
}
