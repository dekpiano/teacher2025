<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $session = session();

        // Check if user is not logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        // Database connections
        $db = db_connect(); // Default database connection
        $db_personnel = db_connect('personnel');
        $db_affairs = db_connect('affairs');

        // --- Get Latest Year/Term ---
        $latestYear = null;
        $allEntries = $db->table('tb_schoolyear')->select('schyear_year')->get()->getResultArray();
        if (!empty($allEntries)) {
            usort($allEntries, function($a, $b) {
                list($termA, $yearA) = explode('/', $a['schyear_year']);
                list($termB, $yearB) = explode('/', $b['schyear_year']);
                if ($yearB != $yearA) {
                    return $yearB <=> $yearA;
                }
                return $termB <=> $termA;
            });
            $latestEntry = $allEntries[0]['schyear_year'];
            $parts = explode('/', $latestEntry);
            $latestYear = $parts[1] ?? date('Y') + 543;
        } else {
            $latestYear = date('Y') + 543; // Fallback
        }

        // --- Get Homeroom Class ---
        $homeroomClass = null;
        if ($latestYear) {
            $homeroomClass = $db->table('tb_regclass')
                                ->select('Reg_Class')
                                ->where('class_teacher', $session->get('person_id'))
                                ->where('Reg_Year', $latestYear)
                                ->get()
                                ->getRow();
        }

        // Fetch other data
        $CheckHomeVisitManager = $db_affairs->table('tb_homevisit_setting')
                                            ->where('homevisit_set_id', 1)
                                            ->get()
                                            ->getRow();

        $OnOff = $db->table('tb_send_plan_setup')->get()->getResult();

        $teacher = $db_personnel->table('tb_personnel')
                                ->select('pers_id, pers_img')
                                ->where('pers_id', $session->get('person_id'))
                                ->get()
                                ->getResult();

        // Prepare data for the view
        $data = [
            'title'                 => 'หน้าแรก',
            'CheckHomeVisitManager' => $CheckHomeVisitManager,
            'OnOff'                 => $OnOff,
            'teacher'               => $teacher,
            'homeroomClass'         => $homeroomClass
        ];

        // Load the view, which will in turn use the main layout
        return view('teacher/home/index', $data);
    }
}
