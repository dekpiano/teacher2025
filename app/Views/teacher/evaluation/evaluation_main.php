<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dropdown-hover:hover > .dropdown-menu {
        display: block;
        margin-top: 0;
    }
</style>
<div class="row">
    <div class="col-md-11 mx-auto">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white py-3">
                <h5 class="mb-0 text-white"><i class="bi bi-file-earmark-pdf me-2"></i> การประเมินผลการปฏิบัติงานข้าราชการหรือพนักงานครูและบุคลากรทางการศึกษาองค์กรปกครองส่วนท้องถิ่น สายงานการสอน ตำแหน่งครู</h5>
                <span class="badge bg-white text-primary">ปีงบประมาณ <?= $current_year ?> | ครั้งที่ <?= $current_round ?></span>
            </div>
            <div class="card-body pt-4">
                <div class="alert alert-info border-0 shadow-sm mb-0">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="bi bi-info-circle-fill fs-3"></i>
                        </div>
                        <div>
                            <strong>คำแนะนำ:</strong> กรุณาส่งเอกสารประกอบการประเมินให้ครบถ้วน 
                            <br><span class="badge bg-warning text-dark"><i class="bi bi-info-circle me-1"></i> สามารถเลือกส่งเฉพาะไฟล์ PDF หรือเฉพาะลิ้งก์ Canva อย่างใดอย่างหนึ่งก็ได้</span>
                            <br><small>สำหรับรอบการประเมิน: 
                                <?= $current_round == 1 ? "1 ตุลาคม " . ($current_year - 1) . " - 31 มีนาคม " . ($current_year) : "1 เมษายน " . ($current_year) . " - 30 กันยายน " . ($current_year) ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="uploadForm" class="no-loader" action="<?= base_url('evaluation/upload') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="eva_year" value="<?= $current_year ?>">
            <input type="hidden" name="eva_round" value="<?= $current_round ?>">

            <div class="row">
                <div class="col-md-6">
                    <!-- Card 1: PDF Upload -->
                    <div class="card mb-4 shadow-sm h-100 <?= ($evaluation && !empty($evaluation['eva_file'])) ? 'border-success border-5' : 'border-0' ?>">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-1-circle-fill me-2"></i> เอกสารสรุปผล (PDF)</h6>
                            <?php if ($evaluation && !empty($evaluation['eva_file'])) : ?>
                                <span class="badge bg-label-success"><i class="bi bi-check-circle-fill me-1"></i> ส่งไฟล์แล้ว</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body py-4 h-100 d-flex flex-column">
                            <div class="flex-grow-1">
                                <?php if ($evaluation && !empty($evaluation['eva_file'])) : ?>
                                    <div class="alert alert-outline-success d-flex align-items-center mb-4" role="alert">
                                        <i class="bi bi-file-earmark-pdf-fill fs-3 me-2"></i>
                                        <div>
                                            <div class="fw-bold">พบไฟล์เดิมในระบบ</div>
                                            <a href="<?= env('upload.server.baseurl.evaluation') . $evaluation['eva_year'] . '/' . $evaluation['eva_round'] . '/' . $evaluation['eva_file'] ?>" target="_blank" class="alert-link small">คลิกเพื่อดูไฟล์ที่ส่งแล้ว</a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <label for="eva_file" class="form-label fw-bold">อัปโหลดไฟล์ PDF <?= ($evaluation && !empty($evaluation['eva_file'])) ? 'ใหม่เพื่อแทนที่' : '' ?></label>
                                <input class="form-control form-control-lg border-primary" type="file" id="eva_file" name="eva_file" accept=".pdf">
                                <div class="form-text text-danger mt-3">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ไฟล์ PDF ขนาดไม่เกิน 20MB
                                </div>
                            </div>
                            <button type="submit" id="btn-save-pdf" class="btn btn-primary btn-lg w-100 mt-4 shadow-sm">
                                <i class="bi bi-cloud-arrow-up-fill me-2"></i> บันทึกไฟล์ PDF
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Card 2: Canva Link -->
                    <div class="card mb-4 shadow-sm h-100 <?= ($evaluation && !empty($evaluation['eva_canva_link'])) ? 'border-success border-5' : 'border-0' ?>">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-2-circle-fill me-2"></i> สื่อนำเสนอผลงาน (Canva)</h6>
                            <?php if ($evaluation && !empty($evaluation['eva_canva_link'])) : ?>
                                <span class="badge bg-label-success"><i class="bi bi-check-circle-fill me-1"></i> ส่งลิ้งก์แล้ว</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body py-4 h-100 d-flex flex-column">
                            <div class="flex-grow-1">
                                <?php if ($evaluation && !empty($evaluation['eva_canva_link'])) : ?>
                                    <div class="alert alert-outline-danger d-flex align-items-center mb-4" role="alert">
                                        <i class="bi bi-link-45deg fs-3 me-2"></i>
                                        <div>
                                            <div class="fw-bold">พบลิ้งก์เดิมในระบบ</div>
                                            <a href="<?= $evaluation['eva_canva_link'] ?>" target="_blank" class="alert-link small text-truncate d-inline-block" style="max-width: 200px;">คลิกเพื่อเปิดดูลิ้งก์เดิม</a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <label for="eva_canva_link" class="form-label fw-bold">ลิ้งก์นำเสนอจาก Canva หรือสื่ออื่นๆ</label>
                                <div class="input-group input-group-merge border-primary">
                                    <span class="input-group-text bg-light"><i class="bi bi-link-45deg"></i></span>
                                    <input type="url" class="form-control form-control-lg" id="eva_canva_link" name="eva_canva_link" placeholder="https://www.canva.com/design/..." value="<?= $evaluation['eva_canva_link'] ?? '' ?>">
                                </div>
                                <div class="form-text mt-3">ส่งเป็น URL จาก Canva, YouTube หรือ Google Drive เพื่อเป็นสื่อประกอบ</div>
                            </div>
                            <button type="submit" id="btn-save-canva" class="btn btn-primary btn-lg w-100 mt-4 shadow-sm">
                                <i class="bi bi-link-45deg me-2"></i> บันทึกลิ้งก์สื่อนำเสนอ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i> ประวัติการส่งย้อนหลัง</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ปีงบประมาณ</th>
                                <th>รอบที่</th>
                                <th>วันที่ส่ง</th>
                                <th class="text-center">เอกสาร PDF</th>
                                <th class="text-center">สื่อนำเสนอ</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($history)) : ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">ไม่พบประวัติการส่ง</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($history as $row) : ?>
                                    <tr>
                                        <td><?= $row['eva_year'] ?></td>
                                        <td>ครั้งที่ <?= $row['eva_round'] ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['eva_created_at'])) ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($row['eva_file'])) : ?>
                                                <a href="<?= env('upload.server.baseurl.evaluation') . $row['eva_year'] . '/' . $row['eva_round'] . '/' . $row['eva_file'] ?>" target="_blank" class="btn btn-icon btn-label-primary">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>
                                            <?php else : ?>
                                                <span class="badge bg-label-secondary small pe-none">ไม่ได้ส่ง</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($row['eva_canva_link'])) : ?>
                                                <a href="<?= $row['eva_canva_link'] ?>" target="_blank" class="btn btn-icon btn-label-danger">
                                                    <i class="bi bi-play-circle-fill"></i>
                                                </a>
                                            <?php else : ?>
                                                <span class="badge bg-label-secondary small pe-none">ไม่ได้ส่ง</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown dropdown-hover d-inline-block">
                                                <button class="btn btn-icon btn-label-secondary p-0" type="button">
                                                    <i class="bi bi-trash-fill text-danger fs-5"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li><h6 class="dropdown-header">เลือกสิ่งที่ต้องการลบ</h6></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btn-delete <?= empty($row['eva_file']) ? 'disabled text-muted' : '' ?>" 
                                                           href="javascript:void(0)" 
                                                           data-id="<?= $row['eva_id'] ?>" 
                                                           data-type="file">
                                                            <i class="bi bi-file-earmark-pdf me-2"></i> ลบไฟล์ PDF
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btn-delete <?= empty($row['eva_canva_link']) ? 'disabled text-muted' : '' ?>" 
                                                           href="javascript:void(0)" 
                                                           data-id="<?= $row['eva_id'] ?>" 
                                                           data-type="link">
                                                            <i class="bi bi-link-45deg me-2"></i> ลบลิ้งก์ผลงาน
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center btn-delete text-danger fw-bold" 
                                                           href="javascript:void(0)" 
                                                           data-id="<?= $row['eva_id'] ?>" 
                                                           data-type="all">
                                                            <i class="bi bi-trash3-fill me-2"></i> ลบข้อมูลรายการนี้ทั้งหมด
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        let activeBtn = null;
        let originalHtml = '';

        $('#uploadForm button[type="submit"]').on('click', function() {
            activeBtn = $(this);
            originalHtml = activeBtn.html();
        });

        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            
            const fileInput = $('#eva_file')[0];
            const file = fileInput.files[0];
            const canvaLink = $('#eva_canva_link').val().trim();
            const $submitBtn = activeBtn || $(this).find('button[type="submit"]').first();
            activeBtn = $submitBtn; // safety

            // Check if at least one is provided
            const hasExistingFile = <?= $evaluation && !empty($evaluation['eva_file']) ? 'true' : 'false' ?>;
            
            if (!file && !canvaLink && !hasExistingFile) {
                Swal.fire({ 
                    icon: 'warning', 
                    title: 'ข้อมูลไม่ครบถ้วน', 
                    text: 'กรุณาอัปโหลดไฟล์ PDF หรือใส่ลิ้งก์สื่อนำเสนอ (อย่างใดอย่างหนึ่ง)' 
                });
                return;
            }

            Swal.fire({
                title: 'ยืนยันการบันทึก?',
                text: "ระบบจะบันทึกข้อมูลและส่งผลการปฏิบัติงาน",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (file) {
                        executeUploadProcess(file, $submitBtn);
                    } else {
                        // Only update Canva link or other meta
                        saveOnlyMetadata($submitBtn);
                    }
                }
            });

            async function saveOnlyMetadata(submitButton) {
                submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> กำลังบันทึก...');
                
                const formData = new FormData($('#uploadForm')[0]);
                formData.delete('eva_file');

                $.ajax({
                    url: $('#uploadForm').attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: res.message, timer: 2000, showConfirmButton: false }).then(() => location.reload());
                        } else {
                            Swal.fire('ผิดพลาด', res.message, 'error');
                            restoreBtn(submitButton);
                        }
                    },
                    error: function() {
                        Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                        restoreBtn(submitButton);
                    }
                });
            }

            async function executeUploadProcess(file, submitButton) {
                // Configuration
                const chunkSize = 500 * 1024; // 500KB per chunk
                const totalChunks = Math.ceil(file.size / chunkSize);
                
                // Generate a unique filename
                const teacherId = '<?= session()->get('person_id') ?>';
                const year = '<?= $current_year ?>';
                const round = '<?= $current_round ?>';
                const timestamp = Math.floor(Date.now() / 1000);
                const fileExt = file.name.split('.').pop();
                const finalFileName = `PA_${year}_${round}_${teacherId}_${timestamp}.${fileExt}`;

                Swal.fire({
                    title: 'กำลังอัปโหลด...',
                    html: 'ระบบกำลังเริ่มดำเนินการ (0%)',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> กำลังอัปโหลด...');

                const uploadSingleChunk = async (chunkIndex) => {
                    const start = chunkIndex * chunkSize;
                    const end = Math.min(start + chunkSize, file.size);
                    const chunk = file.slice(start, end);

                    const formData = new FormData();
                    formData.append('file', chunk);
                    formData.append('path', `personnel/teacher/evaluation/${year}/${round}`);
                    formData.append('filename', finalFileName);
                    formData.append('chunk', chunkIndex);
                    formData.append('chunks', totalChunks);

                    return $.ajax({
                        url: '<?= site_url('evaluation/upload-chunk') ?>',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false
                    });
                };

                try {
                    // Upload all chunks sequentially
                    for (let i = 0; i < totalChunks; i++) {
                        await uploadSingleChunk(i);
                        const percent = Math.round(((i + 1) / totalChunks) * 100);
                        Swal.update({ html: `กำลังอัปโหลดส่วนประกอบของไฟล์ (${percent}%)` });
                    }

                    // Chunks uploaded, now save record
                    const formData = new FormData($('#uploadForm')[0]);
                    formData.set('eva_file_name_ready', finalFileName);
                    formData.delete('eva_file'); // Remove original file blob

                    $.ajax({
                        url: $('#uploadForm').attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({ 
                                    icon: 'success', 
                                    title: 'สำเร็จ!', 
                                    text: res.message, 
                                    timer: 2000, 
                                    showConfirmButton: false 
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('ผิดพลาด', res.message, 'error');
                                restoreBtn(submitButton);
                            }
                        },
                        error: function() {
                            Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                            restoreBtn(submitButton);
                        }
                    });

                } catch (err) {
                    console.error('Upload Error:', err);
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'การอัปโหลดขัดข้อง', 
                        text: 'ไม่สามารถส่งไฟล์ได้ (แนะนำให้ลองบีบอัดไฟล์ PDF หรือเชื่อมต่ออินเทอร์เน็ตที่เสถียร)' 
                    });
                    restoreBtn(submitButton);
                }
            }

            function restoreBtn(btn) {
                if (btn) {
                    btn.prop('disabled', false).html(originalHtml);
                }
            }
        });

        // Delete Logic
        $('.btn-delete').on('click', function() {
            const id = $(this).data('id');
            const type = $(this).data('type');
            let typeText = 'ข้อมูล';
            
            if (type === 'file') typeText = 'ไฟล์ PDF';
            else if (type === 'link') typeText = 'ลิ้งก์ผลงาน';
            else if (type === 'all') typeText = 'ข้อมูลและไฟล์ทั้งหมดในรอบนี้';

            Swal.fire({
                title: `ยืนยันการลบ${typeText}?`,
                text: "การดำเนินการนี้ไม่สามารถเรียกคืนได้",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ยืนยันการลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= site_url('evaluation/delete-item') ?>',
                        type: 'POST',
                        data: { id: id, type: type },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({ icon: 'success', title: 'ลบสำเร็จ!', text: res.message, timer: 1500, showConfirmButton: false })
                                .then(() => location.reload());
                            } else {
                                Swal.fire('ผิดพลาด', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
