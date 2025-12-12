<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'แผนการสอน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>




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

            <!-- Alert for System Status -->
             <?php if($is_system_on): ?>
            <div class="alert alert-success d-flex align-items-center justify-content-between" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2 lead"></i>
                    <div>
                        <strong>แจ้งเตือน!</strong> ระบบเปิดให้ส่งงาน
                        <strong> (สิ้นสุด: <?= $deadline ? thai_date_and_time(strtotime($deadline)) : '-' ?>)</strong>
                    </div>
                </div>
                 <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#usageGuideModal">
                    <i class="bi bi-info-circle me-1"></i> คู่มือการใช้งาน
                </button>
            </div>
            
            <?php else: ?>
            <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
                <div class="d-flex align-items-center">
                     <i class="bi bi-exclamation-triangle-fill me-2 lead"></i>
                    <div>
                        <strong>แจ้งเตือน!</strong> ขณะนี้ระบบปิดรับส่งแผนการสอน
                    </div>
                </div>
                 <button type="button" class="btn btn-outline-danger btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#usageGuideModal">
                    <i class="bi bi-info-circle me-1"></i> คู่มือการใช้งาน
                </button>
            </div>
            <?php endif; ?>

            <?php
            // --- Dashboard Data Calculation ---
            // Use active plan types passed from the controller
            $all_plan_types = array_column($activePlanTypes, 'type_name');

            $total_plans = count($planNew) * count($all_plan_types); // This might need adjustment based on actual required types per subject
            $submitted_count = 0;
            $dept_head_approved_count = 0;
            $curriculum_head_approved_count = 0;
            $revision_count = 0;
            $plan_type_submitted_count = array_fill_keys($all_plan_types, 0); // Initialize with fetched types

            foreach ($plan as $p) {
                if (!empty($p->seplan_file)) {
                    $submitted_count++;
                    if (isset($plan_type_submitted_count[$p->type_name])) { // Use type_name
                        $plan_type_submitted_count[$p->type_name]++;
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
            $groupedPlans = [];
            foreach ($plan as $p) {
                $groupedPlans[$p->seplan_coursecode][] = $p;
            }
            // No need for $typeplan_map anymore, we iterate directly over groupedPlans
            ?>

            <div class="row" id="subject-cards-container"> 
                <?php foreach ($planNew as $v_planNew) : // This loop is for course headers ?>
                    <div class="col-12 mb-4" data-course-code="<?= esc($v_planNew->seplan_coursecode) ?>" data-is-main-subject="<?= esc($v_planNew->seplan_is_main_subject ?? 0) ?>">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <strong><?= esc($v_planNew->seplan_coursecode) ?></strong> - <?= esc($v_planNew->seplan_namesubject) ?>
                                    <small class="">(ชั้น ม.<?= esc($v_planNew->seplan_gradelevel) ?> | <?= esc($v_planNew->seplan_typesubject) ?>)</small>
                                </h5>
                            </div>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ประเภทเอกสาร</th>
                                            <th>สถานะส่ง</th>
                                            <th>หน.กลุ่มสาระฯ</th>
                                            <th>หน.หลักสูตรฯ</th>
                                            <th>จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        <?php foreach ($groupedPlans[$v_planNew->seplan_coursecode] as $v_plan) : // Iterate over actual plan items for this course ?>
                                            <?php if (in_array($v_plan->type_name, $all_plan_types)) : // Only display if type is active ?>
                                            <tr data-typeplan="<?= esc($v_plan->type_name) ?>"
                                                style="<?php
                                                    // If it's the main subject, show all document types
                                                    if (($v_planNew->seplan_is_main_subject ?? 0) == 1) {
                                                        echo ''; // No inline style needed, it will be visible by default
                                                    } else {
                                                        // If it's not the main subject, only show 'โครงการสอน'
                                                        echo ($v_plan->type_name === 'โครงการสอน') ? '' : 'display: none !important;';
                                                    }
                                                ?>">
                                                <td><strong><?= esc($v_plan->type_name) ?></strong></td>
                                                <td>
                                                    <?php if ($v_plan && !empty($v_plan->seplan_file)) : ?>
                                                        <span class="badge bg-success">ส่งแล้ว</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">ยังไม่ส่ง</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($v_plan) : ?>
                                                        <?php
                                                            if (trim($v_plan->seplan_status1) == 'ผ่าน') {
                                                                echo '<span class="badge bg-success">ผ่าน</span>';
                                                            } elseif (trim($v_plan->seplan_status1) == 'ไม่ผ่าน') {
                                                                echo '<span class="badge bg-danger" title="' . esc($v_plan->seplan_comment1) . '">ไม่ผ่าน</span>';
                                                            } else {
                                                                echo '<span class="badge bg-warning">รอตรวจ</span>';
                                                            }
                                                        ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($v_plan) : ?>
                                                        <?php
                                                            if (trim($v_plan->seplan_status2) == 'ผ่าน') {
                                                                echo '<span class="badge bg-success">ผ่าน</span>';
                                                            } elseif (trim($v_plan->seplan_status2) == 'ไม่ผ่าน') {
                                                                echo '<span class="badge bg-danger" title="' . esc($v_plan->seplan_comment2) . '">ไม่ผ่าน</span>';
                                                            } else {
                                                                echo '<span class="badge bg-warning">รอตรวจ</span>';
                                                            }
                                                        ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
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
                                                            data-seplan-typeplan="<?= esc($v_plan->type_name) ?>"
                                                            data-seplan-sendcomment="<?= esc($v_plan->seplan_sendcomment ?? '') ?>">
                                                            <i class="bi bi-upload"></i> <?= $v_plan && $v_plan->seplan_file ? 'แก้ไข' : 'เพิ่ม' ?>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>


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


<!-- Modal Usage Guide -->
<div class="modal fade" id="usageGuideModal" tabindex="-1" aria-labelledby="usageGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-label-primary">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded-circle bg-primary"><i class='bx bx-book-open fs-4'></i></span>
                    </div>
                    <h4 class="modal-title text-primary fw-bold" id="usageGuideModalLabel">คู่มือและขั้นตอนการส่งแผนการสอน</h4>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 p-md-5 bg-light-subtle">
                <div class="alert alert-primary d-flex align-items-center mb-4 shadow-sm" role="alert">
                    <i class="bi bi-info-circle-fill me-3 fs-3"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1">คำแนะนำเบื้องต้น</h6>
                        <span>ตรวจสอบรายวิชาที่รับผิดชอบให้ครบถ้วน หากมีข้อสงสัยกรุณาติดต่อเจ้าหน้าที่งานหลักสูตร</span>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                     <!-- Step 1 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                            <div class="card-body text-center p-4">
                                <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-label-primary mb-3 position-relative">
                                    <i class="bi bi-check2-circle fs-2 text-primary"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light shadow-sm">1</span>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">เลือกวิชาหลัก</h5>
                                <p class="text-secondary small mb-0">
                                    กำหนด 1 รายวิชาเป็น <strong>"วิชาหลัก"</strong> สำหรับส่งเอกสารครบ 5 รายการ (วิชาอื่นส่งเฉพาะโครงการสอน)
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                            <div class="card-body text-center p-4">
                                <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-label-warning mb-3 position-relative">
                                    <i class="bi bi-file-earmark-pdf fs-2 text-warning"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light shadow-sm">2</span>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">เตรียมไฟล์</h5>
                                <p class="text-secondary small mb-0">
                                    รวบรวมเอกสารแต่ละหัวข้อให้เป็น <strong>1 ไฟล์ .PDF</strong> (หรือ Word) เพื่อความสะดวกในการตรวจ
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                            <div class="card-body text-center p-4">
                                <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-label-info mb-3 position-relative">
                                    <i class="bi bi-cloud-arrow-up fs-2 text-info"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light shadow-sm">3</span>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">อัปโหลด</h5>
                                <p class="text-secondary small mb-0">
                                    คลิกปุ่ม <span class="badge bg-danger shadow-sm">เพิ่ม</span> หรือ <span class="badge bg-warning text-dark shadow-sm">แก้ไข</span> เพื่ออัปโหลดไฟล์เข้าระบบ
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Step 4 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                            <div class="card-body text-center p-4">
                                <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-label-success mb-3 position-relative">
                                    <i class="bi bi-clock-history fs-2 text-success"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light shadow-sm">4</span>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">รอตรวจสอบ</h5>
                                <p class="text-secondary small mb-0">
                                    สถานะจะเปลี่ยนเป็น <span class="badge bg-success shadow-sm">ผ่าน</span> เมื่อได้รับการอนุมัติจากหัวหน้ากลุ่มฯ และงานหลักสูตร
                                </p>
                            </div>
                        </div>
                    </div>
                </div>



            </div>
            <div class="modal-footer bg-light-subtle">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
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
                    cardHeader.classList.add('bg-primary');
                    cardHeader.classList.add('text-white');
                    cardTitle.classList.add('text-white');
                }
                cardTitle.insertAdjacentHTML('beforeend', ' <span class="badge bg-warning text-dark main-subject-badge">วิชาหลักการส่งแผน</span>');

                // สำหรับวิชาหลัก ให้แสดงเอกสารทุกประเภท
                listItems.forEach(item => {
                    item.style.display = ''; // Revert to default display
                });

            } else {
                // สำหรับวิชาที่ไม่ใช่วิชาหลัก ให้แสดงเฉพาะ 'โครงการสอน'
                listItems.forEach(item => {
                    if (item.dataset.typeplan === 'โครงการสอน') {
                        item.style.display = ''; // Revert to default display
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

    // --- Modal Handling --- 
    const modalUpdatePlanEl = document.getElementById('ModalUpdatePlan');
    const modalUpdatePlan = new bootstrap.Modal(modalUpdatePlanEl);

    // Populate modal with data when triggered
    document.querySelectorAll('.Model_update').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('seplan_ID').value = this.dataset.seplanId;
            document.getElementById('seplan_typeplan').value = this.dataset.seplanTypeplan;
            document.getElementById('seplan_coursecode').value = this.dataset.seplanCoursecode;
            document.getElementById('seplan_sendcomment').value = this.dataset.seplanSendcomment;
            modalUpdatePlan.show();
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
                modalUpdatePlan.hide(); // Hide modal on response
                if (data.status === 'success') {
                    Swal.fire('สำเร็จ', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('ผิดพลาด', data.message, 'error');
                }
            })
            .catch(error => {
                modalUpdatePlan.hide(); // Hide modal on error
                console.error('Error:', error);
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์', 'error');
            })
            .finally(() => {
                // Restore button state regardless of outcome
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonHtml;
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
