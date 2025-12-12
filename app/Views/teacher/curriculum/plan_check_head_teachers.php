<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'เลือกครูเพื่อตรวจแผน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>



        <div class="container-fluid">
            <div class="row">
                <?php if (empty($teachers)): ?>
                    <div class="col-12">
                        <div class="alert alert-warning" role="alert">
                            ไม่พบข้อมูลครูในกลุ่มสาระของท่าน
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($teachers as $teacher): ?>
                        <div class="col-md-4 col-sm-6 col-12">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <div class="avatar avatar-xl mb-3">
                                        <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= esc($teacher->pers_img) ?>" 
                                             alt="<?= esc($teacher->pers_firstname) ?>" 
                                             class="rounded-circle" 
                                             style="object-fit: cover;"
                                             onerror="this.onerror=null;this.src='https://placehold.co/100x100/EFEFEF/AAAAAA&text=No+Image';">
                                    </div>
                                    <h5 class="card-title"><?= esc($teacher->pers_prefix . $teacher->pers_firstname . ' ' . $teacher->pers_lastname) ?></h5>
                                    <a href="<?= site_url('curriculum/check-plan-head-detail/' . $teacher->pers_id) ?>" class="btn btn-primary btn-sm">ดูแผนการสอน</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

<?= $this->endSection() ?>
