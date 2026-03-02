<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานสรุปผลการประเมินคุณลักษณะอันพึงประสงค์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Sarabun', sans-serif;
    }

    .print-container {
        max-width: 1100px;
        /* Wider for landscape */
        margin: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid black;
        padding: 5px;
        text-align: center;
    }

    .header-text {
        text-align: center;
        font-weight: bold;
        font-size: 1.1rem;
    }

    .report-header p {
        margin-bottom: 0.25rem;
    }

    .signature-section {
        margin-top: 50px;
        line-height: 1.8;
    }

    .signature-line {
        border-bottom: 1px dotted black;
        width: 250px;
        display: inline-block;
    }

            .assessment-table-print .sub-item-col, 
            .assessment-table-print .summary-col-print {
                width: 32px;
                min-width: 32px;
                max-width: 32px;
            }
            .assessment-table-print .summary-col-final-print {
                width: 75px;
                min-width: 75px;
                max-width: 75px;
            }
            .compact-text-cell {
                font-size: 0.5rem !important;
                white-space: nowrap;
                padding: 1px !important;
                display: block;
                text-align: center;
                line-height: 1;
                writing-mode: horizontal-tb !important;
                transform: none !important;
            }
            .vertical-header-cell {
                font-size: 0.65rem !important;
                white-space: nowrap;
                padding: 1px !important;
                display: inline-block; /* Changed to inline-block for centering */
                text-align: center;
                line-height: 1;
                writing-mode: vertical-rl !important;
                transform: rotate(180deg) !important;
                margin: 0 auto;
            }
            .text-danger-custom {
                color: #dc3545 !important;
                font-weight: 800 !important;
            }
            .main-item-name-header {
                white-space: nowrap;
            }
    .signature-name {
        margin-top: -10px;
        margin-bottom: 20px;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 5mm;
        }

        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print {
            display: none;
        }

        .page-1 {
            font-size: 0.95rem;
        }

        .page-1 table th,
        .page-1 table td {
            padding: 2px;
        }

        .page-1 .signature-section {
            margin-top: 20px;
            line-height: 1.2;
        }
    }
    .text-danger-custom {
        color: #dc3545 !important;
        font-weight: bold !important;
    }
    </style>
</head>

