<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center position-relative" style="z-index: 2;">
                        <div class="mb-3 mb-md-0">
                            <h2 class="fw-bold text-white mb-1">ประวัติการอบรมและผลงาน (Training & Works)</h2>
                            <p class="mb-0 opacity-75"><?= $dateLabel ?></p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <form action="<?= base_url('portfolio') ?>" method="GET" class="d-flex align-items-center bg-white bg-opacity-10 p-2 rounded-3 border border-white border-opacity-25 shadow-sm">
                                <i class="bi bi-filter-left text-white me-2"></i>
                                <select name="filter" class="form-select form-select-sm border-0 shadow-none bg-white text-dark fw-bold rounded-2" onchange="this.form.submit()" style="min-width: 280px;">
                                    <?php foreach ($filterOptions as $opt): ?>
                                        <option value="<?= $opt['value'] ?>" <?= $selectedFilter == $opt['value'] ? 'selected' : '' ?>>
                                            <?= $opt['label'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                            <div class="d-none d-xl-block">
                                <i class="bi bi-person-workspace" style="font-size: 3rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Decorative shapes -->
                    <div class="position-absolute" style="top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                    <div class="position-absolute" style="bottom: -30px; left: 10%; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Tabs & Forms -->
        <div class="col-lg-8">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-pills mb-3 gap-2" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active d-flex align-items-center" role="tab" data-bs-toggle="tab" data-bs-target="#navs-training" aria-controls="navs-training" aria-selected="true">
                            <i class="bi bi-calendar-check me-2"></i> ประวัติการอบรม
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link d-flex align-items-center" role="tab" data-bs-toggle="tab" data-bs-target="#navs-academic" aria-controls="navs-academic" aria-selected="false">
                            <i class="bi bi-book me-2"></i> ผลงานวิชาการ / กิจกรรม
                        </button>
                    </li>
                </ul>
                <div class="tab-content p-0 bg-transparent border-0 shadow-none">
                    <!-- Training Tab -->
                    <div class="tab-pane fade show active" id="navs-training" role="tabpanel">
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3" style="border-radius: 15px 15px 0 0;">
                                <h5 class="mb-0 fw-bold"><i class="bi bi-list-stars text-primary me-2"></i>รายการการอบรม</h5>
                                <button class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTraining">
                                    <i class="bi bi-plus-circle me-1"></i> เพิ่มรายการ
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">ชื่อหลักสูตร</th>
                                                <th>สถานที่</th>
                                                <th>วันที่</th>
                                                <th class="text-center">ชม.</th>
                                                <th class="text-center">เกียรติบัตร</th>
                                                <th class="text-center">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($trainings)): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center py-5 opacity-50">ยังไม่มีข้อมูลการอบรม</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($trainings as $t): ?>
                                                    <tr>
                                                        <td class="ps-4 fw-bold text-dark"><?= esc($t['train_name']) ?></td>
                                                        <td><small><?= esc($t['train_location']) ?></small></td>
                                                        <td><small><?= date('d/m/Y', strtotime($t['train_start_date'])) ?></small></td>
                                                        <td class="text-center"><span class="badge bg-label-info"><?= esc($t['train_hours']) ?></span></td>
                                                        <td class="text-center">
                                                            <?php if (!empty($t['train_certificate'])): ?>
                                                                <a href="<?= $remote_base_url ?>/personnel/teacher/training/<?= session()->get('person_id') ?>/<?= $t['train_certificate'] ?>" target="_blank" class="btn btn-icon btn-sm btn-label-success rounded-circle">
                                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="text-muted fs-tiny">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="dropdown">
                                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                    <i class="bi bi-three-dots-vertical"></i>
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item edit-training" href="javascript:void(0);" data-id="<?= $t['id'] ?>" data-json='<?= json_encode($t) ?>'><i class="bi bi-pencil-square me-1"></i> แก้ไข</a>
                                                                    <a class="dropdown-item delete-training text-danger" href="javascript:void(0);" data-id="<?= $t['id'] ?>"><i class="bi bi-trash me-1"></i> ลบ</a>
                                                                </div>
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

                    <!-- Academic/Document Tab -->
                    <div class="tab-pane fade" id="navs-academic" role="tabpanel">
                         <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3" style="border-radius: 15px 15px 0 0;">
                                <h5 class="mb-0 fw-bold"><i class="bi bi-images text-success me-2"></i>รายการผลงานและกิจกรรม</h5>
                                <button class="btn btn-success btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalDocument">
                                    <i class="bi bi-plus-circle me-1"></i> เพิ่มรายการ
                                </button>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <?php if (empty($documents)): ?>
                                        <div class="col-12 text-center py-5 opacity-50">ยังไม่มีข้อมูลผลงานหรือกิจกรรม</div>
                                    <?php else: ?>
                                        <?php foreach ($documents as $d): ?>
                                            <div class="col-md-6 col-xl-4">
                                                <div class="card h-100 border-0 shadow-sm bento-card overflow-hidden d-flex flex-column">
                                                    <!-- Content -->
                                                    <div class="card-body p-4 d-flex flex-column flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <span class="badge bg-label-primary fs-tiny">
                                                                <i class="bi bi-tag-fill me-1"></i> <?= esc($d['doc_category']) ?>
                                                            </span>
                                                            <div class="dropdown">
                                                                <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                                                                    <i class="bi bi-three-dots-vertical"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="<?= $remote_base_url ?>/<?= $d['file_path'] ?>" target="_blank"><i class="bi bi-eye me-2"></i>ดูผลงาน</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li><a class="dropdown-item text-danger delete-doc" href="javascript:void(0);" data-id="<?= $d['id'] ?>"><i class="bi bi-trash me-2"></i>ลบผลงาน</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <h6 class="card-title fw-bold mb-2" title="<?= esc($d['doc_title']) ?>" style="line-height: 1.5; font-size: 1.05rem;">
                                                            <?= esc($d['doc_title']) ?>
                                                        </h6>
                                                        <p class="text-muted small mb-4" style="font-size: 0.85rem; line-height: 1.6;"><?= esc($d['doc_note'] ?: 'ไม่มีรายละเอียด') ?></p>
                                                        
                                                        <div class="mt-auto pt-3 d-flex justify-content-between align-items-center border-top border-light">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-xs bg-label-secondary me-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                                    <i class="bi bi-calendar-event fs-tiny"></i>
                                                                </div>
                                                                <span class="text-muted fs-tiny"><?= date('d M Y', strtotime($d['doc_date'])) ?></span>
                                                            </div>
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi bi-file-earmark-text text-muted me-1 fs-tiny"></i>
                                                                <span class="text-muted fs-tiny fw-medium"><?= number_format(($d['file_size'] ?? 0) / 1024, 1) ?> KB</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Stats & Profile Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <!-- <div class="avatar avatar-xl mx-auto mb-3">
                            <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= session()->get('person_img') ?>" alt="Profile" class="rounded-circle border border-3 border-white shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                        </div> -->
                        <h5 class="fw-bold mb-0">ครู<?= session()->get('fullname') ?></h5>
                        <p class="text-primary mb-1 small fw-bold"><?= $user_info->posi_name ?? 'ยังไม่มีตำแหน่ง' ?></p>
                        <!-- <small class="text-muted"><?= session()->get('pers_learning') ?: 'ยังไม่ได้ระบุกลุ่มสาระ' ?></small> -->
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="bg-label-primary p-3 rounded-3 text-center h-100">
                                <h3 class="mb-0 fw-bold"><?= count($trainings) ?></h3>
                                <small class="text-uppercase fw-bold fs-tiny">ครั้งการอบรม</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-label-success p-3 rounded-3 text-center h-100">
                                <h3 class="mb-0 fw-bold"><?= array_sum(array_column($trainings, 'train_hours')) ?></h3>
                                <small class="text-uppercase fw-bold fs-tiny">ชั่วโมงรวม</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="bg-label-warning p-3 rounded-3 text-center">
                                <h3 class="mb-0 fw-bold"><?= count($documents) ?></h3>
                                <small class="text-uppercase fw-bold fs-tiny">ผลงานและกิจกรรมรวม</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tip/Quote -->
            <div class="card bg-label-info border-0 shadow-none" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="avatar bg-info rounded me-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-lightbulb text-white fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">คำแนะนำ</h6>
                            <p class="small mb-0 opacity-75">การบันทึกประวัติการอบรมและผลงานอย่างสม่ำเสมอ จะช่วยให้การทำรายงาน PA และการขอเลื่อนวิทยฐานะทำได้ง่ายและรวดเร็วขึ้น</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Training -->
