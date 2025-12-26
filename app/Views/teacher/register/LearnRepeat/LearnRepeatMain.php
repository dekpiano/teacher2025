<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
บันทึกผลการเรียน (ซ้ำ)
<?= $this->endSection() ?>

<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
บันทึกผลการเรียน (ซ้ำ)
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class="bi bi-journal-bookmark-fill me-2"></i> รายวิชาที่สอน (สำหรับนักเรียนเรียนซ้ำ)</h5>
                    <small class="text-muted">ปีการศึกษา <?= $onoff[0]->onoff_year ?? '-' ?></small>
                </div>
                <div class="card-body">
                    <?php if (($onoff[0]->onoff_status ?? 'off') == 'off') : ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
                            <div>
                                <strong>ระบบปิด:</strong> ขณะนี้ระบบยังไม่เปิดให้บันทึกผลการเรียน (ซ้ำ) ทางฝ่ายวิชาการจะแจ้งให้ทราบอีกครั้ง
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($check_subject)) : ?>
                        <div class="text-center py-5">
                            <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
                            <p class="mt-3 text-muted">ไม่พบรายวิชาที่สอนสำหรับการเรียนซ้ำในขณะนี้</p>
                        </div>
                    <?php else : ?>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>ปีการศึกษา</th>
                                        <th>ชั้นที่สอน</th>
                                        <th>วิชา</th>
                                        <th class="text-center">หน่วยกิต</th>
                                        <th class="text-center">ชั่วโมง</th>
                                        <th class="text-center">จัดการ</th>
                                        <th class="text-center">รายงาน</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php foreach ($check_subject as $key => $v_check_subject) : ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-label-primary"><?= esc($v_check_subject->RepeatYear) ?></span>
                                                <?php if($v_check_subject->RegisterYear != $v_check_subject->RepeatYear): ?>
                                                    <br><small class="text-muted">เดิม: <?= esc($v_check_subject->RegisterYear) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($v_check_subject->RegisterClasses) ?></td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-medium text-heading"><?= esc($v_check_subject->SubjectName) ?></span>
                                                    <small class="text-muted"><?= esc($v_check_subject->SubjectCode) ?></small>
                                                </div>
                                            </td>
                                            <td class="text-center"><?= esc($v_check_subject->SubjectUnit) ?></td>
                                            <td class="text-center"><?= esc($v_check_subject->SubjectHour) ?></td>
                                            <td class="text-center">
                                                <?php if (($onoff[0]->onoff_status ?? 'off') == 'off') : ?>
                                                    <span class="badge bg-label-secondary" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#AlertNoReg">
                                                        <i class="bi bi-lock-fill me-1"></i> ปิดรับ
                                                    </span>
                                                <?php else : ?>
                                                    <a href="<?= base_url('assessment/save-score-repeat-add/' . $v_check_subject->SubjectID . '/all') ?>" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-pencil-square me-1"></i> บันทึกผล
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" id="chcek_report" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#Modalprint" report-yaer="<?= esc($v_check_subject->RegisterYear) ?>" report-subject="<?= esc($v_check_subject->SubjectID) ?>">
                                                    <i class="bi bi-printer me-1"></i> พิมพ์
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Print -->
<div class="modal fade" id="Modalprint" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-printer me-2"></i>พิมพ์รายงาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('assessment/report-learn-repeat'); ?>" method="post" target="_blank" class="no-loader" onsubmit="setTimeout(() => { this.reset(); $('#Modalprint').modal('hide'); }, 100);">
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <select name="select_print" id="select_print" class="form-select" aria-label="เลือกห้องเรียน">
                            <option value="all">ทั้งหมด</option>
                        </select>
                        <label for="select_print">เลือกห้องเรียน</label>
                    </div>
                    <input type="hidden" name="report_RegisterYear" id="report_RegisterYear">
                    <input type="hidden" name="report_SubjectID" id="report_SubjectID">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-printer me-1"></i> พิมพ์</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Alert -->
<div class="modal fade" id="AlertNoReg" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pb-5">
                <div class="mb-3">
                    <i class="bi bi-exclamation-circle text-warning display-4"></i>
                </div>
                <h4>แจ้งเตือน</h4>
                <p>ขณะนี้ระบบยังไม่เปิดให้ลงผลการเรียน<br>กรุณารอประกาศจากฝ่ายวิชาการ</p>
                <button type="button" class="btn btn-primary mt-3" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        $(document).on('click', '#chcek_report', function(e) {
            e.preventDefault();

            var year = $(this).attr('report-yaer');
            var subject = $(this).attr('report-subject');

            $("#report_RegisterYear").val(year);
            $("#report_SubjectID").val(subject);

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
                    selectPrint.append('<option value="all">ทั้งหมด</option>');
                    $.each(data, function(key, val) {
                        selectPrint.append('<option value="' + val.StudentClass + '">' + val.StudentClass + '</option>');
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถดึงข้อมูลห้องเรียนได้: ' + textStatus
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>