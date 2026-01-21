<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ตารางกิจกรรมชุมนุม') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .schedule-header {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        border-radius: 1.25rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .schedule-header::after {
        content: "";
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        pointer-events: none;
    }
    .table-card {
        border-radius: 1.25rem;
        border: none;
        overflow: hidden;
    }
    .week-badge {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #4338ca;
        border-radius: 10px;
        font-weight: 700;
        margin: 0 auto;
    }
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .btn-action {
        border-radius: 0.75rem;
        padding: 0.5rem 1.25rem;
        font-weight: 600;
        transition: all 0.2s;
    }
</style>

<div class="container-fluid py-2">
    <!-- Help Modal (Placed at top for reliability) -->
    <div class="modal fade" id="clubHelpModal" tabindex="-1" aria-labelledby="clubHelpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light py-3">
                    <h5 class="modal-title fw-bold" id="clubHelpModalLabel">
                        <i class="bi bi-question-circle-fill text-primary me-2"></i>คู่มือการใช้งานระบบชุมนุม
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <?php include('help_modal_content.php'); ?>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ปิดคู่มือ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Section -->
    <div class="schedule-header shadow-lg">
        <div class="row align-items-center text-start">
            <div class="col-lg-7">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-light mb-2">
                        <li class="breadcrumb-item"><a href="<?= site_url('home') ?>" class="text-white opacity-75">หน้าหลัก</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('club') ?>" class="text-white opacity-75">ชุมนุม</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">ตารางกิจกรรม</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold text-white mb-1">
                    <i class="bi bi-calendar-week me-2"></i><?= esc($club->club_name) ?>
                </h1>
                <p class="lead mb-0 text-white opacity-75 small">
                    <i class="bi bi-info-circle me-1"></i> ตารางกิจกรรมและบันทึกเวลาเรียนประจำเทอม
                </p>
            </div>
            <div class="col-lg-5 mt-4 mt-lg-0">
                <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                    <div class="btn-group shadow-sm rounded-pill overflow-hidden bg-white p-1">
                        <a href="<?= site_url('club/manual') ?>" class="btn btn-white border-0 rounded-pill px-3 py-2 text-primary fw-bold small">
                            <i class="bi bi-book-half me-2"></i> คู่มือ
                        </a>
                        <button type="button" class="btn btn-white border-0 rounded-pill px-3 py-2 text-muted" data-bs-toggle="modal" data-bs-target="#clubHelpModal">
                            <i class="bi bi-question-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Note Section -->
    <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center p-3 animate__animated animate__fadeIn">
        <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3">
            <i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i>
        </div>
        <div>
            <h6 class="alert-heading fw-bold mb-1">หมายเหตุการบันทึกข้อมูล</h6>
            <p class="mb-0 small text-dark">คุณครูจำเป็นต้องกดปุ่ม <strong class="text-primary"><i class="bi bi-pencil-square"></i> กิจกรรม</strong> เพื่อบันทึกรายละเอียดการสอนในสัปดาห์นั้นๆ ให้เรียบร้อยก่อน ระบบจึงจะเปิดให้กดปุ่ม <strong class="text-primary"><i class="bi bi-person-check"></i> เช็คชื่อ</strong> ได้ครับ</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="card table-card shadow-sm">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-list-stars text-primary me-2"></i>รายสัปดาห์กิจกรรม
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($schedules) && is_array($schedules)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center py-3" style="width: 100px;">สัปดาห์</th>
                                <th class="py-3">วันที่จัดกิจกรรม</th>
                                <th class="py-3">หัวข้อกิจกรรม</th>
                                <th class="text-center py-3">จำนวนคาบ</th>
                                <th class="text-center py-3">สถานะบันทึก</th>
                                <th class="text-center py-3" style="width: 320px;">ดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody class="text-start">
                            <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td class="text-center">
                                        <div class="week-badge"><?= esc($schedule->tcs_week_number) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <?php if ($schedule->tcs_start_date == '0000-00-00' || empty($schedule->tcs_start_date)): ?>
                                                <span class="text-muted"><i class="bi bi-dash-circle me-1"></i>รอกำหนดวันที่</span>
                                            <?php else: ?>
                                                <i class="bi bi-calendar-event text-primary me-1"></i>
                                                <?= esc(date('d/m/Y', strtotime($schedule->tcs_start_date))) ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($schedule->act_name)): ?>
                                            <span class="fw-bold text-indigo"><?= esc($schedule->act_name) ?></span>
                                            <div class="text-muted smallest text-truncate" style="max-width: 250px;">
                                                <?= esc($schedule->act_location ?? 'ไม่ระบุสถานที่') ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic smallest">ยังไม่ได้บันทึกรายละเอียด</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border"><?= esc($schedule->act_number_of_periods ?? '-') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($schedule->attendance_recorded): ?>
                                            <span class="status-badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                                <i class="bi bi-check-circle-fill me-1"></i> เช็คชื่อแล้ว
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                                                <i class="bi bi-clock-history me-1"></i> ยังไม่เช็คชื่อ
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <?php 
                                                $isDateNotSet = ($schedule->tcs_start_date == '0000-00-00' || empty($schedule->tcs_start_date));
                                                $isActivityNotSet = empty($schedule->act_name);
                                                $isAttendanceDisabled = $isDateNotSet || $isActivityNotSet;
                                            ?>
                                            <button type="button" class="btn btn-light btn-sm btn-action border shadow-sm" data-bs-toggle="modal" data-bs-target="#activityModal"
                                                data-date="<?= esc($schedule->tcs_start_date) ?>"
                                                data-name="<?= esc($schedule->act_name ?? '') ?>"
                                                data-description="<?= esc($schedule->act_description ?? '') ?>"
                                                data-location="<?= esc($schedule->act_location ?? '') ?>"
                                                data-start-time="<?= esc($schedule->act_start_time ?? '') ?>"
                                                data-end-time="<?= esc($schedule->act_end_time ?? '') ?>"
                                                data-periods="<?= esc($schedule->act_number_of_periods ?? '1') ?>"
                                                <?= $isDateNotSet ? 'disabled' : '' ?>>
                                                <i class="bi bi-pencil-square me-1"></i> กิจกรรม
                                            </button>
                                            
                                            <?php if ($isAttendanceDisabled): ?>
                                                <button class="btn btn-secondary btn-sm btn-action shadow-sm opacity-50" disabled title="กรุณาบันทึกกิจกรรมก่อนเช็คชื่อ">
                                                    <i class="bi bi-lock-fill me-1"></i> เช็คชื่อ
                                                </button>
                                            <?php else: ?>
                                                <a href="<?= site_url('club/recordAttendance/' . $club->club_id . '/' . $schedule->tcs_schedule_id) ?>" class="btn btn-primary btn-sm btn-action shadow-sm">
                                                    <i class="bi bi-person-check me-1"></i> เช็คชื่อ
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-muted opacity-25" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">ไม่พบข้อมูลตารางกิจกรรม</h5>
                    <p class="text-muted small">ผู้ดูแลระบบยังไม่ได้ตั้งค่าสัปดาห์กิจกรรมสำหรับเทอมนี้</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Activity Modal -->