<div class="modal fade" id="modalTraining" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg" action="<?= base_url('portfolio/save-training') ?>" method="POST" enctype="multipart/form-data" style="border-radius: 20px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTrainingTitle">เพิ่มข้อมูลการอบรม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="train_id">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">ชื่อหลักสูตร / หัวข้อสัมมนา</label>
                        <input type="text" name="train_name" id="train_name" class="form-control" placeholder="เช่น การจัดการเรียนรู้ในศตวรรษที่ 21" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">สถานที่ / หน่วยงานที่จัด</label>
                        <input type="text" name="train_location" id="train_location" class="form-control" placeholder="เช่น โรงแรม... หรือ มหาวิทยาลัย...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">วันที่เริ่ม</label>
                        <input type="date" name="train_start_date" id="train_start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">วันที่สิ้นสุด</label>
                        <input type="date" name="train_end_date" id="train_end_date" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">จำนวนชั่วโมง</label>
                        <input type="number" name="train_hours" id="train_hours" class="form-control" placeholder="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">ไฟล์เกียรติบัตร (PDF/Image)</label>
                        <input type="file" name="train_certificate" class="form-control" accept=".pdf,image/*">
                        <small class="text-muted mt-1 d-block"><i class="bi bi-info-circle me-1"></i>อัปโหลดไฟล์ใหม่เพื่อเปลี่ยนไฟล์เดิม</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-label-secondary rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">บันทึกข้อมูล</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Document -->
