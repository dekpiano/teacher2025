<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <!-- Header Section -->
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="row g-0">
                <div class="col-md-8 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md bg-label-warning rounded me-3">
                            <i class="bi bi-journal-check fs-3"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-dark fw-bold"><?= esc(@$check_student[0]->SubjectCode) ?> <?= esc(@$check_student[0]->SubjectName) ?></h4>
                            <span class="badge bg-label-warning">ระบบบันทึกคะแนนเรียนซ้ำ</span>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-3 text-muted small">
                        <div class="d-flex align-items-center"><i class="bi bi-calendar3 me-1"></i> ปีการศึกษา: <span class="fw-bold text-dark ms-1"><?= esc(@$onoff[0]->onoff_year) ?></span></div>
                        <div class="d-flex align-items-center"><i class="bi bi-list-ol me-1"></i> ครั้งที่: <span class="fw-bold text-dark ms-1"><?= esc(@$onoff[0]->onoff_detail) ?></span></div>
                        <div class="d-flex align-items-center"><i class="bi bi-person me-1"></i> ผู้สอน: <span class="fw-bold text-dark ms-1"><?= esc(@$teacher[0]->pers_prefix . @$teacher[0]->pers_firstname . ' ' . @$teacher[0]->pers_lastname) ?></span></div>
                    </div>
                </div>
                <div class="col-md-4 bg-warning d-flex align-items-center justify-content-center p-4 text-white">
                    <div class="text-center">
                        <?php if (!empty($set_score)) : ?>
                            <button type="button" id="chcek_score" subject-id="<?= esc(@$check_student[0]->SubjectID) ?>" 
                                    class="btn btn-white text-warning shadow-sm hover-elevate mb-2 btn-lg w-100" 
                                    data-bs-toggle="modal" data-bs-target="#myModal">
                                <i class="bi bi-gear-fill me-2"></i> ตั้งค่าคะแนนเก็บ
                            </button>
                        <?php endif; ?>
                        <p class="mb-0 opacity-75 small text-nowrap">คลิกเพื่อจัดการสัดส่วนคะแนนสะสม</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm bg-label-secondary me-2 rounded">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h5 class="card-title mb-0">รายชื่อนักเรียนที่ต้องประเมินผล</h5>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <label for="check_room" class="form-label mb-0 text-muted small"><i class="bi bi-filter"></i> กรองห้อง:</label>
                    <select name="check_room" id="check_room" class="form-select form-select-sm w-auto border-secondary">
                        <option value="all">ทั้งหมด ทุกห้องเรียน</option>
                        <?php
                        foreach ($check_room as $v_check_room) :
                            $sub_doc = explode('.', $v_check_room->StudentClass);
                            if (isset($sub_doc[1])) {
                                $sub_room = explode('/', $sub_doc[1]);
                                $all_room = isset($sub_room[0], $sub_room[1]) ? $sub_room[0] . '-' . $sub_room[1] : $v_check_room->StudentClass;
                            } else {
                                $all_room = $v_check_room->StudentClass;
                            }
                        ?>
                            <option <?= service('uri')->getSegment(4) == $all_room ? "selected" : "" ?> value="<?= $all_room; ?>"><?= esc($v_check_room->StudentClass); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="card-body p-0">
                <?php if (!empty($set_score)) : ?>
                    <?php
                    $timeNum = match (floatval(@$check_student[0]->SubjectUnit)) {
                        0.5 => 20, 1.0 => 40, 1.5 => 60, 2.0 => 80,
                        2.5 => 100, 3.0 => 120, 3.5 => 140, 4.0 => 160,
                        4.5 => 180, 5.0 => 200, default => 0,
                    };
                    ?>
                    <form class="form_score_repeat">
                        <!-- Redundant data moved to top level of form -->
                        <input type="hidden" name="SubjectID" value="<?= esc(@$check_student[0]->SubjectID) ?>">
                        <input type="hidden" name="TimeNum" value="<?= $timeNum ?>">
                        
                        <div class="text-nowrap">
                            <table id="tb_score" class="table table-hover align-middle mb-0">
                                <thead class="bg-light sticky-top" style="z-index: 10;">
                                    <tr>
                                        <th rowspan="2" class="text-center py-4">ห้อง</th>
                                        <th rowspan="2" class="text-center">เลขที่</th>
                                        <th rowspan="2" class="text-center">เลขประจำตัว</th>
                                        <th rowspan="2" class="ps-4">ชื่อ - นามสกุล</th>
                                        <th rowspan="2" class="text-center text-info">เวลาเรียน<br><span class="badge bg-label-info opacity-75">/<?= $timeNum ?></span></th>
                                        <?php
                                        $sum_scoer = 0;
                                        foreach ($set_score as $v_set_score) {
                                            $sum_scoer += $v_set_score->regscore_score;
                                        }
                                        ?>
                                        <th colspan="<?= count($set_score) ?>" class="text-center border-bottom">การประเมินผลการเรียน</th>
                                        <th rowspan="2" class="text-center bg-label-secondary">คะแนนรวม<br><span class="badge bg-secondary opacity-75">/<?= $sum_scoer ?></span></th>
                                        <th rowspan="2" class="text-center">เกรด</th>
                                        <th rowspan="2" class="text-center" style="width: 100px;">พฤติกรรม</th>
                                    </tr>
                                    <tr class="bg-light">
                                        <?php foreach ($set_score as $v_set_score) : ?>
                                            <th class="text-center small py-2 fw-normal fs-tiny">
                                                <?= esc($v_set_score->regscore_namework) ?><br>
                                                <span class="badge bg-label-dark">/<?= esc($v_set_score->regscore_score) ?></span>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($check_student)) : ?>
                                        <?php foreach ($check_student as $v_check_student) : ?>
                                            <?php if ($v_check_student->RepeatStatus != '') : ?>
                                                <tr>
                                                    <td class="text-center text-muted"><?= esc($v_check_student->StudentClass) ?></td>
                                                    <td class="text-center fw-bold"><?= esc($v_check_student->StudentNumber) ?></td>
                                                    <td class="text-center text-muted font-monospace small"><?= esc($v_check_student->StudentCode) ?></td>
                                                    <td class="ps-4">
                                                        <div class="fw-bold text-dark mb-0"><?= esc($v_check_student->StudentPrefix . $v_check_student->StudentFirstName . ' ' . $v_check_student->StudentLastName) ?></div>
                                                        <small class="badge bg-label-secondary border-0 mt-1"><?= esc($v_check_student->Grade_Type) ?></small>
                                                        <input type="hidden" name="StudentID[]" value="<?= esc($v_check_student->StudentID) ?>">
                                                        <input type="hidden" name="RegisterYear[]" value="<?= esc($v_check_student->RegisterYear) ?>">
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="score-input-wrapper">
                                                            <input type="text" class="form-control study_time KeyEnter text-center fw-bold bg-label-info border-transparent" 
                                                                   name="study_time[]" value="<?= esc($v_check_student->StudyTime) ?>" 
                                                                   check-time="<?= $timeNum ?>" autocomplete="off" style="width: 60px;">
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
                                                        <td class="text-center">
                                                            <div class="score-input-wrapper">
                                                                <input type="text" class="form-control check_score KeyEnter text-center fw-bold" 
                                                                       name="<?= esc($v_check_student->StudentID) ?>[]" 
                                                                       value="<?= esc($scores[$key] ?? '') ?>" 
                                                                       check-score-key="<?= esc($v_set_score->regscore_score) ?>"
                                                                       <?= $onoff_status == "off" ? "readonly bg-light" : "" ?> 
                                                                       autocomplete="off" style="width: 60px;">
                                                            </div>
                                                        </td>
                                                    <?php endforeach; ?>
                                                    <td class="text-center bg-label-secondary fw-bold subtot h5 mb-0"></td>
                                                    <td class="text-center grade h5 mb-0 fw-black"></td>
                                                    <td class="text-center">
                                                        <span class="badge rounded-pill <?= $v_check_student->StudentBehavior == 'ปกติ' ? 'bg-label-success' : 'bg-label-danger' ?>">
                                                            <?= esc($v_check_student->StudentBehavior) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="12" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center text-muted">
                                                    <i class="bi bi-person-x display-4 mb-2 text-danger"></i>
                                                    <p class="h5">** ไม่มีนักเรียนลงทะเบียนเรียนซ้ำในรายวิชานี้ **</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (!empty($check_student)) : ?>
                            <div class="card-footer bg-light border-top d-flex justify-content-center py-4">
                                <button type="submit" class="btn btn-warning btn-lg shadow px-5 hover-elevate">
                                    <i class="bi bi-save-fill me-2"></i> บันทึกข้อมูลคะแนนทั้งหมด
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                <?php else : ?>
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-danger rounded-circle mb-4 mx-auto">
                            <i class="bi bi-exclamation-octagon fs-1"></i>
                        </div>
                        <h4 class="text-danger">ยังไม่ได้ตั้งค่าสัดส่วนคะแนน!</h4>
                        <p class="text-muted mb-4">กรุณาตั้งค่าคะแนนเก็บสำหรับรายวิชานี้ก่อนเริ่มการบันทึกคะแนน</p>
                        <button type="button" id="chcek_score" subject-id="<?= esc(@$check_student[0]->SubjectID) ?>" 
                                class="btn btn-primary shadow px-5" data-bs-toggle="modal" data-bs-target="#myModal">
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
            <div class="modal-header bg-warning py-3">
                <h5 class="modal-title text-white"><i class="bi bi-gear-fill me-2"></i> ตั้งค่าสัดส่วนคะแนนสะสม</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form_set_score">
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 d-flex mb-4">
                        <i class="bi bi-info-circle me-3 fs-3"></i>
                        <div>
                            <small class="d-block fw-bold">เงื่อนไขการตั้งค่า:</small>
                            <small>ผลรวมของสัดส่วนคะแนนทั้ง 4 ส่วนจะต้องมีค่าเท่ากับ **100 คะแนนพอดี**</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input id="before_middle_score" name="before_middle_score" type="number" step="0.5" class="form-control score border-primary" placeholder="0">
                                <label for="before_middle_score">คะแนนก่อนกลางภาค</label>
                                <input name="before_middle" type="hidden" value="ก่อนกลางภาค">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input id="test_midterm_score" type="number" step="0.5" name="test_midterm_score" class="form-control score border-primary" placeholder="0">
                                <label for="test_midterm_score">คะแนนกลางภาค</label>
                                <input name="test_midterm" type="hidden" value="สอบกลางภาค">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input id="after_midterm_score" name="after_midterm_score" type="number" step="0.5" class="form-control score border-primary" placeholder="0">
                                <label for="after_midterm_score">คะแนนหลังกลางภาค</label>
                                <input name="after_midterm" type="hidden" value="หลังกลางภาค">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input id="final_exam_score" name="final_exam_score" type="number" step="0.5" class="form-control score border-primary" placeholder="0">
                                <label for="final_exam_score">คะแนนปลายภาค</label>
                                <input name="final_exam" type="hidden" value="สอบปลายภาค">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card bg-label-secondary border-0">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">รวมคะแนนทั้งหมด:</h5>
                                        <div class="d-flex align-items-center">
                                            <input id="sum" type="text" name="sum" class="form-control form-control-lg text-center fw-bold border-0 bg-transparent p-0" style="width: 100px; font-size: 1.5rem;" readonly value="0">
                                            <span class="h4 mb-0 ms-1">/ 100</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <input id="regscore_subjectID" type="hidden" name="regscore_subjectID" value="<?= esc(@$check_student[0]->SubjectID); ?>">
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning px-5 shadow-sm">
                        <i class="bi bi-save me-2"></i> บันทึกการตั้งค่า
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Theme Custom Styles */
    .sticky-top { 
        top: 0px !important; 
        background-color: #fff !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .fs-tiny { font-size: 0.65rem; }
    
    .score-input-wrapper {
        position: relative;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.15);
        border-color: #ffc107;
    }

    .fw-black { font-weight: 900 !important; }
    
    .grade { transition: all 0.3s ease; }
    .grade-0 { color: #ea5455; }
    .grade-1 { color: #ff9f43; }
    .grade-4 { color: #28c76f; }

    .hover-elevate:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    
    /* Disable arrows for number input */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    input[type=number] {
      -moz-appearance: textfield;
    }

    .border-transparent { border-color: transparent; }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Arrow Key Navigation
        $(document).on('keydown', '.KeyEnter', function(e) {
            var inputs = $('input.KeyEnter');
            var KeyEn = inputs.index(this);
            var numCols = $(this).closest('tr').find('.KeyEnter').length;

            if (e.keyCode == 37) { // Left
                if (KeyEn > 0) inputs.eq(KeyEn - 1).focus().select();
            }
            if (e.keyCode == 39) { // Right
                if (KeyEn < inputs.length - 1) inputs.eq(KeyEn + 1).focus().select();
            }
            if (e.keyCode == 38) { // Up
                if (KeyEn >= numCols) inputs.eq(KeyEn - numCols).focus().select();
                e.preventDefault();
            }
            if (e.keyCode == 40) { // Down
                if (KeyEn + numCols < inputs.length) inputs.eq(KeyEn + numCols).focus().select();
                e.preventDefault();
            }
        });

        // Room Filter Change
        $(document).on('change', '#check_room', function() {
            const baseUrl = '<?= site_url("assessment/save-score-repeat-add/" . service('uri')->getSegment(3)) ?>';
            window.location.href = baseUrl + '/' + $(this).val();
        });

        // Score Setting Sum Calculation
        $(".score").on('input', function() {
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
                $("#sum").closest('.card').addClass("bg-label-success").removeClass("bg-label-secondary bg-label-danger");
            } else {
                $("#sum").closest('.card').addClass("bg-label-danger").removeClass("bg-label-secondary bg-label-success");
            }
        }

        // Setting Score Ajax
        $(document).on('submit', '.form_set_score', function(e) {
            e.preventDefault();
            var form = $(this);
            var submitButton = form.find('button[type="submit"]');
            var originalButtonText = submitButton.html();

            if (parseFloat($('#sum').val()) !== 100) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ผลรวมคะแนนไม่ถูกต้อง',
                    text: 'กรุณาตรวจสอบให้แน่ใจว่าผลรวมคะแนนเท่ากับ 100 คะแนนพอดี'
                });
                return;
            }

            submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> กำลังบันทึก...');
            $.ajax({
                url: '<?= site_url("assessment/save-score-repeat/setting-score/") ?>' + form.attr('id'),
                type: "post",
                data: form.serialize(),
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        $('#myModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกการตั้งค่าสำเร็จ',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => { window.location.reload(); });
                    } else {
                        Swal.fire({ icon: 'error', title: 'ล้มเหลว', text: data.message });
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ' });
                },
                complete: function() {
                    submitButton.prop('disabled', false).html(originalButtonText);
                }
            });
        });

        // Row Calculation
        $(".check_score, .study_time").on('input', function() {
            calculateRowSum($(this).closest('tr'));
        });

        function calculateRowSum(row) {
            var TimeNum = row.find('.study_time').attr('check-time');
            var sum = 0;
            var study_time = row.find('.study_time').val();
            var hasRo = false;

            row.find('.check_score').each(function() {
                var val = $(this).val().toLowerCase();
                if (val === "ร") {
                    hasRo = true;
                } else if ($.isNumeric(val)) {
                    sum += parseFloat(val);
                }
            });

            row.find('.subtot').text(sum);

            var gradeText = "";
            if (study_time !== '' && (80 * TimeNum / 100) > parseFloat(study_time)) {
                gradeText = "มส";
            } else if (hasRo) {
                gradeText = "ร";
            } else {
                gradeText = checkGrade(sum);
            }

            var $gradeCell = row.find('.grade');
            $gradeCell.text(gradeText);
            
            // Add visual cue for grade
            $gradeCell.removeClass('text-success text-danger text-warning');
            if (gradeText == "0" || gradeText == "มส") $gradeCell.addClass('text-danger');
            else if (gradeText == "ร") $gradeCell.addClass('text-warning');
            else if (parseFloat(gradeText) >= 3) $gradeCell.addClass('text-success');
        }

        function checkGrade(sum) {
            if (sum > 100 || sum < 0) return "-";
            if (sum >= 79.5) return 4;
            if (sum >= 74.5) return 3.5;
            if (sum >= 69.5) return 3;
            if (sum >= 64.5) return 2.5;
            if (sum >= 59.5) return 2;
            if (sum >= 54.5) return 1.5;
            if (sum >= 49.5) return 1;
            return 0;
        }

        // Initialize calculations
        $('#tb_score tbody tr').each(function() {
            calculateRowSum($(this));
        });

        // Edit Score Proportion Helper
        $(document).on('click', '#chcek_score', function(e) {
            e.preventDefault();
            var subjectId = $(this).attr('subject-id');
            var $btn = $(this);
            var originalHtml = $btn.html();

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> กำลังดึงข้อมูล...');

            $.ajax({
                url: "<?= site_url('assessment/save-score-repeat/edit-score') ?>",
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
                complete: function() {
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // Auto Save logic
        const Toast = Swal.mixin({ 
            toast: true, 
            position: 'bottom-end', 
            showConfirmButton: false, 
            timer: 2000, 
            timerProgressBar: true 
        });

        $(document).on('input', '.check_score, .study_time', function() {
            var $input = $(this);
            var val = $input.val();
            var max = parseFloat($input.attr('check-score-key') || $input.attr('check-time'));

            // Basic Validation
            if ($.isNumeric(val) && parseFloat(val) > max) {
                Toast.fire({ icon: 'error', title: 'ค่าที่กรอกเกินกำหนด!' });
                $input.val(0).select();
                calculateRowSum($input.closest('tr'));
                return;
            }

            var currentTimeout = $input.data('autosaveTimeout');
            clearTimeout(currentTimeout);

            var newTimeout = setTimeout(function() {
                var $row = $input.closest('tr');
                var studentID = $row.find('input[name="StudentID[]"]').val();
                
                var studentData = {
                    StudentID: studentID,
                    SubjectID: $('input[name="SubjectID"]').val(),
                    RegisterYear: $row.find('input[name="RegisterYear[]"]').val(),
                    TimeNum: $('input[name="TimeNum"]').val(),
                    study_time: $row.find('input[name="study_time[]"]').val(),
                    scores: $row.find('input[name="' + studentID + '[]"]').map(function() { return $(this).val(); }).get()
                };

                $.ajax({
                    url: '<?= site_url("assessment/save-score-repeat/autosave-score") ?>',
                    type: 'POST',
                    data: studentData,
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            Toast.fire({ icon: 'success', title: 'บันทึกอัตโนมัติสำเร็จ' });
                        }
                    }
                });
            }, 1000);
            $input.data('autosaveTimeout', newTimeout);
        });

        // Manual Save
        $(document).on('submit', '.form_score_repeat', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');
            var originalText = $btn.html();

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> กำลังบันทึก...');

            $.ajax({
                url: '<?= site_url("assessment/save-score-repeat/insert-score") ?>',
                type: "post",
                data: $form.serialize(),
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        Swal.fire({ icon: 'success', title: 'บันทึกข้อมูลเรียบร้อย', showConfirmButton: false, timer: 1500 });
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>