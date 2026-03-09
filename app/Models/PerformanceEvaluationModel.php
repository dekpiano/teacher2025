<?php

namespace App\Models;

use CodeIgniter\Model;

class PerformanceEvaluationModel extends Model
{
    protected $table            = 'tb_teacher_evaluation';
    protected $primaryKey       = 'eva_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'eva_teacher_id',
        'eva_year',
        'eva_round',
        'eva_file',
        'eva_status',
        'eva_comment'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'eva_created_at';
    protected $updatedField  = 'eva_updated_at';

    /**
     * Get evaluation by teacher, year, and round
     */
    public function getEvaluation($teacherId, $year, $round)
    {
        return $this->where([
            'eva_teacher_id' => $teacherId,
            'eva_year'       => $year,
            'eva_round'      => $round
        ])->first();
    }
}
