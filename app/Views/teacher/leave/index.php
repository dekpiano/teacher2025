<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
<style>
    .flatpickr-calendar {
        font-family: 'K2D', sans-serif !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
        border: 1px solid #e2e8f0 !important;
    }
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
        background: #696cff !important;
        border-color: #696cff !important;
    }
    .flatpickr-months .flatpickr-month {
        color: #566a7f !important;
    }
    .flatpickr-current-month .cur-month {
        font-weight: 700 !important;
    }
    .flatpickr-year-be-select {
        font-family: 'K2D', sans-serif !important;
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        color: #566a7f !important;
        background: transparent !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        padding: 2px 6px !important;
        cursor: pointer !important;
        outline: none !important;
        margin-left: 4px !important;
    }
    .flatpickr-year-be-select:focus {
        border-color: #696cff !important;
        box-shadow: 0 0 0 2px rgba(105, 108, 255, 0.2) !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card mb-4 bg-primary">
            <div class="card-body d-flex align-items-center justify-content-between py-3">
                <div>
                    <h4 class="text-white mb-0">
                        <i class="bi bi-calendar-check-fill me-2"></i>ระบบการลาออนไลน์
                    </h4>
                    <p class="text-white opacity-75 mb-0">บันทึกและติดตามสถานะการลาของคุณ</p>
                </div>
                <button type="button" class="btn btn-white text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalLeave">
                    <i class="bi bi-plus-circle me-1"></i> เขียนใบลา
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Leave Summary Cards & Filter -->
<div class="row mb-4">
    <div class="col-12 mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h5 class="mb-0">
                <i class="bi bi-pie-chart-fill me-2 text-primary"></i>สรุปวันลาคงเหลือ
            </h5>
            <div class="d-flex align-items-center">
                <form id="filter-year-form" class="d-flex align-items-center bg-white p-1 rounded-pill shadow-sm border position-relative">
                    <!-- Dropdown ปีงบประมาณ -->
                    <div class="d-flex align-items-center ps-2 pe-1">
                        <i class="bi bi-calendar3 text-primary me-1 fs-7"></i>
                        <select name="year_id" id="select-year-filter" class="form-select form-select-sm border-0 bg-transparent py-0 pe-4 shadow-none fw-semibold" style="width: auto; cursor: pointer;">
                            <?php foreach ($leaveYears as $y): ?>
                                <option value="<?= $y['ly_id'] ?>" <?= ($selectedYearId == $y['ly_id']) ? 'selected' : '' ?>>
                                    ปีงบฯ <?= esc($y['ly_name']) ?> <?= ($y['ly_status'] == 'active') ? '★' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <span class="text-muted opacity-25">|</span>

                    <!-- Dropdown รอบประเมิน -->
                    <div class="d-flex align-items-center ps-2 pe-1">
                        <i class="bi bi-hourglass-split text-info me-1 fs-7"></i>
                        <select name="round" id="select-round-filter" class="form-select form-select-sm border-0 bg-transparent py-0 pe-4 shadow-none fw-semibold" style="width: auto; cursor: pointer;">
                            <option value="" <?= empty($selectedRound) ? 'selected' : '' ?>>รอบปัจจุบัน</option>
                            <option value="1" <?= ($selectedRound == '1') ? 'selected' : '' ?>>รอบ 1 (1 ต.ค. - 31 มี.ค.)</option>
                            <option value="2" <?= ($selectedRound == '2') ? 'selected' : '' ?>>รอบ 2 (1 เม.ย. - 30 ก.ย.)</option>
                        </select>
                    </div>

                    <!-- Spinner Loader Indicator -->
                    <div id="filter-loading-spinner" class="d-none align-items-center px-2 text-primary">
                        <div class="spinner-border spinner-border-sm text-primary" role="status" style="width: 14px; height: 14px; border-width: 2px;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Ultra-Modern Compact Summary Dashboard -->
    <div class="col-12" id="dashboard-summary-container">
        <div class="card shadow-sm border-0 mb-3 bg-white">
            <div class="card-body p-3">
                <div class="row g-3 align-items-center" id="stat-cards-container">
                    <!-- Stat 1: ลารอบ 6 เดือน -->
                    <?php 
                    $termUsed = $termLeaveInfo['used_in_term'] ?? 0;
                    $termMax = $termLeaveInfo['max_quota'] ?? 23.0;
                    $termRemaining = $termLeaveInfo['remaining_in_term'] ?? 23.0;
                    $termPercent = min(100, round(($termUsed / $termMax) * 100));
                    $termColor = ($termRemaining <= 3) ? 'danger' : (($termRemaining <= 7) ? 'warning' : 'primary');
                    ?>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center p-2 rounded-3 bg-label-<?= $termColor ?>">
                            <div class="avatar avatar-md me-2 flex-shrink-0">
                                <span class="avatar-initial rounded-3 bg-<?= $termColor ?> text-white shadow-sm">
                                    <i class="bi bi-hourglass-split fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold small text-truncate text-<?= $termColor ?>">
                                        <?= esc($termLeaveInfo['term_info']['short_name'] ?? 'รอบ 6 เดือน') ?>
                                    </span>
                                    <span class="badge bg-<?= $termColor ?> text-white" style="font-size: 10px;">เหลือ <?= number_format($termRemaining, 1) ?> ว.</span>
                                </div>
                                <div class="d-flex align-items-baseline gap-1">
                                    <h5 class="mb-0 fw-bold text-dark"><?= number_format($termUsed, 1) ?></h5>
                                    <small class="text-muted">/ 23 วัน</small>
                                </div>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-<?= $termColor ?>" style="width: <?= $termPercent ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 2: รวมทั้งปีงบประมาณ -->
                    <?php 
                    $totalPercent = ($totalAllQuota > 0) ? min(100, round(($totalAllUsed / $totalAllQuota) * 100)) : 0;
                    ?>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center p-2 rounded-3 bg-label-info">
                            <div class="avatar avatar-md me-2 flex-shrink-0">
                                <span class="avatar-initial rounded-3 bg-info text-white shadow-sm">
                                    <i class="bi bi-calendar2-check-fill fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold small text-truncate text-info">รวมทั้งปี <?= esc($activeYear->ly_name ?? '') ?></span>
                                    <span class="badge bg-info text-white" style="font-size: 10px;">เหลือ <?= number_format($totalAllRemaining, 1) ?> ว.</span>
                                </div>
                                <div class="d-flex align-items-baseline gap-1">
                                    <h5 class="mb-0 fw-bold text-dark"><?= number_format($totalAllUsed, 1) ?></h5>
                                    <small class="text-muted">/ 46 วัน</small>
                                </div>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-info" style="width: <?= $totalPercent ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 3: สถิติมาสาย -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center p-2 rounded-3 bg-label-danger" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalLateDetails">
                            <div class="avatar avatar-md me-2 flex-shrink-0">
                                <span class="avatar-initial rounded-3 bg-danger text-white shadow-sm">
                                    <i class="bi bi-clock-history fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold small text-truncate text-danger">สถิติมาสาย</span>
                                    <span class="badge bg-danger text-white" style="font-size: 10px;">คลิกดู</span>
                                </div>
                                <div class="d-flex align-items-baseline gap-1">
                                    <h5 class="mb-0 fw-bold text-dark"><?= number_format($lateCount) ?></h5>
                                    <small class="text-muted">ครั้ง</small>
                                </div>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-danger" style="width: <?= min(100, $lateCount * 10) ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 4: สถานะรวม -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center p-2 rounded-3 bg-label-success">
                            <div class="avatar avatar-md me-2 flex-shrink-0">
                                <span class="avatar-initial rounded-3 bg-success text-white shadow-sm">
                                    <i class="bi bi-shield-check fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold small text-truncate text-success">สถานะการลา</span>
                                    <span class="badge bg-success text-white" style="font-size: 10px;">ปกติ</span>
                                </div>
                                <div class="d-flex align-items-baseline gap-1">
                                    <h5 class="mb-0 fw-bold text-dark"><?= count($leaves) ?></h5>
                                    <small class="text-muted">รายการทั้งหมด</small>
                                </div>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sleek Chips for Individual Leave Types -->
                <div class="d-flex flex-wrap gap-2 mt-3 pt-2 border-top align-items-center" id="leave-chips-container">
                    <small class="text-muted fw-semibold me-1"><i class="bi bi-tags-fill me-1"></i>โควตาแต่ละประเภท:</small>
                    <?php 
                    $typeColors = ['success', 'primary', 'warning', 'info', 'secondary', 'dark'];
                    $typeIcons = ['bi-heart-pulse-fill', 'bi-briefcase-fill', 'bi-umbrella-fill', 'bi-balloon-heart-fill', 'bi-people-fill', 'bi-moon-stars-fill'];
                    foreach ($leaveSummary as $index => $summary): 
                        $c = $typeColors[$index % count($typeColors)];
                        $ic = $typeIcons[$index % count($typeIcons)];
                    ?>
                    <div class="d-inline-flex align-items-center py-1 px-2 rounded-2 border bg-light small" style="font-size: 12px;">
                        <i class="bi <?= $ic ?> text-<?= $c ?> me-1"></i>
                        <span class="text-muted me-1"><?= esc($summary['type_name']) ?>:</span>
                        <strong class="text-<?= $c ?> me-1"><?= number_format($summary['remaining'], 1) ?></strong>
                        <span class="text-muted" style="font-size: 10px;">/<?= number_format($summary['quota'], 0) ?> ว.</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal รายละเอียดการมาสาย -->
<div class="modal fade" id="modalLateDetails" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-clock-history me-2 text-danger"></i>รายละเอียดการมาสาย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($lateDetails)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle text-success fs-1"></i>
                        <p class="mt-2">ยอดเยี่ยมมาก! ไม่พบประวัติการมาสายในปีการศึกษานี้</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr class="table-light">
                                    <th class="text-center" width="60">ลำดับ</th>
                                    <th>วันที่มาสาย</th>
                                    <th class="text-center">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lateDetails as $index => $late): ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td>
                                            <div class="fw-bold"><?= date('d/m/Y', strtotime($late['att_date'])) ?></div>
                                            <small class="text-muted"><?= date('l', strtotime($late['att_date'])) ?></small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-danger">มาสาย</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- ประวัติการลา -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2 text-primary"></i>ประวัติการลา 
                    <span id="table-year-badge" class="badge bg-label-info ms-2 fs-6 fw-normal">ประจำปีงบประมาณ <?= esc($activeYear->ly_name ?? '') ?></span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>วันที่บันทึก</th>
                                <th>ประเภท</th>
                                <th>เรื่องที่ลา</th>
                                <th>วันที่ลา</th>
                                <th class="text-center">จำนวนวัน</th>
                                <th>สถานะ</th>
                                <th>แหล่งที่มา</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaves as $leave) : ?>
                                <tr>
                                    <td><?= $leave['created_at'] ? date('d/m/Y H:i', strtotime($leave['created_at'])) : '-' ?></td>
                                    <td>
                                        <span class="badge bg-label-info"><?= esc($leave['type_name']) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= esc($leave['topic']) ?></div>
                                        <small class="text-muted"><?= mb_strimwidth(esc($leave['detail'] ?? ''), 0, 50, "...") ?></small>
                                    </td>
                                    <td>
                                        <div><?= date('d/m/Y', strtotime($leave['start_date'])) ?></div>
                                        <small class="text-muted">ถึง <?= date('d/m/Y', strtotime($leave['end_date'])) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold"><?= number_format($leave['total_days'], 1) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($leave['status'] == 'pending') : ?>
                                            <span class="badge bg-label-warning"><i class="bi bi-clock me-1"></i> รออนุมัติ</span>
                                        <?php elseif ($leave['status'] == 'approved') : ?>
                                            <span class="badge bg-label-success"><i class="bi bi-check-circle me-1"></i> อนุมัติแล้ว</span>
                                        <?php elseif ($leave['status'] == 'rejected') : ?>
                                            <span class="badge bg-label-danger"><i class="bi bi-x-circle me-1"></i> ไม่อนุมัติ</span>
                                        <?php else : ?>
                                            <span class="badge bg-label-secondary"><?= esc($leave['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($leave['source'] == 'self') : ?>
                                            <span class="badge bg-label-primary"><i class="bi bi-person-fill me-1"></i> ลาเอง</span>
                                        <?php else : ?>
                                            <span class="badge bg-label-secondary"><i class="bi bi-person-badge me-1"></i> เจ้าหน้าที่บันทึก</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <?php if ($leave['source'] == 'self') : ?>
                                                <a href="<?= base_url('leave/print/' . $leave['id']) ?>" class="btn btn-sm btn-outline-info" target="_blank" title="พิมพ์ใบลา">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($leave['can_cancel']) : ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteLeave(<?= $leave['id'] ?>)" title="ยกเลิกใบลา">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                            <?php if ($leave['source'] != 'self' && !$leave['can_cancel']) : ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal เขียนใบลา -->
<div class="modal fade" id="modalLeave" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= base_url('leave/create') ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLeaveTitle"><i class="bi bi-pencil-square me-2"></i>เขียนใบลาออนไลน์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="leave_type_id" name="leave_type_id" required>
                                    <option value="">-- เลือกประเภทการลา --</option>
                                    <?php foreach ($leaveSummary as $summary) : ?>
                                        <option value="<?= $summary['type_id'] ?>"><?= esc($summary['type_name']) ?> (คงเหลือ <?= number_format($summary['remaining'], 1) ?> วัน)</option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="leave_type_id">ประเภทการลา <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="leave_period" name="leave_period">
                                    <option value="full">เต็มวัน</option>
                                    <option value="morning">ครึ่งเช้า</option>
                                    <option value="afternoon">ครึ่งบ่าย</option>
                                </select>
                                <label for="leave_period">ระยะเวลา <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="leave_topic" name="leave_topic" placeholder="ตัวอย่าง: ลาป่วยเนื่องจากเป็นไข้หวัด" required />
                                <label for="leave_topic">เรื่องที่ลา <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating form-floating-outline position-relative">
                                <input type="text" class="form-control bg-white thai-datepicker" id="leave_start_date" name="leave_start_date" placeholder="วว/ดด/ปปปป (พ.ศ.)" required readonly />
                                <label for="leave_start_date"><i class="bi bi-calendar-event me-1 text-primary"></i> ตั้งแต่วันที่ (พ.ศ.) <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating form-floating-outline position-relative">
                                <input type="text" class="form-control bg-white thai-datepicker" id="leave_end_date" name="leave_end_date" placeholder="วว/ดด/ปปปป (พ.ศ.)" required readonly />
                                <label for="leave_end_date"><i class="bi bi-calendar-event me-1 text-primary"></i> ถึงวันที่ (พ.ศ.) <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-floating form-floating-outline">
                                <textarea class="form-control" id="leave_detail" name="leave_detail" style="height: 80px" placeholder="รายละเอียดการลา (ถ้ามี)"></textarea>
                                <label for="leave_detail">เหตุผล/รายละเอียดการลา</label>
                            </div>
                        </div>
                        
                        <!-- ช่องเพิ่มเติมตามแบบฟอร์มราชการ -->
                        <div class="col-12">
                            <div class="alert alert-light border mb-3 p-3">
                                <h6 class="alert-heading mb-3"><i class="bi bi-geo-alt me-2"></i>ข้อมูลติดต่อระหว่างลา (ตามแบบฟอร์ม บค.๐๐๒/๒๕๖๘)</h6>
                                <div class="row g-3">
                                    <div class="col-12 col-md-8">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="leave_contact_address" name="leave_contact_address" value="<?= esc($teacher['pers_address'] ?? '') ?>" placeholder="ที่อยู่ที่ติดต่อได้" />
                                            <label for="leave_contact_address">ที่อยู่ที่ติดต่อได้ระหว่างลา</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="tel" class="form-control" id="leave_contact_phone" name="leave_contact_phone" value="<?= esc($teacher['pers_phone'] ?? '') ?>" placeholder="เบอร์โทรศัพท์" />
                                            <label for="leave_contact_phone">เบอร์โทรศัพท์</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label for="leave_file" class="form-label">แนบหลักฐาน (ถ้ามี, ไฟล์ .pdf, .jpg, .png)</label>
                            <input class="form-control" type="file" id="leave_file" name="leave_file">
                        </div>
                        <div class="col-12 mt-3" id="quota-info" style="display:none;">
                            <div class="alert alert-info d-flex align-items-center mb-0" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <div>
                                    โควตาคงเหลือ: <span id="remaining-days" class="fw-bold">0</span> วัน 
                                    (ใช้ไปแล้ว <span id="used-days">0</span>/<span id="total-quota">0</span>)
                                    <div id="request-preview" class="small mt-1" style="display:none;">
                                        ลาครั้งนี้: <span id="request-days-preview" class="fw-bold">0</span> วัน
                                    </div>
                                </div>
                            </div>
                            <div id="quota-warning" class="alert alert-danger d-flex align-items-center mt-2 mb-0" style="display:none;">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>ขออภัย วันลาของคุณครูไม่พอสำหรับการลาครั้งนี้</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-leave">ส่งใบลา</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Flatpickr JS & Thai Localization -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>

<script>
    let dataTableInstance = null;
    let startDatePicker = null;
    let endDatePicker = null;

    $(function() {
        // Initialize Thai Buddhist Era Flatpickr
        const thaiMonthsShort = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        const thaiMonthsFull = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];

        // Custom Thai Buddhist Era Formatter Function
        function formatThaiBE(date) {
            if (!date) return '';
            const d = date.getDate();
            const m = thaiMonthsFull[date.getMonth()];
            const y = date.getFullYear() + 543;
            return `${d} ${m} ${y}`;
        }

        // Flatpickr common config
        const fpConfig = {
            locale: 'th',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'j F Y',
            formatDate: function(date, format, locale) {
                const day = String(date.getDate()).padStart(2, '0');
                const month = thaiMonthsFull[date.getMonth()];
                const yearBE = date.getFullYear() + 543;
                return `${day} ${month} ${yearBE}`;
            },
            onReady: function(selectedDates, dateStr, instance) {
                // Render interactive Thai Buddhist Era (พ.ศ.) Year Dropdown
                function renderThaiYearDropdown() {
                    const currentYearAD = instance.currentYear;
                    const $container = $(instance.calendarContainer);
                    const $numInputWrapper = $container.find('.numInputWrapper');
                    
                    // Generate Year Options (-3 to +3 years around current year)
                    const baseYear = new Date().getFullYear();
                    let optionsHtml = '';
                    for (let y = baseYear - 3; y <= baseYear + 3; y++) {
                        const yBE = y + 543;
                        const isSelected = (y === currentYearAD) ? 'selected' : '';
                        optionsHtml += `<option value="${y}" ${isSelected}>${yBE}</option>`;
                    }

                    if ($numInputWrapper.length > 0) {
                        $numInputWrapper.hide();
                    }

                    let $yearSelect = $container.find('.flatpickr-year-be-select');
                    if ($yearSelect.length === 0) {
                        $yearSelect = $(`<select class="flatpickr-year-be-select" aria-label="เลือกปี พ.ศ.">${optionsHtml}</select>`);
                        $container.find('.flatpickr-current-month').append($yearSelect);

                        $yearSelect.on('change', function(e) {
                            e.stopPropagation();
                            const chosenYearAD = parseInt($(this).val(), 10);
                            instance.changeYear(chosenYearAD);
                        });
                    } else {
                        $yearSelect.html(optionsHtml);
                        $yearSelect.val(currentYearAD);
                    }
                }

                renderThaiYearDropdown();

                instance.config.onMonthChange.push(function(selectedDates, dateStr, inst) {
                    renderThaiYearDropdown();
                });
                instance.config.onYearChange.push(function(selectedDates, dateStr, inst) {
                    renderThaiYearDropdown();
                });
            }
        };

        startDatePicker = flatpickr("#leave_start_date", {
            ...fpConfig,
            onChange: function(selectedDates, dateStr) {
                if (selectedDates.length > 0) {
                    // Set min date for end date picker
                    endDatePicker.set('minDate', selectedDates[0]);
                    // If end date is empty or earlier than start date, sync it
                    const endSelected = endDatePicker.selectedDates[0];
                    if (!endSelected || endSelected < selectedDates[0]) {
                        endDatePicker.setDate(selectedDates[0], true);
                    }
                }
                checkQuota();
            }
        });

        endDatePicker = flatpickr("#leave_end_date", {
            ...fpConfig,
            onChange: function(selectedDates, dateStr) {
                checkQuota();
            }
        });

        dataTableInstance = $('.datatable').DataTable({
            order: [[0, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
            }
        });

        // AJAX Filter Change without page refresh
        $('#select-year-filter, #select-round-filter').on('change', function() {
            applyLeaveFilter();
        });

        // Real-time Quota Check
        $('#leave_type_id, input[name="leave_period"]').on('change', function() {
            checkQuota();
        });

        // Form Submit Validation Check
        $('#modalLeave form').on('submit', function(e) {
            if ($('#btn-submit-leave').prop('disabled')) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'วันลาไม่เพียงพอ',
                    text: 'จำนวนวันที่ท่านต้องการลา เกินกว่าโควตาวันลาคงเหลือ',
                    confirmButtonColor: '#696cff'
                });
                return false;
            }
        });
    });

    function applyLeaveFilter() {
        const yearId = $('#select-year-filter').val();
        const roundVal = $('#select-round-filter').val();

        // 1. Show Spinner & Loading Overlay
        $('#filter-loading-spinner').removeClass('d-none').addClass('d-flex');
        $('#dashboard-summary-container').css({
            'opacity': '0.4',
            'transition': 'opacity 0.2s ease'
        });
        
        // Show table loading state if DataTable exists
        if (dataTableInstance) {
            $('.datatable tbody').css({
                'opacity': '0.4',
                'transition': 'opacity 0.2s ease'
            });
        }

        $.get('<?= base_url('leave/filterData') ?>', {
            year_id: yearId,
            round: roundVal
        }, function(res) {
            // Hide Spinner & Restore Opacity
            $('#filter-loading-spinner').removeClass('d-flex').addClass('d-none');
            $('#dashboard-summary-container').css('opacity', '1');
            $('.datatable tbody').css('opacity', '1');

            if (res.status === 'success') {
                // 1. Update Term Card
                const termUsed = res.termLeaveInfo.used_in_term;
                const termRemaining = res.termLeaveInfo.remaining_in_term;
                const termPercent = Math.min(100, Math.round((termUsed / 23) * 100));
                const termColor = (termRemaining <= 3) ? 'danger' : ((termRemaining <= 7) ? 'warning' : 'primary');

                // 2. Update Total Year Card
                const totalUsed = res.totalAllUsed;
                const totalRemaining = res.totalAllRemaining;
                const totalPercent = Math.min(100, Math.round((totalUsed / 46) * 100));
                const yearName = res.activeYear ? res.activeYear.ly_name : '';

                // Update Table Badge
                $('#table-year-badge').text('ประจำปีงบประมาณ ' + yearName);

                // 3. Re-render Stat Cards
                let statsHtml = `
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center p-2 rounded-3 bg-label-${termColor}">
                            <div class="avatar avatar-md me-2 flex-shrink-0">
                                <span class="avatar-initial rounded-3 bg-${termColor} text-white shadow-sm">
                                    <i class="bi bi-hourglass-split fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold small text-truncate text-${termColor}">
                                        ${res.termLeaveInfo.term_info.short_name}
                                    </span>
                                    <span class="badge bg-${termColor} text-white" style="font-size: 10px;">เหลือ ${termRemaining.toFixed(1)} ว.</span>
                                </div>
                                <div class="d-flex align-items-baseline gap-1">
                                    <h5 class="mb-0 fw-bold text-dark">${termUsed.toFixed(1)}</h5>
                                    <small class="text-muted">/ 23 วัน</small>
                                </div>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-${termColor}" style="width: ${termPercent}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center p-2 rounded-3 bg-label-info">
                            <div class="avatar avatar-md me-2 flex-shrink-0">
                                <span class="avatar-initial rounded-3 bg-info text-white shadow-sm">
                                    <i class="bi bi-calendar2-check-fill fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold small text-truncate text-info">รวมทั้งปี ${yearName}</span>
                                    <span class="badge bg-info text-white" style="font-size: 10px;">เหลือ ${totalRemaining.toFixed(1)} ว.</span>
                                </div>
                                <div class="d-flex align-items-baseline gap-1">
                                    <h5 class="mb-0 fw-bold text-dark">${totalUsed.toFixed(1)}</h5>
                                    <small class="text-muted">/ 46 วัน</small>
                                </div>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-info" style="width: ${totalPercent}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center p-2 rounded-3 bg-label-danger" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalLateDetails">
                            <div class="avatar avatar-md me-2 flex-shrink-0">
                                <span class="avatar-initial rounded-3 bg-danger text-white shadow-sm">
                                    <i class="bi bi-clock-history fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold small text-truncate text-danger">สถิติมาสาย</span>
                                    <span class="badge bg-danger text-white" style="font-size: 10px;">คลิกดู</span>
                                </div>
                                <div class="d-flex align-items-baseline gap-1">
                                    <h5 class="mb-0 fw-bold text-dark">${res.lateCount}</h5>
                                    <small class="text-muted">ครั้ง</small>
                                </div>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-danger" style="width: ${Math.min(100, res.lateCount * 10)}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="d-flex align-items-center p-2 rounded-3 bg-label-success">
                            <div class="avatar avatar-md me-2 flex-shrink-0">
                                <span class="avatar-initial rounded-3 bg-success text-white shadow-sm">
                                    <i class="bi bi-shield-check fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold small text-truncate text-success">สถานะการลา</span>
                                    <span class="badge bg-success text-white" style="font-size: 10px;">ปกติ</span>
                                </div>
                                <div class="d-flex align-items-baseline gap-1">
                                    <h5 class="mb-0 fw-bold text-dark">${res.leaves.length}</h5>
                                    <small class="text-muted">รายการทั้งหมด</small>
                                </div>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#stat-cards-container').html(statsHtml);

                // 4. Re-render Chips
                const colors = ['success', 'primary', 'warning', 'info', 'secondary', 'dark'];
                const icons = ['bi-heart-pulse-fill', 'bi-briefcase-fill', 'bi-umbrella-fill', 'bi-balloon-heart-fill', 'bi-people-fill', 'bi-moon-stars-fill'];
                let chipsHtml = '<small class="text-muted fw-semibold me-1"><i class="bi bi-tags-fill me-1"></i>โควตาแต่ละประเภท:</small>';
                res.leaveSummary.forEach((s, idx) => {
                    const c = colors[idx % colors.length];
                    const ic = icons[idx % icons.length];
                    chipsHtml += `
                        <div class="d-inline-flex align-items-center py-1 px-2 rounded-2 border bg-light small" style="font-size: 12px;">
                            <i class="bi ${ic} text-${c} me-1"></i>
                            <span class="text-muted me-1">${s.type_name}:</span>
                            <strong class="text-${c} me-1">${s.remaining.toFixed(1)}</strong>
                            <span class="text-muted" style="font-size: 10px;">/${s.quota} ว.</span>
                        </div>
                    `;
                });
                $('#leave-chips-container').html(chipsHtml);

                // 5. Update DataTable Rows
                if (dataTableInstance) {
                    dataTableInstance.clear();
                    res.leaves.forEach(leave => {
                        const createDate = leave.created_at ? new Date(leave.created_at) : null;
                        const createDateStr = createDate ? `${String(createDate.getDate()).padStart(2, '0')}/${String(createDate.getMonth() + 1).padStart(2, '0')}/${createDate.getFullYear()} ${String(createDate.getHours()).padStart(2, '0')}:${String(createDate.getMinutes()).padStart(2, '0')}` : '-';

                        const startDate = new Date(leave.start_date);
                        const endDate = new Date(leave.end_date);
                        const startStr = `${String(startDate.getDate()).padStart(2, '0')}/${String(startDate.getMonth() + 1).padStart(2, '0')}/${startDate.getFullYear()}`;
                        const endStr = `${String(endDate.getDate()).padStart(2, '0')}/${String(endDate.getMonth() + 1).padStart(2, '0')}/${endDate.getFullYear()}`;

                        let statusBadge = '';
                        if (leave.status === 'pending') {
                            statusBadge = '<span class="badge bg-label-warning"><i class="bi bi-clock me-1"></i> รออนุมัติ</span>';
                        } else if (leave.status === 'approved') {
                            statusBadge = '<span class="badge bg-label-success"><i class="bi bi-check-circle me-1"></i> อนุมัติแล้ว</span>';
                        } else if (leave.status === 'rejected') {
                            statusBadge = '<span class="badge bg-label-danger"><i class="bi bi-x-circle me-1"></i> ไม่อนุมัติ</span>';
                        } else {
                            statusBadge = `<span class="badge bg-label-secondary">${leave.status}</span>`;
                        }

                        const sourceBadge = (leave.source === 'self')
                            ? '<span class="badge bg-label-primary"><i class="bi bi-person-fill me-1"></i> ลาเอง</span>'
                            : '<span class="badge bg-label-secondary"><i class="bi bi-person-badge me-1"></i> เจ้าหน้าที่บันทึก</span>';

                        let actionsHtml = '<div class="d-flex gap-1">';
                        if (leave.source === 'self') {
                            actionsHtml += `<a href="<?= base_url('leave/print/') ?>${leave.id}" class="btn btn-sm btn-outline-info" target="_blank" title="พิมพ์ใบลา"><i class="bi bi-printer"></i></a>`;
                        }
                        if (leave.can_cancel) {
                            actionsHtml += `<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteLeave(${leave.id})" title="ยกเลิกใบลา"><i class="bi bi-trash"></i></button>`;
                        }
                        if (leave.source !== 'self' && !leave.can_cancel) {
                            actionsHtml += '<span class="text-muted">-</span>';
                        }
                        actionsHtml += '</div>';

                        dataTableInstance.row.add([
                            createDateStr,
                            `<span class="badge bg-label-info">${leave.type_name}</span>`,
                            `<div class="fw-bold">${leave.topic}</div><small class="text-muted">${(leave.detail || '').substring(0, 50)}</small>`,
                            `<div>${startStr}</div><small class="text-muted">ถึง ${endStr}</small>`,
                            `<div class="text-center fw-bold">${parseFloat(leave.total_days).toFixed(1)}</div>`,
                            statusBadge,
                            sourceBadge,
                            actionsHtml
                        ]);
                    });
                    dataTableInstance.draw();
                }
            }
        });
    }

    let currentCanLeave = true;

    function checkQuota() {
        const typeId = $('#leave_type_id').val();
        const start = $('#leave_start_date').val();
        const end = $('#leave_end_date').val();
        const period = $('input[name="leave_period"]:checked').val() || 'full';

        if (typeId) {
            $.post('<?= base_url('leave/check-quota') ?>', {
                leave_type_id: typeId,
                leave_start_date: start,
                leave_end_date: end,
                leave_period: period
            }, function(res) {
                if (res.status === 'success') {
                    $('#quota-info').show();
                    $('#remaining-days').text(res.remaining.toFixed(1));
                    $('#used-days').text(res.used.toFixed(1));
                    $('#total-quota').text(res.quota);

                    // If request days are calculated
                    if (res.request_days > 0 && start && end) {
                        $('#request-preview').show();
                        $('#request-days-preview').text(res.request_days.toFixed(1));
                        
                        // Check if request exceeds remaining quota or term quota
                        if (!res.can_leave) {
                            currentCanLeave = false;
                            let warnMsg = 'ขออภัย วันลาของคุณครูไม่พอสำหรับการลาครั้งนี้';
                            if (res.term_check && res.term_check.term_exceeded) {
                                warnMsg = res.term_check.term_message;
                            }
                            $('#quota-warning').show().html('<i class="bi bi-exclamation-triangle-fill me-2"></i><div>' + warnMsg + '</div>');
                            $('#btn-submit-leave').prop('disabled', true);
                        } else {
                            currentCanLeave = true;
                            $('#quota-warning').hide();
                            $('#btn-submit-leave').prop('disabled', false);
                        }
                    } else {
                        // Just checking type quota
                        if (res.remaining <= 0) {
                            currentCanLeave = false;
                            $('#quota-warning').show().html('<i class="bi bi-exclamation-triangle-fill me-2"></i><div>โควตาวันลาประเภทนี้หมดแล้ว</div>');
                            $('#btn-submit-leave').prop('disabled', true);
                        } else {
                            currentCanLeave = true;
                            $('#request-preview').hide();
                            $('#quota-warning').hide();
                            $('#btn-submit-leave').prop('disabled', false);
                        }
                    }
                }
            });
        } else {
            $('#quota-info').hide();
            $('#quota-warning').hide();
            $('#btn-submit-leave').prop('disabled', false);
        }
    }

    function deleteLeave(id) {
        Swal.fire({
            title: 'ยกเลิกใบลา?',
            text: "คุณต้องการยกเลิกคำขอลาใช่หรือไม่!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ยกเลิกเลย!',
            cancelButtonText: 'ไม่'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url('leave/delete/') ?>' + id;
            }
        })
    }
</script>
<?= $this->endSection() ?>
