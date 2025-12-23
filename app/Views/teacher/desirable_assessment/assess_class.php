<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ประเมินคุณลักษณะฯ') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    /* Hide spinner arrows on number inputs */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }

    .manage-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
        border-radius: 1rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .manage-header::after {
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
    .stat-card {
        border: none;
        border-radius: 1rem;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .table-card {
        border-radius: 1rem;
        border: none;
        overflow: hidden;
    }
    .badge-status {
        padding: 0.4rem 0.8rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .form-control-modern {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 0.25rem;
        text-align: center;
        font-weight: 700;
        transition: all 0.2s;
        width: 45px;
    }
    .form-control-modern:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 0.25rem rgba(14, 165, 233, 0.1);
    }
    .rotated-text {
        writing-mode: vertical-rl;
        text-orientation: mixed;
        transform: rotate(180deg);
        white-space: nowrap;
        font-size: 0.75rem;
        padding: 10px 5px !important;
    }
    .main-item-header {
        background: rgba(14, 165, 233, 0.05);
        font-size: 0.8rem;
        font-weight: 700;
    }
    .sub-item-header {
        font-size: 0.7rem;
        color: #64748b;
    }
    .summary-col {
        background-color: rgba(99, 102, 241, 0.05);
        font-weight: bold;
        color: #4f46e5;
    }
    .final-summary-col {
        background-color: rgba(14, 165, 233, 0.1);
        font-weight: bold;
    }

    /* Page Loader Styles */
    #page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(5px);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        transition: opacity 0.5s ease;
    }
    .loader-content {
        text-align: center;
        background: white;
        padding: 2.5rem;
        border-radius: 2rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
    }
</style>

<!-- Page Loader -->
<div id="page-loader">
    <div class="loader-content">
        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <h5 class="fw-bold mb-1">กำลังประมวลผลข้อมูล...</h5>
        <p class="text-muted small mb-0">โปรดรอสักครู่ ระบบกำลังจัดเตรียมข้อมูลชุดใหญ่สำหรับคุณ</p>
    </div>
</div>

<div class="container-fluid py-2 text-start">
    <!-- Breadcrumb & Header -->
    <div class="manage-header shadow-sm mt-3">
        <div class="row align-items-center">
            <div class="col-md-7">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-light mb-2">
                        <li class="breadcrumb-item"><a href="<?= site_url('home') ?>" class="text-white opacity-75">หน้าหลัก</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('teacher/desirable_assessment') ?>" class="text-white opacity-75">แบบประเมินคุณลักษณะฯ</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">บันทึกผลการประเมิน</li>
                    </ol>
                </nav>
                <h2 class="fw-bold mb-1 text-white">ห้องมัธยมศึกษาปีที่ <?= esc($className) ?></h2>
                <p class="mb-0 text-white text-opacity-75 small mt-2">
                    <i class="bi bi-star-fill me-1"></i> ประเมินคุณลักษณะอันพึงประสงค์ 8 ประการ ประจำปีการศึกษา <?= esc($term) ?>/<?= esc($academicYear) ?>
                </p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0 mx-auto">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <a href="<?= base_url('teacher/desirable_assessment/print_report/' . $className) ?>" target="_blank" class="btn btn-white rounded-pill px-4 shadow-sm text-primary fw-bold">
                        <i class="bi bi-printer-fill me-1"></i> พิมพ์รายงานสรุป
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-success bg-opacity-10 text-success me-2">
                            <i class="bi bi-stars"></i>
                        </div>
                        <div>
                            <div class="text-muted smallest fw-bold text-uppercase">ดีเยี่ยม</div>
                            <h4 class="fw-bold mb-0"><?= esc($overallSummary['ดีเยี่ยม'] ?? 0) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-info bg-opacity-10 text-info me-2">
                            <i class="bi bi-hand-thumbs-up-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted smallest fw-bold text-uppercase">ดี</div>
                            <h4 class="fw-bold mb-0"><?= esc($overallSummary['ดี'] ?? 0) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning me-2">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted smallest fw-bold text-uppercase">ผ่าน</div>
                            <h4 class="fw-bold mb-0"><?= esc($overallSummary['ผ่าน'] ?? 0) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger me-2">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted smallest fw-bold text-uppercase">ไม่ผ่าน</div>
                            <h4 class="fw-bold mb-0"><?= esc($overallSummary['ไม่ผ่าน'] ?? 0) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm h-100 bg-primary bg-opacity-10 border-primary border-opacity-25 border border-dashed">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-primary smallest fw-bold text-uppercase">ความคืบหน้าการประเมิน</div>
                        <h4 class="fw-bold mb-0 text-primary"><?= $totalAssessedStudents ?> / <?= $totalStudents ?> <span class="small fw-normal">คน</span></h4>
                    </div>
                    <div class="text-primary opacity-50"><i class="bi bi-people-fill fs-1"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessment Form -->
    <form action="<?= base_url('teacher/desirable_assessment/save_class') ?>" method="post" id="assessment-form">
        <?= csrf_field() ?>
        <input type="hidden" name="className" value="<?= esc($className) ?>">
        
        <div class="card table-card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-pencil-square me-2"></i>รายชื่อนักเรียนและตัวชี้วัดย่อย</h5>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('teacher/desirable_assessment') ?>" class="btn btn-light rounded-pill px-4 fw-bold">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                            <i class="bi bi-save-fill me-2"></i> บันทึกข้อมูลทั้งหมด
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($students) && !empty($assessmentItems)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0 text-center">
                            <thead>
                                <tr class="bg-light">
                                    <th rowspan="4" class="ps-3" style="min-width: 60px;">เลขที่</th>
                                    <th rowspan="4" class="text-start" style="min-width: 200px;">ชื่อ-นามสกุล</th>
                                    <?php foreach ($assessmentItems as $mainItem): ?>
                                        <th colspan="<?= count($mainItem['sub_items']) + 1 ?>" class="main-item-header">
                                            ข้อที่ <?= $mainItem['item_order'] ?>
                                        </th>
                                    <?php endforeach; ?>
                                    <th rowspan="4" class="rotated-text final-summary-col">สรุปผลภาพรวม</th>
                                </tr>
                                <tr class="bg-light">
                                    <?php foreach ($assessmentItems as $mainItem): ?>
                                        <th colspan="<?= count($mainItem['sub_items']) + 1 ?>" class="small fw-bold">
                                            <span data-bs-toggle="tooltip" title="<?= esc($mainItem['item_name']) ?>">
                                                <?= mb_strimwidth($mainItem['item_name'], 0, 20, "...") ?>
                                            </span>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <?php foreach ($assessmentItems as $mainItem): ?>
                                        <th colspan="<?= count($mainItem['sub_items']) ?>" class="smallest text-muted text-uppercase">ตัวชี้วัด</th>
                                        <th rowspan="2" class="rotated-text summary-col">ผลประเมิน</th>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <?php foreach ($assessmentItems as $mainItem): ?>
                                        <?php foreach ($mainItem['sub_items'] as $subItem): ?>
                                            <th class="sub-item-header">
                                                <div data-bs-toggle="tooltip" title="<?= esc($subItem['item_name']) ?>">
                                                    <?= $mainItem['item_order'] ?>.<?= $subItem['item_order'] ?>
                                                </div>
                                                <div class="dropdown mt-1">
                                                    <button class="btn btn-xs btn-link p-0 text-decoration-none" type="button" data-bs-toggle="dropdown">
                                                        <i class="bi bi-magic smallest"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 small">
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
                                        <td class="fw-bold text-muted"><?= esc($student['StudentNumber']) ?></td>
                                        <td class="text-start">
                                            <div class="fw-bold text-dark smallest-mobile"><?= esc($student['StudentPrefix'] . $student['StudentFirstName'] . ' ' . $student['StudentLastName']) ?></div>
                                        </td>
                                        
                                        <?php foreach ($assessmentItems as $mainItem): ?>
                                            <?php foreach ($mainItem['sub_items'] as $subItem): ?>
                                                <td class="sub-item-col-<?= $subItem['item_id'] ?>">
                                                    <input type="number" 
                                                        name="scores[<?= $student['StudentID'] ?>][<?= $subItem['item_id'] ?>]" 
                                                        value="<?= esc($evaluations[$student['StudentID']][$subItem['item_id']] ?? '') ?>" 
                                                        class="form-control-modern score-input mx-auto" 
                                                        data-main-item="<?= $mainItem['item_id'] ?>"
                                                        min="0" max="3">
                                                </td>
                                            <?php endforeach; ?>
                                            <td class="summary-col main-item-result fw-bold" data-main-item-result="<?= $mainItem['item_id'] ?>">
                                                <?= $studentResults[$student['StudentID']]['main_item_numeric_levels'][$mainItem['item_id']] ?? '-' ?>
                                            </td>
                                        <?php endforeach; ?>

                                        <td class="final-summary-col overall-result fw-bold text-primary smallest">
                                            <?= $studentResults[$student['StudentID']]['overall_level'] ?? '-' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="text-muted opacity-50 mb-3"><i class="bi bi-clipboard-x" style="font-size: 3rem;"></i></div>
                        <p class="mb-0">ไม่พบข้อมูลตัวชี้วัดหรือรายชื่อนักเรียน</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-light py-3 border-0 text-end">
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">
                    <i class="bi bi-save-fill me-2"></i> บันทึกข้อมูล ทั้งหมด
                </button>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Flash Messages
    <?php if (session()->getFlashdata('message')) : ?>
        Swal.fire({ 
            icon: 'success', title: 'สำเร็จ!', text: '<?= session()->getFlashdata('message') ?>', 
            confirmButtonColor: '#0ea5e9'
        });
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        Swal.fire({ 
            icon: 'error', title: 'เกิดข้อผิดพลาด', text: '<?= session()->getFlashdata('error') ?>',
            confirmButtonColor: '#ef4444'
        });
    <?php endif; ?>

    const assessmentItems = <?= json_encode($assessmentItems ?? []) ?>;

    const Toast = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
    });

    function getQualityLevel(score) {
        if (score >= 2.51) return 'ดีเยี่ยม';
        if (score >= 1.51) return 'ดี';
        if (score >= 1.00) return 'ผ่าน';
        return 'ไม่ผ่าน';
    }

    function getLevelAsNumber(levelText) {
        const levels = { 'ดีเยี่ยม': 3, 'ดี': 2, 'ผ่าน': 1 };
        return levels[levelText] || 0;
    }

    function updateRowCalculations(row) {
        let studentTotalScore = 0;
        let totalSubItems = 0;

        assessmentItems.forEach(mainItem => {
            let mId = mainItem.item_id;
            let mScore = 0;
            let subCount = mainItem.sub_items.length;
            
            if (subCount > 0) {
                totalSubItems += subCount;
                row.find(`.score-input[data-main-item="${mId}"]`).each(function() {
                    let s = $(this).val();
                    if ($.isNumeric(s)) mScore += parseFloat(s);
                });
                let avg = mScore / subCount;
                let lvlTxt = getQualityLevel(avg);
                row.find(`.main-item-result[data-main-item-result="${mId}"]`).text(getLevelAsNumber(lvlTxt));
                studentTotalScore += mScore;
            }
        });

        if (totalSubItems > 0) {
            let overallAvg = studentTotalScore / totalSubItems;
            row.find('.overall-result').text(getQualityLevel(overallAvg));
        }
    }

    $('tbody').on('input', '.score-input', function() {
        let sVal = parseFloat($(this).val());
        if (!isNaN(sVal) && (sVal > 3 || sVal < 0)) {
            $(this).val('');
            Toast.fire({ icon: 'warning', title: 'คะแนนต้องอยู่ระหว่าง 0 - 3' });
        }
        updateRowCalculations($(this).closest('tr'));
    });

    $('.assessment-table, .modern-table, table').on('click', '.fill-col', function(e) {
        e.preventDefault();
        const id = $(this).data('subitem-id');
        const sc = $(this).data('score');
        $('td.sub-item-col-' + id).find('.score-input').val(sc).trigger('input');
    });

    $('#assessment-form').on('submit', function(e) {
        let ok = true;
        let btn = $(this).find('button[type="submit"]');
        let originalHtml = btn.html();

        $('.score-input').each(function() {
            if ($(this).val() === '' || $(this).val() === null) {
                ok = false;
                $(this).addClass('border-danger');
            } else {
                $(this).removeClass('border-danger');
            }
        });

        if (!ok) {
            e.preventDefault();
            Swal.fire({ 
                icon: 'error', 
                title: 'กรอกข้อมูลไม่ครบ', 
                text: 'กรุณากรอกคะแนนให้ครบทุกช่องก่อนบันทึก',
                confirmButtonColor: '#0ea5e9' 
            }).then(() => {
                btn.prop('disabled', false).html(originalHtml);
            });
            // Immediately restore just in case
            btn.prop('disabled', false).html(originalHtml);
        } else {
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> กำลังบันทึก...');
        }
    });

    // Initial calculation for all rows with small delay to allow loader to show
    setTimeout(function() {
        $('tbody tr[id^="student-row-"]').each(function() { 
            updateRowCalculations($(this)); 
        });
        
        // Hide loader after all calculations are done (Increase delay to make it visible)
        $('#page-loader').css('opacity', '0');
        setTimeout(() => $('#page-loader').remove(), 600);
    }, 500);
});
</script>
<?= $this->endSection() ?>
