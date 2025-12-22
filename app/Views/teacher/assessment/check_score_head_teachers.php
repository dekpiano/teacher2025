<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .teacher-card {
        transition: all 0.2s ease-in-out;
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 12px;
        overflow: hidden;
    }
    .teacher-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(105, 108, 255, 0.15);
        border-color: rgba(105, 108, 255, 0.3);
    }
    .avatar-wrapper {
        position: relative;
        display: inline-block;
        padding: 4px;
        background: linear-gradient(45deg, #696cff, #e7e7ff);
        border-radius: 50%;
        margin-bottom: 1rem;
    }
    .teacher-avatar {
        width: 80px;
        height: 80px;
        border: 3px solid #fff;
        border-radius: 50%;
        object-fit: cover;
    }
    .teacher-name {
        font-weight: 700;
        color: #566a7f;
        margin-bottom: 0.5rem;
        font-size: 1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .btn-view-scores {
        border-radius: 8px;
        padding: 0.5rem 1.25rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .page-header {
        margin-bottom: 2rem;
        border-bottom: 2px solid #f5f5f9;
        padding-bottom: 1rem;
    }
</style>

<div class="container-xxl flex-grow-1">
    <div class="page-header d-flex align-items-center">
        <div class="avatar avatar-md bg-label-primary me-3 flex-shrink-0">
            <i class="bi bi-clipboard-data fs-3"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-0">ตรวจสอบการบันทึกคะแนน</h4>
            <small class="text-muted">เลือกครูที่ต้องการตรวจสอบความคืบหน้าการบันทึกคะแนนในกลุ่มสาระ</small>
        </div>
    </div>

    <div class="row g-4">
        <?php if (empty($teachers)): ?>
            <div class="col-12">
                <div class="card bg-label-warning border-0">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-exclamation-triangle fs-1 mb-3"></i>
                        <h5>ไม่พบข้อมูลครูในกลุ่มสาระของท่าน</h5>
                        <p class="mb-0">กรุณาติดต่อเจ้าหน้าที่หากข้อมูลไม่ถูกต้อง</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($teachers as $teacher): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="card teacher-card h-100 shadow-none">
                        <div class="card-body text-center d-flex flex-column align-items-center justify-content-center py-4">
                            <div class="avatar-wrapper">
                                <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= esc($teacher->pers_img) ?>" 
                                     alt="<?= esc($teacher->pers_firstname) ?>" 
                                     class="teacher-avatar" 
                                     onerror="this.onerror=null;this.src='https://placehold.co/100x100/EFEFEF/AAAAAA&text=No+Image';">
                            </div>
                            <div class="mb-3 w-100 px-2">
                                <h6 class="teacher-name mb-1">
                                    <?= esc($teacher->pers_prefix) ?>
                                </h6>
                                <div class="fw-bold text-dark fs-5 mb-2">
                                    <?= esc($teacher->pers_firstname . ' ' . $teacher->pers_lastname) ?>
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-mortarboard me-1"></i> ชั้นที่สอน: 
                                    <?php if (!empty($teacher->classes)): ?>
                                        <div class="mt-1 d-flex flex-wrap justify-content-center gap-1">
                                            <?php 
                                            $class_list = array_map(function($c) { return $c->RegisterClass; }, $teacher->classes);
                                            // Show first 3 classes and count the rest if many
                                            if (count($class_list) > 4) {
                                                echo '<span class="badge bg-label-secondary p-1">' . implode('</span> <span class="badge bg-label-secondary p-1">', array_slice($class_list, 0, 3)) . '</span>';
                                                echo ' <span class="badge bg-label-secondary p-1">+' . (count($class_list) - 3) . '</span>';
                                            } else {
                                                echo '<span class="badge bg-label-secondary p-1">' . implode('</span> <span class="badge bg-label-secondary p-1">', $class_list) . '</span>';
                                            }
                                            ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-light-50">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <a href="<?= site_url('assessment-head/check-score-detail/' . $teacher->pers_id) ?>" 
                               class="btn btn-outline-primary btn-sm btn-view-scores mt-auto w-100 rounded-pill">
                               <i class="bi bi-bar-chart-fill me-1"></i> ดูคะแนน
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
