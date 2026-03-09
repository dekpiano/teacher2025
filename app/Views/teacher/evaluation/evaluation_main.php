<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
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

        <?php if ($evaluation) : ?>
            <div class="card mb-4 border-start border-success border-5 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-patch-check-fill text-success fs-1 me-3"></i>
                            <div>
                                <h5 class="mb-1 text-success fw-bold">ส่งข้อมูลแล้ว</h5>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= env('upload.server.baseurl.evaluation') . $evaluation['eva_year'] . '/' . $evaluation['eva_round'] . '/' . $evaluation['eva_file'] ?>" target="_blank" class="btn btn-outline-primary">
                                        <i class="bi bi-file-pdf me-1"></i> ไฟล์ PDF เดิม
                                    </a>
                                    <?php if (!empty($evaluation['eva_canva_link'])) : ?>
                                        <a href="<?= $evaluation['eva_canva_link'] ?>" target="_blank" class="btn btn-outline-danger">
                                            <i class="bi bi-play-circle me-1"></i> ลิ้งก์ Canva เดิม
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-1 small text-muted">ส่งล่าสุดเมื่อ: <?= date('d/m/Y H:i', strtotime($evaluation['eva_updated_at'] ?? $evaluation['eva_created_at'])) ?></div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success">สถานะ: <?= $evaluation['eva_status'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form id="uploadForm" action="<?= base_url('evaluation/upload') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="eva_year" value="<?= $current_year ?>">
            <input type="hidden" name="eva_round" value="<?= $current_round ?>">

            <div class="row">
                <div class="col-md-6">
                    <!-- Card 1: PDF Upload -->
                    <div class="card mb-4 shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-1-circle-fill me-2"></i> เอกสารสรุปผลการปฏิบัติงาน (PDF)</h6>
                        </div>
                        <div class="card-body py-4">
                            <div class="mb-0">
                                <label for="eva_file" class="form-label fw-bold">เลือกไฟล์ PDF อัปโหลดใหม่ <?= $evaluation ? '(ส่งทับไฟล์เดิม)' : '' ?></label>
                                <input class="form-control form-control-lg border-primary" type="file" id="eva_file" name="eva_file" accept=".pdf" <?= $evaluation ? '' : 'required' ?>>
                                <div class="form-text text-danger mt-3">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> ไฟล์ PDF ขนาดไม่เกิน 20MB
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Card 2: Canva Link -->
                    <div class="card mb-4 shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-2-circle-fill me-2"></i> สื่อนำเสนอผลการปฏิบัติงาน (Canva)</h6>
                        </div>
                        <div class="card-body py-4">
                            <div class="mb-0">
                                <label for="eva_canva_link" class="form-label fw-bold">ลิ้งก์วิดีโอนำเสนอจาก Canva หรือสื่ออื่นๆ</label>
                                <div class="input-group input-group-merge border-primary">
                                    <span class="input-group-text bg-light"><i class="bi bi-link-45deg"></i></span>
                                    <input type="url" class="form-control form-control-lg" id="eva_canva_link" name="eva_canva_link" placeholder="https://www.canva.com/design/..." value="<?= $evaluation['eva_canva_link'] ?? '' ?>">
                                </div>
                                <div class="form-text mt-3">ส่งเป็น URL จาก Canva, YouTube หรือ Google Drive เพื่อเป็นสื่อประกอบ</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid mb-5 mt-2">
                <button type="submit" class="btn btn-primary btn-lg shadow p-3">
                    <i class="bi bi-cloud-arrow-up-fill me-2"></i> บันทึกและส่งผลการปฏิบัติงานทั้งหมด
                </button>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($history)) : ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4">ไม่พบประวัติการส่ง</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($history as $row) : ?>
                                    <tr>
                                        <td><?= $row['eva_year'] ?></td>
                                        <td>ครั้งที่ <?= $row['eva_round'] ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['eva_created_at'])) ?></td>
                                        <td class="text-center">
                                            <a href="<?= env('upload.server.baseurl.evaluation') . $row['eva_year'] . '/' . $row['eva_round'] . '/' . $row['eva_file'] ?>" target="_blank" class="btn btn-icon btn-label-primary">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($row['eva_canva_link'])) : ?>
                                                <a href="<?= $row['eva_canva_link'] ?>" target="_blank" class="btn btn-icon btn-label-danger">
                                                    <i class="bi bi-play-circle-fill"></i>
                                                </a>
                                            <?php else : ?>
                                                <span class="text-muted small">- ไม่มี -</span>
                                            <?php endif; ?>
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
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            
            const fileInput = $('#eva_file')[0];
            const file = fileInput.files[0];
            const canvaLink = $('#eva_canva_link').val().trim();
            const $submitBtn = $(this).find('button[type="submit"]');

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
                title: 'ยืนยันการส่ง?',
                text: "ข้อมูลเดิมจะถูกแทนที่ด้วยไฟล์ปัจจุบัน (หากมี)",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ยืนยันอัปโหลด',
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
                            restoreBtn();
                        }
                    },
                    error: function() {
                        Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                        restoreBtn();
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
                    formData.append('path', `academic/teacher/evaluation/${year}/${round}`);
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
                                restoreBtn();
                            }
                        },
                        error: function() {
                            Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                            restoreBtn();
                        }
                    });

                } catch (err) {
                    console.error('Upload Error:', err);
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'การอัปโหลดขัดข้อง', 
                        text: 'ไม่สามารถส่งไฟล์ได้ (แนะนำให้ลองบีบอัดไฟล์ PDF หรือเชื่อมต่ออินเทอร์เน็ตที่เสถียร)' 
                    });
                    restoreBtn();
                }
            }

            function restoreBtn() {
                $submitBtn.prop('disabled', false).html('<i class="bi bi-cloud-arrow-up-fill me-2"></i> ยืนยันการอัปโหลดไฟล์');
            }
        });
    });
</script>
<?= $this->endSection() ?>
