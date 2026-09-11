<?php

namespace App\Models;

use CodeIgniter\Model;

class PaAgreementConfigModel extends Model
{
    protected $DBGroup          = 'personnel';
    protected $table            = 'tb_teacher_pa_agreement_config';
    protected $primaryKey       = 'conf_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'conf_year',
        'conf_status',
        'conf_start_datetime',
        'conf_end_datetime',
        'conf_note',
        'conf_created_at',
        'conf_updated_at'
    ];

    /**
     * Get submission configuration for a given fiscal year
     */
    public function getConfigByYear($year)
    {
        return $this->where('conf_year', (string)$year)->first();
    }
}