<body>
    <div class="print-container">
        <div class="page-1">
            <div class="report-header">
                <p class="header-text">แบบประเมินคุณลักษณะอันพึงประสงค์ ชั้นมัธยมศึกษาปีที่ <?= esc($className) ?></p>
                <p class="header-text">ภาคเรียนที่ <?= esc($term) ?> ปีการศึกษา <?= esc($academicYear) ?>
                    โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</p>

                <p class="text-center"><b>จำนวนนักเรียนทั้งหมด:</b> <?= esc($totalStudents) ?> คน, <b>ประเมินแล้ว:</b>
                    <?= esc($totalAssessedStudents) ?> คน</p>
            </div>

            <table>
                <thead class="table-light">
                    <tr>
                        <th>ผลการประเมิน</th>
                        <th>ดีเยี่ยม</th>
                        <th>ดี</th>
                        <th>ผ่าน</th>
                        <th>ไม่ผ่าน</th>
                        <th>รวม</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalAssessedStudents > 0): ?>
                    <?php foreach ($report as $key => $mainItemReport): ?>
                    <tr>
                        <td class="text-start"><?=($key+0).'.'. esc($mainItemReport['name']) ?></td>
                        <td><?= $mainItemReport['summary']['ดีเยี่ยม'] ?></td>
                        <td><?= $mainItemReport['summary']['ดี'] ?></td>
                        <td><?= $mainItemReport['summary']['ผ่าน'] ?></td>
                        <td><?= $mainItemReport['summary']['ไม่ผ่าน'] ?></td>
                        <td><?= array_sum($mainItemReport['summary']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold">
                        <td class="text-end">รวมผลการประเมินนักเรียน</td>
                        <td><?= $overallSummary['ดีเยี่ยม'] ?></td>
                        <td><?= $overallSummary['ดี'] ?></td>
                        <td><?= $overallSummary['ผ่าน'] ?></td>
                        <td><?= $overallSummary['ไม่ผ่าน'] ?></td>
                        <td><?= $totalAssessedStudents ?></td>
                    </tr>
                    <tr class="fw-bold">
                        <td class="text-end">คิดเป็นร้อยละ</td>
                        <td><?= number_format(($overallSummary['ดีเยี่ยม'] / $totalAssessedStudents) * 100, 2) ?>%</td>
                        <td><?= number_format(($overallSummary['ดี'] / $totalAssessedStudents) * 100, 2) ?>%</td>
                        <td><?= number_format(($overallSummary['ผ่าน'] / $totalAssessedStudents) * 100, 2) ?>%</td>
                        <td><?= number_format(($overallSummary['ไม่ผ่าน'] / $totalAssessedStudents) * 100, 2) ?>%</td>
                        <td>100.00%</td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td colspan="6">ยังไม่มีข้อมูลการประเมิน</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="signature-section">
                <?php
                $teacherCount = count($homeroom_teachers);
                $colClass = 'col-6'; // Default for 1 or 2 teachers
                if ($teacherCount === 1) {
                    $colClass = 'col-12';
                }
                ?>
                <?php if ($teacherCount === 3) : ?>
                    <div class="row">
                        <div class="col-6 text-center">
                            <p>ลงชื่อ...........................................ครูที่ปรึกษา</p>
                            <p class="signature-name">( <?= esc($homeroom_teachers[0]) ?> )</p>
                        </div>
                        <div class="col-6 text-center">
                            <p>ลงชื่อ...........................................ครูที่ปรึกษา</p>
                            <p class="signature-name">( <?= esc($homeroom_teachers[1]) ?> )</p>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12 text-center">
                            <p>ลงชื่อ...........................................ครูที่ปรึกษา</p>
                            <p class="signature-name">( <?= esc($homeroom_teachers[2]) ?> )</p>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="row">
                        <?php foreach ($homeroom_teachers as $index => $teacherName) : ?>
                            <div class="<?= $colClass ?> text-center">
                                <p>ลงชื่อ...........................................ครูที่ปรึกษา</p>
                                <p class="signature-name">( <?= esc($teacherName) ?> )</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-6 text-center">
                        <p>ลงชื่อ...........................................หัวหน้าระดับชั้น</p>
                        <p class="signature-name">( <?= esc($grade_level_head) ?> )</p>
                    </div>
                    <div class="col-6 text-center">
                        <p>ลงชื่อ...........................................<?= esc($academic_head_position) ?></p>
                        <p class="signature-name">( <?= esc($academic_head_name) ?> )</p>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12 text-center">                       

                        <p>ลงชื่อ...........................................<?= esc($deputy_director_position) ?></p>
                        <p class="signature-name">( <?= esc($deputy_director_name) ?> )</p>
<br>
                         <p>☐ อนุมัติ &nbsp;&nbsp;&nbsp; ☐ ไม่อนุมัติ
                            เนื่องจาก...........................................</p>

                        <p>ลงชื่อ...........................................<?= esc($director_position) ?></p>
                        <p class="signature-name">( <?= esc($director_name) ?> )</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page 2: Detailed Assessment Table -->
    <div style="page-break-before: always;">

        <?php if (!empty($students) && !empty($assessmentItems)): ?>
        <table class="mt-3 assessment-table-print" style="font-size: 0.8rem;">
            <thead style="background-color: #e9ecef;">
                <tr>
                    <th rowspan="4">เลขที่</th>
                    <th rowspan="4">ชื่อ - นามสกุล</th>
                    <?php foreach ($assessmentItems as $mainItem): ?>
                    <th colspan="<?= count($mainItem['sub_items']) + 1 ?>">ข้อที่ <?= $mainItem['item_order'] ?></th>
                    <?php endforeach; ?>
                    <th rowspan="4" class="summary-col-final-print align-middle text-center">
                        <div class="vertical-header-cell">สรุปผลการประเมิน</div>
                    </th>
                </tr>
                                    <tr>
                                        <?php foreach ($assessmentItems as $mainItem): ?>
                                            <th class="main-item-name-header" colspan="<?= count($mainItem['sub_items']) + 1 ?>"><?= esc($mainItem['item_name']) ?></th>
                                        <?php endforeach; ?>
                                    </tr>                <tr>
                    <?php foreach ($assessmentItems as $mainItem): ?>
                    <th colspan="<?= count($mainItem['sub_items']) ?>">ตัวชี้วัด</th>
                    <th rowspan="2" class="summary-col-print">
                        <div class="vertical-header-cell">ผลการประเมิน</div>
                    </th>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <?php foreach ($assessmentItems as $mainItem): ?>
                    <?php foreach ($mainItem['sub_items'] as $subItem): ?>
                    <th class="sub-item-col"><?= $mainItem['item_order'] ?>.<?= $subItem['item_order'] ?></th>
                    <?php endforeach; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= esc($student['StudentNumber']) ?></td>
                    <td style="text-align: left; white-space: nowrap;">
                        <?= esc($student['StudentPrefix'] . $student['StudentFirstName'] . ' ' . $student['StudentLastName']) ?>
                    </td>

                    <?php foreach ($assessmentItems as $mainItem): ?>
                    <?php foreach ($mainItem['sub_items'] as $subItem): ?>
                    <?php $score = $evaluations[$student['StudentID']][$subItem['item_id']] ?? '-'; ?>
                    <td class="sub-item-col <?= ($score === '0' || $score === 0) ? 'text-danger-custom' : '' ?>">
                        <?= esc($score) ?>
                    </td>
                    <?php endforeach; ?>
                    <td class="summary-col-print" style="background-color: #f8f9fa; font-weight: bold;">
                        <?php $mainLevelNumeric = $studentResults[$student['StudentID']]['main_item_numeric_levels'][$mainItem['item_id']] ?? '-'; ?>
                        <div style="font-size: 0.8rem; text-align: center;" class="<?= ($mainLevelNumeric === '0' || $mainLevelNumeric === 0) ? 'text-danger-custom' : '' ?>">
                            <?= $mainLevelNumeric ?>
                        </div>
                    </td>
                    <?php endforeach; ?>

                    <td class="summary-col-final-print align-middle text-center" style="background-color: #fff3cd; font-weight: bold; vertical-align: middle;">
                        <?php $overallLevel = $studentResults[$student['StudentID']]['overall_level'] ?? '-'; ?>
                        <div style="font-size: 0.8rem; text-align: center; width: 100%;" class="horizontal-normal-text <?= ($overallLevel === 'ไม่ผ่าน') ? 'text-danger-custom' : '' ?>">
                            <?= $overallLevel ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-center mt-4">ไม่พบข้อมูลการประเมินรายบุคคล</p>
        <?php endif; ?>
    </div>

    <script>
        window.onload = function() {
            window.print();
            window.addEventListener('afterprint', function() {
                window.close();
            });
        }
    </script>
</body>

</html>