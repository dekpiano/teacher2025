<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'คู่มือการใช้งานระบบชุมนุม') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .manual-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border-radius: 1.5rem;
        padding: 3rem 2rem;
        color: white;
        margin-bottom: 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.2);
    }
    .manual-header::before {
        content: "";
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        pointer-events: none;
    }
    .manual-section {
        background: #fff;
        border-radius: 1.25rem;
        padding: 2.5rem;
        margin-bottom: 2rem;
        border: 1px solid #edf2f7;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .manual-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
    }
    .section-icon {
        width: 60px;
        height: 60px;
        background: #f0f4ff;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        font-size: 1.75rem;
        color: #4f46e5;
    }
    .manual-section h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .manual-section h4 {
        font-size: 1.15rem;
        font-weight: 600;
        color: #2d3748;
        margin-top: 1.75rem;
        margin-bottom: 0.75rem;
        padding-left: 0.5rem;
        border-left: 4px solid #4f46e5;
    }
    .manual-section p {
        color: #4a5568;
        line-height: 1.7;
    }
    .manual-section ul {
        padding-left: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .manual-section li {
        margin-bottom: 0.75rem;
        color: #4a5568;
        position: relative;
    }
    .manual-section li strong {
        color: #2d3748;
    }
    .step-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        background: #4f46e5;
        color: white;
        border-radius: 50%;
        font-size: 0.85rem;
        font-weight: 700;
        margin-right: 0.75rem;
    }
    .feature-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    .feature-item {
        background: #f8fafc;
        padding: 1.25rem;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
    }
    .feature-item i {
        font-size: 1.25rem;
        color: #4f46e5;
        margin-bottom: 0.75rem;
        display: block;
    }
    .feature-item h5 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .feature-item p {
        font-size: 0.9rem;
        margin-bottom: 0;
    }
    .btn-print-manual {
        position: absolute;
        bottom: 2rem;
        right: 2rem;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 100px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-print-manual:hover {
        background: white;
        color: #4f46e5;
    }
    .quick-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 3rem;
    }
    .quick-nav-item {
        background: white;
        padding: 0.75rem 1.5rem;
        border-radius: 100px;
        color: #4a5568;
        font-weight: 600;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid #edf2f7;
    }
    .quick-nav-item:hover {
        background: #4f46e5;
        color: white;
        transform: translateY(-2px);
    }
    
    /* Mockup Wrapper Styles */
    .mockup-wrapper {
        background: #f8faff;
        border: 2px dashed rgba(79, 70, 229, 0.15);
        border-radius: 1.5rem;
        padding: 3rem 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 2rem;
        overflow: hidden;
    }
    .mockup-card {
        background: #fff;
        border-radius: 1.25rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 380px;
        overflow: hidden;
        border: 1px solid #edf2f7;
    }
    .mockup-card-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        padding: 1rem 1.25rem;
        color: white;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .mockup-card-body {
        padding: 1.5rem;
    }
    .mockup-input {
        height: 10px;
        background: #edf2f7;
        border-radius: 5px;
        margin-bottom: 1rem;
    }
    .mockup-button {
        height: 35px;
        background: #4f46e5;
        border-radius: 100px;
        width: 100%;
        margin-top: 1rem;
    }
    .rotate-right { transform: rotate(2deg); }
    .rotate-left { transform: rotate(-2deg); }
    
    @media (max-width: 991px) {
        .mockup-wrapper {
            padding: 2rem 1rem;
            margin-top: 2.5rem;
        }
        .mockup-card {
            max-width: 320px;
        }
    }
</style>

