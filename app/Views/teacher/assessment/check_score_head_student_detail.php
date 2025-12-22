<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .header-card {
        background: white;
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }
    .score-table th {
        background-color: #f8f9fa;
        text-wrap: nowrap;
        font-size: 0.85rem;
        text-transform: uppercase;
        color: #566a7f;
        border-top: none;
    }
    .score-table td {
        vertical-align: middle;
    }
    .student-number {
        width: 50px;
        font-weight: bold;
        color: #696cff;
    }
    .score-cell {
        width: 100px;
        text-align: center;
        font-weight: 600;
    }
    .badge-grade {
        font-size: 0.9rem;
        width: 45px;
    }
    .btn-back {
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
</style>

<div class="container-xxl flex-grow-1">
    <div class="card header-card">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <a href="<?= site_url('assessment-head/check-score-detail/' . $teacher_info->pers_id . '/' . $year_term) ?>" class="btn btn-label-secondary btn-back me-3">
                    <i class="bi bi-chevron-left"></i>
                </a>
                <div>
                    <h5 class="mb-0 fw-bold"><?= esc($subject_info->SubjectCode) ?> - <?= esc($subject_info->SubjectName) ?></h5>
                    <small class="text-muted">ชั้น <?= esc($class) ?> | ครูผู้สอน: <?= esc($teacher_info->pers_prefix . $teacher_info->pers_firstname . ' ' . $teacher_info->pers_lastname) ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover score-table mb-0">
                <thead>
                    <tr>
                        <th class="text-center">ที่</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th class="text-center">ก่อนกลางภาค</th>
                        <th class="text-center">กลางภาค</th>
                        <th class="text-center">หลังกลางภาค</th>
                        <th class="text-center">ปลายภาค</th>
                        <th class="text-center">รวม</th>
                        <th class="text-center">เกรด</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <p class="text-muted mb-0">ไม่พบข้อมูลนักเรียน</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                            <?php 
                                $scores = explode('|', $student->Score100);
                                $p1 = $scores[0] ?? '-';
                                $p2 = $scores[1] ?? '-';
                                $p3 = $scores[2] ?? '-';
                                $p4 = $scores[3] ?? '-';
                                
                                // Calculate total
                                $total = 0;
                                foreach($scores as $s) {
                                    if(is_numeric($s)) $total += $s;
                                }

                                $grade_color = 'secondary';
                                if($student->Grade != "") {
                                    if($student->Grade >= 1) $grade_color = 'success';
                                    elseif($student->Grade == 0) $grade_color = 'danger';
                                    else $grade_color = 'warning'; // สำหรับ ร, มส
                                }
                            ?>
                            <tr>
                                <td class="text-center student-number"><?= esc($student->StudentNumber) ?></td>
                                <td>
                                    <div class="fw-bold"><?= esc($student->StudentPrefix . $student->StudentFirstName . ' ' . $student->StudentLastName) ?></div>
                                    <small class="text-muted"><?= esc($student->StudentID) ?></small>
                                </td>
                                <td class="text-center score-cell"><?= $p1 !== '' ? $p1 : '-' ?></td>
                                <td class="text-center score-cell"><?= $p2 !== '' ? $p2 : '-' ?></td>
                                <td class="text-center score-cell"><?= $p3 !== '' ? $p3 : '-' ?></td>
                                <td class="text-center score-cell"><?= $p4 !== '' ? $p4 : '-' ?></td>
                                <td class="text-center fw-bold text-primary fs-5"><?= ($student->Grade != "") ? $total : '-' ?></td>
                                <td class="text-center">
                                    <?php if($student->Grade != ""): ?>
                                        <span class="badge bg-<?= $grade_color ?> badge-grade"><?= esc($student->Grade) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
