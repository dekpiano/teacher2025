<style>
    #clubHelpModal .modal-body h4 {
        font-size: 1.1rem;
        font-weight: bold;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
        color: #566a7f;
        border-bottom: 1px solid #d9dee3;
        padding-bottom: 0.5rem;
    }
    #clubHelpModal .modal-body h5 {
        font-size: 1rem;
        font-weight: bold;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        color: #696cff;
    }
    #clubHelpModal .modal-body ul {
        padding-left: 20px;
    }
    #clubHelpModal .modal-body li {
        margin-bottom: 0.5rem;
    }
    #clubHelpModal .modal-body .nav-pills {
        margin-bottom: 1rem;
    }
    #clubHelpModal .modal-body .nav-link {
        cursor: pointer;
    }
</style>

<!-- Nav Pills -->
<ul class="nav nav-pills nav-fill mb-4" id="help-pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pills-index-tab" data-bs-toggle="pill" data-bs-target="#pills-index" type="button" role="tab" aria-controls="pills-index" aria-selected="true">หน้าหลัก</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-manage-tab" data-bs-toggle="pill" data-bs-target="#pills-manage" type="button" role="tab" aria-controls="pills-manage" aria-selected="false">จัดการชุมนุม</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-schedule-tab" data-bs-toggle="pill" data-bs-target="#pills-schedule" type="button" role="tab" aria-controls="pills-schedule" aria-selected="false">ตารางกิจกรรม</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-objectives-tab" data-bs-toggle="pill" data-bs-target="#pills-objectives" type="button" role="tab" aria-controls="pills-objectives" aria-selected="false">จุดประสงค์</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-attendance-tab" data-bs-toggle="pill" data-bs-target="#pills-attendance" type="button" role="tab" aria-controls="pills-attendance" aria-selected="false">บันทึกเข้าเรียน</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-report-tab" data-bs-toggle="pill" data-bs-target="#pills-report" type="button" role="tab" aria-controls="pills-report" aria-selected="false">รายงาน</button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="help-pills-tabContent">
    <div class="tab-pane fade show active" id="pills-index" role="tabpanel" aria-labelledby="pills-index-tab">
        <h4>1. หน้าหลักชุมนุม</h4>
        <p>หน้านี้เป็นภาพรวมของชุมนุมที่คุณเป็นที่ปรึกษา</p>
        <h5>การสร้างชุมนุมใหม่</h5>
        <ul>
            <li>หากคุณยังไม่มีชุมนุมสำหรับภาคเรียนปัจจุบัน จะมีปุ่ม "สร้างชุมนุมใหม่" แสดงขึ้น</li>
            <li>คลิกที่ปุ่มนี้เพื่อเปิดหน้าต่างสำหรับกรอกข้อมูลชุมนุม เช่น ชื่อ, คำอธิบาย, จำนวนรับสูงสุด, และระดับชั้น</li>
            <li>กรอกข้อมูลให้ครบถ้วนแล้วคลิก "บันทึก"</li>
        </ul>
        <h5>การจัดการชุมนุมที่มีอยู่</h5>
        <ul>
            <li>แต่ละชุมนุมจะแสดงเป็น "Card" พร้อมข้อมูลสรุป</li>
            <li>คลิกปุ่ม "จัดการชุมนุม" บน Card ของชุมนุมที่คุณต้องการ เพื่อเข้าสู่หน้าจัดการรายละเอียดของชุมนุมนั้นๆ</li>
        </ul>
    </div>

    <div class="tab-pane fade" id="pills-manage" role="tabpanel" aria-labelledby="pills-manage-tab">
        <h4>2. หน้าจัดการชุมนุม</h4>
        <p>หน้านี้ใช้สำหรับจัดการรายละเอียดและสมาชิกของชุมนุมที่คุณเลือก</p>
        <h5>การแก้ไขข้อมูลชุมนุม</h5>
        <ul>
            <li>คลิกปุ่ม "แก้ไขข้อมูลชุมนุม" เพื่อเปิดหน้าต่างแก้ไข</li>
            <li>ปรับเปลี่ยนข้อมูลที่ต้องการ เช่น ชื่อ, คำอธิบาย, จำนวนรับสูงสุด, สถานะ, หรือระดับชั้น</li>
            <li>คลิก "บันทึกการเปลี่ยนแปลง" เพื่อยืนยัน</li>
        </ul>
        <h5>การจัดการสมาชิกในชุมนุม</h5>
        <ul>
            <li>ส่วน "สมาชิกในชุมนุม" จะแสดงรายชื่อนักเรียนที่เข้าร่วม</li>
            <li><strong>กำหนดบทบาท:</strong> คลิกปุ่ม "กำหนดบทบาท" ข้างชื่อนักเรียน เพื่อเปลี่ยนบทบาทเป็น "สมาชิก" หรือ "หัวหน้า"</li>
        </ul>
        <h5>ลิงก์ไปยังส่วนอื่นๆ ของชุมนุม</h5>
        <ul>
            <li><strong>บันทึกเวลากิจกรรม:</strong> เพื่อไปยังหน้าตารางกิจกรรมและบันทึกเวลาเรียน</li>
            <li><strong>จุดประสงค์กิจกรรม:</strong> เพื่อไปยังหน้าจัดการและบันทึกความก้าวหน้าตามจุดประสงค์</li>
            <li><strong>รายงานกิจกรรม:</strong> เพื่อดูรายงานสรุปการเข้าเรียนและผลการประเมินจุดประสงค์</li>
        </ul>
    </div>

    <div class="tab-pane fade" id="pills-schedule" role="tabpanel" aria-labelledby="pills-schedule-tab">
        <h4>3. หน้าตารางกิจกรรมชุมนุม</h4>
        <p>หน้านี้แสดงตารางกิจกรรมของชุมนุมและเป็นจุดเริ่มต้นในการบันทึกกิจกรรมและการเข้าเรียน</p>
        <h5>การบันทึก/แก้ไขกิจกรรม</h5>
        <ul>
            <li>ในแต่ละแถวของตาราง คลิกปุ่ม "บันทึก/แก้ไขกิจกรรม"</li>
            <li>กรอกหรือแก้ไขรายละเอียดกิจกรรมสำหรับสัปดาห์นั้นๆ เช่น ชื่อกิจกรรม, สถานที่, เวลา, และจำนวนคาบ</li>
            <li>คลิก "บันทึก" เพื่อบันทึกข้อมูลกิจกรรม</li>
        </ul>
        <h5>การบันทึกการเข้าเรียน</h5>
        <ul>
            <li>หลังจากบันทึกกิจกรรมแล้ว คุณสามารถคลิกปุ่ม "บันทึกการเข้าเรียน" เพื่อไปยังหน้าบันทึกสถานะการเข้าเรียนของนักเรียนในกิจกรรมนั้นๆ</li>
        </ul>
    </div>

    <div class="tab-pane fade" id="pills-objectives" role="tabpanel" aria-labelledby="pills-objectives-tab">
        <h4>4. หน้าจุดประสงค์กิจกรรม</h4>
        <p>หน้านี้ใช้สำหรับกำหนดจุดประสงค์ของชุมนุมและบันทึกความก้าวหน้าของนักเรียน</p>
        <h5>การจัดการจุดประสงค์ (เพิ่ม/แก้ไข/ลบ)</h5>
        <ul>
            <li>คลิกปุ่ม "จัดการจุดประสงค์" เพื่อเปิดหน้าต่างจัดการ</li>
            <li><strong>เพิ่ม:</strong> กรอก "ชื่อจุดประสงค์", "คำอธิบาย", และ "ลำดับ" แล้วคลิก "บันทึกจุดประสงค์"</li>
            <li><strong>แก้ไข:</strong> ในตาราง "จุดประสงค์ที่มีอยู่" คลิกปุ่ม "แก้ไข"</li>
            <li><strong>ลบ:</strong> คลิกปุ่ม "ลบ" ข้างจุดประสงค์ที่ต้องการลบ</li>
        </ul>
        <h5>การบันทึกความก้าวหน้าของนักเรียน</h5>
        <ul>
            <li>ในตารางหลัก ทำเครื่องหมายในช่องที่นักเรียนผ่านจุดประสงค์นั้นๆ</li>
            <li>ระบบจะคำนวณผลโดยอัตโนมัติ</li>
            <li>เมื่อทำเครื่องหมายครบถ้วนแล้ว คลิกปุ่ม "บันทึกข้อมูล" ที่ด้านล่างของตาราง</li>
        </ul>
    </div>

    <div class="tab-pane fade" id="pills-attendance" role="tabpanel" aria-labelledby="pills-attendance-tab">
        <h4>5. หน้าบันทึกการเข้าเรียน</h4>
        <p>หน้านี้ใช้สำหรับบันทึกสถานะการเข้าเรียนของนักเรียนในแต่ละกิจกรรม</p>
        <h5>การเลือกสถานะการเข้าเรียน</h5>
        <ul>
            <li>สำหรับนักเรียนแต่ละคน ให้เลือกสถานะการเข้าเรียนจากเมนู (เช่น มา, ขาด, ลาป่วย)</li>
            <li>สีของช่องเลือกจะเปลี่ยนไปตามสถานะที่เลือก</li>
        </ul>
        <h5>การบันทึก</h5>
        <ul>
            <li>เมื่อเลือกสถานะของนักเรียนทุกคนเรียบร้อยแล้ว คลิกปุ่ม "บันทึกการเข้าเรียน"</li>
        </ul>
    </div>

    <div class="tab-pane fade" id="pills-report" role="tabpanel" aria-labelledby="pills-report-tab">
        <h4>6. หน้ารายงานกิจกรรม</h4>
        <p>หน้านี้แสดงรายงานสรุปการเข้าเรียนและผลการประเมินจุดประสงค์ของนักเรียนในชุมนุม</p>
        <h5>รายงานผลการบันทึกเวลาเรียน</h5>
        <ul>
            <li>ตารางแรกจะแสดงสถานะการเข้าเรียนของนักเรียนแต่ละคนในแต่ละวันที่มีกิจกรรม พร้อมสรุปผล</li>
        </ul>
        <h5>รายงานผลการประเมินตามจุดประสงค์</h5>
        <ul>
            <li>ตารางที่สองจะแสดงสถานะการผ่านจุดประสงค์แต่ละข้อของนักเรียน พร้อมสรุปผล</li>
        </ul>
        <h5>การพิมพ์รายงาน</h5>
        <ul>
            <li>คลิกปุ่ม "พิมพ์รายงาน" ที่ด้านบนของหน้า เพื่อเปิดหน้าสำหรับพิมพ์รายงานทั้งหมด</li>
        </ul>
    </div>
</div>
