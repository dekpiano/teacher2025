<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ตรวจสอบงาน') ?><?= !empty($lean[0]->lear_namethai) ? ' - ' . esc($lean[0]->lear_namethai) : '' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <?php
    // Use active plan types passed from the controller
    $distinctTypePlans = array_column($activePlanTypes ?? [], 'type_name');
    sort($distinctTypePlans);
    $teacher_info = $planNew[0] ?? null;
    ?>
    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h5 class="card-title mb-0">
                ส่งแผนของ <?= esc($teacher_info->pers_prefix ?? '') ?><?= esc($teacher_info->pers_firstname ?? '') ?> <?= esc($teacher_info->pers_lastname ?? '') ?>
            </h5>
            <div class="d-flex align-items-center">
                <label for="CheckYearCheckPlan" class="form-label me-2 mb-0 text-nowrap">ปีการศึกษา:</label>
                <select name="CheckYearCheckPlan" id="CheckYearCheckPlan" class="form-select form-select-sm w-auto">
                    <?php foreach (($CheckYear ?? []) as $v_CheckYear): ?>
                    <option
                        <?= (service('uri')->getSegment(5, '') == $v_CheckYear->seplan_year && service('uri')->getSegment(6, '') == $v_CheckYear->seplan_term) ? "selected" : "" ?>
                        value="<?= esc($v_CheckYear->seplan_year . '/' . $v_CheckYear->seplan_term) ?>">
                        ภาคเรียนที่ <?= esc($v_CheckYear->seplan_term . '/' . $v_CheckYear->seplan_year) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tb_checkplan" class="table table-striped table-hover align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-nowrap">ปีการศึกษา</th>
                            <th style="min-width: 200px;">รหัสชื่อวิชา</th>
                            <th class="text-nowrap">ระดับ</th>
                            <th class="text-nowrap">ผู้ส่ง</th>
                            <?php foreach ($distinctTypePlans as $tp): ?>
                            <th style="min-width: 170px;"><?= esc($tp) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Pre-process checkplan for efficient lookup
                        $checkplanLookup = [];
                        foreach (($checkplan ?? []) as $cp) {
                            if (!empty($cp->seplan_coursecode) && !empty($cp->type_name) && !empty($cp->seplan_usersend)) {
                                $checkplanLookup[$cp->seplan_coursecode][$cp->type_name][$cp->seplan_usersend] = $cp;
                            }
                        }
                        ?>
                        <?php foreach (($planNew ?? []) as $v_planNew): ?>
                        <tr>
                            <td class="text-nowrap"><?= esc($v_planNew->seplan_year ?? '') ?>/<?= esc($v_planNew->seplan_term ?? '') ?></td>
                            <td>
                                <strong><?= esc($v_planNew->seplan_coursecode ?? '') ?></strong> <?= esc($v_planNew->seplan_namesubject ?? '') ?>
                                <br><small class="text-muted">(<?= esc($v_planNew->seplan_typesubject ?? '') ?>)</small>
                            </td>
                            <td class="text-nowrap">ม.<?= esc($v_planNew->seplan_gradelevel ?? '') ?></td>
                            <td class="text-nowrap"><?= esc($v_planNew->pers_prefix ?? '') ?><?= esc($v_planNew->pers_firstname ?? '') ?> <?= esc($v_planNew->pers_lastname ?? '') ?></td>

                            <?php foreach ($distinctTypePlans as $v_typeplan_name): ?>
                            <?php
                                $found_plan = $checkplanLookup[$v_planNew->seplan_coursecode][$v_typeplan_name][$v_planNew->seplan_usersend] ?? null;
                            ?>
                            <td>
                                <?php if ($found_plan && !empty($found_plan->seplan_file)): ?>
                                    <span class="badge bg-label-success">ส่งแล้ว</span>
                                    <a href="<?= site_url('curriculum/download-plan-file/' . esc($found_plan->seplan_ID)) ?>"
                                        target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-icon btn-outline-primary" title="ดูไฟล์">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                <?php elseif ($found_plan && empty($found_plan->seplan_file)): ?>
                                    <span class="badge bg-label-danger">ยังไม่ส่ง</span>
                                <?php else: ?>
                                    <span class="badge bg-label-secondary">ไม่มีข้อมูล</span>
                                <?php endif; ?>

                                <br>
                                <small><b>ผู้ส่ง :</b> <?= esc($found_plan->seplan_sendcomment ?? '-') ?></small>
                                <br>

                                <!-- หน.กลุ่มสาระ (Status 1) -->
                                <div class="d-flex align-items-center mt-1">
                                    <small class="me-2 text-nowrap"><b>หน.ก : </b></small>
                                    <?php 
                                    $status_class_1 = 'bg-label-warning';
                                    if ($found_plan && $found_plan->seplan_status1 == "ผ่าน") {
                                        $status_class_1 = 'bg-label-success';
                                    } elseif ($found_plan && $found_plan->seplan_status1 == "ไม่ผ่าน") {
                                        $status_class_1 = 'bg-label-danger';
                                    }
                                    ?>
                                    <?php if (session('person_id') == 'pers_014' && session('pers_learning') != ($IDlear ?? '')): ?>
                                        <span class="badge <?= esc($status_class_1) ?>"><?= esc($found_plan->seplan_status1 ?? 'รอตรวจ') ?></span>
                                    <?php elseif ($found_plan): ?>
                                        <select name="seplan_status1"
                                            data-plan-id="<?= esc($found_plan->seplan_ID) ?>"
                                            class="form-select form-select-sm seplan_status1 <?= esc($status_class_1) ?>" style="width: auto;">
                                            <option value="รอตรวจ" <?= ($found_plan->seplan_status1 == "รอตรวจ") ? 'selected' : '' ?>>รอตรวจ</option>
                                            <option value="ผ่าน" <?= ($found_plan->seplan_status1 == "ผ่าน") ? 'selected' : '' ?>>ผ่าน</option>
                                            <option value="ไม่ผ่าน" <?= ($found_plan->seplan_status1 == "ไม่ผ่าน") ? 'selected' : '' ?>>ไม่ผ่าน</option>
                                        </select>
                                        <div class="IDCom0<?= esc($found_plan->seplan_ID) ?> TbShowComment1 ms-2">
                                            <?php if ($found_plan->seplan_status1 == "ไม่ผ่าน"): ?>
                                                <a href="javascript:void(0);" class="show_comment text-danger" data-bs-toggle="modal" data-plan-id="<?= esc($found_plan->seplan_ID) ?>" data-comment-type="1" data-bs-target="#commentModal"><i class="bi bi-chat-dots-fill"></i> หมายเหตุ</a>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-label-secondary">-</span>
                                    <?php endif; ?>
                                </div>

                                <!-- หน.งาน (Status 2) -->
                                <div class="d-flex align-items-center mt-1">
                                    <small class="me-2 text-nowrap"><b>หน.ง : </b></small>
                                    <?php 
                                    $status_class_2 = 'bg-label-warning';
                                    if ($found_plan && $found_plan->seplan_status2 == "ผ่าน") {
                                        $status_class_2 = 'bg-label-success';
                                    } elseif ($found_plan && $found_plan->seplan_status2 == "ไม่ผ่าน") {
                                        $status_class_2 = 'bg-label-danger';
                                    }
                                    ?>
                                    <?php if (session('person_id') == 'pers_051' && $found_plan): ?>
                                        <select name="seplan_status2"
                                            data-plan-id="<?= esc($found_plan->seplan_ID) ?>"
                                            class="form-select form-select-sm seplan_status2 <?= esc($status_class_2) ?>" style="width: auto;">
                                            <option value="รอตรวจ" <?= ($found_plan->seplan_status2 == "รอตรวจ") ? 'selected' : '' ?>>รอตรวจ</option>
                                            <option value="ผ่าน" <?= ($found_plan->seplan_status2 == "ผ่าน") ? 'selected' : '' ?>>ผ่าน</option>
                                            <option value="ไม่ผ่าน" <?= ($found_plan->seplan_status2 == "ไม่ผ่าน") ? 'selected' : '' ?>>ไม่ผ่าน</option>
                                        </select>
                                        <div class="IDCom<?= esc($found_plan->seplan_ID) ?> TbShowComment2 ms-2">
                                            <?php if ($found_plan->seplan_status2 == "ไม่ผ่าน"): ?>
                                                <a href="javascript:void(0);" class="show_comment text-danger" data-bs-toggle="modal" data-plan-id="<?= esc($found_plan->seplan_ID) ?>" data-comment-type="2" data-bs-target="#commentModal"><i class="bi bi-chat-dots-fill"></i> หมายเหตุ</a>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge <?= esc($status_class_2) ?>"><?= esc($found_plan->seplan_status2 ?? 'รอตรวจ') ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal หมายเหตุ (Unified Bootstrap 5 Modal) -->
