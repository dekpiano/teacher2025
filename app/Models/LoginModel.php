<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginModel extends Model
{
    protected $DBGroup          = 'personnel';
    protected $table            = 'tb_personnel';
    protected $primaryKey       = 'pers_id';
    protected $allowedFields    = ['login_oauth_uid', 'updated_at'];

    public function checkLogin($username, $password)
    {
        $db_skj = \Config\Database::connect('skj');
        return $this->select('tb_personnel.pers_id, tb_personnel.pers_username, CONCAT(tb_personnel.pers_prefix, tb_personnel.pers_firstname, " ", tb_personnel.pers_lastname) as fullname, tb_personnel.pers_img, ' . $db_skj->database . '.tb_position.posi_name as position')
                     ->join($db_skj->database . '.tb_position', $db_skj->database . '.tb_position.posi_id = tb_personnel.pers_position', 'left')
                     ->where('tb_personnel.pers_username', $username)
                     ->where('tb_personnel.pers_password', $password)
                     ->where('tb_personnel.pers_status', 'กำลังใช้งาน')
                     ->first();
    }

    public function checkGoogleLogin($email)
    {
        $db_skj = \Config\Database::connect('skj');
        return $this->select('tb_personnel.pers_id, tb_personnel.pers_username, CONCAT(tb_personnel.pers_prefix, tb_personnel.pers_firstname, " ", tb_personnel.pers_lastname) as fullname, tb_personnel.pers_img, tb_personnel.pers_groupleade, tb_personnel.pers_learning, ' . $db_skj->database . '.tb_position.posi_name as position')
                     ->join($db_skj->database . '.tb_position', $db_skj->database . '.tb_position.posi_id = tb_personnel.pers_position', 'left')
                     ->where('tb_personnel.pers_username', $email)
                     //->where('pers_status', 'กำลังใช้งาน')
                     ->first();
    }

    public function updateGoogleUserData($email, $oauth_uid)
    {
        $data = [
            'login_oauth_uid' => $oauth_uid,
            'updated_at'      => date('Y-m-d H:i:s')
        ];
        return $this->where('pers_username', $email)->set($data)->update();
    }
}
