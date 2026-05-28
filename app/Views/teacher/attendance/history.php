<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
ประวัติการเช็คชื่อ
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .filter-card {
        background: #fff;
        border-radius: 1.25rem;
        padding: 1.25rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        margin-bottom: 1.5rem;
    }
    .history-table img.selfie-thumb {
        width: 45px;
        height: 45px;
        object-fit: cover;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .history-table img.selfie-thumb:hover {
        transform: scale(1.1);
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-clock-history me-2 text-primary"></i>ประวัติการเช็คชื่อ</h4>
            <p class="text-muted mb-0">ดูประวัติการเช็คชื่อเข้า-ออกงานย้อนหลัง</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-card">
        <form method="get" action="<?= base_url('attendance/history') ?>" class="row g-3 align-items-end">
            <div class="col-md-3 col-6">
                <label class="form-label">เดือน</label>
                <select name="month" class="form-select">
                    <?php foreach ($months as $num => $name): ?>
                        <option value="<?= $num ?>" <?= $month == $num ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label">ปี (พ.ศ.)</label>
                <select name="year" class="form-select">
                    <?php for ($y = date('Y') + 543; $y >= date('Y') + 540; $y--): ?>
                        <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> ค้นหา
                </button>
            </div>
            <div class="col-md-3">
                <a href="<?= base_url('attendance') ?>" class="btn btn-outline-primary w-100">
                    <i class="bi bi-clock-history me-1"></i> เช็คชื่อวันนี้
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100 border-primary">
                <div class="card-body text-center p-3">
                    <i class="bi bi-check-circle-fill text-primary fs-2"></i>
                    <h3 class="fw-bold mb-0"><?= $summary['ปกติ'] ?></h3>
                    <small class="text-muted">มาทำงานปกติ (วัน)</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-warning">
                <div class="card-body text-center p-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-2"></i>
                    <h3 class="fw-bold mb-0"><?= $summary['มาสาย'] ?></h3>
                    <small class="text-muted">มาสาย (วัน)</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-danger">
                <div class="card-body text-center p-3">
                    <i class="bi bi-x-circle-fill text-danger fs-2"></i>
                    <h3 class="fw-bold mb-0"><?= $summary['ขาด'] ?></h3>
                    <small class="text-muted">ขาดงาน (วัน)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-table me-2"></i>
                รายละเอียดการเช็คชื่อ เดือน <?= $months[$month] ?? '' ?> พ.ศ. <?= $year ?>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($records)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-2">ไม่พบข้อมูลการเช็คชื่อในเดือนนี้</p>
                </div>
            <?php else: ?>
                <div class="table-responsive text-nowrap history-table">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">#</th>
                                <th>วันที่</th>
                                <th>เวลาเข้า</th>
                                <th>เวลาออก</th>
                                <th>รูปเข้า</th>
                                <th>รูปออก</th>
                                <th class="text-center">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $thDaysShort = ['อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.'];
                            foreach ($records as $i => $r):
                                $d = new DateTime($r['att_date']);
                                $dayName = $thDaysShort[$d->format('w')];
                                $dateDisplay = $d->format('d/m/') . ($d->format('Y') + 543);
                            ?>
                            <tr>
                                <td class="text-center"><?= $i + 1 ?></td>
                                <td>
                                    <div class="fw-bold"><?= $dateDisplay ?></div>
                                    <small class="text-muted">วัน<?= $dayName ?></small>
                                </td>
                                <td>
                                    <?php if ($r['check_in']): ?>
                                        <span class="fw-bold text-primary"><?= date('H:i', strtotime($r['check_in'])) ?></span>
                                        <?php if ($r['check_in_lat']): ?>
                                            <br><small class="text-muted">
                                                <i class="bi bi-geo-alt"></i>
                                                <?= number_format($r['check_in_lat'], 4) ?>, <?= number_format($r['check_in_lng'], 4) ?>
                                            </small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($r['check_out']): ?>
                                        <span class="fw-bold text-success"><?= date('H:i', strtotime($r['check_out'])) ?></span>
                                        <?php if ($r['check_out_lat']): ?>
                                            <br><small class="text-muted">
                                                <i class="bi bi-geo-alt"></i>
                                                <?= number_format($r['check_out_lat'], 4) ?>, <?= number_format($r['check_out_lng'], 4) ?>
                                            </small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($r['check_in_photo']): ?>
                                        <img src="<?= esc($r['check_in_photo']) ?>"
                                             class="selfie-thumb"
                                             onclick="showSelfieModal('<?= esc($r['check_in_photo']) ?>', 'เช็คชื่อเข้า - <?= $dateDisplay ?>')"
                                             alt="Selfie Check-in">
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($r['check_out_photo']): ?>
                                        <img src="<?= esc($r['check_out_photo']) ?>"
                                             class="selfie-thumb"
                                             onclick="showSelfieModal('<?= esc($r['check_out_photo']) ?>', 'เช็คชื่อออก - <?= $dateDisplay ?>')"
                                             alt="Selfie Check-out">
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $statusClass = [
                                        'ปกติ'  => 'bg-label-success',
                                        'มาสาย' => 'bg-label-warning',
                                        'ขาด'   => 'bg-label-danger',
                                    ];
                                    $class = $statusClass[$r['status']] ?? 'bg-label-secondary';
                                    ?>
                                    <span class="badge <?= $class ?>"><?= $r['status'] ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        $('.datatable').DataTable({
            order: [[1, 'desc']],
            pageLength: 25,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json',
            },
            columnDefs: [
                { orderable: false, targets: [4, 5] }, // รูปไม่ต้อง sort
            ],
        });
    });

    // แสดงรูป selfie แบบ modal
    function showSelfieModal(src, title) {
        Swal.fire({
            title: title,
            imageUrl: src,
            imageWidth: 400,
            imageHeight: 300,
            imageAlt: 'Selfie',
            confirmButtonText: 'ปิด',
        });
    }
</script>
<?= $this->endSection() ?>
