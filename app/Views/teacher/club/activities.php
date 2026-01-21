<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'รายงานสรุปผลกิจกรรม') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .activities-header {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        border-radius: 1.25rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .activities-header::after {
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
    .report-card {
        border-radius: 1.25rem;
        border: none;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 2rem;
        background: #fff;
    }
    .table-responsive {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f8fafc;
    }
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 10px;
    }
    .summary-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 0.6rem;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .badge-pass { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-fail { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    
    .sticky-col {
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 5;
    }
    .sticky-header th {
        position: sticky;
        top: 0;
        background: #f8fafc !important;
        z-index: 10;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    .month-header {
        background: rgba(99, 102, 241, 0.05) !important;
        color: #4338ca;
        font-weight: 700;
        font-size: 0.85rem;
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
    <div class="activities-header shadow-lg">
        <div class="row align-items-center text-start">
            <div class="col-lg-7">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-light mb-2">
                        <li class="breadcrumb-item"><a href="<?= site_url('home') ?>" class="text-white opacity-75">หน้าหลัก</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('club') ?>" class="text-white opacity-75">ชุมนุม</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">รายงานสรุปผล</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold text-white mb-2">
                    <i class="bi bi-bar-chart-line-fill me-2"></i>รายงานสรุปผลกิจกรรม
                </h1>
                <p class="lead mb-0 text-white opacity-75 small">
                    <i class="bi bi-tag-fill me-1"></i> <?= esc($club->club_name) ?>
                </p>
            </div>
            <div class="col-lg-5 mt-4 mt-lg-0">
                <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                    <a href="<?= site_url('club/printActivitiesReport/' . $club->club_id) ?>" target="_blank" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-bold border-2 border-white">
                        <i class="bi bi-printer-fill me-2"></i> พิมพ์รายงาน PDF
                    </a>
                    
                    <div class="btn-group shadow-sm rounded-pill overflow-hidden bg-white p-1">
                        <a href="<?= site_url('club/manual') ?>" class="btn btn-white border-0 rounded-pill px-3 py-2 text-primary fw-bold small">
                            <i class="bi bi-book-half me-2"></i> คู่มือ
                        </a>
                        <button type="button" class="btn btn-white border-0 rounded-pill px-3 py-2 text-muted" data-bs-toggle="modal" data-bs-target="#clubHelpModal">
                            <i class="bi bi-question-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Part 1: Attendance Report -->
    <div class="report-card card shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-calendar-check text-primary me-2"></i>1. รายงานผลการบันทึกเวลาเรียน
            </h5>
            <div class="text-muted smallest">เกณฑ์การผ่าน: เข้าเรียนไม่น้อยกว่า 80%</div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light sticky-header">
                        <?php
                            $thaiMonths = [
                                'January' => 'ม.ค.', 'February' => 'ก.พ.', 'March' => 'มี.ค.',
                                'April' => 'เม.ย.', 'May' => 'พ.ค.', 'June' => 'มิ.ย.',
                                'July' => 'ก.ค.', 'August' => 'ส.ค.', 'September' => 'ก.ย.',
                                'October' => 'ต.ค.', 'November' => 'พ.ย.', 'December' => 'ธ.ค.',
                            ];
                        ?>
                        <tr class="text-center">
                            <th rowspan="2" class="align-middle py-3" style="width: 60px;">เลขที่</th>
                            <th rowspan="2" class="align-middle py-3 text-start" style="min-width: 200px;">ชื่อ - นามสกุล</th>
                            <th rowspan="2" class="align-middle py-3" style="width: 80px;">ชั้น</th>
                            <?php foreach ($schedulesByMonth as $month => $schedulesInMonth): ?>
                                <?php
                                    $englishMonthName = date('F', strtotime($month));
                                    $thaiMonthName = $thaiMonths[$englishMonthName] ?? $englishMonthName;
                                    $year = date('Y', strtotime($month));
                                ?>
                                <th colspan="<?= count($schedulesInMonth) ?>" class="month-header py-2"><?= $thaiMonthName . ' ' . ($year + 543) ?></th>
                            <?php endforeach; ?>
                            <th rowspan="2" class="align-middle py-3" style="width: 80px;">รวม</th>
                            <th rowspan="2" class="align-middle py-3" style="width: 100px;">ผลเวลาเรียน</th>
                        </tr>
                        <tr class="text-center">
                            <?php foreach ($schedulesByMonth as $month => $schedulesInMonth): ?>
                                <?php foreach ($schedulesInMonth as $schedule): ?>
                                    <th class="py-2 smallest fw-bold" style="min-width: 40px;"><?= date('d', strtotime($schedule->tcs_start_date)) ?></th>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $totalDisplayedSchedules = 0;
                            if (!empty($schedulesByMonth)) {
                                foreach ($schedulesByMonth as $schedulesInMonth) {
                                    $totalDisplayedSchedules += count($schedulesInMonth);
                                }
                            }
                        ?>
                        <?php if (!empty($members)): ?>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td class="text-center text-muted fw-bold"><?= esc($member->StudentNumber) ?></td>
                                    <td class="text-start">
                                        <div class="fw-bold text-dark"><?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?></div>
                                        <div class="smallest text-muted">ID: <?= esc($member->StudentCode ?? $member->StudentID) ?></div>
                                    </td>
                                    <td class="text-center"><?= esc($member->StudentClass) ?></td>
                                    <?php 
                                        $totalPresent = 0;
                                        foreach ($schedulesByMonth as $month => $schedulesInMonth):
                                            foreach ($schedulesInMonth as $schedule):
                                                $status = $attendanceMap[$member->StudentID][$schedule->tcs_schedule_id] ?? '-';
                                                if ($status === 'มา') {
                                                    $totalPresent++;
                                                }
                                    ?>
                                        <td class="text-center">
                                            <?php if ($status === 'มา'): ?>
                                                <i class="bi bi-check-circle-fill text-success" style="font-size: 1.1rem;"></i>
                                            <?php elseif ($status === 'ขาด'): ?>
                                                <i class="bi bi-x-circle-fill text-danger" style="font-size: 1.1rem;"></i>
                                            <?php else: ?>
                                                <span class="text-muted fs-4">-</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php 
                                            endforeach;
                                        endforeach; 
                                    ?>
                                    <td class="text-center fw-bold text-primary"><?= $totalPresent ?></td>
                                    <td class="text-center">
                                        <?php
                                            if ($totalDisplayedSchedules > 0) {
                                                $percentage = ($totalPresent / $totalDisplayedSchedules) * 100;
                                                $isPass = ($percentage >= 80);
                                                echo '<span class="summary-badge ' . ($isPass ? 'badge-pass' : 'badge-fail') . '">' . ($isPass ? 'ผ' : 'มผ') . '</span>';
                                            } else {
                                                echo '<span class="text-muted">-</span>';
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= 5 + ($totalDisplayedSchedules ?? 0) ?>" class="text-center py-5">
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

    <!-- Part 2: Objectives Report -->
    <div class="report-card card shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-journal-check text-primary me-2"></i>2. รายงานผลการประเมินตามจุดประสงค์
            </h5>
            <div class="text-muted smallest">เกณฑ์การผ่าน: ผ่านครบทุกจุดประสงค์</div>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($club_objectives)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-center">
                                <th rowspan="2" class="align-middle py-3" style="width: 60px;">เลขที่</th>
                                <th rowspan="2" class="align-middle py-3 text-start" style="min-width: 200px;">ชื่อ - นามสกุล</th>
                                <th colspan="<?= count($club_objectives) ?>" class="py-2 bg-indigo bg-opacity-10">จุดประสงค์ที่</th>
                                <th rowspan="2" class="align-middle py-3" style="width: 100px;">รวมผ่าน</th>
                                <th rowspan="2" class="align-middle py-3" style="width: 100px;">ผลการประเมิน</th>
                            </tr>
                            <tr class="text-center">
                                <?php foreach ($club_objectives as $objective): ?>
                                    <th class="py-2" style="width: 50px;">
                                        <div class="badge rounded-pill bg-primary" title="<?= esc($objective->objective_name) ?>">
                                            <?= esc($objective->objective_order) ?>
                                        </div>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td class="text-center text-muted fw-bold"><?= esc($member->StudentNumber) ?></td>
                                    <td class="text-start">
                                        <div class="fw-bold text-dark"><?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?></div>
                                        <div class="smallest text-muted">ID: <?= esc($member->StudentCode ?? $member->StudentID) ?></div>
                                    </td>
                                    <?php
                                        $totalPassed = 0;
                                        foreach ($club_objectives as $objective):
                                            $hasPassed = $objectiveProgressMap[$member->StudentID][$objective->objective_id] ?? false;
                                            if ($hasPassed) $totalPassed++;
                                    ?>
                                        <td class="text-center">
                                            <?php if ($hasPassed): ?>
                                                <i class="bi bi-check-circle-fill text-success" style="font-size: 1.1rem;"></i>
                                            <?php else: ?>
                                                <i class="bi bi-x-circle-fill text-danger" style="font-size: 1.1rem;"></i>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="text-center fw-bold text-primary"><?= $totalPassed ?></td>
                                    <td class="text-center">
                                        <?php
                                            $totalObjectivesCount = count($club_objectives);
                                            $isPass = ($totalPassed === $totalObjectivesCount);
                                            echo '<span class="summary-badge ' . ($isPass ? 'badge-pass' : 'badge-fail') . '">' . ($isPass ? 'ผ' : 'มผ') . '</span>';
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-5 text-center">
                    <i class="bi bi-journal-x text-muted opacity-25" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3 mb-0">ยังไม่มีการกำหนดจุดประสงค์สำหรับชุมนุมนี้</p>
                    <a href="<?= site_url('club/objectives/' . $club->club_id) ?>" class="btn btn-link btn-sm mt-2">ไปที่หน้าประเมินจุดประสงค์</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const helpModal = document.getElementById('clubHelpModal');
        if (helpModal) {
            helpModal.addEventListener('show.bs.modal', function () {
                setTimeout(() => {
                    const tabEl = document.querySelector('#pills-report-tab');
                    if (tabEl) new bootstrap.Tab(tabEl).show();
                }, 300);
            });
        }
    });
</script>
<?= $this->endSection() ?>