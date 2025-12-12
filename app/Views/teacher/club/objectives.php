<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'จุดประสงค์กิจกรรม') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><?= esc($title ?? 'จุดประสงค์กิจกรรม') ?></h3>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#clubHelpModal">
                            <i class="bi bi-question-circle"></i> คำแนะนำ
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#manageObjectivesModal">
                            <i class="bi bi-plus-circle"></i> จัดการจุดประสงค์
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($objectives)): ?>
                    <form action="<?= site_url('club/saveObjectives/' . $club->club_id) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="align-middle">เลขที่</th>
                                        <th rowspan="2" class="align-middle">ชื่อ - นามสกุล</th>
                                        <th colspan="<?= count($objectives) ?>" class="text-center">จุดประสงค์ที่</th>
                                        <th rowspan="2" class="align-middle">รวมจุดประสงค์ที่ผ่าน</th>
                                        <th rowspan="2" class="align-middle">ผลจุดประสงค์<br>(ผ/มผ)</th>
                                        <th rowspan="2" class="align-middle">ระดับผลกิจกรรม <br>(ผ/มผ)</th>
                                        <th rowspan="2" class="align-middle">การแก้ไข</th>
                                    </tr>
                                    <tr>
                                        <?php foreach ($objectives as $objective): ?>
                                        <th class="text-center"><?= esc($objective->objective_order) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($members)): ?>
                                    <?php foreach ($members as $member): ?>
                                    <?php $total_passed = 0; ?>
                                    <tr data-student-id="<?= esc($member->StudentID) ?>">
                                        <td><?= esc($member->StudentNumber) ?></td>
                                        <td><?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?>
                                        </td>
                                        <?php foreach ($objectives as $objective): ?>
                                        <?php
                                                                                                                $isChecked = isset($progress[$member->StudentID][$objective->objective_id]) && $progress[$member->StudentID][$objective->objective_id]->status == 1;
                                                                                                                if ($isChecked) {
                                                                                                                    $total_passed++;
                                                                                                                }
                                                                                                            ?>
                                        <td class="text-center">
                                            <input class="form-check-input objective-checkbox" type="checkbox"
                                                name="progress[<?= $member->StudentID ?>][<?= $objective->objective_id ?>]"
                                                value="1" <?= $isChecked ? 'checked' : '' ?>>
                                        </td>
                                        <?php endforeach; ?>
                                        <td class="text-center total-passed-cell"><?= $total_passed ?></td>
                                        <td class="text-center objective-result-cell">
                                            <!-- Placeholder for objective_result -->
                                        </td>
                                        <td class="text-center activity-result-cell">
                                            <!-- Placeholder for result_level -->
                                        </td>
                                        <td>
                                            <!-- Placeholder for activity_notes -->
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="<?= 7 + count($objectives) ?>" class="text-center">
                                            ไม่พบสมาชิกในชุมนุมนี้</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                            <a href="<?= site_url('club/manage/' . $club->club_id) ?>"
                                class="btn btn-secondary">ย้อนกลับ</a>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-info">
                        ยังไม่มีจุดประสงค์สำหรับชุมนุมนี้ กรุณากำหนดจุดประสงค์ก่อน
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<!-- Manage Objectives Modal -->
<div class="modal fade" id="manageObjectivesModal" tabindex="-1" aria-labelledby="manageObjectivesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= site_url('club/saveObjectiveDefinition/' . $club->club_id) ?>" method="post"
                id="objectiveDefinitionForm">
                <?= csrf_field() ?>
                <input type="hidden" name="objective_id" id="objective_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageObjectivesModalLabel">จัดการจุดประสงค์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="objective_name" class="form-label">ชื่อจุดประสงค์ <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="objective_name" name="objective_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="objective_description" class="form-label">คำอธิบาย</label>
                        <textarea class="form-control" id="objective_description" name="objective_description"
                            rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="objective_order" class="form-label">ลำดับ <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="objective_order" name="objective_order" required>
                    </div>
                    <button type="submit" class="btn btn-success mb-3">บันทึกจุดประสงค์</button>

                    <hr>

                    <h5>จุดประสงค์ที่มีอยู่</h5>
                    <?php if (!empty($objectives)): ?>
                    <div class="table-responsive">
