<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
บันทึกผลการเรียน (ปกติ)
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4 score-dashboard">
    <!-- Hero Section -->
    <div class="col-12">
        <div class="card border-0 shadow-none bg-label-primary mb-4 score-hero">
            <div class="card-body d-flex align-items-center p-4">
                <div class="avatar avatar-lg bg-primary rounded me-3">
                    <i class="bi bi-journal-check fs-2 text-white"></i>
                </div>
                <div class="score-hero-content">
                    <h4 class="mb-1 text-primary">ระบบบันทึกผลการเรียน (ภาคเรียนปกติ)</h4>
                    <p class="mb-0 opacity-75">จัดการข้อมูลคะแนนและเกรดของนักเรียนในรายวิชาที่รับผิดชอบ</p>
                    <?php
                        $activeTermParts = explode('/', (string) ($current_school_year ?? ''), 2);
                        $activeTermLabel = count($activeTermParts) === 2 ? trim($activeTermParts[0]) : '';
                        $activeAcademicYear = count($activeTermParts) === 2 ? trim($activeTermParts[1]) : trim($activeTermParts[0]);
                    ?>
                    <div class="score-active-period mt-3" aria-label="ช่วงปีและภาคเรียนที่ระบบตั้งค่า">
                        <span class="score-current-label">ช่วงปัจจุบันที่ระบบตั้งค่า</span>
                        <span class="score-period-chip"><i class="bi bi-calendar-check me-2"></i><?= $activeTermLabel !== '' ? 'ภาคเรียน ' . esc($activeTermLabel) : 'ภาคเรียนปัจจุบัน' ?> <span class="mx-1">/</span> ปีการศึกษา <?= $activeAcademicYear !== '' ? esc($activeAcademicYear) : '-' ?></span>
                    </div>
                </div>
                <div class="score-hero-actions ms-auto">
                    <form method="get" action="<?= current_url() ?>" class="score-year-filter">
                        <label for="school-year-select" class="form-label mb-0"><i class="bi bi-search me-1"></i>ดูคะแนนปี/เทอม</label>
                        <select name="score_period" id="school-year-select" class="form-select form-select-sm" aria-label="เลือกปีการศึกษาและภาคเรียน" onchange="this.form.submit()" <?= count($available_school_years ?? []) < 2 ? 'disabled' : '' ?>>
                            <?php if (empty($available_school_years) && !empty($current_school_year)) : ?>
                                <?php $periodParts = explode('/', $current_school_year, 2); ?>
                                <option value="<?= esc($current_school_year) ?>" selected><?= count($periodParts) === 2 ? 'ภาคเรียน ' . esc($periodParts[0]) . ' / ปีการศึกษา ' . esc($periodParts[1]) : esc($current_school_year) ?></option>
                            <?php endif; ?>
                            <?php foreach (($available_school_years ?? []) as $schoolYear) : ?>
                                <?php $periodParts = explode('/', $schoolYear, 2); ?>
                                <option value="<?= esc($schoolYear) ?>" <?= ($schoolYear === ($selected_school_year ?? '')) ? 'selected' : '' ?>><?= count($periodParts) === 2 ? 'ภาคเรียน ' . esc($periodParts[0]) . ' / ปีการศึกษา ' . esc($periodParts[1]) : esc($schoolYear) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <?php if (empty($onoff) || $onoff[0]->onoff_status == "off") : ?>
                        <span class="badge bg-danger p-2 px-3">
                            <i class="bi bi-clock-history me-1"></i> ระบบปิดการบันทึกคะแนน
                        </span>
                    <?php else : ?>
                        <span class="badge bg-success p-2 px-3 animate__animated animate__pulse animate__infinite">
                            <i class="bi bi-check-circle me-1"></i> ระบบเปิดการบันทึกคะแนน
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100 score-stat score-stat-subjects">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bi bi-book"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0"><?= count($check_subject) ?></h4>
                </div>
                <p class="mb-1">จำนวนวิชาที่สอน</p>
                <p class="mb-0">
                    <small class="text-muted">วิชาทั้งหมดในปีการศึกษาปัจจุบัน</small>
                </p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100 score-stat score-stat-units">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-info"><i class="bi bi-award"></i></span>
                    </div>
                    <?php 
                        $totalUnits = 0;
                        foreach($check_subject as $s) $totalUnits += $s->SubjectUnit;
                    ?>
                    <h4 class="ms-1 mb-0"><?= number_format($totalUnits, 1) ?></h4>
                </div>
                <p class="mb-1">หน่วยกิตสะสม</p>
                <p class="mb-0">
                    <small class="text-muted">รวมหน่วยกิตทุกรายวิชา</small>
                </p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100 score-stat score-stat-hours">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-warning"><i class="bi bi-clock"></i></span>
                    </div>
                     <?php 
                        $totalHours = 0;
                        foreach($check_subject as $s) $totalHours += $s->SubjectHour;
                    ?>
                    <h4 class="ms-1 mb-0"><?= $totalHours ?></h4>
                </div>
                <p class="mb-1">จำนวนชั่วโมงรวม</p>
                <p class="mb-0">
                    <small class="text-muted">รวมชั่วโมงเรียนต่อเทอม</small>
                </p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100 score-stat score-stat-status <?= (empty($onoff) || $onoff[0]->onoff_status == "off") ? 'bg-label-danger' : 'bg-label-success' ?> border-0">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded <?= (empty($onoff) || $onoff[0]->onoff_status == "off") ? 'bg-danger' : 'bg-success' ?> text-white font-weight-bold">
                            <i class="bi <?= (empty($onoff) || $onoff[0]->onoff_status == "off") ? 'bi-lock-fill' : 'bi-unlock-fill' ?>"></i>
                        </span>
                    </div>
                    <h5 class="ms-1 mb-0"><?= (empty($onoff) || $onoff[0]->onoff_status == "off") ? 'ปิดระบบ' : 'เปิดระบบ' ?></h5>
                </div>
                <p class="mb-1">สถานะการบันทึกข้อมูล</p>
                <p class="mb-0">
                    <small class="text-muted opacity-75">จัดการโดยฝ่ายวิชาการ</small>
                </p>
            </div>
        </div>
    </div>

    <!-- Subject List -->
    <div class="col-12">
        <div class="card shadow-sm border-0 score-table-card">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom mb-3 pb-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm bg-label-primary me-2 rounded">
                        <i class="bi bi-list-stars"></i>
                    </div>
                    <h5 class="card-title mb-0">รายการวิชาที่รับผิดชอบ</h5>
                </div>
                <div class="card-actions">
                    <?php if (!empty($can_manage_historical_access)) : ?>
                        <a href="<?= site_url('assessment/historical-score-access') ?>" class="btn btn-sm btn-outline-primary text-nowrap">
                            <i class="bi bi-shield-lock me-1"></i>จัดการสิทธิ์ปีเก่า
                        </a>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise"></i> รีเฟรช
                    </button>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle datatable-init score-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" style="width: 80px;">ลำดับ</th>
                                <th style="width: 120px;">ปีการศึกษา</th>
                                <th>รหัสวิชา / ชื่อวิชา</th>
                                <th class="text-center">ชั้นที่สอน</th>
                                <th class="text-center">หน่วยกิต</th>
                                <th class="text-center">ชั่วโมง</th>
                                <th class="text-center" style="width: 300px;">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($check_subject)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center text-muted">
                                            <i class="bi bi-folder2-open display-4 mb-2"></i>
                                            <p>ไม่มีรายวิชาที่สอนในปีการศึกษานี้</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($check_subject as $key => $v_check_subject) : ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?= $key + 1 ?></td>
                                        <td>
                                            <span class="badge bg-label-secondary border-0"><?= esc($v_check_subject->RegisterYear) ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="badge bg-label-primary p-2 me-3 rounded border-0">
                                                    <span class="fw-bold"><?= esc($v_check_subject->SubjectCode) ?></span>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold text-dark fs-6"><?= esc($v_check_subject->SubjectName) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-primary fw-medium"><?= esc($v_check_subject->RegisterClasses) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-info"><?= $v_check_subject->SubjectUnit ?> นก.</span>
                                        </td>
                                        <td class="text-center text-muted">
                                            <?= $v_check_subject->SubjectHour ?> ชม./สัปดาห์
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <a href="<?= base_url('assessment/save-score-add/' . $v_check_subject->RegisterYear . '/' . $v_check_subject->SubjectID . '/all') ?>"
                                                   class="btn btn-sm px-3 shadow-sm hover-elevate <?= !empty($selected_is_read_only) ? 'btn-outline-secondary' : 'btn-primary' ?>">
                                                    <i class="bi <?= !empty($selected_is_read_only) ? 'bi-eye' : 'bi-pencil-square' ?> me-1"></i>
                                                    <?= !empty($selected_is_read_only) ? 'ดูเกรด' : 'บันทึกเกรด' ?>
                                                </a>
                                                
                                                <?php if ($v_check_subject->score_settings_count == 0) : ?>
                                                    <button type="button" class="btn btn-secondary btn-sm px-3 disabled" 
                                                            data-bs-toggle="tooltip" title="กรุณาตั้งค่าคะแนนเก็บก่อนออกรายงาน">
                                                        <i class="bi bi-printer me-1"></i> รายงาน
                                                    </button>
                                                <?php else : ?>
                                                    <button type="button" class="btn btn-info btn-sm px-3 shadow-sm hover-elevate btn-check-report" 
                                                            data-report-year="<?= esc($v_check_subject->RegisterYear) ?>" 
                                                            data-report-subject="<?= esc($v_check_subject->SubjectID) ?>">
                                                        <i class="bi bi-printer me-1"></i> รายงาน
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Print -->
<div class="modal fade" id="Modalprint" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info py-3">
                <h5 class="modal-title text-white"><i class="bi bi-printer me-2"></i> ออกรายงานผลการเรียน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('assessment/report-learn-normal'); ?>" method="post" target="_blank" class="no-loader">
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl bg-label-info rounded-circle mb-3 mx-auto">
                            <i class="bi bi-file-pdf fs-1"></i>
                        </div>
                        <h6>โปรดเลือกห้องเรียนที่คุณต้องการออกรายงาน</h6>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <select name="select_print" id="select_print" class="form-select border-primary">
                            <option value="all">ทุกห้องเรียนที่สอน</option>
                        </select>
                        <label for="select_print">เลือกห้องเรียน</label>
                    </div>
                    
                    <div class="alert alert-info border-0 d-flex mb-0">
                        <i class="bi bi-info-circle me-3 fs-3"></i>
                        <div>
                            <small class="d-block fw-bold">หมายเหตุ:</small>
                            <small>รายงานจะถูกเปิดในแท็บใหม่ในรูปแบบไฟล์ PDF พร้อมสำหรับการพิมพ์หรือบันทึกไฟล์</small>
                        </div>
                    </div>
                    
                    <input type="hidden" name="report_RegisterYear" id="report_RegisterYear">
                    <input type="hidden" name="report_SubjectID" id="report_SubjectID">
                </div>
                <div class="modal-footer border-top-0 d-flex justify-content-between p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-info px-5 shadow-sm" id="btn_submit_print">
                        <i class="bi bi-printer me-2"></i> พิมพ์รายงาน
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .score-dashboard {
        --score-ink: #172b4d;
        --score-muted: #66758a;
        --score-line: #e8edf4;
        color: var(--score-ink);
    }
    .score-dashboard > .col-12:first-child { margin-bottom: .25rem; }
    .score-hero {
        overflow: hidden;
        border-radius: 1.15rem;
        background: linear-gradient(115deg, #5b21b6 0%, #7c3aed 52%, #9333ea 100%) !important;
        box-shadow: 0 12px 28px rgba(91, 33, 182, .24);
    }
    .score-hero .card-body { min-height: 116px; gap: 1rem; flex-wrap: wrap; }
    .score-hero-content { flex: 1 1 420px; min-width: 0; }
    .score-hero .avatar { background: rgba(255, 255, 255, .18) !important; box-shadow: 0 8px 20px rgba(35, 10, 79, .22); }
    .score-hero h4 { color: #fff !important; font-weight: 750; letter-spacing: -.02em; }
    .score-hero p { color: rgba(255, 255, 255, .82); }
    .score-active-period { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem .75rem; }
    .score-current-label { color: rgba(255,255,255,.76); font-size: .78rem; font-weight: 600; }
    .score-period-chip { display: inline-flex; align-items: center; padding: .45rem .8rem; border: 1px solid rgba(255,255,255,.42); border-radius: 999px; background: rgba(255,255,255,.2); color: #fff; font-size: .86rem; font-weight: 750; }
    .score-hero .badge { border-radius: 999px; font-weight: 650; }
    .score-hero .badge.bg-success { color: #146c43 !important; background: #d1fae5 !important; }
    .score-hero .badge.bg-danger { color: #a61b29 !important; background: #ffe1e5 !important; }
    .score-hero-actions { display: flex; flex: 0 1 430px; flex-direction: column; align-items: stretch; gap: .45rem; min-width: 0; padding: .65rem .8rem; border: 1px solid rgba(255,255,255,.3); border-radius: .8rem; background: rgba(42, 12, 91, .2); }
    .score-hero .score-year-filter { display: grid; grid-template-columns: auto minmax(180px, 1fr); align-items: center; gap: .5rem; width: 100%; }
    .score-hero .score-year-filter .form-label { color: #fff; font-size: .84rem; font-weight: 750; }
    .score-hero .score-year-filter .form-select { min-width: 0; min-height: 38px; color: #253047 !important; -webkit-text-fill-color: #253047; background-color: #fff !important; border: 1px solid rgba(255,255,255,.7); box-shadow: 0 4px 12px rgba(35, 10, 79, .16); }
    .score-hero .score-year-filter .form-select:disabled { color: #253047 !important; -webkit-text-fill-color: #253047; background-color: #f3f4f6 !important; opacity: 1; }
    .score-stat {
        border: 1px solid var(--score-line) !important;
        border-radius: 1rem;
        box-shadow: 0 5px 18px rgba(27, 48, 79, .045);
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .score-stat:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(27, 48, 79, .09); }
    .score-stat .card-body { padding: 1.25rem; }
    .score-stat h4, .score-stat h5 { color: var(--score-ink); font-weight: 750; letter-spacing: -.025em; }
    .score-stat p.mb-1 { color: #344761; font-weight: 650; }
    .score-stat small { color: var(--score-muted) !important; }
    .score-stat-status { background: linear-gradient(145deg, #eefaf4, #f8fcfa) !important; }
    .score-stat-status.bg-label-danger { background: linear-gradient(145deg, #fff1f0, #fffafa) !important; }
    .score-table-card { overflow: hidden; border-radius: 1rem; box-shadow: 0 8px 28px rgba(27, 48, 79, .06) !important; }
    .score-table-card > .card-header { padding: 1.1rem 1.35rem; border-color: var(--score-line) !important; }
    .score-table-card .card-title { color: var(--score-ink); font-weight: 700; }
    .score-year-filter .form-select { min-width: 220px; border-color: #dbe3ef; }
    .score-year-filter .form-label { color: #52647d; font-size: .85rem; font-weight: 650; }
    .score-table-card > .card-body { padding: .25rem 1.25rem 1.15rem; }
    .score-table { margin-bottom: 0; }
    .score-table thead th {
        padding: .85rem .75rem;
        color: #687991;
        background: #f7f9fc;
        border-bottom: 1px solid var(--score-line);
        font-size: .72rem;
        letter-spacing: .055em;
        white-space: nowrap;
    }
    .score-table tbody td { padding: .9rem .75rem; border-color: #edf1f6; }
    .score-table tbody tr { transition: background-color .15s ease; }
    .score-table tbody tr:hover { background-color: #f8faff; }
    .score-table .btn { min-height: 36px; border-radius: .6rem; font-weight: 600; }
    .score-table .btn-primary { box-shadow: 0 3px 9px rgba(53, 104, 212, .16) !important; }
    .score-table .badge { border-radius: .5rem; }
    .score-table .datatable-empty, .score-table td[colspan] { padding: 3rem 1rem !important; }
    @media (max-width: 767.98px) {
        .score-dashboard { --bs-gutter-y: 1rem; }
        .score-hero .card-body { align-items: flex-start !important; padding: 1.25rem !important; }
        .score-hero-content { flex-basis: calc(100% - 3.75rem); }
        .score-hero h4 { font-size: 1.05rem; line-height: 1.5; }
        .score-period-chip { font-size: .76rem; padding: .38rem .65rem; }
        .score-hero .avatar { width: 2.75rem; height: 2.75rem; flex: 0 0 auto; }
        .score-hero-actions { flex-basis: 100%; width: 100%; margin-left: 0 !important; }
        .score-hero .score-year-filter { grid-template-columns: 1fr; gap: .45rem; }
        .score-hero .score-year-filter .form-select { width: 100%; }
        .score-stat .card-body { padding: 1rem; }
        .score-table-card > .card-header { padding: .9rem 1rem; }
        .score-table-card > .card-header { align-items: stretch !important; gap: .75rem; }
        .score-table-card .card-actions { display: flex; flex-wrap: wrap; gap: .5rem; width: 100%; }
        .score-table-card .score-year-filter { flex: 1 1 100%; }
        .score-table-card .score-year-filter .form-select { min-width: 0; flex: 1; }
        .score-table-card > .card-body { padding: 0 .75rem .75rem; }
        .score-table td:last-child { min-width: 230px; }
        .score-table td:last-child .d-flex { flex-wrap: wrap; gap: .4rem !important; }
    }
    .hover-elevate:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease;
    }
    .card {
        transition: all 0.3s ease;
    }
    .datatable-init thead th {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }
    .badge {
        font-weight: 600;
    }
    .bg-label-info { background-color: #e0f7fa !important; color: #00bcd4 !important; }
    .bg-label-warning { background-color: #fff8e1 !important; color: #ffc107 !important; }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        const modalEl = document.getElementById('Modalprint');
        const printModal = bootstrap.Modal.getOrCreateInstance(modalEl);

        function cleanupModalPrintBackdrop() {
            if (!$('#Modalprint').hasClass('show')) {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css({
                    'overflow': '',
                    'padding-right': ''
                });
            }
        }

        $(document).on('click', '.btn-check-report, #chcek_report', function(e) {
            e.preventDefault();

            var year = $(this).attr('data-report-year') || $(this).attr('report-yaer');
            var subject = $(this).attr('data-report-subject') || $(this).attr('report-subject');
            const $btn = $(this);
            const originalHtml = $btn.html();

            $("#report_RegisterYear").val(year);
            $("#report_SubjectID").val(subject);

            // Pre-fill select with loading indicator
            var selectPrint = $('#select_print');
            selectPrint.html('<option value="" disabled selected>กำลังโหลดข้อมูลห้องเรียน...</option>');

            // Open modal immediately
            printModal.show();

            // Show loading state on button
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');

            $.ajax({
                url: "<?= site_url('assessment/save-score/checkroom-report') ?>",
                type: 'POST',
                data: {
                    report_yaer: year,
                    report_subject: subject
                },
                dataType: 'json',
                success: function(data) {
                    selectPrint.empty();
                    selectPrint.append('<option value="all">ทุกห้องเรียนที่สอน</option>');
                    if (Array.isArray(data)) {
                        $.each(data, function(key, val) {
                            if (val && val.StudentClass) {
                                selectPrint.append('<option value="' + val.StudentClass + '">' + val.StudentClass + '</option>');
                            }
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    selectPrint.html('<option value="all">ทุกห้องเรียนที่สอน</option>');
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถดึงข้อมูลห้องเรียนได้: ' + textStatus,
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // Close modal when form is submitted for printing (PDF in target="_blank")
        $('#Modalprint form').on('submit', function() {
            printModal.hide();
            // Background tab safety timeout (since target="_blank" opens new tab and suspends CSS transitionend)
            setTimeout(cleanupModalPrintBackdrop, 350);
            setTimeout(cleanupModalPrintBackdrop, 1000);
        });

        // Ensure backdrop is destroyed whenever modal is completely hidden
        modalEl.addEventListener('hidden.bs.modal', function() {
            cleanupModalPrintBackdrop();
        });
    });
</script>
<?= $this->endSection() ?>
