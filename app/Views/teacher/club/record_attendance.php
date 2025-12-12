<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'บันทึกการเข้าเรียน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    select.form-select option {
        color: black;
    }
</style>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><?= esc($title ?? 'บันทึกการเข้าเรียนชุมนุม') ?></h3>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#clubHelpModal">
                            <i class="bi bi-question-circle"></i> คำแนะนำ
                        </button>
                        <?php if (isset($hasAttendanceRecord)): ?>
                            <?php if (!$hasAttendanceRecord): ?>
                                <span class="badge bg-warning">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ยังไม่ได้บันทึกการเข้าเรียน
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle-fill me-1"></i> บันทึกการเข้าเรียนแล้ว
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('club/saveAttendance/' . $club->club_id . '/' . $schedule->tcs_schedule_id) ?>" method="post">
                        <?= csrf_field() ?>
                        <?php if (!empty($members) && is_array($members)): ?>
                            <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>รหัสนักเรียน</th>
                                        <th>ชื่อ-นามสกุล</th>
                                        <th>ชั้น</th>
                                        <th>เลขที่</th>
                                        <th style="width: 150px;">สถานะการเข้าเรียน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $i = 1;
                                        $statusColors = [
                                            'มา' => 'bg-success text-white',
                                            'ขาด' => 'bg-danger text-white',
                                            'ลาป่วย' => 'bg-warning',
                                            'ลากิจ' => 'bg-info',
                                            'กิจกรรม' => 'bg-secondary text-white',
                                        ];
                                    ?>
                                    <?php foreach ($members as $member): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= esc($member->StudentID) ?></td>
                                            <td><?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?></td>
                                            <td><?= esc($member->StudentClass) ?></td>
                                            <td><?= esc($member->StudentNumber) ?></td>
                                            <td>
                                                <?php
                                                    $currentStatus = $existingAttendance[$member->StudentID] ?? 'มา';
                                                    $colorClass = $statusColors[$currentStatus] ?? '';
                                                ?>
                                                <select id="attendance-select-<?= esc($member->StudentID) ?>" class="form-select <?= $colorClass ?>" name="attendance[<?= esc($member->StudentID) ?>]">
                                                    <option value="มา" <?= $currentStatus === 'มา' ? 'selected' : '' ?>>มา</option>
                                                    <option value="ขาด" <?= $currentStatus === 'ขาด' ? 'selected' : '' ?>>ขาด</option>
                                                    <option value="ลาป่วย" <?= $currentStatus === 'ลาป่วย' ? 'selected' : '' ?>>ลาป่วย</option>
                                                    <option value="ลากิจ" <?= $currentStatus === 'ลากิจ' ? 'selected' : '' ?>>ลากิจ</option>
                                                    <option value="กิจกรรม" <?= $currentStatus === 'กิจกรรม' ? 'selected' : '' ?>>กิจกรรม</option>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">บันทึกการเข้าเรียน</button>
                                <a href="<?= site_url('club/schedule/' . $club->club_id) ?>" class="btn btn-secondary">ย้อนกลับ</a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                ไม่พบสมาชิกในชุมนุมนี้ ไม่สามารถบันทึกการเข้าเรียนได้
                            </div>
                            <a href="<?= site_url('club/schedule/' . $club->club_id) ?>" class="btn btn-secondary">ย้อนกลับ</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
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
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // When the help modal is shown, activate the correct tab for this page
        const helpModal = document.getElementById('clubHelpModal');
        if (helpModal) {
            helpModal.addEventListener('show.bs.modal', function () {
                var tab = new bootstrap.Tab(document.querySelector('#pills-attendance-tab'));
                tab.show();
            });
        }

        const statusColors = {
            'มา': 'bg-success text-white',
            'ขาด': 'bg-danger text-white',
            'ลาป่วย': 'bg-warning',
            'ลากิจ': 'bg-info',
            'กิจกรรม': 'bg-secondary text-white',
        };

        const allColorClasses = Object.values(statusColors);

        document.querySelectorAll('.form-select[name^="attendance"]').forEach(selectElement => {
            selectElement.addEventListener('change', function () {
                // Remove all possible color classes
                this.classList.remove(...allColorClasses);
                
                // Add the new color class
                const selectedStatus = this.value;
                const newColorClass = statusColors[selectedStatus];
                if (newColorClass) {
                    // The classes in newColorClass might be multiple, e.g., 'bg-success text-white'
                    this.classList.add(...newColorClass.split(' '));
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
