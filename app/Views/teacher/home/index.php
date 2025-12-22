<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
หน้าแรก
<?= $this->endSection() ?>

<?= $this->section('content') ?>



	<!--begin::Container-->
	<div class="">

    <style>
        .dashboard-container {
            padding-top: 1rem;
        }
        /* Welcome Card Refinement */
        .welcome-hero {
            background-color: #fff;
            border: none;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            overflow: hidden;
            position: relative;
            background: linear-gradient(to right, #ffffff, #f8f9ff);
        }
        .welcome-hero .hero-content {
            padding: 2.5rem;
            z-index: 2;
            position: relative;
        }
        .hero-bg-decoration {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 40%;
            background: linear-gradient(135deg, rgba(105, 108, 255, 0.05) 0%, rgba(105, 108, 255, 0.2) 100%);
            clip-path: polygon(20% 0%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 1;
        }
        .teacher-avatar-luxe {
            width: 120px;
            height: 120px;
            border-radius: 2rem;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 15px 35px rgba(105, 108, 255, 0.2);
        }

        /* Stat Cards Styling */
        .stat-badge-card {
            background: #fff;
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 1.25rem;
            padding: 1.25rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
        }
        .stat-badge-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(105, 108, 255, 0.08);
            border-color: rgba(105, 108, 255, 0.2);
        }
        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Action Menu Styling */
        .menu-title-row {
            margin-top: 2rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .menu-title-row h5 {
            margin-bottom: 0;
            font-weight: 800;
            color: #32325d;
            letter-spacing: -0.02em;
        }
        .menu-line {
            height: 2px;
            flex-grow: 1;
            background: linear-gradient(to right, rgba(105, 108, 255, 0.2), transparent);
        }

        .luxe-card {
            background: #fff;
            border: 1px solid rgba(0,0,0,0.04);
            border-radius: 1.25rem;
            padding: 1.5rem;
            text-decoration: none;
            display: block;
            transition: all 0.2s ease-out;
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .luxe-card:hover {
            background: #696cff;
            transform: scale(1.03);
            box-shadow: 0 20px 40px rgba(105, 108, 255, 0.2);
            border-color: #696cff;
        }
        .luxe-card .card-icon {
            font-size: 2rem;
            color: #696cff;
            margin-bottom: 1rem;
            transition: all 0.2s;
        }
        .luxe-card .card-text h6 {
            font-weight: 700;
            color: #32325d;
            margin-bottom: 0.25rem;
            transition: all 0.2s;
        }
        .luxe-card .card-text p {
            font-size: 0.85rem;
            color: #677788;
            margin-bottom: 0;
            transition: all 0.2s;
        }
        .luxe-card:hover .card-icon, 
        .luxe-card:hover .card-text h6, 
        .luxe-card:hover .card-text p {
            color: #fff !important;
        }

        /* Special Badge for HoD */
        .hod-badge {
            background: linear-gradient(135deg, #ffab00 0%, #ff3e1d 100%);
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
    </style>

    <div class="container-xxl dashboard-container">
        <!-- Hero Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="welcome-hero">
                    <div class="hero-bg-decoration"></div>
                    <div class="hero-content">
                        <div class="row align-items-center">
                            <div class="col-md-auto d-flex justify-content-center mb-4 mb-md-0">
                                <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= esc($teacher[0]->pers_img ?? '') ?>" 
                                     class="teacher-avatar-luxe"
                                     onerror="this.onerror=null;this.src='https://placehold.co/200x200/696cff/ffffff?text=SKJ';">
                            </div>
                            <div class="col text-center text-md-start">
                                <?php if (session()->get('pers_groupleade') !== null && session()->get('pers_groupleade') !== ''): ?>
                                    <div class="hod-badge">
                                        <i class="bi bi-shield-shaded"></i> หัวหน้ากลุ่มสาระการเรียนรู้
                                    </div>
                                <?php endif; ?>
                                <h1 class="display-6 fw-black text-dark mb-2">ยินดีต้อนรับ, <span class="text-primary"><?= session()->get('fullname') ?></span></h1>
                                <p class="lead text-muted mb-4">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</p>
                                
                                <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-4 mt-2">
                                    <div class="text-center text-md-start">
                                        <div class="text-uppercase small fw-bold text-muted mb-1">กลุ่มสาระ</div>
                                        <div class="fw-bold text-dark fs-5"><?= esc($learningGroupName) ?></div>
                                    </div>
                                    <div class="vr mx-2 d-none d-md-block"></div>
                                    <div class="text-center text-md-start">
                                        <div class="text-uppercase small fw-bold text-muted mb-1">ปีการศึกษา</div>
                                        <div class="fw-bold text-dark fs-5"><?= esc($latestEntry) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Indicators -->
        <div class="row g-4 mb-2">
            <div class="col-lg-3 col-6">
                <div class="stat-badge-card">
                    <div class="icon-box bg-label-primary text-primary">
                        <i class="bi bi-journal-check"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0"><?= esc($subjectCount) ?></h3>
                        <div class="text-muted small">รายวิชาที่รับผิดชอบ (<?= esc($latestEntry) ?>)</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stat-badge-card">
                    <div class="icon-box bg-label-success text-success">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0"><?= esc($studentCount ?: '-') ?></h3>
                        <div class="text-muted small">นร.ประจำชั้น (<?= $homeroomClass ? esc($homeroomClass->Reg_Class) : 'ไม่มี' ?>)</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stat-badge-card">
                    <div class="icon-box bg-label-info text-info">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0"><?= date('H:i') ?> น.</h3>
                        <div class="text-muted small">เวลาปัจจุบัน</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stat-badge-card">
                    <div class="icon-box bg-label-warning text-warning">
                        <i class="bi bi-calendar3"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0"><?= date('d/m') ?></h3>
                        <div class="text-muted small"><?= date('Y') + 543 ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation Areas -->
        <div class="row">
            <div class="col-lg-12">
                <!-- Academic Section -->
                <div class="menu-title-row">
                    <h5>งานวัดผลและวิชาการ</h5>
                    <div class="menu-line"></div>
                </div>
                <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                        <a href="<?= base_url('assessment/save-score-normal') ?>" class="luxe-card">
                            <i class="bi bi-clipboard-data card-icon"></i>
                            <div class="card-text">
                                <h6>บันทึกผลการเรียน</h6>
                                <p>จัดการคะแนนรายวิชาปกติ</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="<?= base_url('assessment/save-score-repeat') ?>" class="luxe-card">
                            <i class="bi bi-arrow-repeat card-icon"></i>
                            <div class="card-text">
                                <h6>บันทึกเรียนซ้ำ</h6>
                                <p>บันทึกคะแนนสอบแก้ตัว/เรียนซ้ำ</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="<?= base_url('teacher/reading_assessment') ?>" class="luxe-card">
                            <i class="bi bi-book card-icon"></i>
                            <div class="card-text">
                                <h6>อ่าน คิดวิเคราะห์</h6>
                                <p>ประเมินทักษะการอ่านเขียน</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="<?= base_url('teacher/desirable_assessment') ?>" class="luxe-card">
                            <i class="bi bi-award card-icon"></i>
                            <div class="card-text">
                                <h6>คุณลักษณะฯ</h6>
                                <p>ประเมินพฤติกรรม 8 ประการ</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Curriculum & Planning -->
                <div class="menu-title-row">
                    <h5>งานหลักสูตรและพัฒนาครู</h5>
                    <div class="menu-line"></div>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <a href="<?= base_url('curriculum/SendPlan') ?>" class="luxe-card">
                            <i class="bi bi-file-earmark-arrow-up card-icon"></i>
                            <div class="card-text">
                                <h6>ส่งแผนการสอน</h6>
                                <p>อัปโหลดแผนการจัดการเรียนรู้</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <a href="<?= base_url('research') ?>" class="luxe-card">
                            <i class="bi bi-layers card-icon"></i>
                            <div class="card-text">
                                <h6>งานวิจัยในชั้นเรียน</h6>
                                <p>ส่งเล่มวิจัยเพื่อพัฒนาการเรียนสอน</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <a href="<?= base_url('curriculum/download-plan') ?>" class="luxe-card">
                            <i class="bi bi-cloud-download card-icon"></i>
                            <div class="card-text">
                                <h6>ดาวน์โหลดแผน</h6>
                                <p>เรียกดูไฟล์แผนย้อนหลัง</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Student Development -->
                <div class="menu-title-row">
                    <h5>กิจกรรมและพัฒนาผู้เรียน</h5>
                    <div class="menu-line"></div>
                </div>
                <div class="row g-4 mb-5">
                    <div class="col-lg-4 col-md-6">
                        <a href="<?= base_url('club') ?>" class="luxe-card">
                            <i class="bi bi-people card-icon"></i>
                            <div class="card-text">
                                <h6>กิจกรรมชุมนุม</h6>
                                <p>บันทึกเวลาเรียนและกิจกรรมชมรม</p>
                            </div>
                        </a>
                    </div>
                    <!-- <div class="col-lg-4 col-md-6">
                        <div class="luxe-card opacity-50 pe-none">
                            <i class="bi bi-house-door card-icon"></i>
                            <div class="card-text">
                                <h6>เยี่ยมบ้านนักเรียน</h6>
                                <p>(ระบบกำลังปรับปรุง)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="luxe-card opacity-50 pe-none">
                            <i class="bi bi-megaphone card-icon"></i>
                            <div class="card-text">
                                <h6>ประกาศข่าว</h6>
                                <p>ติดตามข่าวสารจากทางโรงเรียน</p>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
    </div>

		<!-- ...existing code... -->

	</div>
	<!--end::Container-->


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<?= $this->endSection() ?>