<div class="modal fade" id="modalDocument" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg" action="<?= base_url('portfolio/save-document') ?>" method="POST" enctype="multipart/form-data" style="border-radius: 20px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">เพิ่มผลงาน / กิจกรรม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">ประเภท</label>
                        <select name="doc_category" class="form-select" required>
                            <option value="ผลงานวิชาการ">ผลงานวิชาการ</option>
                            <option value="รูปภาพกิจกรรม">รูปภาพกิจกรรม</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">หัวข้อ / ชื่อผลงาน</label>
                        <input type="text" name="doc_title" class="form-control" placeholder="ระบุหัวข้อหรือชื่อผลงาน" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">วันที่</label>
                        <input type="date" name="doc_date" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">รายละเอียด / หมายเหตุ</label>
                        <textarea name="doc_note" class="form-control" rows="3" placeholder="ระบุรายละเอียดเพิ่มเติม..."></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">ไฟล์หรือรูปภาพ</label>
                        <input type="file" name="portfolio_file" class="form-control" accept=".pdf,image/*" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-label-secondary rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-success rounded-pill px-4">บันทึกข้อมูล</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Helper for Chunked Upload
    async function uploadFileInChunks(file, uploadPath, fileName) {
        const chunkSize = 512 * 1024; // 512KB chunks
        const totalChunks = Math.ceil(file.size / chunkSize);
        
        for (let i = 0; i < totalChunks; i++) {
            const start = i * chunkSize;
            const end = Math.min(file.size, start + chunkSize);
            const chunk = file.slice(start, end);

            const formData = new FormData();
            formData.append('file', chunk);
            formData.append('path', uploadPath);
            formData.append('filename', fileName);
            formData.append('chunk', i);
            formData.append('chunks', totalChunks);

            // Update Progress Bar
            const percent = Math.round((i / totalChunks) * 100);
            Swal.update({
                html: `
                    <div class="progress mt-3" style="height: 20px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                             role="progressbar" style="width: ${percent}%" 
                             aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100">
                             ${percent}%
                        </div>
                    </div>
                    <p class="mt-2 small text-muted">กำลังอัปโหลดส่วนที่ ${i + 1} จาก ${totalChunks}</p>
                `
            });

            try {
                const response = await $.ajax({
                    url: '<?= base_url('portfolio/upload-chunk') ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false
                });

                let res = response;
                if (typeof response === 'string') {
                    try { res = JSON.parse(response); } catch(e) { console.error('Parse error:', response); throw new Error('Server returned invalid JSON'); }
                }

                if (res.status === 'error') throw new Error(res.message);
                
                // Final Progress update on success
                if (i === totalChunks - 1) {
                    const finalPercent = 100;
                    Swal.update({
                        html: `
                            <div class="progress mt-3" style="height: 20px;">
                                <div class="progress-bar bg-success" 
                                     role="progressbar" style="width: ${finalPercent}%">
                                     ${finalPercent}%
                                </div>
                            </div>
                            <p class="mt-2 small text-success">อัปโหลดไฟล์เสร็จสมบูรณ์ กำลังประมวลผล...</p>
                        `
                    });
                    return res.filename;
                }
            } catch (err) {
                console.error(`Chunk ${i} failed:`, err);
                throw new Error(`อัปโหลดล้มเหลวที่ ${percent}%: ${err.statusText || (err.responseJSON ? err.responseJSON.message : err.message)}`);
            }
        }
        return null;
    }

    // Edit Training
    $('.edit-training').on('click', function() {
        const data = $(this).data('json');
        $('#train_id').val(data.id);
        $('#train_name').val(data.train_name);
        $('#train_location').val(data.train_location);
        $('#train_start_date').val(data.train_start_date ? data.train_start_date.split(' ')[0] : '');
        $('#train_end_date').val(data.train_end_date ? data.train_end_date.split(' ')[0] : '');
        $('#train_hours').val(data.train_hours);
        $('#modalTrainingTitle').text('แก้ไขข้อมูลการอบรม');
        $('#modalTraining').modal('show');
    });

    // Reset Modal on hide
    $('#modalTraining').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $('#train_id').val('');
        $('#modalTrainingTitle').text('เพิ่มข้อมูลการอบรม');
    });

    // Handle Training Form Submit
    $('#modalTraining form').on('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        const fileInput = $(form).find('input[type="file"]')[0];
        const file = fileInput.files[0];
        
        // Hide Modal first
        $('#modalTraining').modal('hide');

        Swal.fire({
            title: 'กำลังบันทึกข้อมูล...',
            text: 'กำลังเตรียมการอัปโหลด',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            if (file) {
                Swal.update({ text: 'กำลังอัปโหลดไฟล์แบบแบ่งส่วน (Chunked)...' });
                const personId = '<?= session()->get('person_id') ?>';
                const uploadPath = `personnel/teacher/training/${personId}`;
                const fileName = `Cert_${Date.now()}.${file.name.split('.').pop()}`;
                const uploadedFileName = await uploadFileInChunks(file, uploadPath, fileName);
                if (uploadedFileName) {
                    formData.set('file_name_ready', uploadedFileName);
                }
            }

            Swal.update({ text: 'กำลังบันทึกรายละเอียดลงฐานข้อมูล...' });
            const res = await $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false
            });

            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => { location.reload(); });
            } else {
                Swal.fire('ผิดพลาด', res.message, 'error');
            }
        } catch (error) {
            Swal.fire('ผิดพลาด', error.message || 'ไม่สามารถอัปโหลดไฟล์ได้', 'error');
        }
    });

    // Handle Document Form Submit
    $('#modalDocument form').on('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        const fileInput = $(form).find('input[type="file"]')[0];
        const file = fileInput.files[0];
        
        // Hide Modal first
        $('#modalDocument').modal('hide');

        Swal.fire({
            title: 'กำลังบันทึกข้อมูล...',
            text: 'กำลังเตรียมการอัปโหลด',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            if (file) {
                Swal.update({ text: 'กำลังอัปโหลดไฟล์แบบแบ่งส่วน (Chunked)...' });
                const personId = '<?= session()->get('person_id') ?>';
                const uploadPath = `personnel/teacher/portfolio/${personId}`;
                const fileName = `Port_${Date.now()}.${file.name.split('.').pop()}`;
                const uploadedFileName = await uploadFileInChunks(file, uploadPath, fileName);
                if (uploadedFileName) {
                    formData.set('file_name_ready', uploadedFileName);
                    formData.set('file_type', file.type);
                    formData.set('file_size', file.size);
                }
            }

            Swal.update({ text: 'กำลังบันทึกรายละเอียดลงฐานข้อมูล...' });
            const res = await $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false
            });

            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => { location.reload(); });
            } else {
                Swal.fire('ผิดพลาด', res.message, 'error');
            }
        } catch (error) {
            Swal.fire('ผิดพลาด', error.message || 'ไม่สามารถอัปโหลดไฟล์ได้', 'error');
        }
    });

    // Delete Training
    $('.delete-training').on('click', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: "ข้อมูลการอบรมและไฟล์เกียรติบัตรจะถูกลบถาวร",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบข้อมูล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'กำลังลบข้อมูล...',
                    didOpen: () => { Swal.showLoading(); }
                });
                $.post('<?= base_url('portfolio/delete-training') ?>/' + id, function(res) {
                    if (res.status === 'success') {
                        location.reload();
                    } else {
                        Swal.fire('ผิดพลาด', res.message, 'error');
                    }
                });
            }
        });
    });

    // Delete Document
    $('.delete-doc').on('click', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: "ข้อมูลผลงานและไฟล์จะถูกลบถาวร",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ลบข้อมูล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'กำลังลบข้อมูล...',
                    didOpen: () => { Swal.showLoading(); }
                });
                $.post('<?= base_url('portfolio/delete-document') ?>/' + id, function(res) {
                    if (res.status === 'success') {
                        location.reload();
                    } else {
                        Swal.fire('ผิดพลาด', res.message, 'error');
                    }
                });
            }
        });
    });
});
</script>
<style>
    .bento-card {
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .bento-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .card-image-wrapper img {
        transition: transform 0.6s ease;
    }
    .bento-card:hover .card-image-wrapper img {
        transform: scale(1.1);
    }
    .card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.3);
        opacity: 0;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(2px);
    }
    .bento-card:hover .card-overlay {
        opacity: 1;
    }
    .glass-badge {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.85) !important;
        border: 1px solid rgba(255, 255, 255, 0.2);
        font-weight: 600;
        font-size: 0.75rem;
    }
    .btn-white {
        background: #fff;
        color: #333;
    }
    .btn-white:hover {
        background: #f8f9fa;
        color: #000;
    }
    .btn-icon {
        width: 38px;
        height: 38px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .hover-scale {
        transition: transform 0.2s ease;
    }
    .hover-scale:hover {
        transform: scale(1.15);
    }
    .bg-soft-primary { background-color: #e7e7ff; }
    .fs-tiny { font-size: 0.75rem; }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .transition-all { transition: all 0.3s ease; }
    .swal2-container { z-index: 10000 !important; }
</style>
<?= $this->endSection() ?>
