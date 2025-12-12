<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? '') ?><?= esc($lean[0]->lear_namethai ?? '') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>




        <div class="container-fluid">
            <?php
            // Use active plan types passed from the controller
            $distinctTypePlans = array_column($activePlanTypes, 'type_name');
            // Sort them if a specific order is desired
            sort($distinctTypePlans);
            ?>
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">ส่งแผนของ <?= esc($planNew[0]->pers_prefix ?? '') ?><?= esc($planNew[0]->pers_firstname ?? '') ?> <?= esc($planNew[0]->pers_lastname ?? '') ?></h5>
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
                    <div class="table-responsive">
                        <table id="tb_checkplan" class="table table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="w-auto">ปีการศึกษา</th>
                                    <th class="w-25">รหัสชื่อวิชา</th>
                                    <th class="w-auto">ระดับ</th>
                                    <th class="w-auto">ผู้ส่ง</th>
                                    <?php foreach($distinctTypePlans as $tp): // Use dynamically generated types ?>
                                    <th class="w-auto"><?= esc($tp) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Pre-process checkplan for efficient lookup
                                $checkplanLookup = [];
                                foreach ($checkplan as $cp) {
                                    $checkplanLookup[$cp->seplan_coursecode][$cp->type_name][$cp->seplan_usersend] = $cp;
                                }
                                ?>
                                <?php  foreach ($planNew as $v_planNew): ?>
                                <tr>
                                    <td scope="row"><?= esc($v_planNew->seplan_year) ?>/<?= esc($v_planNew->seplan_term) ?></td>
                                    <td><?= esc($v_planNew->seplan_coursecode) ?> <?= esc($v_planNew->seplan_namesubject) ?>
                                        (<?= esc($v_planNew->seplan_typesubject) ?>)</td>
                                    <td>ม.<?= esc($v_planNew->seplan_gradelevel) ?></td>
                                    <td><?= esc($v_planNew->pers_prefix) ?><?= esc($v_planNew->pers_firstname) ?> <?= esc($v_planNew->pers_lastname) ?>
                                    </td>

                                    <?php foreach($distinctTypePlans as $v_typeplan_name): // Iterate over dynamic types ?>
                                    <?php
                                        $found_plan = $checkplanLookup[$v_planNew->seplan_coursecode][$v_typeplan_name][$v_planNew->seplan_usersend] ?? null;
                                    ?>
                                    <td>
                                        <?php if($found_plan && $found_plan->seplan_file == null): ?>
                                    <span class="badge bg-label-danger">ยังไม่ส่ง</span>
                                    <?php elseif($found_plan && $found_plan->seplan_file != null): ?>
                                    <span class="badge bg-label-success">ส่งแล้ว</span>
                                    <a href="<?= base_url('uploads/academic/course/plan/' . esc($found_plan->seplan_year) . '/' . esc($found_plan->seplan_term) . '/' . esc($found_plan->seplan_namesubject) . '/' . esc($found_plan->seplan_file)) ?>"
                                        target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-icon item-edit">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <?php else: ?>
                                    <span class="badge bg-label-secondary">ไม่มีข้อมูล</span>
                                    <?php endif; ?>

                                    <br>
                                    <small><b>ผู้ส่ง :</b> <?= esc($found_plan ? $found_plan->seplan_sendcomment : '-') ?></small> <br>
                                                                            <div class="d-flex align-items-center">
                                                                                <small class="me-2"><b>หน.ก : </b></small>
                                                                                <?php 
                                                                                $status_class_1 = '';
                                                                                if($found_plan && $found_plan->seplan_status1 == "ผ่าน"){
                                                                                    $status_class_1 = 'bg-label-success';
                                                                                }elseif($found_plan && $found_plan->seplan_status1 == "ไม่ผ่าน"){
                                                                                    $status_class_1 = 'bg-label-danger';
                                                                                } else {
                                                                                    $status_class_1 = 'bg-label-warning';
                                                                                }
                                                                                ?>
                                                                                <?php if(session('person_id') == 'pers_014' && session('pers_learning') != ($IDlear ?? '')): // This logic needs to be re-evaluated for CI4 roles ?>
                                                                                <span class="badge <?= esc($status_class_1) ?>"> <?= esc($found_plan ? $found_plan->seplan_status1 : 'รอตรวจ') ?></span>
                                                                                <?php else: ?>
                                                                                <select name="seplan_status1"
                                                                                    data-planId="<?= esc($found_plan ? $found_plan->seplan_ID : '') ?>"
                                                                                    class="form-select form-select-sm seplan_status1 <?= esc($status_class_1) ?>">
                                                                                    <option value="รอตรวจ" <?= ($found_plan && $found_plan->seplan_status1 == "รอตรวจ") ? 'selected' : ''?>>รอตรวจ</option>
                                                                                    <option value="ผ่าน" <?= ($found_plan && $found_plan->seplan_status1 == "ผ่าน") ? 'selected' : ''?>>ผ่าน</option>
                                                                                    <option value="ไม่ผ่าน" <?= ($found_plan && $found_plan->seplan_status1 == "ไม่ผ่าน") ? 'selected' : ''?>>ไม่ผ่าน</option>
                                                                                </select>
                                                                                <div class="IDCom0<?= esc($found_plan ? $found_plan->seplan_ID : '') ?> TbShowComment1 ms-2">
                                                                                    <?= ($found_plan && $found_plan->seplan_status1 == "ไม่ผ่าน") ? '<a href="#" class="show_comment1" data-bs-toggle="modal" data-planId="' . esc($found_plan->seplan_ID) . '" data-bs-target="#addcomment1">หมายเหตุ</a>' : ''?>
                                                                                </div>
                                                                                <?php endif; ?>
                                                                            </div>                                    <br>
                                                                            <div class="d-flex align-items-center">
                                                                                <small class="me-2"><b>หน.ง : </b></small>
                                                                                <?php 
                                                                                $status_class_2 = '';
                                                                                if($found_plan && $found_plan->seplan_status2 == "ผ่าน"){
                                                                                    $status_class_2 = 'bg-label-success';
                                                                                }elseif($found_plan && $found_plan->seplan_status2 == "ไม่ผ่าน"){
                                                                                    $status_class_2 = 'bg-label-danger';
                                                                                } else {
                                                                                    $status_class_2 = 'bg-label-warning';
                                                                                }
                                                                                ?>
                                                                                <?php if(session('person_id') == 'pers_051'):?>
                                                                                <select name="seplan_status2"
                                                                                    data-planId="<?= esc($found_plan ? $found_plan->seplan_ID : '') ?>"
                                                                                    class="form-select form-select-sm seplan_status2 <?= esc($status_class_2) ?>">
                                                                                    <option value="รอตรวจ" <?= ($found_plan && $found_plan->seplan_status2 == "รอตรวจ") ? 'selected' : ''?>>รอตรวจ</option>
                                                                                    <option value="ผ่าน" <?= ($found_plan && $found_plan->seplan_status2 == "ผ่าน") ? 'selected' : ''?>>ผ่าน</option>
                                                                                    <option value="ไม่ผ่าน" <?= ($found_plan && $found_plan->seplan_status2 == "ไม่ผ่าน") ? 'selected' : ''?>>ไม่ผ่าน</option>
                                                                                </select>
                                                                                <div class="IDCom<?= esc($found_plan ? $found_plan->seplan_ID : '') ?> TbShowComment2 ms-2">
                                                                                    <?= ($found_plan && $found_plan->seplan_status2 == "ไม่ผ่าน") ? '<a href="#" class="show_comment2" data-bs-toggle="modal" data-planId="' . esc($found_plan->seplan_ID) . '" data-bs-target="#addcomment2">หมายเหตุ</a>' : ''?>
                                                                                </div>
                                                                                <?php else: ?>
                                                                                    <span class="badge <?= esc($status_class_2) ?>"><?= esc($found_plan ? $found_plan->seplan_status2 : 'รอตรวจ') ?></span>
                                                                                <?php endif; ?>
                                                                            </div>                                </td>
                            </tr>
                            <?php  endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    
