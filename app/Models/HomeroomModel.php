<?php

namespace App\Models;

use CodeIgniter\Model;

class HomeroomModel extends Model
{
    protected $DBGroup          = 'affairs';
    protected $table            = 'tb_checkhomeroom';
    protected $primaryKey       = 'chk_home_id';
    protected $allowedFields    = [
        'chk_home_date', 'chk_home_time', 'chk_home_teacher', 'chk_home_room',
        'chk_home_ma', 'chk_home_khad', 'chk_home_la', 'chk_home_sahy',
        'chk_home_kid', 'chk_home_hnee', 'chk_home_term', 'chk_home_yaer',
        'chk_home_topic', 'chk_home_remark'
    ];

    /**
     * ดึงปีการศึกษาและภาคเรียนปัจจุบัน
     */
    public function getCurrentYearAndTerm(): array
    {
        $db = db_connect('default');
        $allEntries = $db->table('tb_schoolyear')->select('schyear_year')->get()->getResultArray();
        
        if (!empty($allEntries)) {
            usort($allEntries, function ($a, $b) {
                $partsA = explode('/', $a['schyear_year'] ?? '');
                $partsB = explode('/', $b['schyear_year'] ?? '');
                $termA = $partsA[0] ?? '';
                $yearA = $partsA[1] ?? '';
                $termB = $partsB[0] ?? '';
                $yearB = $partsB[1] ?? '';

                if ($yearB != $yearA) {
                    return $yearB <=> $yearA;
                }
                return $termB <=> $termA;
            });
            $latestEntry = $allEntries[0]['schyear_year'];
            $parts = explode('/', $latestEntry);
            return [
                'term' => $parts[0] ?? '1',
                'year' => $parts[1] ?? (string)(date('Y') + 543),
                'full' => $latestEntry
            ];
        }

        return [
            'term' => '1',
            'year' => (string)(date('Y') + 543),
            'full' => '1/' . (date('Y') + 543)
        ];
    }

    /**
     * ดึงข้อมูลห้องเรียนและครูที่ปรึกษา
     */
    public function getTeacherHomeroomInfo($personId, $academicYear = null)
    {
        $db = db_connect('default');
        $db_personnel = db_connect('personnel');

        if (!$academicYear) {
            $current = $this->getCurrentYearAndTerm();
            $academicYear = $current['year'];
        }

        // หาห้องที่ครูคนนี้เป็นครูประจำชั้น
        $builder = $db->table('tb_regclass')
            ->select('tb_regclass.Reg_Year, tb_regclass.Reg_Class, tb_regclass.class_teacher, p.pers_prefix, p.pers_firstname, p.pers_lastname, p.pers_id, p.pers_img')
            ->join($db_personnel->database . '.tb_personnel p', 'p.pers_id = tb_regclass.class_teacher', 'left')
            ->where('tb_regclass.class_teacher', $personId);

        if ($academicYear) {
            $builder->where('tb_regclass.Reg_Year', $academicYear);
        }

        $teacherInfo = $builder->get()->getRow();

        return $teacherInfo;
    }

    /**
     * ดึงรายชื่อครูที่ปรึกษาทั้งหมดของห้องนั้น (กรณีมีครูที่ปรึกษา 2 คน)
     */
    public function getCoAdvisorsByClass(string $className, string $academicYear): array
    {
        $db = db_connect('default');
        $db_personnel = db_connect('personnel');

        return $db->table('tb_regclass')
            ->select('p.pers_id, p.pers_prefix, p.pers_firstname, p.pers_lastname, p.pers_img, p.pers_phone')
            ->join($db_personnel->database . '.tb_personnel p', 'p.pers_id = tb_regclass.class_teacher', 'left')
            ->where('tb_regclass.Reg_Class', $className)
            ->where('tb_regclass.Reg_Year', $academicYear)
            ->get()->getResult();
    }

    /**
     * ดึงรายชื่อนักเรียนในห้องที่ปรึกษา (โมดูลที่ 1)
     */
    public function getStudentsInClass(string $className, string $status = '1/ปกติ'): array
    {
        $db = db_connect('default');
        $classQuery = (strpos($className, 'ม.') === false) ? 'ม.' . $className : $className;

        $builder = $db->table('tb_students')
            ->where('StudentClass', $classQuery);

        if ($status !== 'all') {
            $builder->where('StudentStatus', $status);
        }

        return $builder->orderBy('CAST(StudentNumber AS UNSIGNED)', 'ASC')
                       ->orderBy('StudentNumber', 'ASC')
                       ->get()
                       ->getResult();
    }

