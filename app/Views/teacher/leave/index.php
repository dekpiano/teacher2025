<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card mb-4 bg-primary">
            <div class="card-body d-flex align-items-center justify-content-between py-3">
                <div>
                    <h4 class="text-white mb-0">
                        <i class="bi bi-calendar-check-fill me-2"></i>ระบบการลาออนไลน์
                    </h4>
                    <p class="text-white opacity-75 mb-0">บันทึกและติดตามสถานะการลาของคุณ</p>
                </div>
                <button type="button" class="btn btn-white text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalLeave">
                    <i class="bi bi-plus-circle me-1"></i> เขียนใบลา
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Leave Summary Cards -->
<div class="row mb-4">
    <div class="col-12 mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="bi bi-pie-chart-fill me-2 text-primary"></i>สรุปวันลาคงเหลือ
            </h5>
            <?php if ($activeYear): ?>
            <span class="badge bg-label-primary fs-6">
                <i class="bi bi-calendar3 me-1"></i>
                ปีงบประมาณ <?= esc($activeYear->ly_name) ?>
                <small class="ms-2">(<?= date('d/m/Y', strtotime($activeYear->ly_start_date)) ?> - <?= date('d/m/Y', strtotime($activeYear->ly_end_date)) ?>)</small>
            </span>
            <?php endif; ?>
        </div>
    </div>
    <?php 
    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
    $icons = ['bi-heart-pulse-fill', 'bi-briefcase-fill', 'bi-umbrella-fill', 'bi-balloon-heart-fill', 'bi-people-fill', 'bi-moon-stars-fill'];
    foreach ($leaveSummary as $index => $summary): 
        $color = $colors[$index % count($colors)];
        $icon = $icons[$index % count($icons)];
        $percent = $summary['quota'] > 0 ? round(($summary['used'] / $summary['quota']) * 100) : 0;
    ?>
    <div class="col-6 col-md-4 col-lg-2 mb-3">
        <div class="card h-100 border-<?= $color ?> shadow-sm">
            <div class="card-body text-center p-3">
                <div class="mb-2">
                    <i class="bi <?= $icon ?> fs-3 text-<?= $color ?>"></i>
                </div>
                <h6 class="card-title mb-1 text-truncate" title="<?= esc($summary['type_name']) ?>"><?= esc($summary['type_name']) ?></h6>
                <div class="d-flex justify-content-center align-items-baseline mb-2">
                    <span class="fs-4 fw-bold text-<?= $color ?>"><?= number_format($summary['remaining'], 1) ?></span>
                    <span class="text-muted ms-1">/ <?= number_format($summary['quota'], 0) ?> วัน</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-<?= $color ?>" role="progressbar" style="width: <?= $percent ?>%"></div>
                </div>
                <small class="text-muted">ใช้ไป <?= number_format($summary['used'], 1) ?> วัน</small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <!-- Late Arrival Card -->
    <div class="col-6 col-md-4 col-lg-2 mb-3">
        <div class="card h-100 border-danger shadow-sm" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalLateDetails">
            <div class="card-body text-center p-3">
                <div class="mb-2">
                    <i class="bi bi-clock-history fs-3 text-danger"></i>
                </div>
                <h6 class="card-title mb-1">มาสาย</h6>
                <div class="d-flex justify-content-center align-items-baseline mb-2">
                    <span class="fs-4 fw-bold text-danger"><?= number_format($lateCount) ?></span>
                    <span class="text-muted ms-1">ครั้ง</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%"></div>
                </div>
                <small class="text-muted">ปีการศึกษาปัจจุบัน</small>
            </div>
        </div>
    </div>
</div>

