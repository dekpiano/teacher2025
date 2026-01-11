<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaveTypeModel extends Model
{
    protected $DBGroup          = 'personnel';
    protected $table            = 'tb_leave_types';
    protected $primaryKey       = 'leave_type_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'leave_type_name',
        'leave_type_quota',
        'leave_type_status',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