    /**
     * ดึงโปรไฟล์เชิงลึกของนักเรียนรายบุคคล (โมดูลที่ 1)
     */
    public function getStudentProfile($studentCodeOrId)
    {
        $db = db_connect('default');
        return $db->table('tb_students')
            ->groupStart()
                ->where('StudentID', $studentCodeOrId)
                ->orWhere('StudentCode', $studentCodeOrId)
            ->groupEnd()
            ->get()
            ->getRow();
    }

    /**
     * ดึงประวัติการเช็คชื่อย้อนหลังของห้อง (โมดูลที่ 2)
     */
    public function getAttendanceHistory(string $room, string $year = null, string $term = null, int $limit = 60): array
    {
        $builder = $this->where('chk_home_room', $room);

        if ($year) {
            $builder->where('chk_home_yaer', $year);
        }
        if ($term) {
            $builder->where('chk_home_term', $term);
        }

        return $builder->orderBy('chk_home_date', 'DESC')
                       ->limit($limit)
                       ->findAll();
    }

    /**
     * คำนวณสถิติการมาเรียนรายบุคคลตลอดภาคเรียน (โมดูลที่ 2)
     */
    public function getStudentAttendanceSummary(string $room, string $year, string $term, array $students): array
    {
        $records = $this->where('chk_home_room', $room)
                        ->where('chk_home_yaer', $year)
                        ->where('chk_home_term', $term)
                        ->findAll();

        $totalDays = count($records);
        $summary = [];

        // กำหนดค่าเริ่มต้นให้นักเรียนทุกคน
        foreach ($students as $s) {
            $code = $s->StudentCode;
            $summary[$code] = [
                'student' => $s,
                'total_days' => $totalDays,
                'ma' => 0,
                'khad' => 0,
                'la' => 0,
                'sahy' => 0,
                'kid' => 0,
                'hnee' => 0,
                'rate' => 0
            ];
        }

        // นับจำนวนครั้งแต่ละสถานะ
        $statusFields = ['ma', 'khad', 'la', 'sahy', 'kid', 'hnee'];
        foreach ($records as $rec) {
            foreach ($statusFields as $st) {
                $field = 'chk_home_' . $st;
                if (!empty($rec[$field])) {
                    $codes = explode('|', $rec[$field]);
                    foreach ($codes as $c) {
                        $c = trim($c);
                        if (isset($summary[$c])) {
                            $summary[$c][$st]++;
                        }
                    }
                }
            }
        }

        // คำนวณร้อยละการมาเรียน
        foreach ($summary as $code => &$data) {
            if ($totalDays > 0) {
                $presentDays = $data['ma'] + $data['kid'] + ($data['sahy'] * 0.5);
                $data['rate'] = round(($presentDays / $totalDays) * 100, 1);
            } else {
                $data['rate'] = 100;
            }
        }

        return $summary;
    }

    /**
     * ดึงรายการปีการศึกษาทั้งหมด
     */
    public function getAllSchoolYears(): array
    {
        $db = db_connect('default');
        return $db->table('tb_schoolyear')->select('schyear_year')->get()->getResultArray();
    }