<table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ลำดับ</th>
                                <th>ชื่อจุดประสงค์</th>
                                <th>คำอธิบาย</th>
                                <th style="width: 150px;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($objectives as $objective): ?>
                            <tr>
                                <td><?= esc($objective->objective_order) ?></td>
                                <td><?= esc($objective->objective_name) ?></td>
                                <td><?= esc($objective->objective_description) ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm edit-objective-btn"
                                        data-objective-id="<?= esc($objective->objective_id) ?>"
                                        data-objective-name="<?= esc($objective->objective_name) ?>"
                                        data-objective-description="<?= esc($objective->objective_description) ?>"
                                        data-objective-order="<?= esc($objective->objective_order) ?>">
                                        <i class="bi bi-pencil"></i> แก้ไข
                                    </button>
                                    <a href="<?= site_url('club/deleteObjective/' . $club->club_id . '/' . $objective->objective_id) ?>"
                                        class="btn btn-danger btn-sm delete-objective-btn">
                                        <i class="bi bi-trash"></i> ลบ
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">ยังไม่มีจุดประสงค์สำหรับชุมนุมนี้</div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </form>
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
document.addEventListener('DOMContentLoaded', function() {
    // When the help modal is shown, activate the correct tab for this page
    const helpModal = document.getElementById('clubHelpModal');
    if (helpModal) {
        helpModal.addEventListener('show.bs.modal', function () {
            var tab = new bootstrap.Tab(document.querySelector('#pills-objectives-tab'));
            tab.show();
        });
    }

    const totalObjectives = <?= !empty($objectives) ? count($objectives) : 0 ?>;

    function updateRowSummary(row) {
        if (!row) return;

        const checkboxes = row.querySelectorAll('.objective-checkbox');
        const checkedCount = row.querySelectorAll('.objective-checkbox:checked').length;

        const totalPassedCell = row.querySelector('.total-passed-cell');
        const objectiveResultCell = row.querySelector('.objective-result-cell');
        const activityResultCell = row.querySelector('.activity-result-cell');

        if (totalPassedCell) {
            totalPassedCell.textContent = checkedCount;
        }

        const isPass = (totalObjectives > 0 && checkedCount === totalObjectives);
        const resultText = isPass ? 'ผ' : 'มผ';

        if (objectiveResultCell) {
            objectiveResultCell.textContent = resultText;
        }
        if (activityResultCell) {
            activityResultCell.textContent = resultText;
        }
    }

    // Add event listener to all checkboxes
    document.querySelectorAll('.objective-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr');
            updateRowSummary(row);
        });
    });

    // Initial calculation on page load
    document.querySelectorAll('tbody tr[data-student-id]').forEach(row => {
        updateRowSummary(row);
    });

    // --- Existing Modal Scripts ---
    // Handle "Manage Objectives" button click to open modal
    const manageButton = document.querySelector('button[data-bs-target="#manageObjectivesModal"]');
    if (manageButton) {
        manageButton.addEventListener('click', function() {
            document.getElementById('objectiveDefinitionForm').reset();
            document.getElementById('objective_id').value = '';
            document.getElementById('manageObjectivesModalLabel').textContent = 'จัดการจุดประสงค์';
        });
    }

    // Handle "Edit" button click inside the modal
    document.querySelectorAll('.edit-objective-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('objective_id').value = this.dataset.objectiveId;
            document.getElementById('objective_name').value = this.dataset.objectiveName;
            document.getElementById('objective_description').value = this.dataset
                .objectiveDescription;
            document.getElementById('objective_order').value = this.dataset.objectiveOrder;
            document.getElementById('manageObjectivesModalLabel').textContent =
                'แก้ไขจุดประสงค์';
        });
    });

    // Handle "Delete" button click inside the modal
    document.querySelectorAll('.delete-objective-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบจุดประสงค์นี้?')) {
                window.location.href = this.href;
            }
        });
    });
});
</script>
<?= $this->endSection() ?>