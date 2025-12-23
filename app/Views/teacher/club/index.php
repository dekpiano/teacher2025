<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ชุมนุมที่ปรึกษา') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .club-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        border-radius: 1.25rem;
        overflow: hidden;
    }
    .club-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .club-icon-wrapper {
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
    <!-- Help Modal -->
    <div class="modal fade" id="clubHelpModal" tabindex="-1" aria-labelledby="clubHelpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light py-3">
                    <h5 class="modal-title fw-bold" id="clubHelpModalLabel">
                        <i class="bi bi-question-circle-fill text-primary me-2"></i>คู่มือการใช้งานระบบชุมนุม
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <?php include('help_modal_content.php'); ?>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ปิดคู่มือ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Section -->
    <div class="glass-header shadow-lg">
        <div class="row align-items-center text-start">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-light mb-2">
                        <li class="breadcrumb-item"><a href="<?= site_url('home') ?>" class="text-white opacity-75">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">ระบบชุมนุม</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold text-white mb-2">
                    <i class="bi bi-people-fill me-2"></i><?= esc($title ?? 'ชุมนุมที่ปรึกษา') ?>
                </h1>
                <?php if (isset($currentAcademicYear) && isset($currentTerm)): ?>
                    <p class="lead mb-0 text-white opacity-75">
                        ประจำปีการศึกษา <?= esc($currentAcademicYear) ?> ภาคเรียนที่ <?= esc($currentTerm) ?>
                    </p>
                <?php endif; ?>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <div class="d-flex flex-column flex-lg-row justify-content-lg-end gap-3">
                    <?php if (!(isset($hasClubForCurrentYear) && $hasClubForCurrentYear)): ?>
                        <button type="button" class="btn btn-white btn-lg rounded-pill shadow-lg px-4 py-3 text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createClubModal">
                            <i class="bi bi-plus-circle-fill me-2"></i> สร้างชุมนุมใหม่
                        </button>
                    <?php else: ?>
                        <div class="badge bg-white text-primary rounded-pill px-4 py-3 shadow-sm border">
                            <i class="bi bi-check-circle-fill me-2"></i> คุณมีชุมนุมในเทอมนี้แล้ว
                        </div>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-light btn-lg rounded-pill px-4 py-3 border-2" data-bs-toggle="modal" data-bs-target="#clubHelpModal">
                        <i class="bi bi-question-circle me-2"></i> คู่มือการใช้งาน
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <?php if (!empty($clubs) && is_array($clubs)): ?>
        <div class="row g-4 text-start">
            <?php foreach ($clubs as $club): ?>
                <?php
                    $currentDate = date('Y-m-d');
                    $statusText = 'ปิดรับสมัคร';
                    $statusColor = 'danger';
                    $statusIcon = 'bi-x-circle';

                    if ($registrationStartDate && $registrationEndDate) {
                        if ($currentDate < $registrationStartDate) {
                            $statusText = 'รอดำเนินการ';
                            $statusColor = 'warning';
                            $statusIcon = 'bi-clock-history';
                        } elseif ($currentDate >= $registrationStartDate && $currentDate <= $registrationEndDate) {
                            $statusText = 'กำลังเปิดรับ';
                            $statusColor = 'success';
                            $statusIcon = 'bi-check-circle';
                        }
                    }
                    
                    $percent = ($club->club_max_participants > 0) ? ($club->member_count / $club->club_max_participants) * 100 : 0;
                    $percent = min($percent, 100);
                ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card club-card shadow-sm h-100 border-0">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="club-icon-wrapper bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-mortarboard"></i>
                                </div>
                                <span class="badge rounded-pill bg-<?= $statusColor ?> bg-opacity-10 text-<?= $statusColor ?> px-3 py-2 border border-<?= $statusColor ?> border-opacity-25">
                                    <i class="bi <?= $statusIcon ?> me-1"></i> <?= $statusText ?>
                                </span>
                            </div>

                            <h4 class="card-title fw-bold mb-1 text-dark"><?= esc($club->club_name) ?></h4>
                            <p class="text-muted small mb-4 line-clamp-2">
                                <?= !empty($club->club_description) ? esc($club->club_description) : 'ไม่มีคำอธิบายเพิ่มเติม' ?>
                            </p>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">ความจุสมาชิก</span>
                                    <span class="fw-bold small"><?= esc($club->member_count) ?> / <?= esc($club->club_max_participants) ?> คน</span>
                                </div>
                                <div class="progress progress-minimal">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $percent ?>%" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <div class="p-2 rounded bg-light border border-dashed text-center">
                                        <div class="text-muted smallest text-uppercase fw-semibold mb-1">ระดับชั้น</div>
                                        <div class="small fw-bold text-dark"><?= esc($club->club_level) ?></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded bg-light border border-dashed text-center">
                                        <div class="text-muted smallest text-uppercase fw-semibold mb-1">เทอม/ปี</div>
                                        <div class="small fw-bold text-dark"><?= esc($club->club_trem) ?>/<?= esc($club->club_year) ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto pt-3 border-top">
                                <a href="<?= site_url('club/manage/' . $club->club_id) ?>" class="btn btn-outline-primary btn-lg rounded-pill w-100 fw-bold shadow-sm">
                                    <i class="bi bi-gear-fill me-2"></i> จัดการชุมนุม
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-light">
            <div class="mb-4 text-primary opacity-25">
                <i class="bi bi-collection-fill" style="font-size: 5rem;"></i>
            </div>
            <h3 class="text-muted mb-3 fw-bold">ไม่พบข้อมูลชุมนุม</h3>
            <p class="text-muted mb-4 px-4 mx-auto" style="max-width: 500px;">ดูเหมือนว่าคุณยังไม่ได้สร้างชุมนุมสำหรับภาคเรียนนี้ คลิกที่ปุ่มด้านบนเพื่อเริ่มต้นสร้างชุมนุมของคุณและเปิดรับสมัครนักเรียน</p>
            <?php if (!(isset($hasClubForCurrentYear) && $hasClubForCurrentYear)): ?>
                <button type="button" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createClubModal">
                    <i class="bi bi-plus-circle me-2"></i> สร้างชุมนุมตอนนี้
                </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Create Club Modal -->
<div class="modal fade" id="createClubModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="createClubModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= site_url('club/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white border-0 py-4">
                    <div class="text-start">
                        <h4 class="modal-title fw-bold text-white" id="createClubModalLabel">สร้างชุมนุมใหม่</h4>
                        <p class="text-white text-opacity-75 small mb-0">เริ่มต้นสร้างสังคมแห่งการเรียนรู้ร่วมกับนักเรียนของคุณ</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="club_name" name="club_name" placeholder="ชื่อชุมนุม" required>
                        <label for="club_name">ระบุชื่อชุมนุม</label>
                    </div>
                    <div class="form-floating mb-4">
                        <textarea class="form-control" id="club_description" name="club_description" placeholder="รายละเอียด" style="height: 120px"></textarea>
                        <label for="club_description">รายละเอียด / คำอธิบายโดยย่อ</label>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-4">
                                <input type="number" class="form-control" id="club_max_participants" name="club_max_participants" placeholder="จำนวนรับ" value="20" required>
                                <label for="club_max_participants">จำนวนรับสูงสุด (คน) สามารถเพิ่ม - ลด ได้</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-4">
                                <select class="form-select" id="club_level" name="club_level" required>
                                    <option value="" selected disabled>-- เลือก --</option>
                                    <option value="ม.ต้น">ม.ต้น</option>
                                    <option value="ม.ปลาย">ม.ปลาย</option>
                                    <option value="ม.ต้น และ ม.ปลาย">ม.ต้น และ ม.ปลาย</option>
                                </select>
                                <label for="club_level">ระดับชั้นที่เปิดรับ</label>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 py-3 rounded-3 d-flex align-items-center">
                        <i class="bi bi-info-circle-fill fs-4 me-3 text-info"></i>
                        <span class="small">ข้อมูลเหล่านี้สามารถแก้ไขได้ในภายหลังจากหน้ารายละเอียดชุมนุม</span>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold me-auto" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold">สร้างชุมนุม</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#clubHelpModal').on('show.bs.modal', function () {
            setTimeout(function() {
                var tabEl = document.querySelector('#help-pills-index-tab');
                if (tabEl && typeof bootstrap !== 'undefined') {
                    var tab = new bootstrap.Tab(tabEl);
                    tab.show();
                }
            }, 300);
        });
    });
</script>
<?= $this->endSection() ?>