    /**
     * ดึงข้อมูลผลการเรียนและวิชาที่ติด 0, ร, มส, มผ ของนักเรียนในห้อง (โมดูลที่ 4)
     * ดึงข้อมูลตรงจากตาราง skjacth_academic.tb_register และ tb_subjects
     */
    public function getAcademicTrackingData(string $room, string $academicYear, string $term): array
    {
        $db = db_connect('default');
        $cleanRoom = str_replace('ม.', '', $room);
        $regYearFull = $term . '/' . $academicYear;

        // ดึงนักเรียนในห้อง
        $students = $this->getStudentsInClass($cleanRoom);
        if (empty($students)) {
            return [
                'students' => [],
                'subjects' => [],
                'at_risk_count' => 0,
                'total_gpa_avg' => 0
            ];
        }

        $studentIds = array_column($students, 'StudentID');

        // ดึงผลการเรียนตรงจาก skjacth_academic.tb_register
        $builder = $db->table('tb_register')
            ->select('tb_register.StudentID, tb_register.SubjectID, tb_register.Score100, tb_register.Grade, tb_register.RegisterYear, tb_register.RegisterClass, tb_register.StudyTime, sub.SubjectCode, sub.SubjectName, sub.SubjectUnit, sub.SubjectType, sub.SubjectHour')
            ->join('tb_subjects sub', 'sub.SubjectID = tb_register.SubjectID', 'left')
            ->whereIn('tb_register.StudentID', $studentIds);

        $builder->groupStart()
            ->where('tb_register.RegisterYear', $regYearFull)
            ->orWhere('tb_register.RegisterYear', $academicYear)
            ->orLike('tb_register.RegisterYear', $academicYear, 'both')
        ->groupEnd();

        $registers = $builder->get()->getResult();

        // จัดกลุ่มผลการเรียนตาม StudentID
        $studentScores = [];
        $allSubjects = [];
        $atRiskCount = 0;
        $totalGpaSum = 0;
        $gpaCount = 0;

        foreach ($registers as $reg) {
            $sId = $reg->StudentID;
            $subId = $reg->SubjectID;

            if (!isset($studentScores[$sId])) {
                $studentScores[$sId] = [
                    'scores' => [],
                    'grades' => [],
                    'total_credit' => 0,
                    'total_grade_point' => 0,
                    'gpa' => 0,
                    'failed_subjects' => []
                ];
            }

            $studentScores[$sId]['scores'][$subId] = $reg->Score100;
            $studentScores[$sId]['grades'][$subId] = $reg->Grade;

            // ตรวจสอบว่ามีวิชาติด 0, ร, มส, มผ หรือไม่
            $grade = trim((string)$reg->Grade);
            if (in_array($grade, ['0', 'ร', 'มส', 'มผ'])) {
                $studentScores[$sId]['failed_subjects'][] = [
                    'code' => $reg->SubjectCode,
                    'name' => $reg->SubjectName,
                    'grade' => $grade
                ];
            }

            // คำนวณ GPA เบื้องต้นหากเกรดเป็นตัวเลข
            if (is_numeric($grade) && !empty($reg->SubjectUnit)) {
                $unit = (float)$reg->SubjectUnit;
                $studentScores[$sId]['total_credit'] += $unit;
                $studentScores[$sId]['total_grade_point'] += ((float)$grade * $unit);
            }

            // เก็บรายวิชาทั้งหมดของห้อง
            if (!isset($allSubjects[$subId])) {
                $allSubjects[$subId] = [
                    'id' => $subId,
                    'code' => $reg->SubjectCode,
                    'name' => $reg->SubjectName,
                    'unit' => $reg->SubjectUnit,
                    'type' => $reg->SubjectType
                ];
            }
        }

        // สรุปเกรดเฉลี่ยรายคนและภาพรวมห้อง
        foreach ($students as $st) {
            $sId = $st->StudentID;
            if (isset($studentScores[$sId])) {
                if ($studentScores[$sId]['total_credit'] > 0) {
                    $studentScores[$sId]['gpa'] = round($studentScores[$sId]['total_grade_point'] / $studentScores[$sId]['total_credit'], 2);
                    $totalGpaSum += $studentScores[$sId]['gpa'];
                    $gpaCount++;
                }
                if (!empty($studentScores[$sId]['failed_subjects'])) {
                    $atRiskCount++;
                }
                $st->academic = $studentScores[$sId];
            } else {
                $st->academic = [
                    'scores' => [],
                    'grades' => [],
                    'gpa' => 0,
                    'failed_subjects' => []
                ];
            }
        }

        $avgGpa = ($gpaCount > 0) ? round($totalGpaSum / $gpaCount, 2) : 0;

        return [
            'students' => $students,
            'subjects' => array_values($allSubjects),
            'at_risk_count' => $atRiskCount,
            'total_gpa_avg' => $avgGpa
        ];
    }

    /**
     * ดึงสถานะกิจกรรมพัฒนาผู้เรียน (ชุมนุม + ลูกเสือ) ของนักเรียนในห้อง (โมดูลที่ 4)
     */
    public function getActivitiesTrackingData(array $students, string $academicYear, string $term): array
    {
        $db = db_connect('default');
        $activityStatus = [];

        foreach ($students as $st) {
            $sId = $st->StudentID;
            $activityStatus[$sId] = [
                'club' => null,
            ];
        }

        if (empty($students)) {
            return $activityStatus;
        }

        $studentIds = array_column($students, 'StudentID');

        try {
            if ($db->tableExists('tb_club_members') && $db->tableExists('tb_club')) {
                $clubMembers = $db->table('tb_club_members cm')
                    ->select('cm.member_student_id, cm.member_role, c.club_id, c.club_name, c.club_status')
                    ->join('tb_club c', 'c.club_id = cm.member_club_id', 'left')
                    ->whereIn('cm.member_student_id', $studentIds)
                    ->where('cm.member_status', 'active')
                    ->get()
                    ->getResult();

                $clubMap = [];
                foreach ($clubMembers as $cm) {
                    $clubMap[$cm->member_student_id] = $cm;
                }

                foreach ($students as $st) {
                    $sId = $st->StudentID;
                    $activityStatus[$sId] = [
                        'club' => $clubMap[$sId] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'getActivitiesTrackingData error: ' . $e->getMessage());
        }

        return $activityStatus;
    }
}
