<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? '') ?><?= esc($lean[0]->lear_namethai ?? '') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>




        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">กลุ่มสาระการเรียนรู้ <?= esc($lean[0]->lear_namethai ?? '') ?></h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if(isset($techer) && !empty($techer)): ?>
                        <?php foreach ($techer as $v_techer): ?>
                            <a href="<?= site_url('curriculum/check-plan-lear-teacher/' . esc($lean[0]->lear_id) . '/' . esc($v_techer->pers_id) . '/' . (isset($OnOff[0]) ? esc($OnOff[0]->seplanset_year) : '') . '/' . (isset($OnOff[0]) ? esc($OnOff[0]->seplanset_term) : '')) ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                                <div class="avatar avatar-md me-3">
                                    <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= esc($v_techer->pers_img) ?>" alt="..." class="rounded-circle">
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1"><?= esc($v_techer->pers_prefix . $v_techer->pers_firstname . ' ' . $v_techer->pers_lastname) ?></h6>
                                    <small class="text-muted"><?= esc($lean[0]->lear_namethai ?? '') ?></small>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item">
                            <div class="text-center text-muted">ไม่พบครูผู้สอนในกลุ่มสาระนี้</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>


<?= $this->endSection() ?>