<div class="container-fluid">
    <div class="manual-header shadow-lg text-center">
        <h1 class="display-5 fw-bold mb-3">คู่มือการใช้งานระบบชุมนุม</h1>
        <p class="lead mb-0 opacity-75">ขั้นตอนและวิธีการจัดการกิจกรรมชุมนุมสำหรับคุณครูที่ปรึกษา</p>
        <div class="mt-4 d-flex justify-content-center gap-3">
            <span class="badge bg-white text-primary px-3 py-2 rounded-pill">ระบบใหม่ 2025</span>
            <span class="badge bg-white text-primary px-3 py-2 rounded-pill">คู่มือฉบับสมบูรณ์</span>
        </div>
        <!-- <button onclick="window.print()" class="btn btn-print-manual">
            <i class="bi bi-printer me-2"></i> พิมพ์คู่มือนี้
        </button> -->
    </div>

    <div class="quick-nav justify-content-center">
        <a href="#section-manage" class="quick-nav-item">1. การจัดการชุมนุม</a>
        <a href="#section-attendance" class="quick-nav-item">2. การเช็คชื่อนักเรียน</a>
        <a href="#section-evaluation" class="quick-nav-item">3. การประเมินผล</a>
        <a href="#section-report" class="quick-nav-item">4. การรายงานผล</a>
    </div>

    <!-- Section 1: การจัดการชุมนุม -->
    <div id="section-manage" class="manual-section">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="section-icon"><i class="bi bi-gear-wide-connected"></i></div>
                <h2>1. การจัดการชุมนุม</h2>
                <p>เริ่มต้นการสร้างและจัดการข้อมูลพื้นฐานของชุมนุม เพื่อเปิดรับนักเรียนเข้าสังกัด</p>
                
                <h4>การเพิ่มชุมนุมใหม่</h4>
                <ul>
                    <li><span class="step-badge">1</span>ไปที่เมนู <strong>"งานพัฒนาผู้เรียน"</strong> > <strong>"บันทึกชุมนุม"</strong></li>
                    <li><span class="step-badge">2</span>กดปุ่ม <strong>"สร้างชุมนุมใหม่"</strong> สีน้ำเงินด้านขวาบน</li>
                    <li><span class="step-badge">3</span>กรอกชื่อชุมนุม, รายละเอียด, จำนวนที่รับ และระดับชั้นที่เปิดรับ</li>
                    <li><span class="step-badge">4</span>ระบบจะกำหนด ปีการศึกษา/ภาคเรียน ให้โดยอัตโนมัติตามที่แอดมินตั้งค่าไว้</li>
                </ul>

                <h4>การจัดการสมาชิก</h4>
                <div class="feature-list">
                    <div class="feature-item">
                        <i class="bi bi-person-badge"></i>
                        <h5>บทบาทสมาชิก</h5>
                        <p>กำหนด <strong>"หัวหน้าชุมนุม"</strong> เพื่อช่วยดูแลระเบียบ</p>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-person-dash"></i>
                        <h5>การคัดออก</h5>
                        <p>กดปุ่ม <strong>"ลบ"</strong> เพื่อให้นักเรียนว่างและย้ายได้</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="mockup-wrapper">
                    <div class="mockup-card rotate-right">
                        <div class="mockup-card-header">
                            <i class="bi bi-plus-circle"></i>
                            <span class="fw-bold">สร้างชุมนุมใหม่</span>
                        </div>
                        <div class="mockup-card-body">
                            <div class="mockup-input w-75"></div>
                            <div class="mockup-input"></div>
                            <div class="row">
                                <div class="col-6"><div class="mockup-input"></div></div>
                                <div class="col-6"><div class="mockup-input"></div></div>
                            </div>
                            <div class="mockup-button"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: การเช็คชื่อนักเรียน -->
    <div id="section-attendance" class="manual-section">
        <div class="row align-items-center">
            <div class="col-lg-7 order-lg-2">
                <div class="section-icon" style="background: #fff7ed; color: #ea580c;"><i class="bi bi-calendar-check"></i></div>
                <h2>2. การเช็คชื่อนักเรียน</h2>
                <p>การบันทึกเวลาเรียนเป็นส่วนสำคัญในการตัดสินผลการเรียน (ต้องมีเวลาเรียนไม่น้อยกว่า 80%)</p>

                <h4>ขั้นตอนการเช็คชื่อ</h4>
                <ul>
                    <li><span class="step-badge">1</span>เลือกเมนู <strong>"ตารางกิจกรรม"</strong> และกด <strong>"เพิ่มสัปดาห์"</strong></li>
                    <li><span class="step-badge">2</span>คลิกปุ่ม <strong>"บันทึกเวลาเรียน"</strong> ในสัปดาห์ที่ต้องการ</li>
                    <li><span class="step-badge">3</span>เลือกสถานะ (มา, ขาด, ลา) และกด <strong>"บันทึกข้อมูล"</strong></li>
                </ul>
                <div class="alert alert-info rounded-4 border-0 mt-3 small">
                    <i class="bi bi-info-circle-fill me-2"></i> ระบบจะคำนวณร้อยละการเข้าเรียนให้อัตโนมัติ
                </div>
            </div>
            <div class="col-lg-5 order-lg-1">
                <div class="mockup-wrapper" style="background: #fffafa; border-color: rgba(234, 88, 12, 0.15);">
                    <div class="mockup-card rotate-left">
                        <div class="mockup-card-header" style="background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);">
                            <i class="bi bi-person-check"></i>
                            <span class="fw-bold">บันทึกเวลาเรียน - สัปดาห์ที่ 1</span>
                        </div>
                        <div class="mockup-card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <div class="mockup-input w-50 mb-0"></div>
                                <div class="badge bg-success rounded-pill" style="font-size: 0.6rem;">มา</div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <div class="mockup-input w-50 mb-0"></div>
                                <div class="badge bg-danger rounded-pill" style="font-size: 0.6rem;">ขาด</div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="mockup-input w-50 mb-0"></div>
                                <div class="badge bg-success rounded-pill" style="font-size: 0.6rem;">มา</div>
                            </div>
                            <div class="mockup-button" style="background: #ea580c;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: การประเมินผล -->
    <div id="section-evaluation" class="manual-section">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="section-icon" style="background: #f0fdf4; color: #16a34a;"><i class="bi bi-clipboard-check"></i></div>
                <h2>3. การประเมินผล</h2>
                <p>การตัดสินผลการเรียนชุมนุม (ผ่าน/ไม่ผ่าน) จะพิจารณาจาก 2 ส่วนคือ เวลาเรียน และ จุดประสงค์</p>

                <h4>การให้ผลการเรียน</h4>
                <ul>
                    <li><span class="step-badge">1</span>คลิกเมนู <strong>"ประเมินผลกิจกรรม"</strong></li>
                    <li><span class="step-badge">2</span>เช็คถูกในช่องจุดประสงค์ที่นักเรียนทำสำเร็จ</li>
                    <li><span class="step-badge">3</span><strong>ทางลัด:</strong> ใช้ปุ่ม <strong>"ผ่านรายบุคคล"</strong> เพื่อความรวดเร็ว</li>
                </ul>
                <div class="alert alert-warning rounded-4 border-0 mt-3 small">
                    <strong>เกณฑ์:</strong> ต้องมีเวลาเรียน >= 80% และผ่านจุดประสงค์ "ทุกข้อ"
                </div>
            </div>
            <div class="col-lg-5">
                <div class="mockup-wrapper" style="background: #f7fff9; border-color: rgba(22, 163, 74, 0.15);">
                    <div class="mockup-card rotate-right">
                        <div class="mockup-card-header" style="background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);">
                            <i class="bi bi-check-all"></i>
                            <span class="fw-bold">ผลการประเมินกิจกรรม</span>
                        </div>
                        <div class="mockup-card-body p-0">
                            <table class="table mb-0" style="font-size: 0.7rem;">
                                <tr class="bg-light">
                                    <th class="ps-3 py-2">ชื่อ</th>
                                    <th class="text-center py-2">จุดประสงค์</th>
                                    <th class="text-center py-2">สรุป</th>
                                </tr>
                                <tr>
                                    <td class="ps-3"><div class="mockup-input mb-0 w-75" style="height: 6px;"></div></td>
                                    <td class="text-center"><i class="bi bi-check-circle text-success font-bold"></i></td>
                                    <td class="text-center"><span class="badge bg-success p-1">ผ</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3"><div class="mockup-input mb-0 w-75" style="height: 6px;"></div></td>
                                    <td class="text-center"><i class="bi bi-check-circle text-success font-bold"></i></td>
                                    <td class="text-center"><span class="badge bg-success p-1">ผ</span></td>
                                </tr>
                            </table>
                            <div class="p-3">
                                <div class="mockup-button" style="background: #16a34a; height: 25px; margin-top: 0;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 4: การรายงานผล -->
    <div id="section-report" class="manual-section">
        <div class="row align-items-center">
            <div class="col-lg-7 order-lg-2">
                <div class="section-icon" style="background: #fdf2f8; color: #db2777;"><i class="bi bi-file-earmark-pdf"></i></div>
                <h2>4. การรายงานผล</h2>
                <p>การจัดทำเอกสารสรุปผลกิจกรรมชุมนุม 4 หน้ามาตรฐานเพื่อส่งฝ่ายวิชาการ</p>

                <h4>วิธีพิมพ์รายงาน</h4>
                <ul>
                    <li><span class="step-badge">1</span>เลือกเมนู <strong>"รายงาน & พิมพ์"</strong></li>
                    <li><span class="step-badge">2</span>หน้าจอจะแสดงพรีวิวรายงาน 4 หน้าแบบครบถ้วน</li>
                    <li><span class="step-badge">3</span>กดปุ่ม <strong>"พิมพ์รายงาน PDF"</strong> เพื่อ Save หรือสั่งพิมพ์</li>
                </ul>
                <div class="alert alert-dark rounded-4 border-0 mt-3 small opacity-75">
                    <i class="bi bi-info-circle-fill me-2"></i> ระบบดึงลายเซ็นผู้บริหารให้อัตโนมัติ
                </div>
            </div>
            <div class="col-lg-5 order-lg-1">
                <div class="mockup-wrapper" style="background: #fffafb; border-color: rgba(219, 39, 119, 0.15);">
                    <div class="mockup-card rotate-left">
                        <div class="mockup-card-header" style="background: linear-gradient(135deg, #db2777 0%, #ec4899 100%);">
                            <i class="bi bi-file-earmark-text"></i>
                            <span class="fw-bold">พรีวิวรายงาน PDF</span>
                        </div>
                        <div class="mockup-card-body">
                            <div class="text-center py-2">
                                <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 3rem;"></i>
                                <div class="mockup-input mx-auto mt-2 w-50"></div>
                                <div class="mockup-input mx-auto w-25"></div>
                            </div>
                            <div class="mockup-button" style="background: #db2777;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center py-5">
        <p class="text-muted">มีข้อสงสัยเพิ่มเติมหรือติดปัญหาการใช้งาน?</p>
        <a href="https://facebook.com/dekpiano" target="_blank" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-chat-dots me-2"></i> ติดต่อเจ้าหน้าที่ผู้ดูแลระบบ
        </a>
    </div>
</div>

<?= $this->endSection() ?>
