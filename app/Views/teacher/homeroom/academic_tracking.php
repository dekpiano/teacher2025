<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ติดตามผลการเรียน & กิจกรรม') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .academic-header-card {
        background: linear-gradient(135deg, #5046e5 0%, #696cff 50%, #8c7cf0 100%);
        border-radius: 1.25rem;
        padding: 2rem 2.25rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(105, 108, 255, 0.28);
    }

    .academic-header-card::after {
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

    .kpi-card {
        border-radius: 1rem;
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
        padding: 1.25rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(105, 108, 255, 0.1);
        border-color: rgba(105, 108, 255, 0.25);
    }

    .failed-subject-badge {
        font-size: 0.78rem;
        padding: 0.25rem 0.6rem;
        border-radius: 0.4rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        margin: 2px;
    }

    .badge-grade-0 {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .badge-grade-ror {
        background-color: #fef9c3;
        color: #854d0e;
        border: 1px solid #fef08a;
    }

    .badge-grade-ms {
        background-color: #ffedd5;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }

    .badge-grade-mf {
        background-color: #f3e8ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid py-2">
    <!-- Header Banner -->
    <div class="academic-header-card mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="breadcrumb-light-wrapper mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-light mb-0">
                            <li class="breadcrumb-item"><a href="<?= site_url('homeroom') ?>"><i
                                        class="bi bi-house-door me-1"></i>ระบบครูที่ปรึกษา</a></li>
                            <li class="breadcrumb-item active" aria-current="page">ติดตามผลการเรียน & กิจกรรม</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="display-6 fw-bold text-white mb-2">
                    <i class="bi bi-mortarboard-fill me-2 text-warning"></i>ติดตามผลการเรียน & กิจกรรม
                    ม.<?= esc($className) ?>
                </h2>
                <p class="mb-3 text-white text-opacity-90 fs-6">
                    <i class="bi bi-calendar3 me-1"></i> ประจำภาคเรียนที่ <?= esc($term) ?>/<?= esc($year) ?> |
                    ครูที่ปรึกษา:
                    <?= esc($teacher->pers_prefix . $teacher->pers_firstname . ' ' . $teacher->pers_lastname) ?>
                </p>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-white bg-opacity-20 text-dark rounded-pill px-3 py-2">
                        <i class="bi bi-people me-1"></i> นักเรียน: <?= count($students) ?> คน
                    </span>
                    <span class="badge bg-white bg-opacity-20 text-dark rounded-pill px-3 py-2">
                        <i class="bi bi-book me-1"></i> รายวิชาที่มีผลการเรียน: <?= count($subjects) ?> วิชา
                    </span>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <form action="<?= site_url('homeroom/academic-tracking') ?>" method="GET"
                    class="card bg-white bg-opacity-20 border-0 p-3 rounded-4 d-inline-block text-start"
                    style="backdrop-filter: blur(10px); min-width: 260px;">
                    <label class="text-dark small fw-bold mb-1">
                        <i class="bi bi-funnel me-1"></i> เลือกภาคเรียน / ปีการศึกษา:
                    </label>
                    <div class="input-group input-group-sm">
                        <select name="term" class="form-select bg-white text-dark fw-bold" style="max-width: 90px;">
                            <option value="1" <?= $term == '1' ? 'selected' : '' ?>>เทอม 1</option>
                            <option value="2" <?= $term == '2' ? 'selected' : '' ?>>เทอม 2</option>
                        </select>
                        <select name="year" class="form-select bg-white text-dark fw-bold">
                            <?php if (!empty($schoolYears)): ?>
                                <?php foreach ($schoolYears as $sy): ?>
                                    <?php
                                    $parts = explode('/', $sy['schyear_year']);
                                    $y = $parts[1] ?? $parts[0];
                                    ?>
                                    <option value="<?= esc($y) ?>" <?= $year == $y ? 'selected' : '' ?>>ปี <?= esc($y) ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="<?= esc($year) ?>" selected>ปี <?= esc($year) ?></option>
                            <?php endif; ?>
                        </select>
                        <button type="submit" class="btn btn-warning text-dark fw-bold">
                            ดูข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-medium">เกรดเฉลี่ยรวมของห้อง (GPA)</span>
                        <h3 class="fw-bold text-primary mb-0 mt-1"><?= esc($avgGpa) ?></h3>
                    </div>
                    <div
                        class="avatar avatar-md bg-label-primary rounded-3 d-flex align-items-center justify-content-center fs-4">
                        <i class="bi bi-award-fill"></i>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">ผลการเรียนเฉลี่ยรวมทุกรายวิชา</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-medium">นักเรียนที่ติด 0, ร, มส, มผ</span>
                        <h3 class="fw-bold text-danger mb-0 mt-1"><?= esc($atRiskCount) ?> <small
                                class="fs-6 fw-normal text-muted">คน</small></h3>
                    </div>
                    <div
                        class="avatar avatar-md bg-label-danger rounded-3 d-flex align-items-center justify-content-center fs-4">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                </div>
                <small class="text-danger d-block mt-2">
                    <?= $atRiskCount > 0 ? 'ควรติดตามและช่วยเหลือนักเรียนกลุ่มนี้' : 'ยอดเยี่ยม! ไม่มีนักเรียนติด 0, ร, มส, มผ' ?>
                </small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-medium">ผลการเรียนปกติ (ผ่านทุกวิชา)</span>
                        <h3 class="fw-bold text-success mb-0 mt-1"><?= count($students) - $atRiskCount ?> <small
                                class="fs-6 fw-normal text-muted">คน</small></h3>
                    </div>
                    <div
                        class="avatar avatar-md bg-label-success rounded-3 d-flex align-items-center justify-content-center fs-4">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">คิดเป็น
                    <?= count($students) > 0 ? round(((count($students) - $atRiskCount) / count($students)) * 100, 1) : 0 ?>%
                    ของห้อง</small>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs nav-fill border-0" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active py-3 fw-bold fs-6 border-0 border-bottom border-3 border-primary"
                        id="grades-tab" data-bs-toggle="tab" data-bs-target="#grades-pane" type="button" role="tab">
                        <i class="bi bi-journal-text me-2 text-primary"></i>สรุปผลการเรียนรายบุคคล
                        (<?= count($students) ?> คน)
                    </button>
                </li>
                <li class="nav-item">
                    <button
                        class="nav-link py-3 fw-bold fs-6 border-0 border-bottom border-3 border-transparent text-muted"
                        id="activities-tab" data-bs-toggle="tab" data-bs-target="#activities-pane" type="button"
                        role="tab">
                        <i class="bi bi-person-arms-up me-2 text-warning"></i>ติดตามกิจกรรมพัฒนาผู้เรียน (ชุมนุม)
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content p-0">
            <!-- Tab 1: Grades & At-Risk -->
            <div class="tab-pane fade show active" id="grades-pane" role="tabpanel" tabindex="0">
                <!-- Filter bar -->
                <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button"
                            class="btn btn-sm btn-primary rounded-pill px-3 fw-bold filter-grade-btn active"
                            data-filter="all">
                            ทั้งหมด (<?= count($students) ?>)
                        </button>
                        <button type="button"
                            class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold filter-grade-btn"
                            data-filter="at-risk">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> เฉพาะที่ติด 0, ร, มส, มผ
                            (<?= esc($atRiskCount) ?>)
                        </button>
                    </div>
                </div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 60px;">เลขที่</th>
                                <th style="width: 110px;">รหัสนักเรียน</th>
                                <th>ชื่อ - นามสกุล</th>
                                <th>แผนการเรียน</th>
                                <th class="text-center" style="width: 100px;">เกรดเฉลี่ย (GPA)</th>
                                <th>รายวิชาที่ต้องแก้ไข (0, ร, มส, มผ)</th>
                                <th class="text-center">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $st): ?>
                                    <?php
                                    $acad = $st->academic ?? ['gpa' => 0, 'failed_subjects' => []];
                                    $hasFailed = !empty($acad['failed_subjects']);
                                    ?>
                                    <tr class="academic-row" data-at-risk="<?= $hasFailed ? '1' : '0' ?>">
                                        <td class="ps-4 fw-bold text-dark fs-6"><?= esc($st->StudentNumber) ?></td>
                                        <td>
                                            <span
                                                class="badge bg-label-secondary font-monospace"><?= esc($st->StudentCode) ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                <?= esc($st->StudentPrefix . $st->StudentFirstName . ' ' . $st->StudentLastName) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-label-info"><?= esc($st->StudentStudyLine ?? 'สามัญ') ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="fw-bold fs-6 text-<?= $acad['gpa'] >= 2.5 ? 'success' : ($acad['gpa'] >= 2.0 ? 'primary' : 'danger') ?>">
                                                <?= number_format((float) $acad['gpa'], 2) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($hasFailed): ?>
                                                <div class="d-flex flex-wrap">
                                                    <?php foreach ($acad['failed_subjects'] as $fSub): ?>
                                                        <?php
                                                        $gradeBadgeClass = 'badge-grade-0';
                                                        if ($fSub['grade'] === 'ร')
                                                            $gradeBadgeClass = 'badge-grade-ror';
                                                        elseif ($fSub['grade'] === 'มส')
                                                            $gradeBadgeClass = 'badge-grade-ms';
                                                        elseif ($fSub['grade'] === 'มผ')
                                                            $gradeBadgeClass = 'badge-grade-mf';
                                                        ?>
                                                        <span class="failed-subject-badge <?= $gradeBadgeClass ?>"
                                                            title="<?= esc($fSub['name']) ?>">
                                                            <?= esc($fSub['code']) ?> (เกรด <?= esc($fSub['grade']) ?>)
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-success small fw-medium">
                                                    <i class="bi bi-check2-circle me-1"></i> ผ่านทุกรายวิชา
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($hasFailed): ?>
                                                <span class="badge bg-label-danger rounded-pill">ต้องติดตาม</span>
                                            <?php else: ?>
                                                <span class="badge bg-label-success rounded-pill">ปกติ</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        ไม่พบข้อมูลผลการเรียน
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Activities (Club & Scout) -->
            <div class="tab-pane fade" id="activities-pane" role="tabpanel" tabindex="0">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 60px;">เลขที่</th>
                                <th style="width: 110px;">รหัสนักเรียน</th>
                                <th>ชื่อ - นามสกุล</th>
                                <th>กิจกรรมชุมนุมที่ลงทะเบียน</th>
                                <th>บทบาทในชุมนุม</th>
                                <th class="text-center">สถานะการลงทะเบียน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $st): ?>
                                    <?php
                                    $sId = $st->StudentID;
                                    $act = $activities[$sId] ?? null;
                                    $club = $act['club'] ?? null;
                                    ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark fs-6"><?= esc($st->StudentNumber) ?></td>
                                        <td>
                                            <span
                                                class="badge bg-label-secondary font-monospace"><?= esc($st->StudentCode) ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                <?= esc($st->StudentPrefix . $st->StudentFirstName . ' ' . $st->StudentLastName) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($club && !empty($club->club_name)): ?>
                                                <span class="fw-bold text-primary">
                                                    <i class="bi bi-tag-fill me-1 text-primary"></i><?= esc($club->club_name) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-danger small fw-bold">
                                                    <i class="bi bi-exclamation-circle me-1"></i> ยังไม่ได้ลงทะเบียนชุมนุม
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($club): ?>
                                                <span
                                                    class="badge bg-label-<?= ($club->member_role ?? '') === 'Leader' ? 'warning' : 'info' ?>">
                                                    <?= ($club->member_role ?? '') === 'Leader' ? 'หัวหน้าชุมนุม' : 'สมาชิก' ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($club): ?>
                                                <span class="badge bg-label-success rounded-pill">ลงทะเบียนแล้ว</span>
                                            <?php else: ?>
                                                <span class="badge bg-label-danger rounded-pill">ยังไม่ลงทะเบียน</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        ไม่พบข้อมูลนักเรียน
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Filter At-Risk vs All Students
        const filterBtns = document.querySelectorAll('.filter-grade-btn');
        const rows = document.querySelectorAll('.academic-row');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                filterBtns.forEach(b => {
                    b.classList.remove('btn-primary', 'btn-danger', 'active');
                    if (b.getAttribute('data-filter') === 'all') b.classList.add('btn-outline-primary');
                    if (b.getAttribute('data-filter') === 'at-risk') b.classList.add('btn-outline-danger');
                });

                this.classList.remove('btn-outline-primary', 'btn-outline-danger');
                if (this.getAttribute('data-filter') === 'all') {
                    this.classList.add('btn-primary', 'active');
                } else {
                    this.classList.add('btn-danger', 'active');
                }

                const filter = this.getAttribute('data-filter');
                rows.forEach(row => {
                    const isAtRisk = row.getAttribute('data-at-risk') === '1';
                    if (filter === 'all') {
                        row.style.display = '';
                    } else if (filter === 'at-risk') {
                        row.style.display = isAtRisk ? '' : 'none';
                    }
                });
            });
        });
    });
</script>
<?= $this->endSection() ?>