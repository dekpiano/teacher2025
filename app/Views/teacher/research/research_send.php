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

<?php if($is_system_on): ?>
<div class="alert alert-success d-flex align-items-center" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>
    <div>
        <strong>แจ้งเตือน!</strong> ระบบเปิดให้ส่งงานวิจัยในชั้นเรียน
        <strong> (สิ้นสุด: <?= $deadline ? thai_date_and_time(strtotime($deadline)) : '-' ?>)</strong>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger d-flex align-items-center" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <div>
        <strong>แจ้งเตือน!</strong> ขณะนี้ระบบปิดรับส่งงานวิจัยในชั้นเรียน
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">ส่งงานวิจัยในชั้นเรียน</h4>
    </div>
    <div class="card-body">
        <form class="needs-validation" novalidate id="form_insert_research" action="<?= site_url('research/insert-research') ?>" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="seres_research_name" name="seres_research_name" placeholder="ชื่องานวิจัย" required>
                        <label for="seres_research_name">ชื่องานวิจัย</label>
                        <div class="invalid-feedback">กรุณากรอกชื่อหัวข้อวิจัย</div>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="seres_namesubject" name="seres_namesubject" placeholder="ชื่อรายวิชา" required>
                        <label for="seres_namesubject">ชื่อรายวิชา</label>
                        <div class="invalid-feedback">กรุณากรอกชื่อรายวิชา</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="seres_coursecode" name="seres_coursecode" placeholder="รหัสวิชา" required>
                                <label for="seres_coursecode">รหัสวิชา</label>
                                <div class="invalid-feedback">กรุณากรอกรหัสวิชา</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-floating mb-3">
                                <select class="form-select" id="seres_gradelevel" name="seres_gradelevel" required>
                                    <option value="" selected disabled>เลือก...</option>
                                    <option value="1">ม.1</option>
                                    <option value="2">ม.2</option>
                                    <option value="3">ม.3</option>
                                    <option value="4">ม.4</option>
                                    <option value="5">ม.5</option>
                                    <option value="6">ม.6</option>
                                </select>
                                <label for="seres_gradelevel">ระดับชั้น</label>
                                <div class="invalid-feedback">กรุณาเลือกระดับชั้น</div>
                            </div>
                        </div>
                    </div>
                     <div class="form-floating mb-3">
                        <textarea class="form-control" placeholder="รายละเอียดเพิ่มเติม/หมายเหตุ" id="seres_sendcomment" name="seres_sendcomment" style="height: 100px"></textarea>
                        <label for="seres_sendcomment">รายละเอียดเพิ่มเติม/หมายเหตุ</label>
                    </div>
                     <div class="mb-3">
                        <label for="seres_file" class="form-label">อัปโหลดไฟล์งานวิจัย</label>
                        <input class="form-control" type="file" id="seres_file" name="seres_file" accept="application/pdf" required>
                        <div class="invalid-feedback">กรุณาเลือกไฟล์งานวิจัย</div>
                    </div>
                </div>
                 <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">คำแนะนำ</h5>
                            <p class="card-text">
                                กรุณากรอกข้อมูลและอัปโหลดไฟล์งานวิจัยให้ครบถ้วนก่อนกดบันทึก
                            </p>
                            <h5 class="card-title mt-3">ไฟล์ที่รองรับ</h5>
                            <p class="card-text">
                                ระบบรองรับไฟล์ PDF เท่านั้น
                            </p>
                        </div>
                    </div>
                </div>
            </div>
             <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary btn-lg" <?= $is_system_on ? '' : 'disabled' ?>>
                    <i class="bi bi-save me-2"></i> บันทึกและส่งงานวิจัย
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    // Handle file input change event for PDF validation
    const fileInput = document.getElementById('seres_file');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const fileName = file.name;
                const fileExt = fileName.split('.').pop().toLowerCase();
                if (fileExt !== 'pdf') {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด!',
                        text: 'ระบบรองรับไฟล์ PDF เท่านั้น',
                    });
                    this.value = ''; // Clear the file input
                }
            }
        });
    }

    // Handle form submission via AJAX for insert_research
    $('#form_insert_research').on('submit', function(e) {
        e.preventDefault();
        
        // Check standard HTML5 validation first
        if (this.checkValidity() === false) {
            this.classList.add('was-validated');
            return;
        }

        var formData = new FormData(this); // Use FormData for file uploads
        const submitButton = $(this).find('button[type="submit"]');
        
        // Show Loading Swal
        Swal.fire({
            title: 'กำลังบันทึกข้อมูล...',
            text: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            allowEscapeKey: false,
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
            processData: false, // Important: tell jQuery not to process the data
            contentType: false, // Important: tell jQuery not to set contentType
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: response.message,
                        showConfirmButton: true
                    }).then(() => {
                        window.location.href = '<?= site_url('research') ?>'; // Redirect to main page after success
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error, xhr.responseText);
                let msg = 'เกิดข้อผิดพลาดในการส่งข้อมูล';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    msg += ': ' + xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด',
                    text: msg
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
