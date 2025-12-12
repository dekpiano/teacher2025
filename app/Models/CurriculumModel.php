<?php

namespace App\Models;

use CodeIgniter\Model;

class CurriculumModel extends Model
{
    protected $DBGroup = 'default'; // Default DB for tb_send_plan and tb_send_plan_setup
    protected $table = 'tb_send_plan';
    protected $primaryKey = 'seplan_ID';
    protected $allowedFields = [
        'seplan_namesubject', 'seplan_coursecode', 'seplan_typesubject', 'seplan_year',
        'seplan_term', 'seplan_usersend', 'seplan_learning', 'seplan_status1',
        'seplan_status2', 'seplan_sendcomment', 'seplan_gradelevel', 'seplan_typeplan',
        'seplan_typeplan_id',
        'seplan_file', 'seplan_createdate', 'seplan_checkdate1', 'seplan_inspector1',
        'seplan_comment1', 'seplan_checkdate2', 'seplan_inspector2', 'seplan_comment2',
        'seplan_is_main_subject'
    ];

    // For tb_send_plan_setup, we'll interact directly via db_connect() in controller or a separate model if it grows.
    // For now, direct DB calls for setup table are fine.

    // Custom update method for plan settings by teacher (multiple fields)
    public function planSettingUpdateTeacher($data, $courseCode, $year, $term)
    {
        return $this->where(['seplan_coursecode' => $courseCode, 'seplan_year' => $year, 'seplan_term' => $term])->update(null, $data);
    }

    // Custom delete method for plan settings by teacher (multiple fields) with file deletion
    public function planSettingDeleteTeacher($courseCode, $term, $year, $name)
    {
        $db = db_connect();
        $result = $this->where(['seplan_coursecode' => $courseCode, 'seplan_year' => $year, 'seplan_term' => $term])->delete();

        if ($result) {
            // Attempt to delete associated directory and files
            $folder = $year . '/' . $term;
            $dir_path = FCPATH . 'uploads/academic/course/plan/' . $folder . '/' . $name;
            
            if (is_dir($dir_path)) {
                // Helper function to delete directory and its contents
                $this->deleteDirectory($dir_path);
            }
        }
        return $result;
    }

    // Helper to delete directory recursively
    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }

    // Update status methods
    public function updatePlanStatus($id, $data)
    {
        return $this->update($id, $data);
    }

    // Update comment methods
    public function updatePlanComment($id, $data)
    {
        return $this->update($id, $data);
    }

    public function setTeacherMainSubject($personId, $courseCode, $year, $term)
    {
        // Set all subjects for this teacher, year, and term to not be main (0)
        $this->where('seplan_usersend', $personId)
             ->where('seplan_year', $year)
             ->where('seplan_term', $term)
             ->set(['seplan_is_main_subject' => 0])
             ->update();

        // Set the selected subject to be main (1)
        return $this->where('seplan_usersend', $personId)
                    ->where('seplan_coursecode', $courseCode)
                    ->where('seplan_year', $year)
                    ->where('seplan_term', $term)
                    ->set(['seplan_is_main_subject' => 1])
                    ->update();
    }
}
