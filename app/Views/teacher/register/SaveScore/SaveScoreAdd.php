<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-book"></i> <?= 'รายวิชา ' . esc($check_student[0]->SubjectCode) . ' ' . esc($check_student[0]->SubjectName) ?>
        </h5>
        <div>
            <?php if (!empty($set_score)) : ?>
                <button type="button" id="chcek_score" subject-id="<?= esc($check_student[0]->SubjectID) ?>" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#myModal">
                    <i class="bi bi-gear"></i> ตั้งค่าคะแนน
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <style>
            .check_score, .study_time {
                width: 60px; /* Adjust as needed for 2-digit numbers */
                display: inline-block;
            }
            /* Ensure table cells don't force extra width */
            #tb_score td, #tb_score th {
                /* white-space: nowrap; */ /* Prevent text wrapping in table headers/cells */
            }
            .col-name {
                white-space: nowrap;
            }
            .col-status {
                width: 80px;
            }
        </style>
        <?php if (!empty($set_score)) : ?>
            <div class="row justify-content-center mb-3">
                <div class="col-md-6 d-flex align-items-center justify-content-end">
                    <label for="check_room" class="form-label me-2 mb-0"><i class="bi bi-door-open"></i> เลือกห้อง</label>
                </div>
                <div class="col-md-6">
                    <select name="check_room" id="check_room" class="form-select w-auto">
                        <option value="all">ทั้งหมด</option>
                        <?php
                        foreach ($check_room as $v_check_room) :
                            $sub_doc = explode('.', $v_check_room->StudentClass);
                            $sub_room = explode('/', $sub_doc[1]);
                            $all_room = $sub_room[0] . '-' . $sub_room[1];
                        ?>
                            <option <?= $uri->getSegment(7) == $all_room ? "selected" : "" ?> value="<?= $all_room; ?>"><?= esc($v_check_room->StudentClass); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="width: 100%;">
                <form class="form_score">
                    <table id="tb_score" class="table table-hover table-bordered">
                        <thead class="text-center table-light" style="position: -webkit-sticky; position: sticky; top: 0; z-index: 2;">
                            <tr>
                                <th rowspan="2">ชั้น</th>
                                <th rowspan="2">เลขที่</th>
                                <th rowspan="2">เลขประจำตัว</th>
                                <th rowspan="2" width="200" class="col-name">ชื่อ - นามสกุล</th>
                                <?php
                                $timeNum = match (floatval($check_student[0]->SubjectUnit)) {
                                    0.5 => 20, 1.0 => 40, 1.5 => 60, 2.0 => 80,
                                    2.5 => 100, 3.0 => 120, 3.5 => 140, 4.0 => 160,
                                    4.5 => 180, 5.0 => 200, default => 0,
                                };
                                ?>
                                <th rowspan="2">เวลาเรียน<br><small>(<?= $timeNum ?> ชั่วโมง)</small></th>
                                <?php
                                $sum_scoer = 0;
                                foreach ($set_score as $v_set_score) {
                                    $sum_scoer += $v_set_score->regscore_score;
                                }
                                ?>
                                <th colspan="<?= count($set_score) ?>">การประเมินผลการเรียน</th>
                                <th rowspan="2">คะแนนรวม<br>(<?= $sum_scoer ?>)</th>
                                <th rowspan="2">เกรด</th>
                                <th rowspan="2" class="col-status">สถานะ</th>
                            </tr>
                            <tr>
                                <?php foreach ($set_score as $v_set_score) : ?>
                                    <th><?= esc($v_set_score->regscore_namework) ?><br>(<?= esc($v_set_score->regscore_score) ?>)</th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($check_student as $v_check_student) : ?>
                                <?php if ($v_check_student->Grade_Type != "") : ?>
                                    <tr class="table-warning">
                                        <td class="align-middle text-center"><?= esc($v_check_student->StudentClass) ?></td>
                                        <td class="align-middle text-center"><?= esc($v_check_student->StudentNumber) ?></td>
                                        <td class="align-middle text-center"><?= esc($v_check_student->StudentCode) ?></td>
                                        <td class="align-middle col-name"><?= esc($v_check_student->StudentPrefix . $v_check_student->StudentFirstName . ' ' . $v_check_student->StudentLastName) ?></td>
                                        <td colspan="<?= count($set_score) + 3 ?>" class="text-danger text-center align-middle">** นักเรียน เรียนซ้ำ **</td>
                                        <td class="align-middle text-center col-status">
                                            <span class="badge <?= $v_check_student->StudentBehavior == 'ปกติ' ? 'bg-label-success' : 'bg-label-danger' ?>"><?= esc($v_check_student->StudentBehavior) ?></span>
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <tr>
                                        <td class="align-middle text-center"><?= esc($v_check_student->StudentClass) ?></td>
                                        <td class="align-middle text-center"><?= esc($v_check_student->StudentNumber) ?></td>
                                        <td class="align-middle text-center"><?= esc($v_check_student->StudentCode) ?></td>
                                        <td class="align-middle col-name"><?= esc($v_check_student->StudentPrefix . $v_check_student->StudentFirstName . ' ' . $v_check_student->StudentLastName) ?></td>
                                        <input type="hidden" name="StudentID[]" value="<?= esc($v_check_student->StudentID) ?>">
                                        <input type="hidden" name="SubjectID" value="<?= esc($check_student[0]->SubjectID) ?>">
                                        <input type="hidden" name="RegisterYear" value="<?= esc($check_student[0]->RegisterYear) ?>">
                                        <input type="hidden" name="TimeNum" value="<?= $timeNum ?>">
                                        <td>
                                            <input type="text" class="form-control study_time KeyEnter text-center" check-time="<?= $timeNum ?>" name="study_time[]" value="<?= esc($v_check_student->StudyTime) ?>" autocomplete="off">
                                        </td>
                                        <?php
                                        $scores = explode("|", $v_check_student->Score100);
                                        foreach ($set_score as $key => $v_set_score) :
                                            $onoff_status = 'on'; // default
                                            foreach ($onoff_savescore as $o) {
                                                if ($o->onoff_name == $v_set_score->regscore_namework) {
                                                    $onoff_status = $o->onoff_status;
                                                    break;
                                                }
                                            }
                                        ?>
                                            <td>
                                                <input type="text" class="form-control check_score KeyEnter text-center" check-score-key="<?= esc($v_set_score->regscore_score) ?>" name="<?= esc($v_check_student->StudentID) ?>[]" value="<?= esc($scores[$key] ?? '') ?>" <?= $onoff_status == "off" ? "readonly" : "" ?> autocomplete="off">
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="align-middle text-center fw-bold subtot"></td>
                                        <td class="align-middle text-center fw-bold grade"></td>
                                        <td class="align-middle text-center col-status">
                                            <span class="badge <?= $v_check_student->StudentBehavior == 'ปกติ' ? 'bg-label-success' : 'bg-label-danger' ?>"><?= esc($v_check_student->StudentBehavior) ?></span>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> บันทึกคะแนน</button>
                    </div>
                </form>
            </div>
        <?php else : ?>
            <div class="text-center">
                <h4 class="text-danger"><i class="bi bi-exclamation-triangle"></i> กรุณาตั้งค่าคะแนนเก็บก่อน</h4>
                <button type="button" id="chcek_score" subject-id="<?= esc($check_student[0]->SubjectID) ?>" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
                    <i class="bi bi-gear"></i> ตั้งค่าคะแนน
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Set Score -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form_set_score">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"><i class="bi bi-gear"></i> ตั้งค่าคะแนน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <label for="before_middle_score" class="col-sm-4 col-form-label">ก่อนกลางภาค</label>
                        <div class="col-sm-8">
                            <input id="before_middle_score" name="before_middle_score" type="text" placeholder="คะแนนที่เก็บ" class="form-control score">
                            <input name="before_middle" type="hidden" value="ก่อนกลางภาค">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="test_midterm_score" class="col-sm-4 col-form-label">สอบกลางภาค</label>
                        <div class="col-sm-8">
                            <input id="test_midterm_score" type="text" name="test_midterm_score" placeholder="คะแนนที่เก็บ" class="form-control score">
                            <input name="test_midterm" type="hidden" value="สอบกลางภาค">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="after_midterm_score" class="col-sm-4 col-form-label">หลังกลางภาค</label>
                        <div class="col-sm-8">
                            <input id="after_midterm_score" name="after_midterm_score" type="text" placeholder="คะแนนที่เก็บ" class="form-control score">
                            <input name="after_midterm" type="hidden" value="หลังกลางภาค">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="final_exam_score" class="col-sm-4 col-form-label">สอบปลายภาค</label>
                        <div class="col-sm-8">
                            <input id="final_exam_score" name="final_exam_score" type="text" placeholder="คะแนนที่เก็บ" class="form-control score">
                            <input name="final_exam" type="hidden" value="สอบปลายภาค">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="sum" class="col-sm-4 col-form-label">รวมคะแนน</label>
                        <div class="col-sm-8">
                            <input id="sum" type="text" name="sum" placeholder="คะแนนรวม" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-text">**หมายเหตุ คะแนนรวมต้องเท่ากับ 100 คะแนน</div>
                    <input id="regscore_subjectID" type="hidden" name="regscore_subjectID" value="<?= esc($check_student[0]->SubjectID); ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> บันทึกคะแนนเก็บ</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        $(document).on('keydown', '.KeyEnter', function(e) {
            var KeyEn = $(this).index('input.KeyEnter');
            if (e.keyCode == 37) {
                KeyEn = KeyEn - 1;
                $('input.KeyEnter:eq(' + KeyEn + ')').focus();
            }
            if (e.keyCode == 39) {
                KeyEn = KeyEn + 1;
                $('input.KeyEnter:eq(' + KeyEn + ')').focus();
            }
            if (e.keyCode == 38) {
                KeyEn = KeyEn - 5;
                $('input.KeyEnter:eq(' + KeyEn + ')').focus();
            }
            if (e.keyCode == 40) {
                KeyEn = KeyEn + 5;
                $('input.KeyEnter:eq(' + KeyEn + ')').focus();
            }
        });

        $(".score").each(function() {
            $(this).keyup(function() {
                calculateSum();
            });
        });

        function calculateSum() {
            var sum = 0;
            $(".score").each(function() {
                if (!isNaN(this.value) && this.value.length != 0) {
                    sum += parseFloat(this.value);
                }
            });
            $("#sum").val(sum.toFixed(2));
            if (sum == 100) {
                $("#sum").last().addClass("is-valid").removeClass("is-invalid");
            } else {
                $("#sum").addClass("is-invalid").removeClass("is-valid");
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
                    icon: 'error',
                    title: 'คะแนนรวมไม่เท่ากับ 100',
                    text: 'กรุณาตรวจสอบคะแนนที่ตั้งค่าไว้ให้รวมกันได้ 100 คะแนนพอดี',
                });
                return;
            }
            submitButton.prop('disabled', true).html('<i class="bi bi-arrow-clockwise"></i> กำลังบันทึก...');
            $.ajax({
                url: '<?= site_url("assessment/save-score/setting-score/") ?>' + form.attr('id'),
                type: "post",
                data: form.serialize(),
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        $('#myModal').modal('hide');
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'ตั้งค่าคะแนนสำเร็จ',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => { window.location.reload(); });
                    } else {
                        Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: data.message || 'ไม่สามารถบันทึกได้' });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ', text: textStatus });
                },
                complete: function() {
                    submitButton.prop('disabled', false).html(originalButtonText);
                }
            });
        });

        $(".check_score, .study_time").on('keyup', function() {
            calculateRowSum($(this).closest('tr'));
        });

        function calculateRowSum(row) {
            var TimeNum = row.find('.study_time').attr('check-time');
            var sum = 0;
            var study_time = row.find('.study_time').val();
            var Check_ro = 0;

            row.find('.check_score').each(function() {
                if ($(this).val().toLowerCase() == "ร") {
                    Check_ro += 1;
                } else if ($.isNumeric($(this).val())) {
                    sum += parseFloat($(this).val());
                }
            });

            row.find('.subtot').html(sum);

            if (study_time !== undefined && study_time !== '') {
                if ((80 * TimeNum / 100) > parseFloat(study_time)) {
                    row.find('.grade').html('มส');
                } else if (Check_ro > 0) {
                    row.find('.grade').html('ร');
                } else {
                    row.find('.grade').html(check_grade(sum));
                }
            } else {
                if (Check_ro > 0) {
                    row.find('.grade').html('ร');
                } else {
                    row.find('.grade').html(check_grade(sum));
                }
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

        // Initial calculation for all rows
        $('#tb_score tbody tr').each(function() {
            calculateRowSum($(this));
        });

        $(document).on('click', '#chcek_score', function(e) {
            e.preventDefault(); // Prevent default anchor behavior

            var subjectId = $(this).attr('subject-id');

            $.ajax({
                url: "<?= site_url('assessment/save-score/edit-score') ?>",
                type: 'POST',
                data: { subid: subjectId },
                dataType: 'json',
                success: function(data) {
                    // Clear previous values
                    $('.form_set_score')[0].reset();
                    $("#sum").val('');
                    $("#sum").removeClass("is-valid is-invalid");

                    if (data.status === 'not_found') {
                        $(".form_set_score").attr('id', "form_insert_score");
                    } else {
                        $(".form_set_score").attr('id', "form_update_score");
                        $('#before_middle_score').val(data[0].regscore_score);
                        $('#test_midterm_score').val(data[1].regscore_score);
                        $('#after_midterm_score').val(data[2].regscore_score);
                        $('#final_exam_score').val(data[3].regscore_score);
                        calculateSum(); // Recalculate sum after populating
                    }
                    // Show the modal AFTER data is loaded
                    $('#myModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถดึงข้อมูลการตั้งค่าคะแนนได้: ' + textStatus
                    });
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
                // Skip rows for "เรียนซ้ำ" students as they are not part of this form
                if (studentRow.find('td').attr('colspan') === '7') {
                    return true; // continue to next iteration
                }

                var studyTimeInput = studentRow.find('input[name="study_time[]"]');
                var checkScoreInputs = studentRow.find('input.check_score');

                if (studyTimeInput.length > 0) {
                    var enteredStudyTime = parseInt(studyTimeInput.val(), 10);
                    var maxStudyTime = parseInt(studyTimeInput.attr('check-time'), 10);
                    if (!isNaN(enteredStudyTime) && enteredStudyTime > maxStudyTime) {
                        Swal.fire({
                            icon: 'error',
                            title: 'เวลาเรียนเกิน',
                            text: 'นักเรียน ' + studentRow.find('td:eq(3)').text().trim() + ' มีเวลาเรียน (' + enteredStudyTime + ') เกินกว่าที่กำหนด (' + maxStudyTime + ')',
                        });
                        validationFailed = true;
                        return false;
                    }
                }

                checkScoreInputs.each(function() {
                    var checkScoreInput = $(this);
                    var enteredScore = checkScoreInput.val();
                    var maxScore = parseInt(checkScoreInput.attr('check-score-key'), 10);

                    if (enteredScore.toLowerCase() === 'ร') {
                        return true;
                    }
                    
                    var parsedEnteredScore = parseFloat(enteredScore);

                    if (!isNaN(parsedEnteredScore) && parsedEnteredScore > maxScore) {
                        Swal.fire({
                            icon: 'error',
                            title: 'คะแนนเกิน',
                            text: 'นักเรียน ' + studentRow.find('td:eq(3)').text().trim() + ' มีคะแนน (' + parsedEnteredScore + ') เกินกว่าคะแนนเก็บ (' + maxScore + ')',
                        });
                        validationFailed = true;
                        return false;
                    }
                });

                if (validationFailed) {
                    return false;
                }
            });

            if (validationFailed) {
                return; // Stop form submission
            }

            submitButton.prop('disabled', true).html('<i class="bi bi-arrow-clockwise"></i> กำลังบันทึก...');
            $.ajax({
                url: '<?= site_url("assessment/save-score/insert-score") ?>',
                type: "post",
                data: form.serialize(),
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        Swal.fire({ position: 'top-end', icon: 'success', title: 'บันทึกคะแนนสำเร็จ', showConfirmButton: false, timer: 1500 });
                    } else {
                        Swal.fire({ position: 'top-end', icon: 'error', title: 'เกิดข้อผิดพลาด', showConfirmButton: false, timer: 2000 });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({ position: 'top-end', icon: 'error', title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ', text: textStatus, showConfirmButton: false, timer: 2000 });
                },
                complete: function() {
                    submitButton.prop('disabled', false).html(originalButtonText);
                }
            });
        });

        const Toast = Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 2500, timerProgressBar: true, didOpen: (toast) => { toast.addEventListener('mouseenter', Swal.stopTimer); toast.addEventListener('mouseleave', Swal.resumeTimer); } });

        $(document).on('input', '.check_score, .study_time', function() {
            var inputField = $(this);
            var currentTimeout = inputField.data('autosaveTimeout');
            clearTimeout(currentTimeout);
            
             // --- VALIDATION ---
    var enteredValue = parseInt(inputField.val(), 10);
    var maxValue;
    var errorMessage;

    if (inputField.hasClass('check_score')) {
        maxValue = parseInt(inputField.attr('check-score-key'), 10);
        errorMessage = 'คะแนนที่กรอก (' + enteredValue + ') เกินกว่าคะแนนเก็บ (' + maxValue + ')';
    } else if (inputField.hasClass('study_time')) {
        maxValue = parseInt(inputField.attr('check-time'), 10);
        errorMessage = 'เวลาเรียนที่กรอก (' + enteredValue + ') เกินกว่าที่กำหนด (' + maxValue + ')';
    }

    if (maxValue && !isNaN(enteredValue) && enteredValue > maxValue) {
        Toast.fire({
            icon: 'error',
            title: errorMessage,
            timer: 3000 // Longer for error
        });
        
        // Revert to 0 as per new request
        inputField.val('0'); 

        setTimeout(function () {
            inputField.focus().select();
        }, 100); 
        return; // Stop the autosave
    }
    // --- END VALIDATION ---

            Toast.fire({ icon: 'info', title: 'กำลังแก้ไข...', timer: 1000 });
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
                            Toast.fire({ icon: 'success', title: response.message || 'บันทึกข้อมูลเรียบร้อย' });
                        } else {
                            Toast.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด: ' + response.message });
                        }
                    },
                    error: function() {
                        Toast.fire({ icon: 'error', title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ' });
                    }
                });
            }, 1500);
            inputField.data('autosaveTimeout', newTimeout);
        });
    });
</script>
<?= $this->endSection() ?>