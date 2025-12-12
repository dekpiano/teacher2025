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

$(document).on('click', '.clickLoad', function() {
    $(this).prop("disabled", true);
    $(this).html(
        '<i class="fa fa-circle-o-notch fa-spin"></i> loading...'
    );
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
        $("#sum").last().addClass("is-valid");
        $("#sum").removeClass("is-invalid")
    } else {
        $("#sum").addClass("is-invalid")
        $("#sum").removeClass("is-valid")
    }
}

$(document).on('change', '#check_room', function() {
    window.location.href = $(this).val();
});

$(document).on('submit', '.form_set_score', function(e) {
    e.preventDefault();
    var form = $(this);
    var submitButton = form.find('button[type="submit"]');
    var originalButtonText = submitButton.html();
    var sum = parseFloat($('#sum').val());
    if (sum !== 100) {
        Swal.fire({
            icon: 'error',
            title: 'คะแนนรวมไม่เท่ากับ 100',
            text: 'กรุณาตรวจสอบคะแนนที่ตั้งค่าไว้ให้รวมกันได้ 100 คะแนนพอดี',
        });
        return;
    }
    submitButton.prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i> กำลังบันทึก...');
    $.ajax({
        url: '../../../../../ConTeacherRegister/setting_score/' + form.attr('id'),
        type: "post",
        data: form.serialize(),
        success: function(data) {
            if (data > 0) {
                $('#myModal').modal('hide');
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'ตั้งค่าคะแนนสำเร็จ',
                    showConfirmButton: false,
                    timer: 2000
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        window.location.reload();
                    }
                });
            } else {
                 Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถบันทึกการตั้งค่าคะแนนได้',
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ',
                text: textStatus,
            });
        },
        complete: function() {
            submitButton.prop('disabled', false).html(originalButtonText);
        }
    });
});

$(".check_score").each(function() {
    $(this).keyup(function() {
        calculateTotal($(this).parent().index());
    });
});

$(".study_time").each(function() {
    $(this).keyup(function() {
        calculateTotal($(this).parent().index());
    });
});

function calculateTotal(index) {
    var total = 0;
    $('#tb_score tbody tr td').filter(function() {
        if ($(this).index() == index) {
            total += parseInt($(this).find('.check_score').val()) || 0;
        }
    });
    $('#tb_score tbody tr td.totalCol:eq(' + index + ')').html(total);
    calculateRowSum();
}

function Charactor($char) {
    var re = new RegExp("^([0-9]|[ร])+$", "g");
    return re.test($char);
}

function calculateRowSum() {
    var TimeNum = $('.study_time').attr('check-time');
    $('table tbody tr').each(function() {
        var sum = 0;
        var study_time;
        var Check_ro = 0;
        $(this).find('td').each(function() {
            if ($(this).find('.check_score').val() == "ร") {
                Check_ro += 1;
            } else {
                sum += parseInt($(this).find('.check_score').val()) || 0;
            }
        });
        study_time = $(this).find('.study_time').val()
        $(this).find('.subtot').html(sum);
        if (80 * TimeNum / 100 > study_time) {
            $(this).find('.grade').html('มส');
        } else if (Check_ro > 0) {
            $(this).find('.grade').html('ร');
        } else {
            $(this).find('.grade').html(check_grade(sum));
        }
    });
}

function check_grade(sum) {
    if ((sum > 100) || (sum < 0)) {
        var grade = "ไม่สามารถคิดเกรดได้ คะแนนเกิน";
    } else if ((sum >= 79.5) && (sum <= 100)) {
        var grade = 4;
    } else if ((sum >= 74.5) && (sum <= 79.4)) {
        var grade = 3.5;
    } else if ((sum >= 69.5) && (sum <= 74.4)) {
        var grade = 3;
    } else if ((sum >= 64.5) && (sum <= 69.4)) {
        var grade = 2.5;
    } else if ((sum >= 59.5) && (sum <= 64.4)) {
        var grade = 2;
    } else if ((sum >= 54.5) && (sum <= 59.4)) {
        var grade = 1.5;
    } else if ((sum >= 49.5) && (sum <= 54.4)) {
        var grade = 1;
    } else if (sum <= 49.4) {
        var grade = 0;
    }
    return grade;
}
calculateRowSum();

$(document).on('click', '#chcek_score', function() {
    $.post("../../../../../ConTeacherRegister/edit_score", {
        subid: $(this).attr('subject-id')
    }, function(data, status) {
        if (data == 0) {
            $(".form_set_score").attr('id', "form_insert_score");
        } else {
            $(".form_set_score").attr('id', "form_update_score");
            $('#before_middle_score').val(data[0].regscore_score);
            $('#test_midterm_score').val(data[1].regscore_score);
            $('#after_midterm_score').val(data[2].regscore_score);
            $('#final_exam_score').val(data[3].regscore_score);
            $('#sum').val(Number(data[0].regscore_score) + Number(data[1].regscore_score) + Number(data[2].regscore_score) + Number(data[3].regscore_score));
        }
    }, 'json');
});

