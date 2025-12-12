<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ชุมนุมที่ปรึกษา') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <?= esc($title ?? 'ชุมนุมที่ปรึกษา') ?>
                        <?php if (isset($currentAcademicYear) && isset($currentTerm)): ?>
                            <span class="text-muted fs-5 ms-2">(ปีการศึกษา <?= esc($currentAcademicYear) ?> ภาคเรียนที่ <?= esc($currentTerm) ?>)</span>
                        <?php endif; ?>
                    </h3>
                    <div class="card-tools d-flex gap-2">
                        <?php if (isset($hasClubForCurrentYear) && $hasClubForCurrentYear): ?>
                            <span class="text-muted fst-italic">คุณได้สร้างชุมนุมสำหรับภาคเรียนนี้แล้ว</span>
                        <?php else: ?>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createClubModal">
                                <i class="bi bi-plus"></i> สร้างชุมนุมใหม่
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($clubs) && is_array($clubs)): ?>
                        <div class="row g-4">
                            <?php foreach ($clubs as $club): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 ">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="card-title mb-0"><?= esc($club->club_name) ?></h5>
                                                <?php
                                                $currentDate = date('Y-m-d');
                                                $statusText = 'ปิดรับสมัคร'; // Default
                                                $color = 'text-bg-danger'; // Default

                                                if ($registrationStartDate && $registrationEndDate) {
                                                    if ($currentDate < $registrationStartDate) {
                                                        $statusText = 'ยังไม่เปิดรับสมัคร';
                                                        $color = 'text-bg-warning';
                                                    } elseif ($currentDate >= $registrationStartDate && $currentDate <= $registrationEndDate) {
                                                        $statusText = 'เปิดรับสมัคร';
                                                        $color = 'text-bg-success';
                                                    }
                                                }
                                                ?>
                                                <span class="badge <?= $color ?> ms-2"><?= $statusText ?></span>
                                            </div>
                                            <p class="card-text text-muted small">
                                                ปีการศึกษา <?= esc($club->club_year) ?> / <?= esc($club->club_trem) ?>
                                            </p>
                                            <ul class="list-group list-group-flush mb-3">
                                                <li class="list-group-item d-flex justify-content-between align-items-center p-0 border-0">
                                                    <small class="text-muted">ระดับชั้น:</small>
                                                    <small><?= esc($club->club_level) ?></small>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center p-0 border-0">
                                                    <small class="text-muted">จำนวนรับสูงสุด:</small>
                                                    <small><?= esc($club->club_max_participants) ?> คน</small>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center p-0 border-0">
                                                    <small class="text-muted">สมัครแล้ว:</small>
                                                    <small><?= esc($club->member_count) ?> คน</small>
                                                </li>
                                            </ul>
                                            <div class="mt-auto">
                                                <a href="<?= site_url('club/manage/' . $club->club_id) ?>" class="btn btn-primary w-100">
                                                    <i class="bi bi-gear-fill me-1"></i> จัดการชุมนุม
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            ไม่พบชุมนุมที่คุณเป็นที่ปรึกษา
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<!-- Create Club Modal -->
<div class="modal fade" id="createClubModal" tabindex="-1" role="dialog" aria-labelledby="createClubModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url('club/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="createClubModalLabel">สร้างชุมนุมใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="club_name" name="club_name" placeholder="ระบุชื่อชุมนุม" required>
                        <label for="club_name">ชื่อชุมนุม</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="club_description" name="club_description" placeholder="ระบุคำอธิบายโดยย่อ" style="height: 100px"></textarea>
                        <label for="club_description">คำอธิบายชุมนุม</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="club_max_participants" name="club_max_participants" placeholder="จำนวนรับสูงสุด (คน)" value="20" required>
                        <label for="club_max_participants">จำนวนรับสูงสุด (คน)</label>
                    </div>
                    <div class="form-floating">
                        <select class="form-select" id="club_level" name="club_level" required>
                            <option value="" selected disabled>-- เลือกระดับชั้น --</option>
                            <option value="ม.ต้น">ม.ต้น</option>
                            <option value="ม.ปลาย">ม.ปลาย</option>
                            <option value="ม.ต้น หรือ ม.ปลาย">ม.ต้น หรือ ม.ปลาย</option>
                        </select>
                        <label for="club_level">ระดับชั้น</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
<!-- Help Modal -->
<div class="modal fade" id="clubHelpModal" tabindex="-1" aria-labelledby="clubHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clubHelpModalLabel"><i class="bi bi-question-circle-fill me-2"></i>คำแนะนำการใช้งานระบบชุมนุม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php include('help_modal_content.php'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // When the help modal is shown, activate the correct tab for this page
    $('#clubHelpModal').on('show.bs.modal', function () {
        var tab = new bootstrap.Tab(document.querySelector('#pills-index-tab'));
        tab.show();
    });
</script>
<?= $this->endSection() ?>
