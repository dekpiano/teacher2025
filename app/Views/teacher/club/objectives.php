<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'จุดประสงค์กิจกรรม') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .objectives-header {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        border-radius: 1.25rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .objectives-header::after {
        content: "";
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        pointer-events: none;
    }
    .table-card {
        border-radius: 1.25rem;
        border: none;
        overflow: hidden;
        margin-bottom: 5.5rem;
    }
    .student-avatar-sm {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6366f1;
        font-weight: 700;
        font-size: 0.8rem;
    }
    .objective-checkbox {
        width: 22px;
        height: 22px;
        cursor: pointer;
        border-radius: 6px !important;
        border: 2px solid #cbd5e1;
    }
    .objective-checkbox:checked {
        background-color: #6366f1;
        border-color: #6366f1;
    }
    .check-all-objective {
        width: 16px;
        height: 16px;
        cursor: pointer;
        border-radius: 4px !important;
        background-color: #fff;
    }
    .result-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 0.5rem;
        font-weight: 700;
        font-size: 0.85rem;
        min-width: 45px;
        display: inline-block;
    }
    .badge-pass { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-fail { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    
    .floating-save-bar {
        position: fixed;
        bottom: 1.5rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(15px);
        padding: 1rem 2.5rem;
        border-radius: 100px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        border: 1px solid rgba(255,255,255,0.5);
        display: flex;
        align-items: center;
        gap: 2rem;
        min-width: 500px;
    }
    
    .sticky-header-row th {
        position: sticky;
        top: 0;
        background: #f8fafc !important;
        z-index: 10;
        box-shadow: inset 0 -1px 0 #e2e8f0;
    }
    
    .objective-num-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
    }
    .objective-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        background: #6366f1;
        color: white;
        border-radius: 50%;
        font-size: 0.75rem;
        font-weight: 700;
    }

    /* Modal Style Override */
    #manageObjectivesModal .form-control {
        background-color: #ffffff !important;
        border: 1px solid #d9dee3 !important;
    }
    #manageObjectivesModal .form-control:focus {
        border-color: #696cff !important;
    }
</style>

