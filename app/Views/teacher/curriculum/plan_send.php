<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ลงทะเบียนรายวิชา') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .form-header-luxe {
        background: linear-gradient(135deg, #696cff 0%, #3f42ef 100%);
        border-radius: 1.25rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(105, 108, 255, 0.2);
    }
    .luxe-card {
        border: none;
        border-radius: 1.5rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .guide-card {
        background: #f8f9fa;
        border: 1px dashed #696cff;
        border-radius: 1rem;
        padding: 1.5rem;
    }
</style>

<div class="container-xxl flex-grow-1">
    <!-- Header -->
    <div class="form-header-luxe d-flex justify-content-between align-items-center">
        <div>
            <h1 class="display-6 fw-bold mb-1">ลงทะเบียนวิชาสอน</h1>
            <p class="opacity-75 mb-0">เพิ่มรายวิชาใหม่เพื่อเริ่มต้นการนำส่งแผนการสอน</p>
        </div>
        <a href="<?= base_url('curriculum') ?>" class="btn btn-white btn-lg rounded-pill shadow-sm px-4 text-primary fw-bold">
            <i class="bi bi-arrow-left me-2"></i> ย้อนกลับ
        </a>
    </div>

    <form class="needs-validation" novalidate id="form_insert_plan" method="post">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card luxe-card p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar avatar-md me-3">
                            <span class="avatar-initial rounded-circle bg-label-primary"><i class="bi bi-journal-plus fs-4"></i></span>
                        </div>
                        <h4 class="mb-0 fw-bold">ข้อมูลรายวิชา</h4>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <input type="text" id="seplan_namesubject" name="seplan_namesubject" placeholder="ระบุชื่อวิชา"
                                    class="form-control border-0 bg-light" required>
                                <label for="seplan_namesubject">ชื่อวิชา (ภาษาไทย/อังกฤษ)</label>
                                <div class="invalid-feedback">กรุณากรอกชื่อวิชา</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" id="seplan_coursecode" name="seplan_coursecode" placeholder="เช่น ว21101"
                                    class="form-control border-0 bg-light" required>
                                <label for="seplan_coursecode">รหัสวิชา</label>
                                <div class="invalid-feedback">กรุณากรอกรหัสวิชา</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select id="seplan_gradelevel" name="seplan_gradelevel" class="form-select border-0 bg-light" required>
                                    <option value="" selected disabled>เลือก...</option>
                                    <option value="1">มัธยมศึกษาปีที่ 1</option>
                                    <option value="2">มัธยมศึกษาปีที่ 2</option>
                                    <option value="3">มัธยมศึกษาปีที่ 3</option>
                                    <option value="4">มัธยมศึกษาปีที่ 4</option>
                                    <option value="5">มัธยมศึกษาปีที่ 5</option>
                                    <option value="6">มัธยมศึกษาปีที่ 6</option>
                                </select>
                                <label for="seplan_gradelevel">ระดับชั้น</label>
                                <div class="invalid-feedback">กรุณาเลือกระดับชั้น</div>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold mb-3 d-block">ประเภทรายวิชา</label>
                            <div class="d-flex gap-4">
                                <div class="form-check custom-option custom-option-icon border rounded-3 p-3 flex-fill text-center <?= (isset($OnOff[0]) && $OnOff[0]->seplanset_status != "on") ? 'opacity-50' : '' ?>">
                                    <label class="form-check-label custom-option-content" for="seplan_typesubject_base">
                                        <span class="custom-option-body">
                                            <i class="bi bi-book mb-2 fs-2 d-block"></i>
                                            <span class="custom-option-title d-block fw-bold">รายวิชาพื้นฐาน</span>
                                        </span>
                                        <input class="form-check-input" type="radio" name="seplan_typesubject" id="seplan_typesubject_base" value="พื้นฐาน" required>
                                    </label>
                                </div>
                                <div class="form-check custom-option custom-option-icon border rounded-3 p-3 flex-fill text-center <?= (isset($OnOff[0]) && $OnOff[0]->seplanset_status != "on") ? 'opacity-50' : '' ?>">
                                    <label class="form-check-label custom-option-content" for="seplan_typesubject_extra">
                                        <span class="custom-option-body">
                                            <i class="bi bi-plus-circle mb-2 fs-2 d-block"></i>
                                            <span class="custom-option-title d-block fw-bold">รายวิชาเพิ่มเติม</span>
                                        </span>
                                        <input class="form-check-input" type="radio" name="seplan_typesubject" id="seplan_typesubject_extra" value="เพิ่มเติม" required>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 text-center">
                        <?php if(isset($OnOff[0]) && $OnOff[0]->seplanset_status == "on"):?>
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-lg">
                                <i class="bi bi-check-circle-fill me-2"></i> ลงทะเบียนและดำเนินการต่อ
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary btn-lg rounded-pill px-5 py-3 shadow-lg disabled">
                                <i class="bi bi-lock-fill me-2"></i> ระบบปิดรับลงทะเบียน
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="guide-card mb-4">
                    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-lightbulb-fill me-2"></i>คำแนะนำเบื้องต้น</h5>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-1-circle text-primary me-2 fs-5"></i>
                            <span>ลงทะเบียนรายวิชาที่คุณรับผิดชอบสอนในภาคเรียนปัจจุบันให้ครบถ้วน</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi bi-2-circle text-primary me-2 fs-5"></i>
                            <span>หลังลงทะเบียนสำเร็จ ระบบจะนำคุณไปพบหน้าสรุปเพื่ออัปโหลดไฟล์ที่เกี่ยวข้อง</span>
                        </li>
                        <li class="d-flex align-items-start text-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                            <span>ตรวจสอบความถูกต้องของรหัสวิชาและชื่อวิชา เนื่องจากจะไม่สามารถแก้ไขได้เองในภายหลัง</span>
                        </li>
                    </ul>
                </div>

                <div class="card luxe-card bg-label-secondary border-0 text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-shield-lock-fill display-4 opacity-50"></i>
                    </div>
                    <h6 class="fw-bold">ข้อมูลความปลอดภัย</h6>
                    <p class="small mb-0 opacity-75">ข้อมูลรายวิชาจะถูกเชื่อมโยงกับบัญชีผู้ใช้ของคุณ เพื่อใช้ในการประมวลผลการส่งงานและรายงานสรุปสำหรับงานหลักสูตร</p>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Form validation
    (function() {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    // Handle form submission via AJAX for insert_plan
    $('#form_insert_plan').on('submit', function(e) {
        if (!this.checkValidity()) return;
        e.preventDefault();
        
        const btn = $(this).find('button[type="submit"]');
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> กำลังบันทึก...');

        $.ajax({
            url: '<?= site_url('curriculum/insert-plan') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' || response.msg === 'OK') {
                    Swal.fire({
                        icon: 'success',
                        title: 'ลงทะเบียนสำเร็จ',
                        text: 'รระบบกำลังนำคุณไปยังหน้าจัดการแผนการสอน',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '<?= site_url('curriculum') ?>';
                    });
                } else if (response.status === 'duplicate' || response === 2) {
                    Swal.fire('ตรวจพบข้อมูลซ้ำ', 'รหัสวิชานี้ถูกคุณลงทะเบียนไว้แล้วในระบบ', 'warning');
                } else {
                    Swal.fire('ผิดพลาด', response.message || 'ไม่สามารถลงทะเบียนวิชาได้', 'error');
                }
            },
            error: function() {
                Swal.fire('ผิดพลาด', 'เกิดเหตุขัดข้องในการเชื่อมต่อเซิร์ฟเวอร์', 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
