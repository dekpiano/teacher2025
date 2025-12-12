<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ดาวน์โหลดงานวิจัยในชั้นเรียน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

        <div class="container-fluid">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">ตัวกรอง</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label for="SelResearcher" class="form-label">เลือกครูผู้สอน</label>
                            <select name="SelResearcher" id="SelResearcher" class="form-select">
                                <option value="All">กรุณาเลือกครูผู้สอน</option>
                                <?php foreach ($SelTeacher as $v_SelTeacher): // Assuming $SelTeacher is passed with teacher data ?>
                                <option <?= ($CheckTeach == $v_SelTeacher->pers_id) ? "selected":"" ?> value="<?= esc($v_SelTeacher->pers_id) ?>">
                                    <?= esc($v_SelTeacher->pers_prefix . $v_SelTeacher->pers_firstname . ' ' . $v_SelTeacher->pers_lastname) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="CheckYearResearch" class="form-label">ปีการศึกษา</label>
                            <select name="CheckYearResearch" id="CheckYearResearch" class="form-select">
                                <?php foreach ($CheckYear as $v_CheckYear): // Assuming $CheckYear holds year/term data ?>
                                <option
                                    <?= ($current_year == $v_CheckYear->seres_year && $current_term == $v_CheckYear->seres_term) ? "selected":"" ?>
                                    value="<?= esc($v_CheckYear->seres_year.'/'.$v_CheckYear->seres_term) ?>">
                                    <?= esc($v_CheckYear->seres_term.'/'.$v_CheckYear->seres_year) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="SearchResearch" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i> ค้นหา
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($CheckTeach != "All"): ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tb_research">
                            <thead>
                                <tr>
                                    <th scope="col">ปีการศึกษา</th>
                                    <th scope="col">รหัสวิชา</th>
                                    <th scope="col">ชื่อรายวิชา</th>
                                    <th scope="col">ระดับ</th>
                                    <th scope="col">ชื่องานวิจัย</th>
                                    <th scope="col">สถานะ</th>
                                    <th scope="col">ไฟล์งานวิจัย</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($research as $r): // $research is already filtered for teacher, year, term ?>
                                <tr>
                                    <td scope="row"><?= esc($r['seres_year']) ?>/<?= esc($r['seres_term']) ?></td>
                                    <td><?= esc($r['seres_coursecode']) ?></td>
                                    <td><?= esc($r['seres_namesubject']) ?></td>
                                    <td>ม.<?= esc($r['seres_gradelevel']) ?></td>
                                    <td><?= esc($r['seres_research_name']) ?></td>
                                    <td>
                                        <?php if (trim($r['seres_status']) == 'ส่งแล้ว') : ?>
                                            <span class="badge bg-primary">ส่งแล้ว</span>
                                        <?php elseif (trim($r['seres_status']) == 'ตรวจแล้ว') : ?>
                                            <span class="badge bg-success">ตรวจแล้ว</span>
                                        <?php else : ?>
                                            <span class="badge bg-warning">รอดำเนินการ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if(!empty($r['seres_file'])):
                                            $file = $r['seres_file'];
                                            $id = $r['seres_ID'];
                                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            $iconClass = '';
                                            switch ($ext) {
                                                case 'pdf':
                                                    $iconClass = 'bi-file-earmark-pdf-fill text-danger';
                                                    break;
                                                case 'doc':
                                                case 'docx':
                                                    $iconClass = 'bi-file-earmark-word-fill text-primary';
                                                    break;
                                                case 'xls':
                                                case 'xlsx':
                                                    $iconClass = 'bi-file-earmark-excel-fill text-success';
                                                    break;
                                                case 'ppt':
                                                case 'pptx':
                                                    $iconClass = 'bi-file-earmark-ppt-fill text-warning';
                                                    break;
                                                case 'zip':
                                                case 'rar':
                                                    $iconClass = 'bi-file-earmark-zip-fill text-secondary';
                                                    break;
                                                case 'jpg':
                                                case 'jpeg':
                                                case 'png':
                                                case 'gif':
                                                    $iconClass = 'bi-file-earmark-image-fill text-info';
                                                    break;
                                                default:
                                                    $iconClass = 'bi-file-earmark-arrow-down-fill';
                                            }
                                        ?>
                                        <a href="<?= site_url('research/download-research-file/' . esc($id)) ?>">
                                            <i class="bi <?= $iconClass ?> h4"></i>
                                        </a>
                                        <?php else: ?>
                                        <span class="badge bg-label-danger">ไม่มีไฟล์</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php else:  ?>
                <div class="card">
                    <div class="card-body">
                       <div class="text-center">
                       กรุณาเลือกครูผู้สอน เพื่อดูงานวิจัย
                       </div> 
                    </div>
                </div>
            <?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#SearchResearch').on('click', function() {
        var selectedResearcher = $('#SelResearcher').val();
        var selectedYearTerm = $('#CheckYearResearch').val();
        var yearParts = selectedYearTerm.split('/');
        var year = yearParts[0];
        var term = yearParts[1];
        // The controller method loadResearch has year, term, then optional teacher ID
        window.location.href = `<?= site_url('research/load-research/') ?>${year}/${term}/${selectedResearcher}`;
    });

    // Initialize DataTables if needed
    // $('#tb_research').DataTable();
});
</script>
<?= $this->endSection() ?>
