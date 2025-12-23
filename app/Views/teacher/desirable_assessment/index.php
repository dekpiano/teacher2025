<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ประเมินคุณลักษณะอันพึงประสงค์') ?>
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
        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
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

<div class="container-fluid py-2 text-start">
    <!-- Header Section -->
    <div class="glass-header shadow-lg">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-light mb-2">
                        <li class="breadcrumb-item"><a href="<?= site_url('home') ?>" class="text-white opacity-75">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">แบบประเมินคุณลักษณะฯ</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold text-white mb-2">
                    <i class="bi bi-star-fill me-2"></i><?= esc($title ?? 'ประเมินคุณลักษณะอันพึงประสงค์') ?>
                </h1>
                <p class="lead mb-0 text-white opacity-75">
                    ประเมินคุณลักษณะอันพึงประสงค์ 8 ประการ ประจำปีการศึกษา <?= esc($term) ?>/<?= esc($academicYear) ?>
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <div class="badge bg-white text-primary rounded-pill px-4 py-3 shadow-sm border">
                    <i class="bi bi-check-circle-fill me-2"></i> 
                    สถานะ: <?= $assessmentStatus === 'on' ? 'เปิดประเมินปกติ' : 'ระบบปิด' ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($assessmentStatus !== 'on') : ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-4 py-3 mb-4 d-flex align-items-center">
            <i class="bi bi-shield-lock-fill fs-4 me-3"></i>
            <div>
                <h6 class="alert-heading fw-bold mb-0">ระบบปิดการประเมิน</h6>
                <span class="small">กรุณาติดต่อผู้ดูแลระบบเพื่อเปิดใช้งานระบบการบันทึกข้อมูล</span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <?php if (!empty($teacherClasses)) : ?>
        <div class="row g-4">
            <?php foreach ($teacherClasses as $class) : ?>
                <?php
                    $status = $class['status'] ?? ['total' => 0, 'assessed' => 0];
                    $total = $status['total'];
                    $assessed = $status['assessed'];
                    $percentage = ($total > 0) ? round(($assessed / $total) * 100) : 0;
                    
                    $statusText = 'รอประเมิน';
                    $statusColor = 'secondary';
                    $statusIcon = 'bi-circle';

                    if ($assessed > 0) {
                        if ($assessed >= $total) {
                            $statusText = 'ประเมินครบแล้ว';
                            $statusColor = 'success';
                            $statusIcon = 'bi-check-circle-fill';
                        } else {
                            $statusText = 'กำลังดำเนินการ';
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
                                <div class="assessment-icon-wrapper bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-award-fill"></i>
                                </div>
                                <span class="badge rounded-pill bg-<?= $statusColor ?> bg-opacity-10 text-<?= $statusColor ?> px-3 py-2 border border-<?= $statusColor ?> border-opacity-25">
                                    <i class="bi <?= $statusIcon ?> me-1"></i> <?= $statusText ?>
                                </span>
                            </div>

                            <h4 class="card-title fw-bold mb-1 text-dark">ชั้นมัธยมศึกษาปีที่ <?= esc($class['Reg_Class']) ?></h4>
                            <p class="text-muted small mb-4">
                                ประเมินคุณลักษณะอันพึงประสงค์ของนักเรียนห้องที่ท่านเป็นครูที่ปรึกษา
                            </p>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">ความคืบหน้าการส่งผล</span>
                                    <span class="fw-bold small"><?= $assessed ?> / <?= $total ?> คน</span>
                                </div>
                                <div class="progress progress-minimal">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?= $percentage ?>%" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="mt-auto pt-3 border-top">
                                <div class="row g-2">
                                    <div class="col-8">
                                        <a href="<?= base_url('teacher/desirable_assessment/assess/' . $class['Reg_Class']) ?>" 
                                           class="btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow-sm <?= $isDisabled ? 'disabled' : '' ?>">
                                            <i class="bi bi-pencil-square me-2"></i> บันทึกผล
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="<?= base_url('teacher/desirable_assessment/print_report/' . $class['Reg_Class']) ?>" 
                                           target="_blank" 
                                           class="btn btn-outline-info btn-lg rounded-pill w-100 fw-bold shadow-sm <?= $isDisabled ? 'disabled' : '' ?>">
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
            <div class="mb-4 text-info opacity-25">
                <i class="bi bi-clipboard-data-fill" style="font-size: 5rem;"></i>
            </div>
            <h3 class="text-muted mb-3 fw-bold">ไม่พบข้อมูลชั้นเรียน</h3>
            <p class="text-muted mb-4 px-4 mx-auto" style="max-width: 500px;">ดูเหมือนว่าท่านยังไม่ได้เป็นครูที่ปรึกษาในห้องเรียนใดๆ ในปีการศึกษานี้ จึงไม่มีรายชื่อห้องสำหรับการประเมิน</p>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
