<?php

namespace App\Controllers;

use App\Models\TeachingScheduleModel;
use CodeIgniter\HTTP\ResponseInterface;

class TeachingScheduleController extends BaseController
{
    protected $teachingScheduleModel;
    protected $db_personnel;
    protected $db_skj;
    protected $db;
    protected $session;
    protected $setup;

    public function __construct()
    {
        $this->session = session();
        if (!is_cli() && !$this->session->get('isLoggedIn')) {
            service('response')->redirect(base_url('login'))->send();
            exit;
        }

        $this->response ??= service('response');
        $this->request  ??= service('request');

        $this->teachingScheduleModel = new TeachingScheduleModel();
        $this->db = db_connect();
        $this->db_personnel = db_connect('personnel');
        try {
            $this->db_skj = db_connect('skj');
        } catch (\Throwable $e) {
            $this->db_skj = null;
        }
        
        // Get current year and term setup
        $this->setup = $this->db->table('tb_send_plan_setup')->get()->getRow();
    }

    /**
     * Get system Open/Close status for Teaching Schedule (จัดการวิชาเรียน) from tb_register_onoff
     */
    protected function getScheduleOnOff(): array
    {
        $onoffRow = $this->db->table('tb_register_onoff')
            ->where('onoff_id', 16)
            ->get()->getRow();

        if (!$onoffRow) {
            $onoffRow = $this->db->table('tb_register_onoff')
                ->like('onoff_name', 'จัดการวิชาเรียน')
                ->orLike('onoff_name', 'ตารางสอน')
                ->get()->getRow();
        }

        $status = $onoffRow ? ($onoffRow->onoff_status ?? 'off') : 'on';
        $yearTerm = $onoffRow->onoff_year ?? null;

        $defaultTerm = null;
        $defaultYear = null;
        if (!empty($yearTerm) && strpos($yearTerm, '/') !== false) {
            $parts = explode('/', $yearTerm);
            $defaultTerm = trim($parts[0]);
            $defaultYear = trim($parts[1]);
        }

        // ดูเฉพาะสถานะ onoff_status ว่า เปิด ('on'/'true'/'1') หรือ ปิด ('off')
        $isOpen = (strtolower($status) === 'on' || strtolower($status) === 'true' || $status === '1');

        return [
            'row'          => $onoffRow,
            'is_open'      => $isOpen,
            'status'       => $status,
            'start_date'   => $onoffRow->onoff_StartDate ?? null,
            'end_date'     => $onoffRow->onoff_EndDate ?? null,
            'default_term' => $defaultTerm,
            'default_year' => $defaultYear,
        ];
    }

    protected function ensureTablesExist()
    {
        $forge = \Config\Database::forge();
        if (!$this->db->tableExists('tb_teaching_schedule')) {
            $fields = [
                'schedule_id'    => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'teacher_id'     => ['type' => 'VARCHAR', 'constraint' => 20],
                'subject_code'   => ['type' => 'VARCHAR', 'constraint' => 20],
                'subject_name'   => ['type' => 'VARCHAR', 'constraint' => 150],
                'subject_type'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'credit'         => ['type' => 'DECIMAL', 'constraint' => '3,1', 'null' => true],
                'hours_per_week' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'grade_level'    => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'room'           => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'study_plan'     => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'total_hours'    => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'remark'         => ['type' => 'TEXT', 'null' => true],
                'year'           => ['type' => 'VARCHAR', 'constraint' => 4],
                'term'           => ['type' => 'VARCHAR', 'constraint' => 1],
            ];
            $forge->addField($fields);
            $forge->addKey('schedule_id', true);
            $forge->createTable('tb_teaching_schedule', true);
        } else {
            if (!$this->db->fieldExists('study_plan', 'tb_teaching_schedule')) {
                $forge->addColumn('tb_teaching_schedule', [
                    'study_plan' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 150,
                        'null'       => true,
                        'after'      => 'room'
                    ]
                ]);
            }
        }

