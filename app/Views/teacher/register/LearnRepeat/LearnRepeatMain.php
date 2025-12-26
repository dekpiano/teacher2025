<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
บันทึกผลการเรียน (นักเรียนเรียนซ้ำ)
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <!-- Hero Section -->
    <div class="col-12">
        <div class="card border-0 shadow-none bg-label-warning mb-4">
            <div class="card-body d-flex align-items-center p-4">
                <div class="avatar avatar-lg bg-warning rounded me-3">
                    <i class="bi bi-journal-bookmark-fill fs-2 text-white"></i>
                </div>
                <div>
                    <h4 class="mb-1 text-warning">ระบบบันทึกผลการเรียน (นักเรียนเรียนซ้ำ)</h4>
                    <p class="mb-0 opacity-75">จัดการข้อมูลคะแนนสำหรับนักเรียนที่ลงทะเบียนเรียนซ้ำในปีการศึกษาปัจจุบัน</p>
                </div>
                <div class="ms-auto d-none d-md-block">
                    <?php if (($onoff[0]->onoff_status ?? 'off') == 'off') : ?>
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
                        <span class="avatar-initial rounded bg-label-warning"><i class="bi bi-book"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0"><?= count($check_subject) ?></h4>
                </div>
                <p class="mb-1">วิชาที่รับผิดชอบ (ซ้ำ)</p>
                <p class="mb-0">
                    <small class="text-muted">จำนวนวิชาเรียนซ้ำในปีการศึกษานี้</small>
                </p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-info"><i class="bi bi-person-check"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0"><?= $onoff[0]->onoff_year ?? '-' ?></h4>
                </div>
                <p class="mb-1">ปีการศึกษาเรียนซ้ำ</p>
                <p class="mb-0">
                    <small class="text-muted">ภาคเรียน/ปีการศึกษาปัจจุบัน</small>
                </p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-secondary"><i class="bi bi-hash"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0"><?= $onoff[0]->onoff_detail ?? '-' ?></h4>
                </div>
                <p class="mb-1">ครั้งที่/รอบที่</p>
                <p class="mb-0">
                    <small class="text-muted">รอบการลงทะเบียนเรียนซ้ำ</small>
                </p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100 <?= (($onoff[0]->onoff_status ?? 'off') == 'off') ? 'bg-label-danger' : 'bg-label-success' ?> border-0">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded <?= (($onoff[0]->onoff_status ?? 'off') == 'off') ? 'bg-danger' : 'bg-success' ?> text-white font-weight-bold">
                            <i class="bi <?= (($onoff[0]->onoff_status ?? 'off') == 'off') ? 'bi-lock-fill' : 'bi-unlock-fill' ?>"></i>
                        </span>
                    </div>
                    <h5 class="ms-1 mb-0"><?= (($onoff[0]->onoff_status ?? 'off') == 'off') ? 'ปิดระบบ' : 'เปิดระบบ' ?></h5>
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
                    <div class="avatar avatar-sm bg-label-warning me-2 rounded">
                        <i class="bi bi-list-stars"></i>
                    </div>
                    <h5 class="card-title mb-0">รายการวิชาที่รับผิดชอบ (นักเรียนเรียนซ้ำ)</h5>
                </div>
                <div class="card-actions">
                    <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise"></i> รีเฟรช
                    </button>
                </div>
            </div>
            <div class="card-body pt-0">
                <?php if (($onoff[0]->onoff_status ?? 'off') == 'off') : ?>
                    <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-3 fs-4"></i>
                        <div>
                            <h6 class="alert-heading mb-1 fw-bold">ระบบยังไม่เปิดให้บันทึกผลการเรียน!</h6>
                            <span>ขณะนี้ฝ่ายวิชาการยังไม่เปิดระบบให้บันทึกคะแนนสำหรับการเรียนซ้ำ กรุณารอประกาศแจ้งอีกครั้ง</span>
                        </div>
                    </div>
                <?php endif; ?>

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
                                <th class="text-center" style="width: 250px;">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($check_subject)) : ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center text-muted">
                                            <i class="bi bi-folder2-open display-4 mb-2"></i>
                                            <p>ไม่มีรายวิชาสำหรับการเรียนซ้ำในรอบนี้</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($check_subject as $key => $v_check_subject) : ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?= $key + 1 ?></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="badge bg-label-warning border-0 mb-1"><?= esc($v_check_subject->RepeatYear) ?></span>
                                                <?php if($v_check_subject->RegisterYear != $v_check_subject->RepeatYear): ?>
                                                    <small class="text-muted fs-tiny">เดิม: <?= esc($v_check_subject->RegisterYear) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="badge bg-label-warning p-2 me-3 rounded border-0">
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
                                            <?= $v_check_subject->SubjectHour ?> ชม.
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <?php if (($onoff[0]->onoff_status ?? 'off') == 'off') : ?>
                                                    <button type="button" class="btn btn-secondary btn-sm px-3 disabled" data-bs-toggle="tooltip" title="ระบบปิดการบันทึก">
                                                        <i class="bi bi-lock me-1"></i> บันทึกผล
                                                    </button>
                                                <?php else : ?>
                                                    <a href="<?= base_url('assessment/save-score-repeat-add/' . $v_check_subject->SubjectID . '/all') ?>" 
                                                       class="btn btn-warning btn-sm px-3 shadow-sm hover-elevate">
                                                        <i class="bi bi-pencil-square me-1"></i> บันทึกผล
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($v_check_subject->score_settings_count == 0) : ?>
                                                    <button type="button" class="btn btn-secondary btn-sm px-3 disabled" 
                                                            data-bs-toggle="tooltip" title="กรุณาตั้งค่าคะแนนเก็บก่อนออกรายงาน">
                                                        <i class="bi bi-printer me-1"></i> พิมพ์
                                                    </button>
                                                <?php else : ?>
                                                    <button type="button" id="chcek_report" class="btn btn-outline-info btn-sm px-3 shadow-sm hover-elevate" 
                                                            data-bs-toggle="modal" data-bs-target="#Modalprint" 
                                                            report-yaer="<?= esc($v_check_subject->RegisterYear) ?>" 
                                                            report-subject="<?= esc($v_check_subject->SubjectID) ?>">
                                                        <i class="bi bi-printer me-1"></i> พิมพ์
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
                <h5 class="modal-title text-white"><i class="bi bi-printer me-2"></i> ออกรายงานผลการเรียน (ซ้ำ)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('assessment/report-learn-repeat'); ?>" method="post" target="_blank" class="no-loader">
                <div class="modal-body p-4 text-center">
                   <div class="avatar avatar-xl bg-label-info rounded-circle mb-3 mx-auto">
                        <i class="bi bi-file-earmark-pdf fs-1"></i>
                    </div>
                    <h5 class="mb-3">เลือกห้องเรียนที่ต้องการออกรายงาน</h5>
                    
                    <div class="form-floating mb-3">
                        <select name="select_print" id="select_print" class="form-select border-info">
                            <option value="all">ทั้งหมด (ทุกห้องเรียน)</option>
                        </select>
                        <label for="select_print">รายชื่อห้องเรียน</label>
                    </div>

                    <div class="alert alert-info border-0 d-flex text-start mb-0 small">
                        <i class="bi bi-info-circle me-3 fs-3"></i>
                        <div>
                            <strong>คำแนะนำ:</strong> รายงานจะประกอบด้วยใบปะหน้าสรุปผล และรายชื่อนักเรียนแยกตามห้องเรียนที่เลือก
                        </div>
                    </div>
                    
                    <input type="hidden" name="report_RegisterYear" id="report_RegisterYear">
                    <input type="hidden" name="report_SubjectID" id="report_SubjectID">
                </div>
                <div class="modal-footer border-top-0 d-flex justify-content-between p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-info px-5 shadow-sm">
                        <i class="bi bi-printer me-2"></i> พิมพ์รายงาน
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .fs-tiny { font-size: 0.65rem; }
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
    .bg-label-warning { background-color: #fff3e0 !important; color: #ff9800 !important; }
    .bg-label-info { background-color: #e0f7fa !important; color: #00bcd4 !important; }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        // Initialize Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Report Button Handler
        $(document).on('click', '#chcek_report', function(e) {
            e.preventDefault();
            var year = $(this).attr('report-yaer');
            var subject = $(this).attr('report-subject');
            var $btn = $(this);
            var originalHtml = $btn.html();

            $("#report_RegisterYear").val(year);
            $("#report_SubjectID").val(subject);

            // Show loading state
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');

            $.ajax({
                url: "<?= site_url('assessment/save-score-repeat/checkroom-report') ?>",
                type: 'POST',
                data: {
                    report_yaer: year,
                    report_subject: subject
                },
                dataType: 'json',
                success: function(data) {
                    var selectPrint = $('#select_print');
                    selectPrint.empty();
                    selectPrint.append('<option value="all">ทั้งหมด (ทุกห้องเรียน)</option>');
                    $.each(data, function(key, val) {
                        selectPrint.append('<option value="' + val.StudentClass + '">' + val.StudentClass + '</option>');
                    });
                    $btn.prop('disabled', false).html(originalHtml);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $btn.prop('disabled', false).html(originalHtml);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถดึงข้อมูลห้องเรียนได้: ' + textStatus,
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                }
            });
        });

        // Close modal and reset form after PDF generation start
        $('#Modalprint form').on('submit', function() {
            setTimeout(() => {
                $('#Modalprint').modal('hide');
            }, 1500);
        });
    });
</script>
<?= $this->endSection() ?>