<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'แดชบอร์ดโฮมรูม') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart-fill text-primary me-2"></i>แดชบอร์ดสรุปการเช็คชื่อโฮมรูม ระดับชั้น ม.<?= esc($teacher->Reg_Class ?? '') ?>
                        <span class="badge bg-label-primary ms-2"><?= thai_date($current_date, 'short') ?></span>
                    </h5>
                    <form id="date-form" class="d-flex align-items-center gap-2">
                        <label for="homeroom_datepicker" class="text-nowrap mb-0 small fw-semibold">เลือกวันที่:</label>
                        <input type="text" id="homeroom_datepicker" name="date" class="form-control form-control-sm flatpickr-date" value="<?= date('Y-m-d', strtotime($current_date)) ?>" style="width: 170px;">
                        <button type="submit" class="btn btn-sm btn-primary text-nowrap"><i class="bi bi-search me-1"></i>ค้นหา</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="barChartExample"></canvas>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ห้อง</th>
                                    <th>มา</th>
                                    <th>ขาด</th>
                                    <th>ลา</th>
                                    <th>สาย</th>
                                    <th>กิจกรรม</th>
                                    <th>หนี</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($showHR as $record): ?>
                                <tr>
                                    <td><?= esc($record->chk_home_room) ?></td>
                                    <td><?= count(explode('|', $record->chk_home_ma)) ?></td>
                                    <td><?= count(explode('|', $record->chk_home_khad)) ?></td>
                                    <td><?= count(explode('|', $record->chk_home_la)) ?></td>
                                    <td><?= count(explode('|', $record->chk_home_sahy)) ?></td>
                                    <td><?= count(explode('|', $record->chk_home_kid)) ?></td>
                                    <td><?= count(explode('|', $record->chk_home_hnee)) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>แบบประเมิน</h3>
                    <p>อ่าน คิดวิเคราะห์ และเขียน</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book-reader"></i>
                </div>
                <a href="<?= base_url('teacher/reading_assessment') ?>" class="small-box-footer">
                    เข้าสู่ระบบประเมิน <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $('#date-form').on('submit', function(e) {
        e.preventDefault();
        var rawVal = $('#homeroom_datepicker').val();
        if (rawVal) {
            var parts = rawVal.split('-');
            var formatted = (parts.length === 3) ? parts[2] + '-' + parts[1] + '-' + parts[0] : rawVal;
            window.location.href = "<?= site_url('homeroom/dashboard/') ?>" + formatted;
        }
    });

    // Bar Chart
    var barChartData = {
        labels: [],
        datasets: [
            { label: 'มา', backgroundColor: '#28a745', data: [] },
            { label: 'ขาด', backgroundColor: '#dc3545', data: [] },
            { label: 'ลา', backgroundColor: '#ffc107', data: [] },
            { label: 'สาย', backgroundColor: '#17a2b8', data: [] },
            { label: 'กิจกรรม', backgroundColor: '#6c757d', data: [] },
            { label: 'หนี', backgroundColor: '#343a40', data: [] },
        ]
    };

    <?php foreach($showHR as $record): ?>
        barChartData.labels.push("<?= esc($record->chk_home_room) ?>");
        barChartData.datasets[0].data.push(<?= count(explode('|', $record->chk_home_ma)) ?>);
        barChartData.datasets[1].data.push(<?= count(explode('|', $record->chk_home_khad)) ?>);
        barChartData.datasets[2].data.push(<?= count(explode('|', $record->chk_home_la)) ?>);
        barChartData.datasets[3].data.push(<?= count(explode('|', $record->chk_home_sahy)) ?>);
        barChartData.datasets[4].data.push(<?= count(explode('|', $record->chk_home_kid)) ?>);
        barChartData.datasets[5].data.push(<?= count(explode('|', $record->chk_home_hnee)) ?>);
    <?php endforeach; ?>

    var ctx = document.getElementById('barChartExample').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
            responsive: true,
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true }
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
