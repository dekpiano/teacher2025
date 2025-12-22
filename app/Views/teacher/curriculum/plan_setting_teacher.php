<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ตั้งค่าครูผู้สอน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .setting-header-luxe {
        background: linear-gradient(135deg, #696cff 0%, #3f42ef 100%);
        border-radius: 1.25rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(105, 108, 255, 0.2);
    }
    .luxe-card {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    .form-section-title {
        color: #696cff;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<div class="container-xxl flex-grow-1">
    <!-- Header -->
    <div class="setting-header-luxe d-flex justify-content-between align-items-center">
        <div>
            <h1 class="display-6 fw-bold mb-1">ตั้งค่ารายวิชาและครูผู้สอน</h1>
            <p class="opacity-75 mb-0">บริหารจัดการรายวิชาและกำหนดครูผู้สอนประจำแผนการเรียน</p>
        </div>
        <a href="<?= base_url('curriculum') ?>" class="btn btn-white btn-lg rounded-pill shadow-sm px-4 text-primary fw-bold">
            <i class="bi bi-arrow-left me-2"></i> ย้อนกลับ
        </a>
    </div>

    <div class="row g-4">
        <!-- Add Section -->
        <div class="col-lg-8">
            <div class="card luxe-card p-4">
                <h5 class="form-section-title"><i class="bi bi-plus-circle-dotted"></i> เพิ่มข้อมูลรายวิชาทีละรายการ</h5>
                <form class="needs-validation" novalidate id="form_insert_plan">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-1">
                                <input type="text" class="form-control border-0 bg-light" placeholder="รหัสวิชา" id="seplan_coursecode" name="seplan_coursecode" required>
                                <label for="seplan_coursecode">รหัสวิชา</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-1">
                                <input type="text" class="form-control border-0 bg-light" placeholder="ชื่อวิชา" id="seplan_namesubject" name="seplan_namesubject" required>
                                <label for="seplan_namesubject">ชื่อวิชา</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-1">
                                <select class="form-select border-0 bg-light" id="seplan_gradelevel" name="seplan_gradelevel" required>
                                    <option value="" selected disabled>เลือก...</option>
                                    <?php for($i=1; $i<=6; $i++): ?>
                                        <option value="<?= $i ?>">ม.<?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <label for="seplan_gradelevel">ระดับชั้น</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-1">
                                <select class="form-select border-0 bg-light" id="seplan_typesubject" name="seplan_typesubject" required>
                                    <option value="" selected disabled>เลือก...</option>
                                    <option value="พื้นฐาน">พื้นฐาน</option>
                                    <option value="เพิ่มเติม">เพิ่มเติม</option>
                                </select>
                                <label for="seplan_typesubject">ประเภทรายวิชา</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-1">
                                <select class="form-select border-0 bg-light" id="seplan_usersend" name="seplan_usersend" required>
                                    <option value="" selected disabled>เลือกครูผู้สอน...</option>
                                    <?php foreach ($pers as $v_pers): ?>
                                        <option value="<?= esc($v_pers->pers_id) ?>">
                                            <?= esc($v_pers->pers_prefix . $v_pers->pers_firstname . ' ' . $v_pers->pers_lastname) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="seplan_usersend">ครูผู้สอน</label>
                            </div>
                        </div>
                        <div class="col-12 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                                <i class="bi bi-save-fill me-2"></i> บันทึกรายการ
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Import Section -->
        <div class="col-lg-4">
            <div class="card luxe-card p-4 h-100">
                <h5 class="form-section-title"><i class="bi bi-file-earmark-arrow-up"></i> นำเข้าข้อมูล (Bulk)</h5>
                <p class="small text-muted mb-4 text-start">อัปโหลดไฟล์ CSV หรือ Excel เพื่อเพิ่มข้อมูลรายวิชาและครูผู้สอนจำนวนมากในครั้งเดียว</p>
                <form action="<?= site_url('curriculum/upload-plan') ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-4">
                        <input class="form-control" type="file" id="formFile" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                    </div>
                    <button class="btn btn-label-primary w-100 btn-lg rounded-pill" type="submit">
                        <i class="bi bi-cloud-upload me-2"></i> อัปโหลดไฟล์
                    </button>
                </form>
                <div class="mt-4 p-3 bg-label-secondary border-radius-1 shadow-none rounded-3 text-start">
                    <h6 class="fw-bold mb-1 small"><i class="bi bi-info-circle me-1"></i> หมายเหตุ</h6>
                    <p class="mb-0 x-small text-muted">ตรวจสอบรูปแบบคอลัมน์ให้ถูกต้องตามเทมเพลตที่กำหนดก่อนการอัปโหลด</p>
                </div>
            </div>
        </div>

        <!-- List Section -->
        <div class="col-12">
            <div class="card luxe-card overflow-hidden">
                <div class="card-header bg-white py-4 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-table text-primary me-2"></i>รายการวิชาที่สอนทั้งหมด</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="TableShoowPlan">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">ปีการศึกษา</th>
                                <th>รายวิชา</th>
                                <th>ระดับ</th>
                                <th>ประเภท</th>
                                <th>ครูผู้สอน</th>
                                <th class="text-end pe-4">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($Plan)): ?>
                                <?php foreach ($Plan as $v_Plan): ?>
                                    <tr id="<?= esc($v_Plan->seplan_coursecode) ?>" class="align-middle">
                                        <td class="ps-4">
                                            <span class="badge bg-label-secondary"><?= esc($v_Plan->seplan_year) ?>/<?= esc($v_Plan->seplan_term) ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= esc($v_Plan->seplan_coursecode) ?></div>
                                            <div class="small text-muted"><?= esc($v_Plan->seplan_namesubject) ?></div>
                                        </td>
                                        <td>ม.<?= esc($v_Plan->seplan_gradelevel) ?></td>
                                        <td><span class="badge bg-label-info"><?= esc($v_Plan->seplan_typesubject) ?></span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xs me-2"><span class="avatar-initial rounded-circle bg-label-primary"><?= mb_substr($v_Plan->pers_firstname, 0, 1) ?></span></div>
                                                <span><?= esc($v_Plan->pers_prefix . $v_Plan->pers_firstname . ' ' . $v_Plan->pers_lastname) ?></span>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group shadow-sm">
                                                <button class="btn btn-sm btn-white EditTeach" 
                                                    PlanCode="<?= esc($v_Plan->seplan_coursecode) ?>" 
                                                    PlanYear="<?= esc($v_Plan->seplan_year) ?>" 
                                                    PlanTerm="<?= esc($v_Plan->seplan_term) ?>"
                                                    data-bs-toggle="modal" data-bs-target="#editteacher">
                                                    <i class="bi bi-pencil text-warning"></i>
                                                </button>
                                                <button class="btn btn-sm btn-white DeleteTeach"
                                                    delplancode="<?= esc($v_Plan->seplan_coursecode) ?>"
                                                    delplanyear="<?= esc($v_Plan->seplan_year) ?>" 
                                                    delplanterm="<?= esc($v_Plan->seplan_term) ?>"
                                                    delplanname="<?= esc($v_Plan->seplan_namesubject) ?>">
                                                    <i class="bi bi-trash text-danger"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="opacity-25 mb-3"><i class="bi bi-inbox display-4"></i></div>
                                        <h6 class="text-muted">ยังไม่มีข้อมูลรายวิชาที่สอนในระบบ</h6>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editteacher" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning py-3">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-pencil-square me-2"></i>แก้ไขข้อมูลการสอน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="needs-validation" novalidate id="FromUpdateTeacher">
                <div class="modal-body p-4 text-start">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-floating">
                                <input readonly type="text" class="form-control border-0 bg-light" id="up_seplan_year" name="up_seplan_year">
                                <label for="up_seplan_year">ปีการศึกษา</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input readonly type="text" class="form-control border-0 bg-light" id="up_seplan_term" name="up_seplan_term">
                                <label for="up_seplan_term">ภาคเรียน</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input readonly type="text" class="form-control border-0 bg-light" id="up_seplan_coursecode" name="up_seplan_coursecode">
                                <label for="up_seplan_coursecode">รหัสวิชา</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-light border-0" id="up_seplan_namesubject" name="up_seplan_namesubject" required>
                                <label for="up_seplan_namesubject">ชื่อวิชา</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <select class="form-select border-0 bg-light" id="up_seplan_gradelevel" name="up_seplan_gradelevel" required>
                                    <?php for($i=1; $i<=6; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <label for="up_seplan_gradelevel">ระดับชั้น ม.</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <select class="form-select border-0 bg-light" id="up_seplan_typesubject" name="up_seplan_typesubject" required>
                                    <option value="พื้นฐาน">พื้นฐาน</option>
                                    <option value="เพิ่มเติม">เพิ่มเติม</option>
                                </select>
                                <label for="up_seplan_typesubject">ประเภท</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <select class="form-select border-0 bg-light" id="up_seplan_usersend" name="up_seplan_usersend" required>
                                    <?php foreach ($pers as $v_pers): ?>
                                        <option value="<?= esc($v_pers->pers_id) ?>">
                                            <?= esc($v_pers->pers_prefix . $v_pers->pers_firstname . ' ' . $v_pers->pers_lastname) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="up_seplan_usersend">ครูผู้สอน</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning px-4 shadow-sm text-white">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
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

    // Handle insert form submission
    $('#form_insert_plan').on('submit', function(e) {
        if (!this.checkValidity()) return;
        e.preventDefault();
        
        const btn = $(this).find('button[type="submit"]');
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> บันทึก...');

        $.ajax({
            url: '<?= site_url('curriculum/insert-plan') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.msg === 'OK') {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ', text: 'บันทึกข้อมูลสำเร็จ', timer: 1500 }).then(() => location.reload());
                } else if (response.msg === 2) {
                    Swal.fire('ผิดพลาด', 'รหัสวิชานี้ถูกลงทะเบียนไว้แล้ว', 'error');
                } else {
                    Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                }
            },
            error: function() {
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการส่งข้อมูล', 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Populate edit modal
    $('.EditTeach').on('click', function() {
        const planCode = $(this).attr('PlanCode');
        const planYear = $(this).attr('PlanYear');
        const planTerm = $(this).attr('PlanTerm');

        $.ajax({
            url: '<?= site_url('curriculum/setting-teacher-edit') ?>',
            type: 'POST',
            data: { PlanCode: planCode, PlanYear: planYear, PlanTerm: planTerm },
            dataType: 'json',
            success: function(response) {
                if (response && response[0]) {
                    const data = response[0];
                    $('#up_seplan_year').val(data.seplan_year);
                    $('#up_seplan_term').val(data.seplan_term);
                    $('#up_seplan_coursecode').val(data.seplan_coursecode);
                    $('#up_seplan_namesubject').val(data.seplan_namesubject);
                    $('#up_seplan_gradelevel').val(data.seplan_gradelevel);
                    $('#up_seplan_typesubject').val(data.seplan_typesubject);
                    $('#up_seplan_usersend').val(data.seplan_usersend);
                }
            }
        });
    });

    // Update
    $('#FromUpdateTeacher').on('submit', function(e) {
        if (!this.checkValidity()) return;
        e.preventDefault();
        
        $.ajax({
            url: '<?= site_url('curriculum/setting-teacher-update') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response == 1) {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ', text: 'อัปเดตข้อมูลสำเร็จ', timer: 1500 }).then(() => location.reload());
                } else {
                    Swal.fire('ผิดพลาด', 'ไม่สามารถอัปเดตข้อมูลได้', 'error');
                }
            }
        });
    });

    // Delete
    $('.DeleteTeach').on('click', function() {
        const code = $(this).attr('delplancode');
        const year = $(this).attr('delplanyear');
        const term = $(this).attr('delplanterm');
        const name = $(this).attr('delplanname');

        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: `คุณกำลังจะลบวิชา ${code} ${name} ออกจากระบบ`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff3e1d',
            cancelButtonColor: '#8592a3',
            confirmButtonText: 'ลบข้อมูล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('curriculum/setting-teacher-delete') ?>',
                    type: 'POST',
                    data: { PlanCode: code, PlanYear: year, PlanTerm: term, PlanName: name },
                    dataType: 'json',
                    success: function(response) {
                        if (response == 1) {
                            Swal.fire('สำเร็จ', 'ข้อมูลถูกลบออกจากระบบแล้ว', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('ผิดพลาด', 'ไม่สามารถลบข้อมูลได้', 'error');
                        }
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
