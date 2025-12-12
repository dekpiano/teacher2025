<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานสรุปผลการประเมิน - ชั้นเรียน <?= esc($className) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 10.5pt;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #000 !important;
            vertical-align: middle;
            padding: 0.2rem 0.4rem;
        }
        .signature-block {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 11pt;
        }
        .signature-block p {
            margin-bottom: 0;
            line-height: 1.5;
        }
        .signature-line {
            border-bottom: 1px dotted #000;
            min-width: 200px;
            display: inline-block;
        }
        .approval-box {
            border: 1px solid #000;
            padding: 0.75rem;
            margin-top: 1.5rem;
        }
        .approval-box p {
            margin-bottom: 0.5rem;
        }
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }
            body {
                -webkit-print-color-adjust: exact; 
                color-adjust: exact;
            }
            .table-secondary {
                background-color: #e9ecef !important;
            }
            .page-break {
                page-break-before: always;
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
    <div class="container-fluid">
        <div class="mb-3 text-center">
            <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="School Logo" style="width: 90px;">
            <h5 class="mb-1">แบบประเมินการอ่าน คิดวิเคราะห์และเขียน ชั้นมัธยมศึกษาปีที่ <?= esc($className) ?></h5>
                <h6 class="mb-1">ภาคเรียนที่ 1 ปีการศึกษา <?= esc($academicYear) ?> โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</h6>
            <strong>จำนวนนักเรียนที่ประเมิน:</strong> <?= esc($totalStudents) ?> คน
        </div>

        <div class="row">
            <div class="col-12">
                <?php if (!empty($assessmentItems)) : ?>
                    <table class="table table-bordered">
                        <thead class="text-center">
                            <tr>
                                <th rowspan="2" class="align-middle" style="width: 50%;">ผลการประเมิน</th>
                                <th colspan="4">ระดับคุณภาพ (จำนวนนักเรียน)</th>
                                <th rowspan="2" class="align-middle">รวม</th>
                            </tr>
                            <tr>
                                <th>ดีเยี่ยม</th>
                                <th>ดี</th>
                                <th>ผ่าน</th>
                                <th>ไม่ผ่าน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assessmentItems as $item) : ?>
                                <tr>
                                    <td><?= esc($item['ItemName']) ?></td>
                                    <td class="text-center"><?= esc($itemQualityCounts[$item['ItemID']]['ดีเยี่ยม']) ?></td>
                                    <td class="text-center"><?= esc($itemQualityCounts[$item['ItemID']]['ดี']) ?></td>
                                    <td class="text-center"><?= esc($itemQualityCounts[$item['ItemID']]['ผ่าน']) ?></td>
                                    <td class="text-center"><?= esc($itemQualityCounts[$item['ItemID']]['ไม่ผ่าน']) ?></td>
                                    <td class="text-center"><?= array_sum($itemQualityCounts[$item['ItemID']]) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="font-weight-bold table-secondary">
                                <td><strong>สรุปผลการประเมินภาพรวม</strong></td>
                                <td class="text-center"><strong><?= esc($overallQualityCounts['ดีเยี่ยม']) ?></strong></td>
                                <td class="text-center"><strong><?= esc($overallQualityCounts['ดี']) ?></strong></td>
                                <td class="text-center"><strong><?= esc($overallQualityCounts['ผ่าน']) ?></strong></td>
                                <td class="text-center"><strong><?= esc($overallQualityCounts['ไม่ผ่าน']) ?></strong></td>
                                <td class="text-center"><strong><?= esc($totalStudents) ?></strong></td>
                            </tr>
                            <tr class="font-weight-bold table-secondary">
                                <td><strong>คิดเป็นร้อยละ</strong></td>
                                <td class="text-center"><strong><?= esc($overallQualityPercentages['ดีเยี่ยม']) ?>%</strong></td>
                                <td class="text-center"><strong><?= esc($overallQualityPercentages['ดี']) ?>%</strong></td>
                                <td class="text-center"><strong><?= esc($overallQualityPercentages['ผ่าน']) ?>%</strong></td>
                                <td class="text-center"><strong><?= esc($overallQualityPercentages['ไม่ผ่าน']) ?>%</strong></td>
                                <td class="text-center"><strong>100.00%</strong></td>
                            </tr>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p>ไม่พบข้อมูลตัวชี้วัดการประเมิน</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="signature-block">
            <?php
            $teacherCount = count($homeroom_teachers);
            $colClass = 'col-6'; // Default for 1 or 2 teachers
            if ($teacherCount === 1) {
                $colClass = 'col-12';
            }
            ?>
            <?php if ($teacherCount === 3) : ?>
                <div class="row">
                    <div class="col-6">
                        <p>ลงชื่อ <span class="signature-line"></span><br>( <?= esc($homeroom_teachers[0]) ?> )<br>ครูที่ปรึกษา</p>
                    </div>
                    <div class="col-6">
                        <p>ลงชื่อ <span class="signature-line"></span><br>( <?= esc($homeroom_teachers[1]) ?> )<br>ครูที่ปรึกษา</p>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-12">
                        <p>ลงชื่อ <span class="signature-line"></span><br>( <?= esc($homeroom_teachers[2]) ?> )<br>ครูที่ปรึกษา</p>
                    </div>
                </div>
            <?php else : ?>
                <div class="row">
                    <?php foreach ($homeroom_teachers as $teacherName) : ?>
                        <div class="<?= $colClass ?>">
                            <p>ลงชื่อ <span class="signature-line"></span><br>( <?= esc($teacherName) ?> )<br>ครูที่ปรึกษา</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="row" style="margin-top: 2rem;">
                <div class="col-6">
                    <p>ลงชื่อ <span class="signature-line"></span><br>( <?= esc($grade_level_head) ?> )<br>หัวหน้าระดับชั้น</p>
                </div>
                <div class="col-6">
                    <p>ลงชื่อ <span class="signature-line"></span><br>( <?= esc($academic_head_name) ?> )<br><?= esc($academic_head_position) ?></p>
                </div>
            </div>
            <div class="row" style="margin-top: 2rem;">
                <div class="col-12">
                    <p>ลงชื่อ <span class="signature-line"></span><br>( <?= esc($deputy_director_name) ?> )<br><?= esc($deputy_director_position) ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-10 offset-1">
                    <div class="approval-box">
                        <p class="text-start"><strong>ความเห็นผู้อำนวยการสถานศึกษา</strong></p>
                        <p class="text-start"><input type="checkbox" disabled> อนุมัติ &nbsp;&nbsp;&nbsp; <input type="checkbox" disabled> ไม่อนุมัติ เนื่องจาก .......................................
                        <div class="signature-block" style="margin-top: 1rem;">
                            <p>ลงชื่อ <span class="signature-line"></span></p>
                            <p>( <?= esc($director_name) ?> )</p>
                            <p><?= esc($director_position) ?> โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Assessment Table (Page 2) -->
        <div class="page-break">
            <div class="mb-3 text-center">
            <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="School Logo" style="width: 90px;">
            <h6 class="mb-1">แบบประเมินการอ่าน คิดวิเคราะห์และเขียน ชั้นมัธยมศึกษาปีที่ <?= esc($className) ?></h6>
                <h6 class="mb-1">ภาคเรียนที่ 1 ปีการศึกษา <?= esc($academicYear) ?> โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</h6>
        </div>
            <div class="row">
                <div class="col-12">
                    <?php if (!empty($students)) : ?>
                        <table class="table table-bordered">
                            <thead class="text-center">
                                <tr>
                                    <th rowspan="2" class="align-middle">เลขที่</th>
                                    <th rowspan="2" class="align-middle" style="width: 30%;">ชื่อ - นามสกุล</th>
                                    <th colspan="<?= count($assessmentItems) ?>">ระดับคะแนน (0-3)</th>
                                    <th rowspan="2" class="align-middle">คะแนนรวม</th>
                                    <th rowspan="2" class="align-middle">ระดับคะแนน</th>
                                    <th rowspan="2" class="align-middle">ระดับคุณภาพ</th>
                                </tr>
                                <tr>
                                    <?php foreach ($assessmentItems as $item) : ?>
                                        <th style="width: 80px;">ตัวชี้วัดที่ <?= esc($item['ItemID']) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student) : ?>
                                    <tr>
                                        <td class="text-center"><?= esc($student['StudentNumber']) ?></td>
                                        <td><?= esc($student['StudentPrefix'] . $student['StudentFirstName'] . ' ' . $student['StudentLastName']) ?></td>
                                        <?php
                                        $totalScore = 0;
                                        $itemCount = 0;
                                        $assessedItems = 0;

                                        foreach ($assessmentItems as $item) {
                                            $score = $evaluations[$student['StudentID']][$item['ItemID']] ?? '';
                                            echo '<td class="text-center">' . esc(intVal($score)) . '</td>'; // Display score as text
                                            if (is_numeric($score)) {
                                                $totalScore += $score;
                                                $assessedItems++;
                                            }
                                            $itemCount++;
                                        }

                                        if ($assessedItems > 0) {
                                            $averageScore = $totalScore / $itemCount; 
                                            $qualityLevel = '-';
                                            if ($averageScore >= 2.51) {
                                                $qualityLevel = 'ดีเยี่ยม';
                                            } elseif ($averageScore >= 1.51) {
                                                $qualityLevel = 'ดี';
                                            } elseif ($averageScore >= 1.00) {
                                                $qualityLevel = 'ผ่าน';
                                            } else {
                                                $qualityLevel = 'ไม่ผ่าน';
                                            }
                                        } else {
                                            $totalScore = '-';
                                            $averageScore = '-';
                                            $qualityLevel = '-';
                                        }
                                        ?>
                                        <td class="text-center"><?= $totalScore ?></td>
                                        <td class="text-center"><?= is_numeric($averageScore) ? number_format($averageScore, 2) : '-' ?></td>
                                        <td class="text-center"><?= $qualityLevel ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p>ไม่พบข้อมูลนักเรียนในชั้นเรียนนี้</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>