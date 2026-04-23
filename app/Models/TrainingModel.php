<?php

namespace App\Models;

use CodeIgniter\Model;

class TrainingModel extends Model
{
    protected $DBGroup          = 'personnel';
    protected $table            = 'tb_personnel_training';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pers_id',
        'train_name',
        'train_location',
        'train_start_date',
        'train_end_date',
        'train_hours',
        'train_certificate',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at field in current schema
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'train_name' => 'required|min_length[3]',
        'train_start_date' => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
