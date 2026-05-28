<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
รายงานสรุปรายเดือน
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .stat-card {
        background: #fff;
        border-radius: 1.25rem;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        text-align: center;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
    }
    .stat-label {
        font-size: 0.9rem;
        color: #677788;
        margin-top: 0.25rem;
    }
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin: 0 auto 0.75rem;
    }
    .chart-card {
        background: #fff;
        border-radius: 1.25rem;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1"><i class="bi bi-graph-up me-2 text-primary"></i>รายงานสรุปรายเดือน</h4>
            <p class="text-muted mb-0">สถิติการเช็คชื่อเข้า-ออกงาน</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="get" action="<?= base_url('attendance/report') ?>" class="row g-3 align-items-end">
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
                                <i class="bi bi-search me-1"></i> ดูรายงาน
                            </button>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('attendance/history?month=' . $month . '&year=' . $year) ?>" class="btn btn-outline-info w-100">
                                <i class="bi bi-table me-1"></i> ดูรายละเอียด
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Title -->
    <div class="alert alert-primary d-flex align-items-center" role="alert">
        <i class="bi bi-person-badge me-2 fs-4"></i>
        <div>
            <strong><?= esc($fullname) ?></strong> —
            รายงานการเช็คชื่อ เดือน <?= $months[$month] ?? '' ?> พ.ศ. <?= $year ?>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon bg-label-primary text-primary"><i class="bi bi-calendar-check"></i></div>
                <div class="stat-number text-primary"><?= array_sum($summary) ?></div>
                <div class="stat-label">จำนวนวันที่มีประวัติ</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon bg-label-success text-success"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-number text-success"><?= $summary['ปกติ'] ?></div>
                <div class="stat-label">มาปกติ (วัน)</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon bg-label-warning text-warning"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="stat-number text-warning"><?= $summary['มาสาย'] ?></div>
                <div class="stat-label">มาสาย (วัน)</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon bg-label-danger text-danger"><i class="bi bi-x-circle-fill"></i></div>
                <div class="stat-number text-danger"><?= $summary['ขาด'] ?></div>
                <div class="stat-label">ขาดงาน (วัน)</div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="chart-card mb-4">
        <h5 class="mb-3"><i class="bi bi-pie-chart me-2 text-primary"></i>กราฟสรุป</h5>
        <div id="chart-summary" style="height: 350px;"></div>
    </div>

    <!-- Attendance Record Table -->
    <div class="chart-card">
        <h5 class="mb-3"><i class="bi bi-list-check me-2 text-primary"></i>รายละเอียด</h5>
        <?php if (empty($records)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-2">ไม่พบข้อมูล</p>
            </div>
        <?php else: ?>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th class="text-center" width="50">#</th>
                            <th>วันที่</th>
                            <th>เวลาเข้า</th>
                            <th>เวลาออก</th>
                            <th>ระยะเวลาทำงาน</th>
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

                            // คำนวณระยะเวลาทำงาน
                            $workHours = '-';
                            if ($r['check_in'] && $r['check_out']) {
                                $t1 = strtotime($r['check_in']);
                                $t2 = strtotime($r['check_out']);
                                $diff = $t2 - $t1;
                                $h = floor($diff / 3600);
                                $m = floor(($diff % 3600) / 60);
                                $workHours = "{$h} ชม. {$m} น.";
                            }
                        ?>
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td>
                                <div class="fw-bold"><?= $dateDisplay ?></div>
                                <small class="text-muted">วัน<?= $dayName ?></small>
                            </td>
                            <td>
                                <?php if ($r['check_in']): ?>
                                    <span class="badge bg-label-primary"><?= date('H:i', strtotime($r['check_in'])) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($r['check_out']): ?>
                                    <span class="badge bg-label-success"><?= date('H:i', strtotime($r['check_out'])) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($r['check_in'] && $r['check_out']): ?>
                                    <span class="fw-bold text-dark"><?= $workHours ?></span>
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
        });
    });

    // Pie Chart with ApexCharts
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            series: [<?= $summary['ปกติ'] ?>, <?= $summary['มาสาย'] ?>, <?= $summary['ขาด'] ?>],
            chart: {
                type: 'donut',
                height: 350,
            },
            labels: ['มาปกติ', 'มาสาย', 'ขาดงาน'],
            colors: ['#28a745', '#ffc107', '#dc3545'],
            legend: {
                position: 'bottom',
                fontFamily: 'K2D, sans-serif',
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return Math.round(val) + '%';
                },
                style: {
                    fontFamily: 'K2D, sans-serif',
                },
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'รวม',
                                fontFamily: 'K2D, sans-serif',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + ' วัน';
                                },
                            },
                            value: {
                                fontFamily: 'K2D, sans-serif',
                            },
                        },
                    },
                },
            },
        };

        var chart = new ApexCharts(document.querySelector('#chart-summary'), options);
        chart.render();
    });
</script>
<?= $this->endSection() ?>
