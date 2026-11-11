<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'สืบค้นแผนการสอน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .browse-header-luxe {
        background: linear-gradient(135deg, #696cff 0%, #3f42ef 100%);
        border-radius: 1.25rem;
        padding: 2.25rem;
        color: white;
        margin-bottom: 1.75rem;
        box-shadow: 0 10px 30px rgba(105, 108, 255, 0.2);
    }
    .filter-card-luxe {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.04);
        margin-bottom: 1.75rem;
    }
    .teacher-summary-card {
        border: none;
        border-radius: 1.25rem;
        background: #ffffff;
        box-shadow: 0 8px 25px rgba(0,0,0,0.04);
        margin-bottom: 1.75rem;
        border-left: 5px solid #696cff;
    }
    .luxe-table-card {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .luxe-table thead th {
        background: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #566a7f;
        padding: 1.25rem 1rem;
        border-bottom: 2px solid #e7e7ff;
    }
    .luxe-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    .file-icon-luxe {
        font-size: 1.5rem;
        transition: transform 0.2s;
    }
    .file-link:hover .file-icon-luxe {
        transform: scale(1.2);
    }

    /* Select2 Luxe Styling */
    .select2-container .select2-selection--single {
        height: 50px !important;
        border: 1px solid #d9dee3 !important;
        border-radius: 0.75rem !important;
        background-color: #f8f9fa !important;
        display: flex !important;
        align-items: center !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, background-color 0.15s ease-in-out;
    }
    .select2-container--default .select2-selection--single:hover {
        border-color: #696cff !important;
        background-color: #ffffff !important;
    }
    .select2-container--default.select2-container--open .select2-selection--single,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #696cff !important;
        box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.15) !important;
        background-color: #ffffff !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 48px !important;
        padding-left: 1.1rem !important;
        padding-right: 2.2rem !important;
        color: #566a7f !important;
        font-weight: 500;
        font-size: 0.95rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px !important;
        right: 14px !important;
    }
    .select2-dropdown {
        border-radius: 0.75rem !important;
        border: 1px solid #e0e4e8 !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden;
        z-index: 9999 !important;
    }
    .select2-search--dropdown {
        padding: 0.75rem !important;
    }
    .select2-search--dropdown .select2-search__field {
        border-radius: 0.5rem !important;
        border: 1px solid #d9dee3 !important;
        padding: 0.55rem 0.85rem !important;
    }
    .select2-search--dropdown .select2-search__field:focus {
        border-color: #696cff !important;
        outline: none !important;
    }
    .select2-results__option {
        padding: 0.65rem 1rem !important;
        font-size: 0.95rem;
    }
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #696cff !important;
        color: #ffffff !important;
    }

    @media (max-width: 767.98px) {
        .browse-header-luxe {
            padding: 1.5rem 1.25rem;
            border-radius: 1rem;
        }
        .browse-header-luxe h1 {
            font-size: 1.5rem;
        }
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="container-xxl flex-grow-1">
    <?php
    $distinctTypePlans = array_column($activePlanTypes, 'type_name');
    sort($distinctTypePlans);

    // Find current learning name
    $currentLearningName = 'ทุกกลุ่มสาระการเรียนรู้';
    if (!empty($learnings)) {
        foreach ($learnings as $l) {
            if ($l->lear_id == $current_learning) {
                $currentLearningName = $l->lear_namethai;
                break;
            }
        }
    }
    ?>

    <!-- Header -->
    <div class="browse-header-luxe d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm fw-bold">
                    <i class="bi bi-bookmark-check-fill me-1 text-primary"></i> <?= esc($current_learning_name ?? $currentLearningName) ?>
                </span>
            </div>
            <h1 class="display-6 fw-bold mb-1 text-white">สืบค้นแผนการสอน</h1>
            <p class="opacity-75 mb-0">ค้นหาและดาวน์โหลดเอกสารแผนการสอนกลุ่มสาระฯ <?= esc($current_learning_name ?? $currentLearningName) ?> เพื่อการเรียนรู้และพัฒนา</p>
        </div>
        <a href="<?= base_url('curriculum') ?>" class="btn btn-white btn-lg rounded-pill shadow-sm px-4 text-primary fw-bold">
            <i class="bi bi-house-door me-2"></i> หน้าหลักงานวิชาการ
        </a>
    </div>

    <!-- Filters Card -->
    <div class="card filter-card-luxe">
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <?php if (empty($is_locked_learning)): ?>
                    <!-- Learning Group Filter (For administrators / staff without assigned department) -->
                    <div class="col-lg-4 col-md-6">
                        <label for="SelLearning" class="form-label fw-bold text-muted small mb-2">
                            <i class="bi bi-collection-fill text-primary me-1"></i> กลุ่มสาระการเรียนรู้
                        </label>
                        <select name="SelLearning" id="SelLearning" class="form-select select2">
                            <option value="All" <?= ($current_learning === 'All') ? 'selected' : '' ?>>-- ทุกกลุ่มสาระการเรียนรู้ --</option>
                            <?php if (!empty($learnings)): ?>
                                <?php foreach ($learnings as $l): ?>
                                    <option value="<?= esc($l->lear_id) ?>" <?= ($current_learning === $l->lear_id) ? 'selected' : '' ?>>
                                        <?= esc($l->lear_namethai) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Teacher Filter -->
                <div class="<?= empty($is_locked_learning) ? 'col-lg-3 col-md-6' : 'col-lg-6 col-md-6' ?>">
                    <label for="SelTeacher" class="form-label fw-bold text-muted small mb-2">
                        <i class="bi bi-person-badge-fill text-primary me-1"></i> ครูผู้สอน
                    </label>
                    <select name="SelTeacher" id="SelTeacher" class="form-select select2">
                        <option value="All" <?= ($CheckTeach === 'All') ? 'selected' : '' ?>>-- ครูทุกคนในกลุ่มสาระนี้ --</option>
                        <?php if (!empty($SelTeacher)): ?>
                            <?php foreach ($SelTeacher as $v_SelTeacher): ?>
                                <?php 
                                    $tName = trim(($v_SelTeacher->pers_prefix ?? '') . ($v_SelTeacher->pers_firstname ?? '') . ' ' . ($v_SelTeacher->pers_lastname ?? ''));
                                    if (empty($tName)) $tName = 'รหัสครู ' . $v_SelTeacher->pers_id;
                                    $posDesc = !empty($v_SelTeacher->posi_name) ? " ({$v_SelTeacher->posi_name})" : '';
                                ?>
                                <option <?= ($CheckTeach == $v_SelTeacher->pers_id) ? 'selected' : '' ?> value="<?= esc($v_SelTeacher->pers_id) ?>">
                                    <?= esc($tName . $posDesc) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Semester / School Year Filter -->
                <div class="<?= empty($is_locked_learning) ? 'col-lg-3 col-md-6' : 'col-lg-4 col-md-6' ?>">
                    <label for="CheckYear" class="form-label fw-bold text-muted small mb-2">
                        <i class="bi bi-calendar-event-fill text-primary me-1"></i> ปีการศึกษา / ภาคเรียน
                    </label>
                    <select name="CheckYear" id="CheckYear" class="form-select select2">
                        <?php foreach ($CheckYear as $v_CheckYear): ?>
                            <option <?= ($current_year == $v_CheckYear->seplan_year && $current_term == $v_CheckYear->seplan_term) ? 'selected' : '' ?>
                                value="<?= esc($v_CheckYear->seplan_year.'/'.$v_CheckYear->seplan_term) ?>">
                                ภาคเรียนที่ <?= esc($v_CheckYear->seplan_term.'/'.$v_CheckYear->seplan_year) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Search Button -->
                <div class="<?= empty($is_locked_learning) ? 'col-lg-2 col-md-6' : 'col-lg-2 col-md-12' ?>">
                    <button type="button" id="SearchPlan" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm" style="height: 50px;">
                        <i class="bi bi-search me-1"></i> ค้นหาข้อมูล
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($hasSearched)): ?>
        <!-- Active Search Summary Badge / Info Card -->
        <?php if (!empty($selectedTeacherInfo)): ?>
            <?php 
                $selectedName = trim(($selectedTeacherInfo->pers_prefix ?? '') . ($selectedTeacherInfo->pers_firstname ?? '') . ' ' . ($selectedTeacherInfo->pers_lastname ?? ''));
                $selectedPosition = $selectedTeacherInfo->posi_name ?? 'ครูผู้สอน';
            ?>
            <div class="card teacher-summary-card p-3 mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-label-primary p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-person-check-fill text-primary fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark"><?= esc($selectedName) ?></h5>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge bg-label-primary"><?= esc($selectedPosition) ?></span>
                                <span class="text-muted small"><i class="bi bi-mortarboard me-1"></i>กลุ่มสาระ: <?= esc($currentLearningName) ?></span>
                                <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>ภาคเรียนที่ <?= esc($current_term . '/' . $current_year) ?></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="<?= site_url('curriculum/download-plan-zip/' . esc($selectedTeacherInfo->pers_id)) ?>" class="btn btn-outline-primary rounded-pill shadow-sm">
                            <i class="bi bi-file-earmark-zip me-1"></i> ดาวน์โหลดรวม ZIP (ของครูท่านนี้)
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Plans Table -->
        <?php 
        $organizedPlans = [];
        foreach ($plan as $p) {
            $key = $p->seplan_coursecode . '-' . $p->seplan_usersend . '-' . $p->seplan_year . '-' . $p->seplan_term;
            if (!isset($organizedPlans[$key])) {
                $tName = trim(($p->pers_prefix ?? '') . ($p->pers_firstname ?? '') . ' ' . ($p->pers_lastname ?? ''));
                $organizedPlans[$key] = [
                    'seplan_year'        => $p->seplan_year,
                    'seplan_term'        => $p->seplan_term,
                    'seplan_coursecode'  => $p->seplan_coursecode,
                    'seplan_namesubject' => $p->seplan_namesubject,
                    'seplan_gradelevel'  => $p->seplan_gradelevel,
                    'seplan_typesubject' => $p->seplan_typesubject,
                    'teacher_name'       => $tName ?: 'รหัสครู ' . $p->seplan_usersend,
                    'teacher_id'         => $p->seplan_usersend,
                    'files'              => []
                ];
            }
            $organizedPlans[$key]['files'][$p->type_name] = ['file' => $p->seplan_file, 'id' => $p->seplan_ID];
        }
        ?>

        <div class="card luxe-table-card">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-journal-text text-primary me-2"></i>รายการแผนการสอน ภาคเรียนที่ <?= esc($current_term . '/' . $current_year) ?>
                    <span class="badge bg-label-info ms-2"><?= esc($currentLearningName) ?></span>
                </h5>
                <span class="badge bg-label-secondary small">พบข้อมูล <?= count($organizedPlans) ?> รายวิชา</span>
            </div>
            <div class="table-responsive">
                <table class="table luxe-table table-hover mb-0" id="tb_plan">
                    <thead>
                        <tr>
                            <th class="ps-4">รายวิชา</th>
                            <?php if ($CheckTeach === 'All'): ?>
                                <th>ครูผู้สอน</th>
                            <?php endif; ?>
                            <th>ระดับ</th>
                            <th>ประเภท</th>
                            <?php foreach($distinctTypePlans as $tp): ?>
                                <th class="text-center"><?= esc($tp) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($organizedPlans as $op): ?>
                            <tr>
                                <td class="ps-4 text-nowrap">
                                    <div class="fw-bold text-primary mb-0"><?= esc($op['seplan_coursecode']) ?></div>
                                    <div class="small text-muted"><?= esc($op['seplan_namesubject']) ?></div>
                                </td>
                                <?php if ($CheckTeach === 'All'): ?>
                                    <td class="text-nowrap">
                                        <span class="badge bg-label-dark font-weight-medium">
                                            <i class="bi bi-person me-1"></i><?= esc($op['teacher_name']) ?>
                                        </span>
                                    </td>
                                <?php endif; ?>
                                <td>ม.<?= esc($op['seplan_gradelevel']) ?></td>
                                <td><span class="badge bg-label-info"><?= esc($op['seplan_typesubject']) ?></span></td>

                                <?php foreach($distinctTypePlans as $tp): ?>
                                    <td class="text-center">
                                        <?php if(isset($op['files'][$tp]) && !empty($op['files'][$tp]['file'])):
                                            $id = $op['files'][$tp]['id'];
                                            $file = $op['files'][$tp]['file'];
                                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            $iconClass = 'bi-file-earmark-arrow-down-fill text-primary';
                                            if ($ext == 'pdf') $iconClass = 'bi-file-earmark-pdf-fill text-danger';
                                            elseif (in_array($ext, ['doc', 'docx'])) $iconClass = 'bi-file-earmark-word-fill text-primary';
                                        ?>
                                            <a href="<?= site_url('curriculum/download-plan-file/' . esc($id)) ?>" class="file-link" title="ดาวน์โหลด <?= esc($tp) ?>">
                                                <i class="bi <?= $iconClass ?> file-icon-luxe"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="javascript:void(0);" class="no-file-alert badge bg-label-secondary opacity-50 small" title="ไม่มีไฟล์">N/A</a>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($organizedPlans)): ?>
                            <tr>
                                <td colspan="<?= ($CheckTeach === 'All' ? 4 : 3) + count($distinctTypePlans) ?>" class="text-center py-5">
                                    <div class="opacity-50 mb-2 mt-2">
                                        <i class="bi bi-folder-x display-1"></i>
                                    </div>
                                    <h5 class="text-muted fw-bold">ไม่พบข้อมูลแผนการสอนตามเงื่อนไขที่เลือก</h5>
                                    <p class="text-muted small mb-0">ลองเลือกภาคเรียนอื่น หรือเปลี่ยนครูผู้สอนในการค้นหา</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <!-- Initial Prompt State (Before User Searches) -->
        <div class="card luxe-table-card p-5 text-center shadow-sm">
            <div class="mb-4">
                <i class="bi bi-search display-1 text-primary opacity-25"></i>
            </div>
            <h4 class="fw-bold mb-2">เริ่มสืบค้นแผนการสอน</h4>
            <p class="text-muted mx-auto mb-0" style="max-width: 460px;">
                กรุณาเลือกรายชื่อครูผู้สอนและปีการศึกษา/ภาคเรียน จากตัวกรองด้านบน แล้วกดปุ่ม <span class="badge bg-primary text-white px-2 py-1 rounded-pill"><i class="bi bi-search me-1"></i>ค้นหาข้อมูล</span> เพื่อแสดงรายการแผนการสอน
            </p>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 on filter dropdowns
    if ($('#SelLearning').length) {
        $('#SelLearning').select2({
            placeholder: '-- เลือกกลุ่มสาระการเรียนรู้ --',
            width: '100%'
        });

        // Dynamic Teacher list update when Learning Group changes (for admin)
        $('#SelLearning').on('change', function() {
            const learningId = $(this).val();
            const $teacherSelect = $('#SelTeacher');
            
            $teacherSelect.prop('disabled', true);
            
            $.ajax({
                url: '<?= base_url('curriculum/get-teachers-by-learning') ?>',
                type: 'GET',
                data: { learning: learningId },
                dataType: 'json',
                success: function(teachers) {
                    $teacherSelect.empty();
                    $teacherSelect.append('<option value="All">-- ครูทุกคนในกลุ่มสาระนี้ --</option>');
                    
                    teachers.forEach(function(t) {
                        $teacherSelect.append(new Option(t.label, t.id, false, false));
                    });
                    
                    $teacherSelect.prop('disabled', false).trigger('change');
                },
                error: function() {
                    $teacherSelect.prop('disabled', false);
                }
            });
        });
    }

    $('#SelTeacher').select2({
        placeholder: '-- ค้นหาและเลือกครูผู้สอน --',
        width: '100%'
    });

    $('#CheckYear').select2({
        width: '100%'
    });

    // Submit Search
    $('#SearchPlan').on('click', function() {
        const teacher = $('#SelTeacher').val() || 'All';
        const yearTerm = $('#CheckYear').val();
        
        if (!yearTerm) {
            Swal.fire('ข้อผิดพลาด', 'กรุณาเลือกปีการศึกษา/ภาคเรียน', 'warning');
            return;
        }
        
        const [year, term] = yearTerm.split('/');
        <?php if (!empty($is_locked_learning)): ?>
            window.location.href = `<?= site_url('curriculum/download-plan/') ?>${year}/${term}/${teacher}`;
        <?php else: ?>
            const learning = ($('#SelLearning').length && $('#SelLearning').val()) ? $('#SelLearning').val() : 'lear_001';
            window.location.href = `<?= site_url('curriculum/download-plan/') ?>${learning}/${year}/${term}/${teacher}`;
        <?php endif; ?>
    });

    // No file notice
    $(document).on('click', '.no-file-alert', function(e) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'ไม่พบไฟล์',
            text: 'ไม่มีการอัปโหลดไฟล์สำหรับหัวข้อนี้ในระบบ',
            confirmButtonColor: '#696cff',
            confirmButtonText: 'ตกลง'
        });
    });
});
</script>
<?= $this->endSection() ?>
