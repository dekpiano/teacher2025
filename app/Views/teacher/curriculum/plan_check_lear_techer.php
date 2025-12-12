<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? '') ?><?= esc($lean[0]->lear_namethai ?? '') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>




<div class="container-fluid">
    <?php
    // --- Data Preparation ---
    $planDataLookup = []; // Changed from $planData to $planDataLookup for clarity
    foreach ($checkplan as $p) {
        // Create a unique key for each plan entry
        $planDataLookup[$p->seplan_coursecode][$p->type_name][$p->seplan_usersend] = $p;
    }

    $teacher_info = $planNew[0] ?? null;
    ?>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
                ตรวจแผนการสอนของ: <?= esc($teacher_info->pers_prefix ?? '') ?><?= esc($teacher_info->pers_firstname ?? '') ?> <?= esc($teacher_info->pers_lastname ?? '') ?>
            </h5>
            <div class="d-flex align-items-center">
                <label for="CheckYearCheckPlan" class="form-label me-2 mb-0">ปีการศึกษา:</label>
                <select name="CheckYearCheckPlan" id="CheckYearCheckPlan" class="form-select w-auto">
                    <?php foreach ($CheckYear as $v_CheckYear): ?>
                    <option
                        <?= (service('uri')->getSegment(5) == $v_CheckYear->seplan_year && service('uri')->getSegment(6) == $v_CheckYear->seplan_term) ? "selected":"" ?>
                        value="<?= esc($v_CheckYear->seplan_year.'/'.$v_CheckYear->seplan_term) ?>">
                        <?= esc($v_CheckYear->seplan_term.'/'.$v_CheckYear->seplan_year) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="row" id="subject-cards-container">
                <?php foreach ($planNew as $v_planNew): ?>
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <strong><?= esc($v_planNew->seplan_coursecode) ?></strong> - <?= esc($v_planNew->seplan_namesubject) ?>
                                <small>(ชั้น ม.<?= esc($v_planNew->seplan_gradelevel) ?> | <?= esc($v_planNew->seplan_typesubject) ?>)</small>
                            </h5>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ประเภทเอกสาร</th>
                                        <th>ไฟล์</th>
                                        <th>สถานะ (หน.กลุ่มสาระ)</th>
                                        <th>สถานะ (หน.งาน)</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php
                                    // Iterate over the actual plan items for this course, which are already grouped by course in $planNew
                                    // We need to find all plan items for the current course code from $checkplanLookup
                                    $currentCoursePlans = $planDataLookup[$v_planNew->seplan_coursecode] ?? [];
                                    $activeTypeNames = array_column($activePlanTypes, 'type_name'); // Get active type names

                                    foreach ($currentCoursePlans as $type_name => $plans_by_type) :
                                        // Only display if the type is active
                                        if (in_array($type_name, $activeTypeNames)) :
                                            $found_plan = $plans_by_type[$v_planNew->seplan_usersend] ?? null; // Get the specific plan for this teacher and type
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($type_name) ?></strong>
                                            <?php if ($found_plan && $found_plan->seplan_sendcomment): ?>
                                                <p class="text-muted mb-0 small" title="หมายเหตุจากผู้ส่ง">
                                                    <i class="bi bi-chat-left-text"></i> <?= esc($found_plan->seplan_sendcomment) ?>
                                                </p>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($found_plan && $found_plan->seplan_file): ?>
                                                <a href="<?= env('upload.server.baseurl') . esc($found_plan->seplan_year) . '/' . esc($found_plan->seplan_term) . '/' . rawurlencode($found_plan->seplan_namesubject) . '/' . rawurlencode($found_plan->seplan_file) ?>"
                                                    target="_blank" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye-fill"></i> ดูไฟล์
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-label-danger">ยังไม่ส่ง</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($found_plan): ?>
                                                <?php
                                                $status_class_1 = 'bg-label-warning';
                                                if ($found_plan->seplan_status1 == "ผ่าน") $status_class_1 = 'bg-label-success';
                                                if ($found_plan->seplan_status1 == "ไม่ผ่าน") $status_class_1 = 'bg-label-danger';
                                                ?>
                                                <div class="d-flex align-items-center">
                                                    <select name="seplan_status1" data-planid="<?= esc($found_plan->seplan_ID) ?>" class="form-select form-select-sm seplan_status1 <?= esc($status_class_1) ?>">
                                                        <option value="รอตรวจ" <?= ($found_plan->seplan_status1 == "รอตรวจ") ? 'selected' : '' ?>>รอตรวจ</option>
                                                        <option value="ผ่าน" <?= ($found_plan->seplan_status1 == "ผ่าน") ? 'selected' : '' ?>>ผ่าน</option>
                                                        <option value="ไม่ผ่าน" <?= ($found_plan->seplan_status1 == "ไม่ผ่าน") ? 'selected' : '' ?>>ไม่ผ่าน</option>
                                                    </select>
                                                    <button class="btn btn-sm btn-icon btn-outline-secondary ms-2 show_comment1" data-bs-toggle="modal" data-planid="<?= esc($found_plan->seplan_ID) ?>" data-bs-target="#addcomment1" title="เพิ่ม/แก้ไข หมายเหตุ">
                                                        <i class="bi bi-chat-dots"></i>
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-label-secondary">ไม่มีข้อมูล</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($found_plan): ?>
                                                <?php
                                                $status_class_2 = 'bg-label-warning';
                                                if ($found_plan->seplan_status2 == "ผ่าน") $status_class_2 = 'bg-label-success';
                                                if ($found_plan->seplan_status2 == "ไม่ผ่าน") $status_class_2 = 'bg-label-danger';
                                                ?>
                                                <?php if(session('person_id') == 'pers_051'): // Specific logic for curriculum head ?>
                                                    <div class="d-flex align-items-center">
                                                        <select name="seplan_status2" data-planid="<?= esc($found_plan->seplan_ID) ?>" class="form-select form-select-sm seplan_status2 <?= esc($status_class_2) ?>">
                                                            <option value="รอตรวจ" <?= ($found_plan->seplan_status2 == "รอตรวจ") ? 'selected' : '' ?>>รอตรวจ</option>
                                                            <option value="ผ่าน" <?= ($found_plan->seplan_status2 == "ผ่าน") ? 'selected' : '' ?>>ผ่าน</option>
                                                            <option value="ไม่ผ่าน" <?= ($found_plan->seplan_status2 == "ไม่ผ่าน") ? 'selected' : '' ?>>ไม่ผ่าน</option>
                                                        </select>
                                                        <button class="btn btn-sm btn-icon btn-outline-secondary ms-2 show_comment2" data-bs-toggle="modal" data-planid="<?= esc($found_plan->seplan_ID) ?>" data-bs-target="#addcomment2" title="เพิ่ม/แก้ไข หมายเหตุ">
                                                            <i class="bi bi-chat-dots"></i>
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge <?= esc($status_class_2) ?>"><?= esc($found_plan->seplan_status2) ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-label-secondary">ไม่มีข้อมูล</span>
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
</div>




    <div id="addcomment1" tabindex="-1" aria-labelledby="exampleModalLabel" class="modal fade text-left"
        aria-hidden="true" style="display: none;">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">

                    <form id="form-comment1" class="form-comment1">
                        <div class="form-group">
                            <label for="seplan_comment1">หมายเหตุ:</label>
                            <textarea wrap="hard" class="form-control seplan_comment1" rows="5" name="seplan_comment1"
                                id="seplan_comment1"
                                placeholder="ไม่ผ่านเพราะ เช่น ปรับชื่อรายชื่อ หน้า 5 หรือ ลืมใส่ข้อมูลต้องกรอก"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="button" id="sub_comment1" data-planId class="btn btn-primary">บันทึก</button>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div id="addcomment2" tabindex="-1" aria-labelledby="exampleModalLabel" class="modal fade text-left"
        aria-hidden="true" style="display: none;">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">

                    <form id="form-comment2" class="form-comment2">
                        <div class="form-group">
                            <label for="seplan_comment2">หมายเหตุ:</label>
                            <textarea wrap="hard" class="form-control seplan_comment2" rows="5" name="seplan_comment2"
                                id="seplan_comment2"
                                placeholder="ไม่ผ่านเพราะ เช่น ปรับชื่อรายชื่อ หน้า 5 หรือ ลืมใส่ข้อมูลต้องกรอก"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="button" id="sub_comment2" data-planId class="btn btn-primary">บันทึก</button>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <?= $this->endSection() ?>

    <?= $this->section('scripts') ?>
    <script>
    $(document).ready(function() {
        // Year/Term selection change
        $('#CheckYearCheckPlan').on('change', function() {
            // Assuming the URL structure is /controller/method/teacher_id/lean_id/year/term
            const url = window.location.pathname;
            const parts = url.split('/');
            const newYearTerm = $(this).val();
            // Replace the year and term segments (e.g., segments 5 and 6)
            parts[5] = newYearTerm.split('/')[0];
            parts[6] = newYearTerm.split('/')[1];
            window.location.href = parts.join('/');
        });

        // --- Comment Modal 1 (หน.กลุ่มสาระ) ---
        $(document).on('click', '.show_comment1', function() {
            var planId = $(this).data('planid');
            $('#addcomment1 #sub_comment1').data('planid', planId);
            
            $.ajax({
                url: '<?= site_url('curriculum/check-comment1') ?>',
                type: 'POST',
                data: { planId: planId },
                dataType: 'json',
                success: function(response) {
                    const comment = response?.[0]?.seplan_comment1 || '';
                    $('#addcomment1 #seplan_comment1').val(comment.replace(/<br\s*\/?>/gi, '\n'));
                }
            });
        });

        $('#sub_comment1').on('click', function() {
            var planId = $(this).data('planid');
            var comment = $('#addcomment1 #seplan_comment1').val();
            $.ajax({
                url: '<?= site_url('curriculum/update-comment1') ?>',
                type: 'POST',
                data: { planId: planId, seplan_comment1: comment },
                success: function(response) {
                    if (response == 1) {
                        Swal.fire('สำเร็จ', 'บันทึกหมายเหตุสำเร็จ', 'success');
                        $('#addcomment1').modal('hide');
                    } else {
                        Swal.fire('ผิดพลาด', 'บันทึกหมายเหตุไม่สำเร็จ', 'error');
                    }
                }
            });
        });

        // --- Comment Modal 2 (หน.งาน) ---
        $(document).on('click', '.show_comment2', function() {
            var planId = $(this).data('planid');
            $('#addcomment2 #sub_comment2').data('planid', planId);

            $.ajax({
                url: '<?= site_url('curriculum/check-comment2') ?>',
                type: 'POST',
                data: { planId: planId },
                dataType: 'json',
                success: function(response) {
                    const comment = response?.[0]?.seplan_comment2 || '';
                    $('#addcomment2 #seplan_comment2').val(comment.replace(/<br\s*\/?>/gi, '\n'));
                }
            });
        });

        $('#sub_comment2').on('click', function() {
            var planId = $(this).data('planid');
            var comment = $('#addcomment2 #seplan_comment2').val();
            $.ajax({
                url: '<?= site_url('curriculum/update-comment2') ?>',
                type: 'POST',
                data: { planId: planId, seplan_comment2: comment },
                success: function(response) {
                    if (response == 1) {
                        Swal.fire('สำเร็จ', 'บันทึกหมายเหตุสำเร็จ', 'success');
                        $('#addcomment2').modal('hide');
                    } else {
                        Swal.fire('ผิดพลาด', 'บันทึกหมายเหตุไม่สำเร็จ', 'error');
                    }
                }
            });
        });

        // --- Status Update Dropdowns ---
        $(document).on('change', '.seplan_status1', function() {
            var planId = $(this).data('planid');
            var status = $(this).val();
            var $select = $(this);

            $.ajax({
                url: '<?= site_url('curriculum/update-status1') ?>',
                type: 'POST',
                data: { planId: planId, status1: status },
                dataType: 'json',
                success: function(response) {
                    if (response) {
                        $select.removeClass('bg-label-success bg-label-danger bg-label-warning');
                        if (status === 'ผ่าน') {
                            $select.addClass('bg-label-success');
                        } else if (status === 'ไม่ผ่าน') {
                            $select.addClass('bg-label-danger');
                        } else {
                            $select.addClass('bg-label-warning');
                        }
                        // Optionally show a success message
                        // Swal.fire('สำเร็จ', 'อัปเดตสถานะแล้ว', 'success');
                    }
                }
            });
        });

        $(document).on('change', '.seplan_status2', function() {
            var planId = $(this).data('planid');
            var status = $(this).val();
            var $select = $(this);
            $.ajax({
                url: '<?= site_url('curriculum/update-status2') ?>',
                type: 'POST',
                data: { planId: planId, status2: status },
                dataType: 'json',
                success: function(response) {
                     if (response) {
                        $select.removeClass('bg-label-success bg-label-danger bg-label-warning');
                        if (status === 'ผ่าน') {
                            $select.addClass('bg-label-success');
                        } else if (status === 'ไม่ผ่าน') {
                            $select.addClass('bg-label-danger');
                        } else {
                            $select.addClass('bg-label-warning');
                        }
                    }
                }
            });
        });
    });
    </script>
    <?= $this->endSection() ?>