<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'แผนการสอน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">แผนการสอน</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">แผนการสอน</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <?php
            $onOffSetting = $OnOff[0] ?? null;
            $is_system_on = false;
            $deadline = null;
            if ($onOffSetting) {
                $tiemstart = strtotime($onOffSetting->seplanset_startdate);
                $tiemEnd = strtotime($onOffSetting->seplanset_enddate);
                $timeNow = time();
                $is_system_on = ($tiemstart < $timeNow && $tiemEnd > $timeNow && $onOffSetting->seplanset_status == "on");
                $deadline = $onOffSetting->seplanset_enddate;
            }
            ?>

            <?php if($is_system_on): ?>
            <div class="alert alert-success">
                <strong>แจ้งเตือน!</strong> ระบบเปิดให้ส่งงาน
                <strong> (สิ้นสุด: <?= $deadline ? thai_date_and_time(strtotime($deadline)) : '-' ?>)</strong>
            </div>
            <?php else: ?>
            <div class="alert alert-danger">
                <strong>แจ้งเตือน!</strong> ขณะนี้ระบบปิดรับส่งแผนการสอน
            </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title mb-0">คำแนะนำ</h4>
                    <a target="_blank" href="<?= base_url('uploads/academic/course/คู่มือการส่งแผนการสอนออนไลน์2.0.pdf') ?>"
                        class="btn btn-sm btn-outline-dark"><i class="bi bi-book"></i> คู่มือการใช้งาน</a>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="bi bi-info-circle-fill text-primary"></i> กรุณาเลือก "วิชาหลักการส่งแผน" ที่ท่านต้องการส่งเอกสารครบ 5 รายการ</li>
                        <li><i class="bi bi-info-circle-fill text-primary"></i> วิชาอื่นๆ ที่ไม่ใช่-วิชาหลักการส่งแผน จะส่งเฉพาะ "โครงการสอน" เท่านั้น</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> แต่ละรายการที่ส่ง ส่งได้แค่ไฟล์เดียวเท่านั้น ให้รวมไฟล์เป็นไฟล์เดียวในแต่ละรายการ</li>
                    </ul>
                </div>
            </div>

            <?php
            // --- Dashboard Data Calculation ---
            $typeplan_map_for_dashboard = [
                'บันทึกตรวจใช้แผน' => 'บันทึกตรวจใช้แผน',
                'แบบตรวจแผนการจัดการเรียนรู้' => 'แบบตรวจแผนฯ',
                'โครงการสอน' => 'โครงการสอน',
                'แผนการสอนหน้าเดียว' => 'แผนการสอน',
                'บันทึกหลังสอน' => 'บันทึกหลังสอน'
            ];

            $total_plans = count($planNew) * count($typeplan_map_for_dashboard);
            $submitted_count = 0;
            $dept_head_approved_count = 0;
            $curriculum_head_approved_count = 0;
            $revision_count = 0;
            $plan_type_submitted_count = array_fill_keys(array_keys($typeplan_map_for_dashboard), 0);

            foreach ($plan as $p) {
                if (!empty($p->seplan_file)) {
                    $submitted_count++;
                    if (isset($plan_type_submitted_count[$p->seplan_typeplan])) {
                        $plan_type_submitted_count[$p->seplan_typeplan]++;
                    }
                }
                if (trim($p->seplan_status1) == 'ผ่าน') {
                    $dept_head_approved_count++;
                }
                if (trim($p->seplan_status2) == 'ผ่าน') {
                    $curriculum_head_approved_count++;
                }
                if (trim($p->seplan_status1) == 'ไม่ผ่าน' || trim($p->seplan_status2) == 'ไม่ผ่าน') {
                    $revision_count++;
                }
            }
            ?>

            <!-- Dashboard Section -->
            <div class="row">
                <!-- Countdown -->
                <div class="col-lg-12 mb-4">
                    <div class="card <?= $is_system_on ? 'bg-success' : 'bg-danger'; ?> text-white h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                             <h5 class="card-title">กำหนดส่งแผนการสอน</h5>
                            <?php if ($is_system_on && $deadline): ?>
                                <div id="countdown-timer" data-deadline="<?= esc($deadline) ?>" style="font-size: 2rem; font-weight: bold;"></div>
                            <?php else: ?>
                                <h3>ระบบปิดรับการส่งแผนแล้ว</h3>
                            <?php endif; ?>
                            <div class="small mt-2">สิ้นสุด: <?= $deadline ? thai_date_and_time(strtotime($deadline)) : '-'; ?></div>
                        </div>
                    </div>
                </div>
                <!-- Overall Summary -->
                <div class="col-lg-7 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">สรุปภาพรวม (ปีการศึกษา <?= esc($year); ?>/<?= esc($term); ?>)</h5>
                        </div>
                        <div class="card-body">
                        <div class="row text-center">
                                <div class="col">
                                    <div class="h2 font-weight-bold"><?= esc($total_plans) ?></div>
                                    <div class="text-muted small">ทั้งหมด</div>
                                </div>
                                <div class="col">
                                    <div class="h2 font-weight-bold text-info"><?= esc($submitted_count) ?></div>
                                    <div class="text-muted small">ส่งแล้ว</div>
                                </div>
                                <div class="col">
                                    <div class="h2 font-weight-bold text-success"><?= esc($dept_head_approved_count) ?></div>
                                    <div class="text-muted small">หน.สาระฯ ผ่าน</div>
                                </div>
                                <div class="col">
                                    <div class="h2 font-weight-bold text-primary"><?= esc($curriculum_head_approved_count) ?></div>
                                    <div class="text-muted small">หน.หลักสูตรฯ ผ่าน</div>
                                </div>
                                <div class="col">
                                    <div class="h2 font-weight-bold text-danger"><?= esc($revision_count) ?></div>
                                    <div class="text-muted small">รอแก้ไข</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status by Plan Type -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">สถานะตามประเภทของแผน</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach($typeplan_map_for_dashboard as $db_val => $display_val): ?>
                            <div class="col-lg col-md-4 col-sm-6 mb-3">
                                <strong><?= esc($display_val) ?></strong>
                                <div class="progress" style="height: 20px;">
                                    <?php
                                        $total_subjects_for_type = count($planNew);
                                        $submitted_for_type = $plan_type_submitted_count[$db_val] ?? 0;
                                        $percentage = $total_subjects_for_type > 0 ? ($submitted_for_type / $total_subjects_for_type) * 100 : 0;
                                    ?>
                                    <div class="progress-bar font-weight-bold" role="progressbar" style="width: <?= esc($percentage) ?>%;" aria-valuenow="<?= esc($percentage) ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?= esc($submitted_for_type) ?>/<?= esc($total_subjects_for_type) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between mb-3 align-items-center">
                <button type="button" class="btn btn-outline-secondary me-2" id="changeMainSubjectBtn" style="display: none;"><i class="bi bi-arrow-repeat"></i> เปลี่ยนวิชาหลัก</button>
                <div>
                    <label for="CheckYearSendPlan">เลือกปีการศึกษา:</label>
                    <select class="form-select" id="CheckYearSendPlan" style="width: auto;">
                        <?php foreach ($CheckYearPlan as $v_SelYear) : ?>
                        <option <?= ($year . '/' . $term == $v_SelYear->seplan_year . '/' . $v_SelYear->seplan_term) ? "selected" : "" ?> value="<?= esc($v_SelYear->seplan_year.'/'.$v_SelYear->seplan_term) ?>">
                            <?= esc($v_SelYear->seplan_term.'/'.$v_SelYear->seplan_year) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php
            $planData = [];
            foreach ($plan as $p) {
                $key = $p->seplan_coursecode . '|' . $p->seplan_typeplan . '|' . $p->seplan_year . '|' . $p->seplan_term;
                $planData[$key] = $p;
            }
            $typeplan_map = [
                'บันทึกตรวจใช้แผน' => 'บันทึกตรวจใช้แผน',
                'แบบตรวจแผนการจัดการเรียนรู้' => 'แบบตรวจแผนการจัดการเรียนรู้',
                'โครงการสอน' => 'โครงการสอน',
                'แผนการสอนหน้าเดียว' => 'แผนการสอนหน้าเดียว',
                'บันทึกหลังสอน' => 'บันทึกหลังสอน'
            ];
            ?>

            <div class="row" id="subject-cards-container"> 
                <?php foreach ($planNew as $v_planNew) : ?>
                    <div class="col-12 mb-4" data-course-code="<?= esc($v_planNew->seplan_coursecode) ?>" data-is-main-subject="<?= esc($v_planNew->seplan_is_main_subject ?? 0) ?>">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <strong><?= esc($v_planNew->seplan_coursecode) ?></strong> - <?= esc($v_planNew->seplan_namesubject) ?>
                                    <small class="">(ชั้น ม.<?= esc($v_planNew->seplan_gradelevel) ?> | <?= esc($v_planNew->seplan_typesubject) ?>)</small>
                                </h5>
                            </div>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($typeplan_map as $db_val => $display_val) : ?>
                                    <?php
                                    $lookupKey = $v_planNew->seplan_coursecode . '|' . $db_val . '|' . $v_planNew->seplan_year . '|' . $v_planNew->seplan_term;
                                    $v_plan = $planData[$lookupKey] ?? null;
                                    ?>
                                    <li class="list-group-item justify-content-between align-items-center" data-typeplan="<?= esc($db_val) ?>"
                                        style="<?php
                                            // If it's the main subject, show all document types
                                            if (($v_planNew->seplan_is_main_subject ?? 0) == 1) {
                                                echo 'display: flex;';
                                            } else {
                                                // If it's not the main subject, only show 'โครงการสอน'
                                                echo ($db_val === 'โครงการสอน') ? 'display: flex;' : 'display: none !important;';
                                            }
                                        ?>">
                                        <div>
                                            <strong><?= esc($display_val) ?></strong>
                                            <div class="d-flex align-items-center small text-muted mt-1">
                                            <?php if ($v_plan && !empty($v_plan->seplan_file)) : ?>
                                                <span class="badge bg-success">ส่งแล้ว</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">ยังไม่ส่ง</span>
                                            <?php endif; ?>
                                            <?php if ($v_plan) : ?>
                                                <span class="ms-3">หน.กลุ่มสาระฯ:
                                                <?php
                                                    if ($v_plan->seplan_status1 == 'ผ่าน') {
                                                        echo '<span class="badge bg-success">ผ่าน</span>';
                                                    } elseif ($v_plan->seplan_status1 == 'ไม่ผ่าน') {
                                                        echo '<span class="badge bg-danger" title="' . esc($v_plan->seplan_comment1) . '">ไม่ผ่าน</span>';
                                                    } else {
                                                        echo '<span class="badge bg-light text-dark">รอตรวจ</span>';
                                                    }
                                                ?>
                                                </span>
                                                <span class="ms-3">หน.หลักสูตรฯ:
                                                <?php
                                                    if ($v_plan->seplan_status2 == 'ผ่าน') {
                                                        echo '<span class="badge bg-success">ผ่าน</span>';
                                                    } elseif ($v_plan->seplan_status2 == 'ไม่ผ่าน') {
                                                        echo '<span class="badge bg-danger" title="' . esc($v_plan->seplan_comment2) . '">ไม่ผ่าน</span>';
                                                    } else {
                                                        echo '<span class="badge bg-light text-dark">รอตรวจ</span>';
                                                    }
                                                ?>
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="btn-group">
                                            <?php if ($v_plan && $v_plan->seplan_file) : ?>
                                                <?php
                                                $file_ext = strtolower(pathinfo($v_plan->seplan_file, PATHINFO_EXTENSION));
                                                $file_icon = 'bi-file-earmark';
                                                if ($file_ext == 'pdf') $file_icon = 'bi-file-earmark-pdf-fill text-danger';
                                                elseif (in_array($file_ext, ['doc', 'docx'])) $file_icon = 'bi-file-earmark-word-fill text-primary';
                                                ?>
                                                <a target="_blank" href="<?= env('upload.server.baseurl') . $v_plan->seplan_year . '/' . $v_plan->seplan_term . '/' . rawurlencode($v_plan->seplan_namesubject) . '/' . rawurlencode($v_plan->seplan_file) ?>" class="btn btn-sm btn-outline-secondary download-plan-btn" title="ดาวน์โหลด: <?= esc($v_plan->seplan_file) ?>"><i class="bi <?= esc($file_icon) ?>"></i></a>
                                            <?php endif; ?>
                                            <?php
                                            $button_class = ($v_plan && $v_plan->seplan_file) ? 'btn-warning' : 'btn-danger';
                                            ?>
                                            <button class="btn btn-sm <?= $button_class ?> Model_update" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#ModalUpdatePlan"
                                                data-seplan-id="<?= esc($v_plan->seplan_ID ?? '') ?>"
                                                data-seplan-coursecode="<?= esc($v_planNew->seplan_coursecode) ?>"
                                                data-seplan-typeplan="<?= esc($db_val) ?>"
                                                data-seplan-sendcomment="<?= esc($v_plan->seplan_sendcomment ?? '') ?>">
                                                <i class="bi bi-upload"></i> <?= $v_plan && $v_plan->seplan_file ? 'แก้ไข' : 'เพิ่ม' ?>
                                            </button>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<!-- Modal Update Plan -->
