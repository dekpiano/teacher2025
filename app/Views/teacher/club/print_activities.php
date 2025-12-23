<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานบันทึกเวลาเรียนชุมนุม</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 10pt;
            color: #000;
        }
        
        /* MIXED PAGE ORIENTATION RULES */
        /* Default: Portrait (A4) */
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }

        /* Named page for Landscape */
        @page landscape-section {
            size: A4 landscape;
            margin: 1cm;
        }

        .printable-page {
            page-break-before: always;
            position: relative;
        }
        .printable-page:first-child {
            page-break-before: avoidance;
        }

        /* Applying Landscape to Page 3 specifically */
        .landscape-page-3 {
            page: landscape-section;
        }

        /* COMPACT COVER STYLES - FIT IN 1 PAGE */
        .cover-container {
            padding: 0.5rem 0;
        }
        .school-logo {
            width: 80px;
            margin-bottom: 8px;
        }
        .cover-title {
            font-size: 20pt;
            font-weight: 700;
            margin-bottom: 3px;
        }
        .cover-subtitle {
            font-size: 14pt;
            margin-bottom: 8px;
        }
        .cover-info-table {
            width: 85%;
            margin: 1rem auto;
            border-collapse: collapse;
        }
        .cover-info-table td {
            border: 1px solid #000;
            padding: 6px 10px !important;
            font-size: 12pt;
            vertical-align: middle;
        }
        .cover-info-label {
            background-color: #f8f9fa;
            font-weight: 700;
            width: 30%;
            text-align: right;
        }
        .cover-info-value {
            text-align: left;
        }
        .approval-section {
            margin-top: 0.8rem;
        }
        .approval-box {
            border: 1.5px solid #000;
            padding: 12px 15px;
            display: inline-block;
            text-align: left;
            margin-bottom: 8px;
            min-width: 480px;
            font-size: 11pt;
        }
        .signature-line-group {
            margin-bottom: 8px;
        }
        .signature-line-group > div {
            margin-top: 2px !important;
        }
        .sig-dots {
            display: inline-block;
            width: 180px;
            border-bottom: 1px dotted #000;
            margin: 0 3px;
        }

        /* GENERAL TABLE STYLES */
        .table-bordered th, .table-bordered td {
            border: 1px solid #000 !important;
            vertical-align: middle;
            padding: 0.2rem 0.4rem;
        }
        .table-header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .table-bordered {
                font-size: 9pt;
            }
            /* COMPACT PAGE 3 FOR 30 STUDENTS */
            .landscape-page-3 {
                padding: 0.3rem !important;
            }
            .landscape-page-3 .table-bordered {
                font-size: 7pt;
            }
            .landscape-page-3 .table-bordered th,
            .landscape-page-3 .table-bordered td {
                padding: 1px 2px !important;
                line-height: 1.1;
            }
            .landscape-page-3 .table-header {
                margin-bottom: 0.3rem !important;
            }
            .landscape-page-3 .table-header p {
                margin-bottom: 0 !important;
                font-size: 9pt !important;
            }
        }
    </style>
    <script>
        window.onafterprint = function() {
            window.close();
        };
    </script>
