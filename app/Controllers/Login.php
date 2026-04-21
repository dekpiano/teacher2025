<?php

namespace App\Controllers;

use App\Models\LoginModel;

class Login extends BaseController
{
    public function __construct()
    {
    }

    public function index()
    {
        // If user is already logged in, redirect to home page.
        if (session()->get('isLoggedIn')) {
            return redirect()->to('home');
        }

        $config = config('Google');
        $params = [
            'client_id'     => $config->clientId,
            'redirect_uri'  => base_url('login/googleCallback'),
            'response_type' => 'code',
            'scope'         => 'email profile openid',
            'access_type'   => 'online',
            'prompt'        => 'select_account'
        ];
        $data['google_login_url'] = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

        return view('login/index', $data);
    }

    public function authenticate()
    {
        $session = session();
        $model = new LoginModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $model->checkLogin($username, $password);

        if ($user) {
            $ses_data = [
                'person_id'      => $user['pers_id'],
                'gmail_account'  => $user['pers_username'],
                'fullname'       => $user['fullname'],
                'person_img'     => $user['pers_img'],
                'isLoggedIn'     => TRUE
            ];
            $session->set($ses_data);
            return $this->response->setJSON(['success' => true, 'message' => 'Login successful']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }

    public function googleLogin()
    {
        $config = config('Google');
        $params = [
            'client_id'     => $config->clientId,
            'redirect_uri'  => base_url('login/googleCallback'),
            'response_type' => 'code',
            'scope'         => 'email profile openid',
            'access_type'   => 'online',
            'prompt'        => 'select_account'
        ];
        return redirect()->to('https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
    }

    public function googleCallback()
    {
        $session = session();
        $model = new LoginModel();
        
        $code = $this->request->getVar('code');
        if (!$code) {
            $session->setFlashdata('msg', 'ไม่ได้รับรหัสการอนุญาตจาก Google');
            return redirect()->to('login');
        }

        $config = config('Google');
        $curl = \Config\Services::curlrequest();

        try {
            // 1. Exchange code for access_token and id_token
            $response = $curl->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'code'          => $code,
                    'client_id'     => $config->clientId,
                    'client_secret' => $config->clientSecret,
                    'redirect_uri'  => base_url('login/googleCallback'),
                    'grant_type'    => 'authorization_code',
                ],
            ]);
            $tokens = json_decode($response->getBody(), true);

            if (isset($tokens['error']) || !isset($tokens['access_token'])) {
                 $session->setFlashdata('msg', 'Google Token Error: ' . ($tokens['error_description'] ?? $tokens['error'] ?? 'ไม่ได้รับ access_token'));
                 return redirect()->to('login');
            }

            // 2. Get user profile using access_token
            $response = $curl->get('https://www.googleapis.com/oauth2/v3/userinfo', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $tokens['access_token'],
                ],
            ]);
            $userData = json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            $session->setFlashdata('msg', 'การเชื่อมต่อกับ Google ล้มเหลว: ' . $e->getMessage());
            return redirect()->to('login');
        }

        if (!$userData || !isset($userData['email'])) {
            $session->setFlashdata('msg', 'ไม่สามารถดึงข้อมูลอีเมลจาก Google ได้');
            return redirect()->to('login');
        }

        $email = $userData['email'];

        // Check if user exists in database
        $user = $model->checkGoogleLogin($email);

        if ($user) {
            // Update user's OAuth UID and last updated time
            $model->updateGoogleUserData($email, $userData['sub']);

            $ses_data = [
                'person_id'      => $user['pers_id'],
                'gmail_account'  => $user['pers_username'],
                'fullname'       => $user['fullname'],
                'person_img'     => $user['pers_img'],
                'pers_groupleade' => $user['pers_groupleade'],
                'pers_learning' => $user['pers_learning'],
                'isLoggedIn'     => TRUE
            ];
            $session->set($ses_data);

            return redirect()->to('home');
        } else {
            // User not found or not allowed
            $session->setFlashdata('msg', "ไม่พบอีเมล $email ในระบบ หรือคุณไม่มีสิทธิ์เข้าใช้งาน กรุณาติดต่อเจ้หน้าที่คอม");
            return redirect()->to('login');
        }
    }
}