        // tb_teaching_schedule_activity
        if (!$this->db->tableExists('tb_teaching_schedule_activity')) {
            $forge->addField([
                'activity_id'    => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'teacher_id'     => ['type' => 'VARCHAR', 'constraint' => 20],
                'activity_name'  => ['type' => 'VARCHAR', 'constraint' => 150],
                'hours_per_week' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => 1],
                'total_hours'    => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => 20],
                'grade_level'    => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'room'           => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'remark'         => ['type' => 'TEXT', 'null' => true],
                'year'           => ['type' => 'VARCHAR', 'constraint' => 4],
                'term'           => ['type' => 'VARCHAR', 'constraint' => 1],
            ]);
            $forge->addKey('activity_id', true);
            $forge->createTable('tb_teaching_schedule_activity', true);
        }

        // tb_teaching_schedule_duty
        if (!$this->db->tableExists('tb_teaching_schedule_duty')) {
            $forge->addField([
                'duty_id'    => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'teacher_id' => ['type' => 'VARCHAR', 'constraint' => 20],
                'duty_name'  => ['type' => 'VARCHAR', 'constraint' => 255],
                'duty_order' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => 1],
                'year'       => ['type' => 'VARCHAR', 'constraint' => 4],
                'term'       => ['type' => 'VARCHAR', 'constraint' => 1],
            ]);
            $forge->addKey('duty_id', true);
            $forge->createTable('tb_teaching_schedule_duty', true);
        }
    }

    public function index($year = null, $term = null)
    {
        $this->ensureTablesExist();

        // Track page visit
        helper('recent_pages');
        track_recent_page('curriculum/teaching-schedule', 'จัดตารางสอนของกลุ่มสาระ', 'bi-calendar3');

        $data['title'] = "จัดตารางสอนของกลุ่มสาระ";
        $data['OnOff'] = [$this->setup];

        $scheduleOnOff = $this->getScheduleOnOff();
        $data['schedule_onoff'] = $scheduleOnOff;
        $data['is_open'] = $scheduleOnOff['is_open'];
        $data['onoff_row'] = $scheduleOnOff['row'];

        if ($year === null || $term === null) {
            if (!empty($scheduleOnOff['default_year']) && !empty($scheduleOnOff['default_term'])) {
                $year = $scheduleOnOff['default_year'];
                $term = $scheduleOnOff['default_term'];
            } elseif ($this->setup && !empty($this->setup->seplanset_year) && !empty($this->setup->seplanset_term)) {
                $year = $this->setup->seplanset_year;
                $term = $this->setup->seplanset_term;
            } else {
                $year = date('Y') + 543;
                $term = 1;
            }
        }

        $data['current_year'] = $year;
        $data['current_term'] = $term;

        // Fetch distinct study plans (English abbreviations) grouped from tb_students.StudentStudyLine
        $data['study_plans'] = $this->getStudentStudyPlans();
        $data['class_room_map'] = $this->getStudentClassRoomMap();

        // Get user's learning department from database using person_id
        $person_id = $this->session->get('person_id');
        $user_personnel = $this->db_personnel->table('tb_personnel')->select('pers_learning')->where('pers_id', $person_id)->get()->getRow();
        $pers_learning = $user_personnel ? $user_personnel->pers_learning : '';

        // Fetch teachers list for the dropdown (only in the same learning department and currently active)
        $data['teachers'] = $this->db_personnel->table('tb_personnel')
                                ->select('pers_id, pers_prefix, pers_firstname, pers_lastname, pers_learning')
                                ->where('pers_position >=', 'posi_003')
                                ->where('pers_position <=', 'posi_006')
                                ->where('pers_learning', $pers_learning)
                                ->where('pers_status', 'กำลังใช้งาน')
                                ->orderBy('pers_learning', 'ASC')
                                ->orderBy('pers_firstname', 'ASC')
                                ->get()->getResultArray();

        // Fetch schedules
        $schedules = $this->teachingScheduleModel->getSchedulesByTerm($year, $term);

        // Fetch activities for this year and term
        $activities = $this->db->table('tb_teaching_schedule_activity')
            ->where('year', $year)
            ->where('term', $term)
            ->orderBy('activity_id', 'ASC')
            ->get()->getResultArray();
        $teacherActivities = [];
        foreach ($activities as $act) {
            $teacherActivities[$act['teacher_id']][] = $act;
        }

        // Fetch duties for this year and term
        $duties = $this->db->table('tb_teaching_schedule_duty')
            ->where('year', $year)
            ->where('term', $term)
            ->orderBy('duty_order', 'ASC')
            ->orderBy('duty_id', 'ASC')
            ->get()->getResultArray();
        $teacherDuties = [];
        foreach ($duties as $dt) {
            $teacherDuties[$dt['teacher_id']][] = $dt;
        }
        
        // Group schedules by teacher, then group by subject (subject_code + grade_level)
        $groupedSchedules = [];
        foreach ($schedules as $schedule) {
            $teacherId = $schedule['teacher_id'];
            if (!isset($groupedSchedules[$teacherId])) {
                $groupedSchedules[$teacherId] = [
                    'teacher_id'         => $teacherId,
                    'teacher_name'       => trim($schedule['pers_prefix'] . $schedule['pers_firstname'] . ' ' . $schedule['pers_lastname']),
                    'subjects'           => [],
                    'schedules'          => [],
                    'total_weekly_hours' => 0,
                    'total_rooms'        => 0
                ];
            }

            $groupKey = trim($schedule['subject_code']) . '_' . trim($schedule['grade_level']);
            if (!isset($groupedSchedules[$teacherId]['subjects'][$groupKey])) {
                $groupedSchedules[$teacherId]['subjects'][$groupKey] = [
                    'group_key'          => $groupKey,
                    'schedule_id'        => $schedule['schedule_id'],
                    'schedule_ids'       => [],
                    'teacher_id'         => $teacherId,
                    'subject_code'       => $schedule['subject_code'],
                    'subject_name'       => $schedule['subject_name'],
                    'subject_type'       => $schedule['subject_type'],
                    'credit'             => $schedule['credit'],
                    'hours_per_week'     => (float)($schedule['hours_per_week'] ?? 0),
                    'single_total_hours' => (int)($schedule['total_hours'] ?? 0),
                    'grade_level'        => $schedule['grade_level'],
                    'rooms'              => [],
                    'study_plans'        => [],
                    'room_plans'         => [],
                    'total_hours'        => 0,
                    'remarks'            => []
                ];
            }

            $sub = &$groupedSchedules[$teacherId]['subjects'][$groupKey];
            $sub['schedule_ids'][] = (int)$schedule['schedule_id'];
            $roomStr = trim((string)$schedule['room']);
            if ($roomStr !== '' && !in_array($roomStr, $sub['rooms'], true)) {
                $sub['rooms'][] = $roomStr;
            }
            $planStr = trim((string)($schedule['study_plan'] ?? ''));
            if ($planStr !== '' && !in_array($planStr, $sub['study_plans'], true)) {
                $sub['study_plans'][] = $planStr;
            }
            if ($roomStr !== '') {
                $sub['room_plans'][$roomStr] = $planStr;
            }
            $sub['total_hours'] += (int)($schedule['total_hours'] ?? 0);
            $remStr = trim((string)($schedule['remark'] ?? ''));
            if ($remStr !== '' && !in_array($remStr, $sub['remarks'], true)) {
                $sub['remarks'][] = $remStr;
            }
            unset($sub);
        }

        // Post-process each teacher's grouped subjects
        foreach ($groupedSchedules as $tId => &$tData) {
            foreach ($tData['subjects'] as &$sub) {
                // Sort rooms naturally
                usort($sub['rooms'], function($a, $b) {
                    $nA = is_numeric($a) ? (int)$a : null;
                    $nB = is_numeric($b) ? (int)$b : null;
                    if ($nA !== null && $nB !== null) return $nA - $nB;
                    return strnatcasecmp($a, $b);
                });

                $sub['room_count'] = max(count($sub['rooms']), 1);
                $sub['room_range_text'] = $this->formatRoomRange($sub['rooms']);
                $sub['room_text'] = implode(', ', $sub['rooms']);
                $sub['distinct_plans'] = array_values(array_unique(array_filter($sub['study_plans'])));
                $sub['total_weekly_hours'] = $sub['hours_per_week'] * $sub['room_count'];

                $tData['total_weekly_hours'] += $sub['total_weekly_hours'];
                $tData['total_rooms'] += $sub['room_count'];
            }
            $tData['subjects'] = array_values($tData['subjects']);
            $tData['schedules'] = $tData['subjects']; // Backward-compatible alias

            // Attach activities and duties
            $tData['activities'] = $teacherActivities[$tId] ?? [];
            $tData['duties'] = $teacherDuties[$tId] ?? [];
            $tData['total_subject_hours'] = $tData['total_weekly_hours'];
            $tData['total_activity_weekly_hours'] = 0;
            foreach ($tData['activities'] as $act) {
                $tData['total_activity_weekly_hours'] += (float)($act['hours_per_week'] ?? 0);
            }
            $tData['grand_total_weekly_hours'] = $tData['total_subject_hours'] + $tData['total_activity_weekly_hours'];
        }
        unset($tData);

        // Also include teachers who have activities or duties but no subjects yet
        $allExtraTeachers = array_unique(array_merge(array_keys($teacherActivities), array_keys($teacherDuties)));
        foreach ($allExtraTeachers as $extraTId) {
            if (!isset($groupedSchedules[$extraTId])) {
                $tName = $extraTId;
                foreach ($data['teachers'] as $tch) {
                    if ($tch['pers_id'] === $extraTId) {
                        $tName = trim($tch['pers_prefix'] . $tch['pers_firstname'] . ' ' . $tch['pers_lastname']);
                        break;
                    }
                }
                $actList = $teacherActivities[$extraTId] ?? [];
                $actHours = 0;
                foreach ($actList as $act) {
                    $actHours += (float)($act['hours_per_week'] ?? 0);
                }
                $groupedSchedules[$extraTId] = [
                    'teacher_id'                 => $extraTId,
                    'teacher_name'               => $tName,
                    'subjects'                   => [],
                    'schedules'                  => [],
                    'total_weekly_hours'         => 0,
                    'total_rooms'                => 0,
                    'activities'                 => $actList,
                    'duties'                     => $teacherDuties[$extraTId] ?? [],
                    'total_subject_hours'        => 0,
                    'total_activity_weekly_hours'=> $actHours,
                    'grand_total_weekly_hours'   => $actHours,
                ];
            }
        }

        $data['groupedSchedules'] = $groupedSchedules;

        return view('teacher/curriculum/schedule_main', $data);
    }

    public function save()
    {
        $scheduleOnOff = $this->getScheduleOnOff();
        if (!$scheduleOnOff['is_open']) {
            return $this->response->setJSON([
                'status' => 'error', 
                'msg'    => 'ระบบปิดการจัดตารางสอนแล้ว ไม่สามารถบันทึกหรือแก้ไขข้อมูลได้'
            ]);
        }

        $post = $this->request->getPost();
        
        $teacherId = $post['teacher_id'] ?? '';
        $year      = $post['year'] ?? '';
        $term      = $post['term'] ?? '';

        if (empty($teacherId)) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'กรุณาเลือกครูผู้สอน']);
        }

        $classRoomMap = $this->getStudentClassRoomMap();
        $batchData = [];

        // Support indexed arrays (e.g. subject_code[], subject_name[])
        if (isset($post['subject_code']) && is_array($post['subject_code'])) {
            foreach ($post['subject_code'] as $i => $code) {
                $cleanCode = trim($code);
                if (!empty($cleanCode)) {
                    $rawRooms = $post['room'][$i] ?? '';
                    $rooms = $this->expandRooms($rawRooms);
                    if (empty($rooms)) $rooms = [$rawRooms];

                    $selectedPlan = trim($post['study_plan'][$i] ?? '');
                    $gradeLevel   = trim($post['grade_level'][$i] ?? '');
                    $subName      = trim($post['subject_name'][$i] ?? '');
                    $subType      = $post['subject_type'][$i] ?? 'พื้นฐาน';
                    $credit       = !empty($post['credit'][$i]) ? (float)$post['credit'][$i] : 0;
                    $hpw          = !empty($post['hours_per_week'][$i]) ? (int)$post['hours_per_week'][$i] : 0;
                    $totalH       = !empty($post['total_hours'][$i]) ? (int)$post['total_hours'][$i] : 0;
                    $remark       = trim($post['remark'][$i] ?? '');

                    foreach ($rooms as $singleRoom) {
                        $singlePlan = $this->resolveStudyPlan($selectedPlan, $gradeLevel, (string)$singleRoom, $classRoomMap);

                        $batchData[] = [
                            'teacher_id'     => $teacherId,
                            'subject_code'   => $cleanCode,
                            'subject_name'   => $subName,
                            'subject_type'   => $subType,
                            'credit'         => $credit,
                            'hours_per_week' => $hpw,
                            'grade_level'    => $gradeLevel,
                            'room'           => $singleRoom,
                            'study_plan'     => $singlePlan,
                            'total_hours'    => $totalH,
                            'remark'         => $remark,
                            'year'           => $year,
                            'term'           => $term
                        ];
                    }
                }
            }
        } 
        // Support nested subjects array (e.g. subjects[0][subject_code])
        elseif (isset($post['subjects']) && is_array($post['subjects'])) {
            foreach ($post['subjects'] as $sub) {
                $cleanCode = trim($sub['subject_code'] ?? '');
                if (!empty($cleanCode)) {
                    $rawRooms = $sub['room'] ?? '';
                    $rooms = $this->expandRooms($rawRooms);
                    if (empty($rooms)) $rooms = [$rawRooms];

                    $selectedPlan = trim($sub['study_plan'] ?? '');
                    $gradeLevel   = trim($sub['grade_level'] ?? '');
                    $subName      = trim($sub['subject_name'] ?? '');
                    $subType      = $sub['subject_type'] ?? 'พื้นฐาน';
                    $credit       = !empty($sub['credit']) ? (float)$sub['credit'] : 0;
                    $hpw          = !empty($sub['hours_per_week']) ? (int)$sub['hours_per_week'] : 0;
                    $totalH       = !empty($sub['total_hours']) ? (int)$sub['total_hours'] : 0;
                    $remark       = trim($sub['remark'] ?? '');

                    foreach ($rooms as $singleRoom) {
                        $singlePlan = $this->resolveStudyPlan($selectedPlan, $gradeLevel, (string)$singleRoom, $classRoomMap);

                        $batchData[] = [
                            'teacher_id'     => $teacherId,
                            'subject_code'   => $cleanCode,
                            'subject_name'   => $subName,
                            'subject_type'   => $subType,
                            'credit'         => $credit,
                            'hours_per_week' => $hpw,
                            'grade_level'    => $gradeLevel,
                            'room'           => $singleRoom,
                            'study_plan'     => $singlePlan,
                            'total_hours'    => $totalH,
                            'remark'         => $remark,
                            'year'           => $year,
                            'term'           => $term
                        ];
                    }
                }
            }
        }
        // Support single item fallback
        elseif (!empty($post['subject_code'])) {
            $rawRooms = $post['room'] ?? '';
            $rooms = $this->expandRooms($rawRooms);
            if (empty($rooms)) $rooms = [$rawRooms];

            $cleanCode    = trim($post['subject_code']);
            $selectedPlan = trim($post['study_plan'] ?? '');
            $gradeLevel   = trim($post['grade_level'] ?? '');
            $subName      = trim($post['subject_name'] ?? '');
            $subType      = $post['subject_type'] ?? 'พื้นฐาน';
            $credit       = !empty($post['credit']) ? (float)$post['credit'] : 0;
            $hpw          = !empty($post['hours_per_week']) ? (int)$post['hours_per_week'] : 0;
            $totalH       = !empty($post['total_hours']) ? (int)$post['total_hours'] : 0;
            $remark       = trim($post['remark'] ?? '');

            foreach ($rooms as $singleRoom) {
                $singlePlan = $this->resolveStudyPlan($selectedPlan, $gradeLevel, (string)$singleRoom, $classRoomMap);

                $batchData[] = [
                    'teacher_id'     => $teacherId,
                    'subject_code'   => $cleanCode,
                    'subject_name'   => $subName,
                    'subject_type'   => $subType,
                    'credit'         => $credit,
                    'hours_per_week' => $hpw,
                    'grade_level'    => $gradeLevel,
                    'room'           => $singleRoom,
                    'study_plan'     => $singlePlan,
                    'total_hours'    => $totalH,
                    'remark'         => $remark,
                    'year'           => $year,
                    'term'           => $term
                ];
            }
        }

        $hasSubjects   = !empty($batchData);
        $hasActivities = isset($post['save_activities']) || isset($post['activity_name']);
        $hasDuties     = isset($post['save_duties']) || isset($post['duty_name']);

        if (!$hasSubjects && !$hasActivities && !$hasDuties) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'กรุณากรอกข้อมูลรายวิชา หรือ กิจกรรม หรือ หน้าที่พิเศษ']);
        }

        // Database transaction: Save subjects, activities, and duties
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Process subjects
        if ($hasSubjects) {
            $existingIds = $post['existing_schedule_ids'] ?? $post['schedule_id'] ?? '';
            if (!empty($existingIds)) {
                $idsToDelete = is_array($existingIds) ? $existingIds : explode(',', (string)$existingIds);
                $idsToDelete = array_filter(array_map('intval', $idsToDelete));
                if (!empty($idsToDelete)) {
                    $this->teachingScheduleModel->delete($idsToDelete);
                }
            }
            $this->teachingScheduleModel->insertBatch($batchData);
        }

        // 2. Process activities
        if ($hasActivities) {
            $this->db->table('tb_teaching_schedule_activity')
                ->where('teacher_id', $teacherId)
                ->where('year', $year)
                ->where('term', $term)
                ->delete();

            if (!empty($post['activity_name']) && is_array($post['activity_name'])) {
                $actBatch = [];
                foreach ($post['activity_name'] as $idx => $actName) {
                    $cleanName = trim($actName);
                    if ($cleanName !== '') {
                        $hpw = !empty($post['activity_hours_per_week'][$idx]) ? (int)$post['activity_hours_per_week'][$idx] : 1;
                        $tot = !empty($post['activity_total_hours'][$idx]) ? (int)$post['activity_total_hours'][$idx] : ($hpw * 20);
                        $actBatch[] = [
                            'teacher_id'     => $teacherId,
                            'activity_name'  => $cleanName,
                            'hours_per_week' => $hpw,
                            'total_hours'    => $tot,
                            'grade_level'    => trim($post['activity_grade_level'][$idx] ?? ''),
                            'room'           => trim($post['activity_room'][$idx] ?? ''),
                            'remark'         => trim($post['activity_remark'][$idx] ?? ''),
                            'year'           => $year,
                            'term'           => $term
                        ];
                    }
                }
                if (!empty($actBatch)) {
                    $this->db->table('tb_teaching_schedule_activity')->insertBatch($actBatch);
                }
            }
        }

        // 3. Process duties
        if ($hasDuties) {
            $this->db->table('tb_teaching_schedule_duty')
                ->where('teacher_id', $teacherId)
                ->where('year', $year)
                ->where('term', $term)
                ->delete();

            if (!empty($post['duty_name']) && is_array($post['duty_name'])) {
                $dutyBatch = [];
                $order = 1;
                foreach ($post['duty_name'] as $dName) {
                    $cleanDuty = trim($dName);
                    if ($cleanDuty !== '') {
                        $dutyBatch[] = [
                            'teacher_id' => $teacherId,
                            'duty_name'  => $cleanDuty,
                            'duty_order' => $order++,
                            'year'       => $year,
                            'term'       => $term
                        ];
                    }
                }
                if (!empty($dutyBatch)) {
                    $this->db->table('tb_teaching_schedule_duty')->insertBatch($dutyBatch);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง']);
        }

        $msgParts = [];
        if ($hasSubjects) {
            $count = count($batchData);
            $msgParts[] = "รายวิชา $count รายการ/ห้อง";
        }
        if ($hasActivities) {
            $msgParts[] = "กิจกรรม/อื่น ๆ";
        }
        if ($hasDuties) {
            $msgParts[] = "หน้าที่พิเศษ";
        }

        return $this->response->setJSON([
            'status' => 'success',
            'msg'    => 'บันทึกข้อมูลสำเร็จ (' . implode(', ', $msgParts) . ')'
        ]);
    }

    public function delete($id = null)
    {
        $scheduleOnOff = $this->getScheduleOnOff();
        if (!$scheduleOnOff['is_open']) {
            return $this->response->setJSON([
                'status' => 'error', 
                'msg'    => 'ระบบปิดการจัดตารางสอนแล้ว ไม่สามารถลบข้อมูลได้'
            ]);
        }

        $rawIds = $this->request->getPost('ids') ?? $this->request->getGet('ids') ?? $id ?? '';
        if (is_string($rawIds)) {
            $rawIds = preg_split('/[,\|\s]+/', $rawIds, -1, PREG_SPLIT_NO_EMPTY);
        }
        $ids = is_array($rawIds) ? $rawIds : [$rawIds];
        $ids = array_filter(array_map('intval', $ids));

        if (!empty($ids)) {
            $this->teachingScheduleModel->delete($ids);
            return $this->response->setJSON(['status' => 'success', 'msg' => 'ลบข้อมูลสำเร็จ (' . count($ids) . ' รายการ)']);
        }
        return $this->response->setJSON(['status' => 'error', 'msg' => 'ไม่พบข้อมูลที่ต้องการลบ']);
    }

    public function get($id = null)
    {
        $rawIds = $this->request->getGet('ids') ?? $id ?? '';
        if (is_string($rawIds)) {
            $rawIds = preg_split('/[,\|\s]+/', $rawIds, -1, PREG_SPLIT_NO_EMPTY);
        }
        $ids = is_array($rawIds) ? $rawIds : [$rawIds];
        $ids = array_filter(array_map('intval', $ids));

        if (empty($ids)) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'ไม่พบข้อมูล']);
        }

        $records = $this->teachingScheduleModel->whereIn('schedule_id', $ids)->findAll();
        if (empty($records)) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'ไม่พบข้อมูล']);
        }

        $base = $records[0];
        $rooms = [];
        $studyPlans = [];
        foreach ($records as $rec) {
            $r = trim((string)$rec['room']);
            if ($r !== '' && !in_array($r, $rooms, true)) {
                $rooms[] = $r;
            }
            $p = trim((string)($rec['study_plan'] ?? ''));
            if ($p !== '' && !in_array($p, $studyPlans, true)) {
                $studyPlans[] = $p;
            }
        }

        usort($rooms, function($a, $b) {
            $nA = is_numeric($a) ? (int)$a : null;
            $nB = is_numeric($b) ? (int)$b : null;
            if ($nA !== null && $nB !== null) return $nA - $nB;
            return strnatcasecmp($a, $b);
        });

        $uniquePlans = array_values(array_unique(array_filter($studyPlans)));
        $planToUse = (count($uniquePlans) === 1) ? $uniquePlans[0] : '__auto__';

        $base['room'] = implode(', ', $rooms);
        $base['study_plan'] = $planToUse;
        $base['schedule_ids'] = implode(',', array_column($records, 'schedule_id'));

        return $this->response->setJSON(['status' => 'success', 'data' => $base]);
    }

    public function searchSubjects()
    {
        $q = trim($this->request->getGet('q') ?? '');
        $reqYear = $this->request->getGet('year');
        $reqTerm = $this->request->getGet('term');

        // Determine target SubjectYear (เช่น "2/2569")
        $targetSubjectYear = '';
        if (!empty($reqTerm) && !empty($reqYear)) {
            $targetSubjectYear = trim($reqTerm) . '/' . trim($reqYear);
        } else {
            $scheduleOnOff = $this->getScheduleOnOff();
            if (!empty($scheduleOnOff['row']->onoff_year)) {
                $targetSubjectYear = trim($scheduleOnOff['row']->onoff_year);
            }
        }
        
        $builder = $this->db->table('tb_subjects');
        $builder->select('MIN(SubjectID) as SubjectID, SubjectCode, SubjectName, SubjectType, SubjectUnit, SubjectHour, SubjectClass', false);

        if (!empty($targetSubjectYear)) {
            $builder->where('SubjectYear', $targetSubjectYear);
        }
        
        if (!empty($q)) {
            $builder->groupStart()
                    ->like('SubjectCode', $q)
                    ->orLike('SubjectName', $q)
                    ->groupEnd();
        }
        
        $builder->groupBy(['SubjectCode', 'SubjectName', 'SubjectClass', 'SubjectType', 'SubjectUnit', 'SubjectHour']);
        $builder->orderBy('SubjectCode', 'ASC');
        $builder->limit(50);
        
        $query = $builder->get();
        $results = $query->getResultArray();

        // Fallback: If no subjects found for specific SubjectYear and search query exists, try without SubjectYear
        if (empty($results) && !empty($targetSubjectYear) && !empty($q)) {
            $builderFallback = $this->db->table('tb_subjects');
            $builderFallback->select('MIN(SubjectID) as SubjectID, SubjectCode, SubjectName, SubjectType, SubjectUnit, SubjectHour, SubjectClass', false);
            $builderFallback->groupStart()
                    ->like('SubjectCode', $q)
                    ->orLike('SubjectName', $q)
                    ->groupEnd();
            $builderFallback->groupBy(['SubjectCode', 'SubjectName', 'SubjectClass', 'SubjectType', 'SubjectUnit', 'SubjectHour']);
            $builderFallback->orderBy('SubjectCode', 'ASC');
            $builderFallback->limit(50);
            $results = $builderFallback->get()->getResultArray();
        }
        
        $data = [];
        foreach ($results as $s) {
            $rawType = $s['SubjectType'] ?? '';
            $cleanType = 'พื้นฐาน';
            if (mb_strpos($rawType, 'เพิ่มเติม') !== false) {
                $cleanType = 'เพิ่มเติม';
            } elseif (mb_strpos($rawType, 'กิจกรรม') !== false) {
                $cleanType = 'กิจกรรมพัฒนาผู้เรียน';
            }

            $hour = (float)($s['SubjectHour'] ?? 0);
            $hoursPerWeek = $hour > 0 ? (int)ceil($hour / 20) : '';

            $gradeLevel = trim($s['SubjectClass'] ?? '');
            if (!empty($gradeLevel) && !str_starts_with($gradeLevel, 'ม.')) {
                $gradeLevel = 'ม.' . $gradeLevel;
            }

            $credit = (float)($s['SubjectUnit'] ?? 0);

            $data[] = [
                'id'             => $s['SubjectID'],
                'subject_code'   => $s['SubjectCode'],
                'subject_name'   => $s['SubjectName'],
                'subject_type'   => $cleanType,
                'credit'         => $credit > 0 ? $credit : 0,
                'hours_per_week' => $hoursPerWeek,
                'total_hours'    => (int)$hour,
                'grade_level'    => $gradeLevel,
                'text'           => $s['SubjectCode'] . ' ' . $s['SubjectName'] . ($gradeLevel ? " ($gradeLevel)" : '') . ($cleanType ? " [$cleanType]" : '')
            ];
        }
        
        return $this->response->setJSON(['status' => 'success', 'results' => $data]);
    }

    /**
     * Retrieve distinct study plans (English abbreviations) grouped from tb_students.StudentStudyLine
     */
    public function getStudentStudyPlans(): array
    {
        try {
            $builder = $this->db->table('tb_students')
                ->select('DISTINCT(TRIM(StudentStudyLine)) as study_plan', false)
                ->where('StudentStudyLine IS NOT NULL')
                ->where('TRIM(StudentStudyLine) !=', '')
                ->where('TRIM(StudentStudyLine) !=', '-')
                ->orderBy('study_plan', 'ASC');

            $results = $builder->get()->getResultArray();
            $plans = [];
            foreach ($results as $r) {
                $p = trim($r['study_plan'] ?? '');
                // คัดกรองเฉพาะแผนการเรียนที่เป็นตัวย่อภาษาอังกฤษมาตรฐาน (ไม่เอาชื่อคนหรือข้อความภาษาไทยที่ปะปนในฐานข้อมูล)
                if (!empty($p) && preg_match('/^[A-Za-z0-9\(\)\-]+$/', $p) && !in_array($p, $plans)) {
                    $plans[] = $p;
                }
            }

            // หากไม่มีในฐานข้อมูล ให้ใช้รายการแผนการเรียนมาตรฐานตามที่โรงเรียนใช้งาน
            if (empty($plans)) {
                $plans = [
                    'CEP', 'CP', 'GENERAL', 
                    'PAP1', 'PAP2', 'PAP3', 'PAP4', 
                    'SMT(S)', 'SMT(T)', 
                    'SP1', 'SP2', 'SP3', 'SP4'
                ];
            }

            // จัดเรียงลำดับ
            sort($plans, SORT_NATURAL | SORT_FLAG_CASE);

            return $plans;
        } catch (\Throwable $e) {
            log_message('error', 'getStudentStudyPlans error: ' . $e->getMessage());
            return [
                'CEP', 'CP', 'GENERAL', 
                'PAP1', 'PAP2', 'PAP3', 'PAP4', 
                'SMT(S)', 'SMT(T)', 
                'SP1', 'SP2', 'SP3', 'SP4'
            ];
        }
    }

    /**
     * AJAX Endpoint to return study plans list
     */
    public function getStudyPlans()
    {
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $this->getStudentStudyPlans()
        ]);
    }

    /**
     * Retrieve class list with corresponding study plan from tb_students
     */
    public function getStudentClassRoomMap(): array
    {
        try {
            $builder = $this->db->table('tb_students')
                ->select('TRIM(StudentClass) as class_name, TRIM(StudentStudyLine) as study_plan')
                ->where('StudentClass IS NOT NULL')
                ->where('TRIM(StudentClass) !=', '')
                ->groupBy(['TRIM(StudentClass)', 'TRIM(StudentStudyLine)'])
                ->orderBy('class_name', 'ASC');

            $results = $builder->get()->getResultArray();
            $map = [];
            foreach ($results as $row) {
                $rawClass = trim($row['class_name'] ?? '');
                $plan = trim($row['study_plan'] ?? '');
                if (empty($rawClass)) continue;

                $grade = '';
                $room = '';
                if (preg_match('/^(?:ม\.?\s*)?(\d+)[\/\.](\d+|[A-Za-z0-9]+)$/u', $rawClass, $matches)) {
                    $grade = 'ม.' . $matches[1];
                    $room = (string)$matches[2];
                } else {
                    $room = $rawClass;
                }

                if (!empty($grade) && !empty($room)) {
                    if (!isset($map[$grade])) {
                        $map[$grade] = [];
                    }
                    $map[$grade][$room] = [
                        'room'  => $room,
                        'class' => $rawClass,
                        'plan'  => $plan
                    ];
                }
            }

            return $map;
        } catch (\Throwable $e) {
            log_message('error', 'getStudentClassRoomMap error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Expand room input string or array into individual room strings
     * e.g. "1-6" => ['1', '2', '3', '4', '5', '6']
     *      "1, 2, 3" => ['1', '2', '3']
     *      "1-3, 5" => ['1', '2', '3', '5']
     *      "PAP" => ['PAP']
     */
    public function expandRooms($roomInput): array
    {
        if (is_array($roomInput)) {
            $expanded = [];
            foreach ($roomInput as $r) {
                $expanded = array_merge($expanded, $this->expandRooms($r));
            }
            return array_values(array_unique(array_filter($expanded)));
        }

        $raw = trim((string)$roomInput);
        if (empty($raw)) return [];

        // Normalize dashes like 1 - 6 to 1-6
        $raw = preg_replace('/\s*[-–—]\s*/u', '-', $raw);

        // Split by comma, pipe, or space
        $parts = preg_split('/[,\|\s]+/u', $raw, -1, PREG_SPLIT_NO_EMPTY);
        $rooms = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if (preg_match('/^(\d+)-(\d+)$/u', $part, $matches)) {
                $start = (int)$matches[1];
                $end = (int)$matches[2];
                if ($start <= $end && ($end - $start) <= 30) {
                    for ($i = $start; $i <= $end; $i++) {
                        $rooms[] = (string)$i;
                    }
                    continue;
                }
            }
            $rooms[] = $part;
        }

        return array_values(array_unique($rooms));
    }

    /**
     * Resolve study plan for a room based on user selection and database map
     */
    private function resolveStudyPlan(?string $selectedPlan, string $gradeLevel, string $room, array $classRoomMap): string
    {
        $selectedPlan = trim((string)$selectedPlan);
        if (!empty($selectedPlan) && $selectedPlan !== '__auto__' && $selectedPlan !== '__custom__') {
            return $selectedPlan;
        }

        $normGrade = (!empty($gradeLevel) && !str_starts_with($gradeLevel, 'ม.')) ? ('ม.' . $gradeLevel) : $gradeLevel;
        if (isset($classRoomMap[$normGrade][$room]['plan'])) {
            return $classRoomMap[$normGrade][$room]['plan'];
        }
        if (isset($classRoomMap[$gradeLevel][$room]['plan'])) {
            return $classRoomMap[$gradeLevel][$room]['plan'];
        }

        return '';
    }

    /**
     * Format an array of rooms into range or comma-separated string
     * e.g. ['1', '2', '3', '4', '5', '6'] => '1 - 6'
     */
    public function formatRoomRange(array $rooms): string
    {
        if (empty($rooms)) return '';
        if (count($rooms) === 1) return (string)$rooms[0];

        $numbers = [];
        $allNumeric = true;
        foreach ($rooms as $r) {
            if (!is_numeric($r)) {
                $allNumeric = false;
                break;
            }
            $numbers[] = (int)$r;
        }

        if ($allNumeric) {
            sort($numbers);
            $isSequential = true;
            for ($i = 0; $i < count($numbers) - 1; $i++) {
                if ($numbers[$i + 1] !== $numbers[$i] + 1) {
                    $isSequential = false;
                    break;
                }
            }
            if ($isSequential && count($numbers) >= 2) {
                return $numbers[0] . ' - ' . end($numbers);
            }
        }

        return implode(', ', $rooms);
    }

    /**
     * AJAX Endpoint to return class room and plan map
     */
    public function getClassRoomMap()
    {
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $this->getStudentClassRoomMap()
        ]);
    }

    /**
     * AJAX Endpoint to get teacher activities, duties, and auto-suggested duties
     */
    public function getTeacherExtra($teacherId)
    {
        $year = $this->request->getGet('year') ?? ($this->setup ? $this->setup->seplanset_year : (date('Y') + 543));
        $term = $this->request->getGet('term') ?? ($this->setup ? $this->setup->seplanset_term : 1);

        $this->ensureTablesExist();

        $activities = $this->db->table('tb_teaching_schedule_activity')
            ->where('teacher_id', $teacherId)
            ->where('year', $year)
            ->where('term', $term)
            ->orderBy('activity_id', 'ASC')
            ->get()->getResultArray();

        $duties = $this->db->table('tb_teaching_schedule_duty')
            ->where('teacher_id', $teacherId)
            ->where('year', $year)
            ->where('term', $term)
            ->orderBy('duty_order', 'ASC')
            ->orderBy('duty_id', 'ASC')
            ->get()->getResultArray();

        $suggestedDuties = [];

        // 1. Homeroom advisor from tb_regclass
        try {
            $regclass = $this->db->table('tb_regclass')
                ->where('class_teacher', $teacherId)
                ->where('Reg_Year', $year)
                ->get()->getRow();
            if (!$regclass) {
                $regclass = $this->db->table('tb_regclass')
                    ->where('class_teacher', $teacherId)
                    ->orderBy('Reg_Year', 'DESC')
                    ->get()->getRow();
            }
            if ($regclass && !empty($regclass->Reg_Class)) {
                $suggestedDuties[] = 'ครูที่ปรึกษาชั้นมัธยมศึกษาปีที่ ' . $regclass->Reg_Class;
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // 2. Department head from tb_personnel
        try {
            $teacherPers = $this->db_personnel->table('tb_personnel')
                ->where('pers_id', $teacherId)
                ->get()->getRow();
            if ($teacherPers) {
                if ($teacherPers->pers_position === 'posi_003' || (!empty($teacherPers->pers_groupleade) && $teacherPers->pers_groupleade == 1)) {
                    $learName = 'กลุ่มสาระการเรียนรู้';
                    if ($this->db_skj && !empty($teacherPers->pers_learning)) {
                        $learRow = $this->db_skj->table('tb_learning')->where('lear_id', $teacherPers->pers_learning)->get()->getRow();
                        if ($learRow && !empty($learRow->lear_namethai)) {
                            $learName = $learRow->lear_namethai;
                        }
                    }
                    $suggestedDuties[] = 'หัวหน้า' . $learName;
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return $this->response->setJSON([
            'status'           => 'success',
            'activities'       => $activities,
            'duties'           => $duties,
            'suggested_duties' => $suggestedDuties
        ]);
    }

    /**
     * Prepare complete individual teaching schedule data
     */
    public function getIndividualScheduleData($teacherId, $year = null, $term = null): ?array
    {
        $this->ensureTablesExist();

        if ($year === null) {
            $year = $this->request->getGet('year') ?? ($this->setup ? $this->setup->seplanset_year : (date('Y') + 543));
        }
        if ($term === null) {
            $term = $this->request->getGet('term') ?? ($this->setup ? $this->setup->seplanset_term : 1);
        }

        // 1. Fetch Teacher Info
        $teacher = $this->db_personnel->table('tb_personnel')
            ->select('pers_id, pers_prefix, pers_firstname, pers_lastname, pers_learning, pers_position')
            ->where('pers_id', $teacherId)
            ->get()->getRow();

        if (!$teacher) {
            return null;
        }

        // 2. Fetch Learning Group Name
        $learningName = 'กลุ่มสาระการเรียนรู้';
        if ($this->db_skj && !empty($teacher->pers_learning)) {
            $learRow = $this->db_skj->table('tb_learning')->where('lear_id', $teacher->pers_learning)->get()->getRow();
            if ($learRow && !empty($learRow->lear_namethai)) {
                $learningName = $learRow->lear_namethai;
            }
        }

        // 3. Fetch Subjects and group identical subjects (by subject_code + grade_level)
        $rawSchedules = $this->teachingScheduleModel
            ->where('teacher_id', $teacherId)
            ->where('year', $year)
            ->where('term', $term)
            ->orderBy('grade_level', 'ASC')
            ->orderBy('subject_code', 'ASC')
            ->findAll();

        $groupedSubjects = [];
        $totalCredit = 0;
        $totalBasicHours = 0;
        $totalAdditionalHours = 0;
        $totalSubjectWeeklyHours = 0;
        $basicSubjectCount = 0;
        $additionalSubjectCount = 0;

        foreach ($rawSchedules as $sch) {
            $groupKey = trim($sch['subject_code']) . '_' . trim($sch['grade_level']);
            if (!isset($groupedSubjects[$groupKey])) {
                $groupedSubjects[$groupKey] = [
                    'subject_code'   => $sch['subject_code'],
                    'subject_name'   => $sch['subject_name'],
                    'subject_type'   => $sch['subject_type'] ?? 'พื้นฐาน',
                    'credit'         => (float)($sch['credit'] ?? 0),
                    'hours_per_week' => (float)($sch['hours_per_week'] ?? 0),
                    'grade_level'    => $sch['grade_level'],
                    'rooms'          => [],
                    'study_plans'    => [],
                    'remarks'        => []
                ];
                $totalCredit += (float)($sch['credit'] ?? 0);
                if (mb_strpos($sch['subject_type'] ?? '', 'เพิ่มเติม') !== false) {
                    $additionalSubjectCount++;
                } else {
                    $basicSubjectCount++;
                }
            }

            $r = trim((string)$sch['room']);
            if ($r !== '' && !in_array($r, $groupedSubjects[$groupKey]['rooms'], true)) {
                $groupedSubjects[$groupKey]['rooms'][] = $r;
            }
            $p = trim((string)($sch['study_plan'] ?? ''));
            if ($p !== '' && !in_array($p, $groupedSubjects[$groupKey]['study_plans'], true)) {
                $groupedSubjects[$groupKey]['study_plans'][] = $p;
            }
            $rem = trim((string)($sch['remark'] ?? ''));
            if ($rem !== '' && !in_array($rem, $groupedSubjects[$groupKey]['remarks'], true)) {
                $groupedSubjects[$groupKey]['remarks'][] = $rem;
            }
        }

        // Post-process grouped subjects
        foreach ($groupedSubjects as &$sub) {
            usort($sub['rooms'], function($a, $b) {
                $nA = is_numeric($a) ? (int)$a : null;
                $nB = is_numeric($b) ? (int)$b : null;
                if ($nA !== null && $nB !== null) return $nA - $nB;
                return strnatcasecmp($a, $b);
            });
            $sub['room_count'] = max(count($sub['rooms']), 1);
            $sub['room_text'] = $this->formatRoomRange($sub['rooms']);
            $sub['total_weekly_hours'] = $sub['hours_per_week'] * $sub['room_count'];
            $totalSubjectWeeklyHours += $sub['total_weekly_hours'];

            if (mb_strpos($sub['subject_type'], 'เพิ่มเติม') !== false) {
                $totalAdditionalHours += $sub['total_weekly_hours'];
            } else {
                $totalBasicHours += $sub['total_weekly_hours'];
            }

            $remarkParts = [];
            $uniquePlans = array_values(array_unique(array_filter($sub['study_plans'])));
            if (!empty($uniquePlans)) {
                $remarkParts[] = implode(', ', $uniquePlans);
            }
            if (!empty($sub['remarks'])) {
                $remarkParts[] = implode(', ', $sub['remarks']);
            }
            $sub['final_remark'] = implode(' ', $remarkParts);
        }
        unset($sub);

        // 4. Fetch Activities
        $activities = $this->db->table('tb_teaching_schedule_activity')
            ->where('teacher_id', $teacherId)
            ->where('year', $year)
            ->where('term', $term)
            ->orderBy('activity_id', 'ASC')
            ->get()->getResultArray();

        $totalActivityWeeklyHours = 0;
        foreach ($activities as $act) {
            $totalActivityWeeklyHours += (float)($act['hours_per_week'] ?? 0);
        }

        // 5. Grand Total Hours
        $grandTotalWeeklyHours = $totalSubjectWeeklyHours + $totalActivityWeeklyHours;

        // 6. Fetch Duties
        $duties = $this->db->table('tb_teaching_schedule_duty')
            ->where('teacher_id', $teacherId)
            ->where('year', $year)
            ->where('term', $term)
            ->orderBy('duty_order', 'ASC')
            ->orderBy('duty_id', 'ASC')
            ->get()->getResultArray();

        // 7. Fetch School Info & Signers
        $school = $this->db->table('tb_school')->get()->getRow();

        // Department Head
        $deptHead = $this->db_personnel->table('tb_personnel')
            ->where('pers_learning', $teacher->pers_learning)
            ->groupStart()
                ->where('pers_position', 'posi_003')
                ->orWhere('pers_groupleade', 1)
            ->groupEnd()
            ->get()->getRow();

        // Vice Director (Academic)
        $viceDirector = $this->db_personnel->table('tb_personnel')
            ->where('pers_position', 'posi_002')
            ->get()->getRow();

        return [
            'year'                       => $year,
            'term'                       => $term,
            'teacher'                    => $teacher,
            'learning_name'              => $learningName,
            'grouped_subjects'           => array_values($groupedSubjects),
            'total_credit'               => $totalCredit,
            'total_basic_hours'          => $totalBasicHours,
            'total_additional_hours'     => $totalAdditionalHours,
            'total_subject_weekly_hours' => $totalSubjectWeeklyHours,
            'basic_subject_count'        => $basicSubjectCount,
            'additional_subject_count'   => $additionalSubjectCount,
            'activities'                 => $activities,
            'total_activity_weekly_hours'=> $totalActivityWeeklyHours,
            'grand_total_weekly_hours'   => $grandTotalWeeklyHours,
            'duties'                     => $duties,
            'school'                     => $school,
            'dept_head'                  => $deptHead,
            'vice_director'              => $viceDirector,
        ];
    }

    /**
     * Print View for official individual teaching schedule
     */
    public function printSchedule($teacherId, $year = null, $term = null)
    {
        $data = $this->getIndividualScheduleData($teacherId, $year, $term);
        if (!$data) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลครูผู้สอน');
        }

        $teacherName = ($data['teacher']->pers_prefix ?? '') . ($data['teacher']->pers_firstname ?? '') . ' ' . ($data['teacher']->pers_lastname ?? '');
        $data['title'] = 'ข้อมูลการจัดตารางสอนรายบุคคล - ' . $teacherName;

        return view('teacher/curriculum/schedule_print', $data);
    }

    /**
     * Print View for department schedule summary (ตารางรวมกลุ่มสาระการเรียนรู้)
     */
    public function printAll($year = null, $term = null)
    {
        $this->ensureTablesExist();

        if ($year === null || $term === null) {
            if ($this->setup && !empty($this->setup->seplanset_year) && !empty($this->setup->seplanset_term)) {
                $year = $this->setup->seplanset_year;
                $term = $this->setup->seplanset_term;
            } else {
                $year = date('Y') + 543;
                $term = 1;
            }
        }

        $person_id = $this->session->get('person_id');
        $user_personnel = $this->db_personnel->table('tb_personnel')->select('pers_learning')->where('pers_id', $person_id)->get()->getRow();
        $pers_learning = $user_personnel ? $user_personnel->pers_learning : '';

        // Fetch Learning Group Name
        $learningName = 'กลุ่มสาระการเรียนรู้';
        if ($this->db_skj && !empty($pers_learning)) {
            $learRow = $this->db_skj->table('tb_learning')->where('lear_id', $pers_learning)->get()->getRow();
            if ($learRow && !empty($learRow->lear_namethai)) {
                $learningName = $learRow->lear_namethai;
            }
        }

        // Fetch teachers list in the department
        $teachers = $this->db_personnel->table('tb_personnel')
                                ->select('pers_id, pers_prefix, pers_firstname, pers_lastname, pers_learning')
                                ->where('pers_position >=', 'posi_003')
                                ->where('pers_position <=', 'posi_006')
                                ->where('pers_learning', $pers_learning)
                                ->where('pers_status', 'กำลังใช้งาน')
                                ->orderBy('pers_firstname', 'ASC')
                                ->get()->getResultArray();

        // Fetch schedules
        $schedules = $this->teachingScheduleModel->getSchedulesByTerm($year, $term);

        // Fetch activities
        $activities = $this->db->table('tb_teaching_schedule_activity')
            ->where('year', $year)
            ->where('term', $term)
            ->orderBy('activity_id', 'ASC')
            ->get()->getResultArray();
        $teacherActivities = [];
        foreach ($activities as $act) {
            $teacherActivities[$act['teacher_id']][] = $act;
        }

        // Group schedules by teacher, then by subject
        $groupedSchedules = [];
        foreach ($schedules as $schedule) {
            $teacherId = $schedule['teacher_id'];
            if (!isset($groupedSchedules[$teacherId])) {
                $groupedSchedules[$teacherId] = [
                    'teacher_id'         => $teacherId,
                    'teacher_name'       => trim($schedule['pers_prefix'] . $schedule['pers_firstname'] . ' ' . $schedule['pers_lastname']),
                    'subjects'           => [],
                    'total_weekly_hours' => 0,
                    'total_rooms'        => 0
                ];
            }

            $groupKey = trim($schedule['subject_code']) . '_' . trim($schedule['grade_level']);
            if (!isset($groupedSchedules[$teacherId]['subjects'][$groupKey])) {
                $groupedSchedules[$teacherId]['subjects'][$groupKey] = [
                    'group_key'          => $groupKey,
                    'subject_code'       => $schedule['subject_code'],
                    'subject_name'       => $schedule['subject_name'],
                    'subject_type'       => $schedule['subject_type'],
                    'credit'             => $schedule['credit'],
                    'hours_per_week'     => (float)($schedule['hours_per_week'] ?? 0),
                    'grade_level'        => $schedule['grade_level'],
                    'rooms'              => [],
                    'study_plans'        => [],
                    'total_hours'        => 0,
                    'remarks'            => []
                ];
            }

            $sub = &$groupedSchedules[$teacherId]['subjects'][$groupKey];
            $roomStr = trim((string)$schedule['room']);
            if ($roomStr !== '' && !in_array($roomStr, $sub['rooms'], true)) {
                $sub['rooms'][] = $roomStr;
            }
            $planStr = trim((string)($schedule['study_plan'] ?? ''));
            if ($planStr !== '' && !in_array($planStr, $sub['study_plans'], true)) {
                $sub['study_plans'][] = $planStr;
            }
            $sub['total_hours'] += (int)($schedule['total_hours'] ?? 0);
            $remStr = trim((string)($schedule['remark'] ?? ''));
            if ($remStr !== '' && !in_array($remStr, $sub['remarks'], true)) {
                $sub['remarks'][] = $remStr;
            }
            unset($sub);
        }

        // Post process
        $grandTotalWeeklyHours = 0;
        foreach ($groupedSchedules as $tId => &$tData) {
            foreach ($tData['subjects'] as &$sub) {
                usort($sub['rooms'], function($a, $b) {
                    $nA = is_numeric($a) ? (int)$a : null;
                    $nB = is_numeric($b) ? (int)$b : null;
                    if ($nA !== null && $nB !== null) return $nA - $nB;
                    return strnatcasecmp($a, $b);
                });

                $sub['room_count'] = max(count($sub['rooms']), 1);
                $sub['room_range_text'] = $this->formatRoomRange($sub['rooms']);
                $sub['room_text'] = implode(', ', $sub['rooms']);
                $sub['total_weekly_hours'] = $sub['hours_per_week'] * $sub['room_count'];

                $tData['total_weekly_hours'] += $sub['total_weekly_hours'];
                $tData['total_rooms'] += $sub['room_count'];
                $grandTotalWeeklyHours += $sub['total_weekly_hours'];
            }
            $tData['subjects'] = array_values($tData['subjects']);
            $tData['activities'] = $teacherActivities[$tId] ?? [];
            $tData['total_activity_weekly_hours'] = 0;
            foreach ($tData['activities'] as $act) {
                $tData['total_activity_weekly_hours'] += (float)($act['hours_per_week'] ?? 0);
            }
            $tData['grand_total_weekly_hours'] = $tData['total_weekly_hours'] + $tData['total_activity_weekly_hours'];
        }
        unset($tData);

        // School and signatures
        $school = $this->db->table('tb_school')->get()->getRow();
        $deptHead = $this->db_personnel->table('tb_personnel')
            ->where('pers_learning', $pers_learning)
            ->groupStart()
                ->where('pers_position', 'posi_003')
                ->orWhere('pers_groupleade', 1)
            ->groupEnd()
            ->get()->getRow();

        $viceDirector = $this->db_personnel->table('tb_personnel')
            ->where('pers_position', 'posi_002')
            ->get()->getRow();

        $data = [
            'title'                 => 'ข้อมูลการจัดตารางสอนกลุ่มสาระการเรียนรู้ - ' . $learningName,
            'year'                  => $year,
            'term'                  => $term,
            'learning_name'         => $learningName,
            'groupedSchedules'      => $groupedSchedules,
            'grandTotalWeeklyHours' => $grandTotalWeeklyHours,
            'school'                => $school,
            'dept_head'             => $deptHead,
            'vice_director'         => $viceDirector,
        ];

        return view('teacher/curriculum/schedule_print_all', $data);
    }

    /**
     * In-app View for individual teaching schedule
     */
    public function viewTeacherSchedule($teacherId, $year = null, $term = null)
    {
        $data = $this->getIndividualScheduleData($teacherId, $year, $term);
        if (!$data) {
            return redirect()->to(base_url('curriculum/teaching-schedule'))->with('error', 'ไม่พบข้อมูลครูผู้สอน');
        }

        $teacher = $data['teacher'];
        $teacherName = ($teacher->pers_prefix ?? '') . ($teacher->pers_firstname ?? '') . ' ' . ($teacher->pers_lastname ?? '');

        // Fetch teachers in the same learning department for the quick switcher (only currently active)
        $pers_learning = $teacher->pers_learning ?? '';
        $data['teachers'] = $this->db_personnel->table('tb_personnel')
                                ->select('pers_id, pers_prefix, pers_firstname, pers_lastname, pers_learning')
                                ->where('pers_position >=', 'posi_003')
                                ->where('pers_position <=', 'posi_006')
                                ->where('pers_learning', $pers_learning)
                                ->where('pers_status', 'กำลังใช้งาน')
                                ->orderBy('pers_firstname', 'ASC')
                                ->get()->getResultArray();

        $data['title'] = 'ข้อมูลตารางสอนรายบุคคล: ' . $teacherName;
        $data['OnOff'] = [$this->setup];

        // Track page visit
        helper('recent_pages');
        track_recent_page('curriculum/teaching-schedule/teacher/' . $teacherId, 'ตารางสอน - ' . $teacherName, 'bi-person-badge');

        return view('teacher/curriculum/schedule_view_individual', $data);
    }

    /**
     * View logged-in teacher's individual teaching schedule
     */
    public function mySchedule($year = null, $term = null)
    {
        $person_id = $this->session->get('person_id');
        if (empty($person_id)) {
            return redirect()->to(base_url('login'))->with('error', 'กรุณาเข้าสู่ระบบ');
        }

        // Check if teacher exists in tb_personnel
        $teacher = $this->db_personnel->table('tb_personnel')->where('pers_id', $person_id)->get()->getRow();
        if ($teacher) {
            return $this->viewTeacherSchedule($person_id, $year, $term);
        }

        // If person_id is not in tb_personnel, default to the first teacher with schedule
        $firstSched = $this->teachingScheduleModel->select('teacher_id')->first();
        if ($firstSched && !empty($firstSched['teacher_id'])) {
            return redirect()->to(base_url('curriculum/teaching-schedule/teacher/' . $firstSched['teacher_id']));
        }

        return redirect()->to(base_url('curriculum/teaching-schedule'))->with('error', 'ไม่พบข้อมูลครูผู้สอน');
    }
}