<div class="modal fade" id="ModalUpdatePlan" tabindex="-1" aria-labelledby="ModalUpdatePlanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalUpdatePlanLabel">อัปโหลดไฟล์แผนการสอน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php if($is_system_on): ?>
            <form class="update_seplan">
                <div class="modal-body">
                    <input type="hidden" id="seplan_ID" name="seplan_ID">
                    <input type="hidden" id="seplan_typeplan" name="seplan_typeplan">
                    <input type="hidden" id="seplan_coursecode" name="seplan_coursecode">
                    <input type="hidden" id="seplan_year" name="seplan_year" value="<?= esc($onOffSetting->seplanset_year ?? '') ?>">
                    <input type="hidden" id="seplan_term" name="seplan_term" value="<?= esc($onOffSetting->seplanset_term ?? '') ?>">

                    <div class="mb-3">
                        <label for="seplan_file" class="form-label">เลือกไฟล์ (เฉพาะ .doc, .docx, .pdf)</label>
                        <input class="form-control" type="file" id="seplan_file" name="seplan_file" accept=".doc,.docx,.pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf" required>
                    </div>
                    <div class="mb-3">
                        <label for="seplan_sendcomment" class="form-label">หมายเหตุ</label>
                        <textarea class="form-control" id="seplan_sendcomment" name="seplan_sendcomment" rows="3" placeholder="เช่น ส่งแผนครบแล้ว หรือ ส่งแผนที่ 1 - 4 แล้ว"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-danger">อัปโหลด</button>
                </div>
            </form>
            <?php else: ?>
            <div class="modal-body">
                <p class="text-danger">ระบบปิดรับส่งแผนแล้ว ไม่สามารถอัปโหลดหรือแก้ไขไฟล์ได้</p>
                <p>กรุณาติดต่อหัวหน้างานหลักสูตร</p>
            </div>
            <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal for Selecting Main Subject -->
