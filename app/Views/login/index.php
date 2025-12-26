<!doctype html>
<html lang="th" class="light-style customizer-hide" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>เข้าสู่ระบบ | ระบบบริหารจัดการงานครู สกจ.9</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/css/theme-default.css') ?>" />
    
    <style>
        :root {
            --primary-color: #696cff;
            --secondary-color: #8592a3;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --skj-green: #15a362;
        }

        body {
            font-family: 'K2D', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .login-wrapper {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* Left Side: Branding & Features */
        .info-panel {
            flex: 1.2;
            background: linear-gradient(45deg, #696cff 0%, #4e51d8 100%);
            color: white;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .info-panel::before {
            content: "";
            position: absolute;
            top: -10%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 1;
        }

        .info-panel::after {
            content: "";
            position: absolute;
            bottom: -5%;
            left: -5%;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            z-index: 1;
        }

        .info-content {
            position: relative;
            z-index: 2;
        }

        .school-logo {
            height: 100px;
            margin-bottom: 2rem;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
            animation: fadeInDown 0.8s ease-out;
        }

        .system-title {
            font-weight: 700;
            font-size: 2.8rem;
            margin-bottom: 1rem;
            animation: fadeInLeft 0.8s ease-out;
        }

        .system-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 3rem;
            animation: fadeInLeft 1s ease-out;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            animation: fadeInUp 1.2s ease-out;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 12px;
            backdrop-filter: blur(5px);
            transition: transform 0.3s ease;
        }

        .feature-item:hover {
            transform: translateX(10px);
            background: rgba(255, 255, 255, 0.2);
        }

        .feature-icon {
            font-size: 1.8rem;
            margin-right: 1.2rem;
            width: 50px;
            height: 50px;
            background: white;
            color: var(--primary-color);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .feature-text h6 {
            margin: 0;
            color: white;
            font-weight: 600;
            font-size: 1.05rem;
        }

        .feature-text p {
            margin: 0;
            font-size: 0.85rem;
            opacity: 0.8;
        }

        /* Right Side: Login Form */
        .login-panel {
            flex: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            padding: 2rem;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border: 1px solid rgba(255,255,255,0.5);
            animation: zoomIn 0.6s ease-out;
        }

        .btn-google-login {
            background-color: #fff;
            color: #444;
            border: 1px solid #ddd;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            text-decoration: none;
        }

        .btn-google-login:hover {
            background-color: #f1f1f1;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        .btn-google-login img {
            height: 24px;
            margin-right: 12px;
        }

        .login-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 2rem 0;
            color: #888;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .login-divider::before, .login-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #eee;
        }

        .login-divider span {
            padding: 0 15px;
        }

        .info-note {
            background: rgba(105, 108, 255, 0.08);
            border-radius: 12px;
            padding: 1rem;
            font-size: 0.85rem;
            color: var(--primary-color);
            border: 1px solid rgba(105, 108, 255, 0.1);
        }

        .login-footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.85rem;
            color: #888;
        }

        /* Animations */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        @media (max-width: 992px) {
            .login-wrapper { flex-direction: column; overflow-y: auto; }
            .info-panel { padding: 3rem 2rem; flex: none; }
            .login-panel { flex: none; padding: 4rem 1rem; }
            .system-title { font-size: 2rem; }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <!-- Left Panel -->
        <div class="info-panel">
            <div class="info-content">
                <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="SKJ Logo" class="school-logo">
                <h1 class="system-title">Teacher SKJ Online</h1>
                <p class="system-subtitle">ระบบบริหารจัดการสารสนเทศและงานวิชาการสำหรับครู</p>

                <ul class="feature-list">
                    <li class="feature-item">
                        <div class="feature-icon"><i class="bi bi-journal-check"></i></div>
                        <div class="feature-text">
                            <h6>บันทึกผลการเรียน</h6>
                            <p>จัดการคะแนนและเกรดนักเรียน ทั้งรายวิชาปกติและเรียนซ้ำ</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon"><i class="bi bi-file-earmark-pdf"></i></div>
                        <div class="feature-text">
                            <h6>งานหลักสูตรและแผนการสอน</h6>
                            <p>ส่งแผนการจัดการเรียนรู้ และงานวิจัยในชั้นเรียนออนไลน์</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon"><i class="bi bi-person-check"></i></div>
                        <div class="feature-text">
                            <h6>ประเมินผลนักเรียน</h6>
                            <p>บันทึกอ่านคิดวิเคราะห์ และคุณลักษณะอันพึงประสงค์</p>
                        </div>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon"><i class="bi bi-people"></i></div>
                        <div class="feature-text">
                            <h6>กิจกรรมพัฒนาผู้เรียน</h6>
                            <p>เช็คชื่อและประเมินผลกิจกรรมชุมนุมประจำสัปดาห์</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="login-panel">
            <div class="login-card">
                <div class="text-center mb-5">
                    <h3 class="fw-bold mb-2">ยินดีต้อนรับ</h3>
                    <p class="text-muted small">กรุณาเข้าสู่ระบบเพื่อเริ่มใช้งาน</p>
                </div>

                <?php if (session()->getFlashdata('msg')) : ?>
                    <div class="alert alert-danger mb-4 shadow-sm border-0 d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div><?= session()->getFlashdata('msg') ?></div>
                    </div>
                <?php endif; ?>

                <a href="<?= $google_login_url ?>" class="btn-google-login">
                    <img src="https://www.gstatic.com/images/branding/product/2x/googleg_48dp.png" alt="Google Logo">
                    <span>ด้วยอีเมลองค์กร @skj.ac.th</span>
                </a>

                <div class="login-divider">
                    <span>Academic Information Management</span>
                </div>

                <div class="info-note">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                        <div>
                            <strong>หมายเหตุ:</strong> หากไม่สามารถเข้าสู่ระบบได้ กรุณาตรวจสอบว่าคุณใช้อีเมลภายใต้องค์กร (@skj.ac.th) หรือติดต่อเจ้าหน้าที่หรือครูคอมพิวเตอร์
                        </div>
                    </div>
                </div>

                <div class="login-footer">
                    <p class="mb-0">© <?= date('Y') ?> สวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</p>
                    <small>Version 2.0 Agentic Edition</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="<?= base_url('public/assets/sneat/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= base_url('public/assets/sneat/vendor/js/bootstrap.js') ?>"></script>
</body>
</html>