<!-- Modal รายละเอียดการมาสาย -->
<div class="modal fade" id="modalLateDetails" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-clock-history me-2 text-danger"></i>รายละเอียดการมาสาย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($lateDetails)): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle text-success fs-1"></i>
                        <p class="mt-2">ยอดเยี่ยมมาก! ไม่พบประวัติการมาสายในปีการศึกษานี้</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr class="table-light">
                                    <th class="text-center" width="60">ลำดับ</th>
                                    <th>วันที่มาสาย</th>
                                    <th class="text-center">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lateDetails as $index => $late): ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td>
                                            <div class="fw-bold"><?= date('d/m/Y', strtotime($late['att_date'])) ?></div>
                                            <small class="text-muted"><?= date('l', strtotime($late['att_date'])) ?></small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-danger">มาสาย</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- ประวัติการลา -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">ประวัติการลา</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>วันที่บันทึก</th>
                                <th>ประเภท</th>
                                <th>เรื่องที่ลา</th>
                                <th>วันที่ลา</th>
                                <th class="text-center">จำนวนวัน</th>
                                <th>สถานะ</th>
                                <th>แหล่งที่มา</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaves as $leave) : ?>
                                <tr>
                                    <td><?= $leave['created_at'] ? date('d/m/Y H:i', strtotime($leave['created_at'])) : '-' ?></td>
                                    <td>
                                        <span class="badge bg-label-info"><?= esc($leave['type_name']) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= esc($leave['topic']) ?></div>
                                        <small class="text-muted"><?= mb_strimwidth(esc($leave['detail'] ?? ''), 0, 50, "...") ?></small>
                                    </td>
                                    <td>
                                        <div><?= date('d/m/Y', strtotime($leave['start_date'])) ?></div>
                                        <small class="text-muted">ถึง <?= date('d/m/Y', strtotime($leave['end_date'])) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold"><?= number_format($leave['total_days'], 1) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($leave['status'] == 'pending') : ?>
                                            <span class="badge bg-label-warning"><i class="bi bi-clock me-1"></i> รออนุมัติ</span>
                                        <?php elseif ($leave['status'] == 'approved') : ?>
                                            <span class="badge bg-label-success"><i class="bi bi-check-circle me-1"></i> อนุมัติแล้ว</span>
                                        <?php elseif ($leave['status'] == 'rejected') : ?>
                                            <span class="badge bg-label-danger"><i class="bi bi-x-circle me-1"></i> ไม่อนุมัติ</span>
                                        <?php else : ?>
                                            <span class="badge bg-label-secondary"><?= esc($leave['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($leave['source'] == 'self') : ?>
                                            <span class="badge bg-label-primary"><i class="bi bi-person-fill me-1"></i> ลาเอง</span>
                                        <?php else : ?>
                                            <span class="badge bg-label-secondary"><i class="bi bi-person-badge me-1"></i> เจ้าหน้าที่บันทึก</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($leave['can_cancel']) : ?>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteLeave(<?= $leave['id'] ?>)">
                                                    <i class="bx bx-trash me-1"></i> ยกเลิกใบลา
                                                </a>
                                            </div>
                                        </div>
                                        <?php else : ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal เขียนใบลา -->
<div class="modal fade" id="modalLeave" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= base_url('leave/create') ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLeaveTitle"><i class="bi bi-pencil-square me-2"></i>เขียนใบลาออนไลน์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="leave_type_id" name="leave_type_id" required>
                                    <option value="">-- เลือกประเภทการลา --</option>
                                    <?php foreach ($leaveSummary as $summary) : ?>
                                        <option value="<?= $summary['type_id'] ?>"><?= esc($summary['type_name']) ?> (คงเหลือ <?= number_format($summary['remaining'], 1) ?> วัน)</option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="leave_type_id">ประเภทการลา <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="leave_period" name="leave_period">
                                    <option value="full">เต็มวัน</option>
                                    <option value="morning">ครึ่งเช้า</option>
                                    <option value="afternoon">ครึ่งบ่าย</option>
                                </select>
                                <label for="leave_period">ระยะเวลา <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="leave_topic" name="leave_topic" placeholder="ตัวอย่าง: ลาป่วยเนื่องจากเป็นไข้หวัด" required />
                                <label for="leave_topic">เรื่องที่ลา <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="date" class="form-control" id="leave_start_date" name="leave_start_date" required />
                                <label for="leave_start_date">ตั้งแต่วันที่ <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="date" class="form-control" id="leave_end_date" name="leave_end_date" required />
                                <label for="leave_end_date">ถึงวันที่ <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-floating form-floating-outline">
                                <textarea class="form-control" id="leave_detail" name="leave_detail" style="height: 100px" placeholder="รายละเอียดการลา (ถ้ามี)"></textarea>
                                <label for="leave_detail">รายละเอียดการลา</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="leave_file" class="form-label">แนบหลักฐาน (ถ้ามี, ไฟล์ .pdf, .jpg, .png)</label>
                            <input class="form-control" type="file" id="leave_file" name="leave_file">
                        </div>
                        <div class="col-12 mt-3" id="quota-info" style="display:none;">
                            <div class="alert alert-info d-flex align-items-center mb-0" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <div>
                                    โควตาคงเหลือ: <span id="remaining-days" class="fw-bold">0</span> วัน 
                                    (ใช้ไปแล้ว <span id="used-days">0</span>/<span id="total-quota">0</span>)
                                    <div id="request-preview" class="small mt-1" style="display:none;">
                                        ลาครั้งนี้: <span id="request-days-preview" class="fw-bold">0</span> วัน
                                    </div>
                                </div>
                            </div>
                            <div id="quota-warning" class="alert alert-danger d-flex align-items-center mt-2 mb-0" style="display:none;">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>ขออภัย วันลาของคุณครูไม่พอสำหรับการลาครั้งนี้</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-leave">ส่งใบลา</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        $('.datatable').DataTable({
            order: [[0, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
            }
        });

        // Real-time Quota Check
        $('#leave_type_id, #leave_start_date, #leave_end_date').on('change', function() {
            checkQuota();
        });
    });

    function checkQuota() {
        const typeId = $('#leave_type_id').val();
        const start = $('#leave_start_date').val();
        const end = $('#leave_end_date').val();

        if (typeId) {
            $.post('<?= base_url('leave/check-quota') ?>', {
                leave_type_id: typeId,
                leave_start_date: start,
                leave_end_date: end
            }, function(res) {
                console.log('Quota Check Response:', res); // Debug log
                
                if (res.status === 'success') {
                    $('#quota-info').show();
                    $('#remaining-days').text(res.remaining.toFixed(1));
                    $('#used-days').text(res.used.toFixed(1));
                    $('#total-quota').text(res.quota);

                    // Only show request preview and warning if both dates are filled
                    if (res.request_days > 0 && start && end) {
                        $('#request-preview').show();
                        $('#request-days-preview').text(res.request_days.toFixed(1));
                        
                        // Check if request exceeds remaining quota
                        if (!res.can_leave) {
                            $('#quota-warning').show();
                            $('#btn-submit-leave').prop('disabled', true);
                        } else {
                            $('#quota-warning').hide();
                            $('#btn-submit-leave').prop('disabled', false);
                        }
                    } else {
                        $('#request-preview').hide();
                        $('#quota-warning').hide();
                        $('#btn-submit-leave').prop('disabled', false);
                    }
                }
            });
        } else {
            $('#quota-info').hide();
            $('#quota-warning').hide();
            $('#btn-submit-leave').prop('disabled', false);
        }
    }

    function deleteLeave(id) {
        Swal.fire({
            title: 'ยกเลิกใบลา?',
            text: "คุณต้องการยกเลิกคำขอลาใช่หรือไม่!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ยกเลิกเลย!',
            cancelButtonText: 'ไม่'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url('leave/delete/') ?>' + id;
            }
        })
    }
</script>
<?= $this->endSection() ?>
