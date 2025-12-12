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
        }
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
        .rotate-text {
            white-space: nowrap;
            transform: rotate(-90deg);
            transform-origin: center center;
            display: inline-block;
            height: 50px; /* Adjust as needed */
            width: 10px; /* Adjust as needed */
        }
        @media print {
            @page {
                size: A4 portrait;
                margin: 1cm;
            }
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .table-bordered {
                font-size: 8pt; /* Smaller font for print */
            }
            .table-bordered th, .table-bordered td {
                padding: 0.1rem 0.2rem;
            }
            .printable-page {
                page-break-before: always;
                padding-top: 40px;
            }
            .printable-page:first-child {
                page-break-before: auto;
                padding-top: 0;
            }
        }
    </style>
    <script>
        window.onafterprint = function() {
            window.close();
        };
    </script>
</head>
<!-- onload="window.print()" -->
<body onload="window.print()">

 <!-- Page 4: Student Activity Evaluation Book -->
    <div class="printable-page">
        <div class="text-center mb-4">
            <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="School Logo" style="width: 90px; margin-bottom: 10px;">
            <p class="mb-0 fw-bold h3">สมุดประเมินผลกิจกรรมนักเรียน</p>
            <p class="mb-0 h5">ระดับชั้น <?= esc($club->club_level) ?></p>
            <p class="mb-0 h5">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</p>
            <p class="mb-0 h5">อำเภอเมืองนครสวรรค์ จังหวัดนครสวรรค์</p>
        </div>

        <table class="table table-bordered" style="width: 100%; margin-bottom: 2rem; font-size: 12pt;">
            <tbody>
                <tr>
                    <td style="width: 30%;">ชื่อกิจกรรม</td>
                    <td>ชุมนุม <?= esc($club->club_name) ?></td>
                </tr>
                <tr>
                    <td>ปีการศึกษา</td>
                    <td><?= esc($club->club_trem) ?> / <?= esc($club->club_year) ?></td>
                </tr>
                <tr>
                    <td>หัวหน้ากิจกรรม</td>
                    <td><?= esc($activityHeadName) ?></td>
                </tr>
                <tr>
                    <td>ผู้ดูแลกิจกรรม</td>
                    <td><?= esc($evaluatorName) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="text-center fw-bold mb-3 h5">การอนุมัติผลกิจกรรม</div>

        <div class="row  h6" style="">
            <div class="col-12 offset-48" style="margin-left: 8rem;border: 2px solid #000;width: auto;padding: 1rem;">
                <p class="mb-0">ลงชื่อ...........................................ผู้ให้ระดับผลการเข้าร่วมกิจกรรม</p>
                <p class="mb-4" style="margin-left: 2rem;">( <?= esc($evaluatorName) ?> )</p>

                <p class="mb-0">ลงชื่อ...........................................หัวหน้ากิจกรรม</p>
                <p class="mb-4" style="margin-left: 2rem;">( <?= esc($activityHeadName) ?> )</p>

                <p class="mb-0">ลงชื่อ...........................................หัวหน้างานกิจกรรมพัฒนาผู้เรียน</p>
                <p class="mb-4" style="margin-left: 2rem;">( <?= esc($activityDevHeadName) ?> )</p>

                <p class="mb-0">ลงชื่อ...........................................หัวหน้าฝ่ายวัดผล</p>
                <p class="mb-4" style="margin-left: 2rem;">( <?= esc($measurementHeadName) ?> )</p>
                 <p class="mb-0">ลงชื่อ...........................................รองผู้อำนวยการฝ่ายวิชาการ</p>
                <p class="mb-4" style="margin-left: 2rem;">( <?= esc($deputyDirectorAcademicName) ?> )</p>
            </div>
            <br>
            <div class="col-12 mt-4" style="margin-left: 10rem;border: 2px solid #000;width: auto;padding: 1rem;">

                <p class="mb-4">☐ อนุมัติ &nbsp;&nbsp;&nbsp; ☐ ไม่อนุมัติ  เนื่องจาก...........................................</p> 
               

                <p class="mb-0">ลงชื่อ...........................................ผู้อำนวยการสถานศึกษา</p>
                <p class="mb-0" style="margin-left: 2rem;">( <?= esc($directorName) ?> )</p>
                <p class="mb-4" style="margin-left: 5rem;">...../...../.......</p>
            </div>
        </div>
    </div>
    <!-- New Page 1 Content -->
    <div class="printable-page container-fluid">
        <div class="table-header h5" >
            <p class="mb-0">กำหนดการจัดกิจกรรมการเรียนรู้</p>
            <p class="mb-0">กิจกรรม ชุมนุม <?= esc($club->club_name) ?> ภาคเรียนที่ <?= esc($club->club_trem) ?>  ปีการศึกษา  <?= esc($club->club_year) ?></p>
            <p class="mb-0">ชั้นมัธยมศึกษาตอน  <?= esc($club->club_level) ?>  จำนวน  <?= esc($club->club_max_participants) ?> คาบ</p>
        </div>

        <table class="table table-bordered" style="width: 100%;font-size: 12pt;">
            <thead>
                <tr class="text-center">
                    <th style="width: 50px;">ลำดับที่</th>
                    <th>กิจกรรม</th>
                    <th style="width: 80px;">เวลา/คาบ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($activities)): ?>
                    <?php $totalPeriods = 0; ?>
                    <?php foreach ($activities as $index => $activity): ?>
                        <tr>
                            <td class="text-center"><?= esc($index + 1) ?></td>
                            <td><?= esc($activity->act_name) ?></td>
                            <td class="text-center"><?= esc($activity->act_number_of_periods) ?></td>
                        </tr>
                        <?php $totalPeriods += $activity->act_number_of_periods; ?>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" class="text-end">รวม</td>
                        <td class="text-center"><?= esc($totalPeriods) ?></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">ไม่พบกิจกรรมสำหรับชุมนุมนี้</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
            $studyTimePerWeek = '';
            $totalStudyTime = '';
            if ($club->club_level === 'ม.ปลาย') {
                $studyTimePerWeek = '2 ชั่วโมง/สัปดาห์';
                $totalStudyTime = '40 ชั่วโมง';
            } elseif ($club->club_level === 'ม.ต้น') {
                $studyTimePerWeek = '1 คาบ/สัปดาห์';
                $totalStudyTime = '20 ชั่วโมง';
            }
            ?>

    <div class="printable-page container-fluid"> <!-- Existing Page 1 content starts here -->
        <div class="table-header h5">
            <p class="mb-0">ชื่อกิจกรรม ชุมนุม <?= esc($club->club_name) ?></p>
            <p class="mb-0">เวลาเรียน <?= esc($studyTimePerWeek) ?>  รวมเวลาเรียน <?= esc($totalStudyTime) ?></p>
            <p class="mb-0">การบันทึกเวลาเรียน</p>
        </div>

        <div class="table-responsive">
        <table class="table table-bordered" style="width: 100%;">
            <thead>
                <?php
                    $thaiMonths = [
                        'January' => 'มกราคม', 'February' => 'กุมภาพันธ์', 'March' => 'มีนาคม',
                        'April' => 'เมษายน', 'May' => 'พฤษภาคม', 'June' => 'มิถุนายน',
                        'July' => 'กรกฎาคม', 'August' => 'สิงหาคม', 'September' => 'กันยายน',
                        'October' => 'ตุลาคม', 'November' => 'พฤศจิกายน', 'December' => 'ธันวาคม',
                    ];
                ?>
                <tr class="text-center">
                    <th rowspan="3" class="align-middle">เลขที่</th>
                    <th rowspan="3" class="align-middle" style="min-width: 150px;">ชื่อ - นามสกุล</th>
                    <th rowspan="3" class="align-middle">ชั้น</th>
                    <?php foreach ($schedulesByMonth as $month => $schedulesInMonth): ?>
                        <?php
                            $englishMonthName = date('F', strtotime($month));
                            $thaiMonthName = $thaiMonths[$englishMonthName] ?? $englishMonthName;
                            $year = date('Y', strtotime($month));
                        ?>
                        <th colspan="<?= count($schedulesInMonth) ?>"><?= $thaiMonthName . ' ' . ($year + 543) ?></th>
                    <?php endforeach; ?>
                    <th rowspan="3" class="align-middle">รวม</th>
                    <th rowspan="3" class="align-middle">ผลของ<br>เวลาเรียน<br>ผ/มผ</th>
                </tr>
                <tr class="text-center">
                    <?php foreach ($schedulesByMonth as $month => $schedulesInMonth): ?>
                        <?php foreach ($schedulesInMonth as $schedule): ?>
                            <th style="min-width: 25px;"><?= date('d', strtotime($schedule->tcs_start_date)) ?></th>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Calculate the total number of schedules that are actually being displayed in the table
                    $totalDisplayedSchedules = 0;
                    if (!empty($schedulesByMonth)) {
                        foreach ($schedulesByMonth as $schedulesInMonth) {
                            $totalDisplayedSchedules += count($schedulesInMonth);
                        }
                    }
                ?>
                <?php if (!empty($members)): ?>
                    <?php $i = 1; ?>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>
                            <td><?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?></td>
                            <td class="text-center"><?= esc($member->StudentClass) ?></td>
                            <?php
                                $totalPresent = 0;
                                foreach ($schedulesByMonth as $month => $schedulesInMonth):
                                    foreach ($schedulesInMonth as $schedule):
                                        $status = $attendanceMap[$member->StudentID][$schedule->tcs_schedule_id] ?? '-';
                                        if ($status === 'มา') {
                                            $totalPresent++;
                                        }
                            ?>
                                <td class="text-center">
                                    <?php if ($status === 'มา'): ?>
                                        &#10003; <!-- Checkmark -->
                                    <?php else: ?>
                                        O <!-- Circle for any non-'มา' status -->
                                    <?php endif; ?>
                                </td>
                            <?php
                                    endforeach;
                                endforeach;
                            ?>
                            <td class="text-center"><?= $totalPresent ?></td>
                            <td class="text-center">
                                <?php
                                    if ($totalDisplayedSchedules > 0) {
                                        $percentage = ($totalPresent / $totalDisplayedSchedules) * 100;
                                        $result = ($percentage >= 80) ? 'ผ' : 'มผ';
                                        echo "<strong>{$result}</strong>";
                                    } else {
                                        echo '-';
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= 3 + $totalDisplayedSchedules + 2 ?>" class="text-center">ไม่พบสมาชิกในชุมนุมนี้</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>

<div class="printable-page">
        <div class="table-header h5">
            <p class="mb-0">รายงานผลการประเมินตามจุดประสงค์</p>
            <p class="mb-0">ชุมนุม <?= esc($club->club_name) ?></p>
            <?php
            $studyTimePerWeek = '';
            $totalStudyTime = '';
            if ($club->club_level === 'ม.ปลาย') {
                $studyTimePerWeek = '2 ชั่วโมง/สัปดาห์';
                $totalStudyTime = '40 ชั่วโมง';
            } elseif ($club->club_level === 'ม.ต้น') {
                $studyTimePerWeek = '1 คาบ/สัปดาห์';
                $totalStudyTime = '20 ชั่วโมง';
            }
            ?>
            <p class="mb-0">เวลาเรียน <?= esc($studyTimePerWeek) ?> รวมเวลาเรียน <?= esc($totalStudyTime) ?></p>
        </div>
        <?php  if (!empty($club_objectives)): ?>
            <div class="table-responsive">
                <table class="table table-bordered" style="width: 100%;">
                    <thead>
                        <tr class="text-center">
                            <th rowspan="2" class="align-middle">เลขที่</th>
                            <th rowspan="2" class="align-middle" style="min-width: 150px;">ชื่อ - นามสกุล</th>
                            <th rowspan="2" class="align-middle">ชั้น</th> <!-- Added Class column -->
                            <th colspan="<?= count($club_objectives) ?>">จุดประสงค์ที่</th>
                            <th rowspan="2" class="align-middle">รวมที่ผ่าน</th>
                            <th rowspan="2" class="align-middle">ผล (ผ/มผ)</th>
                        </tr>
                        <tr class="text-center">
                            <?php foreach ($club_objectives as $objective): ?>
                                <th><?= esc($objective->objective_order) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($members)): ?>
                            <?php $i = 1; ?>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td class="text-center"><?= $i++ ?></td>
                                    <td><?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?></td>
                                    <td class="text-center"><?= esc($member->StudentClass) ?></td> <!-- Added Class data -->
                                    <?php
                                        $totalPassed = 0;
                                        foreach ($club_objectives as $objective):
                                            $hasPassed = $objectiveProgressMap[$member->StudentID][$objective->objective_id] ?? false;
                                            if ($hasPassed) {
                                                $totalPassed++;
                                            }
                                    ?>
                                        <td class="text-center">
                                            <?php if ($hasPassed): ?>
                                                &#10003; <!-- Checkmark -->
                                            <?php else: ?>
                                                &#10007; <!-- Cross mark -->
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="text-center"><?= $totalPassed ?></td>
                                    <td class="text-center">
                                        <?php
                                            $totalObjectivesCount = count($club_objectives);
                                            if ($totalObjectivesCount > 0) {
                                                $result = ($totalPassed === $totalObjectivesCount) ? 'ผ' : 'มผ';
                                                echo "<strong>{$result}</strong>";
                                            } else {
                                                echo '-';
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= 3 + count($club_objectives) + 2 ?>" class="text-center">ไม่พบสมาชิกในชุมนุมนี้</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center mt-4">ยังไม่มีการกำหนดจุดประสงค์สำหรับชุมนุมนี้</p>
        <?php endif; ?>
    </div>

    <!-- Page 3: Objectives and Participation Summary -->
    <div class="printable-page">
        <div class="table-header h5">
            <p class="mb-0">จุดประสงค์กิจกรรม</p>
        </div>
        <?php if (!empty($club_objectives)): ?>
            <table class="table table-bordered" style="width: 100%;font-size: 12pt;">
                <tbody>
                    <?php foreach ($club_objectives as $objective): ?>
                        <tr>
                            <td><?= esc($objective->objective_order) ?>. <?= esc($objective->objective_name) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center mt-4">ยังไม่มีการกำหนดจุดประสงค์สำหรับชุมนุมนี้</p>
        <?php endif; ?>

        <div class="table-header mt-4 h5">
            <p class="mb-0">สรุปผลการเข้าร่วมกิจกรรม</p>
        </div>
        <table class="table table-bordered" style="width: 100%; font-size: 12pt;">
            <thead>
                <tr class="text-center">
                    <th rowspan="2">จำนวนนักเรียน</th>
                    <th colspan="4">ระดับผลการเข้าร่วมกิจกรรม</th>
                </tr>
                <tr class="text-center">
                    <th>ผ่าน</th>
                    <th>ไม่ผ่าน</th>
                    <th>ขาดเรียนนาน</th>
                    <th>จำหน่าย</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-center">
                    <td><?= esc($summaryParticipation['totalStudents']) ?></td>
                    <td><?= esc($summaryParticipation['passed']) ?></td>
                    <td><?= esc($summaryParticipation['failed']) ?></td>
                    <td><?= esc($summaryParticipation['longAbsence']) ?></td>
                    <td><?= esc($summaryParticipation['dismissed']) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

   
</body>
</html>