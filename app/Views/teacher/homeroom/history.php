<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ประวัติ & สถิติการมาเรียน') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .history-header-card {
        background: linear-gradient(135deg, #5046e5 0%, #696cff 50%, #8c7cf0 100%);
        border-radius: 1.25rem;
        padding: 2rem 2.25rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(105, 108, 255, 0.28);
    }

    .history-header-card::after {
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

    .attendance-status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.65rem;
        border-radius: 0.5rem;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .status-badge-ma {
        background: #dcfce7;
        color: #15803d;
    }

    .status-badge-khad {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-badge-la {
        background: #fef9c3;
        color: #854d0e;
    }

    .status-badge-sahy {
        background: #ffedd5;
        color: #c2410c;
    }

    .status-badge-kid {
        background: #e0f2fe;
        color: #0369a1;
    }

    .status-badge-hnee {
        background: #f3e8ff;
        color: #7e22ce;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid py-2">
    <!-- Header Banner -->
    <div class="history-header-card mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="breadcrumb-light-wrapper mb-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-light mb-0">
                            <li class="breadcrumb-item"><a href="<?= site_url('homeroom') ?>"><i
                                        class="bi bi-house-door me-1"></i>ระบบครูที่ปรึกษา</a></li>
                            <li class="breadcrumb-item active" aria-current="page">ประวัติ & สถิติการมาเรียน</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="display-6 fw-bold text-white mb-2">
                    <i class="bi bi-clock-history me-2 text-warning"></i>ประวัติ & สถิติการมาเรียน
                    ม.<?= esc($className) ?>
                </h2>
                <p class="mb-3 text-white text-opacity-90 fs-6">
                    <i class="bi bi-calendar3 me-1"></i> ประจำภาคเรียนที่ <?= esc($term) ?>/<?= esc($year) ?> |
                    บันทึกแล้วทั้งหมด: <strong><?= esc($totalDays) ?></strong> วัน
                </p>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-white bg-opacity-20 text-dark rounded-pill px-3 py-2">
                        <i class="bi bi-people me-1"></i> นักเรียน: <?= count($students) ?> คน
                    </span>
                    <span class="badge bg-white bg-opacity-20 text-dark rounded-pill px-3 py-2">
                        <i class="bi bi-calendar-check me-1"></i> จำนวนวันเช็คชื่อ: <?= esc($totalDays) ?> วัน
                    </span>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="<?= site_url('homeroom/add') ?>"
                    class="btn btn-warning text-dark rounded-pill px-4 fw-bold shadow">
                    <i class="bi bi-plus-circle me-1"></i> เช็คชื่อวันนี้
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs nav-fill border-0" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active py-3 fw-bold fs-6 border-0 border-bottom border-3 border-primary"
                        id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily-pane" type="button" role="tab">
                        <i class="bi bi-calendar-range me-2 text-primary"></i>ประวัติการเช็คชื่อรายวัน
                        (<?= count($history) ?> วัน)
                    </button>
                </li>
                <li class="nav-item">
                    <button
                        class="nav-link py-3 fw-bold fs-6 border-0 border-bottom border-3 border-transparent text-muted"
                        id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary-pane" type="button" role="tab">
                        <i class="bi bi-bar-chart-fill me-2 text-success"></i>สถิติรายบุคคลตลอดภาคเรียน
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content p-0">
            <!-- Tab 1: Daily Log -->
            <div class="tab-pane fade show active" id="daily-pane" role="tabpanel" tabindex="0">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">ลำดับ</th>
                                <th>วันที่เช็คชื่อ</th>
                                <th>เวลา</th>
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
                            <?php if (!empty($history)): ?>
                                <?php foreach ($history as $idx => $item): ?>
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
                                        <td class="ps-4 text-muted"><?= $idx + 1 ?></td>
                                        <td>
                                            <span
                                                class="fw-bold text-dark fs-6"><?= esc(thai_date($item['chk_home_date'], 'full')) ?></span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-label-secondary font-monospace"><?= esc(date('H:i', strtotime($item['chk_home_time'] ?? 'now'))) ?>
                                                น.</span>
                                        </td>
                                        <td class="text-center"><span
                                                class="attendance-status-pill status-badge-ma"><?= $ma ?></span></td>
                                        <td class="text-center"><span
                                                class="attendance-status-pill status-badge-khad"><?= $khad ?></span></td>
                                        <td class="text-center"><span
                                                class="attendance-status-pill status-badge-la"><?= $la ?></span></td>
                                        <td class="text-center"><span
                                                class="attendance-status-pill status-badge-sahy"><?= $sahy ?></span></td>
                                        <td class="text-center"><span
                                                class="attendance-status-pill status-badge-kid"><?= $kid ?></span></td>
                                        <td class="text-center"><span
                                                class="attendance-status-pill status-badge-hnee"><?= $hnee ?></span></td>
                                        <td class="text-center fw-bold text-dark"><?= $total ?></td>
                                        <td class="text-end pe-4">
                                            <a href="<?= site_url('homeroom/add/' . $item['chk_home_date']) ?>"
                                                class="btn btn-sm btn-label-primary rounded-pill px-3">
                                                <i class="bi bi-pencil-square me-1"></i> แก้ไข
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center py-5 text-muted">
                                        <i class="bi bi-calendar-x fs-1 d-block mb-2 text-warning"></i>
                                        ยังไม่มีประวัติการเช็คชื่อ
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Individual Student Attendance Rates -->
            <div class="tab-pane fade" id="summary-pane" role="tabpanel" tabindex="0">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 60px;">เลขที่</th>
                                <th style="width: 110px;">รหัสนักเรียน</th>
                                <th>ชื่อ - นามสกุล</th>
                                <th class="text-center">มา (วัน)</th>
                                <th class="text-center">ขาด (วัน)</th>
                                <th class="text-center">ลา (วัน)</th>
                                <th class="text-center">สาย (วัน)</th>
                                <th class="text-center">กิจกรรม (วัน)</th>
                                <th class="text-center" style="width: 220px;">อัตราการมาเรียน (%)</th>
                                <th class="text-center">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($studentSummary)): ?>
                                <?php foreach ($studentSummary as $code => $sum): ?>
                                    <?php
                                    $st = $sum['student'];
                                    $rate = $sum['rate'];
                                    $rateColor = 'success';
                                    $statusLabel = 'ปกติ';
                                    if ($rate < 80) {
                                        $rateColor = 'danger';
                                        $statusLabel = 'เสี่ยง มส. (<80%)';
                                    } elseif ($rate < 85) {
                                        $rateColor = 'warning';
                                        $statusLabel = 'เฝ้าระวัง';
                                    }
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
                                            <small class="text-muted"><?= esc($st->StudentStudyLine ?? 'สามัญ') ?></small>
                                        </td>
                                        <td class="text-center"><span
                                                class="attendance-status-pill status-badge-ma"><?= $sum['ma'] ?></span></td>
                                        <td class="text-center"><span
                                                class="attendance-status-pill status-badge-khad"><?= $sum['khad'] ?></span></td>
                                        <td class="text-center"><span
                                                class="attendance-status-pill status-badge-la"><?= $sum['la'] ?></span></td>
                                        <td class="text-center"><span
                                                class="attendance-status-pill status-badge-sahy"><?= $sum['sahy'] ?></span></td>
                                        <td class="text-center"><span
                                                class="attendance-status-pill status-badge-kid"><?= $sum['kid'] ?></span></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 8px;">
                                                    <div class="progress-bar bg-<?= $rateColor ?>" role="progressbar"
                                                        style="width: <?= $rate ?>%" aria-valuenow="<?= $rate ?>"
                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="fw-bold text-<?= $rateColor ?> small"
                                                    style="min-width: 45px;"><?= $rate ?>%</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-<?= $rateColor ?> rounded-pill">
                                                <?= $statusLabel ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
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