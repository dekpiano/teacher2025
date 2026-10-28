<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ข้อมูลนักเรียนในห้องที่ปรึกษา') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .students-header-card {
        background: linear-gradient(135deg, #5046e5 0%, #696cff 50%, #8c7cf0 100%);
        border-radius: 1.25rem;
        padding: 2rem 2.25rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(105, 108, 255, 0.28);
    }

    .students-header-card::after {
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

    .breadcrumb-light .breadcrumb-item+.breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.6) !important;
    }

    .student-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #ede9fe;
        color: #696cff;
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #ddd6fe;
    }

    .student-avatar.girl {
        background: #fdf2f8;
        color: #db2777;
        border-color: #fbcfe8;
    }

    .badge-filter-btn {
        border-radius: 50rem;
        padding: 0.45rem 1.2rem;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-block;
    }

    .badge-filter-btn.active {
        background-color: #696cff !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
    }

    .badge-filter-btn:not(.active) {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .badge-filter-btn:not(.active):hover {
        background-color: #e2e8f0;
        color: #1e293b;
    }

    .btn-hero-white {
        background: #ffffff !important;
        color: #4338ca !important;
        font-weight: 700;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        transition: all 0.2s ease;
    }

    .btn-hero-white:hover {
        background: #f8fafc !important;
        color: #312e81 !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
    }

    .search-input-box {
        border-radius: 50rem;
        padding-left: 2.5rem;
        border: 1px solid #cbd5e1;
        background-color: #f8fafc;
        transition: all 0.2s;
        color: #1e293b;
    }

    .search-input-box:focus {
        background-color: #ffffff;
        border-color: #696cff;
        box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.18);
    }

    .modal-profile-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #6366f1 100%);
        color: #ffffff;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
        padding: 1.5rem 2rem;
    }

    .profile-info-row {
        padding: 0.65rem 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .profile-info-row:last-child {
        border-bottom: none;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid py-2">
    <!-- Header Banner -->
    <div class="students-header-card mb-4">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="breadcrumb-light-wrapper mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-light mb-0">
                            <li class="breadcrumb-item"><a href="<?= site_url('homeroom') ?>"><i
                                        class="bi bi-house-door me-1"></i>ระบบครูที่ปรึกษา</a></li>
                            <li class="breadcrumb-item active" aria-current="page">ข้อมูลนักเรียน
                                ม.<?= esc($className) ?></li>
                        </ol>
                    </nav>
                </div>
                <h2 class="display-6 fw-bold text-white mb-2">
                    <i class="bi bi-people-fill me-2 text-warning"></i>ข้อมูลนักเรียนชั้น ม.<?= esc($className) ?>
                </h2>
                <p class="mb-3 text-white text-opacity-90 fs-6">
                    <i class="bi bi-calendar3 me-1"></i> ประจำภาคเรียนที่ <?= esc($term) ?>/<?= esc($year) ?> |
                    ครูที่ปรึกษา:
                    <?= esc($teacher->pers_prefix . $teacher->pers_firstname . ' ' . $teacher->pers_lastname) ?>
                </p>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-white bg-opacity-20 text-dark rounded-pill px-3 py-2">
                        <i class="bi bi-person-check me-1"></i> นักเรียนทั้งหมด:
                        <strong><?= esc($totalCount) ?></strong> คน
                    </span>
                    <span class="badge bg-primary rounded-pill px-3 py-2">
                        <i class="bi bi-gender-male me-1"></i> ชาย:
                        <?php
                        $b = 0;
                        $g = 0;
                        foreach ($students as $s) {
                            if (in_array($s->StudentPrefix, ['นาย', 'เด็กชาย', 'ด.ช.']))
                                $b++;
                            else
                                $g++;
                        }
                        echo $b;
                        ?> คน
                    </span>
                    <span class="badge rounded-pill px-3 py-2 text-white" style="background-color: #ec4899;">
                        <i class="bi bi-gender-female me-1"></i> หญิง: <?= $g ?> คน
                    </span>
                </div>
            </div>
            <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <a href="<?= site_url('homeroom/add') ?>"
                        class="btn btn-warning text-dark rounded-pill px-4 shadow fw-bold">
                        <i class="bi bi-calendar-check-fill me-1"></i> ไปหน้าเช็คชื่อ
                    </a>
                    <a href="<?= site_url('homeroom/academic-tracking') ?>"
                        class="btn btn-hero-white rounded-pill px-4 shadow fw-bold">
                        <i class="bi bi-mortarboard-fill me-1 text-primary"></i> ผลการเรียน
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <div class="position-relative">
                    <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" id="studentSearchInput" class="form-control search-input-box py-2"
                        placeholder="ค้นหาตามชื่อ, รหัสนักเรียน, เลขที่, หรือเบอร์โทร...">
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="btn-group shadow-none" role="group">
                    <a href="<?= site_url('homeroom/students?status=1/ปกติ') ?>"
                        class="badge-filter-btn <?= $statusFilter === '1/ปกติ' ? 'active' : '' ?>">
                        สถานะปกติ
                    </a>
                    <a href="<?= site_url('homeroom/students?status=all') ?>"
                        class="badge-filter-btn <?= $statusFilter === 'all' ? 'active' : '' ?> ms-2">
                        ทั้งหมด
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0" id="studentsTable">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">เลขที่</th>
                        <th class="text-center" style="width: 80px;">รูปถ่าย</th>
                        <th style="width: 120px;">รหัสนักเรียน</th>
                        <th>ชื่อ - นามสกุล</th>
                        <th>แผนการเรียน</th>
                        <th>เบอร์โทรนักเรียน</th>
                        <th>ผู้ปกครอง / เบอร์ติดต่อ</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-end pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $st): ?>
                            <?php
                            $isGirl = !in_array($st->StudentPrefix, ['นาย', 'เด็กชาย', 'ด.ช.']);
                            $initial = mb_substr($st->StudentFirstName, 0, 1, 'UTF-8');
                            ?>
                            <tr class="student-row"
                                data-search="<?= esc(strtolower($st->StudentNumber . ' ' . $st->StudentCode . ' ' . $st->StudentPrefix . $st->StudentFirstName . ' ' . $st->StudentLastName . ' ' . ($st->StudentPhone ?? '') . ' ' . ($st->StudentParentName ?? '') . ' ' . ($st->StudentParentPhone ?? ''))) ?>">
                                <td class="text-center fw-bold text-dark fs-6">
                                    <?= esc($st->StudentNumber) ?>
                                </td>
                                <td class="text-center">
                                    <div class="student-avatar <?= $isGirl ? 'girl' : '' ?> mx-auto">
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
                                    <small class="text-muted">
                                        <?= $isGirl ? 'หญิง' : 'ชาย' ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-label-info">
                                        <?= esc($st->StudentStudyLine ?? 'สามัญ') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($st->StudentPhone)): ?>
                                        <a href="tel:<?= esc($st->StudentPhone) ?>" class="text-dark text-decoration-none">
                                            <i class="bi bi-telephone-fill text-success me-1"></i><?= esc($st->StudentPhone) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <span
                                            class="fw-medium text-dark"><?= esc($st->StudentParentName ?? ($st->StudentFatherName ?? ($st->StudentMotherName ?? '-'))) ?></span>
                                    </div>
                                    <?php if (!empty($st->StudentParentPhone)): ?>
                                        <small>
                                            <a href="tel:<?= esc($st->StudentParentPhone) ?>"
                                                class="text-primary text-decoration-none">
                                                <i class="bi bi-telephone me-1"></i><?= esc($st->StudentParentPhone) ?>
                                            </a>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-label-<?= ($st->StudentStatus === '1/ปกติ' || $st->StudentStatus === 'ปกติ') ? 'success' : 'warning' ?> rounded-pill">
                                        <?= esc($st->StudentStatus) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <button type="button"
                                        class="btn btn-sm btn-label-primary rounded-pill px-3 btn-view-profile"
                                        data-student='<?= htmlspecialchars(json_encode($st), ENT_QUOTES, 'UTF-8') ?>'>
                                        <i class="bi bi-eye-fill me-1"></i> ดูประวัติ
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2 text-warning"></i>
                                ไม่พบรายชื่อนักเรียนในห้องนี้
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Student Profile Modal -->
<div class="modal fade" id="studentProfileModal" tabindex="-1" aria-labelledby="studentProfileModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-profile-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="student-avatar bg-white text-primary fs-4" id="modalStudentAvatar"
                        style="width: 52px; height: 52px; border-radius: 14px;">
                        ก
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="modalStudentName">-</h5>
                        <small class="text-white text-opacity-80" id="modalStudentSub">-</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- General Info -->
                    <div class="col-md-6">
                        <div class="card bg-light border-0 rounded-3 p-3 h-100">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-person-lines-fill me-2"></i>ข้อมูลส่วนตัว
                            </h6>
                            <div class="profile-info-row d-flex justify-content-between">
                                <span class="text-muted">รหัสนักเรียน:</span>
                                <span class="fw-bold text-dark" id="modalCode">-</span>
                            </div>
                            <div class="profile-info-row d-flex justify-content-between">
                                <span class="text-muted">เลขประจำตัวประชาชน:</span>
                                <span class="fw-bold text-dark" id="modalIdCard">-</span>
                            </div>
                            <div class="profile-info-row d-flex justify-content-between">
                                <span class="text-muted">วัน/เดือน/ปีเกิด:</span>
                                <span class="fw-bold text-dark" id="modalBirthday">-</span>
                            </div>
                            <div class="profile-info-row d-flex justify-content-between">
                                <span class="text-muted">กรุ๊ปเลือด:</span>
                                <span class="fw-bold text-dark" id="modalBlood">-</span>
                            </div>
                            <div class="profile-info-row d-flex justify-content-between">
                                <span class="text-muted">เบอร์โทรศัพท์:</span>
                                <span class="fw-bold text-dark" id="modalPhone">-</span>
                            </div>
                            <div class="profile-info-row d-flex justify-content-between">
                                <span class="text-muted">แผนการเรียน:</span>
                                <span class="fw-bold text-dark" id="modalStudyLine">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Family & Contact Info -->
                    <div class="col-md-6">
                        <div class="card bg-light border-0 rounded-3 p-3 h-100">
                            <h6 class="fw-bold text-success mb-3">
                                <i class="bi bi-people-fill me-2"></i>ข้อมูลผู้ปกครอง & การติดต่อ
                            </h6>
                            <div class="profile-info-row d-flex justify-content-between">
                                <span class="text-muted">ชื่อบิดา:</span>
                                <span class="fw-bold text-dark" id="modalFather">-</span>
                            </div>
                            <div class="profile-info-row d-flex justify-content-between">
                                <span class="text-muted">ชื่อมารดา:</span>
                                <span class="fw-bold text-dark" id="modalMother">-</span>
                            </div>
                            <div class="profile-info-row d-flex justify-content-between">
                                <span class="text-muted">ผู้ปกครอง:</span>
                                <span class="fw-bold text-dark" id="modalParent">-</span>
                            </div>
                            <div class="profile-info-row d-flex justify-content-between">
                                <span class="text-muted">เบอร์โทรผู้ปกครอง:</span>
                                <span class="fw-bold text-primary" id="modalParentPhone">-</span>
                            </div>
                            <div class="profile-info-row">
                                <div class="text-muted mb-1">ที่อยู่ปัจจุบัน:</div>
                                <div class="fw-medium text-dark small" id="modalAddress">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light py-2 px-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4"
                    data-bs-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Live Client-Side Search Filter
        const searchInput = document.getElementById('studentSearchInput');
        const rows = document.querySelectorAll('.student-row');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                rows.forEach(row => {
                    const searchData = row.getAttribute('data-search') || '';
                    if (searchData.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // 2. View Student Profile Modal
        const profileModal = new bootstrap.Modal(document.getElementById('studentProfileModal'));
        document.querySelectorAll('.btn-view-profile').forEach(btn => {
            btn.addEventListener('click', function () {
                const data = JSON.parse(this.getAttribute('data-student') || '{}');

                const fullName = (data.StudentPrefix || '') + (data.StudentFirstName || '') + ' ' + (data.StudentLastName || '');
                const initial = (data.StudentFirstName || '').charAt(0) || 'น';

                document.getElementById('modalStudentAvatar').textContent = initial;
                document.getElementById('modalStudentName').textContent = fullName;
                document.getElementById('modalStudentSub').textContent = `เลขที่ ${data.StudentNumber || '-'} | ห้อง ม.${data.StudentClass || '-'} | รหัส ${data.StudentCode || '-'}`;

                document.getElementById('modalCode').textContent = data.StudentCode || '-';
                document.getElementById('modalIdCard').textContent = data.StudentID || '-';
                document.getElementById('modalBirthday').textContent = data.StudentBirthDay || '-';
                document.getElementById('modalBlood').textContent = data.StudentBlood || '-';
                document.getElementById('modalPhone').textContent = data.StudentPhone || '-';
                document.getElementById('modalStudyLine').textContent = data.StudentStudyLine || '-';

                document.getElementById('modalFather').textContent = data.StudentFatherName || '-';
                document.getElementById('modalMother').textContent = data.StudentMotherName || '-';
                document.getElementById('modalParent').textContent = data.StudentParentName || '-';
                document.getElementById('modalParentPhone').textContent = data.StudentParentPhone || '-';
                document.getElementById('modalAddress').textContent = data.StudentAddress || '-';

                profileModal.show();
            });
        });
    });
</script>
<?= $this->endSection() ?>