<?php

namespace App\Models;

use CodeIgniter\Model;

class PaAgreementModel extends Model
{
    protected $DBGroup          = 'personnel';
    protected $table            = 'tb_teacher_pa_agreement';
    protected $primaryKey       = 'pa_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pa_teacher_id',
        'pa_year',
        'pa_presentation_link',
        'pa_file_presentation',
        'pa_file_lesson_plan',
        'pa_file_pa1',
        'pa_status',
        'pa_comment'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'pa_created_at';
    protected $updatedField  = 'pa_updated_at';

    /**
     * Ensure table exists in database
     */
    public function ensureTableExists()
    {
        $db = \Config\Database::connect('personnel');
        $sql = "CREATE TABLE IF NOT EXISTS `tb_teacher_pa_agreement` (
            `pa_id` INT(11) NOT NULL AUTO_INCREMENT,
            `pa_teacher_id` VARCHAR(50) NOT NULL,
            `pa_year` VARCHAR(10) NOT NULL,
            `pa_presentation_link` TEXT NULL,
            `pa_file_presentation` VARCHAR(255) NULL,
            `pa_file_lesson_plan` VARCHAR(255) NULL,
            `pa_file_pa1` VARCHAR(255) NULL,
            `pa_status` VARCHAR(50) NULL DEFAULT 'submitted',
            `pa_comment` TEXT NULL,
            `pa_created_at` DATETIME NULL,
            `pa_updated_at` DATETIME NULL,
            PRIMARY KEY (`pa_id`),
            KEY `idx_teacher_year` (`pa_teacher_id`, `pa_year`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        try {
            $db->query($sql);
        } catch (\Exception $e) {
            log_message('error', 'Error creating tb_teacher_pa_agreement: ' . $e->getMessage());
        }
    }

    /**
     * Get PA agreement by teacher and fiscal year
     */
    public function getAgreement($teacherId, $year)
    {
        $this->ensureTableExists();
        return $this->where([
            'pa_teacher_id' => $teacherId,
            'pa_year'       => $year
        ])->first();
    }

    /**
     * Get all submission history for a teacher
     */
    public function getHistory($teacherId)
    {
        $this->ensureTableExists();
        return $this->where('pa_teacher_id', $teacherId)
                    ->orderBy('pa_year', 'DESC')
                    ->orderBy('pa_created_at', 'DESC')
                    ->findAll();
    }
}
