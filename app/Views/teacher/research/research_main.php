<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'งานวิจัยในชั้นเรียน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$setupData = $setup ?? null;
$is_system_on = false;
$deadline = null;
if ($setupData) {
    $tiemstart = strtotime($setupData->seres_setup_startdate);
    $tiemEnd = strtotime($setupData->seres_setup_enddate);
    $timeNow = time();
    $is_system_on = ($tiemstart < $timeNow && $tiemEnd > $timeNow && $setupData->seres_setup_status == "on");
    $deadline = $setupData->seres_setup_enddate;
}
?>

<?php if(!$is_system_on): ?>
<div class="alert alert-danger d-flex align-items-center" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <div>
        <strong>แจ้งเตือน!</strong> ขณะนี้ระบบปิดรับส่งงานวิจัยในชั้นเรียน
    </div>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">
        <h4 class="card-title mb-0">คำแนะนำ</h4>
    </div>
    <div class="card-body">
        <ul class="list-unstyled">
            <li><i class="bi bi-info-circle-fill text-primary"></i> กรุณาส่งไฟล์งานวิจัยในชั้นเรียนตามรายวิชาที่ท่านรับผิดชอบ</li>
            <li><i class="bi bi-check-circle-fill text-success"></i> แต่ละรายวิชา ส่งได้แค่ไฟล์เดียวเท่านั้น</li>
            <?php if($is_system_on): ?>
            <li><i class="bi bi-clock-fill text-info"></i> ระบบเปิดให้ส่งงาน <strong>(สิ้นสุด: <?= $deadline ? thai_date_and_time(strtotime($deadline)) : '-' ?>)</strong></li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<div class="d-flex justify-content-between mb-3 align-items-center">
    <div>
        <label for="CheckYearSendResearch">ปีการศึกษาที่เปิดรับ:
            <strong><?= esc($setup->seres_setup_term.'/'.$setup->seres_setup_year) ?></strong>
        </label>
        <!-- The dropdown below can be expanded to include past years/terms from submissions if needed -->
        <!-- For now, we only show the current setup year/term -->
        <input type="hidden" id="CheckYearSendResearch" value="<?= esc($setup->seres_setup_year.'/'.$setup->seres_setup_term) ?>">
    </div>
</div>

<div class="row" id="research-cards-container"> 
    <?php if (empty($research)): ?>
        <div class="col-12 text-center">
            <div class="alert alert-info" role="alert">
                ยังไม่มีการส่งงานวิจัยสำหรับปีการศึกษาและภาคเรียนนี้
            </div>
            <?php if ($is_system_on): ?>
            <a href="<?= base_url('research/send') ?>" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle me-1"></i> ส่งงานวิจัย
            </a>
            <?php else: ?>
            <button class="btn btn-secondary mt-3" disabled>
                <i class="bi bi-plus-circle me-1"></i> ระบบปิดรับส่งงานวิจัย
            </button>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php foreach ($research as $v_research) : ?>
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="card-title">
                                    <strong><?= esc($v_research['seres_research_name']) ?></strong>
                                </h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?= esc($v_research['seres_coursecode']) ?> - <?= esc($v_research['seres_namesubject']) ?> (ชั้น ม.<?= esc($v_research['seres_gradelevel']) ?>)</h6>
                                <p class="card-text">
                                    <strong>รายละเอียด:</strong> <?= esc($v_research['seres_sendcomment']) ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="mb-3">
                                    <strong>สถานะ:</strong> 
                                    <?php if (trim($v_research['seres_status']) == 'ส่งแล้ว') : ?>
                                        <span class="badge bg-success">ส่งแล้ว</span>
                                    <?php elseif (trim($v_research['seres_status']) == 'ตรวจแล้ว') : ?>
                                        <span class="badge bg-success">ตรวจแล้ว</span>
                                    <?php else : ?>
                                        <span class="badge bg-warning">รอดำเนินการ</span>
                                    <?php endif; ?>
                                </div>
                                <div class="btn-group w-100" role="group" aria-label="Research Actions">
                                     <?php if (!empty($v_research['seres_file'])) : ?>
                                        <?php
                                        $file_ext = strtolower(pathinfo($v_research['seres_file'], PATHINFO_EXTENSION));
                                        $file_icon = 'bi-file-earmark-arrow-down';
                                        if ($file_ext == 'pdf') $file_icon = 'bi-file-earmark-pdf';
                                        elseif (in_array($file_ext, ['doc', 'docx'])) $file_icon = 'bi-file-earmark-word';
                                        ?>
                                        <a target="_blank" href="<?= env('upload.server.baseurl.research') . $v_research['seres_year'] . '/' . $v_research['seres_term'] . '/' . rawurlencode($v_research['seres_file']) ?>" class="btn btn-outline-secondary" title="ดาวน์โหลด: <?= esc($v_research['seres_file']) ?>">
                                            <i class="bi <?= esc($file_icon) ?>"></i> ดาวน์โหลด
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= site_url('research/edit-research/' . esc($v_research['seres_ID'])) ?>" class="btn btn-primary">
                                        <i class="bi bi-pencil-square"></i> แก้ไขงานวิจัย
                                    </a>
                                    <button class="btn btn-danger delete_research_btn" 
                                        data-seres-id="<?= esc($v_research['seres_ID']) ?>">
                                        <i class="bi bi-trash"></i> ลบ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>


