<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row g-4">
    <!-- Page Loader Wrap -->
    <div id="page-loader-wrapper">
        <div class="loader-container">
            <svg class="loader-circle" viewBox="0 0 150 150">
                <circle class="loader-circle-bg" cx="75" cy="75" r="70"></circle>
                <circle class="loader-circle-progress" cx="75" cy="75" r="70"></circle>
            </svg>
            <div class="loader-percentage">0%</div>
        </div>
        <div class="loader-time mt-2 mb-1">0.00s</div>
        <div class="loader-text fw-bold text-uppercase">กำลังประมวลผลข้อมูล...</div>
        <div class="mt-1 small text-muted">ระบบกำลังดึงข้อมูลนักเรียนและจัดเตรียมหน้าบันทึกคะแนน</div>
    </div>

    <!-- Header Hero Section -->
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-label-primary overflow-hidden">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-md-8 p-4 d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center mb-2">
                             <div class="badge bg-primary me-2 rounded-pill px-3 py-2">
                                <i class="bi bi-book-half me-1"></i> <?= esc($check_student[0]->SubjectCode) ?>
                            </div>
                            <span class="text-muted">ปีการศึกษา <?= esc($check_student[0]->RegisterYear) ?></span>
                        </div>
                        <h3 class="mb-2 text-primary fw-bold"><?= esc($check_student[0]->SubjectName) ?></h3>
                        <div class="d-flex flex-wrap gap-3 mt-2">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-award me-1 text-info"></i>
                                <span>หน่วยกิต: <strong><?= $check_student[0]->SubjectUnit ?></strong></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock me-1 text-warning"></i>
                                <span>ชั่วโมง/สัปดาห์: <strong><?= $check_student[0]->SubjectHour ?></strong></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 bg-primary d-flex align-items-center justify-content-center p-4 text-white">
                        <div class="text-center">
                            <?php if (!empty($set_score)) : ?>
                                <button type="button" subject-id="<?= esc($check_student[0]->SubjectID) ?>" 
                                        class="btn btn-white text-primary shadow-sm hover-elevate mb-2 btn-lg w-100 btn-check-score" 
                                        data-bs-toggle="modal" data-bs-target="#myModal">
                                    <i class="bi bi-gear-fill me-2"></i> ตั้งค่าคะแนนเก็บ
                                </button>
                            <?php endif; ?>
                            <p class="mb-0 opacity-75 small text-nowrap">คลิกเพื่อจัดการสัดส่วนคะแนน</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center border-bottom pb-4">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="avatar bg-label-info p-2 me-3 rounded">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">รายชื่อนักเรียนและบันทึกคะแนน</h5>
                        <small class="text-muted">ผลการเรียนจะถูกบันทึกอัตโนมัติเมื่อมีการแก้ไข</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="form-floating">
                        <select name="check_room" id="check_room" class="form-select border-info" style="min-width: 150px;">
                            <option value="all" <?= ($Room == 'all' ? 'selected' : '') ?>>ทั้งหมด</option>
                            <?php
                            foreach ($check_room as $v_check_room) :
                                $sub_doc = explode('.', $v_check_room->StudentClass);
                                $sub_room = explode('/', $sub_doc[1]);
                                $all_room = $sub_room[0] . '-' . $sub_room[1];
                            ?>
                                <option <?= $Room == $all_room ? "selected" : "" ?> value="<?= $all_room; ?>"><?= esc($v_check_room->StudentClass); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="check_room"><i class="bi bi-door-open me-1"></i> เลือกห้องเรียน</label>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <?php if (!empty($set_score)) : ?>
                    <?php
                    $timeNum = match (floatval($check_student[0]->SubjectUnit)) {
                        0.5 => 20, 1.0 => 40, 1.5 => 60, 2.0 => 80,
                        2.5 => 100, 3.0 => 120, 3.5 => 140, 4.0 => 160,
                        4.5 => 180, 5.0 => 200, default => 0,
                    };
                    ?>
                    <form class="form_score">
                        <!-- Redundant data moved to top level of form -->
                        <input type="hidden" name="SubjectID" value="<?= esc($check_student[0]->SubjectID) ?>">
                        <input type="hidden" name="RegisterYear" value="<?= esc($check_student[0]->RegisterYear) ?>">
                        <input type="hidden" name="TimeNum" value="<?= $timeNum ?>">
                        
                        <div class="table-container-fixed">
                            <table id="tb_score" class="table table-hover align-middle mb-0">
                                <thead class="bg-light sticky-top" style="z-index: 10;">
                                    <tr>
                                        <th rowspan="2" class="text-center py-4">ห้อง</th>
                                        <th rowspan="2" class="text-center">เลขที่</th>
                                        <th rowspan="2" class="text-center">เลขประจำตัว</th>
                                        <th rowspan="2" class="ps-4" style="min-width: 250px; max-width: 300px;">ชื่อ - นามสกุล</th>

                                        <th rowspan="2" class="text-center text-info">เวลาเรียน<br><span class="badge bg-label-info opacity-75">/<?= $timeNum ?></span></th>
                                        <?php
                                        $sum_scoer = 0;
                                        foreach ($set_score as $v_set_score) {
                                            $sum_scoer += $v_set_score->regscore_score;
                                        }
                                        ?>
                                        <th colspan="<?= count($set_score) ?>" class="text-center border-start border-end">การประเมินผลการเรียน</th>
                                        <th rowspan="2" class="text-center fw-bold text-primary">รวม<br><span class="badge bg-label-primary">/<?= $sum_scoer ?></span></th>
                                        <th rowspan="2" class="text-center fw-bold">เกรด</th>
                                        <th rowspan="2" class="text-center">สถานะ</th>
                                    </tr>
                                    <tr class="bg-light shadow-sm">
                                        <?php foreach ($set_score as $v_set_score) : ?>
                                            <th class="text-center border-start py-3 fs-tiny">
                                                <small class="d-block text-muted text-uppercase"><?= esc($v_set_score->regscore_namework) ?></small>
                                                <span class="text-primary fw-bold">/<?= esc($v_set_score->regscore_score) ?></span>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($check_student as $v_check_student) : ?>
                                        <?php if ($v_check_student->Grade_Type != "") : ?>
                                            <tr class="bg-label-warning opacity-75 border-bottom">
                                                <td class="text-center"><?= esc($v_check_student->StudentClass) ?></td>
                                                <td class="text-center"><?= esc($v_check_student->StudentNumber) ?></td>
                                                <td class="text-center font-monospace small"><?= esc($v_check_student->StudentCode) ?></td>
                                                <td class="ps-4 fw-medium text-dark"><?= esc($v_check_student->StudentPrefix . $v_check_student->StudentFirstName . ' ' . $v_check_student->StudentLastName) ?></td>
                                                <td colspan="<?= count($set_score) + 3 ?>" class="text-center py-3">
                                                    <span class="badge bg-warning p-2">
                                                        <i class="bi bi-clock-history me-1"></i> นักเรียน เรียนซ้ำ (ผ่านระบบอื่น)
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge <?= $v_check_student->StudentBehavior == 'ปกติ' ? 'bg-label-success' : 'bg-label-danger text-danger' ?>"><?= esc($v_check_student->StudentBehavior) ?></span>
                                                </td>
                                            </tr>
                                        <?php else : ?>
                                            <tr class="border-bottom">
                                                <td class="text-center text-muted small"><?= esc($v_check_student->StudentClass) ?></td>
                                                <td class="text-center fw-bold"><?= esc($v_check_student->StudentNumber) ?></td>
                                                <td class="text-center text-muted font-monospace small"><?= esc($v_check_student->StudentCode) ?></td>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-dark mb-0"><?= esc($v_check_student->StudentPrefix . $v_check_student->StudentFirstName . ' ' . $v_check_student->StudentLastName) ?></div>
                                                    <input type="hidden" name="StudentID[]" value="<?= esc($v_check_student->StudentID) ?>">
                                                </td>
                                                <td class="text-center">
                                                    <div class="score-input-wrapper">
                                                        <input type="text" class="form-control study_time KeyEnter text-center fw-bold bg-label-info border-transparent" 
                                                               check-time="<?= $timeNum ?>" name="study_time[]" 
                                                               value="<?= esc($v_check_student->StudyTime) ?>" autocomplete="off">
                                                    </div>
                                                </td>
                                                <?php
                                                $scores = explode("|", $v_check_student->Score100);
                                                foreach ($set_score as $key => $v_set_score) :
                                                    $onoff_status = 'on'; // default
                                                    foreach ($onoff_savescore as $o) {
                                                        if (stripos($o->onoff_name, $v_set_score->regscore_namework) !== false) {
                                                            $onoff_status = $o->onoff_status;
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                    <td class="text-center border-start">
                                                        <div class="score-input-wrapper">
                                                            <input type="text" class="form-control check_score KeyEnter text-center fw-bold <?= $onoff_status == "off" ? "bg-light opacity-50 cursor-not-allowed" : "border-transparent focus-ring shadow-none" ?>" 
                                                                   check-score-key="<?= esc($v_set_score->regscore_score) ?>" 
                                                                   name="<?= esc($v_check_student->StudentID) ?>[]" 
                                                                   value="<?= esc($scores[$key] ?? '') ?>" <?= $onoff_status == "off" ? "readonly" : "" ?> autocomplete="off">
                                                        </div>
                                                    </td>
                                                <?php endforeach; ?>
                                                <td class="text-center fw-bold text-primary subtot fs-5"></td>
                                                <?php
                                                // Calculate initial grade based on study time
                                                $studyTimeVal = $v_check_student->StudyTime ?? '';
                                                $minTimeRequired = $timeNum * 0.8; // 80% of total time
                                                $initialGrade = 'มส'; // Default to มส
                                                $gradeClass = 'bg-label-danger';
                                                
                                                // Only show grade if study time is filled AND >= 80%
                                                if ($studyTimeVal !== '' && floatval($studyTimeVal) >= $minTimeRequired) {
                                                    $initialGrade = '-';
                                                    $gradeClass = 'bg-label-primary';
                                                }
                                                ?>
                                                <td class="text-center grade">
                                                    <span class="grade-badge badge <?= $gradeClass ?> fs-6 fw-bold"><?= $initialGrade ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="status-badge badge <?= $v_check_student->StudentBehavior == 'ปกติ' ? 'bg-label-success' : 'bg-label-danger' ?>"><?= esc($v_check_student->StudentBehavior) ?></span>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Floating Action Bar -->
                        <div class="sticky-bottom bg-white border-top p-3 shadow-lg d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow hover-elevate">
                                <i class="bi bi-save-fill me-2"></i> บันทึกข้อมูลและยืนยันคะแนน
                            </button>
                        </div>
                    </form>
                <?php else : ?>
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-danger rounded-circle mb-3 mx-auto">
                            <i class="bi bi-shield-exclamation fs-1"></i>
                        </div>
                        <h4 class="text-danger">ยังไม่ได้ตั้งค่าสัดส่วนคะแนน!</h4>
                        <p class="text-muted mb-4">กรุณาตั้งค่าคะแนนเก็บสำหรับรายวิชานี้ก่อนเริ่มการบันทึกคะแนน</p>
                        <button type="button" subject-id="<?= esc($check_student[0]->SubjectID) ?>" 
                                class="btn btn-primary shadow px-5 btn-check-score" data-bs-toggle="modal" data-bs-target="#myModal">
                            <i class="bi bi-gear-fill me-2"></i> ไปหน้าตั้งค่าคะแนน
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Set Score -->
<div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form class="form_set_score">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title text-white"><i class="bi bi-gear-wide-connected me-2"></i> ตั้งสัดส่วนคะแนน (100 คะแนน)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input id="before_middle_score" name="before_middle_score" type="text" class="form-control score fw-bold border-2" placeholder=" " required>
                                <label for="before_middle_score">คะแนนก่อนกลางภาค</label>
                                <input name="before_middle" type="hidden" value="ก่อนกลางภาค">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input id="test_midterm_score" type="text" name="test_midterm_score" class="form-control score fw-bold border-2" placeholder=" " required>
                                <label for="test_midterm_score">คะแนนสอบกลางภาค</label>
                                <input name="test_midterm" type="hidden" value="สอบกลางภาค">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input id="after_midterm_score" name="after_midterm_score" type="text" class="form-control score fw-bold border-2" placeholder=" " required>
                                <label for="after_midterm_score">คะแนนหลังกลางภาค</label>
                                <input name="after_midterm" type="hidden" value="หลังกลางภาค">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input id="final_exam_score" name="final_exam_score" type="text" class="form-control score fw-bold border-2" placeholder=" " required>
                                <label for="final_exam_score">คะแนนสอบปลายภาค</label>
                                <input name="final_exam" type="hidden" value="สอบปลายภาค">
                            </div>
                        </div>
                        <div class="col-12 border-top pt-3 mt-4">
                            <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
                                <span class="fw-bold fs-5">รวมคะแนนทั้งหมด:</span>
                                <div class="d-flex align-items-center">
                                    <input id="sum" type="text" name="sum" class="form-control form-control-lg border-0 bg-transparent text-end fw-bold fs-4" style="width: 100px;" readonly>
                                    <span class="fs-4 fw-bold">/ 100</span>
                                </div>
                            </div>
                            <div id="sum-error" class="text-danger small mt-2 d-none">* คะแนนรวมต้องมียอดรวมเท่ากับ 100 คะแนนเท่านั้น</div>
                        </div>
                    </div>
                    <input id="regscore_subjectID" type="hidden" name="regscore_subjectID" value="<?= esc($check_student[0]->SubjectID); ?>">
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary px-5 shadow">
                        <i class="bi bi-save me-2"></i> บันทึกสัดส่วนคะแนน
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Auto zoom for better visibility on large screens */
    body {
        zoom: 90%;
    }
    
    /* Premium Page Loader */
    #page-loader-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(15px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        transition: all 0.5s cubic-bezier(0.645, 0.045, 0.355, 1);
    }
    #page-loader-wrapper.loaded {
        opacity: 0;
        visibility: hidden;
        transform: scale(1.1);
    }
    .loader-container {
        position: relative;
        width: 160px;
        height: 160px;
    }
    .loader-circle {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }
    .loader-circle-bg {
        fill: none;
        stroke: #f0f2f4;
        stroke-width: 8;
    }
    .loader-circle-progress {
        fill: none;
        stroke: #696cff;
        stroke-width: 8;
        stroke-linecap: round;
        stroke-dasharray: 440;
        stroke-dashoffset: 440;
        transition: stroke-dashoffset 0.3s ease-out;
    }
    .loader-percentage {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2.2rem;
        font-weight: 800;
        color: #696cff;
        font-family: 'Public Sans', sans-serif;
    }
    .loader-text {
        margin-top: 1rem;
        font-size: 0.9rem;
        color: #696cff;
        letter-spacing: 2px;
    }
    .loader-time {
        font-family: 'Monaco', 'Consolas', monospace;
        font-size: 1.1rem;
        color: #696cff;
        font-weight: 600;
        background: rgba(105, 108, 255, 0.1);
        padding: 4px 12px;
        border-radius: 20px;
    }

    /* Theme Custom Styles */
    .sticky-top { 
        top: 0 !important; 
        background-color: #fff !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        z-index: 1020;
    }
    .table-container-fixed {
        overflow-x: auto;
        overflow-y: hidden;
        width: 100%;
        position: relative;
    }
    .fs-tiny { font-size: 0.65rem; }
    
    .score-input-wrapper {
        position: relative;
        width: 100%;
        max-width: 80px;
        margin: 0 auto;
    }
    
    .check_score, .study_time {
        height: 45px;
        font-size: 1.1rem;
        transition: all 0.2s;
        border: 2px solid transparent;
        background-color: #f8f9fa;
        border-radius: 8px;
        text-align: center !important;
        font-weight: bold;
    }
    
    .check_score:focus, .study_time:focus {
        background-color: #fff !important;
        border-color: #696cff !important;
        box-shadow: 0 0.125rem 0.25rem rgba(105, 108, 255, 0.4) !important;
    }
    
    #tb_score thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #e1e4e8;
    }
    
    .hover-elevate:hover { transform: translateY(-2px); transition: transform 0.2s ease; }
    
    .bg-transparent { background-color: transparent !important; }
    .border-transparent { border-color: transparent !important; }
    
    .cursor-not-allowed { cursor: not-allowed; }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // --- Real-time Page Loader Logic ---
        const startTime = performance.now();
        let targetProgress = 0;
        let currentProgress = 0;

        const timerInterval = setInterval(() => {
            const elapsed = (performance.now() - startTime) / 1000;
            $('.loader-time').text(elapsed.toFixed(2) + 's');
            
            // Increment progress based on current state
            if (document.readyState === 'loading') {
                targetProgress = 30;
            } else if (document.readyState === 'interactive') {
                targetProgress = 70;
            }

            if (currentProgress < targetProgress) {
                currentProgress += 0.5;
                updateLoader(currentProgress);
            }
        }, 30);

        function updateLoader(val) {
            const circle = $('.loader-circle-progress');
            const percentage = $('.loader-percentage');
            // Clamp value between 0 and 100
            val = Math.min(100, Math.max(0, val));
            const offset = 440 - (440 * val) / 100;
            circle.css('stroke-dashoffset', offset);
            percentage.text(Math.round(val) + '%');
        }

        function finishLoader() {
            if ($('#page-loader-wrapper').hasClass('loaded')) return;
            
            clearInterval(timerInterval);
            const totalTime = ((performance.now() - startTime) / 1000).toFixed(2);
            $('.loader-time').text(totalTime + 's');
            
            // Smoothly finish to 100%
            let finishInterval = setInterval(() => {
                if (currentProgress < 100) {
                    currentProgress += 5; // Faster finish
                    updateLoader(currentProgress);
                } else {
                    clearInterval(finishInterval);
                    updateLoader(100);
                    setTimeout(() => {
                        $('#page-loader-wrapper').addClass('loaded');
                        setTimeout(() => { $('#page-loader-wrapper').remove(); }, 600);
                    }, 200);
                }
            }, 10);
        }

        // 1. If window is already loaded, finish immediately
        if (document.readyState === 'complete') {
            finishLoader();
        } else {
            // 2. Wait for window load event
            $(window).on('load', finishLoader);
            
            // 3. Safety Timeout: If it takes more than 5 seconds, force hide (to prevent hanging)
            setTimeout(finishLoader, 5000);
        }

        // Navigation using Arrow Keys
        $(document).on('keydown', '.KeyEnter', function(e) {
            var allInputs = $('input.KeyEnter');
            var currentIndex = allInputs.index(this);
            var numCols = 1 + <?= count($set_score) ?>; // study_time + each set_score column
            
            if (e.keyCode == 37) { // Left
                allInputs.eq(currentIndex - 1).focus().select();
                e.preventDefault();
            } else if (e.keyCode == 39) { // Right
                allInputs.eq(currentIndex + 1).focus().select();
                e.preventDefault();
            } else if (e.keyCode == 38) { // Up
                allInputs.eq(currentIndex - numCols).focus().select();
                e.preventDefault();
            } else if (e.keyCode == 40) { // Down
                allInputs.eq(currentIndex + numCols).focus().select();
                e.preventDefault();
            }
        });

        $(".score").on('keyup input', function() {
            calculateSum();
        });

        function calculateSum() {
            var sum = 0;
            $(".score").each(function() {
                if (!isNaN(this.value) && this.value.length != 0) {
                    sum += parseFloat(this.value);
                }
            });
            $("#sum").val(sum);
            if (sum == 100) {
                $("#sum").parent().addClass("text-success").removeClass("text-danger");
                $("#sum-error").addClass("d-none");
            } else {
                $("#sum").parent().addClass("text-danger").removeClass("text-success");
                $("#sum-error").removeClass("d-none");
            }
        }

        $(document).on('change', '#check_room', function() {
            const baseUrl = '<?= site_url("assessment/save-score-add/" . $uri->getSegment(3) . "/" . $uri->getSegment(4) . "/" . $uri->getSegment(5)) ?>';
            window.location.href = baseUrl + '/' + $(this).val();
        });

        $(document).on('submit', '.form_set_score', function(e) {
            e.preventDefault();
            var form = $(this);
            var submitButton = form.find('button[type="submit"]');
            var originalButtonText = submitButton.html();
            
            if (parseFloat($('#sum').val()) !== 100) {
                Swal.fire({
                    icon: 'warning',
                    title: 'คะแนนรวมไม่ครบ 100',
                    text: 'กรุณาตั้งค่าคะแนนให้รวมกันได้ 100 คะแนนพอดี',
                    confirmButtonText: 'รับทราบ',
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                return;
            }
            
            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> กำลังบันทึก...');
            $.ajax({
                url: '<?= site_url("assessment/save-score/setting-score/") ?>' + form.attr('id'),
                type: "post",
                data: form.serialize(),
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        $('#myModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'ตั้งค่าสำเร็จ',
                            text: 'ระบบได้ปรับเปลี่ยนสัดส่วนคะแนนเรียบร้อยแล้ว',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => { window.location.reload(); });
                    } else {
                        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: data.message });
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้' });
                },
                complete: function() {
                    submitButton.prop('disabled', false).html(originalButtonText);
                }
            });
        });

        $(".check_score, .study_time").on('keyup input', function() {
            calculateRowSum($(this).closest('tr'));
        });

        function calculateRowSum(row) {
            var TimeNum = row.find('.study_time').attr('check-time');
            var sum = 0;
            var study_time = row.find('.study_time').val();
            var Check_ro = 0;

            row.find('.check_score').each(function() {
                var val = $(this).val().toLowerCase();
                if (val == "ร") {
                    Check_ro += 1;
                } else if ($.isNumeric(val)) {
                    sum += parseFloat(val);
                }
            });

            row.find('.subtot').html(sum);
            
            var gradeBadge = row.find('.grade-badge');
            var gradeResult = '';
            var minTimeRequired = parseFloat(TimeNum) * 0.8; // 80% of total time
            var studyTimeNum = parseFloat(study_time);

            // If study_time is empty, undefined, NaN, or less than 80%, show 'มส'
            if (study_time === undefined || study_time === '' || isNaN(studyTimeNum) || studyTimeNum < minTimeRequired) {
                gradeResult = 'มส';
            } else if (Check_ro > 0) {
                gradeResult = 'ร';
            } else {
                gradeResult = check_grade(sum);
            }
            
            gradeBadge.html(gradeResult);
            // Apply different colors based on grade
            gradeBadge.removeClass('bg-label-primary bg-label-success bg-label-danger bg-label-warning');
            if (gradeResult == 'มส' || gradeResult == 'ร' || gradeResult == 0) {
                gradeBadge.addClass('bg-label-danger');
            } else if (gradeResult >= 3) {
                gradeBadge.addClass('bg-label-success');
            } else {
                gradeBadge.addClass('bg-label-primary');
            }
        }

        function check_grade(sum) {
            if (sum > 100 || sum < 0) return "Error";
            if (sum >= 79.5) return 4;
            if (sum >= 74.5) return 3.5;
            if (sum >= 69.5) return 3;
            if (sum >= 64.5) return 2.5;
            if (sum >= 59.5) return 2;
            if (sum >= 54.5) return 1.5;
            if (sum >= 49.5) return 1;
            return 0;
        }

        // Initial Row Calculation
        $('#tb_score tbody tr').each(function() {
            if (!$(this).hasClass('bg-label-warning')) {
                calculateRowSum($(this));
            }
        });

        $(document).on('click', '.btn-check-score', function(e) {
            e.preventDefault();
            var subjectId = $(this).attr('subject-id');
            var $btn = $(this);
            var originalText = $btn.html();
            
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> รอสักครู่...');

            $.ajax({
                url: "<?= site_url('assessment/save-score/edit-score') ?>",
                type: 'POST',
                data: { subid: subjectId },
                dataType: 'json',
                success: function(data) {
                    $('.form_set_score')[0].reset();
                    if (data.status === 'not_found') {
                        $(".form_set_score").attr('id', "form_insert_score");
                    } else {
                        $(".form_set_score").attr('id', "form_update_score");
                        $('#before_middle_score').val(data[0].regscore_score);
                        $('#test_midterm_score').val(data[1].regscore_score);
                        $('#after_midterm_score').val(data[2].regscore_score);
                        $('#final_exam_score').val(data[3].regscore_score);
                        calculateSum();
                    }
                    $('#myModal').modal('show');
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถโหลดข้อมูลการตั้งค่าได้' });
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });

        $(document).on('submit', '.form_score', function(e) {
            e.preventDefault();
            var form = $(this);
            var submitButton = form.find('button[type="submit"]');
            var originalButtonText = submitButton.html();
            var validationFailed = false;

            form.find('tbody tr').each(function() {
                var studentRow = $(this);
                if (studentRow.hasClass('bg-label-warning')) return true;

                var studyTimeInput = studentRow.find('input[name="study_time[]"]');
                var checkScoreInputs = studentRow.find('input.check_score');

                if (studyTimeInput.length > 0) {
                    var enteredStudyTime = parseInt(studyTimeInput.val(), 10);
                    var maxStudyTime = parseInt(studyTimeInput.attr('check-time'), 10);
                    if (!isNaN(enteredStudyTime) && enteredStudyTime > maxStudyTime) {
                        Swal.fire({ icon: 'error', title: 'เวลาเรียนเกิน', text: 'ตรวจสอบเวลาเรียนของ ' + studentRow.find('.fw-bold').first().text() });
                        validationFailed = true; return false;
                    }
                }

                checkScoreInputs.each(function() {
                    var enteredScore = $(this).val();
                    var maxScore = parseInt($(this).attr('check-score-key'), 10);
                    if (enteredScore.toLowerCase() === 'ร') return true;
                    var parsedEnteredScore = parseFloat(enteredScore);
                    if (!isNaN(parsedEnteredScore) && parsedEnteredScore > maxScore) {
                        Swal.fire({ icon: 'error', title: 'คะแนนเกิน', text: 'ตรวจสอบคะแนนของ ' + studentRow.find('.fw-bold').first().text() });
                        validationFailed = true; return false;
                    }
                });
            });

            if (validationFailed) return;

            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> กำลังบันทึกข้อมูลทั้งหมด...');
            $.ajax({
                url: '<?= site_url("assessment/save-score/insert-score") ?>',
                type: "post",
                data: form.serialize(),
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ', text: 'ข้อมูลคะแนนทั้งหมดถูกบันทึกลงระบบแล้ว', timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'ขออภัย', text: 'ไม่สามารถบันทึกข้อมูลได้' });
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้' });
                },
                complete: function() {
                    submitButton.prop('disabled', false).html(originalButtonText);
                }
            });
        });

        const Toast = Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 1500, timerProgressBar: true });

        $(document).on('input', '.check_score, .study_time', function() {
            var inputField = $(this);
            var currentTimeout = inputField.data('autosaveTimeout');
            clearTimeout(currentTimeout);
            
            var enteredValue = parseInt(inputField.val(), 10);
            var maxValue = inputField.hasClass('check_score') ? parseInt(inputField.attr('check-score-key'), 10) : parseInt(inputField.attr('check-time'), 10);

            if (maxValue && !isNaN(enteredValue) && enteredValue > maxValue) {
                Toast.fire({ icon: 'error', title: 'ค่าที่กรอกเกินกำหนด!' });
                inputField.val('0').addClass('is-invalid');
                setTimeout(() => { inputField.removeClass('is-invalid').focus().select(); }, 500);
                return;
            }

            Toast.fire({ icon: 'info', title: 'รอการบันทึกอัตโนมัติ...', timer: 1000 });
            var studentRow = inputField.closest('tr');
            var newTimeout = setTimeout(function() {
                var studentID = studentRow.find('input[name="StudentID[]"]').val();
                var scores = studentRow.find('input[name^="' + studentID + '"]').map(function() { return $(this).val(); }).get();
                var studentData = {
                    StudentID: studentID,
                    SubjectID: $('input[name="SubjectID"]').val(),
                    RegisterYear: $('input[name="RegisterYear"]').val(),
                    TimeNum: $('input[name="TimeNum"]').val(),
                    study_time: studentRow.find('input[name="study_time[]"]').val(),
                    scores: scores
                };
                $.ajax({
                    url: '<?= site_url("assessment/save-score/autosave-score") ?>',
                    type: 'POST',
                    data: studentData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Toast.fire({ icon: 'success', title: 'บันทึกอัตโนมัติสำเร็จ' });
                        }
                    }
                });
            }, 1000);
            inputField.data('autosaveTimeout', newTimeout);
        });
    });
</script>
<?= $this->endSection() ?>