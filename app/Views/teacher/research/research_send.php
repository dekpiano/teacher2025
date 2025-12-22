<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ส่งงานวิจัยในชั้นเรียน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$onOffSetting = (isset($OnOff) && is_array($OnOff) && !empty($OnOff)) ? $OnOff[0] : null;
$is_system_on = false;
$deadline = null;
if ($onOffSetting && is_object($onOffSetting)) {
    $tiemstart = strtotime($onOffSetting->seres_setup_startdate);
    $tiemEnd = strtotime($onOffSetting->seres_setup_enddate);
    $timeNow = time();
    $is_system_on = ($tiemstart < $timeNow && $tiemEnd > $timeNow && $onOffSetting->seres_setup_status == "on");
    $deadline = $onOffSetting->seres_setup_enddate;
}
?>

<style>
    .send-research-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    .form-header-luxe {
        background: #fff;
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-left: 6px solid #696cff;
    }
    .luxe-input-card {
        background: #fff;
        border-radius: 1.25rem;
        padding: 2.5rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.01);
    }
    .guide-card {
        border: none;
        border-radius: 1rem;
        background: rgba(105, 108, 255, 0.04);
        border: 1px dashed rgba(105, 108, 255, 0.2);
    }
    .icon-circle {
        width: 50px;
        height: 50px;
        background: #696cff;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
        box-shadow: 0 5px 15px rgba(105, 108, 255, 0.4);
    }
</style>

<div class="send-research-container py-3">
    <!-- Form Header -->
    <div class="form-header-luxe d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1 text-dark">บันทึกส่งงานวิจัยใหม่</h3>
            <p class="text-muted mb-0 small">กรอกรายละเอียดงานวิจัยสำหรับภาคเรียน <?= esc($onOffSetting->seres_setup_term.'/'.$onOffSetting->seres_setup_year) ?></p>
        </div>
        <div class="text-end">
            <?php if($is_system_on): ?>
                <span class="badge bg-label-success rounded-pill px-3 py-1">
                    <i class="bi bi-unlock-fill me-1"></i> ระบบเปิดอยู่
                </span>
                <div class="mt-1 small text-muted">ครบกำหนด: <?= thai_date_and_time(strtotime($deadline)) ?></div>
            <?php else: ?>
                <span class="badge bg-label-danger rounded-pill px-3 py-1">
                    <i class="bi bi-lock-fill me-1"></i> ระบบปิดแล้ว
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Entry Form -->
    <div class="luxe-input-card">
        <form class="needs-validation" novalidate id="form_insert_research" action="<?= site_url('research/insert-research') ?>" method="post" enctype="multipart/form-data">
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="icon-circle">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="seres_research_name" name="seres_research_name" placeholder="ชื่องานวิจัย" required>
                        <label for="seres_research_name">หัวข้อ/ชื่องานวิจัยในชั้นเรียน</label>
                        <div class="invalid-feedback">กรุณากรอกชื่อเรื่องงานวิจัย</div>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="seres_namesubject" name="seres_namesubject" placeholder="ชื่อรายวิชา" required>
                        <label for="seres_namesubject">ชื่อรายวิชาที่ทำวิจัย</label>
                        <div class="invalid-feedback">กรุณากรอกชื่อรายวิชา</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="seres_coursecode" name="seres_coursecode" placeholder="รหัสวิชา" required>
                                <label for="seres_coursecode">รหัสวิชา</label>
                                <div class="invalid-feedback">กรุณากรอกรหัสวิชา</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <select class="form-select" id="seres_gradelevel" name="seres_gradelevel" required>
                                    <option value="" selected disabled>เลือกชั้นเรียน...</option>
                                    <?php for($i=1; $i<=6; $i++): ?>
                                        <option value="<?= $i ?>">มัธยมศึกษาปีที่ <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <label for="seres_gradelevel">ระดับชั้น</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mb-4">
                        <textarea class="form-control" placeholder="รายละเอียดเพิ่มเติม" id="seres_sendcomment" name="seres_sendcomment" style="height: 120px"></textarea>
                        <label for="seres_sendcomment">รายละเอียดเพิ่มเติม / บทคัดย่อโดยย่อ (ถ้ามี)</label>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark d-block">แนบไฟล์งานวิจัย (PDF)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-file-pdf text-danger"></i></span>
                            <input class="form-control" type="file" id="seres_file" name="seres_file" accept=".pdf" required>
                        </div>
                        <div class="form-text small">รองรับไฟล์รูปแบบ PDF เท่านั้น (ขนาดไม่เกิน 50MB)</div>
                    </div>
                </div>

                <!-- Guidance Sidebar -->
                <div class="col-md-4">
                    <div class="guide-card p-4 h-100">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle me-1"></i> ข้อแนะนำการส่ง</h6>
                        <ul class="list-unstyled small text-muted mb-0">
                            <li class="mb-3 d-flex align-items-start">
                                <i class="bi bi-check2-circle text-success me-2 mt-1"></i>
                                <div>ตรวจสอบความถูกต้องของชื่อรหัสวิชาและระดับชั้นก่อนส่ง</div>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <i class="bi bi-check2-circle text-success me-2 mt-1"></i>
                                <div>ไฟล์ควรเป็น PDF ที่ผ่านการตรวจสอบความเรียบร้อยแล้ว</div>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <i class="bi bi-check2-circle text-success me-2 mt-1"></i>
                                <div>ในกรณีที่สอนหลายวิชา ให้แยกส่งทีละรายวิชา</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-center gap-3 mt-5">
                <a href="<?= site_url('research') ?>" class="btn btn-label-secondary btn-lg px-5">ยกเลิก</a>
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow" <?= $is_system_on ? '' : 'disabled' ?>>
                    <i class="bi bi-send-fill me-2"></i> บันทึกและส่งงาน
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Handle form submission via AJAX for insert_research
    $('#form_insert_research').on('submit', function(e) {
        e.preventDefault();
        
        if (this.checkValidity() === false) {
            this.classList.add('was-validated');
            return;
        }

        var formData = new FormData(this);
        const submitButton = $(this).find('button[type="submit"]');
        
        Swal.fire({
            title: 'กำลังส่งข้อมูล...',
            text: 'ระบบกำลังดําเนินการอัปโหลดไฟล์งานวิจัย',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        submitButton.prop('disabled', true);

        $.ajax({
            url: '<?= site_url('research/insert-research') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'ส่งงานวิจัยสําเร็จ!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '<?= site_url('research') ?>';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'การส่งล้มเหลว',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ในขณะนี้'
                });
            },
            complete: function() {
                submitButton.prop('disabled', false);
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
