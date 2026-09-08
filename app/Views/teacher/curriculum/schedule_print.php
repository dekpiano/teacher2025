<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'ข้อมูลการจัดตารางสอนรายบุคคล') ?> - <?= esc($teacher->pers_prefix . $teacher->pers_firstname . ' ' . $teacher->pers_lastname) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 12mm 8mm 12mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: 'Sarabun', 'TH Sarabun New', sans-serif;
            font-size: 13.5px;
            line-height: 1.3;
            color: #000;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        .no-print-bar {
            max-width: 820px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .btn-print {
            background-color: #0d6efd;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            text-decoration: none;
        }
        .btn-print:hover {
            background-color: #0b5ed7;
            color: #fff;
        }

        .btn-close-win {
            background-color: #6c757d;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-close-win:hover {
            background-color: #5c636a;
        }

        .sheet-container {
            background: #ffffff;
            max-width: 820px;
            margin: 0 auto;
            padding: 20px 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.12);
            border-radius: 2px;
        }

        /* Document Header */
        .doc-header {
            text-align: center;
            margin-bottom: 10px;
        }

        .doc-title {
            font-size: 17px;
            font-weight: 700;
            margin: 0 0 3px 0;
        }

        .school-name {
            font-size: 15.5px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }

        .meta-line {
            font-size: 13.5px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 3px;
        }

        /* Tables */
        .table-section-title {
            font-weight: 700;
            font-size: 13.5px;
            margin: 8px 0 3px 0;
        }

        table.schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 12.5px;
        }

        table.schedule-table th, 
        table.schedule-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: middle;
        }

        table.schedule-table th {
            background-color: #f1f3f5;
            font-weight: 700;
            text-align: center;
        }

        .subject-name-cell {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.15;
        }

        .text-center { text-align: center !important; }
        .text-start  { text-align: left !important; }
        .text-end    { text-align: right !important; }
        .fw-bold     { font-weight: 700 !important; }

        /* Grand Total Box */
        .grand-total-box {
            border: 1px solid #000;
            padding: 4px 10px;
            margin: 6px 0 8px 0;
            font-size: 13.5px;
            font-weight: 700;
            text-align: right;
            background-color: #f8f9fa;
        }

        /* Special Duties Section */
        .duty-section {
            margin: 6px 0 10px 0;
            page-break-inside: avoid;
        }

        .duty-title {
            font-weight: 700;
            font-size: 13.5px;
            margin-bottom: 2px;
        }

        .duty-list {
            margin: 0;
            padding-left: 22px;
            font-size: 13px;
        }

        .duty-list li {
            margin-bottom: 2px;
        }

        .duty-blank-line {
            margin-bottom: 2px;
            color: #333;
        }

        /* Signatures Vertical (เรียงเป็นบรรทัดใคร บรรทัดมัน) */
        .signatures-vertical-container {
            margin-top: 14px;
            margin-left: auto;
            width: 440px;
            max-width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
            page-break-inside: avoid;
            font-size: 13px;
        }

        .sig-row {
            display: flex;
            align-items: flex-end;
            white-space: nowrap;
        }

        .sig-lead {
            font-weight: normal;
            white-space: nowrap;
        }

        .sig-dots {
            flex: 1;
            border-bottom: 1px dotted #000;
            margin: 0 8px 3px 4px;
            height: 1px;
        }

        .sig-role {
            width: 165px;
            text-align: left;
            white-space: nowrap;
            font-weight: normal;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }

            .no-print-bar {
                display: none !important;
            }

            .sheet-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
                border-radius: 0;
            }

            table.schedule-table th {
                background-color: #f5f5f5 !important;
            }

            .grand-total-box {
                background-color: transparent !important;
            }

            table.schedule-table,
            .duty-section,
            .signatures-vertical-container {
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar (hidden when printing) -->
    <div class="no-print-bar">
        <div>
            <span class="fw-bold fs-5">พิมพ์ตารางสอนรายบุคคล</span>
            <span class="text-muted ms-2">ภาคเรียนที่ <?= esc($term) ?>/<?= esc($year) ?></span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn-print">
                <i class="bi bi-printer-fill"></i> สั่งพิมพ์เอกสาร
            </button>
            <button onclick="window.close()" class="btn-close-win">
                ปิดหน้าต่าง
            </button>
        </div>
    </div>

    <!-- Document Sheet (A4) -->
    <div class="sheet-container">
        
        <!-- Header -->
        <div class="doc-header">
            <h1 class="doc-title">ข้อมูลการจัดตารางสอนรายบุคคล</h1>
            <div class="school-name"><?= esc(!empty($school->SchoolName) ? $school->SchoolName : 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?></div>
            <div class="meta-line">
                <span><strong>ชื่อ</strong> <?= esc($teacher->pers_prefix . $teacher->pers_firstname . ' ' . $teacher->pers_lastname) ?></span>
                <span><strong>กลุ่มสาระการเรียนรู้</strong> <?= esc($learning_name) ?></span>
            </div>
            <div class="meta-line" style="margin-top: 2px;">
                <span><strong>ภาคเรียนที่</strong> <?= esc($term) ?> <strong>ปีการศึกษา</strong> <?= esc($year) ?></span>
            </div>
        </div>

        <!-- Table 1: รายวิชาที่สอน -->
        <div class="table-section-title">รายวิชาที่สอน</div>
        <table class="schedule-table">
            <thead>
                <tr>
                    <th style="width: 4%;">ที่</th>
                    <th style="width: 11%;">รหัสวิชา</th>
                    <th style="width: 25%;">รายวิชา</th>
                    <th style="width: 7%;">พื้นฐาน</th>
                    <th style="width: 7%;">เพิ่มเติม</th>
                    <th style="width: 7%;">หน่วยกิต</th>
                    <th style="width: 9%;">ชั่วโมง/<br>สัปดาห์</th>
                    <th style="width: 7%;">ระดับ<br>ชั้น</th>
                    <th style="width: 9%;">ห้อง</th>
                    <th style="width: 8%;">รวม<br>ชั่วโมง</th>
                    <th style="width: 6%;">หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($grouped_subjects)): ?>
                    <?php 
                        $i = 1; 
                        foreach ($grouped_subjects as $sub): 
                            $isAdditional = mb_strpos($sub['subject_type'] ?? '', 'เพิ่มเติม') !== false;
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>
                            <td class="text-center fw-bold"><?= esc($sub['subject_code']) ?></td>
                            <?php 
                                $subNameLen = mb_strlen($sub['subject_name'] ?? '');
                                $fontSize = '12px';
                                if ($subNameLen > 35) {
                                    $fontSize = '10px';
                                } elseif ($subNameLen > 25) {
                                    $fontSize = '11px';
                                }
                            ?>
                            <td class="text-start subject-name-cell" style="font-size: <?= $fontSize ?>;" title="<?= esc($sub['subject_name']) ?>">
                                <?= esc($sub['subject_name']) ?>
                            </td>
                            <td class="text-center"><?= !$isAdditional ? '/' : '' ?></td>
                            <td class="text-center"><?= $isAdditional ? '/' : '' ?></td>
                            <td class="text-center"><?= number_format($sub['credit'], 1) ?></td>
                            <td class="text-center"><?= $sub['hours_per_week'] ?></td>
                            <td class="text-center"><?= esc($sub['grade_level']) ?></td>
                            <td class="text-center"><?= esc($sub['room_text']) ?></td>
                            <td class="text-center fw-bold"><?= $sub['total_weekly_hours'] ?></td>
                            <td class="text-center" style="font-size: 11.5px;"><?= esc($sub['final_remark']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" class="text-center" style="padding: 15px; color: #888;">- ไม่พบข้อมูลรายวิชาที่สอน -</td>
                    </tr>
                <?php endif; ?>

                <!-- Table 1 Summary Row -->
                <tr class="fw-bold" style="background-color: #fafafa;">
                    <td colspan="3" class="text-center">รวม ( <?= count($grouped_subjects) ?> รายวิชา )</td>
                    <td class="text-center"><?= $basic_subject_count > 0 ? $basic_subject_count . ' วิชา' : '-' ?></td>
                    <td class="text-center"><?= $additional_subject_count > 0 ? $additional_subject_count . ' วิชา' : '-' ?></td>
                    <td class="text-center"><?= number_format($total_credit, 1) ?></td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center" style="font-size: 14px;"><?= $total_subject_weekly_hours ?></td>
                    <td class="text-center">-</td>
                </tr>
            </tbody>
        </table>

        <!-- Table 2: กิจกรรม / อื่น ๆ -->
        <div class="table-section-title">กิจกรรม / อื่น ๆ</div>
        <table class="schedule-table">
            <thead>
                <tr>
                    <th style="width: 4%;">ที่</th>
                    <th style="width: 48%;">กิจกรรม</th>
                    <th style="width: 10%;">ชั้น</th>
                    <th style="width: 12%;">ห้อง</th>
                    <th style="width: 12%;">รวมชั่วโมง</th>
                    <th style="width: 14%;">หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($activities)): ?>
                    <?php $k = 1; foreach ($activities as $act): ?>
                        <tr>
                            <td class="text-center"><?= $k++ ?></td>
                            <td class="text-start fw-bold"><?= esc($act['activity_name']) ?></td>
                            <td class="text-center"><?= esc($act['grade_level'] ?: '-') ?></td>
                            <td class="text-center"><?= esc($act['room'] ?: '-') ?></td>
                            <td class="text-center fw-bold"><?= esc($act['hours_per_week']) ?></td>
                            <td class="text-center"><?= esc($act['remark'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 10px; color: #888;">- ไม่พบข้อมูลกิจกรรม -</td>
                    </tr>
                <?php endif; ?>

                <!-- Table 2 Summary Row -->
                <tr class="fw-bold" style="background-color: #fafafa;">
                    <td colspan="4" class="text-center">รวม ( <?= count($activities) ?> กิจกรรม )</td>
                    <td class="text-center" style="font-size: 14px;"><?= $total_activity_weekly_hours ?></td>
                    <td class="text-center">-</td>
                </tr>
            </tbody>
        </table>

        <!-- Grand Total Box -->
        <div class="grand-total-box">
            รวมชั่วโมงทั้งสิ้น &nbsp;&nbsp;<span style="font-size: 16px; text-decoration: underline;"><?= $grand_total_weekly_hours ?></span>&nbsp;&nbsp; ชั่วโมง / สัปดาห์
        </div>

        <!-- Special Duties Section -->
        <div class="duty-section">
            <div class="duty-title">หน้าที่พิเศษ</div>
            <?php if (!empty($duties)): ?>
                <ol class="duty-list">
                    <?php foreach ($duties as $dt): ?>
                        <?php 
                            $cleanDutyName = preg_replace('/^\d+[\.\)]\s*/u', '', trim($dt['duty_name']));
                        ?>
                        <li><?= esc($cleanDutyName) ?></li>
                    <?php endforeach; ?>
                </ol>
            <?php else: ?>
                <div class="duty-blank-line">1. ..........................................................................................................................................................</div>
                <div class="duty-blank-line">2. ..........................................................................................................................................................</div>
            <?php endif; ?>
        </div>

        <!-- Signatures (เรียงเป็นบรรทัดใคร บรรทัดมัน ตามแบบฟอร์ม) -->
        <div class="signatures-vertical-container">
            <div class="sig-row">
                <span class="sig-lead">ลงชื่อ</span>
                <span class="sig-dots"></span>
                <span class="sig-role">ครูผู้สอน</span>
            </div>
            <div class="sig-row">
                <span class="sig-lead">ลงชื่อ</span>
                <span class="sig-dots"></span>
                <span class="sig-role">หัวหน้ากลุ่มสาระการเรียนรู้</span>
            </div>
            <div class="sig-row">
                <span class="sig-lead">ลงชื่อ</span>
                <span class="sig-dots"></span>
                <span class="sig-role">รองผู้อำนวยการฝ่ายวิชาการ</span>
            </div>
            <div class="sig-row">
                <span class="sig-lead">ลงชื่อ</span>
                <span class="sig-dots"></span>
                <span class="sig-role">ผู้อำนวยการสถานศึกษา</span>
            </div>
        </div>

    </div>

</body>
</html>
