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
        /* Welcome Card Refinement - Mobile-First Premium Redesign */
        .welcome-hero {
            background: linear-gradient(135deg, #696cff 0%, #3f42b5 100%);
            border: none;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(105, 108, 255, 0.2);
            overflow: hidden;
            position: relative;
            transition: all 0.3s ease;
        }
        .welcome-hero .hero-content {
            padding: 2.5rem;
            z-index: 2;
            position: relative;
            color: #ffffff;
        }
        @media (max-width: 576px) {
            .welcome-hero .hero-content {
                padding: 1.5rem 1.25rem;
            }
        }
        .hero-bg-decoration {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 40%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.12) 100%);
            clip-path: polygon(20% 0%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 1;
        }
        @media (max-width: 768px) {
            .hero-bg-decoration {
                width: 100%;
                clip-path: none;
                background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.15) 0%, transparent 60%);
            }
        }
        .avatar-container {
            position: relative;
            display: inline-block;
        }
        .teacher-avatar-luxe {
            width: 120px;
            height: 120px;
            border-radius: 2rem;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }
        @media (max-width: 768px) {
            .teacher-avatar-luxe {
                width: 85px;
                height: 85px;
                border-radius: 1.5rem;
                border-width: 3px;
            }
        }
        .welcome-hero .welcome-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        @media (max-width: 768px) {
            .welcome-hero .welcome-title {
                font-size: 1.6rem;
            }
        }
        .welcome-hero .welcome-subtitle {
            font-size: 1.05rem;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 400;
        }
        @media (max-width: 768px) {
            .welcome-hero .welcome-subtitle {
                font-size: 0.9rem;
                margin-bottom: 1.25rem !important;
            }
        }
        
        /* Floating Glass Capsules for metadata info */
        .info-capsule {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 1.25rem;
            padding: 0.6rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
        }
        .info-capsule:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        @media (max-width: 576px) {
            .info-capsule {
                padding: 0.5rem 0.9rem;
                width: 100%;
                border-radius: 1rem;
            }
        }
        .info-capsule-icon {
            width: 36px;
            height: 36px;
            background: #ffffff;
            color: #696cff;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            flex-shrink: 0;
        }
        @media (max-width: 576px) {
            .info-capsule-icon {
                width: 30px;
                height: 30px;
                border-radius: 0.5rem;
                font-size: 0.95rem;
            }
        }
        .info-capsule-content {
            display: flex;
            flex-direction: column;
            text-align: left;
        }
        .info-capsule-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255, 255, 255, 0.65);
            font-weight: 700;
            line-height: 1.2;
        }
        .info-capsule-value {
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.3;
        }
        @media (max-width: 576px) {
            .info-capsule-value {
                font-size: 0.85rem;
            }
        }

        /* HOD Badge Customization */
        .welcome-hero .hod-badge-luxe {
            background: linear-gradient(135deg, #ffab00 0%, #ff3e1d 100%);
            color: #ffffff;
            padding: 0.4rem 0.85rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 4px 12px rgba(255, 171, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: pulse-badge 2s infinite;
        }
        @keyframes pulse-badge {
            0% { box-shadow: 0 4px 12px rgba(255, 171, 0, 0.3); }
            50% { box-shadow: 0 4px 20px rgba(255, 171, 0, 0.6); }
            100% { box-shadow: 0 4px 12px rgba(255, 171, 0, 0.3); }
        }

        /* SKJ Check-In Banner Styles - Premium Redesign */
        .checkin-banner-card {
            border: none;
            border-radius: 1.5rem;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            z-index: 1;
        }
        .checkin-banner-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 1.5rem;
            padding: 1.5px;
            background: linear-gradient(135deg, rgba(255,255,255,0.45), rgba(255,255,255,0.15));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            z-index: 2;
        }
        .checkin-banner-card:hover {
            transform: translateY(-5px);
        }
        .checkin-banner-content {
            padding: 1.75rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.5rem;
            position: relative;
            z-index: 3;
        }
        @media (max-width: 768px) {
            .checkin-banner-content {
                padding: 1.5rem;
                flex-direction: column;
                text-align: center;
                gap: 1.25rem;
            }
        }
        .checkin-status-info {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        @media (max-width: 768px) {
            .checkin-status-info {
                flex-direction: column;
                gap: 0.75rem;
            }
        }
        .checkin-pulse-icon {
            width: 56px;
            height: 56px;
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            position: relative;
            flex-shrink: 0;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }
        .checkin-pulse-icon::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: inherit;
            animation: pulse-ring 2s infinite;
            opacity: 0.25;
            background-color: currentColor;
        }
        @keyframes pulse-ring {
            0% { transform: scale(1); opacity: 0.35; }
            100% { transform: scale(1.5); opacity: 0; }
        }
        .checkin-text-group {
            text-align: left;
        }
        @media (max-width: 768px) {
            .checkin-text-group {
                text-align: center;
            }
        }
        .checkin-action-btn {
            padding: 0.75rem 1.8rem;
            border-radius: 3rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            text-decoration: none;
            border: none;
            color: #ffffff !important;
            font-size: 0.95rem;
        }
        .checkin-action-btn i {
            font-size: 1.1rem;
        }
        .checkin-action-btn:hover {
            transform: translateY(-2px);
        }

        /* Premium State Colors & Gradients */
        .checkin-banner-card.checkin-none {
            background: linear-gradient(135deg, #f0f3ff 0%, #e5ebff 100%);
            box-shadow: 0 15px 35px rgba(105, 108, 255, 0.15), inset 0 -4px 10px rgba(105, 108, 255, 0.05);
            border-left: 6px solid #696cff !important;
        }
        .checkin-none .checkin-pulse-icon {
            color: #696cff !important;
            background: rgba(105, 108, 255, 0.1);
        }
        .checkin-none .checkin-action-btn {
            background: linear-gradient(135deg, #696cff 0%, #5f61e6 100%);
            box-shadow: 0 8px 25px rgba(105, 108, 255, 0.35);
        }
        .checkin-none .checkin-action-btn:hover {
            box-shadow: 0 12px 30px rgba(105, 108, 255, 0.5);
        }

        .checkin-banner-card.checkin-active {
            background: linear-gradient(135deg, #fffcf0 0%, #fff7d6 100%);
            box-shadow: 0 15px 35px rgba(255, 171, 0, 0.15), inset 0 -4px 10px rgba(255, 171, 0, 0.05);
            border-left: 6px solid #ffab00 !important;
        }
        .checkin-active .checkin-pulse-icon {
            color: #ffab00 !important;
            background: rgba(255, 171, 0, 0.1);
        }
        .checkin-active .checkin-action-btn {
            background: linear-gradient(135deg, #ffab00 0%, #e09600 100%);
            box-shadow: 0 8px 25px rgba(255, 171, 0, 0.35);
        }
        .checkin-active .checkin-action-btn:hover {
            box-shadow: 0 12px 30px rgba(255, 171, 0, 0.5);
        }

        .checkin-banner-card.checkin-done {
            background: linear-gradient(135deg, #f2faf1 0%, #e3f5e1 100%);
            box-shadow: 0 15px 35px rgba(113, 221, 55, 0.12), inset 0 -4px 10px rgba(113, 221, 55, 0.05);
            border-left: 6px solid #71dd37 !important;
        }
        .checkin-done .checkin-pulse-icon {
            color: #71dd37 !important;
            background: rgba(113, 221, 55, 0.1);
        }
        .checkin-done .checkin-action-btn {
            background: linear-gradient(135deg, #71dd37 0%, #60be2e 100%);
            box-shadow: 0 8px 25px rgba(113, 221, 55, 0.3);
        }
        .checkin-done .checkin-action-btn:hover {
            box-shadow: 0 12px 30px rgba(113, 221, 55, 0.45);
        }

        .live-pulse {
            width: 10px;
            height: 10px;
            background-color: currentColor;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.6rem;
            position: relative;
            vertical-align: middle;
            margin-top: -3px;
        }
        .live-pulse::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: inherit;
            animation: pulse-dot 1.8s infinite;
            left: 0;
            top: 0;
        }
        @keyframes pulse-dot {
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(3.5); opacity: 0; }
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

        .luxe-card-group {
            background: #fff;
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 1.25rem;
            padding: 1.5rem;
            transition: all 0.25s ease-out;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .luxe-card-group:hover {
            box-shadow: 0 15px 35px rgba(105, 108, 255, 0.12) !important;
            border-color: rgba(105, 108, 255, 0.3) !important;
            transform: translateY(-3px);
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
                            <div class="col-md-auto d-flex justify-content-center mb-3 mb-md-0">
                                <div class="avatar-container">
                                    <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= esc($teacher[0]->pers_img ?? '') ?>" 
                                         class="teacher-avatar-luxe"
                                         onerror="this.onerror=null;this.src='https://placehold.co/200x200/696cff/ffffff?text=SKJ';">
                                </div>
                            </div>
                            <div class="col text-center text-md-start">
                                <?php if (session()->get('pers_groupleade') !== null && session()->get('pers_groupleade') !== ''): ?>
                                    <div class="hod-badge-luxe">
                                        <i class="bi bi-shield-shaded"></i> หัวหน้ากลุ่มสาระการเรียนรู้
                                    </div>
                                <?php endif; ?>
                                <h1 class="welcome-title mb-2">
                                    ยินดีต้อนรับ,<br class="d-md-none"> <span class="fw-black text-warning"><?= session()->get('fullname') ?></span>
                                </h1>
                                <p class="welcome-subtitle mb-4">
                                    <i class="bi bi-geo-alt-fill me-1 text-danger"></i> โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                                </p>
                                
                                <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-md-start gap-3 mt-2">
                                    <div class="info-capsule">
                                        <div class="info-capsule-icon">
                                            <i class="bi bi-book-half"></i>
                                        </div>
                                        <div class="info-capsule-content">
                                            <span class="info-capsule-label">กลุ่มสาระการเรียนรู้</span>
                                            <span class="info-capsule-value"><?= esc($learningGroupName) ?></span>
                                        </div>
                                    </div>
                                    <div class="info-capsule">
                                        <div class="info-capsule-icon">
                                            <i class="bi bi-calendar-event-fill"></i>
                                        </div>
                                        <div class="info-capsule-content">
                                            <span class="info-capsule-label">ปีการศึกษา</span>
                                            <span class="info-capsule-value"><?= esc($latestEntry) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SKJ Check-In Quick Access Banner -->
        <?php if ($isSystemActive): ?>
        <div class="row mb-4">
            <div class="col-12">
                <?php if ($attStatus === 'none'): ?>
                    <div class="card checkin-banner-card checkin-none">
                        <div class="checkin-banner-content">
                            <div class="checkin-status-info">
                                <div class="checkin-pulse-icon">
                                    <i class="bi bi-fingerprint"></i>
                                </div>
                                <div class="checkin-text-group">
                                    <h5 class="fw-bold mb-1 text-primary">
                                        <span class="live-pulse text-primary"></span> ยังไม่ได้ลงเวลาเข้างานวันนี้
                                    </h5>
                                    <p class="text-muted small mb-0">
                                        ระบบออนไลน์พร้อมให้บริการแล้ว กรุณาลงเวลาเข้าปฏิบัติงานเพื่อบันทึกสถิติประจำวันของคุณ
                                    </p>
                                </div>
                            </div>
                            <a href="<?= base_url('attendance') ?>" class="checkin-action-btn">
                                <i class="bi bi-fingerprint"></i> ลงเวลาเข้างานทันที <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                <?php elseif ($attStatus === 'checked_in'): ?>
                    <div class="card checkin-banner-card checkin-active">
                        <div class="checkin-banner-content">
                            <div class="checkin-status-info">
                                <div class="checkin-pulse-icon">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <div class="checkin-text-group">
                                    <h5 class="fw-bold mb-1 text-warning">
                                        <span class="live-pulse text-warning"></span> บันทึกเวลาเข้างานเรียบร้อยแล้ว (ยังไม่ได้ลงเวลาออก)
                                    </h5>
                                    <p class="text-muted small mb-0">
                                        เข้าปฏิบัติงานเมื่อ: <strong class="text-dark"><?= date('H:i', strtotime($todayRecord['check_in'])) ?> น.</strong> 
                                        | สถานะ: <span class="badge bg-label-warning px-2 py-0.5"><?= esc($todayRecord['status']) ?></span>
                                    </p>
                                </div>
                            </div>
                            <a href="<?= base_url('attendance') ?>" class="checkin-action-btn">
                                <i class="bi bi-box-arrow-right"></i> ลงเวลาออกงานทันที <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                <?php elseif ($attStatus === 'completed'): ?>
                    <div class="card checkin-banner-card checkin-done">
                        <div class="checkin-banner-content">
                            <div class="checkin-status-info">
                                <div class="checkin-pulse-icon">
                                    <i class="bi bi-patch-check-fill"></i>
                                </div>
                                <div class="checkin-text-group">
                                    <h5 class="fw-bold mb-1 text-success">
                                        <span class="live-pulse text-success" style="animation: none;"></span> บันทึกเวลาปฏิบัติงานเรียบร้อยแล้ววันนี้
                                    </h5>
                                    <p class="text-muted small mb-0">
                                        เวลาเข้างาน: <strong class="text-dark"><?= date('H:i', strtotime($todayRecord['check_in'])) ?> น.</strong> | 
                                        เวลาออกงาน: <strong class="text-dark"><?= date('H:i', strtotime($todayRecord['check_out'])) ?> น.</strong>
                                    </p>
                                </div>
                            </div>
                            <a href="<?= base_url('attendance') ?>" class="checkin-action-btn">
                                <i class="bi bi-file-earmark-bar-graph"></i> ดูประวัติเวลางาน <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

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
                        <?php if (!empty($recentPages) && isset($recentPages[0])): ?>
                            <a href="<?= base_url($recentPages[0]['page_url']) ?>" class="text-decoration-none">
                                <h6 class="fw-bold mb-0 text-dark"><?= esc($recentPages[0]['page_name']) ?></h6>
                                <div class="text-muted small">เข้าล่าสุด</div>
                            </a>
                        <?php else: ?>
                            <h6 class="fw-bold mb-0 text-muted">-</h6>
                            <div class="text-muted small">เข้าล่าสุด</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stat-badge-card">
                    <div class="icon-box bg-label-warning text-warning">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div>
                        <?php if (!empty($recentPages) && isset($recentPages[1])): ?>
                            <a href="<?= base_url($recentPages[1]['page_url']) ?>" class="text-decoration-none">
                                <h6 class="fw-bold mb-0 text-dark"><?= esc($recentPages[1]['page_name']) ?></h6>
                                <div class="text-muted small">เข้าล่าสุดอันดับ 2</div>
                            </a>
                        <?php else: ?>
                            <h6 class="fw-bold mb-0 text-muted">-</h6>
                            <div class="text-muted small">เข้าล่าสุดอันดับ 2</div>
                        <?php endif; ?>
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
                <div class="row g-4 mb-4">
                    <div class="col-lg-4 col-md-6">
                        <a href="<?= base_url('club') ?>" class="luxe-card">
                            <i class="bi bi-people card-icon"></i>
                            <div class="card-text">
                                <h6>กิจกรรมชุมนุม</h6>
                                <p>บันทึกเวลาเรียนและกิจกรรมชมรม</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- งานบุคลากร -->
                <div class="menu-title-row">
                    <h5>งานบุคลากร</h5>
                    <div class="menu-line"></div>
                </div>
                <div class="row g-4 mb-5">
                    <div class="col-lg-4 col-md-6">
                        <a href="<?= base_url('attendance') ?>" class="luxe-card">
                            <i class="bi bi-clock-history card-icon text-primary"></i>
                            <div class="card-text">
                                <h6>SKJ Check-In</h6>
                                <p>เช็คชื่อเข้า-ออกงานออนไลน์ พร้อม GPS และ ถ่ายรูป</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <a href="<?= base_url('leave') ?>" class="luxe-card">
                            <i class="bi bi-calendar-check card-icon text-success"></i>
                            <div class="card-text">
                                <h6>ระบบการลา</h6>
                                <p>เขียนใบลาและติดตามสถานะการลา</p>
                            </div>
                        </a>
                    </div>
                    <?php if ($isPAPermitted): ?>
                    <div class="col-lg-4 col-md-6">
                        <a href="<?= base_url('evaluation') ?>" class="luxe-card">
                            <i class="bi bi-file-earmark-pdf card-icon text-danger"></i>
                            <div class="card-text">
                                <h6 style="line-height: 1.35;">การประเมินผลการปฏิบัติงานข้าราชการหรือพนักงานครูและบุคลากรทางการศึกษาองค์กรปกครองส่วนท้องถิ่น</h6>
                                <p>ครั้งที่ 1 (1 ต.ค. - 31 มี.ค.) ครั้งที่ 2 (1 เม.ย. - 30 ก.ย.)</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <a href="<?= base_url('pa-agreement') ?>" class="luxe-card">
                            <i class="bi bi-journal-bookmark-fill card-icon text-primary"></i>
                            <div class="card-text">
                                <h6>การประเมินผลการพัฒนางานตามข้อตกลง (PA)</h6>
                                <p>ส่งสื่อนำเสนอ, แผนการสอน, PA1</p>
                            </div>
                        </a>
                    </div>
                    <?php endif; ?>
                    <div class="col-lg-4 col-md-6">
                        <a href="<?= base_url('portfolio') ?>" class="luxe-card">
                            <i class="bi bi-person-workspace card-icon text-info"></i>
                            <div class="card-text">
                                <h6>ประวัติการอบรมและผลงาน</h6>
                                <p>บันทึกประวัติการพัฒนาตนเองและผลงาน</p>
                            </div>
                        </a>
                    </div>
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