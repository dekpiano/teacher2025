<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <!-- Google Fonts: TH Sarabun & Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        @font-face {
            font-family: 'THSarabunPSK';
            src: local('TH Sarabun PSK'), local('THSarabunPSK'), local('TH Sarabun New'), local('THSarabunNew');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'THSarabunPSK';
            src: local('TH Sarabun PSK Bold'), local('THSarabunPSK-Bold'), local('TH Sarabun New Bold'), local('THSarabunNew-Bold');
            font-weight: bold;
            font-style: normal;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body {
            font-family: 'THSarabunPSK', 'TH Sarabun New', 'TH Sarabun', 'Sarabun', sans-serif;
            font-size: 16pt;
            line-height: 1.28;
            color: #000;
            background-color: #525659;
            margin: 0;
            padding: 20px 0;
        }
        .page {
            background: #ffffff;
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            padding: 12mm 18mm 10mm 18mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            position: relative;
            box-sizing: border-box;
        }
        .no-print-bar {
            width: 210mm;
            margin: 0 auto 12px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background-color: #1a73e8;
            color: white;
            border: none;
            padding: 9px 24px;
            font-size: 15px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            font-family: 'Sarabun', sans-serif;
        }
        .btn-print:hover {
            background-color: #1557b0;
        }
        .btn-back {
            background-color: #3c4043;
            color: white;
            text-decoration: none;
            padding: 9px 18px;
            font-size: 15px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Sarabun', sans-serif;
        }
        .btn-back:hover {
            background-color: #202124;
            color: white;
        }

        /* ─── Typography & Form Grid Exact Replication ─── */
        .header-docno {
            text-align: right;
            font-size: 14pt;
            font-weight: normal;
            margin-bottom: 2px;
        }
        .header-meta {
            text-align: right;
            font-size: 14pt;
            line-height: 1.2;
            margin-bottom: 8px;
        }
        .title {
            text-align: center;
            font-size: 17pt;
            font-weight: bold;
            margin-top: 2px;
            margin-bottom: 3px;
        }
        .header-write-date {
            text-align: right;
            font-size: 16pt;
            line-height: 1.35;
            padding-right: 15px;
        }
        .line-row {
            font-size: 16pt;
            line-height: 1.32;
            margin-bottom: 1px;
            white-space: nowrap;
        }
        .line-row-wrap {
            font-size: 16pt;
            line-height: 1.32;
            margin-bottom: 1px;
        }
        .indent-1 {
            padding-left: 70px;
        }
        .indent-la {
            padding-left: 45px;
        }
        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            margin-left: 2px;
            vertical-align: middle;
            text-align: center;
            line-height: 9px;
            font-size: 11pt;
            font-weight: bold;
        }
        .checkbox.checked:after {
            content: "✓";
            display: block;
            margin-top: -2px;
            font-family: sans-serif;
            font-size: 11pt;
        }

        .data-val {
            display: inline;
            font-weight: normal;
            color: #000;
        }

        /* ─── Signature Block (Applicant - Top Right) ─── */
        .signature-top-right {
            margin-top: 10px;
            margin-left: 45%;
            width: 55%;
            text-align: center;
            font-size: 16pt;
            line-height: 1.3;
        }

        /* ─── Bottom Section (2 Columns) ─── */
        .bottom-container {
            display: table;
            width: 100%;
            margin-top: 6px;
            border-collapse: collapse;
        }
        .col-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 12px;
        }
        .col-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 12px;
        }

        table.stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
            margin-bottom: 5px;
            font-size: 14pt;
        }
        table.stats-table th, table.stats-table td {
            border: 1px solid #000;
            padding: 1px 3px;
            text-align: center;
            line-height: 1.15;
            height: 22px;
        }
        table.stats-table th {
            font-weight: normal;
        }

        .sign-box {
            width: 100%;
            text-align: center;
            margin-top: 4px;
            font-size: 15.5pt;
            line-height: 1.25;
        }
        .sign-box .sign-title {
            text-align: left;
            margin-bottom: 2px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .page {
                box-shadow: none;
                margin: 0;
                width: 100%;
                height: 100%;
                padding: 10mm 15mm 8mm 15mm;
            }
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Controls Bar (Hidden in Print) -->
    <div class="no-print-bar">
        <a href="<?= base_url('leave') ?>" class="btn-back">
            <i class="bi bi-arrow-left"></i> ย้อนกลับ
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="bi bi-printer-fill"></i> สั่งพิมพ์เอกสาร / บันทึกเป็น PDF
        </button>
    </div>

    <!-- A4 Paper View (Exact Replica of บค.๐๐๒/๒๕๖๘) -->
    <div class="page">
        <!-- 1. Metadata Box (Top-Right) -->
        <div class="header-docno">บค.๐๐๒/๒๕๖๘</div>
        <div class="header-meta">
            <div>เลขรับเรื่อง...............................................</div>
            <div>วัน/เดือน/ปี.........................เวลา............</div>
        </div>

        <!-- 2. Form Title -->
        <div class="title">ใบลาป่วย  ลาคลอดบุตร  ลากิจส่วนตัว</div>

        <!-- 3. Written at & Date (Right-Aligned) -->
        <div class="header-write-date">
            <div>เขียนที่ <span class="data-val">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</span></div>
            <div>
                วันที่ <span class="data-val"><?= date('j', $createdDate) ?></span>
                &nbsp;&nbsp;เดือน <span class="data-val"><?= $thaiMonths[date('n', $createdDate)] ?></span>
                &nbsp;&nbsp;พ.ศ. <span class="data-val"><?= date('Y', $createdDate) + 543 ?></span>
            </div>
        </div>

        <!-- 4. Header Fields -->
        <div class="line-row" style="margin-top: 4px;">
            เรื่อง&nbsp;&nbsp;&nbsp;&nbsp;<span class="data-val"><?= esc($leave['leave_topic']) ?></span>
        </div>
        <div class="line-row">
            เรียน&nbsp;&nbsp;ผู้อำนวยการสถานศึกษา โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
        </div>
        <div class="line-row">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ข้าพเจ้า <span class="data-val"><?= esc($fullName) ?></span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ตำแหน่ง <span class="data-val"><?= esc($position) ?></span>
        </div>
        <div class="line-row">
            สังกัด <span class="data-val"><?= esc($groupName) ?> สังกัดองค์การบริหารส่วนจังหวัดนครสวรรค์</span>
        </div>

        <!-- 5. Leave Type Options (Exact Column Alignment) -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 3px; font-size: 16pt; line-height: 1.32;">
            <tr>
                <td style="width: 50px; padding: 0;"></td>
                <td style="width: 25px; padding: 0; vertical-align: middle;">
                    <span class="checkbox <?= ($leave['leave_type_name'] == 'ลาป่วย') ? 'checked' : '' ?>"></span>
                </td>
                <td style="width: 80px; padding: 0; vertical-align: middle;">ป่วย</td>
                <td style="width: 65px; padding: 0; vertical-align: middle;">เนื่องจาก</td>
                <td style="padding: 0; vertical-align: middle;">
                    <span class="data-val"><?= ($leave['leave_type_name'] == 'ลาป่วย') ? esc($leave['leave_detail'] ?: '-') : '..................................................................................................' ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 0; vertical-align: middle;">ขอลา</td>
                <td style="padding: 0; vertical-align: middle;">
                    <span class="checkbox <?= ($leave['leave_type_name'] == 'ลากิจส่วนตัว') ? 'checked' : '' ?>"></span>
                </td>
                <td style="padding: 0; vertical-align: middle;">กิจส่วนตัว</td>
                <td style="padding: 0; vertical-align: middle;">เนื่องจาก</td>
                <td style="padding: 0; vertical-align: middle;">
                    <span class="data-val"><?= ($leave['leave_type_name'] == 'ลากิจส่วนตัว') ? esc($leave['leave_detail'] ?: '-') : '..................................................................................................' ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 0;"></td>
                <td style="padding: 0; vertical-align: middle;">
                    <span class="checkbox <?= ($leave['leave_type_name'] == 'ลาคลอดบุตร') ? 'checked' : '' ?>"></span>
                </td>
                <td colspan="3" style="padding: 0; vertical-align: middle;">คลอดบุตร</td>
            </tr>
        </table>

        <!-- 6. Dates & Period -->
        <div class="line-row" style="margin-top: 3px;">
            ตั้งแต่วันที่ <span class="data-val"><?= date('j', $startDate) . ' ' . $thaiMonths[date('n', $startDate)] . ' ' . (date('Y', $startDate) + 543) ?></span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ถึงวันที่ <span class="data-val"><?= date('j', $endDate) . ' ' . $thaiMonths[date('n', $endDate)] . ' ' . (date('Y', $endDate) + 543) ?></span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;มีกำหนด <span class="data-val"><?= number_format($leave['leave_total_days'], 1) ?></span> วัน
        </div>

        <!-- 7. Last Leave Details -->
        <div class="line-row">
            ข้าพเจ้าได้ลา&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="checkbox <?= ($lastLeave && $lastLeave['leave_type_name'] == 'ลาป่วย') ? 'checked' : '' ?>"></span> ป่วย&nbsp;&nbsp;
            <span class="checkbox <?= ($lastLeave && $lastLeave['leave_type_name'] == 'ลากิจส่วนตัว') ? 'checked' : '' ?>"></span> กิจส่วนตัว&nbsp;&nbsp;
            <span class="checkbox <?= ($lastLeave && $lastLeave['leave_type_name'] == 'ลาคลอดบุตร') ? 'checked' : '' ?>"></span> คลอดบุตร&nbsp;&nbsp;
            ครั้งสุดท้ายตั้งแต่วันที่ <?= $lastLeave ? '<span class="data-val">' . date('j', strtotime($lastLeave['leave_start_date'])) . ' ' . $thaiMonths[date('n', strtotime($lastLeave['leave_start_date']))] . ' ' . (date('Y', strtotime($lastLeave['leave_start_date'])) + 543) . '</span>' : '....................................' ?>
        </div>
        <div class="line-row">
            ถึงวันที่ <?= $lastLeave ? '<span class="data-val">' . date('j', strtotime($lastLeave['leave_end_date'])) . ' ' . $thaiMonths[date('n', strtotime($lastLeave['leave_end_date']))] . ' ' . (date('Y', strtotime($lastLeave['leave_end_date'])) + 543) . '</span>' : '..................................................' ?>
            &nbsp;&nbsp;มีกำหนด <?= $lastLeave ? '<span class="data-val">' . number_format($lastLeave['leave_total_days'], 1) . '</span>' : '...........' ?> วัน
            &nbsp;&nbsp;ในระหว่างลาติดต่อข้าพเจ้าได้ที่ <?= $contactPhone ? '<span class="data-val">' . esc($contactPhone) . '</span>' : '..........................................' ?>
        </div>
        <div class="line-row-wrap">
            <?= $contactAddress ? '<span class="data-val">' . esc($contactAddress) . '</span>' : '...................................................................................................................................................................................................' ?>
        </div>

        <!-- 8. Top-Right Signature Block (ผู้ลา) -->
        <div class="signature-top-right">
            <div>ขอแสดงความนับถือ</div>
            <div style="margin-top: 5px;">(ลงชื่อ)...................................................................</div>
            <div>( <span class="data-val"><?= esc($fullName) ?></span> )</div>
            <div>ตำแหน่ง <span class="data-val"><?= esc($position) ?></span></div>
        </div>

        <!-- 9. Bottom Two-Column Section -->
        <div class="bottom-container">
            <!-- Left Column: Statistics & Inspector & Department Head -->
            <div class="col-left">
                <?php
                    // คำนวณช่วงปีงบประมาณอัตโนมัติจากวันเริ่มต้นขอลา (รองรับทั้ง 2 ระบบ)
                    $fYear = (int)date('Y', $startDate);
                    $fMonth = (int)date('n', $startDate);
                    if ($fMonth >= 10) {
                        $fYearBE = $fYear + 1 + 543;
                        $fStartTh = "1 ต.ค. " . ($fYear + 543);
                        $fEndTh = "30 ก.ย. " . ($fYear + 1 + 543);
                    } else {
                        $fYearBE = $fYear + 543;
                        $fStartTh = "1 ต.ค. " . ($fYear - 1 + 543);
                        $fEndTh = "30 ก.ย. " . ($fYear + 543);
                    }
                    if (isset($fiscalYearBE)) $fYearBE = $fiscalYearBE;
                    if (isset($fiscalStartTh)) $fStartTh = $fiscalStartTh;
                    if (isset($fiscalEndTh)) $fEndTh = $fiscalEndTh;
                ?>
                <div style="font-size: 14.5pt; font-weight: normal; margin-bottom: 2px;">
                    สถิติการลาในปีงบประมาณนี้ <span class="data-val">(<?= esc($fYearBE) ?>: <?= esc($fStartTh) ?> - <?= esc($fEndTh) ?>)</span>
                </div>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th style="width: 25%;">ประเภท<br>การลา</th>
                            <th style="width: 25%;">ลามาแล้ว<br>(วันทำการ)</th>
                            <th style="width: 25%;">ลาครั้งนี้<br>(วันทำการ)</th>
                            <th style="width: 25%;">รวมเป็น<br>(วันทำการ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $isSick = (strpos($leave['leave_type_name'], 'ป่วย') !== false);
                            $isPersonal = (strpos($leave['leave_type_name'], 'กิจ') !== false);
                            $isMaternity = (strpos($leave['leave_type_name'], 'คลอด') !== false);
                        ?>
                        <tr>
                            <td style="text-align: left; padding-left: 4px;">ป่วย</td>
                            <td><?= number_format($leaveStats['ลาป่วย']['used_before'], 1) ?></td>
                            <td><?= $isSick ? number_format($leave['leave_total_days'], 1) : '-' ?></td>
                            <td><?= number_format($leaveStats['ลาป่วย']['used_before'] + ($isSick ? $leave['leave_total_days'] : 0), 1) ?></td>
                        </tr>
                        <tr>
                            <td style="text-align: left; padding-left: 4px;">กิจส่วนตัว</td>
                            <td><?= number_format($leaveStats['ลากิจส่วนตัว']['used_before'], 1) ?></td>
                            <td><?= $isPersonal ? number_format($leave['leave_total_days'], 1) : '-' ?></td>
                            <td><?= number_format($leaveStats['ลากิจส่วนตัว']['used_before'] + ($isPersonal ? $leave['leave_total_days'] : 0), 1) ?></td>
                        </tr>
                        <tr>
                            <td style="text-align: left; padding-left: 4px;">คลอดบุตร</td>
                            <td><?= number_format($leaveStats['ลาคลอดบุตร']['used_before'], 1) ?></td>
                            <td><?= $isMaternity ? number_format($leave['leave_total_days'], 1) : '-' ?></td>
                            <td><?= number_format($leaveStats['ลาคลอดบุตร']['used_before'] + ($isMaternity ? $leave['leave_total_days'] : 0), 1) ?></td>
                        </tr>
                    </tbody>
                </table>

                <!-- 1. Inspector Sign (ผู้ตรวจสอบ) -->
                <div class="sign-box">
                    <div>(ลงชื่อ)...................................................................ผู้ตรวจสอบ</div>
                    <div>( <?= $approver ? '<span class="data-val">' . esc($approver['pers_prefix'] . $approver['pers_firstname'] . ' ' . $approver['pers_lastname']) . '</span>' : '...................................................................' ?> )</div>
                    <div>ตำแหน่ง <?= $approver ? '<span class="data-val">' . esc($approver['posi_name'] ?? 'เจ้าหน้าที่') . '</span>' : '...................................................................' ?></div>
                    <div>วันที่ <?= (!empty($leave['approved_at'])) ? '<span class="data-val">' . date('j', strtotime($leave['approved_at'])) . ' ' . $thaiMonths[date('n', strtotime($leave['approved_at']))] . ' ' . (date('Y', strtotime($leave['approved_at'])) + 543) . '</span>' : '............/......................../...................' ?></div>
                </div>

                <!-- 2. Department Head Sign (หัวหน้ากลุ่มสาระฯ) -->
                <div class="sign-box" style="margin-top: 6px;">
                    <div class="sign-title">ความเห็นของหัวหน้ากลุ่มสาระฯ/หัวหน้างานฝ่าย</div>
                    <div>.......................................................................................................</div>
                    <div style="margin-top: 3px;">(ลงชื่อ)...................................................................</div>
                    <div>(...................................................................)</div>
                    <div>ตำแหน่ง...................................................................</div>
                    <div>วันที่............/......................../...................</div>
                </div>
            </div>

            <!-- Right Column: Commander Opinion & Director Order -->
            <div class="col-right">
                <!-- 3. Commander Opinion (ความเห็นผู้บังคับบัญชา - รองผู้อำนวยการฝ่ายบริหารงานบุคคล) -->
                <div class="sign-box">
                    <div class="sign-title">ความเห็นผู้บังคับบัญชา</div>
                    <div>.......................................................................................................</div>
                    <div>.......................................................................................................</div>
                    <div style="margin-top: 3px;">(ลงชื่อ)...................................................................</div>
                    <div>( <?= $deputyDirector ? '<span class="data-val">' . esc($deputyDirector['pers_prefix'] . $deputyDirector['pers_firstname'] . ' ' . $deputyDirector['pers_lastname']) . '</span>' : '...................................................................' ?> )</div>
                    <div>ตำแหน่ง <?= $deputyDirector ? '<span class="data-val">' . esc($deputyDirector['role_position'] ?? 'รองผู้อำนวยการสถานศึกษา') . '</span>' : 'รองผู้อำนวยการสถานศึกษา' ?></div>
                    <div>วันที่ <?= (!empty($leave['approved_at'])) ? '<span class="data-val">' . date('j', strtotime($leave['approved_at'])) . ' ' . $thaiMonths[date('n', strtotime($leave['approved_at']))] . ' ' . (date('Y', strtotime($leave['approved_at'])) + 543) . '</span>' : '............/......................../...................' ?></div>
                </div>

                <!-- 4. Director Order (คำสั่งผู้อำนวยการสถานศึกษา) -->
                <div class="sign-box" style="margin-top: 6px;">
                    <div class="sign-title">คำสั่ง</div>
                    <div style="margin-top: 2px; margin-bottom: 2px;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="checkbox"></span> อนุญาต
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="checkbox"></span> ไม่อนุญาต
                    </div>
                    <div>.......................................................................................................</div>
                    <div>.......................................................................................................</div>
                    <div style="margin-top: 3px;">(ลงชื่อ)...................................................................</div>
                    <div>( <?= $director ? '<span class="data-val">' . esc($director['pers_prefix'] . $director['pers_firstname'] . ' ' . $director['pers_lastname']) . '</span>' : '...................................................................' ?> )</div>
                    <div>ตำแหน่ง <span class="data-val">ผู้อำนวยการสถานศึกษา</span></div>
                    <div><span class="data-val">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</span></div>
                    <div>วันที่ ............/......................../...................</div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

