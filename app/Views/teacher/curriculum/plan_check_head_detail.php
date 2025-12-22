<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? '') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>




<style>
    .teacher-profile-header {
        background: linear-gradient(135deg, #696cff 0%, #71dd37 100%);
        border-radius: 15px;
        padding: 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .status-select {
        border-radius: 6px;
        font-weight: 600;
        border: 1px solid transparent;
        transition: all 0.2s;
    }
    .status-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.25);
    }
    .subject-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .subject-card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eef0f2;
        padding: 0.75rem 1.25rem;
    }
    .file-link {
        display: inline-flex;
        align-items: center;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .file-link:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .btn-comment {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.85rem;
    }
    .table thead th {
        background-color: #fcfcfd;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #566a7f;
    }
</style>

<div class="container-xxl flex-grow-1">
    <!-- Teacher Header -->
    <div class="teacher-profile-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center">
            <div class="avatar avatar-xl me-3 border-3 border-white rounded-circle overflow-hidden shadow">
                 <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= esc($teacher_info->pers_img ?? '') ?>" 
                      alt="Avatar" 
                      class="h-100 w-100 object-fit-cover"
                      onerror="this.onerror=null;this.src='https://placehold.co/100x100/EFEFEF/AAAAAA&text=No+Image';">
            </div>
            <div>
                <h4 class="mb-1 fw-bold text-white"><?= esc($teacher_info->pers_prefix . $teacher_info->pers_firstname . ' ' . $teacher_info->pers_lastname) ?></h4>
                <div class="d-flex align-items-center text-white-50 fs-small">
                    <i class="bi bi-calendar-event me-2"></i>
                    <span>ตรวจสอบแผนการสอน ประจำปีการศึกษา</span>
                </div>
            </div>
        </div>
        <div class="bg-white p-2 rounded-3 shadow-sm d-flex align-items-center">
            <i class="bi bi-filter-circle me-2 text-primary"></i>
            <select name="CheckYearCheckPlan" id="CheckYearCheckPlan" class="form-select border-0 bg-transparent fw-bold" style="width: auto;">
                <?php foreach ($CheckYear as $v_CheckYear): ?>
                <option
                    <?= ($current_year == $v_CheckYear->seplan_year && $current_term == $v_CheckYear->seplan_term) ? "selected":"" ?>
                    value="<?= esc($v_CheckYear->seplan_year.'/'.$v_CheckYear->seplan_term) ?>">
                    ภาคเรียนที่ <?= esc($v_CheckYear->seplan_term) ?> / <?= esc($v_CheckYear->seplan_year) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Subjects Grid -->
    <div class="row" id="subject-cards-container">
        <?php
        // Data grouping and sorting
        $groupedPlans = [];
        foreach ($plans as $plan_item) {
            if (!isset($groupedPlans[$plan_item->seplan_coursecode])) {
                $groupedPlans[$plan_item->seplan_coursecode] = (object)[
                    'seplan_coursecode' => $plan_item->seplan_coursecode,
                    'seplan_namesubject' => $plan_item->seplan_namesubject,
                    'seplan_gradelevel' => $plan_item->seplan_gradelevel,
                    'seplan_typesubject' => $plan_item->seplan_typesubject,
                    'seplan_is_main_subject' => $plan_item->seplan_is_main_subject ?? 0,
                    'items' => []
                ];
            }
            $groupedPlans[$plan_item->seplan_coursecode]->items[] = $plan_item;
        }
        
        usort($groupedPlans, function($a, $b) {
            return ($b->seplan_is_main_subject ?? 0) - ($a->seplan_is_main_subject ?? 0);
        });

        $activeTypeNames = array_column($activePlanTypes, 'type_name');
        ?>

        <?php foreach ($groupedPlans as $v_planNew) : ?>
        <div class="col-12" data-course-code="<?= esc($v_planNew->seplan_coursecode) ?>">
            <div class="card subject-card">
                <div class="subject-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-label-primary p-2 rounded-3 me-3">
                            <i class="bi bi-journal-text fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">
                                [<?= esc($v_planNew->seplan_coursecode) ?>] <?= esc($v_planNew->seplan_namesubject) ?>
                                <?php if (($v_planNew->seplan_is_main_subject ?? 0) == 1) : ?>
                                    <span class="badge bg-label-success ms-2 rounded-pill"><i class="bi bi-star-fill me-1"></i>วิชาหลัก</span>
                                <?php endif; ?>
                            </h5>
                            <small class="text-muted">ม.<?= esc($v_planNew->seplan_gradelevel) ?> • <?= esc($v_planNew->seplan_typesubject) ?></small>
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 25%">ประเภทเอกสาร</th>
                                <th style="width: 15%">ไฟล์</th>
                                <th style="width: 25%">สถานะ (หน.กลุ่มสาระ)</th>
                                <th style="width: 15%">สถานะ (งานหลักสูตร)</th>
                                <th style="width: 20%" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($v_planNew->items as $v_plan) : ?>
                                <?php 
                                if (!in_array($v_plan->type_name, $activeTypeNames)) continue;
                                $isVisible = (($v_planNew->seplan_is_main_subject ?? 0) == 1) || ($v_plan->type_name === 'โครงการสอน');
                                if (!$isVisible) continue;
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="badge bg-label-secondary me-2"><i class="bi bi-file-earmark-text"></i></div>
                                            <span class="fw-bold"><?= esc($v_plan->type_name) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($v_plan->seplan_file): ?>
                                            <?php
                                            $file_ext = strtolower(pathinfo($v_plan->seplan_file, PATHINFO_EXTENSION));
                                            $style_class = ($file_ext == 'pdf') ? 'btn-label-danger' : 'btn-label-primary';
                                            $file_icon = ($file_ext == 'pdf') ? 'bi-file-earmark-pdf' : 'bi-file-earmark-word';
                                            ?>
                                            <a target="_blank" 
                                               href="<?= rtrim($upload_base_url, '/') .'/'. esc($v_plan->seplan_year) . '/' . esc($v_plan->seplan_term) . '/' . rawurlencode(esc($v_plan->seplan_namesubject)) . '/' . rawurlencode(esc($v_plan->seplan_file)) ?>"
                                               class="btn btn-sm <?= $style_class ?> file-link">
                                                <i class="bi <?= $file_icon ?> me-1"></i> ดูไฟล์
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-label-secondary">ยังไม่แนบไฟล์</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status1_class = '';
                                        if ($v_plan->seplan_status1 == 'ผ่าน') $status1_class = 'bg-label-success';
                                        elseif ($v_plan->seplan_status1 == 'ไม่ผ่าน') $status1_class = 'bg-label-danger';
                                        else $status1_class = 'bg-label-warning';
                                        ?>
                                        <select class="form-select status-select form-select-sm seplan_status1 <?= $status1_class ?>" 
                                                data-plan-id="<?= esc($v_plan->seplan_ID) ?>">
                                            <option value="รอตรวจ" <?= $v_plan->seplan_status1 == 'รอตรวจ' ? 'selected' : '' ?>>🟡 รอตรวจ</option>
                                            <option value="ผ่าน" <?= $v_plan->seplan_status1 == 'ผ่าน' ? 'selected' : '' ?>>🟢 ผ่าน</option>
                                            <option value="ไม่ผ่าน" <?= $v_plan->seplan_status1 == 'ไม่ผ่าน' ? 'selected' : '' ?>>🔴 ไม่ผ่าน</option>
                                        </select>
                                    </td>
                                    <td>
                                        <?php
                                        $status2_class = ($v_plan->seplan_status2 == 'ผ่าน') ? 'bg-label-success' : (($v_plan->seplan_status2 == 'ไม่ผ่าน') ? 'bg-label-danger' : 'bg-label-warning');
                                        ?>
                                        <span class="badge rounded-pill <?= $status2_class ?> px-3">
                                            <?= esc($v_plan->seplan_status2) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-icon btn-outline-primary btn-comment" 
                                                data-plan-id="<?= esc($v_plan->seplan_ID) ?>" 
                                                data-comment-type="1"
                                                title="ความคิดเห็น">
                                            <i class="bi bi-chat-dots"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
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
            "<?= site_url('assessment-head/check-plan-detail/' . ($teacher_info->pers_id ?? '') . '/') ?>" +
            selectedYearTerm;
    });

    // Handle status change (delegated to document)
    $(document).on('change', '.seplan_status1', function() {
        const planId = $(this).data('plan-id');
        const status = $(this).val();

        Swal.fire({
            title: 'กำลังบันทึก...',
            text: 'กรุณารอสักครู่',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= site_url('assessment-head/update_status1') ?>',
            method: 'POST',
            data: {
                plan_id: planId,
                status: status
            },
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: 'อัปเดตสถานะเรียบร้อย',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    const selectElement = $(`.seplan_status1[data-plan-id='${planId}']`);
                    selectElement.removeClass('bg-label-success bg-label-danger bg-label-warning');
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
            },
            error: function() {
                Swal.close();
                Swal.fire('ผิดพลาด!', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
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
            url: '<?= site_url('assessment-head/get_comment') ?>',
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
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> กำลังบันทึก...');

        const planId = $('#plan_id').val();
        const commentType = $('#comment_type').val();
        const comment = $('#comment-text').val();

        $.ajax({
            url: '<?= site_url('assessment-head/save_comment') ?>',
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
            },
            error: function() {
                Swal.fire('ผิดพลาด!', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
<?= $this->endSection() ?>