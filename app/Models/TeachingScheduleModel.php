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

    public function getSchedulesByTerm($year, $term, $learning = null)
    {
        // Join with personnel to get teacher name and department
        $db = db_connect();
        $builder = $db->table($this->table);
        $builder->select('tb_teaching_schedule.*, tb_personnel.pers_prefix, tb_personnel.pers_firstname, tb_personnel.pers_lastname, tb_personnel.pers_learning, tb_personnel.pers_groupleade, tb_personnel.pers_numberGroup, tb_personnel.pers_img');
        $builder->join('skjacth_personnel.tb_personnel', 'skjacth_personnel.tb_personnel.pers_id COLLATE utf8_general_ci = tb_teaching_schedule.teacher_id COLLATE utf8_general_ci', 'left', false);
        $builder->where('year', $year);
        $builder->where('term', $term);
        if (!empty($learning)) {
            $builder->where('skjacth_personnel.tb_personnel.pers_learning', $learning);
        }
        // Order by department head first, then sequence number in group, then teacher name, then subject code
        $builder->orderBy("CASE WHEN skjacth_personnel.tb_personnel.pers_groupleade LIKE '%หัวหน้ากลุ่มสาระ%' OR skjacth_personnel.tb_personnel.pers_groupleade = '1' THEN 0 ELSE 1 END", 'ASC', false);
        $builder->orderBy('skjacth_personnel.tb_personnel.pers_numberGroup', 'ASC');
        $builder->orderBy('skjacth_personnel.tb_personnel.pers_firstname', 'ASC');
        $builder->orderBy('subject_code', 'ASC');
        
        $query = $builder->get();
        return $query->getResultArray();
    }
}
