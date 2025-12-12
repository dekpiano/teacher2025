<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'แก้ไขงานวิจัยในชั้นเรียน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">แก้ไขงานวิจัยในชั้นเรียน</h4>
    </div>
    <div class="card-body">
        <form class="needs-validation" novalidate id="form_edit_research" action="<?= site_url('research/update-research') ?>" method="post" enctype="multipart/form-data">
             <input type="hidden" name="seres_ID" value="<?= esc($research['seres_ID'] ?? '') ?>">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="seres_research_name" name="seres_research_name" placeholder="ชื่องานวิจัย" value="<?= esc($research['seres_research_name'] ?? '') ?>" required>
                        <label for="seres_research_name">ชื่องานวิจัย</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="seres_namesubject" name="seres_namesubject" placeholder="ชื่อรายวิชา" value="<?= esc($research['seres_namesubject'] ?? '') ?>" required readonly>
                        <label for="seres_namesubject">ชื่อรายวิชา (ไม่สามารถแก้ไขได้)</label>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="seres_coursecode" name="seres_coursecode" placeholder="รหัสวิชา" value="<?= esc($research['seres_coursecode'] ?? '') ?>" required readonly>
                                <label for="seres_coursecode">รหัสวิชา (ไม่สามารถแก้ไขได้)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-floating mb-3">
                                <select class="form-select" id="seres_gradelevel" name="seres_gradelevel" required readonly>
                                     <?php for ($i = 1; $i <= 6; $i++): ?>
                                        <option value="<?= $i ?>" <?= (isset($research['seres_gradelevel']) && $research['seres_gradelevel'] == $i) ? 'selected' : '' ?>>ม.<?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <label for="seres_gradelevel">ระดับชั้น (ไม่สามารถแก้ไขได้)</label>
                            </div>
                        </div>
                    </div>
                     <div class="form-floating mb-3">
                        <textarea class="form-control" placeholder="รายละเอียดเพิ่มเติม/หมายเหตุ" id="seres_sendcomment" name="seres_sendcomment" style="height: 100px"><?= esc($research['seres_sendcomment'] ?? '') ?></textarea>
                        <label for="seres_sendcomment">รายละเอียดเพิ่มเติม/หมายเหตุ</label>
                    </div>
                     <div class="mb-3">
                        <label for="seres_file" class="form-label">อัปโหลดไฟล์ใหม่ (ถ้าต้องการ)</label>
                        <input class="form-control" type="file" id="seres_file" name="seres_file" accept="application/pdf">
                        <div class="form-text">หากไม่เลือกไฟล์ใหม่ ไฟล์เดิมจะยังคงอยู่</div>
                    </div>
                </div>
                 <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                             <h5 class="card-title">ไฟล์ปัจจุบัน</h5>
                             <?php if (!empty($research['seres_file'])): ?>
                                <p><a href="<?= env('upload.server.baseurl.research') . esc($research['seres_year']) . '/' . esc($research['seres_term']) . '/' . rawurlencode($research['seres_file']) ?>" target="_blank"><i class="bi bi-file-earmark-arrow-down"></i> <?= esc($research['seres_file']) ?></a></p>
                            <?php else: ?>
                                <p class="text-muted">ยังไม่มีไฟล์</p>
                            <?php endif; ?>

                            <h5 class="card-title mt-3">สถานะ</h5>
                             <p class="card-text">
                                <?php if (trim($research['seres_status']) == 'ส่งแล้ว') : ?>
                                    <span class="badge bg-success">ส่งแล้ว</span>
                                <?php elseif (trim($research['seres_status']) == 'ตรวจแล้ว') : ?>
                                    <span class="badge bg-success">ตรวจแล้ว</span>
                                <?php else : ?>
                                    <span class="badge bg-warning">รอดำเนินการ</span>
                                <?php endif; ?>
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
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save me-2"></i> บันทึกการแก้ไข
                </button>
                 <a href="<?= site_url('research') ?>" class="btn btn-secondary btn-lg">
                    <i class="bi bi-x-circle me-2"></i> กลับหน้าหลัก
                </a>
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

    // Handle form submission via AJAX for update_research
    $('#form_edit_research').on('submit', function(e) {
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
            url: '<?= site_url('research/update-research') ?>',
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
                        window.location.href = '<?= site_url('research') ?>'; // Redirect to main page after update
                    });
                } else {
                    Swal.fire('ผิดพลาด', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error, xhr.responseText);
                let msg = 'เกิดข้อผิดพลาดในการส่งข้อมูล';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    msg += ': ' + xhr.responseJSON.message;
                }
                Swal.fire('ผิดพลาด', msg, 'error');
            },
            complete: function() {
                submitButton.prop('disabled', false);
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
