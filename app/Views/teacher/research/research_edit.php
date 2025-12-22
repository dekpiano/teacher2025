<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'แก้ไขงานวิจัยในชั้นเรียน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .edit-research-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    .form-header-edit {
        background: #fff;
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-left: 6px solid #ffab00; /* Yellow for edit */
    }
    .luxe-edit-card {
        background: #fff;
        border-radius: 1.25rem;
        padding: 2.5rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.01);
    }
    .readonly-field {
        background-color: #f8f9fa !important;
        cursor: not-allowed;
    }
    .current-file-box {
        background: rgba(105, 108, 255, 0.04);
        border: 1px solid rgba(105, 108, 255, 0.1);
        border-radius: 0.75rem;
        padding: 1.25rem;
    }
</style>

<div class="edit-research-container py-3">
    <!-- Form Header -->
    <div class="form-header-edit d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1 text-dark">แก้ไขข้อมูลงานวิจัย</h3>
            <p class="text-muted mb-0 small">ภาคเรียน <?= esc($research['seres_term'].'/'.$research['seres_year']) ?> | <?= esc($research['seres_coursecode']) ?></p>
        </div>
        <div>
            <?php 
                $status = trim($research['seres_status']);
                $badgeClass = 'bg-label-warning';
                if($status == 'ส่งแล้ว') $badgeClass = 'bg-label-primary';
                if($status == 'ตรวจแล้ว') $badgeClass = 'bg-label-success';
            ?>
            <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-1"><?= $status ?></span>
        </div>
    </div>

    <!-- Main Edit Form -->
    <div class="luxe-edit-card">
        <form class="needs-validation" novalidate id="form_edit_research" action="<?= site_url('research/update-research') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="seres_ID" value="<?= esc($research['seres_ID'] ?? '') ?>">
            
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="seres_research_name" name="seres_research_name" placeholder="ชื่องานวิจัย" value="<?= esc($research['seres_research_name'] ?? '') ?>" required>
                        <label for="seres_research_name">หัวข้อ/ชื่องานวิจัยในชั้นเรียน</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="text" class="form-control readonly-field" id="seres_namesubject" placeholder="ชื่อรายวิชา" value="<?= esc($research['seres_namesubject'] ?? '') ?>" disabled>
                        <label for="seres_namesubject">ชื่อรายวิชา (แก้ไขไม่ได้)</label>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <input type="text" class="form-control readonly-field" id="seres_coursecode" placeholder="รหัสวิชา" value="<?= esc($research['seres_coursecode'] ?? '') ?>" disabled>
                                <label for="seres_coursecode">รหัสวิชา (แก้ไขไม่ได้)</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <?php 
                                    $grade = esc($research['seres_gradelevel'] ?? '');
                                    $display_grade = "มัธยมศึกษาปีที่ " . $grade;
                                ?>
                                <input type="text" class="form-control readonly-field" id="seres_gradelevel" value="<?= $display_grade ?>" disabled>
                                <label for="seres_gradelevel">ระดับชั้น (แก้ไขไม่ได้)</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mb-4">
                        <textarea class="form-control" placeholder="รายละเอียดเพิ่มเติม" id="seres_sendcomment" name="seres_sendcomment" style="height: 120px"><?= esc($research['seres_sendcomment'] ?? '') ?></textarea>
                        <label for="seres_sendcomment">รายละเอียดเพิ่มเติม / บทคัดย่อโดยย่อ</label>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark d-block">อัปเดตไฟล์งานวิจัย (PDF)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-cloud-upload text-primary"></i></span>
                            <input class="form-control" type="file" id="seres_file" name="seres_file" accept=".pdf">
                        </div>
                        <div class="form-text small">ปล่อยว่างไว้หากไม่ต้องการเปลี่ยนไฟล์ใหม่</div>
                    </div>
                </div>

                <!-- File Status Sidebar -->
                <div class="col-md-4">
                    <div class="current-file-box h-100">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-check me-1"></i> ไฟล์ปัจจุบัน</h6>
                        <?php if (!empty($research['seres_file'])): ?>
                            <div class="d-grid mb-4">
                                <a href="<?= env('upload.server.baseurl.research') . esc($research['seres_year']) . '/' . esc($research['seres_term']) . '/' . rawurlencode($research['seres_file']) ?>" 
                                   target="_blank" class="btn btn-label-primary btn-sm">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> เปิดดูไฟล์เดิม
                                </a>
                                <small class="text-muted mt-2 d-block text-truncate"><?= esc($research['seres_file']) ?></small>
                            </div>
                        <?php else: ?>
                            <p class="small text-muted mb-4">ยังไม่ได้แนบไฟล์</p>
                        <?php endif; ?>

                        <hr class="my-4 opacity-50">
                        
                        <h6 class="fw-bold text-dark mb-2">ข้อจำกัดการแก้ไข</h6>
                        <p class="small text-muted mb-0">คุณไม่สามารถแก้ไข รายวิชา, รหัสวิชา และระดับชั้นได้ หากต้องการแก้ไขข้อมูลเหล่านี้ กรุณาลบและสร้างรายการใหม่</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-center gap-3 mt-5">
                <a href="<?= site_url('research') ?>" class="btn btn-label-secondary btn-lg px-5">ยกเลิก</a>
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                    <i class="bi bi-check-circle-fill me-2"></i> บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#form_edit_research').on('submit', function(e) {
        e.preventDefault();
        
        if (this.checkValidity() === false) {
            this.classList.add('was-validated');
            return;
        }

        var formData = new FormData(this);
        const submitButton = $(this).find('button[type="submit"]');
        
        Swal.fire({
            title: 'กำลังอัปเดตข้อมูล...',
            text: 'ระบบกำลังดำเนินการบันทึกข้อมูลและไฟล์ใหม่',
            allowOutsideClick: false,
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
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'อัปเดตสําเร็จ!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '<?= site_url('research') ?>';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'อัปเดตไม่สำเร็จ',
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
