<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ตั้งค่าการส่งงานวิจัย') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">ตั้งค่าการส่งงานวิจัยในชั้นเรียน</h5>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('research/setting-update') ?>" method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seres_setup_startdate" class="form-label">วันที่เริ่มต้น</label>
                                    <input type="datetime-local" class="form-control" id="seres_setup_startdate" name="seres_setup_startdate" value="<?= date('Y-m-d\TH:i', strtotime($setup->seres_setup_startdate)) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="seres_setup_enddate" class="form-label">วันที่สิ้นสุด</label>
                                    <input type="datetime-local" class="form-control" id="seres_setup_enddate" name="seres_setup_enddate" value="<?= date('Y-m-d\TH:i', strtotime($setup->seres_setup_enddate)) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="seres_setup_year" class="form-label">ปีการศึกษา</label>
                                    <input type="number" class="form-control" id="seres_setup_year" name="seres_setup_year" value="<?= esc($setup->seres_setup_year) ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="seres_setup_term" class="form-label">ภาคเรียน</label>
                                    <select class="form-select" id="seres_setup_term" name="seres_setup_term">
                                        <option value="1" <?= ($setup->seres_setup_term == '1') ? 'selected' : '' ?>>1</option>
                                        <option value="2" <?= ($setup->seres_setup_term == '2') ? 'selected' : '' ?>>2</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="seres_setup_status" class="form-label">สถานะ</label>
                                    <select class="form-select" id="seres_setup_status" name="seres_setup_status">
                                        <option value="on" <?= ($setup->seres_setup_status == 'on') ? 'selected' : '' ?>>เปิด</option>
                                        <option value="off" <?= ($setup->seres_setup_status == 'off') ? 'selected' : '' ?>>ปิด</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">บันทึกการตั้งค่า</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
