<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ประเมินคุณลักษณะอันพึงประสงค์') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .assessment-table th,
    .assessment-table td {
        vertical-align: middle;
        text-align: center;
        padding: 0.25rem;
        white-space: nowrap;
    }
    .assessment-table .student-name {
        text-align: left;
        white-space: normal;
    }
    .assessment-table .sub-item-col {
        width: 60px;
    }
    .assessment-table input[type="number"] {
        width: 50px;
        padding: 0.1rem 0.25rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        text-align: center;
    }
    .rotated-text {
        writing-mode: vertical-rl;
        text-orientation: mixed;
        transform: rotate(180deg);
        white-space: nowrap;
    }
    .summary-col {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    .final-summary-col {
        background-color: #fff3cd;
        font-weight: bold;
    }
    thead th {
        background-color: #e9ecef;
    }
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>

<div class="container-fluid">
    <!-- Summary Report Card -->
    <div class="card mb-4" id="report-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bi bi-bar-chart-line-fill me-2"></i>รายงานสรุปผลการประเมิน (ภาพรวมทั้งห้อง)</h5>
            <a href="<?= base_url('teacher/desirable_assessment/print_report/' . $className) ?>" target="_blank" class="btn btn-sm btn-primary">
                <i class="bi bi-printer-fill me-1"></i> พิมพ์รายงานสรุป
            </a>
        </div>
        <div class="card-body">
            <?php if ($totalAssessedStudents > 0): ?>
                <p>สรุปผลนักเรียนที่ได้รับการประเมินแล้วจำนวน <?= $totalAssessedStudents ?> คน จากทั้งหมด <?= $totalStudents ?> คน</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="">ผลการประเมิน</th>
                                <th>ดีเยี่ยม</th>
                                <th>ดี</th>
                                <th>ผ่าน</th>
                                <th>ไม่ผ่าน</th>
                                <th>รวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report as $key => $mainItemReport): ?>
                                <tr>
                                    <td class="text-start"><?=($key+1).'.'. esc($mainItemReport['name']) ?></td>
                                    <td><?= esc($mainItemReport['summary']['ดีเยี่ยม'] ?? 0) ?></td>
                                    <td><?= esc($mainItemReport['summary']['ดี'] ?? 0) ?></td>
                                    <td><?= esc($mainItemReport['summary']['ผ่าน'] ?? 0) ?></td>
                                    <td><?= esc($mainItemReport['summary']['ไม่ผ่าน'] ?? 0) ?></td>
                                    <td><?= array_sum($mainItemReport['summary'] ?? []) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="fw-bold table-secondary">
                                <td class="text-end">รวมผลการประเมินนักเรียน</td>
                                <td><?= esc($overallSummary['ดีเยี่ยม'] ?? 0) ?></td>
                                <td><?= esc($overallSummary['ดี'] ?? 0) ?></td>
                                <td><?= esc($overallSummary['ผ่าน'] ?? 0) ?></td>
                                <td><?= esc($overallSummary['ไม่ผ่าน'] ?? 0) ?></td>
                                <td><?= esc($totalAssessedStudents) ?></td>
                            </tr>
                            <tr class="fw-bold table-secondary">
                                <td class="text-end">คิดเป็นร้อยละ</td>
                                <td><?= number_format((($overallSummary['ดีเยี่ยม'] ?? 0) / $totalAssessedStudents) * 100, 2) ?>%</td>
                                <td><?= number_format((($overallSummary['ดี'] ?? 0) / $totalAssessedStudents) * 100, 2) ?>%</td>
                                <td><?= number_format((($overallSummary['ผ่าน'] ?? 0) / $totalAssessedStudents) * 100, 2) ?>%</td>
                                <td><?= number_format((($overallSummary['ไม่ผ่าน'] ?? 0) / $totalAssessedStudents) * 100, 2) ?>%</td>
                                <td>100.00%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <div>ยังไม่มีนักเรียนที่ได้รับการประเมิน</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Assessment Table Card -->
    <form action="<?= base_url('teacher/desirable_assessment/save_class') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="className" value="<?= esc($className) ?>">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0"><i class="bi bi-pencil-square me-2"></i>ประเมินคุณลักษณะอันพึงประสงค์ - ชั้นเรียน <?= esc($className) ?></h5>
                <div>
                    <a href="<?= base_url('teacher/desirable_assessment') ?>" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left-circle-fill me-1"></i> กลับ</a>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-save-fill me-1"></i> บันทึกข้อมูล</button>
                </div>
            </div>
            <div class="card-body">
                <div id="overflow-alert" class="alert alert-info alert-dismissible fade show" role="alert" style="display: none;">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>คำแนะนำ:</strong> เนื่องจากตารางมีขนาดกว้าง คุณสามารถ 1) ย่อหน้าจอ (Zoom Out) โดยกด <code>Ctrl</code> + <code>-</code> หรือ 2) ซ่อนแถบเมนูโดยกดปุ่ม <i class="bi bi-list"></i> ที่มุมบนซ้าย
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                <?php if (!empty($students) && !empty($assessmentItems)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped assessment-table">
                            <thead class="text-center">
                                <tr>
                                    <th rowspan="4">เลขที่</th>
                                    <th rowspan="4">ชื่อ - นามสกุล</th>
                                    <?php foreach ($assessmentItems as $mainItem): ?>
                                        <th colspan="<?= count($mainItem['sub_items']) + 1 ?>">ข้อที่ <?= $mainItem['item_order'] ?></th>
                                    <?php endforeach; ?>
                                    <th rowspan="4" class="rotated-text final-summary-col">สรุปผลการประเมิน</th>
                                </tr>
                                <tr>
                                    <?php foreach ($assessmentItems as $mainItem): ?>
                                        <th colspan="<?= count($mainItem['sub_items']) + 1 ?>"><?= esc($mainItem['item_name']) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <?php foreach ($assessmentItems as $mainItem): ?>
                                        <th colspan="<?= count($mainItem['sub_items']) ?>">ตัวชี้วัด</th>
                                        <th rowspan="2" class="rotated-text summary-col">ผลการประเมิน</th>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <?php foreach ($assessmentItems as $mainItem): ?>
                                        <?php foreach ($mainItem['sub_items'] as $subItem): ?>
                                            <th>
                                                <div data-bs-toggle="tooltip" title="<?= esc($subItem['item_name']) ?>">
                                                    <?= $mainItem['item_order'] ?>.<?= $subItem['item_order'] ?>
                                                </div>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-xs btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="กรอกคะแนนทั้งหมด" style="padding: 0 5px;">
                                                        <i class="bi bi-caret-down-fill"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item fill-col" href="#" data-subitem-id="<?= $subItem['item_id'] ?>" data-score="3">กรอก 3</a></li>
                                                        <li><a class="dropdown-item fill-col" href="#" data-subitem-id="<?= $subItem['item_id'] ?>" data-score="2">กรอก 2</a></li>
                                                        <li><a class="dropdown-item fill-col" href="#" data-subitem-id="<?= $subItem['item_id'] ?>" data-score="1">กรอก 1</a></li>
                                                        <li><a class="dropdown-item fill-col" href="#" data-subitem-id="<?= $subItem['item_id'] ?>" data-score="0">กรอก 0</a></li>
                                                    </ul>
                                                </div>
                                            </th>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr id="student-row-<?= $student['StudentID'] ?>">
                                        <td><?= esc($student['StudentNumber']) ?></td>
                                        <td class="student-name"><?= esc($student['StudentPrefix'] . $student['StudentFirstName'] . ' ' . $student['StudentLastName']) ?></td>
                                        
                                        <?php foreach ($assessmentItems as $mainItem): ?>
                                            <?php foreach ($mainItem['sub_items'] as $subItem): ?>
                                                <td class="sub-item-col-<?= $subItem['item_id'] ?>">
                                                    <input type="number" 
                                                        name="scores[<?= $student['StudentID'] ?>][<?= $subItem['item_id'] ?>]" 
                                                        value="<?= esc($evaluations[$student['StudentID']][$subItem['item_id']] ?? '') ?>" 
                                                        class="form-control form-control-sm score-input" 
                                                        data-main-item="<?= $mainItem['item_id'] ?>"
                                                        min="0" max="3">
                                                </td>
                                            <?php endforeach; ?>
                                            <td class="summary-col main-item-result" data-main-item-result="<?= $mainItem['item_id'] ?>">
                                                <?= $studentResults[$student['StudentID']]['main_item_numeric_levels'][$mainItem['item_id']] ?? '-' ?>
                                            </td>
                                        <?php endforeach; ?>

                                        <td class="final-summary-col overall-result">
                                            <?= $studentResults[$student['StudentID']]['overall_level'] ?? '-' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center"> 
                        <h4><i class="bi bi-exclamation-triangle-fill me-2"></i> ไม่พบข้อมูลตัวชี้วัด</h4>
                        โปรดตรวจสอบว่าท่านได้เพิ่มข้อมูลคุณลักษณะอันพึงประสงค์และตัวชี้วัดย่อยในฐานข้อมูลแล้วหรือไม่
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save-fill me-1"></i> บันทึกข้อมูล</button>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Enable Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    <?php if (session()->getFlashdata('message')) : ?>
        Swal.fire({ icon: 'success', title: 'สำเร็จ', text: '<?= session()->getFlashdata('message') ?>', timer: 1500, showConfirmButton: false });
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: '<?= session()->getFlashdata('error') ?>' });
    <?php endif; ?>

    const assessmentItems = <?= json_encode($assessmentItems ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    function getQualityLevel(score) {
        if (score >= 2.51) return 'ดีเยี่ยม';
        if (score >= 1.51) return 'ดี';
        if (score >= 1.00) return 'ผ่าน';
        return 'ไม่ผ่าน';
    }

    function getLevelAsNumber(levelText) {
        switch (levelText) {
            case 'ดีเยี่ยม': return 3;
            case 'ดี': return 2;
            case 'ผ่าน': return 1;
            default: return 0;
        }
    }

    function updateRowCalculations(row) {
        let studentTotalScore = 0;
        let totalSubItems = 0;

        assessmentItems.forEach(mainItem => {
            let mainItemId = mainItem.item_id;
            let mainItemScore = 0;
            let subItemCount = mainItem.sub_items.length;
            
            if (subItemCount > 0) {
                totalSubItems += subItemCount;
                row.find(`.score-input[data-main-item="${mainItemId}"]`).each(function() {
                    let score = $(this).val();
                    if ($.isNumeric(score)) {
                        mainItemScore += parseFloat(score);
                    }
                });
                let mainItemAverage = mainItemScore / subItemCount;
                let mainItemLevelText = getQualityLevel(mainItemAverage);
                let mainItemLevelNumber = getLevelAsNumber(mainItemLevelText);
                row.find(`.main-item-result[data-main-item-result="${mainItemId}"]`).text(mainItemLevelNumber);
                studentTotalScore += mainItemScore;
            }
        });

        if (totalSubItems > 0) {
            let studentOverallAverage = studentTotalScore / totalSubItems;
            row.find('.overall-result').text(getQualityLevel(studentOverallAverage));
        }
    }

    $('tbody').on('input', '.score-input', function() {
        if ($(this).val() !== '') {
            $(this).removeClass('is-invalid');
        }

        let scoreVal = parseFloat($(this).val());
        
        if (!isNaN(scoreVal) && (scoreVal > 3 || scoreVal < 0)) {
            $(this).val(''); 
            Toast.fire({
                icon: 'warning',
                title: 'คะแนนต้องอยู่ระหว่าง 0 - 3'
            });
        }

        updateRowCalculations($(this).closest('tr'));
    });

    setTimeout(function() {
        const table = $('.assessment-table');
        if (table.length > 0 && table[0].scrollWidth > table.innerWidth()) {
            $('#overflow-alert').show();
        }
    }, 500);

    $('.assessment-table').on('click', '.fill-col', function(e) {
        e.preventDefault();
        const subItemId = $(this).data('subitem-id');
        const score = $(this).data('score');
        $('td.sub-item-col-' + subItemId).find('.score-input').val(score).trigger('input');
    });

    $('form').on('submit', function(e) {
        let allFilled = true;
        $('.score-input').each(function() {
            if ($(this).val() === '' || $(this).val() === null) {
                allFilled = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!allFilled) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'ข้อมูลไม่ครบถ้วน',
                text: 'กรุณากรอกคะแนนให้ครบทุกช่อง'
            });
        }
    });

    // Initial calculation for all rows
    $('tbody tr[id^="student-row-"]').each(function() {
        updateRowCalculations($(this));
    });
});
</script>
<?= $this->endSection() ?>
