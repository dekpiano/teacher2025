<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
บันทึกผลการเรียน (ปกติ)
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="bi bi-book"></i> รายวิชาที่สอน</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ปีการศึกษา</th>
                        <th>ชั้นที่สอน</th>
                        <th>วิชา</th>
                        <th>หน่วยกิต</th>
                        <th>ชั่วโมง</th>
                        <th>บันทึกผลการเรียน</th>
                        <th>รายงาน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($check_subject as $key => $v_check_subject) : ?>
                        <tr>
                            <th><?= $v_check_subject->RegisterYear ?></th>
                            <td><?= $v_check_subject->RegisterClass ?></td>
                            <td><?= $v_check_subject->SubjectCode ?> <?= $v_check_subject->SubjectName ?></td>
                            <td><?= $v_check_subject->SubjectUnit ?></td>
                            <td><?= $v_check_subject->SubjectHour ?></td>
                            <td>
                                <?php if ($onoff[0]->onoff_status == "off") : ?>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#AlertNoReg">
                                        <i class="bi bi-ban"></i> ยังไม่เปิดให้บันทึก
                                    </button>
                                <?php else : ?>
                                    <a href="<?= base_url('assessment/save-score-add/' . $v_check_subject->RegisterYear . '/' . $v_check_subject->SubjectID . '/all') ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil-square"></i>  บันทึกผลการเรียน
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" id="chcek_report" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#Modalprint" report-yaer="<?= $v_check_subject->RegisterYear ?>" report-subject="<?= $v_check_subject->SubjectID ?>">
                                    <i class="bi bi-printer"></i>  พิมพ์รายงาน
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Modal Print -->
<div class="modal fade" id="Modalprint" tabindex="-1" aria-labelledby="ModalprintLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalprintLabel"><i class="bi bi-printer"></i> พิมพ์รายงาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('assessment/report-learn-normal'); ?>" method="post" target="_blank">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="select_print" class="form-label">เลือกห้องเรียน</label>
                        <select name="select_print" id="select_print" class="form-select">
                            <option value="all">ทั้งหมด</option>
                        </select>
                    </div>
                    <input type="hidden" name="report_RegisterYear" id="report_RegisterYear">
                    <input type="hidden" name="report_SubjectID" id="report_SubjectID">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> ปิด</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-printer"></i> พิมพ์</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Alert -->
<div class="modal fade" id="AlertNoReg" tabindex="-1" aria-labelledby="AlertNoRegLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AlertNoRegLabel"><i class="bi bi-exclamation-triangle"></i> แจ้งเตือน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-center">ขณะนี้! ระบบยังไม่เปิดให้ลงผลการเรียน รอสักครู่... <br>ทางฝ่ายวิชาการจะแจ้งให้ทราบอีกครั้ง</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $(document).on('click', '#chcek_report', function(e) {
            e.preventDefault();

            var year = $(this).attr('report-yaer');
            var subject = $(this).attr('report-subject');

            $("#report_RegisterYear").val(year);
            $("#report_SubjectID").val(subject);

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
                    selectPrint.append('<option value="all">ทั้งหมด</option>');
                    $.each(data, function(key, val) {
                        selectPrint.append('<option value="' + val.StudentClass + '">' + val.StudentClass + '</option>');
                    });
                    // The modal is already targeted by data-bs-target, so no need to call .modal('show') here
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