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
        'subject_id',
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
        // Join with personnel and tb_subjects for live curriculum sync
        $db = db_connect();
        $builder = $db->table($this->table);
        $builder->select('
            tb_teaching_schedule.*,
            COALESCE(sub.SubjectUnit, tb_teaching_schedule.credit) as credit,
            COALESCE(sub.SubjectName, tb_teaching_schedule.subject_name) as subject_name,
            COALESCE(sub.SubjectType, tb_teaching_schedule.subject_type) as subject_type,
            COALESCE(sub.SubjectHour, tb_teaching_schedule.total_hours) as total_hours,
            tb_teaching_schedule.hours_per_week as hours_per_week,
            tb_personnel.pers_prefix, tb_personnel.pers_firstname, tb_personnel.pers_lastname, 
            tb_personnel.pers_learning, tb_personnel.pers_groupleade, tb_personnel.pers_numberGroup, 
            tb_personnel.pers_img
        ');
        $builder->join('tb_subjects sub', 'sub.SubjectID = tb_teaching_schedule.subject_id', 'left');
        $builder->join('skjacth_personnel.tb_personnel', 'skjacth_personnel.tb_personnel.pers_id COLLATE utf8_general_ci = tb_teaching_schedule.teacher_id COLLATE utf8_general_ci', 'left', false);
        $builder->where('tb_teaching_schedule.year', $year);
        $builder->where('tb_teaching_schedule.term', $term);
        if (!empty($learning)) {
            $builder->where('skjacth_personnel.tb_personnel.pers_learning', $learning);
        }
        // Order by department head first, then sequence number in group, then teacher name, then subject code
        $builder->orderBy("CASE WHEN skjacth_personnel.tb_personnel.pers_groupleade LIKE '%หัวหน้ากลุ่มสาระ%' OR skjacth_personnel.tb_personnel.pers_groupleade = '1' THEN 0 ELSE 1 END", 'ASC', false);
        $builder->orderBy('skjacth_personnel.tb_personnel.pers_numberGroup', 'ASC');
        $builder->orderBy('skjacth_personnel.tb_personnel.pers_firstname', 'ASC');
        $builder->orderBy('tb_teaching_schedule.subject_code', 'ASC');
        
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getTeacherSchedulesWithSubjects($teacherId, $year, $term)
    {
        $db = db_connect();
        $builder = $db->table($this->table);
        $builder->select('
            tb_teaching_schedule.*,
            COALESCE(sub.SubjectUnit, tb_teaching_schedule.credit) as credit,
            COALESCE(sub.SubjectName, tb_teaching_schedule.subject_name) as subject_name,
            COALESCE(sub.SubjectType, tb_teaching_schedule.subject_type) as subject_type,
            COALESCE(sub.SubjectHour, tb_teaching_schedule.total_hours) as total_hours,
            tb_teaching_schedule.hours_per_week as hours_per_week
        ');
        $builder->join('tb_subjects sub', 'sub.SubjectID = tb_teaching_schedule.subject_id', 'left');
        $builder->where('tb_teaching_schedule.teacher_id', $teacherId);
        $builder->where('tb_teaching_schedule.year', $year);
        $builder->where('tb_teaching_schedule.term', $term);
        $builder->orderBy('tb_teaching_schedule.grade_level', 'ASC');
        $builder->orderBy('tb_teaching_schedule.subject_code', 'ASC');
        return $builder->get()->getResultArray();
    }
}