$(document).on('submit', '.form_score', function(e) {
    e.preventDefault();
    var form = $(this);
    var submitButton = form.find('button[type="submit"]');
    var originalButtonText = submitButton.html();

    // --- START VALIDATION ---
    var validationFailed = false;
    form.find('tbody tr').each(function() {
        var studentRow = $(this);
        var studyTimeInput = studentRow.find('input[name="study_time[]"]');
        var checkScoreInputs = studentRow.find('input[class*="check_score"]'); // Use class* to match check_score class

        // Validate study_time
        if (studyTimeInput.length > 0) {
            var enteredStudyTime = parseInt(studyTimeInput.val(), 10);
            var maxStudyTime = parseInt(studyTimeInput.attr('check-time'), 10);
            if (!isNaN(enteredStudyTime) && enteredStudyTime > maxStudyTime) {
                Swal.fire({
                    icon: 'error',
                    title: 'เวลาเรียนที่กรอก (' + enteredStudyTime + ') เกินกว่าที่กำหนด (' + maxStudyTime + ')',
                    text: 'กรุณาตรวจสอบข้อมูลในตาราง',
                });
                validationFailed = true;
                return false; // Break out of .each() loop
            }
        }

        // Validate check_score inputs
        checkScoreInputs.each(function() {
            var checkScoreInput = $(this);
            var enteredScore = parseInt(checkScoreInput.val(), 10);
            var maxScore = parseInt(checkScoreInput.attr('check-score-key'), 10);

            // Allow "ร" character
            if (checkScoreInput.val() === "ร") {
                return true; // Continue to next input
            }

            if (!isNaN(enteredScore) && enteredScore > maxScore) {
                Swal.fire({
                    icon: 'error',
                    title: 'คะแนนที่กรอก (' + enteredScore + ') เกินกว่าคะแนนเก็บ (' + maxScore + ')',
                    text: 'กรุณาตรวจสอบข้อมูลในตาราง',
                });
                validationFailed = true;
                return false; // Break out of .each() loop
            }
        });

        if (validationFailed) {
            return false; // Break out of outer .each() loop
        }
    });

    if (validationFailed) {
        submitButton.prop('disabled', false).html(originalButtonText); // Re-enable button
        return; // Stop form submission
    }
    // --- END VALIDATION ---

    submitButton.prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i> กำลังบันทึก...');
    $.ajax({
        url: '../../../../../ConTeacherRegister/insert_score',
        type: "post",
        data: form.serialize(),
        success: function(data) {
            if (data > 0) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'บันทึกคะแนนสำเร็จ',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาดในการบันทึก',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ',
                text: textStatus,
                showConfirmButton: false,
                timer: 2000
            });
        },
        complete: function() {
            submitButton.prop('disabled', false).html(originalButtonText);
        }
    });
});

$(document).on('submit', '.form_score_repeat', function(e) {
    e.preventDefault();
    $.ajax({
        url: '../../../../../ConTeacherRegister/insert_score_repeat',
        type: "post",
        data: $(this).serialize(),
        success: function(data) {
            if (data > 0) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'บันทึกคะแนนสำเร็จ',
                    showConfirmButton: false,
                    timer: 2000
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        //window.location.reload();
                    }
                })
            } else {
                // window.location.reload();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.responseText);
        }
    });
});

$(document).on('click', '#chcek_report', function() {
    $("#report_RegisterYear").val($(this).attr('report-yaer'));
    $("#report_SubjectID").val($(this).attr('report-subject'));
    $('#select_print option').remove();
    $.post("../ConTeacherRegister/checkroom_report", {
        report_yaer: $(this).attr('report-yaer'),
        report_subject: $(this).attr('report-subject')
    }, function(data, status) {
        $.each(data, function(key, val) {
            $('#select_print').append('<option value="' + val.StudentClass + '">' + val.StudentClass + '</option>');
        });
        $('#select_print').append('<option value="all">ทั้งหมด</option>');
    }, 'json');
});

// --- Auto-save functionality with Validation (Per-Field Debounce) ---
// No global autosaveTimeout needed anymore
// var autosaveTimeout; // This line is commented out or removed

const Toast = Swal.mixin({
  toast: true,
  position: 'bottom-end',
  showConfirmButton: false,
  timer: 2500,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
});

$(document).on('input', '.check_score, .study_time', function() {
    var inputField = $(this);
    var currentTimeout = inputField.data('autosaveTimeout'); // Get timeout specific to this field
    clearTimeout(currentTimeout); // Clear this field's previous timeout

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

    // --- AUTOSAVE ---
    Toast.fire({
        icon: 'info',
        title: 'กำลังแก้ไข...',
        timer: 1800
    });

    var studentRow = inputField.closest('tr'); // Use inputField directly
    var newTimeout = setTimeout(function() { // Store this new timeout
        var studentID = studentRow.find('input[name="StudentID[]"]').val();
        var scores = studentRow.find('input[name^="' + studentID + '"]').map(function() {
            return $(this).val();
        }).get();
        
        var studentData = {
            StudentID: studentID,
            SubjectID: $('input[name="SubjectID"]').val(),
            RegisterYear: $('input[name="RegisterYear"]').val(),
            TimeNum: $('input[name="TimeNum"]').val(),
            study_time: studentRow.find('input[name="study_time[]"]').val(),
            scores: scores
        };

        $.ajax({
            url: '../../../../../ConTeacherRegister/autosave_score',
            type: 'POST',
            data: studentData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Toast.fire({
                        icon: 'success',
                        title: 'บันทึกข้อมูลเรียบร้อย'
                    });
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด: ' + response.message
                    });
                }
            },
            error: function() {
                Toast.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ'
                });
            }
        });
    }, 1500); 

    inputField.data('autosaveTimeout', newTimeout); // Store the new timeout ID on the field
});

