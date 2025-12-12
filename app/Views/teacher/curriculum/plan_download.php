<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ดาวน์โหลดแผน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>




        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ชื่อ - นามสกุล</th>
                                    <th class="text-center">คำสั่ง</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <?php if (!empty($teacher)): ?>
                                <?php foreach ($teacher as $v_teacher) : ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= esc($v_teacher->pers_img) ?>" alt="Avatar" class="rounded-circle">
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium"><?= esc($v_teacher->pers_prefix . $v_teacher->pers_firstname . ' ' . $v_teacher->pers_lastname) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('curriculum/download-plan-zip/' . $v_teacher->pers_id) ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-download me-1"></i> ดาวน์โหลด
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center">ไม่พบข้อมูลครูผู้สอน</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
            </div>


<?= $this->endSection() ?>
