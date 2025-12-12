<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ReadingAssessmentController extends BaseController
{
    private $teacherId;

    public function __construct()
    {
        $this->teacherId = session('person_id');
        if (empty($this->teacherId)) {
            // Using echo and exit because redirect() doesn't work in constructor in CI4
            echo '<script>window.location.href = "' . base_url('login') . '";</script>';
            exit();
        }
    }

    public function index()
    {
        $model = new \App\Models\ReadingAssessmentModel();
        $activeYearTerm = $model->getAssessmentAcademicYearAndTerm();
        $academicYear = $activeYearTerm['year'];
        $term = $activeYearTerm['term'];

        $teacherClasses = $model->getTeacherClasses($this->teacherId, $academicYear);

        // Add status to each class
        foreach ($teacherClasses as &$class) { // Use reference to modify array directly
            $status = $model->getAssessmentStatusForClass($class['Reg_Class'], $academicYear, $term);
            $class['status'] = $status;
        }

        $assessmentStatus = $model->getAssessmentOnOffStatus();

        $data = [
            'teacherClasses' => $teacherClasses,
            'academicYear' => $academicYear,
            'term' => $term,
            'assessmentStatus' => $assessmentStatus,
            'title' => 'แบบประเมินการอ่าน คิดวิเคราะห์ และเขียน'
        ];

        return view('teacher/reading_assessment/index', $data);
    }

    public function assessClass($class, $room)
    {
        $className = $class . '/' . $room;
        $model = new \App\Models\ReadingAssessmentModel();
        $activeYearTerm = $model->getAssessmentAcademicYearAndTerm();
        $academicYear = $activeYearTerm['year'];
        $term = $activeYearTerm['term'];

        $students = $model->getStudentsByHomeroomClass($className, $academicYear);
        $assessmentItems = $model->getAssessmentItems();
        $studentIds = array_column($students, 'StudentID');
        $evaluations = $model->getEvaluationsForClass($studentIds, $academicYear, $term);

        // --- Start Report Data Processing ---
        $reportData = [];
        $qualityLevels = ['ดีเยี่ยม' => 0, 'ดี' => 0, 'ผ่าน' => 0, 'ไม่ผ่าน' => 0];
        $itemQualityCounts = [];

        foreach ($assessmentItems as $item) {
            $itemQualityCounts[$item['ItemID']] = $qualityLevels;
        }

        $totalAssessedStudents = 0;

        foreach ($students as $student) {
            $studentTotalScore = 0;
            $studentAssessedItems = 0;
            $studentScores = [];

            if (isset($evaluations[$student['StudentID']])) {
                $totalAssessedStudents++;
                foreach ($assessmentItems as $item) {
                    $score = $evaluations[$student['StudentID']][$item['ItemID']] ?? '';
                    $studentScores[$item['ItemID']] = $score;

                    if (is_numeric($score) && $score !== '') {
                        $studentTotalScore += (int)$score;
                        $studentAssessedItems++;
                    }
                }

                if ($studentAssessedItems > 0) {
                    $averageScore = $studentTotalScore / count($assessmentItems);
                    $studentQualityLevel = 'ไม่ผ่าน';
                    if ($averageScore >= 2.51) {
                        $studentQualityLevel = 'ดีเยี่ยม';
                    } elseif ($averageScore >= 1.51) {
                        $studentQualityLevel = 'ดี';
                    } elseif ($averageScore >= 1.00) {
                        $studentQualityLevel = 'ผ่าน';
                    }
                    $qualityLevels[$studentQualityLevel]++;

                    foreach ($assessmentItems as $item) {
                        $itemScore = $studentScores[$item['ItemID']];
                        if (is_numeric($itemScore) && $itemScore !== '') {
                            $itemQuality = 'ไม่ผ่าน';
                            if ($itemScore == 3) {
                                $itemQuality = 'ดีเยี่ยม';
                            } elseif ($itemScore == 2) {
                                $itemQuality = 'ดี';
                            } elseif ($itemScore == 1) {
                                $itemQuality = 'ผ่าน';
                            }
                            $itemQualityCounts[$item['ItemID']][$itemQuality]++;
                        }
                    }
                }
            }
        }

        $overallQualityPercentages = [];
        foreach ($qualityLevels as $level => $count) {
            $overallQualityPercentages[$level] = ($totalAssessedStudents > 0) ? number_format(($count / $totalAssessedStudents) * 100, 2) : '0.00';
        }
        // --- End Report Data Processing ---

        $data = [
            'className' => $className,
            'students' => $students,
            'assessmentItems' => $assessmentItems,
            'evaluations' => $evaluations,
            'itemQualityCounts' => $itemQualityCounts,
            'overallQualityCounts' => $qualityLevels,
            'overallQualityPercentages' => $overallQualityPercentages,
            'totalStudents' => $totalAssessedStudents
        ];

        return view('teacher/reading_assessment/assess_class', $data);
    }

    public function printReport($class, $room)
    {
        $className = $class . '/' . $room;
        $model = new \App\Models\ReadingAssessmentModel();
        $activeYearTerm = $model->getAssessmentAcademicYearAndTerm();
        $academicYear = $activeYearTerm['year'];
        $term = $activeYearTerm['term'];

        // --- Get Signatory Names ---
        $homeroomTeachersData = $model->getHomeroomTeachersByClassAndYear($class . '/' . $room, $academicYear);
        
        $homeroom_teachers = [];
        foreach ($homeroomTeachersData as $teacherData) {
            $persId = $teacherData['class_teacher'];
            $teacherInfo = $model->getPersonnelFullName($persId);
            if ($teacherInfo) {
                $homeroom_teachers[] = $teacherInfo['pers_prefix'] . $teacherInfo['pers_firstname'] . ' ' . $teacherInfo['pers_lastname'];
            }
        }
//print_r($homeroom_teachers);exit();
        // Fallback if no homeroom teachers found in tb_regclass
        if (empty($homeroom_teachers)) {
            $homeroom_teacher_info = $model->getPersonnelFullName($this->teacherId);
            if ($homeroom_teacher_info) {
                $homeroom_teachers[] = $homeroom_teacher_info['pers_prefix'] . $homeroom_teacher_info['pers_firstname'] . ' ' . $homeroom_teacher_info['pers_lastname'];
            } else {
                $homeroom_teachers[] = '...........................................';
            }
        }

        $gradeLevelHeadId = $model->getGradeLevelHead($class, $academicYear);
        $grade_level_head_info = null;
        if ($gradeLevelHeadId) {
            $grade_level_head_info = $model->getPersonnelFullName($gradeLevelHeadId);
        }
        $grade_level_head = ($grade_level_head_info) ? $grade_level_head_info['pers_prefix'] . $grade_level_head_info['pers_firstname'] . ' ' . $grade_level_head_info['pers_lastname'] : '...........................................';

        // Academic Head
        $academicHeadInfo = $model->getAdminPersonnelInfoByPosition('หัวหน้างานวิชาการ');
        $academic_head_name = '...........................................';
        $academic_head_position = 'หัวหน้างานวิชาการ';
        if ($academicHeadInfo) {
            $academic_head_personnel = $model->getPersonnelFullName($academicHeadInfo['admin_rloes_userid']);
            if ($academic_head_personnel) {
                $academic_head_name = $academic_head_personnel['pers_prefix'] . $academic_head_personnel['pers_firstname'] . ' ' . $academic_head_personnel['pers_lastname'];
            }
            if (!empty($academicHeadInfo['admin_rloes_academic_position'])) {
                $academic_head_position = $academicHeadInfo['admin_rloes_academic_position'];
            }
        }

        // Deputy Director
        $deputyDirectorInfo = $model->getAdminPersonnelInfoByPosition('รองผู้อำนวยการฝ่ายวิชาการ');
        $deputy_director_name = '...........................................';
        $deputy_director_position = 'รองผู้อำนวยการฝ่ายวิชาการ';
        if ($deputyDirectorInfo) {
            $deputy_director_personnel = $model->getPersonnelFullName($deputyDirectorInfo['admin_rloes_userid']);
            if ($deputy_director_personnel) {
                $deputy_director_name = $deputy_director_personnel['pers_prefix'] . $deputy_director_personnel['pers_firstname'] . ' ' . $deputy_director_personnel['pers_lastname'];
            }
            if (!empty($deputyDirectorInfo['admin_rloes_academic_position'])) {
                $deputy_director_position = $deputyDirectorInfo['admin_rloes_academic_position'];
            }
        }

        // Director
        $directorInfo = $model->getAdminPersonnelInfoByPosition('ผู้อำนวยการสถานศึกษา');
        $director_name = '...........................................';
        $director_position = 'ผู้อำนวยการสถานศึกษา';
        if ($directorInfo) {
            $director_personnel = $model->getPersonnelFullName($directorInfo['admin_rloes_userid']);
            if ($director_personnel) {
                $director_name = $director_personnel['pers_prefix'] . $director_personnel['pers_firstname'] . ' ' . $director_personnel['pers_lastname'];
            }
            if (!empty($directorInfo['admin_rloes_academic_position'])) {
                $director_position = $directorInfo['admin_rloes_academic_position'];
            }
        }

        // --- Get Report Data ---
        $students = $model->getStudentsByHomeroomClass($className, $academicYear);
        $assessmentItems = $model->getAssessmentItems();
        $studentIds = array_column($students, 'StudentID');
        $evaluations = $model->getEvaluationsForClass($studentIds, $academicYear, $term);

        $qualityLevels = ['ดีเยี่ยม' => 0, 'ดี' => 0, 'ผ่าน' => 0, 'ไม่ผ่าน' => 0];
        $itemQualityCounts = [];
        foreach ($assessmentItems as $item) {
            $itemQualityCounts[$item['ItemID']] = $qualityLevels;
        }

        $totalAssessedStudents = 0;
        if(!empty($evaluations)){
            $totalAssessedStudents = count(array_keys($evaluations));
        }

        foreach ($evaluations as $studentId => $studentScores) {
            $studentTotalScore = array_sum($studentScores);
            $averageScore = $studentTotalScore / count($assessmentItems);

            $studentQualityLevel = 'ไม่ผ่าน';
            if ($averageScore >= 2.51) {
                $studentQualityLevel = 'ดีเยี่ยม';
            } elseif ($averageScore >= 1.51) {
                $studentQualityLevel = 'ดี';
            } elseif ($averageScore >= 1.00) {
                $studentQualityLevel = 'ผ่าน';
            }
            $qualityLevels[$studentQualityLevel]++;

            foreach ($studentScores as $itemId => $score) {
                $itemQuality = 'ไม่ผ่าน';
                if ($score == 3) {
                    $itemQuality = 'ดีเยี่ยม';
                } elseif ($score == 2) {
                    $itemQuality = 'ดี';
                } elseif ($score == 1) {
                    $itemQuality = 'ผ่าน';
                }
                if (isset($itemQualityCounts[$itemId])) {
                    $itemQualityCounts[$itemId][$itemQuality]++;
                }
            }
        }

        $overallQualityPercentages = [];
        foreach ($qualityLevels as $level => $count) {
            $overallQualityPercentages[$level] = ($totalAssessedStudents > 0) ? number_format(($count / $totalAssessedStudents) * 100, 2) : '0.00';
        }

        $data = [
            'className' => $className,
            'academicYear' => $academicYear,
            'students' => $students,
            'assessmentItems' => $assessmentItems,
            'evaluations' => $evaluations,
            'itemQualityCounts' => $itemQualityCounts,
            'overallQualityCounts' => $qualityLevels,
            'overallQualityPercentages' => $overallQualityPercentages,
            'totalStudents' => $totalAssessedStudents,
            'homeroom_teachers' => $homeroom_teachers,
            'grade_level_head' => $grade_level_head,
            'academic_head_name' => $academic_head_name,
            'academic_head_position' => $academic_head_position,
            'deputy_director_name' => $deputy_director_name,
            'deputy_director_position' => $deputy_director_position,
            'director_name' => $director_name,
            'director_position' => $director_position
        ];

        return view('teacher/reading_assessment/print_report', $data);
    }

    public function saveClassEvaluation()
    {
        $model = new \App\Models\ReadingAssessmentModel();
        $className = $this->request->getPost('className');
        $scores = $this->request->getPost('scores');
        $activeYearTerm = $model->getAssessmentAcademicYearAndTerm();

        $data = [
            'scores' => $scores,
            'term' => $activeYearTerm['term'],
            'academicYear' => $activeYearTerm['year'],
            'evaluatorId' => $this->teacherId
        ];

        if ($model->saveEvaluation($data)) {
            return redirect()->to(base_url('teacher/reading_assessment/assess/' . $className))->with('message', 'บันทึกข้อมูลสำเร็จ');
        } else {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
        }
    }
}
