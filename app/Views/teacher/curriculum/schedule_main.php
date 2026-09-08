<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'จัดตารางสอนของกลุ่มสาระ') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white border-0 shadow-sm overflow-hidden">
                <div class="card-body position-relative p-4 p-md-5">
                    <div class="position-relative zindex-1">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md me-3">
                                <span class="avatar-initial rounded-circle bg-white text-primary">
                                    <i class="bi bi-calendar3 fs-4"></i>
                                </span>
                            </div>
                            <h1 class="display-6 fw-bold mb-0 text-white">จัดตารางสอนของกลุ่มสาระ</h1>
                        </div>
                        <p class="opacity-75 mb-0">ระบบกำหนดรายวิชา ชั้น ห้อง และชั่วโมงสอน สำหรับครูแต่ละท่าน ประจำภาคเรียนที่ <?= esc($current_term) ?> ปีการศึกษา <?= esc($current_year) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php 
        $isSystemOpen = isset($is_open) ? $is_open : true;
        $onoffStatus = $schedule_onoff['status'] ?? 'on';
        $startDate = $schedule_onoff['start_date'] ?? null;
        $endDate = $schedule_onoff['end_date'] ?? null;
    ?>

    <?php if (!$isSystemOpen): ?>
        <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-3 fs-3"></i>
            <div>
                <h5 class="alert-heading mb-1 fw-bold">ขณะนี้ระบบปิดการจัดตารางสอน!</h5>
                <div>ฝ่ายวิชาการปิดระบบการจัดตารางสอนชั่วคราว ท่านสามารถดูและพิมพ์เอกสารตารางสอนได้ตามปกติ แต่ไม่สามารถเพิ่ม แก้ไข หรือลบข้อมูลได้</div>
            </div>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header border-bottom bg-white d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center gap-2">
                <h5 class="mb-0 fw-bold"><i class="bi bi-list-columns me-2 text-primary"></i>ข้อมูลการจัดตารางสอน</h5>
                <?php if ($isSystemOpen): ?>
                    <span class="badge bg-label-success rounded-pill"><i class="bi bi-check-circle me-1"></i>ระบบเปิด</span>
                <?php else: ?>
                    <span class="badge bg-label-danger rounded-pill"><i class="bi bi-lock-fill me-1"></i>ระบบปิด</span>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= base_url('curriculum/teaching-schedule/print-all/' . $current_year . '/' . $current_term) ?>" target="_blank" class="btn btn-outline-primary shadow-xs">
                    <i class="bi bi-printer me-1"></i> พิมพ์ตารางรวม
                </a>
                <?php if ($isSystemOpen): ?>
                    <button type="button" class="btn btn-primary" onclick="openAddModal()">
                        <i class="bi bi-plus-circle me-1"></i> เพิ่มข้อมูลตารางสอน
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary" disabled title="ระบบปิดการจัดตารางสอน">
                        <i class="bi bi-lock me-1"></i> ระบบปิดรับข้อมูล
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-bordered mb-0" style="min-width: 1200px;">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th width="4%">ที่</th>
                            <th width="14%">ครูผู้สอน</th>
                            <th width="9%">รหัสวิชา</th>
                            <th width="17%">รายวิชา</th>
                            <th width="8%">ประเภท</th>
                            <th width="6%">หน่วยกิต</th>
                            <th width="7%">ชม./สัปดาห์</th>
                            <th width="6%">ชั้น</th>
                            <th width="6%">ห้อง</th>
                            <th width="9%">แผนที่เรียน</th>
                            <th width="5%">รวม</th>
                            <th width="7%">หมายเหตุ</th>
                            <th width="8%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0 align-middle">
                        <?php if (empty($groupedSchedules)): ?>
                            <tr>
                                <td colspan="13" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    ยังไม่มีข้อมูลตารางสอนในภาคเรียนนี้
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $teacherIndex = 1; ?>
                            <?php foreach ($groupedSchedules as $teacherId => $data): ?>
                                <?php 
                                    $subjects = $data['subjects'] ?? $data['schedules'] ?? [];
                                    $rowspan = max(count($subjects), 1);
                                    $first = true;
                                ?>
                                <?php if (empty($subjects)): ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?= $teacherIndex++ ?></td>
                                        <td class="align-top">
                                            <div class="fw-bold text-dark fs-6"><?= esc($data['teacher_name']) ?></div>
                                            <div class="mt-1 d-flex flex-column gap-1">
                                                <div class="small text-muted d-flex align-items-center gap-1">
                                                    <i class="bi bi-flag text-info"></i> กิจกรรม <?= $data['total_activity_weekly_hours'] ?> คาบ
                                                </div>
                                                <div>
                                                    <span class="badge bg-label-success rounded-pill fw-bold" style="font-size: 0.76rem;">
                                                        <i class="bi bi-clock-history me-1"></i>รวม <?= $data['grand_total_weekly_hours'] ?> คาบ/สัปดาห์
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="btn-group btn-group-xs mt-2" role="group">
                                                <a href="<?= base_url('curriculum/teaching-schedule/teacher/' . $teacherId . '/' . $current_year . '/' . $current_term) ?>" class="btn btn-xs btn-outline-info" title="ดูข้อมูลตารางสอน">
                                                    <i class="bi bi-eye me-1"></i>ดูข้อมูล
                                                </a>
                                                <a href="<?= base_url('curriculum/teaching-schedule/print/' . $teacherId . '/' . $current_year . '/' . $current_term) ?>" target="_blank" class="btn btn-xs btn-outline-primary" title="พิมพ์ตารางสอน">
                                                    <i class="bi bi-printer me-1"></i>พิมพ์
                                                </a>
                                                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="openTeacherExtraModal('<?= esc($teacherId) ?>', '<?= esc($data['teacher_name']) ?>')" title="จัดการกิจกรรมและหน้าที่พิเศษ">
                                                    <i class="bi bi-gear me-1"></i>หน้าที่พิเศษ
                                                </button>
                                            </div>
                                        </td>
                                        <td colspan="10" class="text-center text-muted py-3">
                                            <em>- ยังไม่มีรายวิชาที่สอน (มีกิจกรรม <?= count($data['activities'] ?? []) ?> รายการ, หน้าที่พิเศษ <?= count($data['duties'] ?? []) ?> รายการ) -</em>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($isSystemOpen): ?>
                                                <button type="button" class="btn btn-sm btn-primary rounded-pill" onclick="openAddModalForTeacher('<?= esc($teacherId) ?>')">
                                                    <i class="bi bi-plus-lg me-1"></i>เพิ่มวิชา
                                                </button>
                                            <?php else: ?>
                                                <span class="badge bg-label-secondary"><i class="bi bi-lock-fill me-1"></i>ปิด</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($subjects as $index => $row): ?>
                                        <tr>
                                            <?php if ($first): ?>
                                                <td class="text-center fw-bold" rowspan="<?= $rowspan ?>"><?= $teacherIndex++ ?></td>
                                                <td rowspan="<?= $rowspan ?>" class="align-top">
                                                    <div class="fw-bold text-dark fs-6"><?= esc($data['teacher_name']) ?></div>
                                                    
                                                    <!-- สรุปภาระงานแบบกระชับ -->
                                                    <div class="mt-1 d-flex flex-column gap-1">
                                                        <div class="small text-muted d-flex align-items-center gap-1">
                                                            <i class="bi bi-book text-primary"></i> สอน <?= count($subjects) ?> วิชา (<?= $data['total_subject_hours'] ?> คาบ)
                                                            <?php if (!empty($data['total_activity_weekly_hours'])): ?>
                                                                <span class="text-secondary">•</span> <i class="bi bi-flag text-info"></i> กิจกรรม <?= $data['total_activity_weekly_hours'] ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div>
                                                            <span class="badge bg-label-success rounded-pill fw-bold" style="font-size: 0.76rem;">
                                                                <i class="bi bi-clock-history me-1"></i>รวม <?= $data['grand_total_weekly_hours'] ?> คาบ/สัปดาห์
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <?php if (!empty($data['duties'])): ?>
                                                        <div class="text-muted small mt-1 text-truncate" style="max-width: 220px;" title="<?= esc(implode(', ', array_column($data['duties'], 'duty_name'))) ?>">
                                                            <i class="bi bi-award text-warning me-1"></i><?= esc($data['duties'][0]['duty_name']) ?><?= count($data['duties']) > 1 ? ' ...' : '' ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- ปุ่มจัดการขนาดกะทัดรัด -->
                                                    <div class="btn-group btn-group-xs mt-2" role="group">
                                                        <a href="<?= base_url('curriculum/teaching-schedule/teacher/' . $teacherId . '/' . $current_year . '/' . $current_term) ?>" class="btn btn-xs btn-outline-info" title="ดูข้อมูลตารางสอน">
                                                            <i class="bi bi-eye me-1"></i>ดูข้อมูล
                                                        </a>
                                                        <a href="<?= base_url('curriculum/teaching-schedule/print/' . $teacherId . '/' . $current_year . '/' . $current_term) ?>" target="_blank" class="btn btn-xs btn-outline-primary" title="พิมพ์ตารางสอน">
                                                            <i class="bi bi-printer me-1"></i>พิมพ์
                                                        </a>
                                                        <?php if ($isSystemOpen): ?>
                                                            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="openTeacherExtraModal('<?= esc($teacherId) ?>', '<?= esc($data['teacher_name']) ?>')" title="จัดการกิจกรรมและหน้าที่พิเศษ">
                                                                <i class="bi bi-gear me-1"></i>หน้าที่พิเศษ
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                            <td class="text-center fw-bold text-primary"><?= esc($row['subject_code']) ?></td>
                                            <td class="fw-medium"><?= esc($row['subject_name']) ?></td>
                                            <td class="text-center">
                                                <span class="badge <?= ($row['subject_type'] === 'เพิ่มเติม') ? 'bg-label-info' : 'bg-label-secondary' ?>">
                                                    <?= esc($row['subject_type']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center"><?= esc($row['credit']) ?></td>
                                            <td class="text-center">
                                                <span class="fw-bold fs-6"><?= esc($row['hours_per_week']) ?></span>
                                                <?php if ($row['room_count'] > 1): ?>
                                                    <div class="text-primary small fw-semibold" style="font-size: 0.72rem;">(รวม <?= $row['total_weekly_hours'] ?> คาบ)</div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center fw-semibold"><?= esc($row['grade_level']) ?></td>

                                            <!-- กลุ่มห้องที่สอนวิชานี้ -->
                                            <td class="text-center">
                                                <?php 
                                                    $displayRoom = !empty($row['room_range_text']) ? $row['room_range_text'] : (!empty($row['rooms']) ? implode(', ', $row['rooms']) : '-');
                                                ?>
                                                <span class="badge bg-primary fw-bold px-2 py-1 shadow-xs">
                                                    <i class="bi bi-door-open me-1"></i>ห้อง <?= esc($displayRoom) ?>
                                                </span>
                                            </td>

                                            <!-- แผนที่เรียน -->
                                            <td class="text-center">
                                                <?php if (empty($row['distinct_plans'])): ?>
                                                    <span class="text-muted">-</span>
                                                <?php else: ?>
                                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                                        <?php foreach ($row['distinct_plans'] as $p): ?>
                                                            <span class="badge bg-label-info px-2 py-1"><?= esc($p) ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>

                                            <!-- รวม ชม. -->
                                            <td class="text-center fw-bold">
                                                <?= esc($row['total_hours']) ?>
                                                <?php if ($row['room_count'] > 1 && !empty($row['single_total_hours'])): ?>
                                                    <div class="text-muted small fw-normal" style="font-size: 0.7rem;">(<?= $row['single_total_hours'] ?> ชม./ห้อง)</div>
                                                <?php endif; ?>
                                            </td>

                                            <!-- หมายเหตุ -->
                                            <td>
                                                <?= !empty($row['remarks']) ? esc(implode(', ', $row['remarks'])) : '<span class="text-muted">-</span>' ?>
                                            </td>

                                            <!-- จัดการ -->
                                            <td class="text-center">
                                                <?php if ($isSystemOpen): ?>
                                                    <button type="button" class="btn btn-icon btn-sm btn-outline-warning rounded-pill me-1" 
                                                            onclick="editSchedule('<?= implode(',', $row['schedule_ids']) ?>')" title="แก้ไขรายวิชานี้">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-icon btn-sm btn-outline-danger rounded-pill" 
                                                            onclick="deleteSchedule('<?= implode(',', $row['schedule_ids']) ?>', '<?= esc($row['subject_code']) ?>', '<?= esc($row['room_range_text'] ?: $row['room_text']) ?>')" title="ลบรายวิชานี้">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge bg-label-secondary"><i class="bi bi-lock-fill me-1"></i>ปิด</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php $first = false; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalTitle">
                    <i class="bi bi-calendar-plus me-2 text-primary"></i>เพิ่มข้อมูลตารางสอน
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="scheduleForm" onsubmit="saveSchedule(event)">
                <div class="modal-body">
                    <input type="hidden" id="schedule_id" name="schedule_id">
                    <input type="hidden" id="existing_schedule_ids" name="existing_schedule_ids">
                    <input type="hidden" id="year" name="year" value="<?= esc($current_year) ?>">
                    <input type="hidden" id="term" name="term" value="<?= esc($current_term) ?>">
                    <input type="hidden" name="save_activities" value="1">
                    <input type="hidden" name="save_duties" value="1">

                    <!-- เลือกครูผู้สอน -->
                    <div class="card border-0 bg-label-primary p-3 mb-3">
                        <label class="form-label fw-bold text-primary mb-1">
                            <i class="bi bi-person-badge-fill me-1"></i> ครูผู้สอน <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="teacher_id" name="teacher_id" required onchange="onTeacherChanged(this.value)">
                            <option value="">-- เลือกครูผู้สอน --</option>
                            <?php foreach ($teachers as $t): ?>
                                <option value="<?= esc($t['pers_id']) ?>"><?= esc($t['pers_prefix'] . $t['pers_firstname'] . ' ' . $t['pers_lastname']) ?> (<?= esc($t['pers_learning']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Nav Tabs: 1. รายวิชาที่สอน, 2. กิจกรรม/อื่น ๆ, 3. หน้าที่พิเศษ -->
                    <ul class="nav nav-tabs nav-fill mb-3" id="scheduleModalTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link active fw-semibold" id="tab-subjects-btn" data-bs-toggle="tab" data-bs-target="#tab-subjects" role="tab">
                                <i class="bi bi-journal-text me-1"></i> 1. รายวิชาที่สอน
                                <span class="badge bg-label-primary rounded-pill ms-1" id="subjectCountBadge">1 รายวิชา</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link fw-semibold" id="tab-activities-btn" data-bs-toggle="tab" data-bs-target="#tab-activities" role="tab">
                                <i class="bi bi-flag me-1"></i> 2. กิจกรรม / อื่น ๆ
                                <span class="badge bg-label-info rounded-pill ms-1" id="activityCountBadge">0 รายการ</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link fw-semibold" id="tab-duties-btn" data-bs-toggle="tab" data-bs-target="#tab-duties" role="tab">
                                <i class="bi bi-award me-1"></i> 3. หน้าที่พิเศษ
                                <span class="badge bg-label-warning rounded-pill ms-1" id="dutyCountBadge">0 รายการ</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-0">
                        <!-- Tab 1: รายวิชาที่สอน -->
                        <div class="tab-pane fade show active" id="tab-subjects" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                                        <i class="bi bi-journal-text me-2 text-primary fs-5"></i> รายการวิชาที่สอน 
                                    </h6>
                                </div>
                            </div>

                            <!-- Dynamic Subject Cards Container -->
                            <div id="subjectRowsContainer">
                                <!-- รายการวิชาจะถูกสร้างแบบ Dynamic ที่นี่ -->
                            </div>

                            <!-- ปุ่มเพิ่มอีกวิชา ย้ายมาไว้ด้านล่างตามรายการวิชา -->
                            <div class="text-center mt-3 mb-2" id="bottomAddSubjectWrapper">
                                <button type="button" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill" id="btnAddSubjectRow" onclick="addSubjectRow()">
                                    <i class="bi bi-plus-circle-fill me-2 fs-6"></i> เพิ่มอีกวิชา
                                </button>
                            </div>
                        </div>

                        <!-- Tab 2: กิจกรรม / อื่น ๆ -->
                        <div class="tab-pane fade" id="tab-activities" role="tabpanel">
                            <!-- Preset Quick Add Bar -->
                            <div class="card border border-info mb-3 bg-label-info">
                                <div class="card-body p-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="d-flex align-items-center flex-wrap gap-1">
                                        <span class="small fw-bold text-dark me-1"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>เพิ่มด่วน:</span>
                                        <button type="button" class="btn btn-xs btn-white border shadow-xs" onclick="quickAddActivity('สวนกุหลาบศึกษา', 1)">+ สวนกุหลาบศึกษา (1 ชม.)</button>
                                        <button type="button" class="btn btn-xs btn-white border shadow-xs" onclick="quickAddActivity('ชุมนุม', 2)">+ ชุมนุม (2 ชม.)</button>
                                        <button type="button" class="btn btn-xs btn-white border shadow-xs" onclick="quickAddActivity('ลูกเสือ-เนตรนารี', 1)">+ ลูกเสือ-เนตรนารี (1 ชม.)</button>
                                        <button type="button" class="btn btn-xs btn-white border shadow-xs" onclick="quickAddActivity('PLC', 2)">+ PLC (2 ชม.)</button>
                                        <button type="button" class="btn btn-xs btn-white border shadow-xs" onclick="quickAddActivity('โฮมรูม', 1)">+ โฮมรูม (1 ชม.)</button>
                                        <button type="button" class="btn btn-xs btn-white border shadow-xs" onclick="quickAddActivity('แนะแนว', 1)">+ แนะแนว (1 ชม.)</button>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-info text-white" onclick="addActivityRow()">
                                        <i class="bi bi-plus-lg me-1"></i> เพิ่มกิจกรรม
                                    </button>
                                </div>
                            </div>

                            <!-- Table of Activities -->
                            <div class="table-responsive border rounded bg-white">
                                <table class="table table-bordered table-hover mb-0" id="activityTable">
                                    <thead class="table-light text-center align-middle">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="35%">ชื่อกิจกรรม / อื่น ๆ <span class="text-danger">*</span></th>
                                            <th width="12%">ชม./สัปดาห์ <span class="text-danger">*</span></th>
                                            <th width="12%">ระดับชั้น</th>
                                            <th width="12%">ห้อง</th>
                                            <th width="16%">หมายเหตุ</th>
                                            <th width="8%">ลบ</th>
                                        </tr>
                                    </thead>
                                    <tbody id="activityRowsContainer">
                                        <!-- Dynamic Activity Rows -->
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="2" class="text-end fw-bold">รวมชั่วโมงกิจกรรม:</td>
                                            <td class="text-center fw-bold text-primary" id="totalActivityHoursBadge">0 คาบ</td>
                                            <td colspan="4"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 3: หน้าที่พิเศษ -->
                        <div class="tab-pane fade" id="tab-duties" role="tabpanel">
                            <div class="card border border-warning mb-3 bg-label-warning">
                                <div class="card-body p-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="d-flex align-items-center flex-wrap gap-1">
                                        <button type="button" class="btn btn-sm btn-primary shadow-xs" onclick="autoSuggestDuties()">
                                            <i class="bi bi-magic me-1"></i> ดึงหน้าที่พิเศษอัตโนมัติ (ครูที่ปรึกษา / หัวหน้ากลุ่มสาระ)
                                        </button>
                                        <button type="button" class="btn btn-xs btn-white border shadow-xs" onclick="quickAddDuty('ครูเวรประจำวัน')">+ ครูเวรประจำวัน</button>
                                        <button type="button" class="btn btn-xs btn-white border shadow-xs" onclick="quickAddDuty('หัวหน้างาน')">+ หัวหน้างาน...</button>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-warning text-dark fw-semibold" onclick="addDutyRow()">
                                        <i class="bi bi-plus-lg me-1"></i> เพิ่มหน้าที่พิเศษ
                                    </button>
                                </div>
                            </div>

                            <!-- Duties List Container -->
                            <div id="dutyRowsContainer" class="d-flex flex-column gap-2">
                                <!-- Dynamic Duty Rows -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-white">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="btnSave"><i class="bi bi-save me-1"></i> บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Fix Select2 height to match Bootstrap 5 form-control */
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #d9dee3 !important;
        border-radius: 0.375rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: 14px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    .select2-dropdown {
        z-index: 9999 !important;
    }
    .select-subject-bank-wrapper .select2-container {
        display: block !important;
        width: 100% !important;
    }
    .subject-item-card {
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
    }
    .subject-item-card:hover {
        border-color: #696cff !important;
    }
    .subject-locked-field {
        background-color: #f5f5f9 !important;
        border-color: #d9dee3 !important;
        color: #566a7f !important;
        cursor: not-allowed;
    }
    .manual-entry-box {
        background: #ffffff;
        border: 1px solid #e0e4e8;
        border-radius: 0.6rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .btn-room-pill {
        min-width: 38px;
        height: 32px;
        padding: 0 10px;
        font-size: 0.82rem;
        font-weight: 600;
        border-radius: 6px;
        border: 1.5px solid #d9dee3;
        background-color: #fff;
        color: #566a7f;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease-in-out;
    }
    .btn-room-pill:hover {
        background-color: #f5f5f9;
        border-color: #696cff;
        color: #696cff;
    }
    .btn-room-pill.active {
        background-color: #696cff !important;
        border-color: #696cff !important;
        color: #fff !important;
        box-shadow: 0 2px 6px rgba(105, 108, 255, 0.4);
    }
    .btn-xs {
        padding: 0.2rem 0.6rem;
        font-size: 0.75rem;
        border-radius: 0.35rem;
    }
</style>

<script>
    const modal = new bootstrap.Modal(document.getElementById('scheduleModal'));
    let rowCounter = 0;
    let activityCounter = 0;
    let dutyCounter = 0;
    let cachedSuggestedDuties = [];
    const availableStudyPlans = <?= json_encode($study_plans ?? []) ?>;
    const classRoomMap = <?= json_encode($class_room_map ?? []) ?>;

    $(document).ready(function() {
        $('#teacher_id').select2({
            dropdownParent: $('#scheduleModal'),
            width: '100%',
            placeholder: '-- ค้นหา/เลือกครูผู้สอน --'
        });
    });

    function escapeHtml(text) {
        if (!text && text !== 0) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    /**
     * Parse room input string (e.g. "1-6", "1, 2, 3", "1 - 6", "PAP") into array of rooms
     */
    function parseRooms(roomString) {
        if (!roomString) return [];
        let raw = String(roomString).trim();
        raw = raw.replace(/\s*[-–—]\s*/g, '-');
        const parts = raw.split(/[,\|\s]+/).filter(Boolean);
        const rooms = [];

        parts.forEach(part => {
            const match = part.match(/^(\d+)-(\d+)$/);
            if (match) {
                const start = parseInt(match[1], 10);
                const end = parseInt(match[2], 10);
                if (start <= end && (end - start) <= 30) {
                    for (let i = start; i <= end; i++) {
                        rooms.push(String(i));
                    }
                    return;
                }
            }
            rooms.push(part);
        });

        return [...new Set(rooms)];
    }

    function normalizeGrade(g) {
        if (!g) return '';
        g = String(g).trim();
        return g.startsWith('ม.') ? g : ('ม.' + g);
    }

    /**
     * Get available rooms for a grade level from classRoomMap
     */
    function getRoomsForGrade(gradeLevel) {
        if (gradeLevel) {
            const gradeKey = normalizeGrade(gradeLevel);
            const targetMap = classRoomMap[gradeKey] || classRoomMap[gradeLevel];
            if (targetMap) {
                const rooms = Object.keys(targetMap);
                if (rooms.length > 0) {
                    return rooms.sort((a, b) => {
                        const numA = parseInt(a, 10);
                        const numB = parseInt(b, 10);
                        if (!isNaN(numA) && !isNaN(numB)) return numA - numB;
                        return a.localeCompare(b);
                    });
                }
            }
        }
        return ['1', '2', '3', '4', '5', '6', '7', '8'];
    }

    /**
     * Render clickable room buttons for a subject card
     */
    function renderRoomPills(index, currentRoomStr, gradeLevel = '') {
        const availableRooms = getRoomsForGrade(gradeLevel);
        const selectedRooms = parseRooms(currentRoomStr);
        const gradeKey = normalizeGrade(gradeLevel);
        const gradeMap = classRoomMap[gradeKey] || classRoomMap[gradeLevel];

        let html = '';
        availableRooms.forEach(r => {
            const isActive = selectedRooms.includes(String(r));
            const activeClass = isActive ? 'btn-primary active' : 'btn-outline-primary';
            const plan = (gradeMap && gradeMap[r]) ? gradeMap[r].plan : '';
            const title = plan ? `ห้อง ${r} (${plan})` : `ห้อง ${r}`;
            html += `<button type="button" class="btn btn-sm ${activeClass} btn-room-pill" 
                             data-room="${r}" title="${title}" 
                             onclick="toggleRoomPill(this, '${r}')">${r}</button>`;
        });
        return html;
    }

    /**
     * Toggle individual room pill on/off
     */
    function toggleRoomPill(btn, roomNum) {
        const $card = $(btn).closest('.subject-item-card');
        const $input = $card.find('.room');
        let rooms = parseRooms($input.val());

        roomNum = String(roomNum);
        if (rooms.includes(roomNum)) {
            rooms = rooms.filter(r => r !== roomNum);
        } else {
            rooms.push(roomNum);
        }

        rooms.sort((a, b) => {
            const numA = parseInt(a, 10);
            const numB = parseInt(b, 10);
            if (!isNaN(numA) && !isNaN(numB)) return numA - numB;
            return a.localeCompare(b);
        });

        $input.val(rooms.join(', '));
        updateRoomUI($card);
    }

    /**
     * Quick preset button (e.g. 1-6, all, clear)
     */
    function quickSelectRooms(btn, rangeStr) {
        const $card = $(btn).closest('.subject-item-card');
        const $input = $card.find('.room');
        const gradeLevel = $card.find('.grade-level').val() || '';
        const availableRooms = getRoomsForGrade(gradeLevel);

        if (rangeStr === 'clear') {
            $input.val('');
        } else if (rangeStr === '1-6') {
            const target = availableRooms.filter(r => {
                const n = parseInt(r, 10);
                return !isNaN(n) && n >= 1 && n <= 6;
            });
            $input.val(target.length > 0 ? target.join(', ') : '1, 2, 3, 4, 5, 6');
        } else if (rangeStr === 'all') {
            $input.val(availableRooms.join(', '));
        }
        updateRoomUI($card);
    }

    /**
     * Handle typing directly into room input field
     */
    function handleRoomInput(input) {
        const $card = $(input).closest('.subject-item-card');
        updateRoomUI($card);
    }

    /**
     * Update room UI pills, badge count, and live preview tags
     */
    function updateRoomUI($card) {
        const $input = $card.find('.room');
        const currentRooms = parseRooms($input.val());
        const gradeLevel = $card.find('.grade-level').val() || '';
        const selectedPlan = $card.find('.study-plan').val() || '';

        // 1. Sync pill button active states
        $card.find('.btn-room-pill').each(function() {
            const r = String($(this).data('room'));
            if (currentRooms.includes(r)) {
                $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
            } else {
                $(this).removeClass('btn-primary active').addClass('btn-outline-primary');
            }
        });

        // 2. Render live preview badge of selected rooms
        const $preview = $card.find('.selected-rooms-preview');
        if ($preview.length) {
            if (currentRooms.length > 0) {
                let html = '<span class="badge bg-primary fw-bold me-1" style="font-size: 0.76rem;"><i class="bi bi-door-open me-1"></i>ห้องที่เลือก:</span>';
                currentRooms.forEach(function(r) {
                    html += `<span class="badge bg-label-primary fw-semibold" style="font-size: 0.74rem;">ห้อง ${escapeHtml(r)}</span>`;
                });
                html += `<span class="text-muted small ms-1" style="font-size: 0.72rem;">(รวม ${currentRooms.length} ห้อง)</span>`;
                $preview.html(html).show();
            } else {
                $preview.html('<span class="text-muted small" style="font-size: 0.72rem;"><i class="bi bi-info-circle me-1"></i>ยังไม่ได้เลือกห้อง</span>').show();
            }
        }
    }

    function handleStudyPlanChange(select) {
        const $card = $(select).closest('.subject-item-card');
        if (select.value === '__custom__') {
            Swal.fire({
                title: 'ระบุตัวย่อแผนการเรียน',
                input: 'text',
                inputLabel: 'กรุณากรอกตัวย่อแผนการเรียน (ภาษาอังกฤษ เช่น SMTE, MEP, EP, Sci-Math):',
                inputPlaceholder: 'เช่น SMTE, MEP, Sci-Math, Art-Math, General',
                showCancelButton: true,
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก',
                inputValidator: (value) => {
                    if (!value || !value.trim()) {
                        return 'กรุณากรอกตัวย่อแผนการเรียน!';
                    }
                }
            }).then((res) => {
                if (res.isConfirmed && res.value) {
                    const customVal = res.value.trim();
                    if (!availableStudyPlans.includes(customVal)) {
                        availableStudyPlans.push(customVal);
                    }
                    // Check if option already exists in this select
                    let existingOpt = $(select).find(`option[value="${customVal}"]`);
                    if (existingOpt.length === 0) {
                        $(`<option value="${escapeHtml(customVal)}">${escapeHtml(customVal)}</option>`).insertBefore($(select).find('option[value="__custom__"]'));
                    }
                    $(select).val(customVal);
                    updateRoomUI($card);
                } else {
                    $(select).val('__auto__');
                    updateRoomUI($card);
                }
            });
        } else {
            updateRoomUI($card);
        }
    }

    function getSubjectRowTemplate(index, data = null) {
        const code = data ? (data.subject_code || '') : '';
        const name = data ? (data.subject_name || '') : '';
        const type = data ? (data.subject_type || '') : '';
        const credit = data ? (data.credit ?? '') : '';
        const hpw = data ? (data.hours_per_week ?? '') : '';
        const grade = data ? (data.grade_level || '') : '';
        const room = data ? (data.room || '') : '';
        const studyPlan = data ? (data.study_plan || '') : '';
        const totalH = data ? (data.total_hours ?? '') : '';
        const remark = data ? (data.remark || '') : '';

        // Generate study plan options
        let studyPlanOptionsHtml = '<option value="__auto__" ' + (!studyPlan || studyPlan === '__auto__' ? 'selected' : '') + '>-- ตามแผนของแต่ละห้องอัตโนมัติ (จากฐานข้อมูล) --</option>';
        let planMatched = false;
        if (Array.isArray(availableStudyPlans)) {
            availableStudyPlans.forEach(function(sp) {
                const isSelected = (studyPlan && studyPlan !== '__auto__' && studyPlan.trim().toLowerCase() === String(sp).trim().toLowerCase());
                if (isSelected) planMatched = true;
                studyPlanOptionsHtml += `<option value="${escapeHtml(sp)}" ${isSelected ? 'selected' : ''}>${escapeHtml(sp)}</option>`;
            });
        }
        // If editing existing schedule with a plan not currently in list, preserve it as selected option
        if (studyPlan && studyPlan !== '__auto__' && !planMatched) {
            studyPlanOptionsHtml += `<option value="${escapeHtml(studyPlan)}" selected>${escapeHtml(studyPlan)}</option>`;
        }

        return `
        <div class="card border border-2 border-light-subtle shadow-none bg-lighter mb-3 subject-item-card" data-index="${index}">
            <div class="card-header bg-white py-2 px-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary subject-row-number">
                        <i class="bi bi-book me-1"></i> รายวิชาที่ ${index}
                    </span>
                    <span class="badge bg-label-secondary small d-none d-sm-inline-block">
                        <i class="bi bi-lock-fill me-1"></i> บรรทัด 1 ข้อมูลวิชา (ห้ามแก้ไข)
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end" style="max-width: 620px;">
                    <label class="form-label mb-0 small fw-bold text-primary text-nowrap">
                        <i class="bi bi-search me-1"></i> ดึงจากคลังวิชา:
                    </label>
                    <div class="select-subject-bank-wrapper" style="flex: 1; min-width: 250px; position: relative;">
                        <select class="form-select form-select-sm select-subject-bank" id="select_subject_bank_${index}">
                            <option value="">-- พิมพ์รหัสวิชา หรือชื่อวิชา เพื่อค้นหา --</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-pill btn-remove-subject ms-1" onclick="removeSubjectRow(this)" title="ลบวิชานี้">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-3">
                <!-- บรรทัดที่ 1: ข้อมูลจากคลังวิชา (ห้ามแก้ไข) -->
                <div class="row g-2 mb-3">
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label small fw-semibold text-secondary mb-1">รหัสวิชา <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm subject-locked-field subject-code fw-bold text-center" name="subject_code[]" value="${escapeHtml(code)}" readonly required placeholder="รหัสวิชา">
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label small fw-semibold text-secondary mb-1">ชื่อวิชา <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm subject-locked-field subject-name fw-medium" name="subject_name[]" value="${escapeHtml(name)}" readonly required placeholder="ชื่อวิชา">
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <label class="form-label small fw-semibold text-secondary mb-1">ประเภทวิชา</label>
                        <input type="text" class="form-control form-control-sm subject-locked-field subject-type text-center" name="subject_type[]" value="${escapeHtml(type)}" readonly placeholder="ประเภท">
                    </div>
                    <div class="col-md-1 col-sm-4">
                        <label class="form-label small fw-semibold text-secondary mb-1">หน่วยกิต</label>
                        <input type="number" step="0.5" class="form-control form-control-sm subject-locked-field credit text-center" name="credit[]" value="${credit}" readonly placeholder="0">
                    </div>
                    <div class="col-md-1 col-sm-4">
                        <label class="form-label small fw-semibold text-secondary mb-1">ชม./สัปดาห์</label>
                        <input type="number" class="form-control form-control-sm subject-locked-field hours-per-week text-center" name="hours_per_week[]" value="${hpw}" readonly placeholder="0">
                    </div>
                    <div class="col-md-1 col-sm-6">
                        <label class="form-label small fw-semibold text-secondary mb-1">รวม ชม.</label>
                        <input type="number" class="form-control form-control-sm subject-locked-field total-hours text-center" name="total_hours[]" value="${totalH}" readonly placeholder="0">
                    </div>
                    <div class="col-md-1 col-sm-6">
                        <label class="form-label small fw-semibold text-secondary mb-1">ระดับชั้น</label>
                        <input type="text" class="form-control form-control-sm subject-locked-field grade-level text-center" name="grade_level[]" value="${escapeHtml(grade)}" readonly placeholder="ชั้น">
                    </div>
                </div>

                <!-- บรรทัดที่ 2: ส่วนที่ต้องกรอกเอง (ห้อง, แผนที่เรียน, หมายเหตุ) -->
                <div class="manual-entry-box p-3">
                    <div class="row g-3 align-items-start">
                        <!-- เลือกห้อง (คลิกเลือกปุ่มได้ทันที หรือพิมพ์ระบุเอง) -->
                        <div class="col-lg-5 col-md-6 col-12">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label mb-0 fw-bold text-dark d-flex align-items-center">
                                    <i class="bi bi-door-open-fill text-primary me-1 fs-6"></i> 
                                    <span>ห้องที่สอน</span> <span class="text-danger ms-1">*</span>
                                </label>
                                <div class="btn-group btn-group-xs" role="group">
                                    <button type="button" class="btn btn-xs btn-outline-primary fw-semibold" onclick="quickSelectRooms(this, '1-6')" title="เลือกห้อง 1 ถึง 6">
                                        ห้อง 1-6
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-primary fw-semibold" onclick="quickSelectRooms(this, 'all')" title="เลือกทุกห้อง">
                                        ทั้งหมด
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="quickSelectRooms(this, 'clear')" title="ล้างการเลือก">
                                        ล้าง
                                    </button>
                                </div>
                            </div>

                            <!-- ปุ่มคลิกเลือกห้อง (กดเปิด/ปิดห้องได้ง่ายด้วยคลิกเดียว) -->
                            <div class="d-flex flex-wrap gap-1 mb-2 room-pills-container">
                                ${renderRoomPills(index, room, grade)}
                            </div>

                            <!-- ช่องกรอก/แสดงผลห้องแบบกะทัดรัดพร้อมไอคอน -->
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted border-end-0 py-1" style="font-size: 0.78rem;">
                                    <i class="bi bi-pencil-square me-1"></i> ระบุ:
                                </span>
                                <input type="text" class="form-control form-control-sm room border-start-0 ps-1 fw-semibold text-primary" 
                                       name="room[]" value="${escapeHtml(room)}" required 
                                       placeholder="เช่น 1-6 หรือ 1, 2, 3 หรือ PAP"
                                       oninput="handleRoomInput(this)">
                            </div>
                            
                            <!-- แสดงผลห้องที่เลือกแบบเรียลไทม์ -->
                            <div class="selected-rooms-preview mt-1 d-flex flex-wrap align-items-center gap-1">
                                <!-- Generated by updateRoomUI -->
                            </div>
                        </div>

                        <!-- แผนการเรียน -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label mb-2 fw-bold text-dark d-flex align-items-center">
                                <i class="bi bi-diagram-3-fill text-primary me-1 fs-6"></i> 
                                <span>แผนการเรียน</span> <span class="text-danger ms-1">*</span>
                            </label>
                            <select class="form-select form-select-sm study-plan shadow-none fw-medium" name="study_plan[]" onchange="handleStudyPlanChange(this)" required>
                                ${studyPlanOptionsHtml}
                            </select>
                            <div class="form-text text-muted mt-1" style="font-size: 0.73rem;">
                                <i class="bi bi-info-circle me-1"></i>ระบบจะดึงแผนของแต่ละห้องให้อัตโนมัติ หรือเลือกเจาะจงแผนได้
                            </div>
                        </div>

                        <!-- หมายเหตุ -->
                        <div class="col-lg-3 col-md-12 col-12">
                            <label class="form-label mb-2 fw-semibold text-dark d-flex align-items-center">
                                <i class="bi bi-chat-left-dots-fill text-muted me-1 fs-6"></i> 
                                <span>หมายเหตุ</span> <small class="text-muted ms-1">(ถ้ามี)</small>
                            </label>
                            <input type="text" class="form-control form-control-sm remark" name="remark[]" value="${escapeHtml(remark)}" placeholder="เช่น ห้องเรียนพิเศษ, ชุมนุม">
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }

    function initSubjectBankSelect2($select) {
        const $wrapper = $select.closest('.select-subject-bank-wrapper');
        $select.select2({
            dropdownParent: $wrapper.length ? $wrapper : $('#scheduleModal'),
            width: '100%',
            placeholder: '-- พิมพ์รหัสวิชา หรือชื่อวิชา เพื่อค้นหา --',
            allowClear: true,
            ajax: {
                url: '<?= base_url('curriculum/teaching-schedule/search-subjects') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    const formYear = $('#year').val() || '<?= esc($current_year) ?>';
                    const formTerm = $('#term').val() || '<?= esc($current_term) ?>';
                    return {
                        q: params.term || '',
                        year: formYear,
                        term: formTerm
                    };
                },
                processResults: function(response) {
                    return {
                        results: response.results || []
                    };
                },
                cache: false
            },
            minimumInputLength: 0
        }).on('select2:select', function(e) {
            const data = e.params.data;
            const $card = $(this).closest('.subject-item-card');
            if (data && $card.length) {
                if (data.subject_code) $card.find('.subject-code').val(data.subject_code);
                if (data.subject_name) $card.find('.subject-name').val(data.subject_name);
                if (data.subject_type) $card.find('.subject-type').val(data.subject_type);
                if (data.credit !== undefined && data.credit !== '') $card.find('.credit').val(data.credit);
                if (data.hours_per_week !== undefined && data.hours_per_week !== '') $card.find('.hours-per-week').val(data.hours_per_week);
                if (data.total_hours !== undefined && data.total_hours !== '') $card.find('.total-hours').val(data.total_hours);
                if (data.grade_level) {
                    $card.find('.grade-level').val(data.grade_level);
                    // Refresh room pills based on grade level
                    const currentRoom = $card.find('.room').val();
                    const pillsHtml = renderRoomPills($card.data('index'), currentRoom, data.grade_level);
                    $card.find('.room-pills-container').html(pillsHtml);
                    updateRoomUI($card);
                }

                const $lockedInputs = $card.find('.subject-locked-field');
                $lockedInputs.addClass('is-valid');
                setTimeout(() => $lockedInputs.removeClass('is-valid'), 1200);

                // Focus directly on the "ห้อง" field in Row 2 so user can start typing immediately
                setTimeout(() => {
                    $card.find('.room').focus();
                }, 100);
            }
        }).on('select2:clear', function(e) {
            const $card = $(this).closest('.subject-item-card');
            if ($card.length) {
                $card.find('.subject-code').val('');
                $card.find('.subject-name').val('');
                $card.find('.subject-type').val('');
                $card.find('.credit').val('');
                $card.find('.hours-per-week').val('');
                $card.find('.total-hours').val('');
                $card.find('.grade-level').val('');
                $card.find('.room-pills-container').html(renderRoomPills($card.data('index'), '', ''));
                updateRoomUI($card);
            }
        });
    }

    function addSubjectRow(data = null) {
        rowCounter++;
        const currentIndex = rowCounter;
        const html = getSubjectRowTemplate(currentIndex, data);
        $('#subjectRowsContainer').append(html);
        const $newCard = $(`#subjectRowsContainer .subject-item-card[data-index="${currentIndex}"]`);
        const $newSelect = $newCard.find('.select-subject-bank');
        initSubjectBankSelect2($newSelect);

        // Prepopulate select2 in edit mode if code exists
        if (data && data.subject_code) {
            const text = data.subject_code + ' ' + (data.subject_name || '');
            const option = new Option(text, data.subject_code, true, true);
            $newSelect.append(option).trigger('change');
        }

        updateRoomUI($newCard);
        updateRowNumbers();

        // เลื่อนหน้าจอลงมาที่การ์ดวิชาใหม่แบบ smooth เมื่อผู้ใช้กดปุ่มเพิ่มอีกวิชา
        if (!data && rowCounter > 1) {
            setTimeout(() => {
                $newCard[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }
    }

    function removeSubjectRow(btn) {
        const count = $('.subject-item-card').length;
        if (count <= 1) {
            Swal.fire('แจ้งเตือน', 'ต้องมีรายการวิชาอย่างน้อย 1 รายการ', 'warning');
            return;
        }
        const $card = $(btn).closest('.subject-item-card');
        const $select = $card.find('.select-subject-bank');
        if ($select.data('select2')) {
            $select.select2('destroy');
        }
        $card.remove();
        updateRowNumbers();
    }

    function updateRowNumbers() {
        const cards = $('.subject-item-card');
        cards.each(function(index) {
            $(this).find('.subject-row-number').html(`<i class="bi bi-book me-1"></i> รายวิชาที่ ${index + 1}`);
        });
        $('#subjectCountBadge').text(`${cards.length} รายวิชา`);
        
        // Hide remove button if only 1 card, otherwise show
        if (cards.length === 1) {
            cards.find('.btn-remove-subject').hide();
        } else {
            cards.find('.btn-remove-subject').show();
        }
    }

    /* ==========================================================
     * ACTIVITIES (กิจกรรม / อื่น ๆ) FUNCTIONS
     * ========================================================== */
    function renderActivityRow(index, data = null) {
        const name   = data ? (data.activity_name || '') : '';
        const hours  = data ? (data.hours_per_week || 1) : 1;
        const grade  = data ? (data.grade_level || '') : '';
        const room   = data ? (data.room || '') : '';
        const remark = data ? (data.remark || '') : '';

        return `
        <tr class="activity-row" data-index="${index}">
            <td class="text-center fw-bold activity-row-number">${index}</td>
            <td>
                <input type="text" class="form-control form-control-sm activity-name fw-semibold" 
                       name="activity_name[]" value="${escapeHtml(name)}" required 
                       placeholder="เช่น สวนกุหลาบศึกษา, ชุมนุม, ลูกเสือ, PLC">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm text-center activity-hours fw-bold" 
                       name="activity_hours_per_week[]" value="${hours}" min="1" max="40" required 
                       onchange="calculateActivityTotals()" oninput="calculateActivityTotals()">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm text-center activity-grade" 
                       name="activity_grade_level[]" value="${escapeHtml(grade)}" placeholder="เช่น ม.1 หรือ -">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm text-center activity-room" 
                       name="activity_room[]" value="${escapeHtml(room)}" placeholder="เช่น 1-6 หรือ -">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm activity-remark" 
                       name="activity_remark[]" value="${escapeHtml(remark)}" placeholder="หมายเหตุ (ถ้ามี)">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-icon btn-sm btn-outline-danger rounded-pill" 
                        onclick="removeActivityRow(this)" title="ลบกิจกรรมนี้">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>`;
    }

    function addActivityRow(data = null) {
        activityCounter++;
        const html = renderActivityRow(activityCounter, data);
        $('#activityRowsContainer').append(html);
        updateActivityRowNumbers();
        calculateActivityTotals();
    }

    function quickAddActivity(name, hours) {
        const tabBtn = document.getElementById('tab-activities-btn');
        if (tabBtn) {
            bootstrap.Tab.getOrCreateInstance(tabBtn).show();
        }
        addActivityRow({
            activity_name: name,
            hours_per_week: hours,
            total_hours: hours * 20
        });
    }

    function removeActivityRow(btn) {
        $(btn).closest('.activity-row').remove();
        updateActivityRowNumbers();
        calculateActivityTotals();
    }

    function updateActivityRowNumbers() {
        const rows = $('.activity-row');
        rows.each(function(idx) {
            $(this).find('.activity-row-number').text(idx + 1);
        });
        $('#activityCountBadge').text(`${rows.length} รายการ`);
    }

    function calculateActivityTotals() {
        let total = 0;
        $('.activity-hours').each(function() {
            const h = parseFloat($(this).val()) || 0;
            total += h;
        });
        $('#totalActivityHoursBadge').text(`${total} คาบ`);
    }

    /* ==========================================================
     * SPECIAL DUTIES (หน้าที่พิเศษ) FUNCTIONS
     * ========================================================== */
    function renderDutyRow(index, name = '') {
        return `
        <div class="duty-row card border shadow-none p-2 mb-1" data-index="${index}">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-label-warning duty-order-badge px-2 py-1 fw-bold fs-6">${index}</span>
                <input type="text" class="form-control form-control-sm duty-name fw-medium" 
                       name="duty_name[]" value="${escapeHtml(name)}" required 
                       placeholder="ระบุหน้าที่พิเศษ เช่น 1. หัวหน้ากลุ่มสาระ..., 2. ครูที่ปรึกษา ม.4/5">
                <button type="button" class="btn btn-icon btn-sm btn-outline-danger rounded-pill flex-shrink-0" 
                        onclick="removeDutyRow(this)" title="ลบหน้าที่พิเศษนี้">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>`;
    }

    function addDutyRow(name = '') {
        dutyCounter++;
        const html = renderDutyRow(dutyCounter, name);
        $('#dutyRowsContainer').append(html);
        updateDutyRowNumbers();
    }

    function quickAddDuty(name) {
        const tabBtn = document.getElementById('tab-duties-btn');
        if (tabBtn) {
            bootstrap.Tab.getOrCreateInstance(tabBtn).show();
        }
        addDutyRow(name);
    }

    function removeDutyRow(btn) {
        $(btn).closest('.duty-row').remove();
        updateDutyRowNumbers();
    }

    function updateDutyRowNumbers() {
        const rows = $('.duty-row');
        rows.each(function(idx) {
            $(this).find('.duty-order-badge').text(idx + 1);
        });
        $('#dutyCountBadge').text(`${rows.length} รายการ`);
    }

    function autoSuggestDuties() {
        const tabBtn = document.getElementById('tab-duties-btn');
        if (tabBtn) {
            bootstrap.Tab.getOrCreateInstance(tabBtn).show();
        }

        if (!cachedSuggestedDuties || cachedSuggestedDuties.length === 0) {
            const teacherId = $('#teacher_id').val();
            if (!teacherId) {
                Swal.fire('แจ้งเตือน', 'กรุณาเลือกครูผู้สอนก่อน', 'info');
                return;
            }
            loadTeacherExtra(teacherId, true);
            return;
        }

        let addedCount = 0;
        const currentDutyNames = $('.duty-name').map(function() { return $(this).val().trim(); }).get();

        cachedSuggestedDuties.forEach(sug => {
            if (sug && !currentDutyNames.includes(sug.trim())) {
                addDutyRow(sug);
                addedCount++;
            }
        });

        if (addedCount > 0) {
            Swal.fire({
                icon: 'success',
                title: 'ดึงข้อมูลสำเร็จ',
                text: `เพิ่มหน้าที่พิเศษแนะนำ ${addedCount} รายการ`,
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            Swal.fire('แจ้งเตือน', 'มีหน้าที่พิเศษแนะนำในรายการอยู่แล้ว', 'info');
        }
    }

    /* ==========================================================
     * TEACHER EXTRA DATA SYNC (Activities & Duties)
     * ========================================================== */
    function onTeacherChanged(teacherId) {
        if (teacherId) {
            loadTeacherExtra(teacherId);
        }
    }

    function loadTeacherExtra(teacherId, triggerAutoSuggest = false) {
        if (!teacherId) return;

        const year = document.getElementById('year').value;
        const term = document.getElementById('term').value;

        fetch(`<?= base_url('curriculum/teaching-schedule/get-teacher-extra') ?>/${encodeURIComponent(teacherId)}?year=${year}&term=${term}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    cachedSuggestedDuties = res.suggested_duties || [];

                    // Populate Activities
                    $('#activityRowsContainer').empty();
                    activityCounter = 0;
                    if (res.activities && res.activities.length > 0) {
                        res.activities.forEach(act => {
                            addActivityRow(act);
                        });
                    }

                    // Populate Duties
                    $('#dutyRowsContainer').empty();
                    dutyCounter = 0;
                    if (res.duties && res.duties.length > 0) {
                        res.duties.forEach(dt => {
                            addDutyRow(dt.duty_name);
                        });
                    } else if (cachedSuggestedDuties.length > 0) {
                        cachedSuggestedDuties.forEach(sug => {
                            addDutyRow(sug);
                        });
                    }

                    if (triggerAutoSuggest) {
                        autoSuggestDuties();
                    }
                }
            })
            .catch(err => {
                console.error('Error loading teacher extra:', err);
            });
    }

    /* ==========================================================
     * MODAL CONTROLLERS & FORM SUBMISSION
     * ========================================================== */
    function openAddModal() {
        document.getElementById('scheduleForm').reset();
        document.getElementById('schedule_id').value = '';
        document.getElementById('existing_schedule_ids').value = '';
        $('#teacher_id').val('').trigger('change');
        $('#subjectRowsContainer').empty();
        $('#activityRowsContainer').empty();
        $('#dutyRowsContainer').empty();
        $('#btnAddSubjectRow, #bottomAddSubjectWrapper').show();
        rowCounter = 0;
        activityCounter = 0;
        dutyCounter = 0;
        cachedSuggestedDuties = [];
        addSubjectRow(); // start with 1 empty subject row
        calculateActivityTotals();
        updateDutyRowNumbers();

        // Switch to Tab 1
        const tabBtn = document.getElementById('tab-subjects-btn');
        if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();

        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-calendar-plus me-2 text-primary"></i>เพิ่มข้อมูลตารางสอน (ครู 1 ท่าน / หลายวิชา / กิจกรรม / หน้าที่พิเศษ)';
        modal.show();
    }

    function openAddModalForTeacher(teacherId) {
        openAddModal();
        if (teacherId) {
            $('#teacher_id').val(teacherId).trigger('change');
        }
    }

    function openTeacherExtraModal(teacherId, teacherName = '') {
        document.getElementById('scheduleForm').reset();
        document.getElementById('schedule_id').value = '';
        document.getElementById('existing_schedule_ids').value = '';
        $('#subjectRowsContainer').empty();
        $('#btnAddSubjectRow, #bottomAddSubjectWrapper').hide();
        rowCounter = 0;

        $('#teacher_id').val(teacherId).trigger('change');

        // Switch directly to Tab 2 (Activities)
        const tabBtn = document.getElementById('tab-activities-btn');
        if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();

        document.getElementById('modalTitle').innerHTML = `<i class="bi bi-gear-fill me-2 text-primary"></i>จัดการกิจกรรมและหน้าที่พิเศษ - <span class="text-primary">${escapeHtml(teacherName)}</span>`;
        modal.show();
    }

    function editSchedule(ids) {
        Swal.fire({
            title: 'กำลังโหลดข้อมูล...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('<?= base_url('curriculum/teaching-schedule/get') ?>?ids=' + encodeURIComponent(ids))
            .then(response => response.json())
            .then(result => {
                Swal.close();
                if (result.status === 'success') {
                    const data = result.data;
                    document.getElementById('schedule_id').value = data.schedule_id;
                    document.getElementById('existing_schedule_ids').value = data.schedule_ids || ids;
                    $('#teacher_id').val(data.teacher_id).trigger('change');
                    $('#subjectRowsContainer').empty();
                    $('#btnAddSubjectRow, #bottomAddSubjectWrapper').hide(); // in edit mode, editing this subject
                    rowCounter = 0;
                    addSubjectRow(data);

                    // Switch to Tab 1
                    const tabBtn = document.getElementById('tab-subjects-btn');
                    if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();

                    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square me-2 text-warning"></i>แก้ไขข้อมูลตารางสอน';
                    modal.show();
                } else {
                    Swal.fire('ข้อผิดพลาด!', result.msg, 'error');
                }
            })
            .catch(error => {
                Swal.fire('ข้อผิดพลาด!', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
            });
    }

    function saveSchedule(e) {
        e.preventDefault();

        // Check if teacher is selected
        const teacherId = $('#teacher_id').val();
        if (!teacherId) {
            Swal.fire('กรุณาเลือกครูผู้สอน', 'โปรดเลือกครูผู้สอนก่อนทำการบันทึก', 'warning');
            return;
        }

        const hasSubjectCards = $('.subject-item-card').length > 0;
        const hasActivityRows = $('.activity-row').length > 0;
        const hasDutyRows     = $('.duty-row').length > 0;

        let validSubjects = 0;
        let missingSubject = false;
        let missingRoom = false;
        let missingPlan = false;

        if (hasSubjectCards) {
            $('.subject-item-card').each(function(idx) {
                const code = ($(this).find('.subject-code').val() || '').trim();
                const room = ($(this).find('.room').val() || '').trim();
                const plan = ($(this).find('.study-plan').val() || '').trim();
                if (code) {
                    validSubjects++;
                    if (!room) missingRoom = true;
                    if (!plan || plan === '__custom__') missingPlan = true;
                }
            });
        }

        if (!validSubjects && !hasActivityRows && !hasDutyRows) {
            Swal.fire('กรุณากรอกข้อมูล', 'กรุณาระบุรายวิชา หรือ กิจกรรม หรือ หน้าที่พิเศษ อย่างน้อย 1 รายการ', 'warning');
            return;
        }
        if (validSubjects > 0 && (missingRoom || missingPlan)) {
            Swal.fire('กรุณากรอกข้อมูลให้ครบ', 'กรุณาระบุ "ห้อง" และ "แผนที่เรียน" ของรายวิชาให้ครบถ้วน', 'warning');
            return;
        }
        
        const form = document.getElementById('scheduleForm');
        const formData = new FormData(form);
        const btnSave = document.getElementById('btnSave');
        
        btnSave.disabled = true;
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> กำลังบันทึก...';

        fetch('<?= base_url('curriculum/teaching-schedule/save') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(result => {
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="bi bi-save me-1"></i> บันทึกข้อมูล';
            
            if (result.status === 'success') {
                modal.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: result.msg,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('ข้อผิดพลาด!', result.msg || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
            }
        })
        .catch(error => {
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="bi bi-save me-1"></i> บันทึกข้อมูล';
            Swal.fire('ข้อผิดพลาด!', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
        });
    }

    function deleteSchedule(ids, subjectCode = '', roomText = '') {
        const textMsg = subjectCode 
            ? `คุณต้องการลบรายวิชา ${subjectCode} ${roomText ? '(ห้อง ' + roomText + ')' : ''} ใช่หรือไม่? ข้อมูลที่ลบจะไม่สามารถกู้คืนได้`
            : "คุณต้องการลบข้อมูลนี้ใช่หรือไม่? ข้อมูลที่ลบจะไม่สามารถกู้คืนได้";

        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: textMsg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#8592a3',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('ids', ids);

                fetch('<?= base_url('curriculum/teaching-schedule/delete') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'ลบข้อมูลสำเร็จ',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('ข้อผิดพลาด!', result.msg, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('ข้อผิดพลาด!', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>
