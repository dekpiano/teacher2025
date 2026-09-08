<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'ข้อมูลการจัดตารางสอนกลุ่มสาระการเรียนรู้') ?> ภาคเรียนที่ <?= esc($term) ?> ปีการศึกษา <?= esc($year) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 10mm 12mm;
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
            max-width: 960px;
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
            max-width: 960px;
            margin: 0 auto;
            padding: 24px 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.12);
            border-radius: 2px;
        }

        /* Document Header */
        .doc-header {
            text-align: center;
            margin-bottom: 14px;
        }

        .doc-title {
            font-size: 17px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }

        .school-name {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 2px 0;
        }

        .dept-name {
            font-size: 14px;
            color: #333;
            margin: 0;
        }

        /* Schedule Table */
        table.summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 12.5px;
        }

        table.summary-table th, 
        table.summary-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: middle;
        }

        table.summary-table th {
            background-color: #f8f9fa;
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

        /* Signatures Container */
        .signatures-vertical-container {
            margin-top: 25px;
            margin-left: auto;
            width: 460px;
            max-width: 100%;
            display: flex;
            flex-direction: column;
            gap: 16px;
            page-break-inside: avoid;
            font-size: 13.5px;
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
            margin: 0 8px 4px 4px;
            height: 1px;
        }

        .sig-role {
            width: 180px;
            text-align: left;
            white-space: nowrap;
            font-weight: normal;
        }

        /* Print Styles */
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

            table.summary-table th {
                background-color: #f5f5f5 !important;
            }

            .signatures-vertical-container {
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar -->
    <div class="no-print-bar">
        <div>
            <span class="fw-bold fs-5">พิมพ์ข้อมูลการจัดตารางสอนรวม</span>
            <span class="text-muted ms-2"><?= esc($learning_name) ?> ภาคเรียนที่ <?= esc($term) ?>/<?= esc($year) ?></span>
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

    <!-- Printable Sheet -->
    <div class="sheet-container">
        
        <!-- Header -->
        <div class="doc-header">
            <h1 class="doc-title">ข้อมูลการจัดตารางสอนกลุ่มสาระการเรียนรู้ <?= esc($learning_name) ?></h1>
            <div class="doc-subtitle fw-bold" style="font-size: 16px; margin-bottom: 3px;">ภาคเรียนที่ <?= esc($term) ?> ปีการศึกษา <?= esc($year) ?></div>
            <div class="school-name"><?= esc(!empty($school->SchoolName) ? $school->SchoolName : 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?></div>
        </div>

        <!-- Main Summary Table -->
        <table class="summary-table">
            <thead>
                <tr>
                    <th style="width: 4%;">ที่</th>
                    <th style="width: 18%;">ครูผู้สอน</th>
                    <th style="width: 10%;">รหัสวิชา</th>
                    <th style="width: 22%;">รายวิชา</th>
                    <th style="width: 6%;">พื้นฐาน</th>
                    <th style="width: 6%;">เพิ่มเติม</th>
                    <th style="width: 6%;">หน่วยกิต</th>
                    <th style="width: 7%;">ชั่วโมง/<br>สัปดาห์</th>
                    <th style="width: 6%;">ชั้น</th>
                    <th style="width: 7%;">ห้อง</th>
                    <th style="width: 6%;">รวม</th>
                    <th style="width: 8%;">หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($groupedSchedules)): ?>
                    <tr>
                        <td colspan="12" class="text-center" style="padding: 20px; color: #888;">
                            ไม่พบข้อมูลตารางสอนในภาคเรียนนี้
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                        $subjectIndex = 1;
                    ?>
                    <?php foreach ($groupedSchedules as $teacherId => $teacherData): ?>
                        <?php 
                            $subjects = $teacherData['subjects'] ?? [];
                            $subjectCount = count($subjects);
                            if ($subjectCount === 0) continue;
                            $first = true;
                        ?>
                        <?php foreach ($subjects as $row): ?>
                            <?php 
                                $isBasic = (mb_strpos($row['subject_type'] ?? '', 'เพิ่มเติม') === false);
                                $isAdd   = !$isBasic;
                                $displayRoom = !empty($row['room_range_text']) ? $row['room_range_text'] : (!empty($row['rooms']) ? implode(', ', $row['rooms']) : '-');
                            ?>
                            <tr>
                                <!-- ที่ -->
                                <td class="text-center"><?= $subjectIndex++ ?></td>

                                <!-- ครูผู้สอน (rowspan) -->
                                <?php if ($first): ?>
                                    <td rowspan="<?= $subjectCount ?>" style="vertical-align: top; padding-top: 6px;">
                                        <strong><?= esc($teacherData['teacher_name']) ?></strong>
                                    </td>
                                <?php endif; ?>

                                <!-- รหัสวิชา -->
                                <td class="text-center"><?= esc($row['subject_code']) ?></td>

                                <!-- รายวิชา (แสดงบรรทัดเดียว + ลดขนาดตัวอักษรหากข้อความยาว) -->
                                <?php 
                                    $subNameLen = mb_strlen($row['subject_name'] ?? '');
                                    $fontSize = '12px';
                                    if ($subNameLen > 35) {
                                        $fontSize = '10px';
                                    } elseif ($subNameLen > 25) {
                                        $fontSize = '11px';
                                    }
                                ?>
                                <td class="text-start subject-name-cell" style="font-size: <?= $fontSize ?>;" title="<?= esc($row['subject_name']) ?>">
                                    <?= esc($row['subject_name']) ?>
                                </td>

                                <!-- พื้นฐาน -->
                                <td class="text-center"><?= $isBasic ? '/' : '' ?></td>

                                <!-- เพิ่มเติม -->
                                <td class="text-center"><?= $isAdd ? '/' : '' ?></td>

                                <!-- หน่วยกิต -->
                                <td class="text-center"><?= !empty($row['credit']) ? esc($row['credit']) : '-' ?></td>

                                <!-- ชั่วโมง/สัปดาห์ -->
                                <td class="text-center"><?= esc($row['hours_per_week']) ?></td>

                                <!-- ชั้น -->
                                <td class="text-center"><?= esc($row['grade_level']) ?></td>

                                <!-- ห้อง -->
                                <td class="text-center"><?= esc($displayRoom) ?></td>

                                <!-- รวม -->
                                <td class="text-center fw-bold"><?= esc($row['total_weekly_hours']) ?></td>

                                <!-- หมายเหตุ -->
                                <td class="text-center">
                                    <?= !empty($row['remarks']) ? esc(implode(', ', $row['remarks'])) : '' ?>
                                </td>
                            </tr>
                            <?php $first = false; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Summary Row -->
                <tr class="fw-bold" style="background-color: #f8f9fa;">
                    <td colspan="10" class="text-end" style="padding-right: 15px;">รวมจำนวนคาบสอน</td>
                    <td class="text-center" style="font-size: 14px;"><?= $grandTotalWeeklyHours ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <!-- Signatures (เรียง 3 ลายเซ็นตามแบบฟอร์มเอกสารของโรงเรียน) -->
        <div class="signatures-vertical-container">
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
