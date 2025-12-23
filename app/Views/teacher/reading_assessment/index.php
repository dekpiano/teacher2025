<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'แบบประเมินการอ่าน คิดวิเคราะห์ และเขียน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .assessment-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        border-radius: 1.25rem;
        overflow: hidden;
    }
    .assessment-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .assessment-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1rem;
    }
    .progress-minimal {
        height: 8px;
        border-radius: 10px;
        background-color: rgba(0,0,0,0.05);
    }
    .glass-header {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        color: white;
        border-radius: 1.25rem;
        padding: 2.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .glass-header::after {
        content: "";
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        pointer-events: none;
    }
    .smallest {
        font-size: 0.65rem;
    }
</style>

<div class="container-fluid py-2">
    <!-- Header Section -->
    <div class="glass-header shadow-lg">
        <div class="row align-items-center text-start">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-light mb-2">
                        <li class="breadcrumb-item"><a href="<?= site_url('home') ?>" class="text-white opacity-75">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">แบบประเมินการอ่านฯ</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold text-white mb-2">
                    <i class="bi bi-journal-bookmark-fill me-2"></i><?= esc($title ?? 'แบบประเมินการอ่าน คิดวิเคราะห์ และเขียน') ?>
                </h1>
                <p class="lead mb-0 text-white opacity-75">
                    ประเมินการอ่าน คิดวิเคราะห์ และเขียน ประจำปีการศึกษา <?= esc($term) ?>/<?= esc($academicYear) ?>
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <div class="badge bg-white text-primary rounded-pill px-4 py-3 shadow-sm border">
                    <i class="bi bi-clock-history me-2"></i> 
                    สถานะระบบ: <?= $assessmentStatus === 'on' ? 'เปิดให้ประเมิน' : 'ปิดระบบ' ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($assessmentStatus !== 'on') : ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-4 py-3 mb-4 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div>
                <h6 class="alert-heading fw-bold mb-0">ระบบปิดการประเมิน</h6>
                <span class="small text-danger">กรุณาติดต่อฝ่ายวิชาการเพื่อเปิดใช้งานระบบ</span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <?php if (!empty($teacherClasses)) : ?>
        <div class="row g-4 text-start">
            <?php foreach ($teacherClasses as $class) : ?>
                <?php
                    $status = $class['status'] ?? ['total' => 0, 'assessed' => 0];
                    $total = $status['total'];
                    $assessed = $status['assessed'];
                    $percentage = ($total > 0) ? round(($assessed / $total) * 100) : 0;
                    
                    $statusText = 'ยังไม่ประเมิน';
                    $statusColor = 'secondary';
                    $statusIcon = 'bi-circle';

                    if ($assessed > 0) {
                        if ($assessed >= $total) {
                            $statusText = 'ครบแล้ว';
                            $statusColor = 'success';
                            $statusIcon = 'bi-check-circle-fill';
                        } else {
                            $statusText = 'บางส่วน';
                            $statusColor = 'warning';
                            $statusIcon = 'bi-dash-circle-fill';
                        }
                    }
                    $isDisabled = ($assessmentStatus !== 'on');
                ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card assessment-card shadow-sm h-100 border-0">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="assessment-icon-wrapper bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <span class="badge rounded-pill bg-<?= $statusColor ?> bg-opacity-10 text-<?= $statusColor ?> px-3 py-2 border border-<?= $statusColor ?> border-opacity-25">
                                    <i class="bi <?= $statusIcon ?> me-1"></i> <?= $statusText ?>
                                </span>
                            </div>

                            <h4 class="card-title fw-bold mb-1 text-dark">ชั้นมัธยมศึกษาปีที่ <?= esc($class['Reg_Class']) ?></h4>
                            <p class="text-muted small mb-4">
                                ห้องที่ที่ท่านเป็นครูที่ปรึกษาในการประเมิน
                            </p>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">ความคืบหน้าการประเมิน</span>
                                    <span class="fw-bold small"><?= $assessed ?> / <?= $total ?> คน</span>
                                </div>
                                <div class="progress progress-minimal">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $percentage ?>%" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="mt-auto pt-3 border-top">
                                <div class="row g-2">
                                    <div class="col-8">
                                        <a href="<?= base_url('teacher/reading_assessment/assess/' . $class['Reg_Class']) ?>" 
                                           class="btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow-sm <?= $isDisabled ? 'disabled' : '' ?>">
                                            <i class="bi bi-pencil-square me-2"></i> ประเมิน
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="<?= base_url('teacher/reading_assessment/print_report/' . $class['Reg_Class']) ?>" 
                                           target="_blank" 
                                           class="btn btn-outline-primary btn-lg rounded-pill w-100 fw-bold shadow-sm <?= $isDisabled ? 'disabled' : '' ?>">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-light">
            <div class="mb-4 text-primary opacity-25">
                <i class="bi bi-clipboard-x" style="font-size: 5rem;"></i>
            </div>
            <h3 class="text-muted mb-3 fw-bold">ไม่พบข้อมูลชั้นเรียน</h3>
            <p class="text-muted mb-4 px-4 mx-auto" style="max-width: 500px;">ท่านยังไม่ได้รับมอบหมายให้เป็นที่ปรึกษาในการประเมินการอ่านฯ สำหรับปีการศึกษานี้</p>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