<div id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" class="modal fade" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel">บันทึกหมายเหตุ / ข้อเสนอแนะ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal_plan_id" value="">
                <input type="hidden" id="modal_comment_type" value="1">
                <div class="mb-3">
                    <label for="modal_comment_text" class="form-label fw-bold">หมายเหตุ:</label>
                    <textarea wrap="hard" class="form-control" rows="5" id="modal_comment_text"
                        placeholder="ระบุเหตุผลที่ไม่ผ่าน เช่น ปรับแก้หน้า 5 หรือ ลืมใส่กำหนดการสอน"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" id="btn_save_comment" class="btn btn-primary">บันทึก</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Dropdown เปลี่ยนปีการศึกษา
    $('#CheckYearCheckPlan').on('change', function() {
        const selectedYearTerm = $(this).val();
        const url = window.location.pathname;
        const parts = url.split('/');
        // หากมี segment ปี/เทอม ใน URL (index 5 และ 6)
        if (parts.length >= 7) {
            parts[5] = selectedYearTerm.split('/')[0];
            parts[6] = selectedYearTerm.split('/')[1];
            window.location.href = parts.join('/');
        } else {
            window.location.href = '<?= site_url('curriculum/checkPlan/' . ($IDlear ?? '')) ?>/' + selectedYearTerm;
        }
    });

    // เปิด Modal หมายเหตุ (ใช้ Event Delegation)
    $(document).on('click', '.show_comment', function() {
        const planId = $(this).data('plan-id');
        const commentType = $(this).data('comment-type') || 1;

        $('#modal_plan_id').val(planId);
        $('#modal_comment_type').val(commentType);
        $('#modal_comment_text').val('');

        $.ajax({
            url: '<?= site_url('curriculum/get-comment') ?>',
            type: 'POST',
            data: { plan_id: planId, comment_type: commentType },
            dataType: 'json',
            success: function(response) {
                if (response && response.comment) {
                    $('#modal_comment_text').val(response.comment.replace(/<br\s*\/?>/gi, '\n'));
                }
            }
        });
    });

    // บันทึกหมายเหตุ
    $('#btn_save_comment').on('click', function() {
        const planId = $('#modal_plan_id').val();
        const commentType = $('#modal_comment_type').val();
        const comment = $('#modal_comment_text').val();

        if (!planId) return;

        $.ajax({
            url: '<?= site_url('curriculum/save-comment') ?>',
            type: 'POST',
            data: { 
                plan_id: planId, 
                comment_type: commentType, 
                comment: comment 
            },
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'บันทึกหมายเหตุสำเร็จ',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    const modalEl = document.getElementById('commentModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modalInstance.hide();
                } else {
                    Swal.fire('ผิดพลาด', 'บันทึกหมายเหตุไม่สำเร็จ', 'error');
                }
            },
            error: function() {
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์', 'error');
            }
        });
    });

    // อัปเดตสถานะ หน.กลุ่มสาระ (Status 1)
    $(document).on('change', '.seplan_status1', function() {
        const planId = $(this).data('plan-id');
        const status = $(this).val();
        const $select = $(this);

        $.ajax({
            url: '<?= site_url('curriculum/update-status1') ?>',
            type: 'POST',
            data: { plan_id: planId, status: status },
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    $select.removeClass('bg-label-success bg-label-danger bg-label-warning');
                    if (status === 'ผ่าน') {
                        $select.addClass('bg-label-success');
                        $('.IDCom0' + planId).empty();
                    } else if (status === 'ไม่ผ่าน') {
                        $select.addClass('bg-label-danger');
                        $('.IDCom0' + planId).html('<a href="javascript:void(0);" class="show_comment text-danger" data-bs-toggle="modal" data-plan-id="' + planId + '" data-comment-type="1" data-bs-target="#commentModal"><i class="bi bi-chat-dots-fill"></i> หมายเหตุ</a>');
                    } else {
                        $select.addClass('bg-label-warning');
                        $('.IDCom0' + planId).empty();
                    }
                } else {
                    Swal.fire('ผิดพลาด', 'ไม่สามารถอัปเดตสถานะได้', 'error');
                }
            },
            error: function() {
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์', 'error');
            }
        });
    });

    // อัปเดตสถานะ หน.งาน (Status 2)
    $(document).on('change', '.seplan_status2', function() {
        const planId = $(this).data('plan-id');
        const status = $(this).val();
        const $select = $(this);

        $.ajax({
            url: '<?= site_url('curriculum/update-status2') ?>',
            type: 'POST',
            data: { plan_id: planId, status: status },
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    $select.removeClass('bg-label-success bg-label-danger bg-label-warning');
                    if (status === 'ผ่าน') {
                        $select.addClass('bg-label-success');
                        $('.IDCom' + planId).empty();
                    } else if (status === 'ไม่ผ่าน') {
                        $select.addClass('bg-label-danger');
                        $('.IDCom' + planId).html('<a href="javascript:void(0);" class="show_comment text-danger" data-bs-toggle="modal" data-plan-id="' + planId + '" data-comment-type="2" data-bs-target="#commentModal"><i class="bi bi-chat-dots-fill"></i> หมายเหตุ</a>');
                    } else {
                        $select.addClass('bg-label-warning');
                        $('.IDCom' + planId).empty();
                    }
                } else {
                    Swal.fire('ผิดพลาด', 'ไม่สามารถอัปเดตสถานะได้', 'error');
                }
            },
            error: function() {
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์', 'error');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
