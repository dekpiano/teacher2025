<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'หน้าแรกโฮมรูม') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><strong>สถิติการมาเรียนล่าสุด</strong> (<?= isset($ChkHomeRoom['chk_home_date']) ? thai_date(strtotime($ChkHomeRoom['chk_home_date'])) : 'ยังไม่มีข้อมูล'; ?>)</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="<?= site_url('homeroom/add') ?>" class="btn btn-primary btn-lg btn-block">เช็คชื่อเข้าเรียน</a>
                        </div>
                        <div class="col-md-4">
                            <a href="#" class="btn btn-info btn-lg btn-block">สถิติรายบุคคล</a>
                        </div>
                        <div class="col-md-4">
                            <a href="#" class="btn btn-warning btn-lg btn-block">สถิติสรุป</a>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <!-- Card -->
                        <div class="col-md-2">
                            <div class="card">
                                <div class="card-body text-center">
                                    <p>มา</p>
                                    <hr>
                                    <p>ชาย: <?= esc($BoyMa ?? 0) ?></p>
                                    <p>หญิง: <?= esc($GirlMa ?? 0) ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Card -->
                        <div class="col-md-2">
                            <div class="card">
                                <div class="card-body text-center">
                                    <p>ขาด</p>
                                    <hr>
                                    <p>ชาย: <?= esc($BoyKhad ?? 0) ?></p>
                                    <p>หญิง: <?= esc($GirlKhad ?? 0) ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Add other stat cards similarly -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">ประวัติการเช็คชื่อ</div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>วันที่</th>
                                <th>มา</th>
                                <th>ขาด</th>
                                <th>ลา</th>
                                <th>สาย</th>
                                <th>กิจกรรม</th>
                                <th>หนี</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($ChkHomeRoom) && !empty($ChkHomeRoom)): ?>
                                <tr>
                                    <td><?= esc($ChkHomeRoom['chk_home_date']) ?></td>
                                    <td><?= count(explode('|', $ChkHomeRoom['chk_home_ma'])) ?></td>
                                    <td><?= count(explode('|', $ChkHomeRoom['chk_home_khad'])) ?></td>
                                    <td><?= count(explode('|', $ChkHomeRoom['chk_home_la'])) ?></td>
                                    <td><?= count(explode('|', $ChkHomeRoom['chk_home_sahy'])) ?></td>
                                    <td><?= count(explode('|', $ChkHomeRoom['chk_home_kid'])) ?></td>
                                    <td><?= count(explode('|', $ChkHomeRoom['chk_home_hnee'])) ?></td>
                                    <td><a href="#" class="btn btn-info">ดูรายละเอียด</a></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">ไม่พบข้อมูล</td>
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