</head>
<body onload="window.print()">

    <!-- PAGE 1: COVER (PORTRAIT) - COMPACT TO FIT 1 PAGE -->
    <div class="printable-page cover-container text-center">
        <div class="mb-2">
            <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="School Logo" class="school-logo">
            <h1 class="cover-title">สมุดประเมินผลกิจกรรมนักเรียน</h1>
            <p class="cover-subtitle">ระดับชั้นมัธยมศึกษาตอน<?= esc($club->club_level) ?></p>
            <p class="h6 mb-0 fw-bold">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</p>
            <p class="small mb-0">อำเภอเมืองนครสวรรค์ จังหวัดนครสวรรค์</p>
        </div>

        <table class="cover-info-table">
            <tbody>
                <tr>
                    <td class="cover-info-label">ชื่อกิจกรรม</td>
                    <td class="cover-info-value">ชุมนุม <?= esc($club->club_name) ?></td>
                </tr>
                <tr>
                    <td class="cover-info-label">ปีการศึกษา</td>
                    <td class="cover-info-value">ภาคเรียนที่ <?= esc($club->club_trem) ?> / <?= esc($club->club_year) ?></td>
                </tr>
                <tr>
                    <td class="cover-info-label">หัวหน้ากิจกรรม</td>
                    <td class="cover-info-value"><?= esc($activityHeadName) ?></td>
                </tr>
                <tr>
                    <td class="cover-info-label">ผู้ดูแลกิจกรรม</td>
                    <td class="cover-info-value"><?= esc($evaluatorName) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="approval-section text-center">
            <p class="fw-bold mb-2" style="font-size: 13pt; text-decoration: underline;">การอนุมัติผลกิจกรรม</p>
            
            <div class="approval-box">
                <div class="signature-line-group">
                    ลงชื่อ<span class="sig-dots"></span>ผู้ให้ระดับผลการเข้าร่วมกิจกรรม
                    <div style="padding-left: 35px;">( <?= esc($evaluatorName) ?> )</div>
                </div>
                <div class="signature-line-group">
                    ลงชื่อ<span class="sig-dots"></span>หัวหน้ากิจกรรม
                    <div style="padding-left: 35px;">( <?= esc($activityHeadName) ?> )</div>
                </div>
                <div class="signature-line-group">
                    ลงชื่อ<span class="sig-dots"></span>หัวหน้างานกิจกรรมพัฒนาผู้เรียน
                    <div style="padding-left: 35px;">( <?= esc($activityDevHeadName) ?> )</div>
                </div>
                <div class="signature-line-group">
                    ลงชื่อ<span class="sig-dots"></span>หัวหน้าฝ่ายวัดผล
                    <div style="padding-left: 35px;">( <?= esc($measurementHeadName) ?> )</div>
                </div>
                <div class="signature-line-group" style="margin-bottom: 0;">
                    ลงชื่อ<span class="sig-dots"></span>รองผู้อำนวยการฝ่ายวิชาการ
                    <div style="padding-left: 35px;">( <?= esc($deputyDirectorAcademicName) ?> )</div>
                </div>
            </div>

            <div class="approval-box">
                <div class="mb-2">
                    <span class="me-3">&#9744; อนุมัติ</span>
                    <span class="me-4">&#9744; ไม่อนุมัติ</span>
                    <span>เนื่องจาก......................................................</span>
                </div>
                <div class="signature-line-group">
                    ลงชื่อ<span class="sig-dots"></span>ผู้อำนวยการสถานศึกษา
                    <div style="padding-left: 40px; margin-top: 5px;">( <?= esc($directorName) ?> )</div>
                    <div style="padding-left: 60px; margin-top: 5px;">...../...../.......</div>
                </div>
            </div>
        </div>
    </div>

    <!-- PAGE 2: ACTIVITY SCHEDULE (PORTRAIT) - COMPACT FOR 20-22 ROWS -->
    <div class="printable-page container-fluid" style="padding: 1.5rem 2rem;">
        <div class="text-center mb-3">
            <p class="mb-0 fw-bold" style="font-size: 14pt;">กำหนดการจัดกิจกรรมการเรียนรู้</p>
            <p class="mb-0" style="font-size: 12pt;">กิจกรรม ชุมนุม <?= esc($club->club_name) ?> | ภาคเรียนที่ <?= esc($club->club_trem) ?>/<?= esc($club->club_year) ?></p>
            <p class="mb-0" style="font-size: 12pt;">ชั้นมัธยมศึกษาตอน<?= esc($club->club_level) ?> จำนวน <?= esc($club->club_max_participants) ?> คาบ</p>
        </div>

        <table class="table table-bordered" style="width: 100%; font-size: 11pt;">
            <thead class="text-center">
                <tr style="background-color: #f0f0f0;">
                    <th style="width: 60px; padding: 6px;">ลำดับที่</th>
                    <th style="padding: 6px;">กิจกรรม / หัวข้อการเรียนรู้</th>
                    <th style="width: 80px; padding: 6px;">เวลา (คาบ)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($activities)): ?>
                    <?php $totalPeriods = 0; ?>
                    <?php foreach ($activities as $index => $activity): ?>
                        <tr style="height: 28px;">
                            <td class="text-center" style="padding: 5px;"><?= esc($index + 1) ?></td>
                            <td style="padding: 5px 10px;"><?= esc($activity->act_name) ?></td>
                            <td class="text-center" style="padding: 5px;"><?= esc($activity->act_number_of_periods) ?></td>
                        </tr>
                        <?php $totalPeriods += $activity->act_number_of_periods; ?>
                    <?php endforeach; ?>
                    <tr style="background-color: #f0f0f0;">
                        <td colspan="2" class="text-end fw-bold" style="padding: 6px 10px;">รวมเวลาเรียนทั้งสิ้น</td>
                        <td class="text-center fw-bold" style="padding: 6px;"><?= esc($totalPeriods) ?></td>
                    </tr>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center" style="padding: 15px;">ไม่พบข้อมูลกิจกรรม</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGE 3: ATTENDANCE RECORD (LANDSCAPE) - OPTIMIZED FOR 30 STUDENTS -->
    <div class="printable-page container-fluid landscape-page-3 text-center" style="padding: 0.5rem;">
        <?php
            $studyTimePerWeek = ($club->club_level === 'ม.ปลาย') ? '2 ชม./สัปดาห์' : '1 คาบ/สัปดาห์';
            $totalStudyTime = ($club->club_level === 'ม.ปลาย') ? '40 ชม.' : '20 ชม.';
        ?>
        <div class="table-header mb-2" style="font-size: 10pt;">
            <p class="mb-0 fw-bold">การบันทึกเวลาเรียน ชุมนุม <?= esc($club->club_name) ?> | ภาคเรียนที่ <?= esc($club->club_trem) ?>/<?= esc($club->club_year) ?> | เวลาเรียน <?= esc($studyTimePerWeek) ?> รวม <?= esc($totalStudyTime) ?></p>
        </div>

        <table class="table table-bordered mb-0" style="width: 100%; font-size: 7pt;">
            <thead class="bg-light">
                <?php $thaiMonthsShort = ['January' => 'ม.ค.', 'February' => 'ก.พ.', 'March' => 'มี.ค.', 'April' => 'เม.ย.', 'May' => 'พ.ค.', 'June' => 'มิ.ย.', 'July' => 'ก.ค.', 'August' => 'ส.ค.', 'September' => 'ก.ย.', 'October' => 'ต.ค.', 'November' => 'พ.ย.', 'December' => 'ธ.ค.']; ?>
                <tr class="text-center">
                    <th rowspan="2" class="align-middle" style="width: 30px; padding: 2px !important;">ที่</th>
                    <th rowspan="2" class="align-middle" style="min-width: 120px; padding: 2px !important;">ชื่อ - นามสกุล</th>
                    <th rowspan="2" class="align-middle" style="width: 40px; padding: 2px !important;">ชั้น</th>
                    <?php foreach ($schedulesByMonth as $month => $schedulesInMonth): ?>
                        <?php $shortMonth = $thaiMonthsShort[date('F', strtotime($month))] ?? date('M', strtotime($month)); ?>
                        <th colspan="<?= count($schedulesInMonth) ?>" style="padding: 2px !important;"><?= $shortMonth . (date('Y', strtotime($month)) + 543 - 2500) ?></th>
                    <?php endforeach; ?>
                    <th rowspan="2" class="align-middle" style="width: 30px; padding: 2px !important;">รวม</th>
                    <th rowspan="2" class="align-middle" style="width: 30px; padding: 2px !important;">ผล</th>
                </tr>
                <tr class="text-center">
                    <?php foreach ($schedulesByMonth as $month => $schedulesInMonth): ?>
                        <?php foreach ($schedulesInMonth as $schedule): ?>
                            <th style="min-width: 18px; padding: 1px !important; font-size: 6pt;"><?= date('d', strtotime($schedule->tcs_start_date)) ?></th>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php $totalDisplayedSchedules = 0; if (!empty($schedulesByMonth)) { foreach ($schedulesByMonth as $schedulesInMonth) { $totalDisplayedSchedules += count($schedulesInMonth); } } ?>
                <?php if (!empty($members)): ?>
                    <?php foreach ($members as $member): ?>
                        <tr style="height: 16px;">
                            <td class="text-center" style="padding: 1px !important;"><?= esc($member->StudentNumber) ?></td>
                            <td class="text-start" style="padding: 1px 3px !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px;"><?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?></td>
                            <td class="text-center" style="padding: 1px !important;"><?= esc($member->StudentClass) ?></td>
                            <?php $totalPresent = 0; foreach ($schedulesByMonth as $month => $schedulesInMonth): foreach ($schedulesInMonth as $schedule):
                                $status = $attendanceMap[$member->StudentID][$schedule->tcs_schedule_id] ?? '-';
                                if ($status === 'มา') $totalPresent++;
                            ?>
                                <td class="text-center" style="padding: 0 !important;"><?= ($status === 'มา') ? '&#10003;' : ($status === 'ขาด' ? 'O' : '-') ?></td>
                            <?php endforeach; endforeach; ?>
                            <td class="text-center fw-bold" style="padding: 1px !important;"><?= $totalPresent ?></td>
                            <td class="text-center fw-bold" style="padding: 1px !important;">
                                <?php if ($totalDisplayedSchedules > 0) { $percentage = ($totalPresent / $totalDisplayedSchedules) * 100; echo ($percentage >= 80) ? 'ผ' : 'มผ'; } else { echo '-'; } ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
    </div>

    <!-- PAGE 4: OBJECTIVES EVALUATION (PORTRAIT) - COMPACT FOR 25-30 STUDENTS -->
    <div class="printable-page container-fluid" style="padding: 1rem 1.5rem;">
        <div class="text-center mb-2">
            <p class="mb-0 fw-bold" style="font-size: 13pt;">รายงานผลการประเมินตามจุดประสงค์</p>
            <p class="mb-0" style="font-size: 11pt;">กิจกรรมชุมนุม <?= esc($club->club_name) ?> | ภาคเรียนที่ <?= esc($club->club_trem) ?>/<?= esc($club->club_year) ?></p>
        </div>
        <?php if (!empty($club_objectives)): ?>
            <table class="table table-bordered" style="width: 100%; font-size: 9pt;">
                <thead class="text-center" style="background-color: #f0f0f0;">
                    <tr>
                        <th rowspan="2" class="align-middle" style="width: 35px; padding: 3px;">ที่</th>
                        <th rowspan="2" class="align-middle" style="min-width: 120px; padding: 3px;">ชื่อ - นามสกุล</th>
                        <th rowspan="2" class="align-middle" style="width: 45px; padding: 3px;">ชั้น</th>
                        <th colspan="<?= count($club_objectives) ?>" style="padding: 3px;">จุดประสงค์ข้อที่</th>
                        <th rowspan="2" class="align-middle" style="width: 35px; padding: 3px;">รวม</th>
                        <th rowspan="2" class="align-middle" style="width: 35px; padding: 3px;">ผล</th>
                    </tr>
                    <tr>
                        <?php foreach ($club_objectives as $objective): ?>
                            <th style="width: 25px; padding: 2px; font-size: 8pt;"><?= esc($objective->objective_order) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr style="height: 15px;">
                            <td class="text-center" style="padding: 1px;"><?= esc($member->StudentNumber) ?></td>
                            <td style="padding: 1px 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px;"><?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?></td>
                            <td class="text-center" style="padding: 1px;"><?= esc($member->StudentClass) ?></td>
                            <?php $totalPassed = 0; foreach ($club_objectives as $objective):
                                $hasPassed = $objectiveProgressMap[$member->StudentID][$objective->objective_id] ?? false;
                                if ($hasPassed) $totalPassed++;
                            ?>
                                <td class="text-center" style="padding: 0;"><?= ($hasPassed) ? '&#10003;' : '&#10007;' ?></td>
                            <?php endforeach; ?>
                            <td class="text-center fw-bold" style="padding: 1px;"><?= $totalPassed ?></td>
                            <td class="text-center fw-bold" style="padding: 1px;">
                                <?php if (count($club_objectives) > 0) { echo ($totalPassed === count($club_objectives)) ? 'ผ' : 'มผ'; } else { echo '-'; } ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center" style="font-size: 12pt; margin-top: 3rem;">- ยังไม่มีการกำหนดจุดประสงค์ของกิจกรรม -</p>
        <?php endif; ?>
    </div>

    <!-- PAGE 5: SUMMARY & OBJECTIVES (PORTRAIT) - FORMAL STYLE -->
    <div class="printable-page container-fluid" style="padding: 2rem 3rem;">
        <div class="mb-5">
            <p class="fw-bold mb-3" style="font-size: 16pt; text-decoration: underline;">จุดประสงค์กิจกรรม</p>
            <?php if (!empty($club_objectives)): ?>
                <table class="table table-bordered" style="width: 100%; font-size: 14pt;">
                    <tbody>
                        <?php foreach ($club_objectives as $objective): ?>
                            <tr>
                                <td style="width: 80px; padding: 10px;" class="text-center fw-bold"><?= esc($objective->objective_order) ?>.</td>
                                <td style="padding: 10px 15px;"><?= esc($objective->objective_name) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center" style="font-size: 14pt;">- ยังไม่มีข้อมูลจุดประสงค์ -</p>
            <?php endif; ?>
        </div>

        <div>
            <p class="fw-bold mb-3" style="font-size: 16pt; text-decoration: underline;">สรุปผลการเข้าร่วมกิจกรรม</p>
            <table class="table table-bordered text-center" style="width: 100%; font-size: 14pt;">
                <thead style="background-color: #f0f0f0;">
                    <tr>
                        <th rowspan="2" class="align-middle" style="padding: 12px;">จำนวนนักเรียน<br>ทั้งหมด</th>
                        <th colspan="4" style="padding: 10px;">ระดับผลการเข้าร่วมกิจกรรม</th>
                    </tr>
                    <tr>
                        <th style="width: 18%; padding: 10px;">ผ่าน</th>
                        <th style="width: 18%; padding: 10px;">ไม่ผ่าน</th>
                        <th style="width: 18%; padding: 10px;">ขาดเรียนนาน</th>
                        <th style="width: 18%; padding: 10px;">จำหน่าย</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold" style="padding: 15px; font-size: 18pt;"><?= esc($summaryParticipation['totalStudents']) ?></td>
                        <td class="fw-bold" style="padding: 15px; font-size: 18pt;"><?= esc($summaryParticipation['passed']) ?></td>
                        <td class="fw-bold" style="padding: 15px; font-size: 18pt;"><?= esc($summaryParticipation['failed']) ?></td>
                        <td style="padding: 15px; font-size: 18pt;"><?= esc($summaryParticipation['longAbsence']) ?></td>
                        <td style="padding: 15px; font-size: 18pt;"><?= esc($summaryParticipation['dismissed']) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>