<!-- Modal Update Research -->
<div class="modal fade" id="ModalUpdateResearch" tabindex="-1" aria-labelledby="ModalUpdateResearchLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalUpdateResearchLabel">อัปโหลด/แก้ไขไฟล์งานวิจัย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="update_research_form">
                <div class="modal-body">
                    <input type="hidden" id="seres_ID" name="seres_ID">
                    
                    <div class="mb-3">
                        <label for="seres_research_name_edit" class="form-label">ชื่องานวิจัย</label>
                        <input type="text" class="form-control" id="seres_research_name_edit" name="seres_research_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="seres_namesubject_edit" class="form-label">ชื่อรายวิชา</label>
                        <input type="text" class="form-control" id="seres_namesubject_edit" name="seres_namesubject" required>
                    </div>
                     <div class="mb-3">
                        <label for="seres_coursecode_edit" class="form-label">รหัสวิชา</label>
                        <input type="text" class="form-control" id="seres_coursecode_edit" name="seres_coursecode" required>
                    </div>
                     <div class="mb-3">
                        <label for="seres_gradelevel_edit" class="form-label">ระดับชั้น</label>
                        <input type="text" class="form-control" id="seres_gradelevel_edit" name="seres_gradelevel" required>
                    </div>
                    <div class="mb-3">
                        <label for="seres_sendcomment_edit" class="form-label">รายละเอียดเพิ่มเติม</label>
                        <textarea class="form-control" id="seres_sendcomment_edit" name="seres_sendcomment" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="seres_file" class="form-label">เลือกไฟล์ (เฉพาะ .doc, .docx, .pdf)</label>
                        <input class="form-control" type="file" id="seres_file" name="seres_file" accept=".doc,.docx,.pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf">
                        <small class="text-muted">หากไม่เลือกไฟล์ ไฟล์เดิมจะยังคงอยู่</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-danger">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Year/Term Selection
    document.getElementById('CheckYearSendResearch').addEventListener('change', function() {
        const selectedYearTerm = this.value;
        window.location.href = `<?= site_url('research/') ?>${selectedYearTerm}`;
    });

    // --- Modal Handling for Update --- 
    const modalUpdateResearchEl = document.getElementById('ModalUpdateResearch');
    const modalUpdateResearch = new bootstrap.Modal(modalUpdateResearchEl);

    document.querySelectorAll('.Model_update_research').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('seres_ID').value = this.dataset.seresId;
            document.getElementById('seres_research_name_edit').value = this.dataset.seresResearchName;
            document.getElementById('seres_namesubject_edit').value = this.dataset.seresNamesubject;
            document.getElementById('seres_coursecode_edit').value = this.dataset.seresCoursecode;
            document.getElementById('seres_gradelevel_edit').value = this.dataset.seresGradelevel;
            document.getElementById('seres_sendcomment_edit').value = this.dataset.seresSendcomment;
            modalUpdateResearch.show();
        });
    });

    const updateResearchForm = document.querySelector('.update_research_form');
    if(updateResearchForm) {
        updateResearchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            
            // Show Loading Swal
            Swal.fire({
                title: 'กำลังบันทึกข้อมูล...',
                text: 'กรุณารอสักครู่',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            submitButton.disabled = true;
            const formData = new FormData(this);

            fetch('<?= site_url('research/update-research') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                modalUpdateResearch.hide();
                if (data.status === 'success') {
                    Swal.fire('สำเร็จ', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('ผิดพลาด', data.message, 'error');
                }
            })
            .catch(error => {
                modalUpdateResearch.hide();
                console.error('Error:', error);
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์', 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
            });
        });
    }

    // --- Delete Research ---
    document.querySelectorAll('.delete_research_btn').forEach(button => {
        button.addEventListener('click', function() {
            const seresId = this.dataset.seresId;
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "คุณต้องการลบงานวิจัยนี้ใช่หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show Loading
                    Swal.fire({
                        title: 'กำลังลบ...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`<?= site_url('research/delete-research/') ?>${seresId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('ลบสำเร็จ!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('ผิดพลาด!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('ผิดพลาด!', 'เกิดข้อผิดพลาดในการลบงานวิจัย', 'error');
                    });
                }
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
