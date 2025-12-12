<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? '') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>




<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title">รายการแผนการสอนที่ส่งเข้ามา</h3>
            <div class="mr-2">
                <select name="CheckYearCheckPlan" id="CheckYearCheckPlan" class="form-control w-auto">
                    <?php foreach ($CheckYear as $v_CheckYear): ?>
                    <option
                        <?= ($current_year == $v_CheckYear->seplan_year && $current_term == $v_CheckYear->seplan_term) ? "selected":"" ?>
                        value="<?= esc($v_CheckYear->seplan_year.'/'.$v_CheckYear->seplan_term) ?>">
                        <?= esc($v_CheckYear->seplan_term.'/'.$v_CheckYear->seplan_year) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="card-body">
            <?php
                    // Data preparation similar to plan_main.php
                    $planData = [];
                    $groupedPlans = []; // To hold unique subjects and their details

                    foreach ($plans as $plan_item) {
                        $key = $plan_item->seplan_coursecode . '|' . $plan_item->seplan_typeplan . '|' . $plan_item->seplan_year . '|' . $plan_item->seplan_term;
                        $planData[$key] = $plan_item;

                        // Group unique subjects
                        if (!isset($groupedPlans[$plan_item->seplan_coursecode])) {
                            $groupedPlans[$plan_item->seplan_coursecode] = (object)[
                                'seplan_coursecode' => $plan_item->seplan_coursecode,
                                'seplan_namesubject' => $plan_item->seplan_namesubject,
                                'seplan_gradelevel' => $plan_item->seplan_gradelevel,
                                'seplan_typesubject' => $plan_item->seplan_typesubject,
                                'seplan_is_main_subject' => $plan_item->seplan_is_main_subject ?? 0 // Assuming this might come from DB or default to 0
                            ];
                        }
                    }


                    ?>

            <div class="row" id="subject-cards-container">
                <?php foreach ($groupedPlans as $v_planNew) : ?>
                <div class="col-12 mb-4" data-course-code="<?= esc($v_planNew->seplan_coursecode) ?>"
                    data-is-main-subject="<?= esc($v_planNew->seplan_is_main_subject ?? 0) ?>">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <strong><?= esc($v_planNew->seplan_coursecode) ?></strong> -
                                <?= esc($v_planNew->seplan_namesubject) ?>
                                <small class="">(ชั้น ม.<?= esc($v_planNew->seplan_gradelevel) ?> |
                                    <?= esc($v_planNew->seplan_typesubject) ?>)</small>
                            </h5>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ประเภทเอกสาร</th>
                                        <th>ผู้ส่ง</th>
                                        <th>ไฟล์</th>
                                        <th>สถานะ (หน.กลุ่มสาระ)</th>
                                        <th>สถานะ (หน.งาน)</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php $activeTypeNames = array_column($activePlanTypes, 'type_name'); ?>
                                    <?php foreach ($plans as $v_plan) : // Iterate over actual plan items for this course ?>
                                        <?php if ($v_plan->seplan_coursecode === $v_planNew->seplan_coursecode && in_array($v_plan->type_name, $activeTypeNames)) : // Only display if type is active ?>
                                            <tr data-typeplan="<?= esc($v_plan->type_name) ?>" style="<?php
                                                                // If it's the main subject, show all document types
                                                                if (($v_planNew->seplan_is_main_subject ?? 0) == 1) {
                                                                    echo ''; // No inline style needed, it will be visible by default
                                                                } else {
                                                                    // If it's not the main subject, only show 'โครงการสอน'
                                                                    echo ($v_plan->type_name === 'โครงการสอน') ? '' : 'display: none !important;';
                                                                }
                                                            ?>">
                                                <td><strong><?= esc($v_plan->type_name) ?></strong></td>
                                                <td><?= esc($v_plan->pers_firstname . ' ' . $v_plan->pers_lastname ?? '-') ?>
                                                </td>
                                                <td>
                                                    <?php if ($v_plan && $v_plan->seplan_file): ?>
                                                    <?php
                                                                        $file_ext = strtolower(pathinfo($v_plan->seplan_file, PATHINFO_EXTENSION));
                                                                        $file_icon = 'bi-file-earmark';
                                                                        if ($file_ext == 'pdf') $file_icon = 'bi-file-earmark-pdf-fill text-danger';
                                                                        elseif (in_array($file_ext, ['doc', 'docx'])) $file_icon = 'bi-file-earmark-word-fill text-primary';
                                                                        ?>
                                                    <a target="_blank"
                                                        href="<?= rtrim($upload_base_url, '/') .'/'. esc($v_plan->seplan_year) . '/' . esc($v_plan->seplan_term) . '/' . rawurlencode(esc($v_plan->seplan_namesubject)) . '/' . rawurlencode(esc($v_plan->seplan_file)) ?>"
                                                        class="btn btn-sm btn-info"
                                                        title="ดูไฟล์: <?= esc($v_plan->type_name) ?>">
                                                        <i class="bi <?= esc($file_icon) ?>"></i> ดูไฟล์
                                                    </a>
                                                    <?php else: ?>
                                                    <span class="badge bg-danger">ยังไม่ส่ง</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($v_plan) : ?>
                                                    <?php
                                                                            $status_class = '';
                                                                            if ($v_plan->seplan_status1 == 'ผ่าน') {
                                                                                $status_class = 'bg-label-success';
                                                                            } elseif ($v_plan->seplan_status1 == 'ไม่ผ่าน') {
                                                                                $status_class = 'bg-label-danger';
                                                                            } elseif ($v_plan->seplan_status1 == 'รอตรวจ') {
                                                                                $status_class = 'bg-label-warning';
                                                                            }
                                                                        ?>
                                                    <select
                                                        class="form-select form-select-sm seplan_status1 <?= $status_class ?>"
                                                        data-plan-id="<?= esc($v_plan->seplan_ID) ?>">
                                                        <option value="รอตรวจ"
                                                            <?= $v_plan->seplan_status1 == 'รอตรวจ' ? 'selected' : '' ?>>รอตรวจ
                                                        </option>
                                                        <option value="ผ่าน"
                                                            <?= $v_plan->seplan_status1 == 'ผ่าน' ? 'selected' : '' ?>>ผ่าน
                                                        </option>
                                                        <option value="ไม่ผ่าน"
                                                            <?= $v_plan->seplan_status1 == 'ไม่ผ่าน' ? 'selected' : '' ?>>
                                                            ไม่ผ่าน</option>
                                                    </select>
                                                    <?php else: ?>
                                                    <span class="badge bg-secondary">ไม่มีข้อมูล</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($v_plan) : ?>
                                                    <span
                                                        class="badge <?= $v_plan->seplan_status2 == 'ผ่าน' ? 'bg-success' : ($v_plan->seplan_status2 == 'ไม่ผ่าน' ? 'bg-danger' : 'bg-warning') ?>">
                                                        <?= esc($v_plan->seplan_status2) ?>
                                                    </span>
                                                    <?php else: ?>
                                                    <span class="badge bg-secondary">ไม่มีข้อมูล</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($v_plan) : ?>
                                                    <button class="btn btn-sm btn-primary btn-comment"
                                                        data-plan-id="<?= esc($v_plan->seplan_ID) ?>" data-comment-type="1">
                                                        <i class="bi bi-chat-dots"></i> ความเห็น
                                                    </button>
                                                    <?php else: ?>
                                                    -
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>


    <!-- Comment Modal -->
    <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">แสดงความคิดเห็น</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="comment-form">
                        <input type="hidden" name="plan_id" id="plan_id">
                        <input type="hidden" name="comment_type" id="comment_type">
                        <div class="mb-3">
                            <label for="comment-text" class="col-form-label">ความคิดเห็น:</label>
                            <textarea class="form-control" id="comment-text" name="comment"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" id="save-comment">บันทึก</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // No DataTable initialization needed for multiple tables

    $('#CheckYearCheckPlan').on('change', function() {
        var selectedYearTerm = $(this).val();
        window.location =
            "<?= site_url('curriculum/check-plan-head-detail/' . ($teacher_info->pers_id ?? '') . '/') ?>" +
            selectedYearTerm;
    });

    // Handle status change (delegated to document)
    $(document).on('change', '.seplan_status1', function() {
        const planId = $(this).data('plan-id');
        const status = $(this).val();

        $.ajax({
            url: '<?= site_url('curriculum/update_status1') ?>',
            method: 'POST',
            data: {
                plan_id: planId,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('สำเร็จ!', 'อัปเดตสถานะเรียบร้อย', 'success');
                    const selectElement = $(
                        `.seplan_status1[data-plan-id='${planId}']`);
                    selectElement.removeClass(
                        'bg-label-success bg-label-danger bg-label-warning');
                    if (status === 'ผ่าน') {
                        selectElement.addClass('bg-label-success');
                    } else if (status === 'ไม่ผ่าน') {
                        selectElement.addClass('bg-label-danger');
                    } else if (status === 'รอตรวจ') {
                        selectElement.addClass('bg-label-warning');
                    }
                } else {
                    Swal.fire('ผิดพลาด!', 'ไม่สามารถอัปเดตสถานะได้', 'error');
                }
            }
        });
    });

    // Handle comment modal (delegated to document)
    $(document).on('click', '.btn-comment', function() {
        const planId = $(this).data('plan-id');
        const commentType = $(this).data('comment-type');

        const modal = $('#commentModal');
        modal.find('#plan_id').val(planId);
        modal.find('#comment_type').val(commentType);

        // Fetch existing comment
        $.ajax({
            url: '<?= site_url('curriculum/get_comment') ?>',
            method: 'POST',
            data: {
                plan_id: planId,
                comment_type: commentType
            },
            success: function(response) {
                modal.find('#comment-text').val(response.comment);
            }
        });

        modal.modal('show');
    });

    // Save comment
    $('#save-comment').on('click', function() {
        const planId = $('#plan_id').val();
        const commentType = $('#comment_type').val();
        const comment = $('#comment-text').val();

        $.ajax({
            url: '<?= site_url('curriculum/save_comment') ?>',
            method: 'POST',
            data: {
                plan_id: planId,
                comment_type: commentType,
                comment: comment
            },
            success: function(response) {
                if (response.success) {
                    $('#commentModal').modal('hide');
                    Swal.fire('สำเร็จ!', 'บันทึกความคิดเห็นเรียบร้อย', 'success');
                } else {
                    Swal.fire('ผิดพลาด!', 'ไม่สามารถบันทึกความคิดเห็นได้', 'error');
                }
            }
        });
    });
});
</script>
<?= $this->endSection() ?>