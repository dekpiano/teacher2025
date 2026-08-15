<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dropdown-hover:hover > .dropdown-menu {
        display: block;
        margin-top: 0;
    }
    .pa-hero-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: 1px solid rgba(105, 108, 255, 0.15) !important;
        border-radius: 12px;
    }
    .pa-card-action {
        border-radius: 12px;
        border: 1px solid #e7e7e8 !important;
        transition: all 0.2s ease-in-out;
    }
    .pa-card-action:hover {
        box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    }
    .pa-card-active {
        border: 1px solid rgba(113, 221, 55, 0.4) !important;
        background: linear-gradient(180deg, rgba(113, 221, 55, 0.02) 0%, #ffffff 100%);
    }
    .pa-icon-box {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }
    .x-small {
        font-size: 0.78rem;
    }
    /* Drag & Drop Upload Zone Styling */
    .dropzone-box {
        border: 2px dashed #d9dee3;
        border-radius: 10px;
        padding: 20px 12px;
        text-align: center;
        background-color: #fafbfc;
        cursor: pointer;
        transition: all 0.25s ease-in-out;
        position: relative;
    }
    .dropzone-box:hover {
        border-color: #696cff;
        background-color: #f8f9ff;
    }
    .dropzone-box.dragover {
        border-color: #696cff !important;
        background-color: #ebeeff !important;
        transform: scale(1.02);
    }
    .dropzone-icon {
        font-size: 2rem;
        line-height: 1;
        margin-bottom: 6px;
        transition: transform 0.2s ease;
    }
    .dropzone-box:hover .dropzone-icon {
        transform: translateY(-3px);
    }
    .file-selected-indicator {
        display: none;
        background: #ffffff;
        border: 1px solid #e7e7e8;
        border-radius: 8px;
        padding: 8px 10px;
        margin-top: 8px;
    }
</style>

<div class="row">
    <div class="col-12 col-xxl-11 mx-auto">
        <!-- Hero Header -->
        <div class="card mb-3 pa-hero-card shadow-sm">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary rounded-3 me-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-journal-check fs-3 text-primary"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h5 class="mb-0 fw-bold text-dark">การประเมินผลการพัฒนางานตามข้อตกลง (PA)</h5>
                                <span class="badge bg-primary rounded-pill px-2 py-1 fs-7">
                                    ประจำปีงบประมาณ <?= $current_year ?>
                                </span>
                            </div>
                            <div class="text-muted small mt-1">
                                <i class="bi bi-info-circle me-1"></i> กรุณาส่งเอกสารและสื่อนำเสนอผลการพัฒนางานตามข้อตกลงให้ครบถ้วนทั้ง 3 รายการ
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-label-success px-3 py-2 fs-7 rounded-pill">
                            <i class="bi bi-check-circle-fill me-1"></i> ระบบเปิดรับเอกสาร
                        </span>
                    </div>
                </div>

                <!-- Prominent Notice Banner -->
                <div class="alert alert-warning d-flex align-items-center py-2 px-3 mt-3 mb-0 rounded-3 border-0 bg-label-warning" role="alert">
                    <i class="bi bi-shield-lock-fill me-2 fs-5 text-warning"></i>
                    <div class="small fw-semibold">
                        <span class="fw-bold text-dark">หมายเหตุสำคัญ:</span> ระบบข้อตกลงในการพัฒนางาน (PA) นี้ <strong>ใช้เฉพาะข้าราชการครูเท่านั้น</strong> (ครูอัตราจ้าง / เจ้าหน้าที่ ไม่จำเป็นต้องส่งในส่วนนี้)
                    </div>
                </div>
            </div>
        </div>

        <form id="paUploadForm" class="no-loader" action="<?= base_url('pa-agreement/upload') ?>" method="post">
            <input type="hidden" name="pa_year" value="<?= $current_year ?>">
            <input type="hidden" name="uploaded_lesson_plan_filename" id="uploaded_lesson_plan_filename" value="">
            <input type="hidden" name="uploaded_pa1_filename" id="uploaded_pa1_filename" value="">

            <div class="row g-3">
                <!-- Card 1: Presentation (PPT/Canva) -->
                <div class="col-lg-4">
                    <div class="card shadow-sm h-100 pa-card-action <?= ($agreement && !empty($agreement['pa_presentation_link'])) ? 'pa-card-active' : '' ?>">
                        <div class="card-header bg-white border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="pa-icon-box bg-label-danger me-2">
                                    <i class="bi bi-play-circle-fill fs-5 text-danger"></i>
                                </div>
                                <span class="fw-bold text-dark">1. สื่อนำเสนอผลงาน</span>
                            </div>
                            <?php if ($agreement && !empty($agreement['pa_presentation_link'])) : ?>
                                <span class="badge bg-label-success rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> ส่งลิ้งก์แล้ว</span>
                            <?php else: ?>
                                <span class="badge bg-label-secondary rounded-pill px-2 py-1">ยังไม่ส่ง</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <div class="text-muted small mb-2">
                                    รูปแบบ PPT, CANVA หรืออื่นๆ (ความยาวไม่เกิน 10 นาที)
                                </div>

                                <?php if ($agreement && !empty($agreement['pa_presentation_link'])) : ?>
                                    <div class="alert alert-outline-danger d-flex align-items-center justify-content-between p-2 mb-3 rounded-3" role="alert">
                                        <div class="d-flex align-items-center text-truncate me-2">
                                            <i class="bi bi-link-45deg fs-4 text-danger me-1"></i>
                                            <div class="text-truncate">
                                                <div class="small fw-bold text-dark text-truncate">มีลิ้งก์ในระบบแล้ว</div>
                                                <div class="x-small text-muted text-truncate"><?= esc($agreement['pa_presentation_link']) ?></div>
                                            </div>
                                        </div>
                                        <a href="<?= esc($agreement['pa_presentation_link']) ?>" target="_blank" class="btn btn-xs btn-danger text-nowrap rounded-pill px-2">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> เปิดดู
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-2">
                                    <label for="pa_presentation_link" class="form-label small fw-semibold text-secondary mb-1">
                                        วาง URL สื่อนำเสนอ (Canva, Drive, YouTube):
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                        <input type="url" class="form-control form-control-sm" id="pa_presentation_link" name="pa_presentation_link" placeholder="https://..." value="<?= $agreement['pa_presentation_link'] ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="btn-save-link" class="btn btn-primary btn-sm w-100 mt-3 shadow-xs">
                                <i class="bi bi-link-45deg me-1"></i> บันทึกลิ้งก์สื่อนำเสนอ
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Lesson Plan PDF with Drag & Drop Zone -->
                <div class="col-lg-4">
                    <div class="card shadow-sm h-100 pa-card-action <?= ($agreement && !empty($agreement['pa_file_lesson_plan'])) ? 'pa-card-active' : '' ?>">
                        <div class="card-header bg-white border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="pa-icon-box bg-label-info me-2">
                                    <i class="bi bi-journal-text fs-5 text-info"></i>
                                </div>
                                <span class="fw-bold text-dark">2. แผนการจัดการเรียนรู้</span>
                            </div>
                            <?php if ($agreement && !empty($agreement['pa_file_lesson_plan'])) : ?>
                                <span class="badge bg-label-success rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> ส่งไฟล์แล้ว</span>
                            <?php else: ?>
                                <span class="badge bg-label-secondary rounded-pill px-2 py-1">ยังไม่ส่ง</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <div class="text-muted small mb-2">
                                    แผนการจัดการเรียนรู้ที่ใช้ในประเด็นท้าทาย 1 แผน (PDF)
                                </div>

                                <?php if ($agreement && !empty($agreement['pa_file_lesson_plan'])) : ?>
                                    <div class="alert alert-outline-info d-flex align-items-center justify-content-between p-2 mb-2 rounded-3" role="alert">
                                        <div class="d-flex align-items-center text-truncate me-2">
                                            <i class="bi bi-file-earmark-pdf-fill fs-4 text-info me-2"></i>
                                            <div class="text-truncate">
                                                <div class="small fw-bold text-dark text-truncate">มีไฟล์ในระบบแล้ว</div>
                                                <div class="x-small text-muted text-truncate"><?= esc($agreement['pa_file_lesson_plan']) ?></div>
                                            </div>
                                        </div>
                                        <a href="<?= env('upload.server.baseurl.pa_agreement') . $agreement['pa_year'] . '/lesson_plan/' . $agreement['pa_file_lesson_plan'] ?>" target="_blank" class="btn btn-xs btn-info text-nowrap rounded-pill px-2">
                                            <i class="bi bi-eye me-1"></i> เปิดดู
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <!-- Dropzone for Lesson Plan -->
                                <input type="file" id="pa_file_lesson_plan" accept=".pdf" class="d-none">
                                <div class="dropzone-box" id="dropzone_lesson_plan">
                                    <i class="bi bi-cloud-arrow-up-fill dropzone-icon text-info"></i>
                                    <div class="small fw-bold text-dark">ลากและวางไฟล์แผนการสอนที่นี่</div>
                                    <div class="x-small text-muted">หรือ <span class="text-info fw-semibold">คลิกเลือกไฟล์</span> (PDF สูงสุด 20MB)</div>
                                </div>

                                <!-- Selected File Badge -->
                                <div class="file-selected-indicator" id="indicator_lesson_plan">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center text-truncate me-2">
                                            <i class="bi bi-filetype-pdf text-danger fs-5 me-2"></i>
                                            <div class="text-truncate">
                                                <div class="small fw-semibold text-truncate file-name">-</div>
                                                <div class="x-small text-muted file-size">-</div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-xs btn-icon btn-label-secondary remove-file" data-target="lesson_plan" title="ยกเลิกไฟล์นี้">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="progress mt-2 d-none" id="progress_lesson_plan" style="height: 6px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                            <button type="button" id="btn-save-lesson-plan" class="btn btn-info text-white btn-sm w-100 mt-3 shadow-xs">
                                <i class="bi bi-cloud-arrow-up me-1"></i> บันทึกไฟล์แผนการสอน
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 3: PA1 Agreement PDF with Drag & Drop Zone -->
                <div class="col-lg-4">
                    <div class="card shadow-sm h-100 pa-card-action <?= ($agreement && !empty($agreement['pa_file_pa1'])) ? 'pa-card-active' : '' ?>">
                        <div class="card-header bg-white border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="pa-icon-box bg-label-primary me-2">
                                    <i class="bi bi-file-earmark-check fs-5 text-primary"></i>
                                </div>
                                <span class="fw-bold text-dark">3. บันทึกข้อตกลง PA1</span>
                            </div>
                            <?php if ($agreement && !empty($agreement['pa_file_pa1'])) : ?>
                                <span class="badge bg-label-success rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> ส่งไฟล์แล้ว</span>
                            <?php else: ?>
                                <span class="badge bg-label-secondary rounded-pill px-2 py-1">ยังไม่ส่ง</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <div class="text-muted small mb-2">
                                    บันทึกข้อตกลงพัฒนางาน PA1 ประจำปีงบประมาณ <?= $current_year ?> (PDF)
                                </div>

                                <?php if ($agreement && !empty($agreement['pa_file_pa1'])) : ?>
                                    <div class="alert alert-outline-success d-flex align-items-center justify-content-between p-2 mb-2 rounded-3" role="alert">
                                        <div class="d-flex align-items-center text-truncate me-2">
                                            <i class="bi bi-file-earmark-check-fill fs-4 text-success me-2"></i>
                                            <div class="text-truncate">
                                                <div class="small fw-bold text-dark text-truncate">มีไฟล์ในระบบแล้ว</div>
                                                <div class="x-small text-muted text-truncate"><?= esc($agreement['pa_file_pa1']) ?></div>
                                            </div>
                                        </div>
                                        <a href="<?= env('upload.server.baseurl.pa_agreement') . $agreement['pa_year'] . '/pa1/' . $agreement['pa_file_pa1'] ?>" target="_blank" class="btn btn-xs btn-success text-nowrap rounded-pill px-2">
                                            <i class="bi bi-eye me-1"></i> เปิดดู
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <!-- Dropzone for PA1 -->
                                <input type="file" id="pa_file_pa1" accept=".pdf" class="d-none">
                                <div class="dropzone-box" id="dropzone_pa1">
                                    <i class="bi bi-cloud-arrow-up-fill dropzone-icon text-primary"></i>
                                    <div class="small fw-bold text-dark">ลากและวางไฟล์ PA1 ที่นี่</div>
                                    <div class="x-small text-muted">หรือ <span class="text-primary fw-semibold">คลิกเลือกไฟล์</span> (PDF สูงสุด 20MB)</div>
                                </div>

                                <!-- Selected File Badge -->
                                <div class="file-selected-indicator" id="indicator_pa1">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center text-truncate me-2">
                                            <i class="bi bi-filetype-pdf text-danger fs-5 me-2"></i>
                                            <div class="text-truncate">
                                                <div class="small fw-semibold text-truncate file-name">-</div>
                                                <div class="x-small text-muted file-size">-</div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-xs btn-icon btn-label-secondary remove-file" data-target="pa1" title="ยกเลิกไฟล์นี้">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="progress mt-2 d-none" id="progress_pa1" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                            <button type="button" id="btn-save-pa1" class="btn btn-primary btn-sm w-100 mt-3 shadow-xs">
                                <i class="bi bi-cloud-arrow-up me-1"></i> บันทึกไฟล์ PA1
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Table Card: History -->
        <div class="card border-0 shadow-sm mt-4 pa-card-action">
            <div class="card-header bg-white border-bottom py-3 px-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="pa-icon-box bg-label-secondary me-2">
                        <i class="bi bi-clock-history fs-5 text-secondary"></i>
                    </div>
                    <span class="fw-bold text-dark">ประวัติการส่งข้อตกลงในการพัฒนางาน (PA)</span>
                </div>
                <span class="badge bg-label-primary rounded-pill"><?= count($history) ?> รายการ</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ปีงบประมาณ</th>
                                <th>วันที่ส่งล่าสุด</th>
                                <th class="text-center">สื่อนำเสนอ</th>
                                <th class="text-center">แผนการสอน (PDF)</th>
                                <th class="text-center">ข้อตกลง PA1 (PDF)</th>
                                <th class="text-center pe-3">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($history)) : ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-1 text-light"></i>
                                        ไม่พบประวัติการส่งข้อมูล
                                    </td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($history as $row) : ?>
                                    <tr>
                                        <td class="ps-3 fw-semibold text-dark"><?= $row['pa_year'] ?></td>
                                        <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($row['pa_created_at'] . ' +543 years')) ?> น.</td>
                                        <td class="text-center">
                                            <?php if (!empty($row['pa_presentation_link'])) : ?>
                                                <a href="<?= esc($row['pa_presentation_link']) ?>" target="_blank" class="btn btn-sm btn-icon btn-label-danger rounded-pill shadow-xs" title="เปิดดูสื่อนำเสนอ">
                                                    <i class="bi bi-play-circle-fill"></i>
                                                </a>
                                            <?php else : ?>
                                                <span class="badge bg-label-secondary small pe-none">ไม่ได้ส่ง</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($row['pa_file_lesson_plan'])) : ?>
                                                <a href="<?= env('upload.server.baseurl.pa_agreement') . $row['pa_year'] . '/lesson_plan/' . $row['pa_file_lesson_plan'] ?>" target="_blank" class="btn btn-sm btn-icon btn-label-info rounded-pill shadow-xs" title="เปิดดูแผนการสอน">
                                                    <i class="bi bi-journal-text"></i>
                                                </a>
                                            <?php else : ?>
                                                <span class="badge bg-label-secondary small pe-none">ไม่ได้ส่ง</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($row['pa_file_pa1'])) : ?>
                                                <a href="<?= env('upload.server.baseurl.pa_agreement') . $row['pa_year'] . '/pa1/' . $row['pa_file_pa1'] ?>" target="_blank" class="btn btn-sm btn-icon btn-label-primary rounded-pill shadow-xs" title="เปิดดูข้อตกลง PA1">
                                                    <i class="bi bi-file-earmark-check"></i>
                                                </a>
                                            <?php else : ?>
                                                <span class="badge bg-label-secondary small pe-none">ไม่ได้ส่ง</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center pe-3">
                                            <div class="dropdown dropdown-hover d-inline-block">
                                                <button class="btn btn-sm btn-icon btn-label-secondary rounded-pill" type="button">
                                                    <i class="bi bi-trash-fill text-danger"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li><h6 class="dropdown-header">เลือกสิ่งที่ต้องการลบ</h6></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btn-delete <?= empty($row['pa_presentation_link']) ? 'disabled text-muted' : '' ?>" 
                                                           href="javascript:void(0)" 
                                                           data-id="<?= $row['pa_id'] ?>" 
                                                           data-type="presentation">
                                                            <i class="bi bi-play-circle me-2 text-danger"></i> ลบลิ้งก์นำเสนอ
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btn-delete <?= empty($row['pa_file_lesson_plan']) ? 'disabled text-muted' : '' ?>" 
                                                           href="javascript:void(0)" 
                                                           data-id="<?= $row['pa_id'] ?>" 
                                                           data-type="lesson_plan">
                                                            <i class="bi bi-journal-text me-2 text-info"></i> ลบไฟล์แผนการสอน
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btn-delete <?= empty($row['pa_file_pa1']) ? 'disabled text-muted' : '' ?>" 
                                                           href="javascript:void(0)" 
                                                           data-id="<?= $row['pa_id'] ?>" 
                                                           data-type="pa1">
                                                            <i class="bi bi-file-earmark-check me-2 text-primary"></i> ลบไฟล์ข้อตกลง PA1
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btn-delete text-danger fw-bold" 
                                                           href="javascript:void(0)" 
                                                           data-id="<?= $row['pa_id'] ?>" 
                                                           data-type="all">
                                                            <i class="bi bi-trash3-fill me-2"></i> ลบข้อมูลทั้งหมดของปีนี้
                                                        </a>
                                                    </li>
                                                </ul>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        const year = '<?= $current_year ?>';
        const CHUNK_SIZE = 1024 * 1024 * 2; // 2MB chunk

        // Modal notification on page load
        Swal.fire({
            icon: 'warning',
            title: '<span class="text-warning fw-bold fs-4">⚠️ ข้อควรทราบสำคัญ</span>',
            html: `
                <div class="text-center py-2">
                    <div class="p-3 bg-label-warning rounded-3 mb-3 border border-warning">
                        <h5 class="fw-bold text-warning mb-1">
                            <i class="bi bi-shield-lock-fill me-1"></i> ข้อตกลง PA นี้สำหรับ "ข้าราชการครู" เท่านั้น
                        </h5>
                        <div class="text-dark fw-semibold mt-2" style="font-size: 1.05rem;">
                            สำหรับตำแหน่ง <span class="badge bg-warning text-dark fs-7">ครูผู้ช่วย</span> และ <span class="badge bg-warning text-dark fs-7">ครู (คศ.1 - คศ.5)</span>
                        </div>
                    </div>

                    <div class="alert alert-secondary text-start small mb-0 py-2 border">
                        <i class="bi bi-info-circle-fill text-primary me-1"></i> 
                        <strong>ครูอัตราจ้าง / ครูจ้างสอน / เจ้าหน้าที่ธุรการ และบุคลากรอื่น ๆ:</strong><br>
                        <span class="text-muted fw-semibold">ไม่จำเป็นต้องจัดทำหรือส่งข้อมูลข้อตกลง PA ในระบบนี้</span>
                    </div>
                </div>
            `,
            confirmButtonText: '<i class="bi bi-check-circle-fill me-1"></i> รับทราบและเข้าใจแล้ว',
            confirmButtonColor: '#ffab00',
            allowOutsideClick: false,
            customClass: {
                popup: 'rounded-4 shadow-lg p-4'
            }
        });

        // Helper: Upload file in chunks to remote server
        async function uploadFileChunked(file, folderSubpath, progressSelector) {
            const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
            const teacherId = '<?= session()->get('person_id') ?>';
            const timestamp = Math.floor(Date.now() / 1000);
            const fileExt = file.name.split('.').pop() || 'pdf';
            const prefix = (folderSubpath === 'lesson_plan') ? 'Plan' : 'PA1';
            const targetFilename = `PA_${prefix}_${year}_${teacherId}_${timestamp}.${fileExt}`;
            const targetPath = `personnel/teacher/pa_agreement/${year}/${folderSubpath}`;

            $(progressSelector).removeClass('d-none');
            const progressBar = $(progressSelector).find('.progress-bar');
            progressBar.css('width', '0%').text('0%');

            let finalSavedName = targetFilename;

            for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
                const start = chunkIndex * CHUNK_SIZE;
                const end = Math.min(start + CHUNK_SIZE, file.size);
                const chunk = file.slice(start, end);

                const formData = new FormData();
                formData.append('file', chunk);
                formData.append('path', targetPath);
                formData.append('filename', targetFilename);
                formData.append('chunk', chunkIndex);
                formData.append('chunks', totalChunks);

                const response = await $.ajax({
                    url: '<?= base_url('pa-agreement/upload-chunk') ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false
                });

                let res = response;
                if (typeof response === 'string') {
                    try { res = JSON.parse(response); } catch(e) {}
                }

                if (res.status !== 'success') {
                    throw new Error(res.message || 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์');
                }

                if (chunkIndex === totalChunks - 1 && res.filename) {
                    finalSavedName = res.filename;
                }

                const percent = Math.round(((chunkIndex + 1) / totalChunks) * 100);
                progressBar.css('width', percent + '%').text(percent + '%');
            }

            return finalSavedName;
        }

        // Helper: Format File Size
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        // Setup Drag & Drop Zone for a specific input/dropzone pair
        function setupDropzone(dropzoneId, inputId, indicatorId) {
            const dropzone = $(dropzoneId);
            const input = $(inputId);
            const indicator = $(indicatorId);

            // Click dropzone to trigger input
            dropzone.on('click', function() {
                input.trigger('click');
            });

            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone[0].addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Drag over styling
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone[0].addEventListener(eventName, () => dropzone.addClass('dragover'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone[0].addEventListener(eventName, () => dropzone.removeClass('dragover'), false);
            });

            // Handle dropped files
            dropzone[0].addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    handleFileSelection(files[0]);
                }
            }, false);

            // Handle input change
            input.on('change', function() {
                if (this.files.length > 0) {
                    handleFileSelection(this.files[0]);
                }
            });

            function handleFileSelection(file) {
                if (file.type !== 'application/pdf' && !file.name.endsWith('.pdf')) {
                    Swal.fire('ข้อผิดพลาด', 'กรุณาเลือกเฉพาะไฟล์ PDF เท่านั้น', 'warning');
                    input.val('');
                    return;
                }
                if (file.size > 20 * 1024 * 1024) {
                    Swal.fire('ข้อผิดพลาด', 'ขนาดไฟล์ต้องไม่เกิน 20MB', 'warning');
                    input.val('');
                    return;
                }

                // Put file into input element if dropped
                const dt = new DataTransfer();
                dt.items.add(file);
                input[0].files = dt.files;

                // Update UI Indicator
                indicator.find('.file-name').text(file.name);
                indicator.find('.file-size').text(formatBytes(file.size));
                indicator.slideDown(200);
                dropzone.hide();
            }
        }

        setupDropzone('#dropzone_lesson_plan', '#pa_file_lesson_plan', '#indicator_lesson_plan');
        setupDropzone('#dropzone_pa1', '#pa_file_pa1', '#indicator_pa1');

        // Cancel / Remove selected file
        $('.remove-file').on('click', function(e) {
            e.stopPropagation();
            const target = $(this).data('target');
            if (target === 'lesson_plan') {
                $('#pa_file_lesson_plan').val('');
                $('#indicator_lesson_plan').slideUp(150, function() {
                    $('#dropzone_lesson_plan').show();
                });
            } else if (target === 'pa1') {
                $('#pa_file_pa1').val('');
                $('#indicator_pa1').slideUp(150, function() {
                    $('#dropzone_pa1').show();
                });
            }
        });

        // 1. Save Presentation Link
        $('#btn-save-link').on('click', function() {
            const link = $('#pa_presentation_link').val().trim();
            if (!link) {
                Swal.fire('ข้อผิดพลาด', 'กรุณาระบุ URL ลิ้งก์สื่อนำเสนอ', 'warning');
                return;
            }

            const btn = $(this);
            const orig = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> กำลังบันทึก...');

            $.ajax({
                url: '<?= base_url('pa-agreement/upload') ?>',
                type: 'POST',
                data: {
                    pa_year: year,
                    pa_presentation_link: link
                },
                dataType: 'json',
                success: function(res) {
                    btn.prop('disabled', false).html(orig);
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, timer: 1500, showConfirmButton: false })
                            .then(() => location.reload());
                    } else {
                        Swal.fire('ผิดพลาด', res.message, 'error');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html(orig);
                    Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                }
            });
        });

        // 2. Save Lesson Plan PDF
        $('#btn-save-lesson-plan').on('click', async function() {
            const fileInput = $('#pa_file_lesson_plan')[0];
            const file = fileInput.files[0];
            if (!file) {
                Swal.fire('ข้อผิดพลาด', 'กรุณาเลือกไฟล์แผนการจัดการเรียนรู้ (PDF)', 'warning');
                return;
            }
            if (file.type !== 'application/pdf' && !file.name.endsWith('.pdf')) {
                Swal.fire('ข้อผิดพลาด', 'อนุญาตเฉพาะไฟล์นามสกุล PDF เท่านั้น', 'warning');
                return;
            }
            if (file.size > 20 * 1024 * 1024) {
                Swal.fire('ข้อผิดพลาด', 'ขนาดไฟล์ต้องไม่เกิน 20MB', 'warning');
                return;
            }

            const btn = $(this);
            const orig = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> กำลังอัปโหลด...');

            try {
                const uploadedFilename = await uploadFileChunked(file, 'lesson_plan', '#progress_lesson_plan');
                
                $.ajax({
                    url: '<?= base_url('pa-agreement/upload') ?>',
                    type: 'POST',
                    data: {
                        pa_year: year,
                        uploaded_lesson_plan_filename: uploadedFilename
                    },
                    dataType: 'json',
                    success: function(res) {
                        btn.prop('disabled', false).html(orig);
                        if (res.status === 'success') {
                            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: 'บันทึกไฟล์แผนการสอนสำเร็จ', timer: 1500, showConfirmButton: false })
                                .then(() => location.reload());
                        } else {
                            Swal.fire('ผิดพลาด', res.message, 'error');
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html(orig);
                        Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                    }
                });
            } catch (err) {
                btn.prop('disabled', false).html(orig);
                $('#progress_lesson_plan').addClass('d-none');
                Swal.fire('ผิดพลาด', err.message, 'error');
            }
        });

        // 3. Save PA1 PDF
        $('#btn-save-pa1').on('click', async function() {
            const fileInput = $('#pa_file_pa1')[0];
            const file = fileInput.files[0];
            if (!file) {
                Swal.fire('ข้อผิดพลาด', 'กรุณาเลือกไฟล์บันทึกข้อตกลง PA1 (PDF)', 'warning');
                return;
            }
            if (file.type !== 'application/pdf' && !file.name.endsWith('.pdf')) {
                Swal.fire('ข้อผิดพลาด', 'อนุญาตเฉพาะไฟล์นามสกุล PDF เท่านั้น', 'warning');
                return;
            }
            if (file.size > 20 * 1024 * 1024) {
                Swal.fire('ข้อผิดพลาด', 'ขนาดไฟล์ต้องไม่เกิน 20MB', 'warning');
                return;
            }

            const btn = $(this);
            const orig = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> กำลังอัปโหลด...');

            try {
                const uploadedFilename = await uploadFileChunked(file, 'pa1', '#progress_pa1');
                
                $.ajax({
                    url: '<?= base_url('pa-agreement/upload') ?>',
                    type: 'POST',
                    data: {
                        pa_year: year,
                        uploaded_pa1_filename: uploadedFilename
                    },
                    dataType: 'json',
                    success: function(res) {
                        btn.prop('disabled', false).html(orig);
                        if (res.status === 'success') {
                            Swal.fire({ icon: 'success', title: 'สำเร็จ', text: 'บันทึกไฟล์บันทึกข้อตกลง PA1 สำเร็จ', timer: 1500, showConfirmButton: false })
                                .then(() => location.reload());
                        } else {
                            Swal.fire('ผิดพลาด', res.message, 'error');
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html(orig);
                        Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                    }
                });
            } catch (err) {
                btn.prop('disabled', false).html(orig);
                $('#progress_pa1').addClass('d-none');
                Swal.fire('ผิดพลาด', err.message, 'error');
            }
        });

        // Delete Handler
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const type = $(this).data('type');

            let confirmText = 'คุณต้องการลบข้อมูลนี้หรือไม่?';
            if (type === 'all') confirmText = 'คุณต้องการลบข้อมูลรายการนี้ทั้งหมดใช่หรือไม่? ไฟล์ทั้งหมดจะถูกลบออกจากเซิร์ฟเวอร์';

            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#8592a3',
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('pa-agreement/delete-item') ?>',
                        type: 'POST',
                        data: { id: id, type: type },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, timer: 1200, showConfirmButton: false })
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('ผิดพลาด', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์เพื่อลบข้อมูลได้', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
