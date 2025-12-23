<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'จัดการแผนการสอน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .curriculum-header {
        background: linear-gradient(135deg, #696cff 0%, #3f42ef 100%);
        border-radius: 1.25rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(105, 108, 255, 0.2);
        position: relative;
        overflow: hidden;
    }
    .curriculum-header::after {
        content: '\F323'; /* bi-journal-text icon */
        font-family: 'bootstrap-icons';
        position: absolute;
        right: -20px;
        bottom: -30px;
        font-size: 10rem;
        opacity: 0.1;
        transform: rotate(-15deg);
    }
    .status-badge-plan {
        backdrop-filter: blur(8px);
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .stat-card-luxe {
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        transition: transform 0.3s ease;
    }
    .stat-card-luxe:hover {
        transform: translateY(-5px);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .subject-card-luxe {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.04);
        overflow: hidden;
        margin-bottom: 2rem;
        transition: box-shadow 0.3s ease;
    }
    .subject-card-luxe:hover {
        box-shadow: 0 12px 35px rgba(0,0,0,0.08);
    }
    .subject-card-luxe .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.25rem 1.5rem;
    }
    .main-subject-header {
        background: linear-gradient(to right, #696cff, #8e91ff) !important;
        color: white !important;
    }
    .main-subject-header .text-primary,
    .main-subject-header h5 {
        color: white !important;
    }
    .main-subject-header .text-muted {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    .main-subject-badge {
        background: rgba(255, 215, 0, 0.9);
        color: #000;
        font-weight: 600;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .plan-table {
        margin-bottom: 0;
    }
    .plan-table th {
        background: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #566a7f;
        padding: 1rem 1.5rem;
    }
    .plan-table td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
    }
    .badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
    .btn-luxe-primary {
        background: #696cff;
        border: none;
        color: white;
        padding: 0.6rem 1.2rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3);
    }
</style>

<div class="container-xxl flex-grow-1">
    <?php
    $onOffSetting = $OnOff[0] ?? null;
    $is_system_on = false;
    $deadline = null;
    if ($onOffSetting) {
        $tiemstart = strtotime($onOffSetting->seplanset_startdate);
        $tiemEnd = strtotime($onOffSetting->seplanset_enddate);
        $timeNow = time();
        $is_system_on = ($tiemstart < $timeNow && $tiemEnd > $timeNow && $onOffSetting->seplanset_status == "on");
        $deadline = $onOffSetting->seplanset_enddate;
    }

    // --- Stats calculation ---
    $all_plan_types = array_column($activePlanTypes, 'type_name');
    $submitted_count = 0;
    $approved_count = 0;
    foreach ($plan as $p) {
        if (!empty($p->seplan_file)) $submitted_count++;
        if (trim($p->seplan_status2) == 'ผ่าน') $approved_count++;
    }
    $total_required = count($planNew) * count($all_plan_types); // Estimation
    ?>

    <!-- Hero Header -->
    <div class="curriculum-header">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="display-6 fw-bold mb-2 text-white">จัดการแผนการสอน</h1>
                <p class="opacity-75 mb-4">ระบบนำส่งและติดตามตรวจแผนการสอนประจำภาคเรียน</p>
                <div class="d-flex flex-wrap gap-3">
                    <div class="status-badge-plan">
                        <i class="bi bi-calendar3"></i> ภาคเรียน <?= esc($term.'/'.$year) ?>
                    </div>
                    <?php if($is_system_on): ?>
                        <div class="status-badge-plan bg-success border-0 text-white">
                            <i class="bi bi-unlock-fill"></i> ระบบเปิดรับงาน (Deadline: <?= thai_date_and_time(strtotime($deadline)) ?>)
                        </div>
                    <?php else: ?>
                        <div class="status-badge-plan bg-danger border-0 text-white">
                            <i class="bi bi-lock-fill"></i> ระบบปิดรับงาน
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
                <div class="d-flex flex-column flex-lg-row justify-content-lg-end gap-3">
                  
                    <button type="button" class="btn btn-white btn-lg rounded-pill shadow-lg px-4 py-3 text-primary fw-bold opacity-75" data-bs-toggle="modal" data-bs-target="#usageGuideModal">
                        <i class="bi bi-info-circle-fill me-2"></i> คู่มือการใช้งาน
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats & Filter -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="stat-card-luxe">
                <div class="stat-icon bg-label-primary">
                    <i class="bi bi-journal-text fs-4"></i>
                </div>
                <h6 class="text-muted mb-1 small">ส่งแล้วทั้งหมด</h6>
                <h4 class="fw-bold mb-0"><?= $submitted_count ?> รายการ</h4>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="stat-card-luxe">
                <div class="stat-icon bg-label-success">
                    <i class="bi bi-patch-check fs-4"></i>
                </div>
                <h6 class="text-muted mb-1 small">ตรวจผ่านแล้ว</h6>
                <h4 class="fw-bold mb-0"><?= $approved_count ?> รายการ</h4>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="stat-card-luxe">
                <div class="stat-icon bg-label-warning">
                    <i class="bi bi-arrow-repeat fs-4"></i>
                </div>
                <h6 class="text-muted mb-1 small">เปลี่ยนวิชาหลัก</h6>
                <button class="btn btn-link p-0 text-warning fw-bold text-decoration-none" id="changeMainSubjectBtn" style="display: none;">คลิกเพื่อเปลี่ยน</button>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="stat-card-luxe">
                <div class="stat-icon bg-label-info">
                    <i class="bi bi-filter-circle fs-4"></i>
                </div>
                <h6 class="text-muted mb-1 small">ปีการศึกษา/ภาคเรียน</h6>
                <select class="form-select form-select-sm border-0 bg-light p-0 ps-1" id="CheckYearSendPlan">
                    <?php foreach ($CheckYearPlan as $v_SelYear) : ?>
                    <option <?= ($year . '/' . $term == $v_SelYear->seplan_year . '/' . $v_SelYear->seplan_term) ? "selected" : "" ?> value="<?= esc($v_SelYear->seplan_year.'/'.$v_SelYear->seplan_term) ?>">
                        <?= esc($v_SelYear->seplan_term.'/'.$v_SelYear->seplan_year) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <?php
    $groupedPlans = [];
    foreach ($plan as $p) {
        $groupedPlans[$p->seplan_coursecode][] = $p;
    }
    ?>

    <!-- Subject List -->
    <div class="row" id="subject-cards-container"> 
        <?php foreach ($planNew as $v_planNew) : ?>
            <div class="col-12" data-course-code="<?= esc($v_planNew->seplan_coursecode) ?>" data-is-main-subject="<?= esc($v_planNew->seplan_is_main_subject ?? 0) ?>">
                <div class="subject-card-luxe card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                        <div>
                            <span class="text-primary fw-bold me-2"><?= esc($v_planNew->seplan_coursecode) ?></span>
                            <h5 class="d-inline-block mb-0 fw-bold"><?= esc($v_planNew->seplan_namesubject) ?></h5>
                            <span class="text-muted small ms-2">ชั้น ม.<?= esc($v_planNew->seplan_gradelevel) ?> | <?= esc($v_planNew->seplan_typesubject) ?></span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table plan-table">
                            <thead>
                                <tr>
                                    <th>ประเภทเอกสาร</th>
                                    <th>สถานะการส่ง</th>
                                    <th>การตรวจสอบ</th>
                                    <th class="text-end">เครื่องมือ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $subjectsPlans = $groupedPlans[$v_planNew->seplan_coursecode] ?? [];
                                $isMain = ($v_planNew->seplan_is_main_subject ?? 0) == 1;
                                foreach ($subjectsPlans as $v_plan) : 
                                    $planTypeName = $v_plan->type_name ?? $v_plan->seplan_typeplan;
                                ?>
                                    <?php if (in_array($planTypeName, $all_plan_types)) : ?>
                                    <tr data-typeplan="<?= esc($planTypeName) ?>"
                                        style="<?php
                                            if ($isMain) {
                                                echo '';
                                            } else {
                                                echo ($planTypeName === 'โครงการสอน') ? '' : 'display: none !important;';
                                            }
                                        ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                                <span class="fw-bold"><?= esc($planTypeName) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($v_plan && !empty($v_plan->seplan_file)) : ?>
                                                <span class="badge bg-label-success rounded-pill px-3">
                                                    <span class="badge-dot bg-success"></span>ส่งแล้ว
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-label-secondary rounded-pill px-3 opacity-50">
                                                    <span class="badge-dot bg-secondary"></span>ยังไม่ส่ง
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <!-- Inspector 1 -->
                                                <?php
                                                    $s1 = trim($v_plan->seplan_status1);
                                                    $b1 = 'bg-label-warning'; $i1 = 'bi-clock';
                                                    if($s1 == 'ผ่าน') { $b1 = 'bg-label-success'; $i1 = 'bi-patch-check'; }
                                                    elseif($s1 == 'ไม่ผ่าน') { $b1 = 'bg-label-danger'; $i1 = 'bi-x-circle'; }
                                                ?>
                                                <span class="badge <?= $b1 ?> d-flex align-items-center" title="หน.กลุ่มสาระฯ: <?= $s1 ?: 'รอตรวจ' ?>">
                                                    <i class="bi <?= $i1 ?> me-1"></i> หน.กลุ่มฯ
                                                </span>

                                                <!-- Inspector 2 -->
                                                <?php
                                                    $s2 = trim($v_plan->seplan_status2);
                                                    $b2 = 'bg-label-warning'; $i2 = 'bi-clock';
                                                    if($s2 == 'ผ่าน') { $b2 = 'bg-label-success'; $i2 = 'bi-patch-check'; }
                                                    elseif($s2 == 'ไม่ผ่าน') { $b2 = 'bg-label-danger'; $i2 = 'bi-x-circle'; }
                                                ?>
                                                <span class="badge <?= $b2 ?> d-flex align-items-center" title="หน.หลักสูตรฯ: <?= $s2 ?: 'รอตรวจ' ?>">
                                                    <i class="bi <?= $i2 ?> me-1"></i> หน.หลักสูตร
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group shadow-sm">
                                                <?php if ($v_plan && $v_plan->seplan_file) : ?>
                                                    <?php
                                                    $file_ext = strtolower(pathinfo($v_plan->seplan_file, PATHINFO_EXTENSION));
                                                    $file_icon = 'bi-file-earmark';
                                                    if ($file_ext == 'pdf') $file_icon = 'bi-file-earmark-pdf-fill text-danger';
                                                    elseif (in_array($file_ext, ['doc', 'docx'])) $file_icon = 'bi-file-earmark-word-fill text-primary';
                                                    ?>
                                                    <a target="_blank" href="<?= env('upload.server.baseurl') . $v_plan->seplan_year . '/' . $v_plan->seplan_term . '/' . rawurlencode($v_plan->seplan_namesubject) . '/' . rawurlencode($v_plan->seplan_file) ?>" 
                                                       class="btn btn-sm btn-outline-secondary" title="ดูไฟล์">
                                                        <i class="bi <?= esc($file_icon) ?>"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if($is_system_on): ?>
                                                    <button class="btn btn-sm <?= ($v_plan && $v_plan->seplan_file) ? 'btn-label-warning' : 'btn-primary' ?> Model_update" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#ModalUpdatePlan"
                                                        data-seplan-id="<?= esc($v_plan->seplan_ID ?? '') ?>"
                                                        data-seplan-coursecode="<?= esc($v_planNew->seplan_coursecode) ?>"
                                                        data-seplan-typeplan="<?= esc($planTypeName) ?>"
                                                        data-seplan-sendcomment="<?= esc($v_plan->seplan_sendcomment ?? '') ?>">
                                                        <i class="bi <?= $v_plan && $v_plan->seplan_file ? 'bi-pencil-square' : 'bi-cloud-upload' ?> me-1"></i> 
                                                        <?= $v_plan && $v_plan->seplan_file ? 'แก้ไขไฟล์' : 'อัปโหลด' ?>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if (!$isMain): ?>
                    <div class="card-footer bg-label-secondary border-0 py-2 px-4">
                        
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if (empty($planNew)): ?>
            <div class="col-12">
                <div class="card luxe-card p-5 text-center">
                    <div class="mb-4">
                        <i class="bi bi-journal-x display-1 text-muted opacity-25"></i>
                    </div>
                    <h3 class="fw-bold">ยังไม่ได้ลงทะเบียนรายวิชาสอน</h3>
                    <p class="text-muted mb-4">เริ่มต้นด้วยการลงทะเบียนรายวิชาที่คุณรับผิดชอบในภาคเรียนนี้ เพื่อทำการส่งแผนการสอน</p>
                    <div class="d-flex justify-content-center">
                        <a href="<?= base_url('curriculum/send-plan') ?>" class="btn btn-primary btn-lg rounded-pill px-5 shadow">
                            <i class="bi bi-journal-plus me-2"></i> ลงทะเบียนวิชาสอนแรกของคุณ
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- Modal Update Plan -->
<div class="modal fade" id="ModalUpdatePlan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="ModalUpdatePlanLabel">
                    <i class="bi bi-cloud-arrow-up text-primary me-2"></i>อัปโหลดไฟล์แผนการสอน
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php if($is_system_on): ?>
            <form class="update_seplan">
                <div class="modal-body p-4 text-start">
                    <input type="hidden" id="seplan_ID" name="seplan_ID">
                    <input type="hidden" id="seplan_typeplan" name="seplan_typeplan">
                    <input type="hidden" id="seplan_coursecode" name="seplan_coursecode">
                    <input type="hidden" id="seplan_year" name="seplan_year" value="<?= esc($onOffSetting->seplanset_year ?? '') ?>">
                    <input type="hidden" id="seplan_term" name="seplan_term" value="<?= esc($onOffSetting->seplanset_term ?? '') ?>">

                    <div class="mb-4">
                        <label class="form-label fw-bold">เลือกไฟล์เอกสาร</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-file-earmark-arrow-up"></i></span>
                            <input class="form-control" type="file" id="seplan_file" name="seplan_file" accept=".doc,.docx,.pdf" required>
                        </div>
                        <div class="form-text small">รองรับ .doc, .docx, .pdf เท่านั้น</div>
                    </div>
                    <div class="mb-0">
                        <label for="seplan_sendcomment" class="form-label fw-bold">หมายเหตุ (ถ้ามี)</label>
                        <textarea class="form-control" id="seplan_sendcomment" name="seplan_sendcomment" rows="3" placeholder="เช่น ส่งแผนครบแล้ว หรือ ส่งรายละเอียดเพิ่มเติม"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 text-start">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary px-4 shadow">
                        <i class="bi bi-save-fill me-2"></i> เริ่มต้นอัปโหลด
                    </button>
                </div>
            </form>
            <?php else: ?>
            <div class="modal-body p-5 text-center">
                <i class="bi bi-lock-fill display-1 text-danger opacity-25 mb-3"></i>
                <h4 class="fw-bold text-danger">ระบบปิดรับส่งงาน</h4>
                <p class="text-muted">ขณะนี้ระบบปิดรับส่งแผนการสอนแล้ว หากมีกรณีจำเป็นกรุณาติดต่อหัวหน้างานหลักสูตร</p>
                <button type="button" class="btn btn-secondary mt-3 px-4" data-bs-dismiss="modal">รับทราบ</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal for Selecting Main Subject -->
<div class="modal fade" id="selectMainSubjectModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white p-4">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-star-fill me-2"></i>เลือกวิชาหลักของคุณ</h5>
            </div>
            <div class="modal-body p-4 p-md-5">
                <p class="mb-4 lead">เลือกวิชาที่คุณต้องการใช้เป็น <strong>"วิชาหลัก"</strong> สำหรับการนำส่งเอกสารที่เหลืออีก 4 รายการ</p>
                <div class="form-floating mb-3">
                    <select class="form-select border-primary" id="mainSubjectSelector">
                        <option selected disabled value="">-- กรุณาเลือกวิชา --</option>
                        <?php foreach ($planNew as $v_planNew) : ?>
                            <option value="<?= esc($v_planNew->seplan_coursecode) ?>">
                                <?= esc($v_planNew->seplan_coursecode) ?> - <?= esc($v_planNew->seplan_namesubject) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="mainSubjectSelector">รายวิชาที่คุณสอน</label>
                </div>
                <div class="alert alert-label-primary small border-0">
                    <i class="bi bi-info-circle me-1"></i> วิชาหลักคือวิชาที่คุณต้องส่งแผนฯ ครบทุกลำดับ ส่วนวิชาอื่นๆ ส่งเพียงโครงการสอนเท่านั้น
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-primary btn-lg w-100 shadow-lg" id="confirmMainSubjectBtn">ยืนยันการตั้งค่าวิชาหลัก</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Usage Guide -->
<div class="modal fade" id="usageGuideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-light border-bottom p-4">
                <h4 class="modal-title fw-bold text-dark"><i class="bi bi-book-half text-primary me-2"></i>ขั้นตอนการใช้งานระบบจัดการแผนการสอน</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 p-md-5">
                <div class="row g-4 mb-5">
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center p-4 bg-label-primary">
                            <div class="fs-1 text-primary mb-3">1</div>
                            <h6 class="fw-bold">เลือกวิชาหลัก</h6>
                            <p class="small mb-0">กำหนดรายวิชาหลักสำรับการส่งแผนฉบับเต็ม</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center p-4 bg-label-info">
                            <div class="fs-1 text-info mb-3">2</div>
                            <h6 class="fw-bold">เตรียมเอกสาร</h6>
                            <p class="small mb-0">รวบรวมไฟล์แยกตามประเภท (.PDF แนะนำ)</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center p-4 bg-label-warning">
                            <div class="fs-1 text-warning mb-3">3</div>
                            <h6 class="fw-bold">อัปโหลดงาน</h6>
                            <p class="small mb-0">คลิกส่งงานในแต่ละหัวข้อที่กำหนด</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center p-4 bg-label-success">
                            <div class="fs-1 text-success mb-3">4</div>
                            <h6 class="fw-bold">รอผลตรวจสอบ</h6>
                            <p class="small mb-0">ติดตามสถานะการตรวจจากผู้เกี่ยวข้อง</p>
                        </div>
                    </div>
                </div>

                <div class="card bg-light border-0">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-primary"><i class="bi bi-lightbulb-fill me-2"></i>ข้อควรระวัง</h6>
                        <ul class="mb-0 small">
                            <li>การส่งแผนต้องส่งภายในกำหนดเวลาที่แสดงบนหน้าแรกเท่านั้น</li>
                            <li>หากวิชาหลักมีการเปลี่ยนแปลง แผนงานวิจัยที่เคยส่งในวิชาเดิมจะไม่ย้ายมาอัตโนมัติ</li>
                            <li>ไฟล์ .PDF จะแสดงผลได้ดีที่สุดบนทุกหน้าจอและสะดวกต่อผู้ตรวจ</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top p-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">ตกลง เข้าใจแล้ว</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Main Subject Selection Logic ---
    const currentYear = '<?= esc($year) ?>';
    const currentTerm = '<?= esc($term) ?>';
    const currentPersonId = '<?= esc($person_id) ?>';
    const selectModalEl = document.getElementById('selectMainSubjectModal');
    const selectModal = new bootstrap.Modal(selectModalEl);
    const mainSubjectSelector = document.getElementById('mainSubjectSelector');
    const confirmBtn = document.getElementById('confirmMainSubjectBtn');
    const changeBtn = document.getElementById('changeMainSubjectBtn');

    function updateUI(mainSubjectCode) {
        if (!mainSubjectCode) return;
        mainSubjectSelector.value = mainSubjectCode;
        
        const container = document.getElementById('subject-cards-container');
        const cards = document.querySelectorAll('[data-course-code]');
        let mainCard = null;

        cards.forEach(card => {
            const code = card.dataset.courseCode;
            const items = card.querySelectorAll('[data-typeplan]');
            const header = card.querySelector('.card-header');
            const isMain = (code === mainSubjectCode);

            if (isMain) {
                mainCard = card;
                header.classList.add('main-subject-header');
                const title = header.querySelector('h5');
                if(!title.querySelector('.main-subject-badge')) {
                    title.insertAdjacentHTML('afterend', ' <span class="badge rounded-pill main-subject-badge ms-2">วิชาหลัก</span>');
                }
                items.forEach(item => item.style.display = '');
            } else {
                header.classList.remove('main-subject-header');
                const badge = header.querySelector('.main-subject-badge');
                if(badge) badge.remove();
                items.forEach(item => {
                    item.style.display = (item.dataset.typeplan === 'โครงการสอน') ? '' : 'none';
                });
            }
        });

        if (mainCard && container) container.prepend(mainCard);
        changeBtn.style.display = 'inline-block';
    }

    function initialize() {
        let mainCode = null;
        const cards = document.querySelectorAll('[data-course-code]');
        
        cards.forEach(card => {
            if (card.dataset.isMainSubject === '1') mainCode = card.dataset.courseCode;
        });

        // Only show selection modal if subjects exist but no main subject is chosen
        if (!mainCode && cards.length > 0) {
            selectModal.show();
        } else if (mainCode) {
            updateUI(mainCode);
        }
    }

    confirmBtn.addEventListener('click', function() {
        const selectedCode = mainSubjectSelector.value;
        if (selectedCode) {
            fetch('<?= site_url('curriculum/set-main-subject') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ courseCode: selectedCode, year: currentYear, term: currentTerm, person_id: currentPersonId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'ตั้งค่าสำเร็จ', text: data.message, timer: 1500 }).then(() => location.reload());
                } else {
                    Swal.fire('ผิดพลาด', data.message, 'error');
                }
            });
            selectModal.hide();
        } else {
            Swal.fire('คำแนะนำ', 'กรุณาเลือกวิชาหลักสำหรับภาคเรียนนี้', 'info');
        }
    });

    changeBtn.addEventListener('click', () => selectModal.show());
    initialize();

    // Filters
    document.getElementById('CheckYearSendPlan').addEventListener('change', function() {
        window.location.href = `<?= site_url('curriculum/') ?>${this.value}`;
    });

    // Update Modal
    const modalUpdatePlan = new bootstrap.Modal(document.getElementById('ModalUpdatePlan'));
    document.querySelectorAll('.Model_update').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('seplan_ID').value = this.dataset.seplanId;
            document.getElementById('seplan_typeplan').value = this.dataset.seplanTypeplan;
            document.getElementById('seplan_coursecode').value = this.dataset.seplanCoursecode;
            document.getElementById('seplan_sendcomment').value = this.dataset.seplanSendcomment;
            modalUpdatePlan.show();
        });
    });

    // Submission
    const updateForm = document.querySelector('.update_seplan');
    if(updateForm) {
        updateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> กำลังประมวลผล...';

            fetch('<?= site_url('curriculum/update-plan') ?>', { method: 'POST', body: new FormData(this) })
            .then(r => r.json())
            .then(data => {
                modalUpdatePlan.hide();
                if (data.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'อัปโหลดสำเร็จ', text: data.message, timer: 1500 }).then(() => location.reload());
                } else {
                    Swal.fire('ผิดพลาด', data.message, 'error');
                }
            })
            .catch(() => Swal.fire('ผิดพลาด', 'เกิดเหตุขัดข้องทางเทคนิค', 'error'))
            .finally(() => { btn.disabled = false; btn.innerHTML = originalHtml; });
        });
    }
});
</script>
<?= $this->endSection() ?>
