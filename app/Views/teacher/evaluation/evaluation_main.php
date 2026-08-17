<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dropdown-hover:hover > .dropdown-menu {
        display: block;
        margin-top: 0;
    }
    .eva-hero-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: 1px solid rgba(105, 108, 255, 0.15) !important;
        border-radius: 12px;
    }
    .eva-card-action {
        border-radius: 12px;
        border: 1px solid #e7e7e8 !important;
        transition: all 0.2s ease-in-out;
    }
    .eva-card-action:hover {
        box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    }
    .eva-card-active {
        border: 1px solid rgba(113, 221, 55, 0.4) !important;
        background: linear-gradient(180deg, rgba(113, 221, 55, 0.02) 0%, #ffffff 100%);
    }
    .eva-icon-box {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }
</style>

<div class="row">
    <div class="col-12">
        <!-- Compact Hero Header -->
        <div class="card mb-3 eva-hero-card shadow-sm">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary rounded-3 me-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-earmark-check-fill fs-3 text-primary"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h5 class="mb-0 fw-bold text-dark">การประเมินผลการปฏิบัติงานข้าราชการหรือพนักงานครูและบุคลากรทางการศึกษาองค์กรปกครองส่วนท้องถิ่น</h5>
                                <span class="badge bg-primary rounded-pill px-2 py-1 fs-7">
                                    ปีงบประมาณ <?= $current_year ?> (รอบที่ <?= $current_round ?>)
                                </span>
                            </div>
                            <div class="text-muted small mt-1">
                                <i class="bi bi-calendar3 me-1"></i> รอบการประเมิน: 
                                <span class="fw-semibold text-dark">
                                    <?= $current_round == 1 ? "1 ต.ค. " . ($current_year - 1) . " - 31 มี.ค. " . ($current_year) : "1 เม.ย. " . ($current_year) . " - 30 ก.ย. " . ($current_year) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <?php if ($system_config['config']): ?>
                            <div class="d-flex align-items-center bg-white border rounded-pill px-3 py-1 shadow-xs">
                                <span class="badge <?= $system_config['is_open'] ? 'bg-success' : 'bg-danger' ?> rounded-circle p-1 me-2" style="width: 8px; height: 8px;"></span>
                                <span class="small fw-semibold <?= $system_config['is_open'] ? 'text-success' : 'text-danger' ?> me-2">
                                    <?= $system_config['is_open'] ? 'เปิดรับส่งเอกสาร' : 'ปิดรับส่งเอกสาร' ?>
                                </span>
                                <span class="text-muted small border-start ps-2">
                                    <?= date('d/m/Y', strtotime($system_config['config']['conf_start_date'] . ' +543 years')) ?> - <?= date('d/m/Y', strtotime($system_config['config']['conf_end_date'] . ' +543 years')) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!$system_config['is_open']) : ?>
                    <div class="alert alert-danger d-flex align-items-center py-2 px-3 mt-3 mb-0 rounded-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div class="small fw-semibold"><?= $system_config['message'] ?></div>
                    </div>
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-between text-muted small mt-2 pt-2 border-top">
                        <div><i class="bi bi-info-circle text-primary me-1"></i> สามารถเลือกส่งเฉพาะไฟล์ PDF หรือเฉพาะลิ้งก์ผลงานอย่างใดอย่างหนึ่งได้</div>
                        <span class="badge bg-label-info"><i class="bi bi-shield-check me-1"></i> บันทึกแยกอิสระได้ทันที</span>
                    </div>
                <?php endif; ?>

                <!-- Prominent Notice Banner -->
                <div class="alert alert-warning d-flex align-items-center py-2 px-3 mt-2 mb-0 rounded-3 border-0 bg-label-warning" role="alert">
                    <i class="bi bi-shield-lock-fill me-2 fs-5 text-warning"></i>
                    <div class="small fw-semibold">
                        <span class="fw-bold text-dark">หมายเหตุสำคัญ:</span> การประเมินผลการปฏิบัติงานนี้ <strong>ใช้เฉพาะข้าราชการครูเท่านั้น</strong> (ครูอัตราจ้าง / เจ้าหน้าที่ ไม่จำเป็นต้องส่งในส่วนนี้)
                    </div>
                </div>
            </div>
        </div>

        <form id="uploadForm" class="no-loader" action="<?= base_url('evaluation/upload') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="eva_year" value="<?= $current_year ?>">
            <input type="hidden" name="eva_round" value="<?= $current_round ?>">

            <div class="row g-3">
                <!-- Card 1: PDF Upload -->
                <div class="col-lg-6">
                    <div class="card shadow-sm h-100 eva-card-action <?= ($evaluation && !empty($evaluation['eva_file'])) ? 'eva-card-active' : '' ?>">
                        <div class="card-header bg-white border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="eva-icon-box bg-label-primary me-2">
                                    <i class="bi bi-file-earmark-pdf fs-5 text-primary"></i>
                                </div>
                                <span class="fw-bold text-dark">1. เอกสารสรุปผล (PDF)</span>
                            </div>
                            <?php if ($evaluation && !empty($evaluation['eva_file'])) : ?>
                                <span class="badge bg-label-success rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> ส่งไฟล์แล้ว</span>
                            <?php else: ?>
                                <span class="badge bg-label-secondary rounded-pill px-2 py-1">ยังไม่ส่ง</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <?php if ($evaluation && !empty($evaluation['eva_file'])) : ?>
                                    <div class="alert alert-outline-success d-flex align-items-center justify-content-between p-2 mb-3 rounded-3" role="alert">
                                        <div class="d-flex align-items-center text-truncate me-2">
                                            <i class="bi bi-file-earmark-check-fill fs-4 text-success me-2"></i>
                                            <div class="text-truncate">
                                                <div class="small fw-bold text-dark text-truncate">มีไฟล์ในระบบแล้ว</div>
                                                <div class="x-small text-muted text-truncate"><?= esc($evaluation['eva_file']) ?></div>
                                            </div>
                                        </div>
                                        <a href="<?= env('upload.server.baseurl.evaluation') . $evaluation['eva_year'] . '/' . $evaluation['eva_round'] . '/' . $evaluation['eva_file'] ?>" target="_blank" class="btn btn-xs btn-success text-nowrap rounded-pill px-2">
                                            <i class="bi bi-eye me-1"></i> เปิดดูไฟล์
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-2">
                                    <label for="eva_file" class="form-label small fw-semibold text-secondary mb-1">
                                        <?= ($evaluation && !empty($evaluation['eva_file'])) ? 'เลือกไฟล์ใหม่เพื่ออัปโหลดแทนที่:' : 'เลือกไฟล์เอกสาร PDF (ขนาดไม่เกิน 20MB):' ?>
                                    </label>
                                    <input class="form-control form-control-sm" type="file" id="eva_file" name="eva_file" accept=".pdf" <?= !$system_config['is_open'] ? 'disabled' : '' ?>>
                                </div>
                            </div>
                            <button type="submit" id="btn-save-pdf" class="btn btn-primary btn-sm w-100 mt-3 shadow-xs" <?= !$system_config['is_open'] ? 'disabled' : '' ?>>
                                <i class="bi bi-cloud-arrow-up me-1"></i> <?= !$system_config['is_open'] ? 'ระบบปิดรับส่ง' : 'บันทึกไฟล์ PDF' ?>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Canva Link -->
                <div class="col-lg-6">
                    <div class="card shadow-sm h-100 eva-card-action <?= ($evaluation && !empty($evaluation['eva_canva_link'])) ? 'eva-card-active' : '' ?>">
                        <div class="card-header bg-white border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="eva-icon-box bg-label-danger me-2">
                                    <i class="bi bi-link-45deg fs-5 text-danger"></i>
                                </div>
                                <span class="fw-bold text-dark">2. สื่อนำเสนอผลงาน (Canva / ลิงก์)</span>
                            </div>
                            <?php if ($evaluation && !empty($evaluation['eva_canva_link'])) : ?>
                                <span class="badge bg-label-success rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> ส่งลิ้งก์แล้ว</span>
                            <?php else: ?>
                                <span class="badge bg-label-secondary rounded-pill px-2 py-1">ยังไม่ส่ง</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <?php if ($evaluation && !empty($evaluation['eva_canva_link'])) : ?>
                                    <div class="alert alert-outline-danger d-flex align-items-center justify-content-between p-2 mb-3 rounded-3" role="alert">
                                        <div class="d-flex align-items-center text-truncate me-2">
                                            <i class="bi bi-link-45deg fs-4 text-danger me-1"></i>
                                            <div class="text-truncate">
                                                <div class="small fw-bold text-dark text-truncate">มีลิ้งก์ผลงานในระบบแล้ว</div>
                                                <div class="x-small text-muted text-truncate"><?= esc($evaluation['eva_canva_link']) ?></div>
                                            </div>
                                        </div>
                                        <a href="<?= $evaluation['eva_canva_link'] ?>" target="_blank" class="btn btn-xs btn-danger text-nowrap rounded-pill px-2">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> เปิดดูเดิม
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-2">
                                    <label for="eva_canva_link" class="form-label small fw-semibold text-secondary mb-1">
                                        วาง URL สื่อนำเสนอ (Canva, Google Drive, YouTube):
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                        <input type="url" class="form-control form-control-sm" id="eva_canva_link" name="eva_canva_link" placeholder="https://www.canva.com/design/..." value="<?= $evaluation['eva_canva_link'] ?? '' ?>" <?= !$system_config['is_open'] ? 'disabled' : '' ?>>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="btn-save-canva" class="btn btn-primary btn-sm w-100 mt-3 shadow-xs" <?= !$system_config['is_open'] ? 'disabled' : '' ?>>
                                <i class="bi bi-link-45deg me-1"></i> <?= !$system_config['is_open'] ? 'ระบบปิดรับส่ง' : 'บันทึกลิ้งก์สื่อนำเสนอ' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Table Card: History -->
        <div class="card border-0 shadow-sm mt-4 eva-card-action">
            <div class="card-header bg-white border-bottom py-3 px-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="eva-icon-box bg-label-secondary me-2">
                        <i class="bi bi-clock-history fs-5 text-secondary"></i>
                    </div>
                    <span class="fw-bold text-dark">ประวัติการส่งย้อนหลัง</span>
                </div>
                <span class="badge bg-label-primary rounded-pill"><?= count($history) ?> รายการ</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ปีงบประมาณ</th>
                                <th>รอบที่</th>
                                <th>วันที่ส่งล่าสุด</th>
                                <th class="text-center">เอกสาร PDF</th>
                                <th class="text-center">สื่อนำเสนอ</th>
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
                                        <td class="ps-3 fw-semibold text-dark"><?= $row['eva_year'] ?></td>
                                        <td><span class="badge bg-label-info">ครั้งที่ <?= $row['eva_round'] ?></span></td>
                                        <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($row['eva_created_at'] . ' +543 years')) ?> น.</td>
                                        <td class="text-center">
                                            <?php if (!empty($row['eva_file'])) : ?>
                                                <a href="<?= env('upload.server.baseurl.evaluation') . $row['eva_year'] . '/' . $row['eva_round'] . '/' . $row['eva_file'] ?>" target="_blank" class="btn btn-sm btn-icon btn-label-primary rounded-pill shadow-xs" title="เปิดดูไฟล์ PDF">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>
                                            <?php else : ?>
                                                <span class="badge bg-label-secondary small pe-none">ไม่ได้ส่ง</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($row['eva_canva_link'])) : ?>
                                                <a href="<?= $row['eva_canva_link'] ?>" target="_blank" class="btn btn-sm btn-icon btn-label-danger rounded-pill shadow-xs" title="เปิดดูลำดับสื่อนำเสนอ">
                                                    <i class="bi bi-play-circle-fill"></i>
                                                </a>
                                            <?php else : ?>
                                                <span class="badge bg-label-secondary small pe-none">ไม่ได้ส่ง</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center pe-3">
                                            <div class="dropdown dropdown-hover d-inline-block">
                                                <button class="btn btn-sm btn-icon btn-label-secondary rounded-pill" type="button" <?= !$system_config['is_open'] && $row['eva_year'] == $current_year && $row['eva_round'] == $current_round ? 'disabled' : '' ?>>
                                                    <i class="bi bi-trash-fill <?= !$system_config['is_open'] && $row['eva_year'] == $current_year && $row['eva_round'] == $current_round ? 'text-secondary' : 'text-danger' ?>"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li><h6 class="dropdown-header">เลือกสิ่งที่ต้องการลบ</h6></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btn-delete <?= empty($row['eva_file']) ? 'disabled text-muted' : '' ?>" 
                                                           href="javascript:void(0)" 
                                                           data-id="<?= $row['eva_id'] ?>" 
                                                           data-type="file">
                                                            <i class="bi bi-file-earmark-pdf me-2"></i> ลบไฟล์ PDF
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btn-delete <?= empty($row['eva_canva_link']) ? 'disabled text-muted' : '' ?>" 
                                                           href="javascript:void(0)" 
                                                           data-id="<?= $row['eva_id'] ?>" 
                                                           data-type="link">
                                                            <i class="bi bi-link-45deg me-2"></i> ลบลิ้งก์ผลงาน
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btn-delete text-danger fw-bold" 
                                                           href="javascript:void(0)" 
                                                           data-id="<?= $row['eva_id'] ?>" 
                                                           data-type="all">
                                                            <i class="bi bi-trash3-fill me-2"></i> ลบข้อมูลรายการนี้ทั้งหมด
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
        // Modal notification on page load
        Swal.fire({
            icon: 'warning',
            title: '<span class="text-warning fw-bold fs-4">⚠️ ข้อควรทราบสำคัญ</span>',
            html: `
                <div class="text-center py-2">
                    <div class="p-3 bg-label-warning rounded-3 mb-3 border border-warning">
                        <h5 class="fw-bold text-warning mb-1">
                            <i class="bi bi-shield-lock-fill me-1"></i> ระบบนี้สำหรับ "ข้าราชการครู" เท่านั้น
                        </h5>
                        <div class="text-dark fw-semibold mt-2" style="font-size: 1.05rem;">
                            สำหรับตำแหน่ง <span class="badge bg-warning text-dark fs-7">ครูผู้ช่วย</span> และ <span class="badge bg-warning text-dark fs-7">ครู (คศ.1 - คศ.5)</span>
                        </div>
                    </div>

                    <div class="alert alert-secondary text-start small mb-0 py-2 border">
                        <i class="bi bi-info-circle-fill text-primary me-1"></i> 
                        <strong>ครูอัตราจ้าง / ครูจ้างสอน / เจ้าหน้าที่ธุรการ และบุคลากรอื่น ๆ:</strong><br>
                        <span class="text-muted fw-semibold">ไม่จำเป็นต้องกรอกหรือส่งข้อมูลในระบบนี้</span>
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

        let activeBtn = null;
        let originalHtml = '';

        $('#uploadForm button[type="submit"]').on('click', function() {
            activeBtn = $(this);
            originalHtml = activeBtn.html();
        });

        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            
            const fileInput = $('#eva_file')[0];
            const file = fileInput.files[0];
            const canvaLink = $('#eva_canva_link').val().trim();
            const $submitBtn = activeBtn || $(this).find('button[type="submit"]').first();
            activeBtn = $submitBtn; // safety

            // Check if at least one is provided
            const hasExistingFile = <?= $evaluation && !empty($evaluation['eva_file']) ? 'true' : 'false' ?>;
            
            if (!file && !canvaLink && !hasExistingFile) {
                Swal.fire({ 
                    icon: 'warning', 
                    title: 'ข้อมูลไม่ครบถ้วน', 
                    text: 'กรุณาอัปโหลดไฟล์ PDF หรือใส่ลิ้งก์สื่อนำเสนอ (อย่างใดอย่างหนึ่ง)' 
                });
                return;
            }

            Swal.fire({
                title: 'ยืนยันการบันทึก?',
                text: "ระบบจะบันทึกข้อมูลและส่งผลการปฏิบัติงาน",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (file) {
                        executeUploadProcess(file, $submitBtn);
                    } else {
                        // Only update Canva link or other meta
                        saveOnlyMetadata($submitBtn);
                    }
                }
            });

            async function saveOnlyMetadata(submitButton) {
                submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> กำลังบันทึก...');
                
                const formData = new FormData($('#uploadForm')[0]);
                formData.delete('eva_file');

                $.ajax({
                    url: $('#uploadForm').attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: res.message, timer: 2000, showConfirmButton: false }).then(() => location.reload());
                        } else {
                            Swal.fire('ผิดพลาด', res.message, 'error');
                            restoreBtn(submitButton);
                        }
                    },
                    error: function() {
                        Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                        restoreBtn(submitButton);
                    }
                });
            }

            async function executeUploadProcess(file, submitButton) {
                // Configuration
                const chunkSize = 500 * 1024; // 500KB per chunk
                const totalChunks = Math.ceil(file.size / chunkSize);
                
                // Generate a unique filename
                const teacherId = '<?= session()->get('person_id') ?>';
                const year = '<?= $current_year ?>';
                const round = '<?= $current_round ?>';
                const timestamp = Math.floor(Date.now() / 1000);
                const fileExt = file.name.split('.').pop();
                const finalFileName = `PA_${year}_${round}_${teacherId}_${timestamp}.${fileExt}`;

                Swal.fire({
                    title: 'กำลังอัปโหลด...',
                    html: 'ระบบกำลังเริ่มดำเนินการ (0%)',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> กำลังอัปโหลด...');

                const uploadSingleChunk = async (chunkIndex) => {
                    const start = chunkIndex * chunkSize;
                    const end = Math.min(start + chunkSize, file.size);
                    const chunk = file.slice(start, end);

                    const formData = new FormData();
                    formData.append('file', chunk);
                    formData.append('path', `personnel/teacher/evaluation/${year}/${round}`);
                    formData.append('filename', finalFileName);
                    formData.append('chunk', chunkIndex);
                    formData.append('chunks', totalChunks);

                    return $.ajax({
                        url: '<?= site_url('evaluation/upload-chunk') ?>',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false
                    });
                };

                try {
                    let uploadedFileName = finalFileName;
                    // Upload all chunks sequentially
                    for (let i = 0; i < totalChunks; i++) {
                        const response = await uploadSingleChunk(i);
                        if (i === totalChunks - 1) {
                            let res = response;
                            if (typeof response === 'string') {
                                try { res = JSON.parse(response); } catch(e) {}
                            }
                            if (res.status === 'success') {
                                uploadedFileName = res.filename;
                            } else {
                                throw new Error(res.message || 'เกิดความผิดพลาดในการรวมไฟล์');
                            }
                        }
                        const percent = Math.round(((i + 1) / totalChunks) * 100);
                        Swal.update({ html: `กำลังอัปโหลดส่วนประกอบของไฟล์ (${percent}%)` });
                    }

                    // Chunks uploaded, now save record
                    const formData = new FormData($('#uploadForm')[0]);
                    formData.set('eva_file_name_ready', uploadedFileName);
                    formData.delete('eva_file'); // Remove original file blob

                    $.ajax({
                        url: $('#uploadForm').attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({ 
                                    icon: 'success', 
                                    title: 'สำเร็จ!', 
                                    text: res.message, 
                                    timer: 2000, 
                                    showConfirmButton: false 
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('ผิดพลาด', res.message, 'error');
                                restoreBtn(submitButton);
                            }
                        },
                        error: function() {
                            Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                            restoreBtn(submitButton);
                        }
                    });

                } catch (err) {
                    console.error('Upload Error:', err);
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'การอัปโหลดขัดข้อง', 
                        text: 'ไม่สามารถส่งไฟล์ได้ (แนะนำให้ลองบีบอัดไฟล์ PDF หรือเชื่อมต่ออินเทอร์เน็ตที่เสถียร)' 
                    });
                    restoreBtn(submitButton);
                }
            }

            function restoreBtn(btn) {
                if (btn) {
                    btn.prop('disabled', false).html(originalHtml);
                }
            }
        });

        // Delete Logic
        $('.btn-delete').on('click', function() {
            const id = $(this).data('id');
            const type = $(this).data('type');
            let typeText = 'ข้อมูล';
            
            if (type === 'file') typeText = 'ไฟล์ PDF';
            else if (type === 'link') typeText = 'ลิ้งก์ผลงาน';
            else if (type === 'all') typeText = 'ข้อมูลและไฟล์ทั้งหมดในรอบนี้';

            Swal.fire({
                title: `ยืนยันการลบ${typeText}?`,
                text: "การดำเนินการนี้ไม่สามารถเรียกคืนได้",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ยืนยันการลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= site_url('evaluation/delete-item') ?>',
                        type: 'POST',
                        data: { id: id, type: type },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({ icon: 'success', title: 'ลบสำเร็จ!', text: res.message, timer: 1500, showConfirmButton: false })
                                .then(() => location.reload());
                            } else {
                                Swal.fire('ผิดพลาด', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
