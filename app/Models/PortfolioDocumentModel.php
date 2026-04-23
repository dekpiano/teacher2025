<?php

namespace App\Models;

use CodeIgniter\Model;

class PortfolioDocumentModel extends Model
{
    protected $DBGroup          = 'personnel';
    protected $table            = 'tb_personnel_documents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pers_id',
        'doc_category',
        'doc_type',
        'doc_title',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'related_id',
        'doc_date',
        'doc_reference',
        'doc_note',
        'uploaded_by',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'doc_title' => 'required',
        'doc_category' => 'required'
    ];
}
