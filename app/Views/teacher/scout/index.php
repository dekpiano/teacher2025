<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'กิจกรรมลูกเสือ - เนตรนารี') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .scout-card {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: none;
        border-radius: 1.5rem;
        overflow: hidden;
        background: #ffffff;
        position: relative;
    }
    .scout-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        background: linear-gradient(90deg, #6366f1 0%, #a855f7 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .scout-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 30px 60px -12px rgba(99, 102, 241, 0.15), 0 18px 36px -18px rgba(0, 0, 0, 0.12);
    }
    .scout-card:hover::before {
        opacity: 1;
    }
    .scout-icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.12) 0%, rgba(168, 85, 247, 0.12) 100%);
        color: #6366f1;
    }
    .progress-scout {
        height: 10px;
        border-radius: 20px;
        background-color: #f1f5f9;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    }
    .progress-scout .progress-bar {
        background: linear-gradient(90deg, #6366f1 0%, #a855f7 100%);
    }
    .btn-manage-scout {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        color: white;
        border: none;
        padding: 0.85rem;
        border-radius: 1.25rem;
        font-weight: 700;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }
    .btn-manage-scout:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        color: white;
    }
    .scout-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border-radius: 1.75rem;
        padding: 2.75rem 2.5rem;
        margin-bottom: 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px -15px rgba(79, 70, 229, 0.35);
    }
    .scout-header::after {
        content: "";
        position: absolute;
        top: -30%;
        right: -5%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .scout-advisor-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.8rem;
    }
    .scout-advisor-chip img {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        object-fit: cover;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .smallest {
        font-size: 0.72rem;
        letter-spacing: 0.025em;
    }
</style>

<div class="container-fluid py-2">
    <!-- Header Section -->
    <div class="scout-header shadow-lg">
        <div class="row align-items-center text-start">
            <div class="col-lg-7">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-light mb-2">
                        <li class="breadcrumb-item"><a href="<?= site_url('home') ?>" class="text-white opacity-75">หน้าหลัก</a></li>
                        <li class="breadcrumb-item text-white opacity-75">งานพัฒนาผู้เรียน</li>
                        <li class="breadcrumb-item active text-white" aria-current="page">บันทึกลูกเสือ</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-bold text-white mb-2">
                    <i class="bi bi-compass me-2"></i>กิจกรรมลูกเสือ - เนตรนารี
                </h1>
                <?php if (isset($currentAcademicYear) && isset($currentTerm)): ?>
                    <p class="lead mb-0 text-white text-opacity-85">
                        ประจำภาคเรียนที่ <?= esc($currentTerm) ?> ปีการศึกษา <?= esc($currentAcademicYear) ?> (พ.ศ.)
                    </p>
                <?php endif; ?>
            </div>
            <div class="col-lg-5 mt-4 mt-lg-0">
                <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                    <div class="btn btn-white text-primary rounded-pill px-4 py-2 shadow-sm fw-bold border-0 bg-white">
                        <i class="bi bi-shield-check me-2"></i> กองลูกเสือที่กำกับดูแล
                    </div>
                    <a href="<?= site_url('scout/manual') ?>" class="btn btn-outline-light rounded-pill px-3 py-2 fw-semibold">
                        <i class="bi bi-book-half me-1"></i> คู่มือ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <?php if (!empty($clubs) && is_array($clubs)): ?>
        <div class="row g-4 text-start">
            <?php foreach ($clubs as $club): ?>
                <?php
                    $percent = ($club->club_max_participants > 0) ? ($club->member_count / $club->club_max_participants) * 100 : 0;
                    $percent = min($percent, 100);
                ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card scout-card shadow-sm h-100 border-0">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="scout-icon-wrapper bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-compass"></i>
                                </div>
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 border border-success border-opacity-25">
                                    <i class="bi bi-patch-check-fill me-1"></i> กิจกรรมบังคับ
                                </span>
                            </div>

                            <h4 class="card-title fw-bold mb-1 text-dark"><?= esc($club->club_name) ?></h4>
                            <p class="text-muted small mb-4 line-clamp-2">
                                <?= !empty($club->club_description) ? esc($club->club_description) : 'กิจกรรมลูกเสือ-เนตรนารี ยุวกาชาด และผู้บำเพ็ญประโยชน์' ?>
                            </p>

                            <!-- สมาชิก & ความจุ -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">ความจุสมาชิก</span>
                                    <span class="fw-bold small"><?= esc($club->member_count) ?> / <?= esc($club->club_max_participants) ?> คน</span>
                                </div>
                                <div class="progress progress-scout">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $percent ?>%" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <!-- ข้อมูลระดับชั้น / ภาคเรียน -->
                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <div class="p-2 rounded bg-light border border-dashed text-center">
                                        <div class="text-muted smallest text-uppercase fw-semibold mb-1">ระดับชั้น</div>
                                        <div class="small fw-bold text-dark"><?= esc($club->club_level) ?></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded bg-light border border-dashed text-center">
                                        <div class="text-muted smallest text-uppercase fw-semibold mb-1">เทอม/ปี</div>
                                        <div class="small fw-bold text-dark"><?= esc($club->club_trem) ?>/<?= esc($club->club_year) ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- ผู้กำกับร่วม -->
                            <?php if (!empty($club->advisors_list)): ?>
                                <div class="mb-4">
                                    <div class="text-muted smallest text-uppercase fw-semibold mb-2">
                                        <i class="bi bi-person-badge me-1"></i>ผู้กำกับลูกเสือ (<?= count($club->advisors_list) ?> ท่าน)
                                    </div>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach ($club->advisors_list as $advisor): ?>
                                            <div class="scout-advisor-chip">
                                                <?php if (!empty($advisor->pers_img)): ?>
                                                    <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= esc($advisor->pers_img) ?>" alt="Advisor">
                                                <?php else: ?>
                                                    <i class="bi bi-person-circle text-primary"></i>
                                                <?php endif; ?>
                                                <span class="text-truncate" style="max-width: 140px;"><?= esc($advisor->pers_prefix . $advisor->pers_firstname . ' ' . $advisor->pers_lastname) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- ปุ่มจัดการ -->
                            <div class="mt-auto pt-3 border-top">
                                <a href="<?= site_url('scout/manage/' . $club->club_id) ?>" class="btn btn-manage-scout w-100">
                                    <i class="bi bi-gear-fill me-2"></i> เข้าสู่หน้าจัดการกองลูกเสือ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-light">
            <div class="mb-4 text-primary opacity-25">
                <i class="bi bi-compass" style="font-size: 5.5rem;"></i>
            </div>
            <h3 class="text-dark mb-2 fw-bold">ไม่พบข้อมูลกองลูกเสือที่กำกับดูแล</h3>
            <p class="text-muted mb-4 px-4 mx-auto" style="max-width: 520px;">
                ขณะนี้คุณยังไม่มีรายชื่อเป็นผู้กำกับกองลูกเสือ - เนตรนารี ในภาคเรียนปัจจุบัน
                การจัดสรรกองลูกเสือและผู้กำกับจะดำเนินการโดยหัวหน้างานกิจกรรมพัฒนาผู้เรียน ฝ่ายวิชาการ
            </p>
            <div class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill bg-light border text-muted small">
                <i class="bi bi-info-circle text-primary fs-6"></i>
                <span>หากได้รับมอบหมายงานสอนลูกเสือแล้ว กรุณาติดต่อหัวหน้างานกิจกรรมพัฒนาผู้เรียน</span>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
