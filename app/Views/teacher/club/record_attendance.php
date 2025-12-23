<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'บันทึกการเข้าเรียน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .attendance-header {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        border-radius: 1.25rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .attendance-header::after {
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
    .table-card {
        border-radius: 1.25rem;
        border: none;
        overflow: hidden;
        margin-bottom: 5rem;
    }
    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6366f1;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .attendance-select {
        border-radius: 0.75rem;
        font-weight: 600;
        padding: 0.5rem 1rem;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
    }
    .attendance-select:focus {
        box-shadow: none;
        border-color: rgba(99, 102, 241, 0.5);
    }
    .status-badge-lg {
        padding: 0.75rem 1.25rem;
        border-radius: 1rem;
        font-weight: 700;
        font-size: 0.9rem;
    }
    .floating-save-bar {
        position: fixed;
        bottom: 1.5rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(15px);
        padding: 1rem 2rem;
        border-radius: 100px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.5);
        display: flex;
        align-items: center;
        gap: 1.5rem;
        width: auto;
        min-width: 400px;
    }
    .btn-batch {
        padding: 0.5rem 1.25rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .btn-batch:hover {
        transform: translateY(-2px);
    }
    
    /* Status Colors for Select */
    .status-ma { background-color: #dcfce7 !important; color: #15803d !important; border-color: #bbf7d0 !important; }
    .status-khad { background-color: #fee2e2 !important; color: #b91c1c !important; border-color: #fecaca !important; }
    .status-rapwy { background-color: #fef9c3 !important; color: #854d0e !important; border-color: #fef08a !important; }
    .status-rakic { background-color: #e0f2fe !important; color: #0369a1 !important; border-color: #bae6fd !important; }
    .status-kickrrm { background-color: #f1f5f9 !important; color: #475569 !important; border-color: #e2e8f0 !important; }
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
    <div class="attendance-header shadow-lg">
        <div class="row align-items-center text-start">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-light mb-2">
                        <li class="breadcrumb-item"><a href="<?= site_url('home') ?>" class="text-white opacity-75">หน้าหลัก</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('club') ?>" class="text-white opacity-75">ชุมนุม</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('club/schedule/' . $club->club_id) ?>" class="text-white opacity-75">ตารางกิจกรรม</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">บันทึกการเข้าเรียน</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold text-white mb-2">
                    <i class="bi bi-person-check-fill me-2"></i>เช็คชื่อเข้าเรียน : สัปดาห์ที่ <?= esc($schedule->tcs_week_number) ?>
                </h1>
                <div class="d-flex flex-wrap gap-3 align-items-center mt-3">
                    <div class="bg-white bg-opacity-10 px-3 py-2 rounded-pill small border border-white border-opacity-10 text-black">
                        <i class="bi bi-calendar-event me-1"></i> วันที่ <?= esc(date('d/m/Y', strtotime($schedule->tcs_start_date))) ?>
                    </div>
                    <div class="bg-white bg-opacity-10 px-3 py-2 rounded-pill small border border-white border-opacity-10 text-black">
                        <i class="bi bi-tag-fill me-1"></i> <?= esc($club->club_name) ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <?php if (isset($hasAttendanceRecord) && $hasAttendanceRecord): ?>
                    <span class="status-badge-lg bg-white text-success shadow-sm d-inline-block">
                        <i class="bi bi-check-circle-fill me-2"></i> บันทึกข้อมูลแล้ว
                    </span>
                <?php else: ?>
                    <span class="status-badge-lg bg-white bg-opacity-10 text-white border border-white border-opacity-25 d-inline-block">
                        <i class="bi bi-clock-history me-2"></i> ยังไม่ได้บันทึก
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Batch Selection Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 text-start">
            <div class="d-flex align-items-center flex-wrap gap-2 text-start">
                <span class="text-muted fw-bold me-3"><i class="bi bi-lightning-charge-fill text-warning"></i> จัดการด่วน:</span>
                <button type="button" class="btn btn-outline-success btn-batch" onclick="batchCheck('มา')">
                    <i class="bi bi-check-all me-1"></i> เช็ค "มา" ทั้งหมด
                </button>
                <button type="button" class="btn btn-outline-secondary btn-batch" onclick="resetCheck()">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> คืนค่าทั้งหมด
                </button>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <span class="text-muted smallest fw-bold">แสดงสมาชิกทั้งหมด <?= count($members) ?> คน</span>
                    <button type="button" class="btn btn-link text-primary p-0 text-decoration-none smallest fw-bold" data-bs-toggle="modal" data-bs-target="#clubHelpModal">
                        <i class="bi bi-question-circle"></i> ช่วยเหลือ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Form -->
    <form action="<?= site_url('club/saveAttendance/' . $club->club_id . '/' . $schedule->tcs_schedule_id) ?>" method="post" id="attendanceForm">
        <?= csrf_field() ?>
        <div class="card table-card shadow-sm">
            <div class="card-body p-0">
                <?php if (!empty($members) && is_array($members)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center py-3" style="width: 80px;">#</th>
                                    <th class="py-3">รหัสนักเรียน / ชื่อ-นามสกุล</th>
                                    <th class="text-center py-3">ชั้น / เลขที่</th>
                                    <th class="text-center py-3" style="width: 200px;">สถานะการเข้าเรียน</th>
                                </tr>
                            </thead>
                            <tbody class="text-start">
                                <?php
                                    $i = 1;
                                    $statusMap = [
                                        'มา' => 'status-ma',
                                        'ขาด' => 'status-khad',
                                        'ลาป่วย' => 'status-rapwy',
                                        'ลากิจ' => 'status-rakic',
                                        'กิจกรรม' => 'status-kickrrm',
                                    ];
                                ?>
                                <?php foreach ($members as $member): ?>
                                    <tr>
                                        <td class="text-center">
                                            <div class="student-avatar"><?= $i++ ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?></div>
                                            <div class="smallest text-muted"><i class="bi bi-person-badge me-1"></i>รหัสนักเรียน: <?= esc($member->StudentCode ?? $member->StudentID) ?></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-2 py-1">ม.<?= esc($member->StudentClass) ?></span>
                                            <div class="smallest text-muted mt-1">เลขที่ <?= esc($member->StudentNumber) ?></div>
                                        </td>
                                        <td>
                                            <?php
                                                $currentStatus = $existingAttendance[$member->StudentID] ?? 'มา';
                                                $colorClass = $statusMap[$currentStatus] ?? 'status-ma';
                                            ?>
                                            <select class="form-select attendance-select <?= $colorClass ?>" 
                                                    name="attendance[<?= esc($member->StudentID) ?>]"
                                                    data-student-id="<?= esc($member->StudentID) ?>"
                                                    data-original-status="<?= $currentStatus ?>">
                                                <option value="มา" <?= $currentStatus === 'มา' ? 'selected' : '' ?>>มา</option>
                                                <option value="ขาด" <?= $currentStatus === 'ขาด' ? 'selected' : '' ?>>ขาด</option>
                                                <option value="ลาป่วย" <?= $currentStatus === 'ลาป่วย' ? 'selected' : '' ?>>ลาป่วย</option>
                                                <option value="ลากิจ" <?= $currentStatus === 'ลากิจ' ? 'selected' : '' ?>>ลากิจ</option>
                                                <option value="กิจกรรม" <?= $currentStatus === 'กิจกรรม' ? 'selected' : '' ?>>กิจกรรม</option>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-people text-muted opacity-25" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">ไม่พบสมาชิกในชุมนุม</h5>
                        <p class="text-muted small">กรุณาเพิ่มสมาชิกในหน้ารายชื่อชุมนุมก่อน</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Floating Save Bar -->
        <div class="floating-save-bar">
            <div class="d-flex align-items-center">
                <a href="<?= site_url('club/schedule/' . $club->club_id) ?>" class="text-muted text-decoration-none smallest fw-bold me-4">
                    <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
                </a>
                <div class="vr me-4 opacity-10"></div>
                <div class="d-flex flex-column">
                    <span class="smallest fw-bold text-dark">ยืนยันการบันทึก</span>
                    <span class="smallest text-muted">ประจำสัปดาห์ที่ <?= esc($schedule->tcs_week_number) ?></span>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg fw-bold ms-auto">
                <i class="bi bi-save2 me-2"></i> บันทึกการเข้าเรียน
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const statusMap = {
        'มา': 'status-ma',
        'ขาด': 'status-khad',
        'ลาป่วย': 'status-rapwy',
        'ลากิจ': 'status-rakic',
        'กิจกรรม': 'status-kickrrm',
    };

    function batchCheck(status) {
        document.querySelectorAll('.attendance-select').forEach(select => {
            select.value = status;
            updateSelectColor(select);
        });
    }

    function resetCheck() {
        document.querySelectorAll('.attendance-select').forEach(select => {
            select.value = select.dataset.originalStatus || 'มา';
            updateSelectColor(select);
        });
    }

    function updateSelectColor(select) {
        // Remove all status classes
        Object.values(statusMap).forEach(cls => select.classList.remove(cls));
        // Add current status class
        const status = select.value;
        if (statusMap[status]) {
            select.classList.add(statusMap[status]);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Help Modal Activation
        const helpModal = document.getElementById('clubHelpModal');
        if (helpModal) {
            helpModal.addEventListener('show.bs.modal', function () {
                setTimeout(() => {
                    const tabEl = document.querySelector('#help-pills-attendance-tab');
                    if (tabEl) new bootstrap.Tab(tabEl).show();
                }, 300);
            });
        }

        // Individual Select Change Event
        document.querySelectorAll('.attendance-select').forEach(select => {
            select.addEventListener('change', function() {
                updateSelectColor(this);
            });
        });

        // Initialize Form Submission Protection
        const form = document.getElementById('attendanceForm');
        form.addEventListener('submit', function(e) {
            const saveBtn = this.querySelector('button[type="submit"]');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> กำลังบันทึก...';
        });
    });
</script>
<?= $this->endSection() ?>
