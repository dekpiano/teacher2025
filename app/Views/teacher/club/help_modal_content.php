<style>
    #clubHelpModal .modal-body h4 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        color: #4338ca;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    #clubHelpModal .modal-body h4::before {
        content: "";
        display: inline-block;
        width: 4px;
        height: 24px;
        background-color: #4338ca;
        border-radius: 2px;
    }
    #clubHelpModal .modal-body h5 {
        font-size: 1.05rem;
        font-weight: 600;
        margin-top: 1.25rem;
        margin-bottom: 0.75rem;
        color: #1e293b;
    }
    #clubHelpModal .modal-body ul {
        padding-left: 0;
        list-style-type: none;
    }
    #clubHelpModal .modal-body li {
        margin-bottom: 0.75rem;
        position: relative;
        padding-left: 1.75rem;
        color: #475569;
    }
    #clubHelpModal .modal-body li::before {
        content: "\F272";
        font-family: "bootstrap-icons";
        position: absolute;
        left: 0;
        color: #10b981;
        font-weight: bold;
    }
    #clubHelpModal .nav-pills {
        background: #f8fafc;
        padding: 0.5rem;
        border-radius: 0.75rem;
    }
    #clubHelpModal .nav-link {
        color: #64748b;
        font-weight: 600;
        border-radius: 0.5rem;
        padding: 0.6rem 1rem;
        transition: all 0.2s;
    }
    #clubHelpModal .nav-link.active {
        background-color: #ffffff;
        color: #4338ca;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .help-section-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
</style>

<div class="p-4">
    <!-- Nav Pills -->
    <ul class="nav nav-pills nav-fill mb-4" id="help-pills-tab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="help-pills-index-tab" data-bs-toggle="pill" data-bs-target="#help-pills-index" type="button" role="tab">
                <i class="bi bi-house-door me-1"></i> หน้าหลัก
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="help-pills-manage-tab" data-bs-toggle="pill" data-bs-target="#help-pills-manage" type="button" role="tab">
                <i class="bi bi-gear me-1"></i> จัดการชุมนุม
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="help-pills-schedule-tab" data-bs-toggle="pill" data-bs-target="#help-pills-schedule" type="button" role="tab">
                <i class="bi bi-calendar-check me-1"></i> ตารางกิจกรรม
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content border-0" id="help-pills-tabContent">
        <div class="tab-pane fade show active" id="help-pills-index" role="tabpanel">
            <div class="help-section-card">
                <h4>การใช้งานหน้าหลัก</h4>
                <div class="row g-4">
                    <div class="col-md-6">
                        <h5>สร้างชุมนุมใหม่</h5>
                        <ul>
                            <li>กดปุ่ม <strong>"สร้างชุมนุมใหม่"</strong> สีน้ำเงินด้านบน</li>
                            <li>กรอกชื่อและรายละเอียดเบื้องต้น</li>
                            <li>จำนวนรับสามารถปรับได้ตลอดเวลา</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>การเข้าจัดการ</h5>
                        <ul>
                            <li>เลือกชุมนุมที่ต้องการจาก Card รายการ</li>
                            <li>คลิกที่ปุ่ม <strong>"จัดการชุมนุม"</strong> ใต้ Card นั้นๆ</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="help-pills-manage" role="tabpanel">
            <div class="help-section-card">
                <h4>การจัดการสมาชิก</h4>
                <h5>แก้ไขข้อมูลวิชา</h5>
                <ul>
                    <li>คลิกปุ่ม <strong>"แก้ไขข้อมูล"</strong> ในส่วนหัวเพื่อปรับปรุงชื่อหรือจำนวนรับ</li>
                </ul>
                <h5>จัดการนักเรียน</h5>
                <ul>
                    <li><strong>บทบาท:</strong> กำหนดหัวหน้าทีมเพื่อช่วยดูแลเพื่อนๆ</li>
                    <li><strong>การคัดออก:</strong> ใช้ปุ่ม "ลบ" หากนักเรียนต้องการย้ายชุมนุม</li>
                </ul>
            </div>
        </div>

        <div class="tab-pane fade" id="help-pills-schedule" role="tabpanel">
            <div class="help-section-card">
                <h4>การบันทึกหน้างาน</h4>
                <h5>เช็คชื่อ & บันทึกกิจกรรม</h5>
                <ul>
                    <li>ใช้เมนู <strong>"เช็คชื่อนักเรียน"</strong> เพื่อบันทึกการเข้าเรียนรายสัปดาห์</li>
                    <li>ระบบจะคำนวณสถิติเพื่อใช้ตัดสินผลการเรียนอัตโนมัติ</li>
                </ul>
            </div>
        </div>
    </div>
</div>
