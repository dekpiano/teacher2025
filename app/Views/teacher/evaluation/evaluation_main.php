<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white py-3">
                <h5 class="mb-0 text-white"><i class="bi bi-file-earmark-pdf me-2"></i> การประเมินผลการปฏิบัติงานข้าราชการหรือพนักงานครูและบุคลากรทางการศึกษาองค์กรปกครองส่วนท้องถิ่น สายงานการสอน ตำแหน่งครู</h5>
                <span class="badge bg-white text-primary">ปีงบประมาณ <?= $current_year ?> | ครั้งที่ <?= $current_round ?></span>
            </div>
            <div class="card-body pt-4">
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="bi bi-info-circle-fill fs-3"></i>
                        </div>
                        <div>
                            <strong>คำแนะนำ:</strong> กรุณาอัปโหลดไฟล์สรุปผลการปฏิบัติงานเป็นไฟล์ <strong>PDF เพียง 1 ไฟล์เท่านั้น</strong> 
                            <br><small>สำหรับรอบการประเมิน: 
                                <?= $current_round == 1 ? "1 ตุลาคม " . ($current_year - 1) . " - 31 มีนาคม " . ($current_year) : "1 เมษายน " . ($current_year) . " - 30 กันยายน " . ($current_year) ?>
                            </small>
                        </div>
                    </div>
                </div>

                <?php if ($evaluation) : ?>
                    <div class="card border border-primary mb-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-check-fill text-success fs-1 me-3"></i>
                                <div>
                                    <h6 class="mb-1">ไฟล์ที่ส่งแล้ว:</h6>
                                    <a href="<?= env('upload.server.baseurl.evaluation') . $evaluation['eva_year'] . '/' . $evaluation['eva_round'] . '/' . $evaluation['eva_file'] ?>" target="_blank" class="text-decoration-none fw-bold">
                                        <i class="bi bi-download me-1"></i> ดูไฟล์ปัจจุบัน
                                    </a>
                                    <br><small class="text-muted">ส่งเมื่อ: <?= date('d/m/Y H:i', strtotime($evaluation['eva_created_at'])) ?></small>
                                </div>
                            </div>
                            <span class="badge bg-label-success">สถานะ: <?= $evaluation['eva_status'] ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <form id="uploadForm" action="<?= base_url('evaluation/upload') ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="eva_year" value="<?= $current_year ?>">
                    <input type="hidden" name="eva_round" value="<?= $current_round ?>">
                    
                    <div class="mb-4">
                        <label for="eva_file" class="form-label fw-bold">เลือกไฟล์ PDF อัปโหลดใหม่ <?= $evaluation ? '(ส่งทับไฟล์เดิม)' : '' ?></label>
                        <input class="form-control form-control-lg border-primary" type="file" id="eva_file" name="eva_file" accept=".pdf" required>
                        <div class="form-text text-danger mt-2">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> ไฟล์ต้องเป็น PDF และขนาดไม่เกิน 20MB
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg shadow">
                            <i class="bi bi-cloud-arrow-up-fill me-2"></i> ยืนยันการอัปโหลดไฟล์
                        </button>
                    </div>
                </form>
            </div>
        </div>

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
                                <th class="text-center">ตรวจสอบ</th>
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
                                            <a href="<?= env('upload.server.baseurl.evaluation') . $row['eva_year'] . '/' . $row['eva_round'] . '/' . $row['eva_file'] ?>" target="_blank" class="btn btn-sm btn-label-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
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
            const $submitBtn = $(this).find('button[type="submit"]');

            if (!file) {
                Swal.fire({ icon: 'error', title: 'กรุณาเลือกไฟล์', text: 'ต้องแนบไฟล์งานวิจัยก่อนส่ง' });
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
                    executeUploadProcess(file, $submitBtn);
                }
            });

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
