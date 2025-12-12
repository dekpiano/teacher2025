<?php

namespace App\Controllers;

use App\Models\LoginModel;
use Google\Client;
use Google\Service\Oauth2;

class Login extends BaseController
{
    protected $googleClient;

    public function __construct()
    {
        //$path = (dirname(dirname(dirname(dirname((dirname(__FILE__)))))));
		//require SHARED_LIB_PATH.'/google_sheet/vendor/autoload.php';

        // $path = (dirname(dirname(dirname(dirname((dirname(__FILE__)))))));
		// require $path . '/librarie_skj/google_sheet/vendor/autoload.php';
        // require SHARED_LIB_PATH . '/google_sheet/vendor/autoload.php';
        require SHARED_LIB_PATH . '/google_sheet/vendor/autoload.php';

        $this->googleClient = new Client();
        $this->googleClient->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $this->googleClient->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $this->googleClient->setRedirectUri(getenv('GOOGLE_REDIRECT_URI'));
        $this->googleClient->addScope('email');
        $this->googleClient->addScope('profile');
    }

    public function index()
    {
        // If user is already logged in, redirect to home page.
        if (session()->get('isLoggedIn')) {
            return redirect()->to('home');
        }
        $data['google_login_url'] = $this->googleClient->createAuthUrl();
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
        return redirect()->to($this->googleClient->createAuthUrl());
    }

    public function googleCallback()
    {
        $session = session();
        $model = new LoginModel();

        if (isset($_GET['code'])) {
            $token = $this->googleClient->fetchAccessTokenWithAuthCode($_GET['code']);

            if (!isset($token['error'])) {
                $this->googleClient->setAccessToken($token['access_token']);
                $googleService = new Oauth2($this->googleClient);
                $userData = $googleService->userinfo->get();

                $email = $userData['email'];

                // Check if user exists in database
                $user = $model->checkGoogleLogin($email);

                
                if ($user) {
                    // Update user's OAuth UID and last updated time
                    $model->updateGoogleUserData($email, $userData['id']);

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
                    $session->setFlashdata('msg', 'ระบบนี้ใช้ได้แค่อีเมลโรงเรียน @skj.ac.th ที่ลงทะเบียนเท่านั้น กรุณาติดต่อเจ้าหน้าที่คอม');
                    return redirect()->to('login');
                }
            } else {
                // Error fetching access token
                $session->setFlashdata('msg', 'เกิดข้อผิดพลาดในการเข้าสู่ระบบด้วย Google');
                return redirect()->to('login');
            }
        } else {
            // No code received
            $session->setFlashdata('msg', 'ไม่ได้รับรหัสการอนุญาตจาก Google');
            return redirect()->to('login');
        }
    }
}