<div id="addcomment1" tabindex="-1" aria-labelledby="exampleModalLabel" class="modal fade text-left" aria-hidden="true"
    style="display: none;">
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

<div id="addcomment2" tabindex="-1" aria-labelledby="exampleModalLabel" class="modal fade text-left" aria-hidden="true"
    style="display: none;">
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
    // JavaScript for handling comments (AJAX calls will need to be implemented in controller)
    $('.show_comment1').on('click', function() {
        var planId = $(this).data('planid');
        $('#addcomment1 #sub_comment1').data('planId', planId);
        // Fetch existing comment if any
        $.ajax({
            url: '<?= site_url('curriculum/check-comment1') ?>',
            type: 'POST',
            data: { planId: planId },
            dataType: 'json',
            success: function(response) {
                if (response && response[0] && response[0].seplan_comment1) {
                    $('#addcomment1 #seplan_comment1').val(response[0].seplan_comment1.replace(/<br>/g, '\n'));
                } else {
                    $('#addcomment1 #seplan_comment1').val('');
                }
            }
        });
    });

    $('#sub_comment1').on('click', function() {
        var planId = $(this).data('planId');
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

    $('.show_comment2').on('click', function() {
        var planId = $(this).data('planid');
        $('#addcomment2 #sub_comment2').data('planId', planId);
        // Fetch existing comment if any
        $.ajax({
            url: '<?= site_url('curriculum/check-comment2') ?>',
            type: 'POST',
            data: { planId: planId },
            dataType: 'json',
            success: function(response) {
                if (response && response[0] && response[0].seplan_comment2) {
                    $('#addcomment2 #seplan_comment2').val(response[0].seplan_comment2.replace(/<br>/g, '\n'));
                } else {
                    $('#addcomment2 #seplan_comment2').val('');
                }
            }
        });
    });

    $('#sub_comment2').on('click', function() {
        var planId = $(this).data('planId');
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

    // Status update dropdowns
    $('#tb_checkplan').on('change', '.seplan_status1', function() {
        var planId = $(this).data('planid');
        var status = $(this).val();
        var $this = $(this);
        $.ajax({
            url: '<?= site_url('curriculum/update-status1') ?>',
            type: 'POST',
            data: { planId: planId, status1: status },
            dataType: 'json',
            success: function(response) {
                if (response && response[0]) {
                    if (response[0].seplan_status1 === 'ไม่ผ่าน') {
                        $('.IDCom0' + planId).html('<a href="#" class="show_comment1" data-toggle="modal" data-planId="' + planId + '" data-target="#addcomment1">หมายเหตุ</a>');
                        $this.removeClass('text-success').addClass('text-danger');
                    } else if (response[0].seplan_status1 === 'ผ่าน') {
                        $('.IDCom0' + planId).empty();
                        $this.removeClass('text-danger').addClass('text-success');
                    } else {
                        $('.IDCom0' + planId).empty();
                        $this.removeClass('text-success text-danger');
                    }
                }
            }
        });
    });

    $('#tb_checkplan').on('change', '.seplan_status2', function() {
        var planId = $(this).data('planid');
        var status = $(this).val();
        var $this = $(this);
        $.ajax({
            url: '<?= site_url('curriculum/update-status2') ?>',
            type: 'POST',
            data: { planId: planId, status2: status },
            dataType: 'json',
            success: function(response) {
                if (response && response[0]) {
                    if (response[0].seplan_status2 === 'ไม่ผ่าน') {
                        $('.IDCom' + planId).html('<a href="#" class="show_comment2" data-toggle="modal" data-planId="' + planId + '" data-target="#addcomment2">หมายเหตุ</a>');
                        $this.removeClass('text-success').addClass('text-danger');
                    } else if (response[0].seplan_status2 === 'ผ่าน') {
                        $('.IDCom' + planId).empty();
                        $this.removeClass('text-danger').addClass('text-success');
                    } else {
                        $('.IDCom' + planId).empty();
                        $this.removeClass('text-success text-danger');
                    }
                }
            }
        });
    });

});
</script>
<?= $this->endSection() ?>
