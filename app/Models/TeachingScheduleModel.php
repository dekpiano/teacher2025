<?php

namespace App\Models;

use CodeIgniter\Model;

class TeachingScheduleModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'tb_teaching_schedule';
    protected $primaryKey = 'schedule_id';
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'teacher_id', 
        'subject_code', 
        'subject_name', 
        'subject_type', 
        'credit', 
        'hours_per_week', 
        'grade_level', 
        'room', 
        'study_plan',
        'total_hours', 
        'remark', 
        'year', 
        'term'
    ];

    public function getSchedulesByTerm($year, $term)
    {
        // Join with personnel to get teacher name
        $db = db_connect();
        $builder = $db->table($this->table);
        $builder->select('tb_teaching_schedule.*, tb_personnel.pers_prefix, tb_personnel.pers_firstname, tb_personnel.pers_lastname');
        $builder->join('skjacth_personnel.tb_personnel', 'skjacth_personnel.tb_personnel.pers_id COLLATE utf8_general_ci = tb_teaching_schedule.teacher_id COLLATE utf8_general_ci', 'left', false);
        $builder->where('year', $year);
        $builder->where('term', $term);
        // Order by teacher name, then subject code
        $builder->orderBy('tb_personnel.pers_firstname', 'ASC');
        $builder->orderBy('subject_code', 'ASC');
        
        $query = $builder->get();
        return $query->getResultArray();
    }
}
