<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ตารางกิจกรรมชุมนุม') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><?= esc($title ?? 'ตารางกิจกรรมชุมนุม') ?></h3>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#clubHelpModal">
                        <i class="bi bi-question-circle"></i> คำแนะนำ
                    </button>
                </div>
                <div class="card-body">
                    <?php if (!empty($schedules) && is_array($schedules)): ?>
                        <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                   
                                    <th>สัปดาห์ที่</th>
                                    <th>วันที่</th>
                                    <th>หัวข้อกิจกรรมที่บันทึกแล้ว</th>
                                    <th>จำนวนคาบ</th>
                                    <th>สถานะบันทึกเข้าเรียน</th>
                                    <th style="width: 350px;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($schedules as $schedule): ?>
                                    <tr>
                                        
                                        <td><?= esc($schedule->tcs_week_number) ?></td>
                                        <td>
                                            <?php if ($schedule->tcs_start_date == '0000-00-00' || empty($schedule->tcs_start_date)): ?>
                                                ผู้ดูแลยังไม่ได้กำหนดวัน
                                            <?php else: ?>
                                                <?= esc(date('d/m/Y', strtotime($schedule->tcs_start_date))) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($schedule->act_name)): ?>
                                                <span class="fw-bold"><?= esc($schedule->act_name) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic">ยังไม่ได้บันทึกกิจกรรม</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= esc($schedule->act_number_of_periods ?? 'N/A') ?>
                                        </td>
                                        <td>
                                            <?php if ($schedule->attendance_recorded): ?>
                                                <span class="badge bg-success">บันทึกแล้ว</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">ยังไม่บันทึก</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php $isDateNotSet = ($schedule->tcs_start_date == '0000-00-00' || empty($schedule->tcs_start_date)); ?>
                                                <?php $isActivityNotRecorded = empty($schedule->act_name); ?>
                                                <?php $isAttendanceDisabled = $isDateNotSet; // Attendance should be possible once date is set, even without activity details ?>
                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#activityModal"
                                                    data-date="<?= esc($schedule->tcs_start_date) ?>"
                                                    data-name="<?= esc($schedule->act_name ?? '') ?>"
                                                    data-description="<?= esc($schedule->act_description ?? '') ?>"
                                                    data-location="<?= esc($schedule->act_location ?? '') ?>"
                                                    data-start-time="<?= esc($schedule->act_start_time ?? '') ?>"
                                                    data-end-time="<?= esc($schedule->act_end_time ?? '') ?>"
                                                    data-periods="<?= esc($schedule->act_number_of_periods ?? '1') ?>"
                                                    <?= $isDateNotSet ? 'disabled' : '' ?>>
                                                    <i class="bi bi-pencil-square"></i> บันทึก/แก้ไขกิจกรรม
                                                </button>
                                                <a href="<?= site_url('club/recordAttendance/' . $club->club_id . '/' . $schedule->tcs_schedule_id) ?>" class="btn btn-info btn-sm <?= $isAttendanceDisabled ? 'disabled' : '' ?>" <?= $isAttendanceDisabled ? 'style="pointer-events: none; opacity: 0.6;"' : '' ?>>
                                                    <i class="bi bi-clipboard-check"></i> บันทึกการเข้าเรียน
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            ไม่พบตารางกิจกรรมสำหรับชุมนุมนี้ (ผู้ดูแลระบบยังไม่ได้ตั้งค่า)
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Modal -->
<div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?= site_url('club/saveActivity/' . $club->club_id) ?>" method="post" id="activityForm">
                <?= csrf_field() ?>
                <input type="hidden" name="activity_date" id="modal_activity_date">
                <div class="modal-header">
                    <h5 class="modal-title" id="activityModalLabel">บันทึกกิจกรรม</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal_activity_name" class="form-label">ชื่อกิจกรรม <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_activity_name" name="activity_name" placeholder="ระบุชื่อกิจกรรมที่ทำในวันนี้" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="modal_activity_location" class="form-label">สถานที่จัดกิจกรรม</label>
                            <input type="text" class="form-control" id="modal_activity_location" name="activity_location" placeholder="เช่น ห้อง 123, สนามฟุตบอล">
                        </div>
                        <div class="col-md-4">
                            <label for="modal_act_number_of_periods" class="form-label">จำนวนคาบที่สอน</label>
                            <input type="number" class="form-control" id="modal_act_number_of_periods" name="act_number_of_periods" value="1" min="1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="modal_activity_start_time" class="form-label">เวลาเริ่มกิจกรรม</label>
                            <input type="time" class="form-control" id="modal_activity_start_time" name="activity_start_time">
                        </div>
                        <div class="col-md-6">
                            <label for="modal_activity_end_time" class="form-label">เวลาสิ้นสุดกิจกรรม</label>
                            <input type="time" class="form-control" id="modal_activity_end_time" name="activity_end_time">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="modal_activity_description" class="form-label">รายละเอียดกิจกรรม</label>
                        <textarea class="form-control" id="modal_activity_description" name="activity_description" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Help Modal -->
<div class="modal fade" id="clubHelpModal" tabindex="-1" aria-labelledby="clubHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clubHelpModalLabel"><i class="bi bi-question-circle-fill me-2"></i>คำแนะนำการใช้งานระบบชุมนุม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php include('help_modal_content.php'); ?>
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
        // When the help modal is shown, activate the correct tab for this page
        $('#clubHelpModal').on('show.bs.modal', function () {
            var tab = new bootstrap.Tab(document.querySelector('#pills-schedule-tab'));
            tab.show();
        });

        $('#activityModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var date = button.data('date');
            var name = button.data('name');
            var description = button.data('description');
            var location = button.data('location');
            var startTime = button.data('start-time');
            var endTime = button.data('end-time');
            var periods = button.data('periods');

            var modal = $(this);
            modal.find('#modal_activity_date').val(date);
            modal.find('#modal_activity_name').val(name);
            modal.find('#modal_activity_description').val(description);
            modal.find('#modal_activity_location').val(location);
            modal.find('#modal_activity_start_time').val(startTime);
            modal.find('#modal_activity_end_time').val(endTime);
            modal.find('#modal_act_number_of_periods').val(periods);
            
            var formattedDate = new Date(date).toLocaleDateString('th-TH', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            modal.find('.modal-title').text('บันทึกกิจกรรมสำหรับวันที่ ' + formattedDate);
        });

        $('#activityForm').on('submit', function(event) {
            var startTime = $('#modal_activity_start_time').val();
            var endTime = $('#modal_activity_end_time').val();

            if (!startTime || !endTime) {
                var confirmProceed = confirm('ยังไม่ได้บันทึกเวลาเรียน คุณต้องการดำเนินการต่อหรือไม่?');
                if (!confirmProceed) {
                    event.preventDefault(); // Prevent form submission
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
