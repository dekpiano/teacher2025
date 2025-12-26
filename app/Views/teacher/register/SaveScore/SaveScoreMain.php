<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
บันทึกผลการเรียน (ปกติ)
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <!-- Hero Section -->
    <div class="col-12">
        <div class="card border-0 shadow-none bg-label-primary mb-4">
            <div class="card-body d-flex align-items-center p-4">
                <div class="avatar avatar-lg bg-primary rounded me-3">
                    <i class="bi bi-journal-check fs-2 text-white"></i>
                </div>
                <div>
                    <h4 class="mb-1 text-primary">ระบบบันทึกผลการเรียน (ภาคเรียนปกติ)</h4>
                    <p class="mb-0 opacity-75">จัดการข้อมูลคะแนนและเกรดของนักเรียนในรายวิชาที่รับผิดชอบ</p>
                </div>
                <div class="ms-auto d-none d-md-block">
                    <?php if (empty($onoff) || $onoff[0]->onoff_status == "off") : ?>
                        <span class="badge bg-danger p-2 px-3">
                            <i class="bi bi-clock-history me-1"></i> ระบบปิดการบันทึกคะแนน
                        </span>
                    <?php else : ?>
                        <span class="badge bg-success p-2 px-3 animate__animated animate__pulse animate__infinite">
                            <i class="bi bi-check-circle me-1"></i> ระบบเปิดการบันทึกคะแนน
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bi bi-book"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0"><?= count($check_subject) ?></h4>
                </div>
                <p class="mb-1">จำนวนวิชาที่สอน</p>
                <p class="mb-0">
                    <small class="text-muted">วิชาทั้งหมดในปีการศึกษาปัจจุบัน</small>
                </p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-info"><i class="bi bi-award"></i></span>
                    </div>
                    <?php 
                        $totalUnits = 0;
                        foreach($check_subject as $s) $totalUnits += $s->SubjectUnit;
                    ?>
                    <h4 class="ms-1 mb-0"><?= number_format($totalUnits, 1) ?></h4>
                </div>
                <p class="mb-1">หน่วยกิตสะสม</p>
                <p class="mb-0">
                    <small class="text-muted">รวมหน่วยกิตทุกรายวิชา</small>
                </p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-warning"><i class="bi bi-clock"></i></span>
                    </div>
                     <?php 
                        $totalHours = 0;
                        foreach($check_subject as $s) $totalHours += $s->SubjectHour;
                    ?>
                    <h4 class="ms-1 mb-0"><?= $totalHours ?></h4>
                </div>
                <p class="mb-1">จำนวนชั่วโมงรวม</p>
                <p class="mb-0">
                    <small class="text-muted">รวมชั่วโมงเรียนต่อเทอม</small>
                </p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100 <?= (empty($onoff) || $onoff[0]->onoff_status == "off") ? 'bg-label-danger' : 'bg-label-success' ?> border-0">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded <?= (empty($onoff) || $onoff[0]->onoff_status == "off") ? 'bg-danger' : 'bg-success' ?> text-white font-weight-bold">
                            <i class="bi <?= (empty($onoff) || $onoff[0]->onoff_status == "off") ? 'bi-lock-fill' : 'bi-unlock-fill' ?>"></i>
                        </span>
                    </div>
                    <h5 class="ms-1 mb-0"><?= (empty($onoff) || $onoff[0]->onoff_status == "off") ? 'ปิดระบบ' : 'เปิดระบบ' ?></h5>
                </div>
                <p class="mb-1">สถานะการบันทึกข้อมูล</p>
                <p class="mb-0">
                    <small class="text-muted opacity-75">จัดการโดยฝ่ายวิชาการ</small>
                </p>
            </div>
        </div>
    </div>

    <!-- Subject List -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom mb-3 pb-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm bg-label-primary me-2 rounded">
                        <i class="bi bi-list-stars"></i>
                    </div>
                    <h5 class="card-title mb-0">รายการวิชาที่รับผิดชอบ</h5>
                </div>
                <div class="card-actions">
                    <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise"></i> รีเฟรช
                    </button>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle datatable-init">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" style="width: 80px;">ลำดับ</th>
                                <th style="width: 120px;">ปีการศึกษา</th>
                                <th>รหัสวิชา / ชื่อวิชา</th>
                                <th class="text-center">ชั้นที่สอน</th>
                                <th class="text-center">หน่วยกิต</th>
                                <th class="text-center">ชั่วโมง</th>
                                <th class="text-center" style="width: 300px;">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($check_subject)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center text-muted">
                                            <i class="bi bi-folder2-open display-4 mb-2"></i>
                                            <p>ไม่มีรายวิชาที่สอนในปีการศึกษานี้</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($check_subject as $key => $v_check_subject) : ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?= $key + 1 ?></td>
                                        <td>
                                            <span class="badge bg-label-secondary border-0"><?= esc($v_check_subject->RegisterYear) ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="badge bg-label-primary p-2 me-3 rounded border-0">
                                                    <span class="fw-bold"><?= esc($v_check_subject->SubjectCode) ?></span>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold text-dark fs-6"><?= esc($v_check_subject->SubjectName) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-primary fw-medium"><?= esc($v_check_subject->RegisterClasses) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-info"><?= $v_check_subject->SubjectUnit ?> นก.</span>
                                        </td>
                                        <td class="text-center text-muted">
                                            <?= $v_check_subject->SubjectHour ?> ชม./สัปดาห์
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <?php if (empty($onoff) || $onoff[0]->onoff_status == "off") : ?>
                                                    <button type="button" class="btn btn-secondary btn-sm px-3 disabled" data-bs-toggle="tooltip" title="ระบบปิดการบันทึก">
                                                        <i class="bi bi-lock me-1"></i> บันทึกเกรด
                                                    </button>
                                                <?php else : ?>
                                                    <a href="<?= base_url('assessment/save-score-add/' . $v_check_subject->RegisterYear . '/' . $v_check_subject->SubjectID . '/all') ?>" 
                                                       class="btn btn-primary btn-sm px-3 shadow-sm hover-elevate">
                                                        <i class="bi bi-pencil-square me-1"></i> บันทึกเกรด
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($v_check_subject->score_settings_count == 0) : ?>
                                                    <button type="button" class="btn btn-secondary btn-sm px-3 disabled" 
                                                            data-bs-toggle="tooltip" title="กรุณาตั้งค่าคะแนนเก็บก่อนออกรายงาน">
                                                        <i class="bi bi-printer me-1"></i> รายงาน
                                                    </button>
                                                <?php else : ?>
                                                    <button type="button" id="chcek_report" class="btn btn-info btn-sm px-3 shadow-sm hover-elevate" 
                                                            data-bs-toggle="modal" data-bs-target="#Modalprint" 
                                                            report-yaer="<?= $v_check_subject->RegisterYear ?>" 
                                                            report-subject="<?= $v_check_subject->SubjectID ?>">
                                                        <i class="bi bi-printer me-1"></i> รายงาน
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Print -->
<div class="modal fade" id="Modalprint" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info py-3">
                <h5 class="modal-title text-white"><i class="bi bi-printer me-2"></i> ออกรายงานผลการเรียน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('assessment/report-learn-normal'); ?>" method="post" target="_blank" class="no-loader">
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl bg-label-info rounded-circle mb-3 mx-auto">
                            <i class="bi bi-file-pdf fs-1"></i>
                        </div>
                        <h6>โปรดเลือกห้องเรียนที่คุณต้องการออกรายงาน</h6>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <select name="select_print" id="select_print" class="form-select border-primary">
                            <option value="all">ทุกห้องเรียนที่สอน</option>
                        </select>
                        <label for="select_print">เลือกห้องเรียน</label>
                    </div>
                    
                    <div class="alert alert-info border-0 d-flex mb-0">
                        <i class="bi bi-info-circle me-3 fs-3"></i>
                        <div>
                            <small class="d-block fw-bold">หมายเหตุ:</small>
                            <small>รายงานจะถูกเปิดในแท็บใหม่ในรูปแบบไฟล์ PDF พร้อมสำหรับการพิมพ์หรือบันทึกไฟล์</small>
                        </div>
                    </div>
                    
                    <input type="hidden" name="report_RegisterYear" id="report_RegisterYear">
                    <input type="hidden" name="report_SubjectID" id="report_SubjectID">
                </div>
                <div class="modal-footer border-top-0 d-flex justify-content-between p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-info px-5 shadow-sm" id="btn_submit_print">
                        <i class="bi bi-printer me-2"></i> พิมพ์รายงาน
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .hover-elevate:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease;
    }
    .card {
        transition: all 0.3s ease;
    }
    .datatable-init thead th {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }
    .badge {
        font-weight: 600;
    }
    .bg-label-info { background-color: #e0f7fa !important; color: #00bcd4 !important; }
    .bg-label-warning { background-color: #fff8e1 !important; color: #ffc107 !important; }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        $(document).on('click', '#chcek_report', function(e) {
            e.preventDefault();

            var year = $(this).attr('report-yaer');
            var subject = $(this).attr('report-subject');
            const $btn = $(this);
            const originalHtml = $btn.html();

            $("#report_RegisterYear").val(year);
            $("#report_SubjectID").val(subject);

            // Show loading state on button
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');

            $.ajax({
                url: "<?= site_url('assessment/save-score/checkroom-report') ?>",
                type: 'POST',
                data: {
                    report_yaer: year,
                    report_subject: subject
                },
                dataType: 'json',
                success: function(data) {
                    var selectPrint = $('#select_print');
                    selectPrint.empty();
                    selectPrint.append('<option value="all">ทุกห้องเรียนที่สอน</option>');
                    $.each(data, function(key, val) {
                        selectPrint.append('<option value="' + val.StudentClass + '">' + val.StudentClass + '</option>');
                    });
                    
                    // Restore button state
                    $btn.prop('disabled', false).html(originalHtml);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $btn.prop('disabled', false).html(originalHtml);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถดึงข้อมูลห้องเรียนได้: ' + textStatus,
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                }
            });
        });

        // Close modal and reset form after PDF generation start
        $('#Modalprint form').on('submit', function() {
            setTimeout(() => {
                $('#Modalprint').modal('hide');
            }, 1000);
        });
    });
</script>
<?= $this->endSection() ?>