<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ข้อมูลตารางสอนรายบุคคล') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .kpi-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 0.75rem;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.08) !important;
    }
    .doc-preview-sheet {
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 0.75rem;
        padding: 30px 35px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        max-width: 960px;
        margin: 0 auto;
    }
    .official-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.92rem;
    }
    .official-table th, 
    .official-table td {
        border: 1px solid #212529 !important;
        padding: 6px 8px;
        vertical-align: middle;
    }
    .official-table thead th {
        background-color: #f1f5f9 !important;
        font-weight: 700;
        text-align: center;
        color: #1e293b;
    }
    .official-table tbody tr.summary-row td {
        background-color: #f8fafc !important;
        font-weight: 700;
    }
    .grand-total-banner {
        border: 1px solid #212529;
        background-color: #f8fafc;
        padding: 8px 16px;
        font-size: 1.05rem;
        font-weight: 700;
        text-align: right;
        margin: 12px 0 16px 0;
        border-radius: 4px;
    }
    .duty-list-box {
        margin: 14px 0 20px 0;
    }
    .duty-list-box ol {
        margin: 0;
        padding-left: 26px;
        font-size: 0.95rem;
    }
    .duty-list-box li {
        margin-bottom: 4px;
        color: #1e293b;
    }
    /* Stacked signatures line-by-line */
    .signatures-box {
        margin-top: 30px;
        margin-left: auto;
        width: 440px;
        max-width: 100%;
        display: flex;
        flex-direction: column;
        gap: 16px;
        font-size: 0.95rem;
    }
    .sig-row-item {
        display: flex;
        align-items: flex-end;
        white-space: nowrap;
    }
    .sig-dots-line {
        flex: 1;
        border-bottom: 1px dotted #333;
        margin: 0 10px 4px 6px;
        height: 1px;
    }
    .sig-role-text {
        width: 170px;
        text-align: left;
        white-space: nowrap;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-1">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('curriculum/teaching-schedule/' . $year . '/' . $term) ?>">
                            <i class="bi bi-calendar3 me-1"></i>จัดตารางสอนของกลุ่มสาระ
                        </a>
                    </li>
                    <li class="breadcrumb-item active">ข้อมูลรายบุคคล</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-dark">
                <?= esc($teacher->pers_prefix . $teacher->pers_firstname . ' ' . $teacher->pers_lastname) ?>
                <span class="badge bg-label-primary fs-6 ms-2"><?= esc($learning_name) ?></span>
            </h4>
        </div>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            <!-- Quick Teacher Switcher -->
            <?php if (!empty($teachers)): ?>
                <?php 
                    $hasCurrent = false;
                    foreach ($teachers as $t) {
                        $tId = is_array($t) ? ($t['pers_id'] ?? '') : ($t->pers_id ?? '');
                        if ($tId == $teacher->pers_id) {
                            $hasCurrent = true;
                            break;
                        }
                    }
                ?>
                <div class="d-flex align-items-center gap-1">
                    <label class="form-label mb-0 text-muted small text-nowrap d-none d-sm-inline">สลับครูผู้สอน:</label>
                    <select class="form-select form-select-sm" style="min-width: 190px;" onchange="if(this.value) window.location.href=this.value;">
                        <?php if (!$hasCurrent): ?>
                            <option value="<?= base_url('curriculum/teaching-schedule/teacher/' . $teacher->pers_id . '/' . $year . '/' . $term) ?>" selected>
                                <?= esc(trim(($teacher->pers_prefix ?? '') . ($teacher->pers_firstname ?? '') . ' ' . ($teacher->pers_lastname ?? ''))) ?>
                            </option>
                        <?php endif; ?>
                        <?php foreach ($teachers as $t): ?>
                            <?php 
                                $tId = is_array($t) ? ($t['pers_id'] ?? '') : ($t->pers_id ?? '');
                                $tPrefix = is_array($t) ? ($t['pers_prefix'] ?? '') : ($t->pers_prefix ?? '');
                                $tFirst = is_array($t) ? ($t['pers_firstname'] ?? '') : ($t->pers_firstname ?? '');
                                $tLast = is_array($t) ? ($t['pers_lastname'] ?? '') : ($t->pers_lastname ?? '');
                                $tName = trim($tPrefix . $tFirst . ' ' . $tLast);
                                $tUrl = base_url('curriculum/teaching-schedule/teacher/' . $tId . '/' . $year . '/' . $term);
                                $isSelected = ($tId == $teacher->pers_id);
                            ?>
                            <option value="<?= $tUrl ?>" <?= $isSelected ? 'selected' : '' ?>>
                                <?= esc($tName) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Print Button -->
            <a href="<?= base_url('curriculum/teaching-schedule/print/' . $teacher->pers_id . '/' . $year . '/' . $term) ?>" target="_blank" class="btn btn-sm btn-primary d-inline-flex align-items-center">
                <i class="bi bi-printer me-1"></i>พิมพ์ตารางสอน (A4)
            </a>

            <!-- Back to Main Schedule -->
            <a href="<?= base_url('curriculum/teaching-schedule/' . $year . '/' . $term) ?>" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                <i class="bi bi-arrow-left me-1"></i>กลับหน้ารวม
            </a>
        </div>
    </div>

    <!-- KPI Summary Stat Cards -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Subjects Count -->
        <div class="col-sm-6 col-lg-3">
            <div class="card kpi-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-primary rounded-3 me-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-book-half fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">รายวิชาที่สอน</div>
                            <div class="fs-4 fw-bold text-dark"><?= count($grouped_subjects) ?> <span class="fs-6 fw-normal text-muted">วิชา</span></div>
                            <div class="small text-muted">
                                พื้นฐาน <?= $basic_subject_count ?> | เพิ่มเติม <?= $additional_subject_count ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Credits -->
        <div class="col-sm-6 col-lg-2">
            <div class="card kpi-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-warning rounded-3 me-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-award fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">หน่วยกิตรวม</div>
                            <div class="fs-4 fw-bold text-dark"><?= number_format($total_credit, 1) ?></div>
                            <div class="small text-muted">หน่วยกิต</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Academic Hours -->
        <div class="col-sm-6 col-lg-2">
            <div class="card kpi-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-info rounded-3 me-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-clock fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">ชม.วิชาสอน</div>
                            <div class="fs-4 fw-bold text-dark"><?= $total_subject_weekly_hours ?></div>
                            <div class="small text-muted">ชม./สัปดาห์</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Activity Hours -->
        <div class="col-sm-6 col-lg-2">
            <div class="card kpi-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-secondary rounded-3 me-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-flag fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">ชม.กิจกรรม</div>
                            <div class="fs-4 fw-bold text-dark"><?= $total_activity_weekly_hours ?></div>
                            <div class="small text-muted">ชม./สัปดาห์</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 5: Grand Total Hours (Highlighted) -->
        <div class="col-sm-6 col-lg-3">
            <div class="card kpi-card bg-primary text-white border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-white text-primary rounded-3 me-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                        <div>
                            <div class="text-white-50 small">รวมภาระงานทั้งสิ้น</div>
                            <div class="fs-3 fw-bold text-white"><?= $grand_total_weekly_hours ?> <span class="fs-6 fw-normal text-white-50">ชม./สัปดาห์</span></div>
                            <div class="small text-white-50">วิชาสอน <?= $total_subject_weekly_hours ?> + กิจกรรม <?= $total_activity_weekly_hours ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Official Document Preview Card (แบบเดียวกับหน้าพิมพ์) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                <h5 class="mb-0 fw-bold text-dark">แบบแสดงข้อมูลการจัดตารางสอนและภาระงานรายบุคคล</h5>
            </div>
            <div>
                <a href="<?= base_url('curriculum/teaching-schedule/print/' . $teacher->pers_id . '/' . $year . '/' . $term) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-box-arrow-up-right me-1"></i>เปิดโหมดพิมพ์เต็มจอ
                </a>
            </div>
        </div>

        <div class="card-body p-3 p-md-4 bg-light">
            <div class="doc-preview-sheet">
                
                <!-- Document Header -->
                <div class="text-center mb-4">
                    <h4 class="fw-bold text-dark mb-1">ข้อมูลการจัดตารางสอนรายบุคคล</h4>
                    <div class="fs-6 fw-bold text-secondary mb-2"><?= esc(!empty($school->SchoolName) ? $school->SchoolName : 'โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์') ?></div>
                    <div class="d-flex justify-content-center flex-wrap gap-3 text-dark">
                        <span><strong>ชื่อ</strong> <?= esc($teacher->pers_prefix . $teacher->pers_firstname . ' ' . $teacher->pers_lastname) ?></span>
                        <span><strong>กลุ่มสาระการเรียนรู้</strong> <?= esc($learning_name) ?></span>
                    </div>
                    <div class="text-dark mt-1">
                        <span><strong>ภาคเรียนที่</strong> <?= esc($term) ?> <strong>ปีการศึกษา</strong> <?= esc($year) ?></span>
                    </div>
                </div>

                <!-- Table 1: รายวิชาที่สอน -->
                <div class="fw-bold text-dark mb-2 d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-journal-text me-1 text-primary"></i>รายวิชาที่สอน</span>
                    <span class="badge bg-label-primary rounded-pill small"><?= count($grouped_subjects) ?> รายการ</span>
                </div>

                <div class="table-responsive mb-3">
                    <table class="official-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ที่</th>
                                <th style="width: 12%;">รหัสวิชา</th>
                                <th style="width: 25%;">รายวิชา</th>
                                <th style="width: 7%;">พื้นฐาน</th>
                                <th style="width: 7%;">เพิ่มเติม</th>
                                <th style="width: 7%;">หน่วยกิต</th>
                                <th style="width: 8%;">ชั่วโมง/<br>สัปดาห์</th>
                                <th style="width: 7%;">ระดับ<br>ชั้น</th>
                                <th style="width: 9%;">ห้อง</th>
                                <th style="width: 8%;">รวม<br>ชั่วโมง</th>
                                <th style="width: 12%;">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($grouped_subjects)): ?>
                                <?php 
                                    $i = 1; 
                                    foreach ($grouped_subjects as $sub): 
                                        $isAdditional = mb_strpos($sub['subject_type'] ?? '', 'เพิ่มเติม') !== false;
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $i++ ?></td>
                                        <td class="text-center fw-bold"><?= esc($sub['subject_code']) ?></td>
                                        <td class="text-start"><?= esc($sub['subject_name']) ?></td>
                                        <td class="text-center"><?= !$isAdditional ? '<i class="bi bi-check-lg text-success"></i>' : '' ?></td>
                                        <td class="text-center"><?= $isAdditional ? '<i class="bi bi-check-lg text-primary"></i>' : '' ?></td>
                                        <td class="text-center"><?= number_format($sub['credit'], 1) ?></td>
                                        <td class="text-center"><?= $sub['hours_per_week'] ?></td>
                                        <td class="text-center"><?= esc($sub['grade_level']) ?></td>
                                        <td class="text-center fw-semibold"><?= esc($sub['room_text']) ?></td>
                                        <td class="text-center fw-bold text-primary"><?= $sub['total_weekly_hours'] ?></td>
                                        <td class="text-center small"><?= esc($sub['final_remark']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center py-4 text-muted">- ไม่พบข้อมูลรายวิชาที่สอน -</td>
                                </tr>
                            <?php endif; ?>

                            <!-- Summary Row -->
                            <tr class="summary-row">
                                <td colspan="3" class="text-center">รวม ( <?= count($grouped_subjects) ?> รายวิชา )</td>
                                <td class="text-center"><?= $basic_subject_count > 0 ? $basic_subject_count . ' วิชา' : '-' ?></td>
                                <td class="text-center"><?= $additional_subject_count > 0 ? $additional_subject_count . ' วิชา' : '-' ?></td>
                                <td class="text-center"><?= number_format($total_credit, 1) ?></td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td class="text-center text-primary fs-6"><?= $total_subject_weekly_hours ?></td>
                                <td class="text-center">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Table 2: กิจกรรม / อื่น ๆ -->
                <div class="fw-bold text-dark mb-2 d-flex align-items-center justify-content-between mt-4">
                    <span><i class="bi bi-flag me-1 text-info"></i>กิจกรรม / อื่น ๆ</span>
                    <span class="badge bg-label-info rounded-pill small"><?= count($activities) ?> รายการ</span>
                </div>

                <div class="table-responsive mb-3">
                    <table class="official-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ที่</th>
                                <th style="width: 48%;">กิจกรรม</th>
                                <th style="width: 10%;">ชั้น</th>
                                <th style="width: 12%;">ห้อง</th>
                                <th style="width: 12%;">รวมชั่วโมง</th>
                                <th style="width: 13%;">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($activities)): ?>
                                <?php $k = 1; foreach ($activities as $act): ?>
                                    <tr>
                                        <td class="text-center"><?= $k++ ?></td>
                                        <td class="text-start fw-bold"><?= esc($act['activity_name']) ?></td>
                                        <td class="text-center"><?= esc($act['grade_level'] ?: '-') ?></td>
                                        <td class="text-center"><?= esc($act['room'] ?: '-') ?></td>
                                        <td class="text-center fw-bold text-info"><?= esc($act['hours_per_week']) ?></td>
                                        <td class="text-center small"><?= esc($act['remark'] ?: '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">- ไม่พบข้อมูลกิจกรรม -</td>
                                </tr>
                            <?php endif; ?>

                            <!-- Summary Row -->
                            <tr class="summary-row">
                                <td colspan="4" class="text-center">รวม ( <?= count($activities) ?> กิจกรรม )</td>
                                <td class="text-center text-info fs-6"><?= $total_activity_weekly_hours ?></td>
                                <td class="text-center">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Grand Total Banner -->
                <div class="grand-total-banner">
                    รวมชั่วโมงทั้งสิ้น &nbsp;&nbsp;<span class="text-primary text-decoration-underline fs-5"><?= $grand_total_weekly_hours ?></span>&nbsp;&nbsp; ชั่วโมง / สัปดาห์
                </div>

                <!-- Section: หน้าที่พิเศษ -->
                <div class="duty-list-box">
                    <div class="fw-bold text-dark mb-2">
                        <i class="bi bi-award me-1 text-warning"></i>หน้าที่พิเศษ
                    </div>
                    <?php if (!empty($duties)): ?>
                        <ol>
                            <?php foreach ($duties as $dt): ?>
                                <?php 
                                    $cleanDutyName = preg_replace('/^\d+[\.\)]\s*/u', '', trim($dt['duty_name']));
                                ?>
                                <li><?= esc($cleanDutyName) ?></li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else: ?>
                        <div class="text-muted small ps-4 fst-italic">- ไม่ได้ระบุหน้าที่พิเศษ -</div>
                    <?php endif; ?>
                </div>

                <!-- Section: การลงชื่อรับรอง (เรียงเป็นบรรทัดใคร บรรทัดมัน) -->
                <div class="signatures-box">
                    <div class="sig-row-item">
                        <span>ลงชื่อ</span>
                        <span class="sig-dots-line"></span>
                        <span class="sig-role-text">ครูผู้สอน</span>
                    </div>
                    <div class="sig-row-item">
                        <span>ลงชื่อ</span>
                        <span class="sig-dots-line"></span>
                        <span class="sig-role-text">หัวหน้ากลุ่มสาระการเรียนรู้</span>
                    </div>
                    <div class="sig-row-item">
                        <span>ลงชื่อ</span>
                        <span class="sig-dots-line"></span>
                        <span class="sig-role-text">รองผู้อำนวยการฝ่ายวิชาการ</span>
                    </div>
                    <div class="sig-row-item">
                        <span>ลงชื่อ</span>
                        <span class="sig-dots-line"></span>
                        <span class="sig-role-text">ผู้อำนวยการสถานศึกษา</span>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
            <a href="<?= base_url('curriculum/teaching-schedule/' . $year . '/' . $term) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>กลับหน้ารวมตารางสอน
            </a>
            <a href="<?= base_url('curriculum/teaching-schedule/print/' . $teacher->pers_id . '/' . $year . '/' . $term) ?>" target="_blank" class="btn btn-primary">
                <i class="bi bi-printer-fill me-1"></i>พิมพ์เอกสาร (A4)
            </a>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
