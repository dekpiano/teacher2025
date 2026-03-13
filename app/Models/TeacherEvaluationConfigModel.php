<?php

namespace App\Models;

use CodeIgniter\Model;

class TeacherEvaluationConfigModel extends Model
{
    protected $DBGroup          = 'personnel';
    protected $table            = 'tb_teacher_evaluation_config';
    protected $primaryKey       = 'conf_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'conf_year',
        'conf_round',
        'conf_status',
        'conf_start_date',
        'conf_end_date'
    ];

    /**
     * Get config by year and round
     */
    public function getConfig($year, $round)
    {
        return $this->where([
            'conf_year'  => $year,
            'conf_round' => $round
        ])->first();
    }
}
