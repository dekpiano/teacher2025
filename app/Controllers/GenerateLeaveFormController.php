<?php
/**
 * Script สำหรับ Generate แบบฟอร์มใบลาเปล่าเป็น PDF
 * ใช้ mPDF Library
 * เรียกใช้: php spark generate:leave-form หรือเข้าผ่าน URL
 */

namespace App\Controllers;

class GenerateLeaveFormController extends BaseController
{
    public function index()
    {
        // mPDF is now loaded via Composer autoloader
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => WRITEPATH . 'cache',
            'fontDir' => array_merge($fontDirs, [
                ROOTPATH . 'vendor/mpdf/mpdf/ttfonts',
            ]),
            'fontdata' => $fontData + [
                'thsarabun' => [
                    'R' => 'THSarabunNew.ttf',
                    'B' => 'THSarabunNew Bold.ttf',
                    'I' => 'THSarabunNew Italic.ttf',
                    'BI' => 'THSarabunNew BoldItalic.ttf',
                ]
            ],
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 16,
            'default_font' => 'thsarabun',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        $html = $this->getFormHtml();
        $mpdf->WriteHTML($html);

        // บันทึกไฟล์ PDF ไปที่ uploads/personnel/form-la.pdf
        $outputPath = ROOTPATH . 'uploads/personnel/form-la.pdf';
        
        // สร้างโฟลเดอร์ถ้ายังไม่มี
        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        $mpdf->Output($outputPath, \Mpdf\Output\Destination::FILE);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'สร้างไฟล์ form-la.pdf เรียบร้อยแล้ว',
            'path' => $outputPath
        ]);
    }

    /**
     * แสดง PDF ในเบราว์เซอร์
     */
    public function preview()
    {
        // mPDF is now loaded via Composer autoloader
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => WRITEPATH . 'cache',
            'fontDir' => array_merge($fontDirs, [
                ROOTPATH . 'vendor/mpdf/mpdf/ttfonts',
            ]),
            'fontdata' => $fontData + [
                'thsarabun' => [
                    'R' => 'THSarabunNew.ttf',
                    'B' => 'THSarabunNew Bold.ttf',
                    'I' => 'THSarabunNew Italic.ttf',
                    'BI' => 'THSarabunNew BoldItalic.ttf',
                ]
            ],
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 16,
            'default_font' => 'thsarabun',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        $html = $this->getFormHtml();
        $mpdf->WriteHTML($html);

        $this->response->setHeader('Content-Type', 'application/pdf');
        return $mpdf->Output('form-la.pdf', \Mpdf\Output\Destination::INLINE);
    }

    /**
     * สร้าง HTML สำหรับแบบฟอร์มใบลา
     */
    private function getFormHtml()
    {
        return '
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "thsarabun", sans-serif;
            font-size: 15pt;
            line-height: 1.4;
            color: #000;
        }
        .header-box {
            float: right;
            width: 200px;
            font-size: 13pt;
            line-height: 1.25;
            margin-bottom: 5px;
        }
        .header-docno {
            text-align: right;
            font-size: 13pt;
            margin-bottom: 4px;
        }
        .title {
            clear: both;
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 6px;
        }
        .subtitle {
            text-align: center;
            margin-bottom: 4px;
        }
        .date-line {
            text-align: right;
            margin-bottom: 8px;
            padding-right: 30px;
        }
        .content {
            text-indent: 50px;
            margin-bottom: 4px;
        }
        .no-indent {
            text-indent: 0;
        }
        .checkbox {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1px solid #000;
            margin-right: 4px;
            margin-left: 2px;
            vertical-align: middle;
        }
        .dotted {
            border-bottom: 1px dotted #000;
            display: inline-block;
        }
        .signature-section {
            margin-top: 10px;
            text-align: right;
            padding-right: 40px;
            line-height: 1.4;
        }
        table.stats {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            font-size: 13.5pt;
        }
        table.stats th, table.stats td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
        }
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .bottom-table td {
            vertical-align: top;
            padding: 2px 8px;
        }
        .small-text {
            font-size: 13.5pt;
        }
        .center-block {
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header Right Box -->
    <div class="header-box">
        <div class="header-docno">บค.๐๐๒/๒๕๖๘</div>
        <div>เลขรับเรื่อง...............................................</div>
        <div>วัน/เดือน/ปี.........................เวลา............</div>
    </div>

    <!-- Title -->
    <div class="title">ใบลาป่วย  ลาคลอดบุตร  ลากิจส่วนตัว</div>
    <div class="subtitle">
        เขียนที่.......................................................................................................
    </div>
    <div class="date-line">
        วันที่.............เดือน............................................พ.ศ......................
    </div>

    <!-- Subject -->
    <div class="content no-indent" style="margin-bottom: 3px;">
        <strong>เรื่อง</strong> ...................................................................................................................................................................................
    </div>

    <!-- To -->
    <div class="content no-indent" style="margin-bottom: 4px;">
        <strong>เรียน</strong> ผู้อำนวยการสถานศึกษา โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
    </div>

    <!-- Personal Info -->
    <div class="content">
        ข้าพเจ้า..................................................................ตำแหน่ง................................................................................................
    </div>
    <div class="content no-indent">
        สังกัด...........................................................................................................................................................................................
    </div>

    <!-- Leave Type -->
    <div class="content" style="margin-top: 4px;">
        <span style="display:inline-block; width: 60px;">ขอลา</span>
        <span class="checkbox"></span> ป่วย&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;เนื่องจาก ..........................................................................................................
    </div>
    <div class="content" style="text-indent: 60px;">
        <span class="checkbox"></span> กิจส่วนตัว&nbsp;&nbsp;&nbsp;เนื่องจาก ..........................................................................................................
    </div>
    <div class="content" style="text-indent: 60px;">
        <span class="checkbox"></span> คลอดบุตร
    </div>

    <!-- Leave Duration -->
    <div class="content" style="margin-top: 4px;">
        ตั้งแต่วันที่.....................................................ถึงวันที่.......................................................มีกำหนด....................................วัน
    </div>

    <!-- Previous Leave -->
    <div class="content">
        ข้าพเจ้าได้ลา&nbsp;&nbsp;&nbsp;&nbsp;<span class="checkbox"></span> ป่วย&nbsp;&nbsp;&nbsp;&nbsp;<span class="checkbox"></span> กิจส่วนตัว&nbsp;&nbsp;&nbsp;&nbsp;<span class="checkbox"></span> คลอดบุตร&nbsp;&nbsp;ครั้งสุดท้ายตั้งแต่วันที่.................................................
    </div>
    <div class="content no-indent">
        ถึงวันที่..................................................................มีกำหนด...................วัน ในระหว่างลาติดต่อข้าพเจ้าได้ที่ ..............................
    </div>
    <div class="content no-indent">
        ...................................................................................................................................................................................................
    </div>

    <!-- Signature -->
    <div class="signature-section">
        <div>ขอแสดงความนับถือ</div>
        <div style="margin-top: 6px;">(ลงชื่อ)...................................................................</div>
        <div>(...................................................................)</div>
        <div>ตำแหน่ง...................................................................</div>
    </div>

    <!-- Lower Section: 2 Columns (Left: Stats & Approvers / Right: Supervisors & Principal Command) -->
    <table class="bottom-table">
        <tr>
            <!-- Left Column: สถิติการลา & ผู้ตรวจสอบ & หัวหน้ากลุ่มสาระ -->
            <td style="width: 50%; padding-right: 12px;">
                <div style="font-weight: bold; margin-bottom: 2px;">สถิติการลาในปีงบประมาณนี้</div>
                <table class="stats">
                    <thead>
                        <tr>
                            <th style="width: 25%;">ประเภท<br>การลา</th>
                            <th style="width: 25%;">ลามาแล้ว<br>(วันทำการ)</th>
                            <th style="width: 25%;">ลาครั้งนี้<br>(วันทำการ)</th>
                            <th style="width: 25%;">รวมเป็น<br>(วันทำการ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: left; padding-left: 5px;">ป่วย</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="text-align: left; padding-left: 5px;">กิจส่วนตัว</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="text-align: left; padding-left: 5px;">คลอดบุตร</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top: 6px; line-height: 1.4;">
                    <div>(ลงชื่อ)...................................................................ผู้ตรวจสอบ</div>
                    <div style="text-align: center; width: 220px;">(...................................................................)</div>
                    <div>ตำแหน่ง...................................................................</div>
                    <div>วันที่............/......................../...................</div>
                </div>

                <div style="margin-top: 8px; line-height: 1.4;">
                    <div style="font-weight: bold; margin-bottom: 2px;">ความเห็นของหัวหน้ากลุ่มสาระฯ/หัวหน้างานฝ่าย</div>
                    <div>.......................................................................................................</div>
                    <div style="margin-top: 4px;">(ลงชื่อ)...................................................................</div>
                    <div style="text-align: center; width: 220px;">(...................................................................)</div>
                    <div>ตำแหน่ง...................................................................</div>
                    <div>วันที่............/......................../...................</div>
                </div>
            </td>

            <!-- Right Column: ความเห็นผู้บังคับบัญชา & คำสั่ง -->
            <td style="width: 50%; padding-left: 12px;">
                <div style="line-height: 1.4;">
                    <div style="font-weight: bold; margin-bottom: 2px;">ความเห็นผู้บังคับบัญชา</div>
                    <div>.......................................................................................................</div>
                    <div style="margin-top: 4px;">(ลงชื่อ)...................................................................</div>
                    <div style="text-align: center; width: 220px;">(...................................................................)</div>
                    <div>ตำแหน่ง...................................................................</div>
                    <div>วันที่............/......................../...................</div>
                </div>

                <div style="margin-top: 10px; line-height: 1.4;">
                    <div style="font-weight: bold; margin-bottom: 2px;">คำสั่ง</div>
                    <div style="margin-bottom: 4px;">
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="checkbox"></span> อนุญาต&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="checkbox"></span> ไม่อนุญาต
                    </div>
                    <div>.......................................................................................................</div>
                    <div>.......................................................................................................</div>
                    <div style="margin-top: 4px;">(ลงชื่อ)...................................................................</div>
                    <div style="text-align: center; width: 220px;">(...................................................................)</div>
                    <div>ตำแหน่ง...................................................................</div>
                    <div>วันที่............/......................../...................</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>';
    }
}