<div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= site_url('club/saveActivity/' . $club->club_id) ?>" method="post" id="activityForm">
                <?= csrf_field() ?>
                <input type="hidden" name="activity_date" id="modal_activity_date">
                <div class="modal-header bg-primary text-white border-0 py-4">
                    <div class="text-start">
                        <h4 class="modal-title fw-bold text-white" id="activityModalLabel">บันทึกกิจกรรม</h4>
                        <p class="text-white text-opacity-75 small mb-0">ระบุรายละเอียดการจัดการเรียนการสอนในสัปดาห์นี้</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="modal_activity_name" name="activity_name" placeholder="ชื่อกิจกรรม" required>
                        <label for="modal_activity_name">ชื่อกิจกรรม / หัวข้อการเรียนรู้ <span class="text-danger">*</span></label>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-floating mb-4">
                                <input type="text" class="form-control" id="modal_activity_location" name="activity_location" placeholder="สถานที่">
                                <label for="modal_activity_location">สถานที่จัดกิจกรรม (เช่น ห้อง 123)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-4">
                                <input type="number" class="form-control" id="modal_act_number_of_periods" name="act_number_of_periods" value="1" min="1">
                                <label for="modal_act_number_of_periods">จำนวนคาบ</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-4">
                                <input type="time" class="form-control" id="modal_activity_start_time" name="activity_start_time">
                                <label for="modal_activity_start_time">เวลาเริ่ม</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-4">
                                <input type="time" class="form-control" id="modal_activity_end_time" name="activity_end_time">
                                <label for="modal_activity_end_time">เวลาสิ้นสุด</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-0">
                        <textarea class="form-control" id="modal_activity_description" name="activity_description" placeholder="รายละเอียด" style="height: 100px"></textarea>
                        <label for="modal_activity_description">รายละเอียดกิจกรรมเพิ่มเติม (ถ้ามี)</label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold me-auto" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Old Help Modal Removed (Already placed at top) -->

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // When the help modal is shown, activate the correct tab for this page
        $('#clubHelpModal').on('show.bs.modal', function () {
            setTimeout(function() {
                var tabEl = document.querySelector('#help-pills-schedule-tab');
                if (tabEl && typeof bootstrap !== 'undefined') {
                    var tab = new bootstrap.Tab(tabEl);
                    tab.show();
                }
            }, 300);
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
