<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'จัดการชุมนุม') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .manage-header {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        border-radius: 1rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .manage-header::after {
        content: "";
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        pointer-events: none;
    }
    .stat-card {
        border: none;
        border-radius: 1rem;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .table-card {
        border-radius: 1rem;
        border: none;
        overflow: hidden;
    }
    .nav-pills .nav-link {
        border-radius: 0.5rem;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
    }
    .badge-status {
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
    }
    .advisor-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 2px solid rgba(255,255,255,0.5);
        object-fit: cover;
    }
    .advisor-info {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2rem;
        padding: 0.25rem 1rem 0.25rem 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        backdrop-filter: blur(5px);
    }
    .photo-card {
        border-radius: 0.85rem;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .photo-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }
    .photo-thumbnail {
        transition: transform 0.35s ease;
    }
    .photo-card:hover .photo-thumbnail {
        transform: scale(1.05);
    }
    .photo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        opacity: 0;
        transition: opacity 0.25s ease;
        backdrop-filter: blur(2px);
    }
    .photo-card:hover .photo-overlay {
        opacity: 1;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    /* Drag & Drop Upload Zone */
    .dropzone-upload-box {
        border: 2px dashed #10b981;
        border-radius: 1.25rem;
        padding: 2.25rem 1.5rem;
        text-align: center;
        background-color: #f0fdf4;
        cursor: pointer;
        transition: all 0.25s ease-in-out;
        position: relative;
    }
    .dropzone-upload-box:hover {
        border-color: #059669;
        background-color: #ecfdf5;
        transform: translateY(-2px);
    }
    .dropzone-upload-box.dragover {
        border-color: #047857 !important;
        background-color: #d1fae5 !important;
        transform: scale(1.02);
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);
    }
    .dropzone-icon {
        font-size: 3.25rem;
        color: #10b981;
        margin-bottom: 0.5rem;
        transition: transform 0.2s ease;
    }
    .dropzone-upload-box:hover .dropzone-icon {
        transform: translateY(-4px);
    }
    .dropzone-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(95px, 1fr));
        gap: 0.75rem;
        max-height: 230px;
        overflow-y: auto;
        padding: 0.5rem;
        background: #f8fafc;
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
    }
    .dropzone-preview-item {
        position: relative;
        border-radius: 0.5rem;
        overflow: hidden;
        background: #fff;
        border: 1px solid #cbd5e1;
        aspect-ratio: 1;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .dropzone-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .dropzone-preview-remove {
        position: absolute;
        top: 3px;
        right: 3px;
        background: rgba(239, 68, 68, 0.95);
        color: white;
        border: none;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        transition: transform 0.15s ease;
    }
    .dropzone-preview-remove:hover {
        background: #dc2626;
        transform: scale(1.1);
    }
    .page-dropzone-target.dragover {
        border: 2px dashed #10b981 !important;
        background-color: #f0fdf4 !important;
    }
</style>

<div class="container-fluid py-2">
    <!-- Breadcrumb & Header -->
    <div class="manage-header shadow-sm">
        <div class="row align-items-center text-start">
            <div class="col-lg-7">
                <?php 
                    $isScout = !empty($isScout) || \App\Models\ClubModel::isScoutClub($club->club_name);
                    $routePrefix = !empty($routePrefix) ? $routePrefix : ($isScout ? 'scout' : 'club');
                ?>
                <div class="breadcrumb-light-wrapper mb-2">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-light mb-0">
                            <li class="breadcrumb-item"><a href="<?= site_url($routePrefix) ?>"><i class="bi bi-house-door me-1"></i>หน้าหลัก<?= $isScout ? 'ลูกเสือ' : 'ชุมนุม' ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= $isScout ? 'จัดการกองลูกเสือ' : 'จัดการชุมนุม' ?></li>
                        </ol>
                    </nav>
                </div>
                <h2 class="fw-bold mb-1 text-white"><?= esc($club->club_name) ?></h2>
                <div class="d-flex flex-wrap gap-3 align-items-center mt-3">
                    <p class="mb-0 text-white text-opacity-75 small">
                        <i class="bi bi-calendar3 me-1"></i> ปีการศึกษา <?= esc($club->club_year) ?>/<?= esc($club->club_trem) ?>
                    </p>
                    <div class="vr bg-white opacity-25 d-none d-md-block" style="height: 20px;"></div>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if(!empty($advisors)): ?>
                            <?php foreach($advisors as $advisor): ?>
                                <div class="advisor-info">
                                    <?php if(!empty($advisor->pers_img)): ?>
                                        <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= esc($advisor->pers_img) ?>" class="advisor-avatar" alt="Advisor">
                                    <?php else: ?>
                                        <div class="advisor-avatar bg-white text-primary d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="text-white smallest fw-bold"><?= esc($advisor->pers_prefix . $advisor->pers_firstname . ' ' . $advisor->pers_lastname) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 mt-3 mt-lg-0">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <button type="button" class="btn btn-white rounded-pill px-4 py-2 shadow-sm text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#editClubModal">
                        <i class="bi bi-pencil-square me-2"></i> แก้ไขข้อมูล
                    </button>
                    
                    <div class="btn-group shadow-sm rounded-pill overflow-hidden bg-white p-1">
                        <a href="<?= site_url($routePrefix . '/manual') ?>" class="btn btn-white border-0 rounded-pill px-3 py-2 text-primary fw-bold small">
                            <i class="bi bi-book-half me-2"></i> คู่มือ
                        </a>
                        <button type="button" class="btn btn-white border-0 rounded-pill px-3 py-2 text-muted" data-bs-toggle="modal" data-bs-target="#clubHelpModal">
                            <i class="bi bi-question-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($club)): ?>
        <!-- Quick Stats -->
        <div class="row g-3 mb-4 text-start">
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <div class="text-muted small">สมาชิกปัจจุบัน</div>
                                <h4 class="fw-bold mb-0"><?= !empty($members) ? count($members) : 0 ?> / <?= esc($club->club_max_participants) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-info bg-opacity-10 text-info me-3">
                                <i class="bi bi-layers-fill"></i>
                            </div>
                            <div>
                                <div class="text-muted small">ระดับชั้น</div>
                                <h4 class="fw-bold mb-0"><?= esc($club->club_level) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-toggle-on"></i>
                            </div>
                            <div>
                                <div class="text-muted small">สถานะชุมนุม</div>
                                <span class="badge badge-status bg-<?= $club->club_status === 'open' ? 'success' : 'danger' ?> bg-opacity-10 text-<?= $club->club_status === 'open' ? 'success' : 'danger' ?>">
                                    <?= $club->club_status === 'open' ? 'เปิดรับสมัคร' : 'ปิดรับสมัคร' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-warning bg-opacity-10 text-warning me-3">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <div class="text-muted small">วันที่ก่อตั้ง</div>
                                <h4 class="fw-bold mb-0 small"><?= esc(thai_date($club->club_established_date, 'short')) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Menu -->
        <div class="card table-card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 text-start">
                    <h5 class="fw-bold mb-0 text-primary">เมนูจัดการกิจกรรม</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= site_url($routePrefix . '/schedule/' . $club->club_id) ?>" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm d-flex align-items-center">
                            <i class="bi bi-calendar-check me-2"></i> 
                            <span>เช็คชื่อนักเรียน</span>
                        </a>
                        <a href="<?= site_url($routePrefix . '/objectives/' . $club->club_id) ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2 d-flex align-items-center">
                            <i class="bi bi-list-check me-2"></i> ประเมินผล
                        </a>
                        <a href="<?= site_url($routePrefix . '/activities/' . $club->club_id) ?>" class="btn btn-outline-info rounded-pill px-4 py-2 d-flex align-items-center">
                            <i class="bi bi-bar-chart-line me-2"></i> รายงานผล
                        </a>
                        <button type="button" class="btn btn-outline-success rounded-pill px-4 py-2 d-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                            <i class="bi bi-images me-2"></i> 
                            <span>รูปกิจกรรม (<?= !empty($photos) ? count($photos) : 0 ?>)</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-start text-nowrap">
                            <tr>
                                <th class="ps-4" style="width: 80px;">ลำดับ</th>
                                <th>รหัสนักเรียน</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>ชั้น / เลขที่</th>
                                <th>บทบาท</th>
                                <th class="text-center" style="width: 250px;">จัดการสมาชิก</th>
                            </tr>
                        </thead>
                        <tbody class="text-start">
                            <?php if (!empty($members)): ?>
                                <?php $i = 1; foreach ($members as $member): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted"><?= $i++ ?></td>
                                        <td><span class="badge bg-light text-dark font-monospace"><?= esc($member->StudentCode ?? $member->StudentID) ?></span></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-3"><?= esc($member->StudentClass) ?></span>
                                            <span class="ms-1 text-muted small">เลขที่ <?= esc($member->StudentNumber) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($member->member_role === 'Leader'): ?>
                                                <span class="badge bg-warning text-dark rounded-pill px-3"><i class="bi bi-star-fill me-1"></i> หัวหน้า</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted rounded-pill px-3">สมาชิก</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3" 
                                                        data-bs-toggle="modal" data-bs-target="#assignRoleModal"
                                                        data-studentid="<?= esc($member->StudentID) ?>" 
                                                        data-currentrole="<?= esc($member->member_role) ?>">
                                                    <i class="bi bi-shield-lock me-1"></i> บทบาท
                                                </button>
                                                <form action="<?= site_url($routePrefix . '/removeMember/' . $club->club_id . '/' . $member->StudentID) ?>" method="post" class="remove-member-form">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                        <i class="bi bi-person-x me-1"></i> ลบ
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-3"><i class="bi bi-person-dash" style="font-size: 3rem;"></i></div>
                                        <p class="mb-0">ยังไม่มีนักเรียนสมัครเข้าเป็นสมาชิกใน<?= $isScout ? 'กองลูกเสือนี้' : 'ชุมนุมนี้' ?></p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Activity Photos Section -->
        <div class="card table-card shadow-sm mb-4 page-dropzone-target" id="club-photos-section">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3 text-start">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="bi bi-images"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-success">รูปภาพกิจกรรมสรุปผล (<?= !empty($photos) ? count($photos) : 0 ?> รูป)</h5>
                        <p class="text-muted small mb-0">ภาพถ่ายกิจกรรมสรุปผล<?= $isScout ? 'ลูกเสือ' : 'ชุมนุม' ?> สำหรับจัดเก็บและพิมพ์ออกภาคผนวกในรายงาน ปพ. (5 - 10 รูป)</p>
                    </div>
                </div>
                <button type="button" class="btn btn-success rounded-pill px-4 py-2 shadow-sm d-flex align-items-center fw-bold" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                    <i class="bi bi-cloud-arrow-up-fill me-2"></i> เพิ่มรูปกิจกรรม (5 - 10 รูป)
                </button>
            </div>
            <div class="card-body p-4 text-start">
                <?php if (!empty($photos)): ?>
                    <div class="row g-3">
                        <?php foreach ($photos as $photo): ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card h-100 border shadow-none overflow-hidden photo-card">
                                    <div class="position-relative overflow-hidden bg-light" style="padding-top: 75%;">
                                        <img src="<?= base_url('public/uploads/clubs/' . esc($photo->photo_file)) ?>" 
                                             class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover cursor-pointer photo-thumbnail" 
                                             alt="<?= esc($photo->photo_title) ?>"
                                             onclick="openPhotoPreview('<?= base_url('public/uploads/clubs/' . esc($photo->photo_file)) ?>', '<?= esc(addslashes($photo->photo_title)) ?>', '<?= esc(addslashes($photo->photo_description ?? '')) ?>')">

                                        <!-- Hover Action Overlay -->
                                        <div class="photo-overlay d-flex align-items-center justify-content-center gap-2">
                                            <button type="button" class="btn btn-light btn-sm rounded-circle shadow p-2" 
                                                    onclick="openPhotoPreview('<?= base_url('public/uploads/clubs/' . esc($photo->photo_file)) ?>', '<?= esc(addslashes($photo->photo_title)) ?>', '<?= esc(addslashes($photo->photo_description ?? '')) ?>')"
                                                    title="ดูภาพขนาดใหญ่">
                                                <i class="bi bi-arrows-fullscreen text-primary"></i>
                                            </button>
                                            <button type="button" class="btn btn-light btn-sm rounded-circle shadow p-2"
                                                    onclick="openEditPhotoModal(<?= esc($photo->photo_id) ?>, '<?= esc(addslashes($photo->photo_title)) ?>', '<?= esc(addslashes($photo->photo_description ?? '')) ?>')"
                                                    title="แก้ไขคำบรรยาย">
                                                <i class="bi bi-pencil-fill text-warning"></i>
                                            </button>
                                            <button type="button" class="btn btn-light btn-sm rounded-circle shadow p-2"
                                                    onclick="confirmDeletePhoto(<?= esc($photo->photo_id) ?>, '<?= esc(addslashes($photo->photo_title)) ?>')"
                                                    title="ลบรูป">
                                                <i class="bi bi-trash-fill text-danger"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-1 text-truncate" title="<?= esc($photo->photo_title) ?>">
                                            <?= esc($photo->photo_title) ?>
                                        </h6>
                                        <?php if (!empty($photo->photo_description)): ?>
                                            <p class="text-muted small mb-2 text-truncate" title="<?= esc($photo->photo_description) ?>">
                                                <?= esc($photo->photo_description) ?>
                                            </p>
                                        <?php else: ?>
                                            <p class="text-muted small mb-2 fst-italic smallest">ไม่มีคำอธิบายเพิ่มเติม</p>
                                        <?php endif; ?>
                                        <div class="d-flex justify-content-between align-items-center text-muted smallest border-top pt-2 mt-auto">
                                            <span><i class="bi bi-calendar3 me-1"></i><?= thai_date($photo->created_at, 'short') ?></span>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-danger" 
                                                    onclick="confirmDeletePhoto(<?= esc($photo->photo_id) ?>, '<?= esc(addslashes($photo->photo_title)) ?>')">
                                                ลบ
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex p-3 mb-3">
                            <i class="bi bi-images" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">ยังไม่มีรูปภาพกิจกรรมสรุปผล</h6>
                        <p class="text-muted small mb-3">เลือกรูปภาพสรุปผลการจัดกิจกรรมประมาณ 5 - 10 รูป เพื่อแนบในเล่มรายงาน ปพ.</p>
                        <button type="button" class="btn btn-outline-success rounded-pill px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                            <i class="bi bi-cloud-arrow-up-fill me-2"></i> อัปโหลดรูปภาพ (5 - 10 รูป)
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- Modals -->
<!-- Edit Club Modal -->
<div class="modal fade" id="editClubModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= site_url($routePrefix . '/update/' . $club->club_id) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold text-white"><i class="bi bi-pencil-square me-2"></i>แก้ไขข้อมูล<?= $isScout ? 'กองลูกเสือ' : 'ชุมนุม' ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="club_name" name="club_name" value="<?= esc($club->club_name) ?>" required>
                        <label for="club_name">ชื่อ<?= $isScout ? 'กองลูกเสือ' : 'ชุมนุม' ?></label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="club_description" name="club_description" style="height: 100px"><?= esc($club->club_description) ?></textarea>
                        <label for="club_description">คำอธิบาย</label>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="form-floating text-start">
                                <input type="number" class="form-control" id="club_max_participants" name="club_max_participants" 
                                       value="<?= esc($club->club_max_participants) ?>" min="<?= !empty($members) ? count($members) : 0 ?>" required>
                                <label for="club_max_participants">จำนวนรับ (คน)</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating text-start">
                                <select class="form-select" id="club_status" name="club_status">
                                    <option value="open" <?= $club->club_status === 'open' ? 'selected' : '' ?>>เปิดรับสมัคร</option>
                                    <option value="closed" <?= $club->club_status === 'closed' ? 'selected' : '' ?>>ปิดรับสมัคร</option>
                                </select>
                                <label for="club_status">สถานะ</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating">
                        <select class="form-select" id="club_level" name="club_level" required>
                            <option value="ม.ต้น" <?= ($club->club_level === 'ม.ต้น') ? 'selected' : '' ?>>ม.ต้น</option>
                            <option value="ม.ปลาย" <?= ($club->club_level === 'ม.ปลาย') ? 'selected' : '' ?>>ม.ปลาย</option>
                            <option value="ม.ต้น และ ม.ปลาย" <?= ($club->club_level === 'ม.ต้น และ ม.ปลาย') ? 'selected' : '' ?>>ม.ต้น และ ม.ปลาย</option>
                        </select>
                        <label for="club_level">ระดับชั้น</label>
                    </div>
                    <div class="mt-2 small text-warning"><i class="bi bi-info-circle me-1"></i> จำนวนรับต้องไม่น้อยกว่าสมาชิกปัจจุบัน (<?= !empty($members) ? count($members) : 0 ?> คน)</div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Role Modal -->
<div class="modal fade" id="assignRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= site_url($routePrefix . '/updateMemberRole/' . $club->club_id) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-badge me-2"></i>บทบาทสมาชิก</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <input type="hidden" name="student_id" id="modal_student_id">
                    <label class="form-label small fw-bold text-muted">เลือกบทบาทในทีม</label>
                    <select class="form-select py-2" id="member_role" name="member_role">
                        <option value="Member">สมาชิกทั่วไป</option>
                        <option value="Leader"><?= $isScout ? 'นายหมู่ / หัวหน้า' : 'หัวหน้าชุมนุม' ?></option>
                    </select>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold">บันทึกบทบาท</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Help Modal -->
<div class="modal fade" id="clubHelpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-question-circle me-2"></i>ศูนย์ช่วยเหลือ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <?php include('help_modal_content.php'); ?>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">เข้าใจแล้ว</button>
            </div>
        </div>
    </div>
</div>

<!-- Upload Photo Modal (Drag & Drop) -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= site_url($routePrefix . '/uploadPhoto/' . $club->club_id) ?>" method="post" enctype="multipart/form-data" id="photoUploadForm">
                <?= csrf_field() ?>
                <div class="modal-header bg-success text-white border-0 py-3">
                    <h5 class="modal-title fw-bold text-white"><i class="bi bi-cloud-arrow-up me-2"></i>อัปโหลดรูปภาพกิจกรรมสรุปผล (ลาก & วาง)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <!-- Drag & Drop Zone -->
                    <div class="dropzone-upload-box mb-3" id="photoDropzoneBox">
                        <input type="file" id="photo_files_input" name="photos[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple style="display: none;">
                        <i class="bi bi-cloud-arrow-up-fill dropzone-icon d-block"></i>
                        <h6 class="fw-bold text-dark mb-1">ลากไฟล์รูปภาพมาวางที่นี่ หรือ <span class="text-success text-decoration-underline">คลิกเพื่อเลือกไฟล์</span></h6>
                        <p class="text-muted small mb-0">รองรับไฟล์ JPG, PNG, WEBP (ลากวางทีเดียว 5 - 10 รูปได้เลยครับ)</p>
                    </div>

                    <!-- Selected Files Preview Container -->
                    <div id="dropzonePreviewContainer" class="mb-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small text-dark">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>รูปภาพที่เลือก: <span id="selectedCountText" class="badge bg-success rounded-pill">0 รูป</span>
                            </span>
                            <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0" id="btnClearAllPhotos">
                                <i class="bi bi-trash me-1"></i>ล้างทั้งหมด
                            </button>
                        </div>
                        <div class="dropzone-preview-grid" id="dropzonePreviewGrid"></div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="upload_photo_title" name="photo_title" placeholder="ชื่อกิจกรรม" value="ภาพกิจกรรมสรุปผล">
                        <label for="upload_photo_title">ชื่อหรือหัวข้อรูปภาพ</label>
                    </div>

                    <div class="form-floating">
                        <textarea class="form-control" id="upload_photo_description" name="photo_description" style="height: 80px" placeholder="คำอธิบายเพิ่มเติม"></textarea>
                        <label for="upload_photo_description">คำอธิบายภาพกิจกรรม (ถ้ามี)</label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 shadow-sm fw-bold" id="btnSubmitPhotos">
                        <i class="bi bi-cloud-check-fill me-1"></i> อัปโหลดรูปภาพ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Photo Modal -->
<div class="modal fade" id="editPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form id="editPhotoForm" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-warning text-dark border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>แก้ไขคำบรรยายรูปภาพ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="edit_photo_title" name="photo_title" required>
                        <label for="edit_photo_title">ชื่อหรือหัวข้อรูปภาพ <span class="text-danger">*</span></label>
                    </div>

                    <div class="form-floating">
                        <textarea class="form-control" id="edit_photo_description" name="photo_description" style="height: 90px"></textarea>
                        <label for="edit_photo_description">คำอธิบายภาพกิจกรรม</label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 shadow-sm fw-bold">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Photo Modal -->
<div class="modal fade" id="previewPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-dark">
            <div class="modal-header border-0 pb-0 text-white">
                <h5 class="modal-title fw-bold text-white mb-0" id="previewPhotoTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 text-center">
                <img src="" id="previewPhotoImage" class="img-fluid rounded-3" style="max-height: 70vh; object-fit: contain;" alt="Activity Preview">
                <p class="text-white text-opacity-75 mt-3 mb-0 small text-start px-2" id="previewPhotoDesc"></p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a href="" id="previewPhotoDownload" download class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-download me-1"></i> ดาวน์โหลดรูป
                </a>
                <button type="button" class="btn btn-light btn-sm rounded-pill px-4" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#clubHelpModal').on('show.bs.modal', function () {
            setTimeout(function() {
                var tabEl = document.querySelector('#help-pills-manage-tab');
                if (tabEl && typeof bootstrap !== 'undefined') {
                    var tab = new bootstrap.Tab(tabEl);
                    tab.show();
                }
            }, 300);
        });

        $('#assignRoleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var studentId = button.data('studentid');
            var currentRole = button.data('currentrole');
            var modal = $(this);
            modal.find('#modal_student_id').val(studentId);
            modal.find('#member_role').val(currentRole);
        });

        $('.remove-member-form').submit(function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'ยืนยันการลบสมาชิก?',
                text: "นักเรียนจะถูกคัดออกจากชุมนุมทันที และต้องสมัครใหม่หากต้องการกลับเข้าชุมนุม",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ยืนยัน ลบออกจากทีม',
                cancelButtonText: 'ยกเลิก',
                customClass: {
                    confirmButton: 'btn btn-danger rounded-pill px-4',
                    cancelButton: 'btn btn-light rounded-pill px-4 ms-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Activity Photo Functions
    function openPhotoPreview(src, title, desc) {
        $('#previewPhotoImage').attr('src', src);
        $('#previewPhotoTitle').text(title || 'รูปกิจกรรม');
        $('#previewPhotoDesc').text(desc || '');
        $('#previewPhotoDownload').attr('href', src);
        var previewModal = new bootstrap.Modal(document.getElementById('previewPhotoModal'));
        previewModal.show();
    }

    function openEditPhotoModal(photoId, title, desc) {
        var actionUrl = "<?= site_url($routePrefix . '/updatePhoto/' . $club->club_id) ?>/" + photoId;
        $('#editPhotoForm').attr('action', actionUrl);
        $('#edit_photo_title').val(title);
        $('#edit_photo_description').val(desc);
        var editModal = new bootstrap.Modal(document.getElementById('editPhotoModal'));
        editModal.show();
    }

    function confirmDeletePhoto(photoId, photoTitle) {
        Swal.fire({
            title: 'ยืนยันการลบรูปภาพ?',
            text: 'คุณต้องการลบ "' + photoTitle + '" ออกจากกิจกรรมใช่หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ยืนยัน ลบรูปภาพ',
            cancelButtonText: 'ยกเลิก',
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill px-4',
                cancelButton: 'btn btn-light rounded-pill px-4 ms-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = "<?= site_url($routePrefix . '/deletePhoto/' . $club->club_id) ?>/" + photoId;
                
                var csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = "<?= csrf_token() ?>";
                csrf.value = "<?= csrf_hash() ?>";
                form.appendChild(csrf);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // --- Drag & Drop Implementation for Summary Photos ---
    var selectedFilesDT = new DataTransfer();

    function renderDropzonePreviews() {
        var grid = $('#dropzonePreviewGrid');
        grid.empty();
        var files = selectedFilesDT.files;
        
        if (files.length === 0) {
            $('#dropzonePreviewContainer').slideUp(200);
            $('#selectedCountText').text('0 รูป');
            return;
        }

        $('#dropzonePreviewContainer').slideDown(200);
        $('#selectedCountText').text(files.length + ' รูป' + (files.length >= 5 && files.length <= 10 ? ' (ยอดเยี่ยม 5-10 รูป)' : ''));

        Array.from(files).forEach(function(file, index) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var item = $(`
                    <div class="dropzone-preview-item" title="${file.name} (${(file.size / 1024).toFixed(1)} KB)">
                        <img src="${e.target.result}" alt="${file.name}">
                        <button type="button" class="dropzone-preview-remove" data-index="${index}" title="นำรูปนี้ออก">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `);
                grid.append(item);
            };
            reader.readAsDataURL(file);
        });

        var fileInputEl = document.getElementById('photo_files_input');
        if (fileInputEl) {
            fileInputEl.files = selectedFilesDT.files;
        }
    }

    function addFilesToDT(newFiles) {
        var validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        Array.from(newFiles).forEach(function(file) {
            if (validTypes.includes(file.type) || /\.(jpe?g|png|webp|gif)$/i.test(file.name)) {
                selectedFilesDT.items.add(file);
            }
        });
        renderDropzonePreviews();
    }

    // Remove single preview item
    $(document).on('click', '.dropzone-preview-remove', function(e) {
        e.stopPropagation();
        var indexToRemove = $(this).data('index');
        var newDT = new DataTransfer();
        Array.from(selectedFilesDT.files).forEach(function(file, idx) {
            if (idx !== indexToRemove) {
                newDT.items.add(file);
            }
        });
        selectedFilesDT = newDT;
        renderDropzonePreviews();
    });

    // Clear all selected
    $('#btnClearAllPhotos').on('click', function(e) {
        e.preventDefault();
        selectedFilesDT = new DataTransfer();
        renderDropzonePreviews();
    });

    // Setup Drag & Drop on Modal Dropzone
    var dropzoneBox = document.getElementById('photoDropzoneBox');
    var fileInput = document.getElementById('photo_files_input');

    if (dropzoneBox && fileInput) {
        dropzoneBox.addEventListener('click', function() {
            fileInput.click();
        });

        ['dragenter', 'dragover'].forEach(function(name) {
            dropzoneBox.addEventListener(name, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzoneBox.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(function(name) {
            dropzoneBox.addEventListener(name, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzoneBox.classList.remove('dragover');
            });
        });

        dropzoneBox.addEventListener('drop', function(e) {
            if (e.dataTransfer && e.dataTransfer.files.length > 0) {
                addFilesToDT(e.dataTransfer.files);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                addFilesToDT(this.files);
            }
        });
    }

    // Setup Drag & Drop on Page Card (#club-photos-section)
    var pagePhotosSection = document.getElementById('club-photos-section');
    if (pagePhotosSection) {
        ['dragenter', 'dragover'].forEach(function(name) {
            pagePhotosSection.addEventListener(name, function(e) {
                e.preventDefault();
                e.stopPropagation();
                pagePhotosSection.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(function(name) {
            pagePhotosSection.addEventListener(name, function(e) {
                e.preventDefault();
                e.stopPropagation();
                pagePhotosSection.classList.remove('dragover');
            });
        });

        pagePhotosSection.addEventListener('drop', function(e) {
            if (e.dataTransfer && e.dataTransfer.files.length > 0) {
                addFilesToDT(e.dataTransfer.files);
                var uploadModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('uploadPhotoModal'));
                uploadModal.show();
            }
        });
    }

    // Submit Validation & Progress Loading
    $('#photoUploadForm').on('submit', function(e) {
        if (selectedFilesDT.files.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'ยังไม่ได้เลือกหรือลากไฟล์รูปภาพ',
                text: 'กรุณาลากหรือเลือกรูปภาพอย่างน้อย 1 รูป (แนะนำ 5 - 10 รูป) ก่อนกดอัปโหลด',
                confirmButtonColor: '#10b981'
            });
            return false;
        }

        Swal.fire({
            title: 'กำลังอัปโหลดรูปภาพ...',
            html: 'กำลังบันทึกรูปภาพกิจกรรมสรุปผลจำนวน <b>' + selectedFilesDT.files.length + '</b> รูป กรุณารอสักครู่',
            allowOutsideClick: false,
            didOpen: function() {
                Swal.showLoading();
            }
        });
    });


</script>
<?= $this->endSection() ?>
