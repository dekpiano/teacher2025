<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ดาวน์โหลดแผนการสอน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .download-header-luxe {
        background: linear-gradient(135deg, #696cff 0%, #3f42ef 100%);
        border-radius: 1.25rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(105, 108, 255, 0.2);
    }
    .teacher-card-luxe {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        background: #fff;
        height: 100%;
    }
    .teacher-card-luxe:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(105, 108, 255, 0.15);
    }
    .teacher-avatar-wrapper {
        position: relative;
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
    }
    .teacher-avatar-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .btn-download-luxe {
        background: #f8f9fa;
        color: #696cff;
        border: 1px solid #e7e7ff;
        border-radius: 0.75rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-download-luxe:hover {
        background: #696cff;
        color: #fff;
        border-color: #696cff;
    }
</style>

<div class="container-xxl flex-grow-1">
    <!-- Header -->
    <div class="download-header-luxe d-flex justify-content-between align-items-center">
        <div>
            <h1 class="display-6 fw-bold mb-1">คลังแผนการสอน</h1>
            <p class="opacity-75 mb-0">ดาวน์โหลดแผนการสอนรายบุคคล (ไฟล์รวม ZIP)</p>
        </div>
        <a href="<?= base_url('curriculum') ?>" class="btn btn-white btn-lg rounded-pill shadow-sm px-4 text-primary fw-bold">
            <i class="bi bi-house-door me-2"></i> หน้าหลัก
        </a>
    </div>

    <!-- Teacher Grid -->
    <div class="row g-4">
        <?php if (!empty($teacher)): ?>
            <?php foreach ($teacher as $v_teacher) : ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="teacher-card-luxe p-4 text-center d-flex flex-column">
                        <div class="teacher-avatar-wrapper">
                            <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= esc($v_teacher->pers_img) ?>" 
                                 alt="<?= esc($v_teacher->pers_firstname) ?>" 
                                 class="rounded-circle">
                            <span class="position-absolute bottom-0 end-0 badge bg-success border border-white rounded-circle p-2" title="พร้อมดาวน์โหลด">
                                <span class="visually-hidden">Status</span>
                            </span>
                        </div>
                        
                        <h6 class="fw-bold mb-1"><?= esc($v_teacher->pers_prefix . $v_teacher->pers_firstname . ' ' . $v_teacher->pers_lastname) ?></h6>
                        <p class="text-muted small mb-4"><?= esc($v_teacher->pers_position ?? 'ครูผู้สอน') ?></p>
                        
                        <div class="mt-auto">
                            <a href="<?= site_url('curriculum/download-plan-zip/' . $v_teacher->pers_id) ?>" 
                               class="btn btn-download-luxe w-100 py-2">
                                <i class="bi bi-file-earmark-zip me-2"></i> ดาวน์โหลด ZIP
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card luxe-card p-5 text-center">
                    <i class="bi bi-people display-4 opacity-25 mb-3"></i>
                    <h5 class="text-muted">ไม่พบข้อมูลครูผู้สอนในระบบ</h5>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
