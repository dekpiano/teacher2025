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
        // Load mPDF
        if (file_exists(SHARED_LIB_PATH . '/mpdf/vendor/autoload.php')) {
            require_once SHARED_LIB_PATH . '/mpdf/vendor/autoload.php';
        } else {
            $path = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
            require_once $path . '/librarie_skj/mpdf/vendor/autoload.php';
        }

        $mpdf = new \Mpdf\Mpdf([
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
        // Load mPDF
        if (file_exists(SHARED_LIB_PATH . '/mpdf/vendor/autoload.php')) {
            require_once SHARED_LIB_PATH . '/mpdf/vendor/autoload.php';
        } else {
            $path = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
            require_once $path . '/librarie_skj/mpdf/vendor/autoload.php';
        }

        $mpdf = new \Mpdf\Mpdf([
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
            font-size: 16pt;
            line-height: 1.5;
        }
        .header-right {
            text-align: right;
            margin-bottom: 5px;
        }
        .title {
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            margin: 10px 0;
        }
        .subtitle {
            text-align: center;
            margin-bottom: 5px;
        }
        .date-line {
            text-align: right;
            margin-bottom: 10px;
        }
        .content {
            text-indent: 60px;
            margin-bottom: 5px;
        }
        .no-indent {
            text-indent: 0;
        }
        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 3px;
            vertical-align: middle;
        }
        .dotted {
            border-bottom: 1px dotted #000;
            display: inline-block;
        }
        .signature-section {
            margin-top: 20px;
            text-align: right;
            padding-right: 30px;
        }
        table.stats {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 14pt;
        }
        table.stats th, table.stats td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
        }
        .two-col {
            width: 100%;
        }
        .two-col td {
            width: 50%;
            vertical-align: top;
            padding: 5px;
        }
        .small-text {
            font-size: 14pt;
        }
        .mt-10 { margin-top: 10px; }
        .mb-5 { margin-bottom: 5px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-right">
        ปร.001/<span class="dotted" style="width: 80px;">&nbsp;</span>
    </div>

    <!-- Title -->
    <div class="title">ใบลาป่วย  ลาคลอดบุตร  ลากิจส่วนตัว</div>
    <div class="subtitle">
        เขียนที่ <span class="dotted" style="width: 280px;">&nbsp;</span>
    </div>
    <div class="date-line">
        วันที่ <span class="dotted" style="width: 40px;">&nbsp;</span>
        เดือน <span class="dotted" style="width: 100px;">&nbsp;</span>
        พ.ศ. <span class="dotted" style="width: 50px;">&nbsp;</span>
    </div>

    <!-- Subject -->
    <div class="content no-indent mb-5">
        <strong>เรื่อง</strong> <span class="dotted" style="width: 450px;">&nbsp;</span>
    </div>

    <!-- To -->
    <div class="content no-indent mb-5">
        <strong>เรียน</strong> ผู้อำนวยการสถานศึกษา โรงเรียนสวรรค์อนันตวิทยา (จิรประวัติ) นครสวรรค์
    </div>

    <!-- Personal Info -->
    <div class="content">
        ข้าพเจ้า <span class="dotted" style="width: 200px;">&nbsp;</span>
        ตำแหน่ง <span class="dotted" style="width: 120px;">&nbsp;</span>
        สังกัด <span class="dotted" style="width: 180px;">&nbsp;</span>
    </div>

    <!-- Leave Type -->
    <div class="content">
        ขอ
        <span class="checkbox"></span> ป่วย
        <span class="checkbox"></span> กิจส่วนตัว
        <span class="checkbox"></span> คลอดบุตร
        เนื่องจาก <span class="dotted" style="width: 280px;">&nbsp;</span>
    </div>

    <!-- Leave Duration -->
    <div class="content">
        ตั้งแต่วันที่ <span class="dotted" style="width: 40px;">&nbsp;</span>
        เดือน <span class="dotted" style="width: 80px;">&nbsp;</span>
        พ.ศ. <span class="dotted" style="width: 40px;">&nbsp;</span>
        ถึงวันที่ <span class="dotted" style="width: 40px;">&nbsp;</span>
        เดือน <span class="dotted" style="width: 80px;">&nbsp;</span>
        พ.ศ. <span class="dotted" style="width: 40px;">&nbsp;</span>
        มีกำหนด <span class="dotted" style="width: 40px;">&nbsp;</span> วัน
    </div>

    <!-- Previous Leave -->
    <div class="content">
        ข้าพเจ้าได้ลา
        <span class="checkbox"></span> ป่วย
        <span class="checkbox"></span> กิจส่วนตัว
        <span class="checkbox"></span> คลอดบุตร
        ครั้งสุดท้ายตั้งแต่วันที่ <span class="dotted" style="width: 150px;">&nbsp;</span>
    </div>
    <div class="content no-indent">
        ถึงวันที่ <span class="dotted" style="width: 200px;">&nbsp;</span>
        มีกำหนด <span class="dotted" style="width: 40px;">&nbsp;</span> วัน ในระหว่างลาติดต่อข้าพเจ้าได้ที่ <span class="dotted" style="width: 150px;">&nbsp;</span>
    </div>

    <!-- Signature -->
    <div class="signature-section">
        <div class="mb-5">ขอแสดงความนับถือ</div>
        <div class="mt-10">(ลงชื่อ) <span class="dotted" style="width: 180px;">&nbsp;</span></div>
        <div>(<span class="dotted" style="width: 180px;">&nbsp;</span>)</div>
        <div>ตำแหน่ง <span class="dotted" style="width: 150px;">&nbsp;</span></div>
    </div>

    <!-- Statistics Table -->
    <div class="mt-10">
        <strong>สถิติการลาในปีงบประมาณนี้</strong>
    </div>
    <table class="stats">
        <thead>
            <tr>
                <th rowspan="2">ประเภท<br>การลา</th>
                <th colspan="2">ลามาแล้ว</th>
                <th colspan="2">ลาครั้งนี้</th>
                <th colspan="2">รวมเป็น</th>
                <th rowspan="2">ความเห็นผู้รับผิดชอบ</th>
            </tr>
            <tr>
                <th>ครั้ง</th>
                <th>วันทำการ</th>
                <th>ครั้ง</th>
                <th>วันทำการ</th>
                <th>ครั้ง</th>
                <th>วันทำการ</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>ป่วย</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>กิจส่วนตัว</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>คลอดบุตร</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>

    <!-- Approval Section -->
    <table class="two-col" style="margin-top: 10px;">
        <tr>
            <td>
                <div>(ลงชื่อ) <span class="dotted" style="width: 150px;">&nbsp;</span> ผู้ตรวจสอบ</div>
                <div class="mt-10">(<span class="dotted" style="width: 150px;">&nbsp;</span>)</div>
            </td>
            <td>
                <div><strong>คำสั่ง</strong></div>
                <div>
                    <span class="checkbox"></span> อนุญาต
                    <span class="checkbox"></span> ไม่อนุญาต
                </div>
                <div class="mt-10">(ลงชื่อ) <span class="dotted" style="width: 150px;">&nbsp;</span></div>
                <div>(<span class="dotted" style="width: 150px;">&nbsp;</span>)</div>
                <div>ตำแหน่ง <span class="dotted" style="width: 130px;">&nbsp;</span></div>
                <div>วันที่ <span class="dotted" style="width: 140px;">&nbsp;</span></div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="mt-10 small-text">
        <strong>ความเห็นของหัวหน้ากลุ่มสาระ/งานที่รับผิดชอบ</strong>
    </div>
    <div class="small-text">
        (ลงชื่อ) <span class="dotted" style="width: 150px;">&nbsp;</span>
        (<span class="dotted" style="width: 150px;">&nbsp;</span>)
        ตำแหน่ง <span class="dotted" style="width: 120px;">&nbsp;</span>
        วันที่ <span class="dotted" style="width: 100px;">&nbsp;</span>
    </div>

</body>
</html>';
    }
}
