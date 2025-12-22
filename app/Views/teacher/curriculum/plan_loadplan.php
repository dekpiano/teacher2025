<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'สืบค้นแผนการสอน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .browse-header-luxe {
        background: linear-gradient(135deg, #696cff 0%, #3f42ef 100%);
        border-radius: 1.25rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(105, 108, 255, 0.2);
    }
    .filter-card-luxe {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.04);
        margin-bottom: 2rem;
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
</style>

<div class="container-xxl flex-grow-1">
    <?php
    $distinctTypePlans = array_column($activePlanTypes, 'type_name');
    sort($distinctTypePlans);
    ?>

    <!-- Header -->
    <div class="browse-header-luxe d-flex justify-content-between align-items-center">
        <div>
            <h1 class="display-6 fw-bold mb-1 text-white">สืบค้นแผนการสอน</h1>
            <p class="opacity-75 mb-0">ค้นหาและดาวน์โหลดเอกสารแผนการสอนของครูท่านอื่นเพื่อการเรียนรู้</p>
        </div>
        <a href="<?= base_url('curriculum') ?>" class="btn btn-white btn-lg rounded-pill shadow-sm px-4 text-primary fw-bold">
            <i class="bi bi-house-door me-2"></i> หน้าหลัก
        </a>
    </div>

    <!-- Filters -->
    <div class="card filter-card-luxe">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="form-floating">
                        <select name="SelTeacher" id="SelTeacher" class="form-select border-0 bg-light">
                            <option value="All">เลือกครูผู้สอนเพื่อเริ่มต้น...</option>
                            <?php foreach ($SelTeacher as $v_SelTeacher): ?>
                                <option <?= ($CheckTeach == $v_SelTeacher->pers_id) ? "selected":"" ?> value="<?= esc($v_SelTeacher->pers_id) ?>">
                                    <?= esc($v_SelTeacher->pers_prefix . $v_SelTeacher->pers_firstname . ' ' . $v_SelTeacher->pers_lastname) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="SelTeacher">ครูผู้สอน</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select name="CheckYear" id="CheckYear" class="form-select border-0 bg-light">
                            <?php foreach ($CheckYear as $v_CheckYear): ?>
                                <option <?= ($current_year == $v_CheckYear->seplan_year && $current_term == $v_CheckYear->seplan_term) ? "selected":"" ?>
                                    value="<?= esc($v_CheckYear->seplan_year.'/'.$v_CheckYear->seplan_term) ?>">
                                    ภาคเรียนที่ <?= esc($v_CheckYear->seplan_term.'/'.$v_CheckYear->seplan_year) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="CheckYear">ปีการศึกษา/ภาคเรียน</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="button" id="SearchPlan" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm py-3">
                        <i class="bi bi-search me-2"></i> ค้นหาข้อมูล
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if($CheckTeach != "All"): ?>
        <div class="card luxe-table-card">
            <div class="table-responsive">
                <table class="table luxe-table table-hover mb-0" id="tb_plan">
                    <thead>
                        <tr>
                            <th class="ps-4">รายวิชา</th>
                            <th>ระดับ</th>
                            <th>ประเภท</th>
                            <?php foreach($distinctTypePlans as $tp): ?>
                                <th class="text-center"><?= esc($tp) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $organizedPlans = [];
                        foreach ($plan as $p) {
                            $key = $p->seplan_coursecode . '-' . $p->seplan_year . '-' . $p->seplan_term;
                            if (!isset($organizedPlans[$key])) {
                                $organizedPlans[$key] = [
                                    'seplan_year' => $p->seplan_year,
                                    'seplan_term' => $p->seplan_term,
                                    'seplan_coursecode' => $p->seplan_coursecode,
                                    'seplan_namesubject' => $p->seplan_namesubject,
                                    'seplan_gradelevel' => $p->seplan_gradelevel,
                                    'seplan_typesubject' => $p->seplan_typesubject,
                                    'files' => []
                                ];
                            }
                            $organizedPlans[$key]['files'][$p->type_name] = ['file' => $p->seplan_file, 'id' => $p->seplan_ID];
                        }
                        ?>
                        <?php foreach ($organizedPlans as $op):?>
                            <tr>
                                <td class="ps-4 text-nowrap">
                                    <div class="fw-bold text-primary mb-0"><?= esc($op['seplan_coursecode']) ?></div>
                                    <div class="small text-muted"><?= esc($op['seplan_namesubject']) ?></div>
                                </td>
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
                                            <span class="badge bg-label-secondary opacity-50 small">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($organizedPlans)): ?>
                            <tr>
                                <td colspan="<?= 3 + count($distinctTypePlans) ?>" class="text-center py-5">
                                    <div class="opacity-50 mb-2 mt-2">
                                        <i class="bi bi-folder-x display-1"></i>
                                    </div>
                                    <h5 class="text-muted">ไม่พบข้อมูลแผนการสอนในปีการศึกษานี้</h5>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="card luxe-table-card p-5 text-center">
            <div class="mb-4">
                <i class="bi bi-person-search display-1 text-primary opacity-25"></i>
            </div>
            <h4 class="fw-bold">เริ่มค้นหาแผนการสอน</h4>
            <p class="text-muted mx-auto" style="max-width: 400px;">กรุณาเลือกรายชื่อครูผู้สอนและปีการศึกษาที่ต้องการสืบค้นข้อมูลจากตัวกรองด้านบน</p>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#SearchPlan').on('click', function() {
        const teacher = $('#SelTeacher').val();
        const yearTerm = $('#CheckYear').val();
        if (teacher === 'All') {
            Swal.fire('แนะนำ', 'กรุณาเลือกครูผู้สอนก่อนทำการค้นหา', 'info');
            return;
        }
        const [year, term] = yearTerm.split('/');
        window.location.href = `<?= site_url('curriculum/download-plan/') ?>${year}/${term}/${teacher}`;
    });
});
</script>
<?= $this->endSection() ?>
