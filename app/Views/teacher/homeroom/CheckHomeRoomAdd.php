<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'เช็คชื่อโฮมรูม') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .attendance-header-card {
        background: linear-gradient(135deg, #5046e5 0%, #696cff 50%, #8c7cf0 100%);
        border-radius: 1.25rem;
        padding: 2rem 2.25rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(105, 108, 255, 0.28);
    }
    .attendance-header-card::after {
        content: "";
        position: absolute;
        top: -40%;
        right: -10%;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.22) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .breadcrumb-light .breadcrumb-item,
    .breadcrumb-light .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.88) !important;
        text-decoration: none;
        font-size: 0.85rem;
    }
    .breadcrumb-light .breadcrumb-item a:hover {
        color: #ffffff !important;
        text-decoration: underline;
    }
    .breadcrumb-light .breadcrumb-item.active {
        color: #ffffff !important;
        font-weight: 700;
    }
    .breadcrumb-light .breadcrumb-item + .breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.6) !important;
    }
    .status-btn-group {
        display: inline-flex;
        border-radius: 0.6rem;
        background: #eef2f6;
        padding: 3px;
        gap: 2px;
    }
    .status-radio-label {
        padding: 0.35rem 0.75rem;
        font-size: 0.82rem;
        font-weight: 700;
        border-radius: 0.45rem;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
        color: #475569;
        margin-bottom: 0;
        user-select: none;
    }
    .btn-check:checked + .status-label-ma {
        background-color: #10b981 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.35);
    }
    .btn-check:checked + .status-label-khad {
        background-color: #ef4444 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.35);
    }
    .btn-check:checked + .status-label-la {
        background-color: #eab308 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(234, 179, 8, 0.35);
    }
    .btn-check:checked + .status-label-sahy {
        background-color: #f97316 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(249, 115, 22, 0.35);
    }
    .btn-check:checked + .status-label-kid {
        background-color: #0ea5e9 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(14, 165, 233, 0.35);
    }
    .btn-check:checked + .status-label-hnee {
        background-color: #8b5cf6 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(139, 92, 246, 0.35);
    }
    .live-counter-badge {
        font-size: 0.88rem;
        padding: 0.4rem 0.9rem;
        border-radius: 50rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .floating-save-bar {
        position: fixed;
        bottom: 1.5rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1040;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(15px);
        padding: 0.85rem 1.75rem;
        border-radius: 100px;
        box-shadow: 0 10px 40px rgba(105, 108, 255, 0.2);
        border: 1px solid rgba(105, 108, 255, 0.25);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        max-width: 95%;
        width: 620px;
    }
    .student-avatar-sm {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #ede9fe;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        color: #696cff;
    }
    .student-avatar-sm.girl {
        background: #fdf2f8;
        color: #db2777;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid py-2 mb-5">
    <!-- Header Banner -->
    <div class="attendance-header-card mb-4">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="breadcrumb-light-wrapper mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-light mb-0">
                            <li class="breadcrumb-item"><a href="<?= site_url('homeroom') ?>"><i class="bi bi-house-door me-1"></i>ระบบครูที่ปรึกษา</a></li>
                            <li class="breadcrumb-item active" aria-current="page">เช็คชื่อโฮมรูม ม.<?= esc($className) ?></li>
                        </ol>
                    </nav>
                </div>
                <h2 class="display-6 fw-bold text-white mb-2">
                    <i class="bi bi-calendar-check-fill me-2 text-warning"></i>เช็คชื่อโฮมรูม / เข้าแถวประจำวัน
                </h2>
                <p class="mb-3 text-white text-opacity-90 fs-6">
                    ห้อง ม.<?= esc($className) ?> | ภาคเรียนที่ <?= esc($term) ?>/<?= esc($year) ?> | วันที่: <strong><?= esc(thai_date($selectedDate, 'full')) ?></strong>
                </p>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-white bg-opacity-20 text-dark rounded-pill px-3 py-2">
                        <i class="bi bi-people me-1"></i> นักเรียนทั้งหมด: <?= count($students) ?> คน
                    </span>
                    <span class="badge bg-white bg-opacity-20 text-dark rounded-pill px-3 py-2">
                        <i class="bi bi-person-badge me-1"></i> ครูผู้บันทึก: <?= esc($teacher->pers_prefix . $teacher->pers_firstname . ' ' . $teacher->pers_lastname) ?>
                    </span>
                </div>
            </div>
            <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
                <!-- Date Picker Box -->
                <div class="card bg-white bg-opacity-20 border-0 p-3 rounded-4 d-inline-block text-start" style="backdrop-filter: blur(10px); min-width: 280px;">
                    <label class="text-dark small fw-bold mb-1">
                        <i class="bi bi-calendar-date me-1"></i> เปลี่ยนวันที่เช็คชื่อ:
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="date" id="datePickerInput" class="form-control rounded-start-pill bg-white text-dark fw-bold" value="<?= esc($selectedDate) ?>">
                        <button type="button" id="btnChangeDate" class="btn btn-warning rounded-end-pill fw-bold text-dark">
                            เรียกดู
                        </button>
                    </div>
                    <?php if ($existingRecord): ?>
                        <small class="text-warning d-block mt-2 fw-bold">
                            <i class="bi bi-info-circle me-1"></i> พบข้อมูลเดิม (บันทึกเมื่อ <?= esc(date('H:i', strtotime($existingRecord['chk_home_time'] ?? 'now'))) ?> น.)
                        </small>
                    <?php else: ?>
                        <small class="text-dark text-opacity-90 d-block mt-2">
                            <i class="bi bi-plus-circle me-1"></i> ยังไม่มีบันทึกในวันนี้
                        </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Control & Live Counters Card -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <div class="row align-items-center g-3">
            <div class="col-lg-5">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold" id="btnSelectAllMa">
                        <i class="bi bi-check-all me-1"></i> เลือกทุกคนเป็น "มา"
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="btnResetAll">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> รีเซ็ต
                    </button>
                </div>
            </div>
            <div class="col-lg-7 text-lg-end">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <span class="live-counter-badge bg-label-success">
                        มา: <span id="countMa" class="fs-6">0</span>
                    </span>
                    <span class="live-counter-badge bg-label-danger">
                        ขาด: <span id="countKhad" class="fs-6">0</span>
                    </span>
                    <span class="live-counter-badge bg-label-warning text-dark">
                        ลา: <span id="countLa" class="fs-6">0</span>
                    </span>
                    <span class="live-counter-badge bg-label-orange text-dark" style="background-color: #ffedd5; color: #c2410c;">
                        สาย: <span id="countSahy" class="fs-6">0</span>
                    </span>
                    <span class="live-counter-badge bg-label-info">
                        กิจกรรม: <span id="countKid" class="fs-6">0</span>
                    </span>
                    <span class="live-counter-badge bg-label-purple text-dark" style="background-color: #f3e8ff; color: #7e22ce;">
                        หนี: <span id="countHnee" class="fs-6">0</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Form -->
    <form action="<?= esc($actionUrl) ?>" method="POST" id="attendanceForm">
        <?= csrf_field() ?>
        <input type="hidden" name="chk_home_room" value="<?= esc($className) ?>">
        <input type="hidden" name="chk_home_teacher" value="<?= esc($teacher->pers_id) ?>">
        <input type="hidden" name="chk_home_date" value="<?= esc($selectedDate) ?>">
        <input type="hidden" name="chk_home_term" value="<?= esc($term) ?>">
        <input type="hidden" name="chk_home_yaer" value="<?= esc($year) ?>">

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">เลขที่</th>
                            <th class="text-center" style="width: 60px;">รูป</th>
                            <th style="width: 120px;">รหัสนักเรียน</th>
                            <th>ชื่อ - นามสกุล</th>
                            <th class="text-center">เลือกสถานะการเข้าแถว / โฮมรูม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            // Extract student code lists from existing record if available
                            $savedMa = !empty($existingRecord['chk_home_ma']) ? explode('|', $existingRecord['chk_home_ma']) : [];
                            $savedKhad = !empty($existingRecord['chk_home_khad']) ? explode('|', $existingRecord['chk_home_khad']) : [];
                            $savedLa = !empty($existingRecord['chk_home_la']) ? explode('|', $existingRecord['chk_home_la']) : [];
                            $savedSahy = !empty($existingRecord['chk_home_sahy']) ? explode('|', $existingRecord['chk_home_sahy']) : [];
                            $savedKid = !empty($existingRecord['chk_home_kid']) ? explode('|', $existingRecord['chk_home_kid']) : [];
                            $savedHnee = !empty($existingRecord['chk_home_hnee']) ? explode('|', $existingRecord['chk_home_hnee']) : [];
                        ?>

                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $st): ?>
                                <?php 
                                    $code = $st->StudentCode;
                                    $isGirl = !in_array($st->StudentPrefix, ['นาย', 'เด็กชาย', 'ด.ช.']);
                                    $initial = mb_substr($st->StudentFirstName, 0, 1, 'UTF-8');

                                    // Determine current status
                                    $currentStatus = 'ma'; // Default to 'ma'
                                    if ($existingRecord) {
                                        if (in_array($code, $savedKhad)) $currentStatus = 'khad';
                                        elseif (in_array($code, $savedLa)) $currentStatus = 'la';
                                        elseif (in_array($code, $savedSahy)) $currentStatus = 'sahy';
                                        elseif (in_array($code, $savedKid)) $currentStatus = 'kid';
                                        elseif (in_array($code, $savedHnee)) $currentStatus = 'hnee';
                                        elseif (in_array($code, $savedMa)) $currentStatus = 'ma';
                                    }
                                ?>
                                <tr>
                                    <td class="text-center fw-bold text-dark fs-6">
                                        <?= esc($st->StudentNumber) ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="student-avatar-sm <?= $isGirl ? 'girl' : '' ?> mx-auto">
                                            <?= esc($initial) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-secondary font-monospace fw-bold">
                                            <?= esc($st->StudentCode) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6">
                                            <?= esc($st->StudentPrefix . $st->StudentFirstName . ' ' . $st->StudentLastName) ?>
                                        </div>
                                        <small class="text-muted"><?= esc($st->StudentStudyLine ?? 'สามัญ') ?></small>
                                    </td>
                                    <td class="text-center">
                                        <div class="status-btn-group">
                                            <!-- มา -->
                                            <input type="radio" class="btn-check status-radio" name="status[<?= esc($code) ?>]" id="status_ma_<?= esc($code) ?>" value="ma" <?= $currentStatus === 'ma' ? 'checked' : '' ?>>
                                            <label class="status-radio-label status-label-ma" for="status_ma_<?= esc($code) ?>">มา</label>

                                            <!-- ขาด -->
                                            <input type="radio" class="btn-check status-radio" name="status[<?= esc($code) ?>]" id="status_khad_<?= esc($code) ?>" value="khad" <?= $currentStatus === 'khad' ? 'checked' : '' ?>>
                                            <label class="status-radio-label status-label-khad" for="status_khad_<?= esc($code) ?>">ขาด</label>

                                            <!-- ลา -->
                                            <input type="radio" class="btn-check status-radio" name="status[<?= esc($code) ?>]" id="status_la_<?= esc($code) ?>" value="la" <?= $currentStatus === 'la' ? 'checked' : '' ?>>
                                            <label class="status-radio-label status-label-la" for="status_la_<?= esc($code) ?>">ลา</label>

                                            <!-- สาย -->
                                            <input type="radio" class="btn-check status-radio" name="status[<?= esc($code) ?>]" id="status_sahy_<?= esc($code) ?>" value="sahy" <?= $currentStatus === 'sahy' ? 'checked' : '' ?>>
                                            <label class="status-radio-label status-label-sahy" for="status_sahy_<?= esc($code) ?>">สาย</label>

                                            <!-- กิจกรรม -->
                                            <input type="radio" class="btn-check status-radio" name="status[<?= esc($code) ?>]" id="status_kid_<?= esc($code) ?>" value="kid" <?= $currentStatus === 'kid' ? 'checked' : '' ?>>
                                            <label class="status-radio-label status-label-kid" for="status_kid_<?= esc($code) ?>">กิจกรรม</label>

                                            <!-- หนี -->
                                            <input type="radio" class="btn-check status-radio" name="status[<?= esc($code) ?>]" id="status_hnee_<?= esc($code) ?>" value="hnee" <?= $currentStatus === 'hnee' ? 'checked' : '' ?>>
                                            <label class="status-radio-label status-label-hnee" for="status_hnee_<?= esc($code) ?>">หนี</label>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-person-x fs-1 d-block mb-2 text-warning"></i>
                                    ไม่พบรายชื่อนักเรียนในห้องนี้
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sticky Floating Save Bar -->
        <div class="floating-save-bar">
            <div class="d-flex align-items-center gap-2 flex-grow-1">
                <span class="badge bg-primary rounded-pill px-3 py-2 fw-bold">
                    <i class="bi bi-clock-history me-1"></i> ม.<?= esc($className) ?>
                </span>
                <span class="text-muted small d-none d-sm-inline">
                    วันที่ <?= esc(thai_date($selectedDate, 'short')) ?>
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= site_url('homeroom') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    ยกเลิก
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow">
                    <i class="bi bi-save-fill me-1"></i> <?= esc($buttonLabel) ?>
                </button>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Live Counters update
    function updateCounters() {
        let ma = 0, khad = 0, la = 0, sahy = 0, kid = 0, hnee = 0;
        document.querySelectorAll('.status-radio:checked').forEach(r => {
            const val = r.value;
            if (val === 'ma') ma++;
            else if (val === 'khad') khad++;
            else if (val === 'la') la++;
            else if (val === 'sahy') sahy++;
            else if (val === 'kid') kid++;
            else if (val === 'hnee') hnee++;
        });

        document.getElementById('countMa').textContent = ma;
        document.getElementById('countKhad').textContent = khad;
        document.getElementById('countLa').textContent = la;
        document.getElementById('countSahy').textContent = sahy;
        document.getElementById('countKid').textContent = kid;
        document.getElementById('countHnee').textContent = hnee;
    }

    document.querySelectorAll('.status-radio').forEach(r => {
        r.addEventListener('change', updateCounters);
    });

    updateCounters(); // Initial run

    // 2. Select All "Ma"
    const btnSelectAllMa = document.getElementById('btnSelectAllMa');
    if (btnSelectAllMa) {
        btnSelectAllMa.addEventListener('click', function () {
            document.querySelectorAll('.status-radio[value="ma"]').forEach(r => {
                r.checked = true;
            });
            updateCounters();
        });
    }

    // 3. Reset All to Default
    const btnResetAll = document.getElementById('btnResetAll');
    if (btnResetAll) {
        btnResetAll.addEventListener('click', function () {
            document.querySelectorAll('.status-radio[value="ma"]').forEach(r => {
                r.checked = true;
            });
            updateCounters();
        });
    }

    // 4. Change Date Handler
    const btnChangeDate = document.getElementById('btnChangeDate');
    const datePickerInput = document.getElementById('datePickerInput');
    if (btnChangeDate && datePickerInput) {
        btnChangeDate.addEventListener('click', function () {
            const targetDate = datePickerInput.value;
            if (targetDate) {
                window.location.href = '<?= site_url('homeroom/add/') ?>' + targetDate;
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
