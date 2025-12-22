<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'คลังงานวิจัยในชั้นเรียน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .browse-header {
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-left: 6px solid #696cff;
    }
    .filter-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }
    .research-table th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #566a7f;
    }
    .file-icon-btn {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }
    .file-icon-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
</style>

<div class="container-xxl flex-grow-1">
    <!-- Page Header -->
    <div class="browse-header">
        <div>
            <h4 class="fw-bold mb-0">คลังงานวิจัยในชั้นเรียน</h4>
            <p class="text-muted mb-0 small">ค้นหาและดูผลงานวิจัยของบุคลากรภายในโรงเรียน</p>
        </div>
        <div>
            <i class="bi bi-journal-bookmark fs-1 text-primary opacity-25"></i>
        </div>
    </div>

    <!-- Filters -->
    <div class="card filter-card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="SelResearcher" class="form-label fw-bold">เลือกครูผู้สอน</label>
                    <select name="SelResearcher" id="SelResearcher" class="form-select select2">
                        <option value="All">--- ค้นหาครูผู้สอน ---</option>
                        <?php foreach ($SelTeacher as $v_SelTeacher): ?>
                        <option <?= ($CheckTeach == $v_SelTeacher->pers_id) ? "selected":"" ?> value="<?= esc($v_SelTeacher->pers_id) ?>">
                            <?= esc($v_SelTeacher->pers_prefix . $v_SelTeacher->pers_firstname . ' ' . $v_SelTeacher->pers_lastname) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="CheckYearResearch" class="form-label fw-bold">ปีการศึกษา/ภาคเรียน</label>
                    <select name="CheckYearResearch" id="CheckYearResearch" class="form-select">
                        <?php foreach ($CheckYear as $v_CheckYear): ?>
                        <option
                            <?= ($current_year == $v_CheckYear->seres_year && $current_term == $v_CheckYear->seres_term) ? "selected":"" ?>
                            value="<?= esc($v_CheckYear->seres_year.'/'.$v_CheckYear->seres_term) ?>">
                            ภาคเรียนที่ <?= esc($v_CheckYear->seres_term.' ปีการศึกษา '.$v_CheckYear->seres_year) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" id="SearchResearch" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-search me-2"></i> ค้นหา
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Results -->
    <?php if($CheckTeach != "All"): ?>
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 research-table" id="tb_research">
                <thead>
                    <tr>
                        <th class="ps-4">วิชา/ชั้น</th>
                        <th>ชื่องานวิจัย</th>
                        <th>สถานะ</th>
                        <th class="text-center">ตรวจสอบ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($research)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="bi bi-folder-x display-4 text-muted"></i>
                                <p class="text-muted mt-2">ไม่พบข้อมูลงานวิจัยในช่วงเวลาที่แจ้ง</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($research as $r): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><?= esc($r['seres_coursecode']) ?></div>
                                <div class="small text-muted"><?= esc($r['seres_namesubject']) ?> | ม.<?= esc($r['seres_gradelevel']) ?></div>
                            </td>
                            <td>
                                <div class="text-truncate-2" style="max-width: 400px;" title="<?= esc($r['seres_research_name']) ?>">
                                    <?= esc($r['seres_research_name']) ?>
                                </div>
                            </td>
                            <td>
                                <?php 
                                    $status = trim($r['seres_status']);
                                    $badgeStyle = 'bg-label-warning';
                                    if($status == 'ส่งแล้ว') $badgeStyle = 'bg-label-primary';
                                    if($status == 'ตรวจแล้ว') $badgeStyle = 'bg-label-success';
                                ?>
                                <span class="badge rounded-pill <?= $badgeStyle ?>"><?= $status ?></span>
                            </td>
                            <td class="text-center">
                                <?php if(!empty($r['seres_file'])):
                                    $id = $r['seres_ID'];
                                    $ext = strtolower(pathinfo($r['seres_file'], PATHINFO_EXTENSION));
                                    $color = 'btn-label-danger';
                                    $icon = 'bi-file-earmark-pdf-fill';
                                    if(in_array($ext, ['doc', 'docx'])) {
                                        $color = 'btn-label-primary';
                                        $icon = 'bi-file-earmark-word-fill';
                                    }
                                ?>
                                <a href="<?= site_url('research/download-research-file/' . esc($id)) ?>" 
                                   class="btn <?= $color ?> file-icon-btn" title="ดาวน์โหลดไฟล์">
                                    <i class="bi <?= $icon ?> h5 mb-0"></i>
                                </a>
                                <?php else: ?>
                                <span class="badge bg-label-secondary">ไม่มีไฟล์</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else:  ?>
        <div class="card border-0 shadow-sm rounded-4 py-5 text-center">
            <div class="card-body">
                <div class="mb-3">
                    <i class="bi bi-person-badge display-3 text-light"></i>
                </div>
                <h5 class="fw-bold">เริ่มต้นใช้งาน</h5>
                <p class="text-muted">กรุณาเลือกคุณครูผู้สอนที่ต้องการตรวจสอบผลงานวิจัยจากตัวกรองด้านบน</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#SearchResearch').on('click', function() {
        var selectedResearcher = $('#SelResearcher').val();
        var selectedYearTerm = $('#CheckYearResearch').val();
        if(selectedResearcher === 'All') {
            Swal.fire('คำแนะนำ', 'กรุณาเลือกชื่อครูผู้สอนที่ต้องการค้นหา', 'info');
            return;
        }
        var yearParts = selectedYearTerm.split('/');
        var year = yearParts[0];
        var term = yearParts[1];
        window.location.href = `<?= site_url('research/load-research/') ?>${year}/${term}/${selectedResearcher}`;
    });
});
</script>
<?= $this->endSection() ?>
