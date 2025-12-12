<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ประเมินคุณลักษณะอันพึงประสงค์') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ประเมินคุณลักษณะอันพึงประสงค์ (ปีการศึกษา <?= $term ?>/<?= $academicYear ?>)</h3>
                </div>
                <div class="card-body">
                    <?php if (($assessmentStatus ?? 'off') !== 'on') : ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>ปิดระบบ</h4>
                            <p>ระบบประเมินคุณลักษณะอันพึงประสงค์ ถูกปิดการใช้งานชั่วคราว</p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($teacherClasses)) : ?>
                        <div class="row">
                            <?php foreach ($teacherClasses as $class) : ?>
                                <?php
                                    $status = $class['status'] ?? ['total' => 0, 'assessed' => 0];
                                    $statusTextShort = 'ยังไม่ประเมิน';
                                    $statusTextLong = 'ยังไม่มีการประเมินนักเรียนในห้องนี้';
                                    $badgeColor = 'bg-label-secondary';
                                    $isDisabled = (($assessmentStatus ?? 'off') !== 'on');

                                    if ($status['assessed'] > 0) {
                                        if ($status['assessed'] >= $status['total']) {
                                            $statusTextShort = 'ครบแล้ว';
                                            $statusTextLong = 'ประเมินนักเรียนครบทุกคนแล้ว (' . $status['total'] . ' คน)';
                                            $badgeColor = 'bg-label-success';
                                        } else {
                                            $statusTextShort = 'บางส่วน';
                                            $statusTextLong = 'ประเมินแล้ว ' . $status['assessed'] . ' จาก ' . $status['total'] . ' คน';
                                            $badgeColor = 'bg-label-warning';
                                        }
                                    }
                                ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">ห้อง <?= esc($class['Reg_Class']) ?></h5>
                                            <span class="badge <?= $badgeColor ?>"><?= $statusTextShort ?></span>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text"><?= $statusTextLong ?></p>
                                        </div>
                                        <div class="card-footer bg-transparent border-top-0">
                                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                                                <a href="<?= base_url('teacher/desirable_assessment/assess/' . $class['Reg_Class']) ?>" class="btn btn-primary <?= $isDisabled ? 'disabled' : '' ?>" <?= $isDisabled ? 'aria-disabled="true"' : '' ?>>
                                                    <i class="bi bi-pencil-square me-1"></i> ประเมิน
                                                </a>
                                                <a href="<?= base_url('teacher/desirable_assessment/print_report/' . $class['Reg_Class']) ?>" target="_blank" class="btn btn-info <?= $isDisabled ? 'disabled' : '' ?>" <?= $isDisabled ? 'aria-disabled="true"' : '' ?>>
                                                    <i class="bi bi-printer me-1"></i> พิมพ์
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="alert alert-warning" role="alert">
                            ไม่พบชั้นเรียนที่ท่านเป็นที่ปรึกษาในปีการศึกษาปัจจุบัน
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