<div class="container-fluid py-2">
    <!-- Help Modal -->
    <div class="modal fade" id="clubHelpModal" tabindex="-1" aria-labelledby="clubHelpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light py-3">
                    <h5 class="modal-title fw-bold" id="clubHelpModalLabel">
                        <i class="bi bi-question-circle-fill text-primary me-2"></i>คู่มือการใช้งานระบบชุมนุม
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <?php include('help_modal_content.php'); ?>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ปิดคู่มือ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Section -->
    <div class="objectives-header shadow-lg">
        <div class="row align-items-center text-start">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-light mb-2">
                        <li class="breadcrumb-item"><a href="<?= site_url('home') ?>" class="text-white opacity-75">หน้าหลัก</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('club') ?>" class="text-white opacity-75">ชุมนุม</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('club/manage/' . $club->club_id) ?>" class="text-white opacity-75">จัดการชุมนุม</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">ประเมินผลกิจกรรม</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold text-white mb-2">
                    <i class="bi bi-journal-check me-2"></i>ประเมินผลกิจกรรมชุมนุม
                </h1>
                <p class="lead mb-0 text-white opacity-75 small">
                    <i class="bi bi-tag-fill me-1"></i> <?= esc($club->club_name) ?>
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <button type="button" class="btn btn-white rounded-pill px-4 shadow-sm text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#manageObjectivesModal">
                        <i class="bi bi-gear-fill me-1"></i> ตั้งค่าจุดประสงค์
                    </button>
                    <a href="<?= site_url('club/manual') ?>" class="btn btn-white rounded-pill px-4 shadow-sm text-primary fw-bold">
                        <i class="bi bi-book-half me-1"></i> คู่มือการใช้งาน
                    </a>
                    <button type="button" class="btn btn-outline-light rounded-circle p-2 border-2" data-bs-toggle="modal" data-bs-target="#clubHelpModal" title="คู่มือฉบับย่อ">
                        <i class="bi bi-question-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <?php if (!empty($objectives)): ?>
        <form action="<?= site_url('club/saveObjectives/' . $club->club_id) ?>" method="post" id="objectivesForm">
            <?= csrf_field() ?>
            <div class="card table-card shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-people-fill text-primary me-2"></i>รายชื่อนักเรียนและการผ่านจุดประสงค์
                    </h5>
                    <div class="text-muted smallest d-flex align-items-center gap-3">
                        <span class="badge bg-light text-dark border">จำนวนจุดประสงค์ทั้งหมด: <?= count($objectives) ?> ข้อ</span>
                        <div class="vr opacity-10"></div>
                        <span class="smallest text-primary"><i class="bi bi-info-circle me-1"></i>กดปุ่ม <i class="bi bi-check-all"></i> เพื่อเลือกทั้งหมดในแต่ละข้อ</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-header-row">
                                <tr>
                                    <th rowspan="2" class="text-center py-3" style="width: 70px;">เลขที่</th>
                                    <th rowspan="2" class="py-3">ชื่อ - นามสกุล</th>
                                    <th colspan="<?= count($objectives) ?>" class="text-center py-2 bg-indigo bg-opacity-10">จุดประสงค์ที่ผ่าน</th>
                                    <th rowspan="2" class="text-center py-3" style="width: 100px;">รวมผ่าน</th>
                                    <th rowspan="2" class="text-center py-3" style="width: 100px;">ผลประเมิน</th>
                                </tr>
                                <tr>
                                    <?php foreach ($objectives as $objective): ?>
                                        <th class="text-center py-2" style="width: 60px;">
                                            <div class="objective-num-wrapper">
                                                <div class="objective-num" title="<?= esc($objective->objective_name) ?>"><?= esc($objective->objective_order) ?></div>
                                                <input class="form-check-input check-all-objective" type="checkbox" data-objective-id="<?= $objective->objective_id ?>" title="เลือกทั้งหมดข้อนี้">
                                            </div>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody class="text-start">
                                <?php if (!empty($members)): ?>
                                    <?php foreach ($members as $member): ?>
                                        <?php $total_passed = 0; ?>
                                        <tr data-student-id="<?= esc($member->StudentID) ?>">
                                            <td class="text-center">
                                                <div class="fw-bold text-muted"><?= esc($member->StudentNumber) ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark"><?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?></div>
                                                <div class="smallest text-muted">รหัส: <?= esc($member->StudentCode ?? $member->StudentID) ?></div>
                                            </td>
                                            <?php foreach ($objectives as $objective): ?>
                                                <?php
                                                    $isChecked = isset($progress[$member->StudentID][$objective->objective_id]) && $progress[$member->StudentID][$objective->objective_id]->status == 1;
                                                    if ($isChecked) $total_passed++;
                                                ?>
                                                <td class="text-center">
                                                    <input class="form-check-input objective-checkbox obj-check-<?= $objective->objective_id ?>" type="checkbox"
                                                        name="progress[<?= $member->StudentID ?>][<?= $objective->objective_id ?>]"
                                                        value="1" data-objective-id="<?= $objective->objective_id ?>" <?= $isChecked ? 'checked' : '' ?>>
                                                </td>
                                            <?php endforeach; ?>
                                            <td class="text-center fw-bold text-primary fs-5 total-passed-cell"><?= $total_passed ?></td>
                                            <td class="text-center">
                                                <span class="result-badge objective-result-cell"></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="<?= 4 + count($objectives) ?>" class="text-center py-5">
                                            <i class="bi bi-people text-muted opacity-25" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-2">ไม่พบสมาชิกในชุมนุมนี้</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Floating Save Bar -->
            <div class="floating-save-bar">
                <div class="d-flex align-items-center">
                    <a href="<?= site_url('club/manage/' . $club->club_id) ?>" class="text-muted text-decoration-none smallest fw-bold me-4">
                        <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
                    </a>
                    <div class="vr me-4 opacity-10"></div>
                    <div class="d-flex flex-column">
                        <span class="smallest fw-bold text-dark">บันทึกผลการประเมิน</span>
                        <span class="smallest text-muted">ปีการศึกษา <?= esc($club->club_year) ?> ภาคเรียนที่ <?= esc($club->club_trem) ?></span>
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <div class="form-check form-switch smallest d-none d-md-block">
                        <input class="form-check-input" type="checkbox" id="checkAllGlobal">
                        <label class="form-check-label fw-bold text-dark" for="checkAllGlobal">ผ่านทั้งหมดทุกข้อ</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg fw-bold">
                        <i class="bi bi-cloud-upload me-2"></i> บันทึกข้อมูลทั้งหมด
                    </button>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="card shadow-sm rounded-4 border-0 p-5 text-center">
            <i class="bi bi-journal-x text-muted opacity-25" style="font-size: 5rem;"></i>
            <h4 class="mt-4 fw-bold text-dark">ยังไม่มีการกำหนดจุดประสงค์</h4>
            <p class="text-muted">คุณครูจำเป็นต้องกำหนดจุดประสงค์ของกิจกรรมชุมนุมก่อน จึงจะสามารถประเมินผลนักเรียนได้ครับ</p>
            <div class="mt-3">
                <button type="button" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#manageObjectivesModal">
                    <i class="bi bi-plus-circle me-2"></i> เริ่มกำหนดจุดประสงค์
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Manage Objectives Modal -->
<div class="modal fade" id="manageObjectivesModal" tabindex="-1" aria-labelledby="manageObjectivesModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary py-4 border-0">
                <div class="text-start">
                    <h4 class="modal-title fw-bold text-white" id="manageObjectivesModalLabel">จัดการจุดประสงค์</h4>
                    <p class="text-white text-opacity-75 small mb-0">เพิ่มหรือแก้ไขเกณฑ์การประเมินกิจกรรมชุมนุม</p>
                </div>
                <button type="button" class="btn-close btn-close-white closeModalAndReload" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-start">
                <form action="<?= site_url('club/saveObjectiveDefinition/' . $club->club_id) ?>" method="post" id="objectiveDefinitionForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="objective_id" id="objective_id">
                    
                    <div class="card bg-light border-0 rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-plus-square-dotted me-2"></i>เพิ่ม/แก้ไข ข้อมูลจุดประสงค์</h6>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="objective_name" name="objective_name" placeholder="ชื่อจุดประสงค์" required>
                                <label for="objective_name">ชื่อจุดประสงค์ (เช่น ผ่านการทดสอบสมรรถภาพ) <span class="text-danger">*</span></label>
                            </div>
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="objective_description" name="objective_description" placeholder="รายละเอียด" style="height: 80px"></textarea>
                                <label for="objective_description">รายละเอียดเพิ่มเติม (ถ้ามี)</label>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="objective_order" name="objective_order" placeholder="ลำดับ" required>
                                        <label for="objective_order">ลำดับที่แสดง <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm w-100 fw-bold submit-with-loading">
                                        <i class="bi bi-save-fill me-2"></i> บันทึกจุดประสงค์
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <hr class="my-4 opacity-10">

                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-check me-2"></i>จุดประสงค์ที่มีอยู่</h6>
                <div id="objectives-list-container">
                    <?php if (!empty($objectives)): ?>
                        <div class="table-responsive rounded-3 border">
                            <table class="table table-hover align-middle mb-0" id="objectives-modal-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center" style="width: 70px;">ลำดับ</th>
                                        <th>ชื่อจุดประสงค์</th>
                                        <th class="text-end" style="width: 180px;">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($objectives as $objective): ?>
                                        <tr>
                                            <td class="text-center fw-bold"><?= esc($objective->objective_order) ?></td>
                                            <td>
                                                <div class="fw-bold"><?= esc($objective->objective_name) ?></div>
                                                <div class="smallest text-muted text-wrap"><?= esc($objective->objective_description) ?></div>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-2 justify-content-end">
                                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 edit-objective-btn"
                                                        data-objective-id="<?= esc($objective->objective_id) ?>"
                                                        data-objective-name="<?= esc($objective->objective_name) ?>"
                                                        data-objective-description="<?= esc($objective->objective_description) ?>"
                                                        data-objective-order="<?= esc($objective->objective_order) ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="<?= site_url('club/deleteObjective/' . $club->club_id . '/' . $objective->objective_id) ?>"
                                                        class="btn btn-sm btn-outline-danger rounded-pill px-3 delete-objective-btn">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info rounded-3">ยังไม่มีจุดประสงค์สำหรับชุมนุมนี้</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm closeModalAndReload" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let hasChangedObjectives = false;

    // Help Modal Activation
    const helpModal = document.getElementById('clubHelpModal');
    if (helpModal) {
        helpModal.addEventListener('show.bs.modal', function () {
            setTimeout(() => {
                const tabEl = document.querySelector('#help-pills-attendance-tab');
                if (tabEl) new bootstrap.Tab(tabEl).show();
            }, 300);
        });
    }

    const totalObjectives = <?= !empty($objectives) ? count($objectives) : 0 ?>;

    function updateRowSummary(row) {
        if (!row) return;

        const totalPassed = row.querySelectorAll('.objective-checkbox:checked').length;
        const totalPassedCell = row.querySelector('.total-passed-cell');
        const objectiveResultCell = row.querySelector('.objective-result-cell');

        if (totalPassedCell) {
            totalPassedCell.textContent = totalPassed;
        }

        const isPass = (totalObjectives > 0 && totalPassed === totalObjectives);
        
        if (objectiveResultCell) {
            objectiveResultCell.textContent = isPass ? 'ผ' : 'มผ';
            objectiveResultCell.classList.remove('badge-pass', 'badge-fail');
            objectiveResultCell.classList.add(isPass ? 'badge-pass' : 'badge-fail');
        }
    }

    // Single Checkbox events
    document.querySelectorAll('.objective-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateRowSummary(this.closest('tr'));
            updateColumnCheckAllState(this.dataset.objectiveId);
        });
    });

    // --- Check All per Objective Column ---
    document.querySelectorAll('.check-all-objective').forEach(headerCheck => {
        headerCheck.addEventListener('change', function() {
            const objectiveId = this.dataset.objectiveId;
            const isChecked = this.checked;
            
            document.querySelectorAll('.obj-check-' + objectiveId).forEach(cellCheck => {
                cellCheck.checked = isChecked;
                updateRowSummary(cellCheck.closest('tr'));
            });
        });
    });

    function updateColumnCheckAllState(objectiveId) {
        const headerCheck = document.querySelector('.check-all-objective[data-objective-id="' + objectiveId + '"]');
        if (!headerCheck) return;
        
        const columnChecks = document.querySelectorAll('.obj-check-' + objectiveId);
        const checkedCount = document.querySelectorAll('.obj-check-' + objectiveId + ':checked').length;
        headerCheck.checked = (checkedCount === columnChecks.length);
    }

    // --- Check All Global (Pass All Students in All Objectives) ---
    const checkAllGlobal = document.getElementById('checkAllGlobal');
    if (checkAllGlobal) {
        checkAllGlobal.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.objective-checkbox, .check-all-objective').forEach(cb => {
                cb.checked = isChecked;
            });
            document.querySelectorAll('tbody tr[data-student-id]').forEach(row => {
                updateRowSummary(row);
            });
        });
    }

    // Initial calculation and state
    document.querySelectorAll('tbody tr[data-student-id]').forEach(row => {
        updateRowSummary(row);
    });
    
    // Set initial check-all columns state
    <?php foreach ($objectives as $objective): ?>
        updateColumnCheckAllState('<?= $objective->objective_id ?>');
    <?php endforeach; ?>

    // --- Modal Management ---
    const objectiveForm = document.getElementById('objectiveDefinitionForm');
    const modalTitle = document.getElementById('manageObjectivesModalLabel');
    const objectiveIdInput = document.getElementById('objective_id');
    const listContainer = document.getElementById('objectives-list-container');

    // Reset modal
    const settingsBtn = document.querySelector('button[data-bs-target="#manageObjectivesModal"]');
    if (settingsBtn) {
        settingsBtn.addEventListener('click', function() {
            objectiveForm.reset();
            objectiveIdInput.value = '';
            modalTitle.textContent = 'จัดการจุดประสงค์';
        });
    }

    // Close and Reload Functionality
    document.querySelectorAll('.closeModalAndReload').forEach(btn => {
        btn.addEventListener('click', function() {
            if (hasChangedObjectives) {
                location.reload();
            }
        });
    });

    // AJAX Submission for Objective Definition
    if (objectiveForm) {
        objectiveForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const formData = new FormData(this);
            
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> กำลังบันทึก...';

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hasChangedObjectives = true;
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                    
                    this.reset();
                    objectiveIdInput.value = '';
                    modalTitle.textContent = 'จัดการจุดประสงค์';
                    updateObjectivesTable(data.objectives);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            });
        });
    }

    function updateObjectivesTable(objectives) {
        if (!objectives || objectives.length === 0) {
            listContainer.innerHTML = '<div class="alert alert-info rounded-3">ยังไม่มีจุดประสงค์สำหรับชุมนุมนี้</div>';
            return;
        }

        let html = `
            <div class="table-responsive rounded-3 border">
                <table class="table table-hover align-middle mb-0" id="objectives-modal-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 70px;">ลำดับ</th>
                            <th>ชื่อจุดประสงค์</th>
                            <th class="text-end" style="width: 180px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        objectives.forEach(obj => {
            html += `
                <tr>
                    <td class="text-center fw-bold">${obj.objective_order}</td>
                    <td>
                        <div class="fw-bold">${obj.objective_name}</div>
                        <div class="smallest text-muted text-wrap">${obj.objective_description || ''}</div>
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 edit-objective-btn"
                                data-objective-id="${obj.objective_id}"
                                data-objective-name="${obj.objective_name}"
                                data-objective-description="${obj.objective_description || ''}"
                                data-objective-order="${obj.objective_order}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="<?= site_url('club/deleteObjective/' . $club->club_id) ?>/${obj.objective_id}"
                                class="btn btn-sm btn-outline-danger rounded-pill px-3 delete-objective-btn">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        listContainer.innerHTML = html;
    }

    // Delegation for modal edit/delete
    listContainer.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.edit-objective-btn');
        if (editBtn) {
            objectiveIdInput.value = editBtn.dataset.objectiveId;
            document.getElementById('objective_name').value = editBtn.dataset.objectiveName;
            document.getElementById('objective_description').value = editBtn.dataset.objectiveDescription;
            document.getElementById('objective_order').value = editBtn.dataset.objectiveOrder;
            modalTitle.textContent = 'แก้ไขจุดประสงค์';
            objectiveForm.scrollIntoView({ behavior: 'smooth' });
        }

        const deleteBtn = e.target.closest('.delete-objective-btn');
        if (deleteBtn) {
            e.preventDefault();
            const url = deleteBtn.href;
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "จุดประสงค์นี้จะถูกลบออกจากระบบ และส่งผลต่อการประเมิน",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    });

    // Main form submission loading (Redirect)
    const mainForm = document.getElementById('objectivesForm');
    if (mainForm) {
        mainForm.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> กำลังบันทึกข้อมูล...';
        });
    }
});
</script>
<?= $this->endSection() ?>