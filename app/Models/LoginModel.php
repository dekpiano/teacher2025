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
        return $this->select('pers_id, pers_username, CONCAT(pers_firstname, " ", pers_lastname) as fullname, pers_img')
                     ->where('pers_username', $username)
                     ->where('pers_password', $password)
                     ->where('pers_status', 'กำลังใช้งาน')
                     ->first();
    }

    public function checkGoogleLogin($email)
    {
        return $this->select('pers_id, pers_username, CONCAT(pers_firstname, " ", pers_lastname) as fullname, pers_img,pers_groupleade,pers_learning')
                     ->where('pers_username', $email)
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
