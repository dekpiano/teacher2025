<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ระบบครูที่ปรึกษา') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .homeroom-header-card {
        background: linear-gradient(135deg, #5046e5 0%, #696cff 50%, #8c7cf0 100%);
        border-radius: 1.25rem;
        padding: 2rem 2.25rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(105, 108, 255, 0.28);
    }
    .homeroom-header-card::after {
        content: "";
        position: absolute;
        top: -30%;
        right: -5%;
        width: 350px;
        height: 350px;
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
    .homeroom-stat-card {
        border-radius: 1rem;
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
        padding: 1.25rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
    }
    .homeroom-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(105, 108, 255, 0.12);
        border-color: rgba(105, 108, 255, 0.3);
    }
    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .quick-action-card {
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 1.5rem;
        text-decoration: none;
        color: inherit;
        display: block;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .quick-action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(105, 108, 255, 0.18);
        border-color: #696cff;
        color: inherit;
    }
    .badge-advisor-pill {
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        padding: 0.4rem 0.95rem;
        border-radius: 50rem;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
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
    .attendance-status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.65rem;
        border-radius: 0.5rem;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .status-badge-ma { background: #dcfce7; color: #15803d; }
    .status-badge-khad { background: #fee2e2; color: #b91c1c; }
    .status-badge-la { background: #fef9c3; color: #854d0e; }
    .status-badge-sahy { background: #ffedd5; color: #c2410c; }
    .status-badge-kid { background: #e0f2fe; color: #0369a1; }
    .status-badge-hnee { background: #f3e8ff; color: #7e22ce; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (!empty($no_class)): ?>
    <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4">
        <div class="avatar avatar-xl bg-label-warning mx-auto mb-3" style="width: 70px; height: 70px;">
            <i class="bi bi-exclamation-triangle-fill fs-1"></i>
        </div>
        <h4 class="fw-bold text-dark">ไม่พบข้อมูลห้องเรียนที่ปรึกษา</h4>
        <p class="text-muted mb-4">ระบบไม่พบข้อมูลการมอบหมายห้องเรียนที่ปรึกษาของท่านในปีการศึกษา <?= esc($year) ?> ภาคเรียนที่ <?= esc($term) ?><br>หากท่านเป็นครูที่ปรึกษา กรุณาติดต่อฝ่ายวิชาการ/ฝ่ายบริหารจัดการข้อมูล</p>
        <div>
            <a href="<?= base_url() ?>" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-house-door-fill me-1"></i> กลับหน้าหลัก
            </a>
        </div>
    </div>
<?php else: ?>

    <!-- Header Banner -->
    <div class="homeroom-header-card mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="breadcrumb-light-wrapper mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-light mb-0">
                            <li class="breadcrumb-item"><a href="<?= site_url('homeroom') ?>"><i class="bi bi-house-door me-1"></i>ระบบครูที่ปรึกษา</a></li>
                            <li class="breadcrumb-item active" aria-current="page">ชั้น ม.<?= esc($className) ?></li>
                        </ol>
                    </nav>
                </div>
                <h2 class="display-6 fw-bold text-white mb-2">
                    <i class="bi bi-house-heart-fill me-2 text-warning"></i>ห้องเรียนชั้นมัธยมศึกษาปีที่ <?= esc($className) ?>
                </h2>
                <p class="mb-3 text-white text-opacity-90 fs-6">
                    <i class="bi bi-calendar3 me-1"></i> ประจำภาคเรียนที่ <?= esc($term) ?> ปีการศึกษา <?= esc($year) ?> (พ.ศ.)
                </p>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge-advisor-pill">
                        <i class="bi bi-person-badge-fill text-warning"></i> ครูที่ปรึกษา: 
                        <?php if (!empty($coAdvisors)): ?>
                            <?php foreach ($coAdvisors as $idx => $adv): ?>
                                <?= ($idx > 0 ? ', ' : '') . esc($adv->pers_prefix . $adv->pers_firstname . ' ' . $adv->pers_lastname) ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?= esc($teacher->pers_prefix . $teacher->pers_firstname . ' ' . $teacher->pers_lastname) ?>
                        <?php endif; ?>
                    </span>
                    <span class="badge-advisor-pill">
                        <i class="bi bi-people-fill text-info"></i> นักเรียนทั้งหมด <?= esc($totalStudents) ?> คน
                    </span>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <?php if ($todayRecord): ?>
                    <div class="card bg-white bg-opacity-20 border-0 p-3 rounded-3 text-start d-inline-block w-100" style="backdrop-filter: blur(10px);">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-success rounded-pill px-3 py-1 text-white fw-bold">
                                <i class="bi bi-check-circle-fill me-1"></i> วันนี้เช็คชื่อแล้ว
                            </span>
                            <small class="text-white text-opacity-90"><?= esc(date('H:i', strtotime($todayRecord['chk_home_time'] ?? 'now'))) ?> น.</small>
                        </div>
                        <p class="text-white small mb-2">มา <?= count(array_filter(explode('|', $todayRecord['chk_home_ma'] ?? ''))) ?> คน | ขาด <?= count(array_filter(explode('|', $todayRecord['chk_home_khad'] ?? ''))) ?> คน | สาย <?= count(array_filter(explode('|', $todayRecord['chk_home_sahy'] ?? ''))) ?> คน</p>
                        <a href="<?= site_url('homeroom/add/' . date('Y-m-d')) ?>" class="btn btn-sm btn-hero-white w-100 rounded-pill">
                            <i class="bi bi-pencil-square me-1"></i> แก้ไขข้อมูลวันนี้
                        </a>
                    </div>
                <?php else: ?>
                    <div class="card bg-warning bg-opacity-25 border-0 p-3 rounded-3 text-start d-inline-block w-100" style="backdrop-filter: blur(10px);">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold">
                                <i class="bi bi-clock-history me-1"></i> ยังไม่ได้เช็คชื่อวันนี้
                            </span>
                            <small class="text-white text-opacity-90"><?= esc(thai_date(time(), 'short')) ?></small>
                        </div>
                        <p class="text-white small mb-2">กรุณาบันทึกการเช็คชื่อเข้าแถว/โฮมรูมประจำวัน</p>
                        <a href="<?= site_url('homeroom/add') ?>" class="btn btn-sm btn-warning text-dark fw-bold w-100 rounded-pill shadow-sm">
                            <i class="bi bi-calendar-check-fill me-1"></i> เช็คชื่อทันที
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Modules -->
    <div class="row g-3 mb-4">
        <!-- Module 1 -->
        <div class="col-md-4">
            <a href="<?= site_url('homeroom/students') ?>" class="quick-action-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-label-primary text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">ข้อมูลนักเรียนในห้อง</h6>
                        <p class="text-muted small mb-0">รายชื่อ, รูปถ่าย, ผู้ปกครอง, เบอร์โทร (<?= esc($totalStudents) ?> คน)</p>
                    </div>
                </div>
            </a>
        </div>
        <!-- Module 2 -->
        <div class="col-md-4">
            <a href="<?= site_url('homeroom/add') ?>" class="quick-action-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-label-success text-success">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">เช็คชื่อโฮมรูมประจำวัน</h6>
                        <p class="text-muted small mb-0">บันทึก มา/ขาด/ลา/สาย & หัวข้อโฮมรูม</p>
                    </div>
                </div>
            </a>
        </div>
        <!-- Module 4 -->
        <div class="col-md-4">
            <a href="<?= site_url('homeroom/academic-tracking') ?>" class="quick-action-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-label-warning text-warning">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">ติดตามผลการเรียน & กิจกรรม</h6>
                        <p class="text-muted small mb-0">ดูเกรดเฉลี่ย, ติด 0/ร/มส, สถานะชุมนุม</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Summary Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="homeroom-stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-medium">นักเรียนทั้งหมด</span>
                        <h3 class="fw-bold text-primary mb-0 mt-1"><?= esc($totalStudents) ?> <small class="fs-6 fw-normal text-muted">คน</small></h3>
                    </div>
                    <div class="stat-icon-wrapper bg-label-primary text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <div class="mt-2 text-muted small">
                    <span>ชาย: <?= esc($boyCount) ?></span> | <span>หญิง: <?= esc($girlCount) ?></span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="homeroom-stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-medium">เช็คชื่อล่าสุด</span>
                        <h3 class="fw-bold text-success mb-0 mt-1">
                            <?php 
                                $latestMa = !empty($latestRecord['chk_home_ma']) ? count(array_filter(explode('|', $latestRecord['chk_home_ma']))) : 0;
                                echo esc($latestMa);
                            ?> <small class="fs-6 fw-normal text-muted">คน (มา)</small>
                        </h3>
                    </div>
                    <div class="stat-icon-wrapper bg-label-success text-success">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
                <div class="mt-2 text-muted small">
                    วันที่: <?= isset($latestRecord['chk_home_date']) ? esc(thai_date($latestRecord['chk_home_date'], 'short')) : '-' ?>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="homeroom-stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-medium">ขาดเรียนล่าสุด</span>
                        <h3 class="fw-bold text-danger mb-0 mt-1">
                            <?php 
                                $latestKhad = !empty($latestRecord['chk_home_khad']) ? count(array_filter(explode('|', $latestRecord['chk_home_khad']))) : 0;
                                echo esc($latestKhad);
                            ?> <small class="fs-6 fw-normal text-muted">คน</small>
                        </h3>
                    </div>
                    <div class="stat-icon-wrapper bg-label-danger text-danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
                <div class="mt-2 text-muted small">
                    ลา: <?= !empty($latestRecord['chk_home_la']) ? count(array_filter(explode('|', $latestRecord['chk_home_la']))) : 0 ?> คน
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="homeroom-stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-medium">ประวัติทั้งหมด</span>
                        <h3 class="fw-bold text-info mb-0 mt-1">
                            <?= count($recentHistory) ?> <small class="fs-6 fw-normal text-muted">วัน</small>
                        </h3>
                    </div>
                    <div class="stat-icon-wrapper bg-label-info text-info">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
                <div class="mt-2 text-muted small">
                    <a href="<?= site_url('homeroom/history') ?>" class="text-info fw-bold text-decoration-none">ดูประวัติทั้งหมด →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm bg-label-primary rounded">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">ประวัติการเช็คชื่อ 10 วันล่าสุด</h5>
                    <small class="text-muted">บันทึกการเข้าเรียนและการเข้าแถวของห้อง ม.<?= esc($className) ?></small>
                </div>
            </div>
            <a href="<?= site_url('homeroom/history') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-right me-1"></i> ดูประวัติทั้งหมด
            </a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">วันที่</th>
                        <th class="text-center">มา</th>
                        <th class="text-center">ขาด</th>
                        <th class="text-center">ลา</th>
                        <th class="text-center">สาย</th>
                        <th class="text-center">กิจกรรม</th>
                        <th class="text-center">หนี</th>
                        <th class="text-center">รวม (คน)</th>
                        <th class="text-end pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentHistory)): ?>
                        <?php foreach ($recentHistory as $item): ?>
                            <?php 
                                $ma = count(array_filter(explode('|', $item['chk_home_ma'] ?? '')));
                                $khad = count(array_filter(explode('|', $item['chk_home_khad'] ?? '')));
                                $la = count(array_filter(explode('|', $item['chk_home_la'] ?? '')));
                                $sahy = count(array_filter(explode('|', $item['chk_home_sahy'] ?? '')));
                                $kid = count(array_filter(explode('|', $item['chk_home_kid'] ?? '')));
                                $hnee = count(array_filter(explode('|', $item['chk_home_hnee'] ?? '')));
                                $total = $ma + $khad + $la + $sahy + $kid + $hnee;
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?= esc(thai_date($item['chk_home_date'], 'full')) ?></div>
                                    <small class="text-muted"><?= esc(date('H:i', strtotime($item['chk_home_time'] ?? 'now'))) ?> น.</small>
                                </td>
                                <td class="text-center"><span class="attendance-status-pill status-badge-ma"><?= $ma ?></span></td>
                                <td class="text-center"><span class="attendance-status-pill status-badge-khad"><?= $khad ?></span></td>
                                <td class="text-center"><span class="attendance-status-pill status-badge-la"><?= $la ?></span></td>
                                <td class="text-center"><span class="attendance-status-pill status-badge-sahy"><?= $sahy ?></span></td>
                                <td class="text-center"><span class="attendance-status-pill status-badge-kid"><?= $kid ?></span></td>
                                <td class="text-center"><span class="attendance-status-pill status-badge-hnee"><?= $hnee ?></span></td>
                                <td class="text-center fw-bold text-dark"><?= $total ?></td>
                                <td class="text-end pe-4">
                                    <a href="<?= site_url('homeroom/add/' . $item['chk_home_date']) ?>" class="btn btn-sm btn-label-primary rounded-pill px-3">
                                        <i class="bi bi-pencil-square me-1"></i> แก้ไข
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                ยังไม่มีข้อมูลประวัติการเช็คชื่อ
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<?= $this->endSection() ?>
