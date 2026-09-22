<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $isScout = !empty($isScout) || \App\Models\ClubModel::isScoutClub($club->club_name);
    $activityTypeLabel = $isScout ? 'กิจกรรมลูกเสือ - เนตรนารี' : 'กิจกรรมชุมนุม';
    $evaluatorRoleLabel = $isScout ? 'ผู้กำกับลูกเสือ' : 'ครูที่ปรึกษา/ผู้ดูแลกิจกรรม';
    $headRoleLabel = $isScout ? 'หัวหน้าผู้กำกับลูกเสือ' : 'หัวหน้ากิจกรรม';

    $cleanName = $club->club_name;
    if ($isScout) {
        $formattedClubName = (mb_strpos($cleanName, 'ลูกเสือ') !== false || mb_strpos($cleanName, 'เนตรนารี') !== false)
            ? $cleanName
            : 'ลูกเสือ - เนตรนารี ' . $cleanName;
    } else {
        $formattedClubName = (mb_strpos($cleanName, 'ชุมนุม') !== false)
            ? $cleanName
            : 'ชุมนุม ' . $cleanName;
    }

    // Handle multiple advisors/supervisors
    $advisorNamesList = !empty($advisorNames) && is_array($advisorNames)
        ? $advisorNames
        : (!empty($evaluatorName) ? explode(', ', $evaluatorName) : ['...........................................']);
    ?>
    <title><?= $isScout ? 'แบบบันทึกผลการจัดกิจกรรมลูกเสือ - เนตรนารี' : 'แบบบันทึกผลการจัดกิจกรรมชุมนุม' ?> -
        <?= esc($formattedClubName) ?>
    </title>

    <!-- Bootstrap 5 & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts & TH Sarabun New Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,700&display=swap"
        rel="stylesheet">

    <style>
        /* TH Sarabun New / PSK Webfont (fallback จาก CDN สำหรับทุกระบบปฏิบัติการ Windows, macOS, iOS, Android) */
        @font-face {
            font-family: 'THSarabunPSK';
            src: local('TH Sarabun New'), local('THSarabunNew'), local('TH Sarabun PSK'), local('THSarabunPSK'),
                url('https://cdn.jsdelivr.net/gh/lazywasabi/thai-web-fonts@v1.0.0/fonts/THSarabunNew/THSarabunNew.woff2') format('woff2'),
                url('https://cdn.jsdelivr.net/gh/lazywasabi/thai-web-fonts@v1.0.0/fonts/THSarabunNew/THSarabunNew.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'THSarabunPSK';
            src: local('TH Sarabun New Bold'), local('THSarabunNew-Bold'), local('TH Sarabun PSK Bold'), local('THSarabunPSK-Bold'),
                url('https://cdn.jsdelivr.net/gh/lazywasabi/thai-web-fonts@v1.0.0/fonts/THSarabunNew/THSarabunNew-Bold.woff2') format('woff2'),
                url('https://cdn.jsdelivr.net/gh/lazywasabi/thai-web-fonts@v1.0.0/fonts/THSarabunNew/THSarabunNew-Bold.ttf') format('truetype');
            font-weight: bold;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'TH Sarabun New';
            src: local('TH Sarabun New'), local('THSarabunNew'), local('TH Sarabun PSK'), local('THSarabunPSK'),
                url('https://cdn.jsdelivr.net/gh/lazywasabi/thai-web-fonts@v1.0.0/fonts/THSarabunNew/THSarabunNew.woff2') format('woff2'),
                url('https://cdn.jsdelivr.net/gh/lazywasabi/thai-web-fonts@v1.0.0/fonts/THSarabunNew/THSarabunNew.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'TH Sarabun New';
            src: local('TH Sarabun New Bold'), local('THSarabunNew-Bold'), local('TH Sarabun PSK Bold'), local('THSarabunPSK-Bold'),
                url('https://cdn.jsdelivr.net/gh/lazywasabi/thai-web-fonts@v1.0.0/fonts/THSarabunNew/THSarabunNew-Bold.woff2') format('woff2'),
                url('https://cdn.jsdelivr.net/gh/lazywasabi/thai-web-fonts@v1.0.0/fonts/THSarabunNew/THSarabunNew-Bold.ttf') format('truetype');
            font-weight: bold;
            font-style: normal;
            font-display: swap;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'THSarabunPSK', 'TH Sarabun New', 'Sarabun', sans-serif;
            font-size: 16pt;
            line-height: 1.25;
            color: #000;
            background-color: #525659;
            margin: 0;
            padding: 0;
        }

        /* Top Action Bar for Screen View */
        .no-print-bar {
            background: #2b2d30;
            color: #fff;
            padding: 8px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 9999;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .btn-print-action {
            background-color: #15a362;
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 5px;
            font-size: 12pt;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-print-action:hover {
            opacity: 0.9;
            color: #fff;
        }

        .btn-print-action.btn-portrait {
            background-color: #0284c7;
        }

        .btn-print-action.btn-landscape {
            background-color: #ea580c;
        }

        .btn-close-action {
            background-color: #4b5563;
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 5px;
            font-size: 12pt;
            cursor: pointer;
            margin-left: 8px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-close-action:hover {
            background-color: #374151;
            color: #fff;
        }

        /* Print filter modes (for iOS/macOS Safari & specific page prints) */
        body.mode-portrait-only .landscape-page {
            display: none !important;
        }

        body.mode-landscape-only .portrait-page {
            display: none !important;
        }

        /* Page Layout & Sheet Simulation */
        .page-wrapper {
            padding: 20px 0;
        }

        .printable-page {
            background: #ffffff;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.35);
            margin: 0 auto 25px auto;
            position: relative;
            box-sizing: border-box;
        }

        /* Portrait A4 - ขอบกระดาษ 4 ทิศบาลานซ์สมดุล (บน 12mm, ล่าง 12mm, ซ้าย 14mm, ขวา 14mm) */
        .portrait-page {
            width: 210mm;
            height: 297mm;
            max-height: 297mm;
            padding: 12mm 14mm 12mm 14mm;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        /* Landscape A4 (Page 3) - ขอบกระดาษแนวนอน 4 ทิศสมดุล (บน 10mm, ล่าง 10mm, ซ้าย 12mm, ขวา 12mm) */
        .landscape-page {
            width: 297mm;
            height: 210mm;
            max-height: 210mm;
            padding: 10mm 12mm 10mm 12mm;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        /* หน้า 1 (ปกและอนุมัติ): กระชับและจัดรูปเล่มให้สมดุลชิดเป็นสัดส่วน */
        .page-cover {
            justify-content: flex-start !important;
            padding: 10mm 14mm !important;
            gap: 6px;
        }

        /* Official Document Typography */
        .doc-header-title {
            font-size: 18pt;
            font-weight: bold;
            line-height: 1.15;
            margin-bottom: 1px;
        }

        .doc-header-subtitle {
            font-size: 16pt;
            font-weight: bold;
            line-height: 1.15;
            margin-bottom: 1px;
        }

        .doc-header-school {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 1px;
        }

        .doc-header-suborg {
            font-size: 16pt;
            color: #111;
            margin-bottom: 2px;
        }

        .school-logo {
            width: 60px;
            height: auto;
            margin-bottom: 2px;
        }

        /* Official Tables */
        .table-official {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .table-official th,
        .table-official td {
            border: 1px solid #000 !important;
            padding: 4px 6px;
            vertical-align: middle;
            color: #000;
        }

        .table-official th {
            font-weight: bold;
            text-align: center;
            background-color: #f2f2f2 !important;
        }

        /* Cover Info Table */
        .cover-info-table {
            width: 100%;
            margin: 4px auto;
            border-collapse: collapse;
        }

        .cover-info-table td {
            border: 1px solid #000;
            padding: 3px 6px;
            font-size: 14pt;
            vertical-align: middle;
        }

        .cover-info-label {
            background-color: #f7f7f7;
            font-weight: bold;
            width: 32%;
            text-align: right;
            white-space: nowrap;
        }

        .cover-info-value {
            text-align: left;
            padding-left: 10px !important;
        }

        /* Official Signatures & Approval */
        .approval-container {
            width: 100%;
            margin: 0 auto;
        }

        .approval-box {
            border: 1px solid #000;
            padding: 5px 10px;
            text-align: left;
            margin-bottom: 4px;
            background-color: #fff;
            font-size: 14pt;
            line-height: 1.15;
        }

        .approval-box:last-child {
            margin-bottom: 0;
        }

        .signature-line-group {
            margin-bottom: 3px;
            line-height: 1.15;
        }

        .signature-line-group:last-child {
            margin-bottom: 0;
        }

        .sig-dots {
            display: inline-block;
            width: 160px;
            border-bottom: 1px dotted #000;
            margin: 0 4px;
        }

        .sig-name {
            padding-left: 30px;
            margin-top: 1px;
            color: #000;
        }

        /* Page 3: Attendance Grid - ใช้ขนาดเล็กเพราะคอลัมน์เยอะ ป้องกันล้นกระดาษ */
        .landscape-table {
            font-size: 10pt !important;
            line-height: 1.15;
            width: 100%;
        }

        .landscape-table th,
        .landscape-table td {
            padding: 1px 1px !important;
            border: 1px solid #000 !important;
            text-align: center;
        }

        .landscape-table th {
            font-size: 9pt;
            background-color: #f0f0f0 !important;
        }

        .landscape-table .student-num {
            font-size: 11pt;
        }

        .landscape-table .student-class {
            font-size: 11pt;
        }

        .landscape-table .student-name {
            text-align: left;
            padding-left: 6px !important;
            padding-right: 4px !important;
            white-space: nowrap;
            font-size: 12pt !important;
            color: #000;
        }

        .landscape-table .attendance-mark {
            font-size: 10pt;
        }

        .landscape-table .attendance-result {
            font-size: 11pt;
        }

        /* Print Specific Rules - บังคับ 1 แผ่นต่อ 1 ช่วงอย่างเด็ดขาด */
        @media print {

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                font-size: 16pt;
                width: 100% !important;
                height: 100% !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .page-wrapper {
                padding: 0 !important;
                margin: 0 !important;
            }

            .printable-page {
                box-shadow: none !important;
                margin: 0 !important;
                page-break-after: always !important;
                break-after: page !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
            }

            .printable-page:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }

            .portrait-page {
                width: 210mm !important;
                height: 296mm !important;
                max-height: 296mm !important;
                padding: 12mm 14mm 12mm 14mm !important;
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
            }

            .page-cover {
                justify-content: flex-start !important;
                padding: 10mm 14mm !important;
                gap: 6px !important;
            }

            .landscape-page {
                page: landscape-section;
                width: 297mm !important;
                height: 208mm !important;
                max-height: 208mm !important;
                padding: 10mm 12mm 10mm 12mm !important;
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
            }

            .table-official th,
            .table-official td {
                padding: 2px 4px !important;
            }
        }
    </style>
    <!-- Dynamic @page rule for mixed or isolated orientation printing -->
    <style id="dynamic-page-rule">
        @page {
            size: A4 portrait;
            margin: 0 !important;
        }

        @page landscape-section {
            size: A4 landscape;
            margin: 0 !important;
        }
    </style>
</head>

<body>

    <!-- Print / Action Toolbar (Hidden in Print) -->
    <div class="no-print-bar">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <button onclick="printSpecificMode('all')" class="btn-print-action"
                title="สั่งพิมพ์เอกสารทั้งหมด (สำหรับ Windows / Google Chrome)">
                <i class="bi bi-printer-fill"></i> สั่งพิมพ์ทั้งหมด
            </button>
            <button onclick="printSpecificMode('portrait')" class="btn-print-action btn-portrait"
                title="พิมพ์เฉพาะหน้าแนวตั้ง (หน้า 1, 2, 4, 5) แนะนำสำหรับ Mac/iOS Safari">
                <i class="bi bi-file-earmark-text"></i> พิมพ์รายงาน (แนวตั้ง) (สำหรับ Mac OS)
            </button>
            <button onclick="printSpecificMode('landscape')" class="btn-print-action btn-landscape"
                title="พิมพ์เฉพาะหน้าตารางบันทึกเวลาเรียน (หน้า 3) เป็นแนวนอน">
                <i class="bi bi-table"></i> พิมพ์เวลาเรียน (แนวนอน) (สำหรับ Mac OS)
            </button>
            <button onclick="window.close()" class="btn-close-action">
                <i class="bi bi-x-circle"></i> ปิดหน้าต่าง
            </button>
        </div>
        <div style="font-size: 12pt; color: #d1d5db;" class="d-none d-lg-block text-end">
            <i class="bi bi-apple me-1"></i> <span class="text-warning">ผู้ใช้ Mac / iPad / iOS:</span>
            หากพิมพ์รวมแล้วตารางเวลาเรียนถูกตัดขอบ ให้กดปุ่ม <strong>"พิมพ์รายงาน (แนวตั้ง)"</strong> และ
            <strong>"พิมพ์เวลาเรียน (แนวนอน)"</strong> แยกกันครับ
        </div>
    </div>

    <div class="page-wrapper">

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- หน้า 1: ปกเอกสารราชการ (PORTRAIT - บาลานซ์ 4 ทิศทาง)          -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div class="printable-page portrait-page page-cover text-center">

            <div class="text-center doc-header-block">
                <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="ตราโรงเรียน" class="school-logo">
                <div class="doc-header-title">แบบบันทึกผลการประเมินกิจกรรมพัฒนาผู้เรียน</div>
                <div class="doc-header-subtitle">(<?= esc($activityTypeLabel) ?>)</div>
                <div class="doc-header-school">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</div>

            </div>

            <!-- ตารางข้อมูลกิจกรรม -->
            <table class="cover-info-table">
                <tbody>
                    <tr>
                        <td class="cover-info-label">ชื่อกิจกรรม</td>
                        <td class="cover-info-value fw-bold"><?= esc($formattedClubName) ?></td>
                    </tr>
                    <tr>
                        <td class="cover-info-label">ระดับชั้น</td>
                        <td class="cover-info-value">
                            <?= esc($studyTimeInfo['formatted_level'] ?? ('มัธยมศึกษาตอน' . $club->club_level)) ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="cover-info-label">ภาคเรียน / ปีการศึกษา</td>
                        <td class="cover-info-value">ภาคเรียนที่ <?= esc($club->club_trem) ?> ปีการศึกษา
                            <?= esc($club->club_year) ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="cover-info-label">เวลาเรียนตามเกณฑ์</td>
                        <td class="cover-info-value">
                            <?= esc($studyTimeInfo['study_time_per_week_label'] ?? '1 คาบ/สัปดาห์') ?> (รวมตลอดภาคเรียน
                            <?= esc($studyTimeInfo['total_study_time_label'] ?? '20 ชั่วโมง') ?>)
                        </td>
                    </tr>
                    <tr>
                        <td class="cover-info-label"><?= esc($headRoleLabel) ?></td>
                        <td class="cover-info-value"><?= esc($activityHeadName) ?></td>
                    </tr>
                    <tr>
                        <td class="cover-info-label"><?= esc($evaluatorRoleLabel) ?></td>
                        <td class="cover-info-value">
                            <?php if (count($advisorNamesList) > 1): ?>
                                <ol class="mb-0 ps-3">
                                    <?php foreach ($advisorNamesList as $advName): ?>
                                        <li><?= esc($advName) ?></li>
                                    <?php endforeach; ?>
                                </ol>
                            <?php else: ?>
                                <?= esc($evaluatorName) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- ส่วนการอนุมัติผลการประเมินตามแบบราชการ -->
            <div class="approval-container">
                <div class="fw-bold text-start mb-0" style="font-size: 14pt; text-decoration: underline;">
                    การตรวจและอนุมัติผลการจัดกิจกรรม
                </div>

                <!-- กล่องที่ 1: คณะกรรมการผู้ตรวจและประเมิน (เรียงตามลำดับสายบังคับบัญชา) -->
                <div class="approval-box">
                    <!-- 1. บนสุดตรงกลาง: หัวหน้าผู้กำกับลูกเสือ / หัวหน้ากิจกรรม -->
                    <div class="row justify-content-center mb-1 mt-3">
                        <div class="col-12 text-center signature-line-group">
                            <div>ลงชื่อ<span class="sig-dots" style="width: 160px;"></span></div>
                            <div class="sig-name fw-bold" style="padding-left: 0; font-size: 14pt; margin-top: 1px;">(
                                <?= esc($activityHeadName) ?> )
                            </div>
                            <div style="font-size: 13pt; font-weight: 500;"><?= esc($headRoleLabel) ?></div>
                            <div class="text-muted" style="padding-left: 0; font-size: 12pt;">วันที่ ...... เดือน
                                ............................ พ.ศ. ...........</div>
                        </div>
                    </div>

                    <!-- 2. รองลงมา ซ้าย-ขวา: ผู้กำกับลูกเสือ / ครูที่ปรึกษา -->
                    <div class="row gx-2 justify-content-center mb-1 mt-3">
                        <?php if (count($advisorNamesList) === 1): ?>
                            <div class="col-12 text-center signature-line-group">
                                <div>ลงชื่อ<span class="sig-dots" style="width: 160px;"></span></div>
                                <div class="sig-name fw-bold" style="padding-left: 0; font-size: 14pt; margin-top: 1px;">(
                                    <?= esc($advisorNamesList[0]) ?> )
                                </div>
                                <div style="font-size: 13pt; font-weight: 500;"><?= esc($evaluatorRoleLabel) ?></div>
                                <div class="text-muted" style="padding-left: 0; font-size: 12pt;">วันที่ ...... เดือน
                                    ............................ พ.ศ. ...........</div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($advisorNamesList as $idx => $advName): ?>
                                <div class="col-6 px-2 text-center signature-line-group">
                                    <div>ลงชื่อ<span class="sig-dots" style="width: 140px;"></span></div>
                                    <div class="sig-name fw-bold" style="padding-left: 0; font-size: 14pt; margin-top: 1px;">(
                                        <?= esc($advName) ?> )
                                    </div>
                                    <div style="font-size: 13pt; font-weight: 500;">
                                        <?= esc($evaluatorRoleLabel) ?>
                                        <?= count($advisorNamesList) > 1 ? ' (' . ($idx + 1) . ')' : '' ?>
                                    </div>
                                    <div class="text-muted" style="padding-left: 0; font-size: 12pt;">วันที่ ...... เดือน
                                        ............................ พ.ศ. ...........</div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- 3. แถวเดียวกัน: หัวหน้าฝ่ายวัดและประเมินผล กับ หัวหน้างานกิจกรรมพัฒนาผู้เรียน -->
                    <div class="row gx-2 justify-content-center mb-1">
                        <div class="col-6 px-2 text-center signature-line-group">
                            <div>ลงชื่อ<span class="sig-dots" style="width: 140px;"></span></div>
                            <div class="sig-name fw-bold" style="padding-left: 0; font-size: 14pt; margin-top: 1px;">(
                                <?= esc($measurementHeadName) ?> )
                            </div>
                            <div style="font-size: 13pt; font-weight: 500;">หัวหน้าฝ่ายวัดและประเมินผล</div>
                            <div class="text-muted" style="padding-left: 0; font-size: 12pt;">วันที่ ...... เดือน
                                ............................ พ.ศ. ...........</div>
                        </div>
                        <div class="col-6 px-2 text-center signature-line-group">
                            <div>ลงชื่อ<span class="sig-dots" style="width: 140px;"></span></div>
                            <div class="sig-name fw-bold" style="padding-left: 0; font-size: 14pt; margin-top: 1px;">(
                                <?= esc($activityDevHeadName) ?> )
                            </div>
                            <div style="font-size: 13pt; font-weight: 500;">หัวหน้างานกิจกรรมพัฒนาผู้เรียน</div>
                            <div class="text-muted" style="padding-left: 0; font-size: 12pt;">วันที่ ...... เดือน
                                ............................ พ.ศ. ...........</div>
                        </div>
                    </div>

                    <!-- 4. สุดท้าย: รองผู้อำนวยการกลุ่มบริหารวิชาการ -->
                    <div class="row justify-content-center mb-0">
                        <div class="col-12 text-center signature-line-group" style="margin-bottom: 0;">
                            <div>ลงชื่อ<span class="sig-dots" style="width: 160px;"></span></div>
                            <div class="sig-name fw-bold" style="padding-left: 0; font-size: 14pt; margin-top: 1px;">(
                                <?= esc($deputyDirectorAcademicName) ?> )
                            </div>
                            <div style="font-size: 13pt; font-weight: 500;">รองผู้อำนวยการกลุ่มบริหารวิชาการ</div>
                            <div class="text-muted" style="padding-left: 0; font-size: 12pt;">วันที่ ...... เดือน
                                ............................ พ.ศ. ...........</div>
                        </div>
                    </div>
                </div>

                <!-- กล่องที่ 2: ผู้อำนวยการสถานศึกษา -->
                <div class="approval-box">
                    <div class="mb-1 fw-bold mt-3" style="font-size: 14pt;">
                        <span class="me-4">&#9744; อนุมัติ</span>
                        <span class="me-4">&#9744; ไม่อนุมัติ</span>
                        <span class="fw-normal">เนื่องจาก
                            ........................................................................................</span>
                    </div>
                    <div class="signature-line-group mt-1 text-center">
                        <div>ลงชื่อ<span class="sig-dots" style="width: 180px;"></span></div>
                        <div class="sig-name fw-bold" style="padding-left: 0; font-size: 14pt; margin-top: 1px;">(
                            <?= esc($directorName) ?> )
                        </div>
                        <div style="font-size: 14pt; font-weight: 500;">ผู้อำนวยการโรงเรียนสวนกุหลาบวิทยาลัย
                            (จิรประวัติ) นครสวรรค์</div>
                        <div class="sig-name" style="padding-left: 0; margin-top: 1px; font-size: 12pt;">วันที่ ......
                            เดือน ............................ พ.ศ. ...........</div>
                    </div>
                </div>

            </div>

        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- หน้า 2: กำหนดการจัดกิจกรรมการเรียนรู้ (PORTRAIT)              -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div class="printable-page portrait-page">
            <div class="text-center mb-3">
                <div class="fw-bold" style="font-size: 18pt;">กำหนดการจัดกิจกรรมการเรียนรู้</div>
                <div class="fw-bold" style="font-size: 16pt;"><?= esc($activityTypeLabel) ?> :
                    <?= esc($formattedClubName) ?>
                </div>
                <div style="font-size: 16pt;">
                    ระดับชั้น<?= esc($studyTimeInfo['formatted_level'] ?? ('มัธยมศึกษาตอน' . $club->club_level)) ?>
                    ภาคเรียนที่ <?= esc($club->club_trem) ?> ปีการศึกษา <?= esc($club->club_year) ?>
                    (เวลาเรียนรวม <?= esc($studyTimeInfo['total_study_periods_label'] ?? '20 คาบ') ?>)
                </div>
            </div>

            <table class="table-official" style="font-size: 14pt;">
                <thead>
                    <tr>
                        <th style="width: 60px;">ลำดับที่</th>
                        <th>กิจกรรม / แผนการจัดกิจกรรมการเรียนรู้</th>
                        <th style="width: 90px;">เวลา (คาบ)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($activities)): ?>
                        <?php
                        $defaultPeriods = $studyTimeInfo['periods_per_week'] ?? 1;
                        $totalPeriods = 0;
                        ?>
                        <?php foreach ($activities as $index => $activity): ?>
                            <?php
                            $actPeriods = (!empty($activity->act_number_of_periods) && (!($studyTimeInfo['is_high_school_or_mixed'] ?? false) || $activity->act_number_of_periods > 1))
                                ? $activity->act_number_of_periods
                                : $defaultPeriods;
                            ?>
                            <tr>
                                <td class="text-center"><?= esc($index + 1) ?></td>
                                <td><?= esc($activity->act_name) ?></td>
                                <td class="text-center"><?= esc($actPeriods) ?></td>
                            </tr>
                            <?php $totalPeriods += $actPeriods; ?>
                        <?php endforeach; ?>
                        <tr style="background-color: #f7f7f7;">
                            <td colspan="2" class="text-end fw-bold" style="padding-right: 12px;">รวมเวลาเรียนทั้งสิ้น</td>
                            <td class="text-center fw-bold"><?= esc($totalPeriods) ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-4">- ยังไม่มีข้อมูลกำหนดการจัดกิจกรรม -</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- หน้า 3: แบบบันทึกเวลาเรียน (LANDSCAPE)                        -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <?php
        $membersChunks = !empty($members) ? array_chunk($members, 25) : [[]];
        $totalMembersChunks = count($membersChunks);
        foreach ($membersChunks as $chunkIndex => $memberChunk):
            ?>
            <div class="printable-page landscape-page">
                <?php
                $studyTimePerWeek = $studyTimeInfo['study_time_per_week_label'] ?? (($club->club_level === 'ม.ปลาย') ? '2 ชม./สัปดาห์' : '1 คาบ/สัปดาห์');
                $totalStudyTime = $studyTimeInfo['total_study_time_label'] ?? (($club->club_level === 'ม.ปลาย') ? '40 ชม.' : '20 ชม.');
                ?>
                <div class="text-center mb-1">
                    <div class="fw-bold" style="font-size: 14pt;">
                        แบบบันทึกเวลาเรียน <?= esc($activityTypeLabel) ?> : <?= esc($formattedClubName) ?>
                        <?php if ($totalMembersChunks > 1): ?> (แผ่นที่
                            <?= $chunkIndex + 1 ?>/<?= $totalMembersChunks ?>)<?php endif; ?>
                    </div>
                    <div style="font-size: 12pt;">
                        ภาคเรียนที่ <?= esc($club->club_trem) ?> ปีการศึกษา <?= esc($club->club_year) ?>
                        | เกณฑ์เวลาเรียน: <?= esc($studyTimePerWeek) ?> (รวมตลอดภาคเรียน <?= esc($totalStudyTime) ?>)
                        | โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                    </div>
                </div>

                <table class="table-official landscape-table mb-1">
                    <thead>
                        <?php
                        $thaiMonthsShort = [
                            'January' => 'ม.ค.',
                            'February' => 'ก.พ.',
                            'March' => 'มี.ค.',
                            'April' => 'เม.ย.',
                            'May' => 'พ.ค.',
                            'June' => 'มิ.ย.',
                            'July' => 'ก.ค.',
                            'August' => 'ส.ค.',
                            'September' => 'ก.ย.',
                            'October' => 'ต.ค.',
                            'November' => 'พ.ย.',
                            'December' => 'ธ.ค.'
                        ];
                        ?>
                        <tr>
                            <th rowspan="2" style="width: 28px; font-size: 10pt;">เลขที่</th>
                            <th rowspan="2" style="min-width: 155px; font-size: 11pt;" class="text-start ps-2">ชื่อ -
                                นามสกุล</th>
                            <th rowspan="2" style="width: 44px; font-size: 10pt;">ชั้น</th>
                            <?php foreach ($schedulesByMonth as $month => $schedulesInMonth): ?>
                                <?php
                                $shortMonth = $thaiMonthsShort[date('F', strtotime($month))] ?? date('M', strtotime($month));
                                $buddhistYearShort = (date('Y', strtotime($month)) + 543) % 100;
                                ?>
                                <th colspan="<?= count($schedulesInMonth) ?>" style="font-size: 8pt;">
                                    <?= $shortMonth . ' ' . $buddhistYearShort ?>
                                </th>
                            <?php endforeach; ?>
                            <th rowspan="2" style="width: 32px;">รวม (คาบ)</th>
                            <th rowspan="2" style="width: 34px;">ผลการ ประเมิน</th>
                        </tr>
                        <tr>
                            <?php foreach ($schedulesByMonth as $month => $schedulesInMonth): ?>
                                <?php foreach ($schedulesInMonth as $schedule): ?>
                                    <th style="min-width: 17px; font-size: 7pt;">
                                        <?= date('d', strtotime($schedule->tcs_start_date)) ?>
                                    </th>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalDisplayedSchedules = 0;
                        if (!empty($schedulesByMonth)) {
                            foreach ($schedulesByMonth as $schedulesInMonth) {
                                $totalDisplayedSchedules += count($schedulesInMonth);
                            }
                        }
                        ?>
                        <?php if (!empty($memberChunk)): ?>
                            <?php foreach ($memberChunk as $member): ?>
                                <tr>
                                    <td class="student-num"><?= esc($member->StudentNumber) ?></td>
                                    <td class="student-name">
                                        <?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?>
                                    </td>
                                    <td class="student-class"><?= esc($member->StudentClass) ?></td>
                                    <?php
                                    $totalPresent = 0;
                                    foreach ($schedulesByMonth as $month => $schedulesInMonth):
                                        foreach ($schedulesInMonth as $schedule):
                                            $status = $attendanceMap[$member->StudentID][$schedule->tcs_schedule_id] ?? '-';
                                            if ($status === 'มา')
                                                $totalPresent++;
                                            ?>
                                            <td class="attendance-mark">
                                                <?= ($status === 'มา') ? '✓' : ($status === 'ขาด' ? 'ข' : ($status === 'ลาป่วย' ? 'ป' : ($status === 'ลากิจ' ? 'ก' : '-'))) ?>
                                            </td>
                                            <?php
                                        endforeach;
                                    endforeach;
                                    ?>
                                    <td class="fw-bold attendance-result"><?= $totalPresent ?></td>
                                    <td class="fw-bold attendance-result">
                                        <?php
                                        if ($totalDisplayedSchedules > 0) {
                                            $percentage = ($totalPresent / $totalDisplayedSchedules) * 100;
                                            echo ($percentage >= 80) ? 'ผ' : 'มผ';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= 5 + $totalDisplayedSchedules ?>" class="py-2">- ไม่พบรายชื่อนักเรียน -</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div style="font-size: 11pt;">
                    <span class="fw-bold">หมายเหตุการลงเวลา:</span> ✓ = มาเรียน | ข = ขาดเรียน | ป = ลาป่วย | ก = ลากิจ |
                    เกณฑ์ผ่าน (ผ) = เวลาเรียนไม่น้อยกว่าร้อยละ 80
                </div>
            </div>
        <?php endforeach; ?>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- หน้า 4: รายงานผลการประเมินตามจุดประสงค์ (PORTRAIT)             -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <?php
        $objectiveMembersChunks = !empty($members) ? array_chunk($members, 25) : [[]];
        $totalObjChunks = count($objectiveMembersChunks);
        foreach ($objectiveMembersChunks as $chunkIndex => $memberChunk):
            ?>
            <div class="printable-page portrait-page">
                <div class="text-center mb-2">
                    <div class="fw-bold" style="font-size: 16pt;">รายงานผลการประเมินตามจุดประสงค์</div>
                    <div class="fw-bold" style="font-size: 14pt;"><?= esc($activityTypeLabel) ?> :
                        <?= esc($formattedClubName) ?>
                        <?php if ($totalObjChunks > 1): ?> (แผ่นที่
                            <?= $chunkIndex + 1 ?>/<?= $totalObjChunks ?>)<?php endif; ?>
                    </div>
                    <div style="font-size: 14pt;">
                        ระดับชั้น<?= esc($studyTimeInfo['formatted_level'] ?? ('มัธยมศึกษาตอน' . $club->club_level)) ?>
                        ภาคเรียนที่ <?= esc($club->club_trem) ?> ปีการศึกษา <?= esc($club->club_year) ?>
                    </div>
                </div>

                <?php if (!empty($club_objectives)): ?>
                    <table class="table-official" style="font-size: 13pt;">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 40px;">ที่</th>
                                <th rowspan="2" style="min-width: 140px;" class="text-start ps-2">ชื่อ - นามสกุล</th>
                                <th rowspan="2" style="width: 50px;">ชั้น</th>
                                <th colspan="<?= count($club_objectives) ?>">ผลการประเมินตามจุดประสงค์ข้อที่</th>
                                <th rowspan="2" style="width: 45px;">รวม ผ่าน</th>
                                <th rowspan="2" style="width: 48px;">ผล การตัดสิน</th>
                            </tr>
                            <tr>
                                <?php foreach ($club_objectives as $objective): ?>
                                    <th style="width: 28px; font-size: 12pt;"><?= esc($objective->objective_order) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($memberChunk as $member): ?>
                                <tr>
                                    <td class="text-center"><?= esc($member->StudentNumber) ?></td>
                                    <td class="text-start ps-2" style="white-space: nowrap;">
                                        <?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?>
                                    </td>
                                    <td class="text-center"><?= esc($member->StudentClass) ?></td>
                                    <?php
                                    $totalPassed = 0;
                                    foreach ($club_objectives as $objective):
                                        $hasPassed = $objectiveProgressMap[$member->StudentID][$objective->objective_id] ?? false;
                                        if ($hasPassed)
                                            $totalPassed++;
                                        ?>
                                        <td class="text-center"><?= ($hasPassed) ? '✓' : '-' ?></td>
                                    <?php endforeach; ?>
                                    <td class="text-center fw-bold"><?= $totalPassed ?></td>
                                    <td class="text-center fw-bold">
                                        <?php
                                        if (count($club_objectives) > 0) {
                                            echo ($totalPassed === count($club_objectives)) ? 'ผ' : 'มผ';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-4 border my-3" style="font-size: 14pt;">
                        - ยังไม่มีการกำหนดจุดประสงค์ของกิจกรรม -
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- หน้า 5: รายละเอียดจุดประสงค์และสรุปผล (PORTRAIT)               -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div class="printable-page portrait-page">
            <div class="mb-3">
                <div class="fw-bold mb-2" style="font-size: 16pt; text-decoration: underline;">
                    รายละเอียดจุดประสงค์การจัดกิจกรรม
                </div>
                <?php if (!empty($club_objectives)): ?>
                    <table class="table-official" style="font-size: 13pt;">
                        <thead>
                            <tr>
                                <th style="width: 60px;">ข้อที่</th>
                                <th>จุดประสงค์การเรียนรู้ / เกณฑ์การประเมิน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($club_objectives as $objective): ?>
                                <tr>
                                    <td class="text-center fw-bold"><?= esc($objective->objective_order) ?></td>
                                    <td>
                                        <span class="fw-bold"><?= esc($objective->objective_name) ?></span>
                                        <?php if (!empty($objective->objective_description)): ?>
                                            <div class="text-muted" style="font-size: 12pt;">
                                                <?= esc($objective->objective_description) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-2 border mb-2" style="font-size: 14pt;">- ยังไม่มีข้อมูลจุดประสงค์ -</div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <div class="fw-bold mb-2" style="font-size: 16pt; text-decoration: underline;">
                    สรุปผลการประเมินกิจกรรมพัฒนาผู้เรียน
                </div>
                <table class="table-official text-center" style="font-size: 13pt;">
                    <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align: middle; padding: 6px;">จำนวนนักเรียนทั้งหมด</th>
                            <th colspan="4" style="padding: 6px;">ระดับผลการเข้าร่วมกิจกรรม</th>
                        </tr>
                        <tr>
                            <th style="width: 20%; padding: 6px;">ผ่าน (ผ)</th>
                            <th style="width: 20%; padding: 6px;">ไม่ผ่าน (มผ)</th>
                            <th style="width: 20%; padding: 6px;">ขาดเรียนนาน</th>
                            <th style="width: 20%; padding: 6px;">จำหน่าย</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold" style="padding: 6px; font-size: 14pt;">
                                <?= esc($summaryParticipation['totalStudents']) ?> คน
                            </td>
                            <td class="fw-bold text-success" style="padding: 6px; font-size: 14pt;">
                                <?= esc($summaryParticipation['passed']) ?> คน
                            </td>
                            <td class="fw-bold text-danger" style="padding: 6px; font-size: 14pt;">
                                <?= esc($summaryParticipation['failed']) ?> คน
                            </td>
                            <td style="padding: 6px; font-size: 14pt;"><?= esc($summaryParticipation['longAbsence']) ?>
                                คน</td>
                            <td style="padding: 6px; font-size: 14pt;"><?= esc($summaryParticipation['dismissed']) ?> คน
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <script>
        function printSpecificMode(mode) {
            document.body.classList.remove('mode-portrait-only', 'mode-landscape-only');
            const dynamicRule = document.getElementById('dynamic-page-rule');

            if (mode === 'portrait') {
                document.body.classList.add('mode-portrait-only');
                if (dynamicRule) {
                    dynamicRule.textContent = '@page { size: A4 portrait !important; margin: 0 !important; }';
                }
            } else if (mode === 'landscape') {
                document.body.classList.add('mode-landscape-only');
                if (dynamicRule) {
                    dynamicRule.textContent = '@page { size: A4 landscape !important; margin: 0 !important; }';
                }
            } else {
                if (dynamicRule) {
                    dynamicRule.textContent = '@page { size: A4 portrait; margin: 0 !important; } @page landscape-section { size: A4 landscape; margin: 0 !important; }';
                }
            }

            // รอให้ browser อัปเดต DOM/Style เล็กน้อยก่อนเรียกคำสั่งพิมพ์
            setTimeout(function () {
                window.print();
            }, 120);
        }
    </script>
</body>

</html>