<div class="modal fade" id="selectMainSubjectModal" tabindex="-1" aria-labelledby="selectMainSubjectModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selectMainSubjectModalLabel">กรุณาเลือกวิชาหลัก</h5>
            </div>
            <div class="modal-body">
                <p>เลือกรายวิชาที่คุณจะใช้เป็น <strong>วิชาหลัก</strong> สำหรับการส่งแผนการสอนครบทุกรายการในภาคเรียนนี้ (<?= esc($year . '/' . $term) ?>)</p>
                <select class="form-select" id="mainSubjectSelector">
                    <option selected disabled value="">-- กรุณาเลือกวิชา --</option>
                    <?php foreach ($planNew as $v_planNew) : ?>
                        <option value="<?= esc($v_planNew->seplan_coursecode) ?>">
                            <?= esc($v_planNew->seplan_coursecode) ?> - <?= esc($v_planNew->seplan_namesubject) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text mt-2">คุณสามารถเปลี่ยนวิชาหลักได้ในภายหลังผ่านปุ่ม "เปลี่ยนวิชาหลัก"</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmMainSubjectBtn">ยืนยัน</button>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Main Subject Selection Logic ---
    const currentYear = '<?= esc($year) ?>';
    const currentTerm = '<?= esc($term) ?>';
    const currentPersonId = '<?= esc($person_id) ?>'; // Passed from controller
    const selectModalEl = document.getElementById('selectMainSubjectModal');
    const selectModal = new bootstrap.Modal(selectModalEl);
    const mainSubjectSelector = document.getElementById('mainSubjectSelector');
    const confirmBtn = document.getElementById('confirmMainSubjectBtn');
    const changeBtn = document.getElementById('changeMainSubjectBtn');

    function updateUI(mainSubjectCode) {
        if (!mainSubjectCode) return;

        mainSubjectSelector.value = mainSubjectCode;
        
        const subjectCardsContainer = document.getElementById('subject-cards-container');
        const allSubjects = document.querySelectorAll('[data-course-code]');
        let mainSubjectCard = null;

        allSubjects.forEach(card => {
            const currentCode = card.dataset.courseCode;
            const listItems = card.querySelectorAll('[data-typeplan]');
            const cardHeader = card.querySelector('.card-header');
            const cardTitle = card.querySelector('.card-title');
            const isCurrentCardMainSubject = (currentCode === mainSubjectCode); // กำหนดว่าการ์ดนี้เป็นวิชาหลักหรือไม่

            // Reset styles and visibility
            card.style.border = '';
            if(cardHeader) {
                cardHeader.classList.remove('bg-primary', 'text-white');
                cardHeader.classList.add('bg-light');
            }
            const existingBadge = cardTitle.querySelector('.main-subject-badge');
            if(existingBadge) {
                existingBadge.remove();
            }

            // Apply main subject styling if it matches
            if (isCurrentCardMainSubject) {
                mainSubjectCard = card; // เก็บการ์ดวิชาหลัก
            
                if(cardHeader) {
                    cardHeader.classList.remove('bg-light');
                    cardHeader.classList.add('bg-primary', 'text-white');
                }
                cardTitle.insertAdjacentHTML('beforeend', ' <span class="badge bg-warning text-dark main-subject-badge">วิชาหลักการส่งแผน</span>');

                // สำหรับวิชาหลัก ให้แสดงเอกสารทุกประเภท
                listItems.forEach(item => {
                    item.style.display = 'flex';
                });

            } else {
                // สำหรับวิชาที่ไม่ใช่วิชาหลัก ให้แสดงเฉพาะ 'โครงการสอน'
                listItems.forEach(item => {
                    if (item.dataset.typeplan === 'โครงการสอน') {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
        });

        // Move the main subject card to the top if found
        if (mainSubjectCard && subjectCardsContainer) {
            subjectCardsContainer.prepend(mainSubjectCard);
        }

        changeBtn.style.display = 'inline-block';
    }

    function initialize() {
        let mainSubjectCode = null;
        const allSubjects = document.querySelectorAll('[data-course-code]');
        allSubjects.forEach(card => {
            if (card.dataset.isMainSubject === '1') { // Check the new data attribute
                mainSubjectCode = card.dataset.courseCode;
            }
        });

        if (!mainSubjectCode) {
            selectModal.show();
        } else {
            updateUI(mainSubjectCode);
        }
    }

    confirmBtn.addEventListener('click', function() {
        const selectedCode = mainSubjectSelector.value;
        if (selectedCode) {
            // AJAX call to save to database
            fetch('<?= site_url('curriculum/set-main-subject') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' // CodeIgniter 4 expects this for AJAX
                },
                body: JSON.stringify({
                    courseCode: selectedCode,
                    year: currentYear,
                    term: currentTerm,
                    person_id: currentPersonId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('สำเร็จ', data.message, 'success').then(() => {
                        // Reload page to reflect changes from DB
                        location.reload();
                    });
                } else {
                    Swal.fire('ผิดพลาด', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
            });

            selectModal.hide(); // Hide modal immediately, reload will happen on success
        } else {
            alert('กรุณาเลือกวิชาหลักก่อนยืนยัน');
        }
    });

    changeBtn.addEventListener('click', function() {
        selectModal.show();
    });

    initialize();
    // --- End of Main Subject Selection Logic ---


    // Countdown Timer
    const countdownElement = document.getElementById('countdown-timer');
    if (countdownElement) {
        const deadline = new Date(countdownElement.getAttribute('data-deadline').replace(/-/g, '/')).getTime();

        const x = setInterval(function() {
            const now = new Date().getTime();
            const distance = deadline - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            if (distance < 0) {
                clearInterval(x);
                countdownElement.innerHTML = "หมดเวลาส่งแล้ว";
            } else {
                countdownElement.innerHTML = `เหลือเวลา: ${days} วัน ${hours} ชั่วโมง ${minutes} นาที ${seconds} วินาที`;
            }
        }, 1000);
    }

    // Year/Term Selection
    document.getElementById('CheckYearSendPlan').addEventListener('change', function() {
        const selectedYearTerm = this.value;
        window.location.href = `<?= site_url('curriculum/') ?>${selectedYearTerm}`;
    });

    // Modal Update
    const modalUpdatePlan = new bootstrap.Modal(document.getElementById('ModalUpdatePlan'));
    document.querySelectorAll('.Model_update').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('seplan_ID').value = this.dataset.seplanId;
            document.getElementById('seplan_typeplan').value = this.dataset.seplanTypeplan;
            document.getElementById('seplan_coursecode').value = this.dataset.seplanCoursecode;
            document.getElementById('seplan_sendcomment').value = this.dataset.seplanSendcomment;
            // modalUpdatePlan.show(); // This might be an error in original code, let's keep it as is.
        });
    });

    // Form Submission
    const updateForm = document.querySelector('.update_seplan');
    if(updateForm) {
        updateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonHtml = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> กำลังอัปโหลด...';

            const formData = new FormData(this);

            fetch('<?= site_url('curriculum/update-plan') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    submitButton.classList.remove('btn-danger');
                    submitButton.classList.add('btn-warning');
                    submitButton.innerHTML = originalButtonHtml; // Restore text before reload
                    Swal.fire('สำเร็จ', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('ผิดพลาด', data.message, 'error');
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonHtml;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์', 'error');
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonHtml;
            });
        });
    }
    
    // Re-enable showing the modal which might have been a bug in original code
    document.querySelectorAll('.Model_update').forEach(button => {
        button.addEventListener('click', function() {
            // This logic was already present, just ensuring the modal shows
            const modal = new bootstrap.Modal(document.getElementById('ModalUpdatePlan'));
            document.getElementById('seplan_ID').value = this.dataset.seplanId;
            document.getElementById('seplan_typeplan').value = this.dataset.seplanTypeplan;
            document.getElementById('seplan_coursecode').value = this.dataset.seplanCoursecode;
            document.getElementById('seplan_sendcomment').value = this.dataset.seplanSendcomment;
            modal.show();
        });
    });
});
</script>
<?= $this->endSection() ?>
