<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ประเมินการอ่านฯ') ?>
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
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
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
    .stat-card:hover {
        transform: translateY(-5px);
    }
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
        border-radius: 0.75rem;
        border: 2px solid #e9ecef;
        padding: 0.5rem;
        text-align: center;
        font-weight: 700;
        transition: all 0.2s;
    }
    .form-control-modern:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.1);
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

<div class="container-fluid py-2">
    <!-- Breadcrumb & Header -->
    <div class="manage-header shadow-sm mt-3">
        <div class="row align-items-center text-start">
            <div class="col-md-7">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-light mb-2">
                        <li class="breadcrumb-item"><a href="<?= site_url('home') ?>" class="text-white opacity-75">หน้าหลัก</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('teacher/reading_assessment') ?>" class="text-white opacity-75">แบบประเมินการอ่านฯ</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">ประเมินชั้นเรียน</li>
                    </ol>
                </nav>
                <h2 class="fw-bold mb-1 text-white">ห้องมัธยมศึกษาปีที่ <?= esc($className) ?></h2>
                <p class="mb-0 text-white text-opacity-75 small mt-2">
                    <i class="bi bi-calendar3 me-1"></i> ประเมินการอ่าน คิดวิเคราะห์ และเขียน ประจำภาคเรียนที่ <?= esc($term) ?>/<?= esc($academicYear) ?>
                </p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0 mx-auto">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <a href="<?= base_url('teacher/reading_assessment/print_report/' . $className) ?>" target="_blank" class="btn btn-white rounded-pill px-4 shadow-sm text-primary fw-bold">
                        <i class="bi bi-printer-fill me-1"></i> พิมพ์รายงาน
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4 text-start">
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-stars"></i>
                        </div>
                        <div>
                            <div class="text-muted small">ดีเยี่ยม</div>
                            <h4 class="fw-bold mb-0"><?= esc($overallQualityCounts['ดีเยี่ยม'] ?? 0) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-hand-thumbs-up-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small">ดี</div>
                            <h4 class="fw-bold mb-0"><?= esc($overallQualityCounts['ดี'] ?? 0) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small">ผ่าน</div>
                            <h4 class="fw-bold mb-0"><?= esc($overallQualityCounts['ผ่าน'] ?? 0) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger me-3">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small">ไม่ผ่าน</div>
                            <h4 class="fw-bold mb-0"><?= esc($overallQualityCounts['ไม่ผ่าน'] ?? 0) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small">นักเรียนทั้งหมด</div>
                            <h4 class="fw-bold mb-0"><?= esc($totalStudents ?? 0) ?> คน</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessment Form -->
    <form action="<?= base_url('teacher/reading_assessment/save_class') ?>" method="post" id="assessment-form">
        <?= csrf_field() ?>
        <input type="hidden" name="className" value="<?= esc($className) ?>">
        
        <div class="card table-card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 text-start">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-pencil-fill me-2"></i>รายชื่อนักเรียนและตัวชี้วัด</h5>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                        <i class="bi bi-save-fill me-2"></i> บันทึกข้อมูลทั้งหมด
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($students)) : ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-start text-nowrap">
                                <tr>
                                    <th class="ps-4" style="width: 80px;">เลขที่</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <?php foreach (($assessmentItems ?? []) as $item) : ?>
                                        <th class="text-center" style="min-width: 100px;">
                                            <div class="smallest text-muted text-uppercase fw-bold mb-1">ข้อ <?= esc($item['ItemID']) ?></div>
                                            <div class="dropdown">
                                                <button class="btn btn-xs btn-outline-secondary rounded-pill dropdown-toggle px-2" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-magic smallest"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 small">
                                                    <li><a class="dropdown-item fill-col" href="#" data-item-id="<?= $item['ItemID'] ?>" data-score="3">กรอก 3 ทั้งหมด</a></li>
                                                    <li><a class="dropdown-item fill-col" href="#" data-item-id="<?= $item['ItemID'] ?>" data-score="2">กรอก 2 ทั้งหมด</a></li>
                                                    <li><a class="dropdown-item fill-col" href="#" data-item-id="<?= $item['ItemID'] ?>" data-score="1">กรอก 1 ทั้งหมด</a></li>
                                                    <li><a class="dropdown-item fill-col" href="#" data-item-id="<?= $item['ItemID'] ?>" data-score="0">กรอก 0 ทั้งหมด</a></li>
                                                </ul>
                                            </div>
                                        </th>
                                    <?php endforeach; ?>
                                    <th class="text-center" style="width: 80px;">รวม</th>
                                    <th class="text-center" style="width: 120px;">คุณภาพ</th>
                                </tr>
                            </thead>
                            <tbody class="text-start">
                                <?php foreach ($students as $student) : ?>
                                    <tr id="student-row-<?= $student['StudentID'] ?>">
                                        <td class="ps-4 fw-bold text-muted"><?= esc($student['StudentNumber']) ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= esc($student['StudentPrefix'] . $student['StudentFirstName'] . ' ' . $student['StudentLastName']) ?></div>
                                            <div class="smallest text-muted">รหัส: <?= esc($student['StudentID']) ?></div>
                                        </td>
                                        <?php foreach (($assessmentItems ?? []) as $item) : ?>
                                            <?php 
                                                $score = $evaluations[$student['StudentID']][$item['ItemID']] ?? '';
                                                $displayScore = is_numeric($score) ? (int)$score : '';
                                            ?>
                                            <td class="text-center item-col-<?= $item['ItemID'] ?>">
                                                <input type="number" 
                                                       name="scores[<?= $student['StudentID'] ?>][<?= $item['ItemID'] ?>]" 
                                                       value="<?= esc($displayScore) ?>" 
                                                       class="form-control-modern form-control-sm score-input mx-auto" 
                                                       style="width: 60px;"
                                                       min="0" 
                                                       max="3">
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="text-center fw-bold text-primary total-score">-</td>
                                        <td class="text-center quality-level">-</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="text-center py-5">
                        <div class="text-muted opacity-50 mb-3"><i class="bi bi-person-dash" style="font-size: 3rem;"></i></div>
                        <p class="mb-0">ไม่พบข้อมูลนักเรียนในชั้นเรียนนี้</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-light py-3 text-end">
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">
                    <i class="bi bi-save-fill me-2"></i> บันทึกข้อมูลทั้งหมด
                </button>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // SweetAlert2
    <?php if (session()->getFlashdata('message')) : ?>
        Swal.fire({
            icon: 'success', title: 'สำเร็จ', text: '<?= session()->getFlashdata('message') ?>',
            confirmButtonText: 'ตกลง', confirmButtonColor: '#6366f1'
        });
    <?php endif; ?>

    const itemCount = <?= count($assessmentItems ?? []) ?>;

    function getQualityBadge(level) {
        const badges = {
            'ดีเยี่ยม': 'bg-success', 'ดี': 'bg-info', 'ผ่าน': 'bg-warning', 'ไม่ผ่าน': 'bg-danger'
        };
        const cls = badges[level] || 'bg-secondary';
        return `<span class="badge badge-status ${cls} bg-opacity-10 text-${cls.replace('bg-', '')}">${level || '-'}</span>`;
    }

    function updateRowCalculations(row) {
        let totalScore = 0;
        let assessedItems = 0;

        row.find('.score-input').each(function() {
            let score = $(this).val();
            if ($.isNumeric(score) && score !== '') {
                let s = Math.min(3, Math.max(0, parseFloat(score)));
                $(this).val(s);
                totalScore += s;
                assessedItems++;
            }
        });

        let qualityLevel = '-';
        if (assessedItems > 0 && itemCount > 0) {
            let avg = totalScore / itemCount;
            if (avg >= 2.51) qualityLevel = 'ดีเยี่ยม';
            else if (avg >= 1.51) qualityLevel = 'ดี';
            else if (avg >= 1.00) qualityLevel = 'ผ่าน';
            else qualityLevel = 'ไม่ผ่าน';
            row.find('.total-score').text(totalScore);
        } else {
            row.find('.total-score').text('-');
        }
        row.find('.quality-level').html(getQualityBadge(qualityLevel));
    }

    $('tbody').on('input', '.score-input', function() { updateRowCalculations($(this).closest('tr')); });

    $('.fill-col').on('click', function(e) {
        e.preventDefault();
        const itemId = $(this).data('item-id');
        const score = $(this).data('score');
        $('td.item-col-' + itemId).find('.score-input').each(function() {
            $(this).val(score).trigger('input');
        });
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
                confirmButtonColor: '#6366f1' 
            }).then(() => {
                btn.prop('disabled', false).html(originalHtml);
            });
            btn.prop('disabled', false).html(originalHtml);
        } else {
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> กำลังบันทึก...');
        }
    });

    // Initial calculation for all rows with small delay to allow loader to show
    setTimeout(function() {
        $('tbody tr').each(function() { updateRowCalculations($(this)); });
        
        // Hide loader after all calculations are done
        $('#page-loader').css('opacity', '0');
        setTimeout(() => $('#page-loader').remove(), 500);
    }, 200);
});
</script>
<?= $this->endSection() ?>