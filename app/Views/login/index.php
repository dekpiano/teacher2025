<!doctype html>
<html lang="th" class="light-style customizer-hide" dir="ltr" data-theme="theme-default">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
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
            --primary-hover: #5f61e6;
            --secondary-color: #8592a3;
            --glass-bg: rgba(255, 255, 255, 0.78);
            --glass-border: rgba(255, 255, 255, 0.45);
            --skj-green: #15a362;
        }

        body {
            font-family: 'K2D', sans-serif;
            background: radial-gradient(circle at 10% 20%, #f0f3ff 0%, #dce3fa 90%);
            height: 100vh;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        /* Floating background shapes for extra depth */
        .bg-glow-bubble-1 {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(105, 108, 255, 0.15) 0%, rgba(105, 108, 255, 0) 70%);
            top: -200px;
            left: -200px;
            z-index: 0;
            pointer-events: none;
        }

        .bg-glow-bubble-2 {
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(21, 163, 98, 0.1) 0%, rgba(21, 163, 98, 0) 70%);
            bottom: -300px;
            right: -100px;
            z-index: 0;
            pointer-events: none;
        }

        .login-wrapper {
            display: flex;
            height: 100vh;
            width: 100%;
            position: relative;
            z-index: 1;
        }

        /* Left Side: Branding & Features */
        .info-panel {
            flex: 1.2;
            background-color: #696cff;
            background-image: 
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.05' d='M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,181.3C960,181,1056,235,1152,234.7C1248,235,1344,181,1392,154.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3Cpath fill='%23ffffff' fill-opacity='0.08' d='M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,165.3C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E"),
                linear-gradient(135deg, rgba(105, 108, 255, 0.92) 0%, rgba(63, 66, 181, 0.96) 100%);
            background-size: cover, cover;
            background-position: bottom, center;
            background-repeat: no-repeat;
            color: white;
            padding: 5rem 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.05);
        }

        .info-panel::before {
            content: "";
            position: absolute;
            top: -20%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            z-index: 1;
        }

        .info-panel::after {
            content: "";
            position: absolute;
            bottom: -10%;
            left: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            z-index: 1;
        }

        .info-content {
            position: relative;
            z-index: 2;
            max-width: 680px;
            margin: 0 auto;
        }

        .school-logo {
            height: 105px;
            margin-bottom: 2rem;
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.15));
            animation: fadeInDown 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .system-title {
            font-weight: 800;
            font-size: 3.2rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.03em;
            background: linear-gradient(to right, #ffffff, #e5e6ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fadeInLeft 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .system-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 3.5rem;
            font-weight: 400;
            animation: fadeInLeft 1s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .feature-list {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            animation: fadeInUp 1.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @media (max-width: 1200px) {
            .feature-list {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        .feature-item {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.08);
            padding: 1.25rem;
            border-radius: 1.25rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .feature-item:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .feature-icon {
            font-size: 1.8rem;
            margin-right: 1.2rem;
            width: 48px;
            height: 48px;
            background: #ffffff;
            color: var(--primary-color);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .feature-text h6 {
            margin: 0;
            color: white;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: -0.01em;
        }

        .feature-text p {
            margin: 0.25rem 0 0 0;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.3;
        }

        /* Right Side: Login Form */
        .login-panel {
            flex: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 2rem;
            padding: 3.5rem 3rem;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.06), 0 0 0 1px var(--glass-border);
            animation: zoomIn 0.7s cubic-bezier(0.16, 1, 0.3, 1);
            transition: all 0.3s ease;
        }

        .login-card:hover {
            box-shadow: 0 35px 70px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(255, 255, 255, 0.6);
        }

        .login-header-logo {
            display: none;
        }

        .btn-google-login {
            background: linear-gradient(135deg, #696cff 0%, #3f4191 100%);
            color: #ffffff;
            border: none;
            padding: 16px 24px;
            border-radius: 3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 10px 20px rgba(105, 108, 255, 0.4);
            text-decoration: none;
            font-size: 1.1rem;
            animation: pulse-login 2s infinite;
        }

        .btn-google-login:hover {
            background: linear-gradient(135deg, #3f4191 0%, #696cff 100%);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 25px rgba(105, 108, 255, 0.5);
            color: #ffffff !important;
        }

        .btn-google-login img {
            height: 24px;
            margin-right: 12px;
            background: white;
            border-radius: 50%;
            padding: 2px;
        }

        @keyframes pulse-login {
            0% {
                box-shadow: 0 0 0 0 rgba(105, 108, 255, 0.7);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(105, 108, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(105, 108, 255, 0);
            }
        }

        .login-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 2.25rem 0;
            color: #aaafb6;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
        }

        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1.5px solid rgba(0, 0, 0, 0.06);
        }

        .login-divider span {
            padding: 0 15px;
        }

        .info-note {
            background: linear-gradient(135deg, rgba(105, 108, 255, 0.06) 0%, rgba(105, 108, 255, 0.02) 100%);
            border-radius: 1.25rem;
            padding: 1.25rem;
            font-size: 0.85rem;
            color: #5558e0;
            border: 1px solid rgba(105, 108, 255, 0.12);
            line-height: 1.5;
        }

        .login-footer {
            margin-top: 2.5rem;
            text-align: center;
            font-size: 0.8rem;
            color: #88909a;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes float-3d-1 {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        @keyframes float-3d-2 {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(-5deg); }
        }
        @keyframes float-3d-3 {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-25px) scale(1.05); }
        }

        .floating-academic-1 {
            position: absolute;
            top: 10%;
            right: 5%;
            width: 140px;
            opacity: 0.85;
            filter: drop-shadow(0 15px 25px rgba(0,0,0,0.2));
            animation: float-3d-1 6s ease-in-out infinite;
            z-index: 1;
            pointer-events: none;
        }

        .floating-academic-2 {
            position: absolute;
            bottom: 12%;
            left: 5%;
            width: 120px;
            opacity: 0.7;
            filter: drop-shadow(0 15px 25px rgba(0,0,0,0.2));
            animation: float-3d-2 7s ease-in-out infinite;
            z-index: 1;
            pointer-events: none;
        }

        .floating-academic-3 {
            position: absolute;
            bottom: 25%;
            right: 15%;
            width: 100px;
            opacity: 0.6;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.15));
            animation: float-3d-3 5s ease-in-out infinite;
            z-index: 1;
            pointer-events: none;
        }

        @media (max-width: 992px) {
            body {
                overflow-y: auto;
                height: auto;
            }

            .login-wrapper {
                flex-direction: column-reverse;
                height: auto;
                min-height: 100vh;
            }

            .info-panel {
                padding: 3rem 1.5rem;
                flex: none;
                box-shadow: none;
                text-align: center;
            }

            .info-content {
                text-align: left;
            }

            .login-panel {
                flex: none;
                padding: 2rem 1rem;
                background: transparent;
                min-height: auto;
                display: flex;
                align-items: center;
            }

            .login-card {
                padding: 2.5rem 1.5rem;
                border-radius: 1.5rem;
                background: rgba(255, 255, 255, 0.95);
                width: 100%;
                margin-top: 1rem;
            }

            .system-title {
                font-size: 1.8rem;
                text-align: center;
            }

            .system-subtitle {
                font-size: 1rem;
                margin-bottom: 2rem;
                text-align: center;
            }

            .school-logo {
                display: block;
                margin: 0 auto 1.5rem auto;
            }

            .login-header-logo {
                display: block;
                height: 65px;
                margin: 0 auto 1rem auto;
                filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
            }

            /* Adjust floating academic images for mobile */
            .floating-academic-1 {
                width: 80px;
                top: 5%;
                right: 2%;
            }

            .floating-academic-2 {
                width: 70px;
                bottom: 2%;
                left: 2%;
            }

            .floating-academic-3 {
                width: 60px;
                bottom: 10%;
                right: 5%;
            }
        }
    </style>
</head>

<!-- Floating Background Bubbles -->
<div class="bg-glow-bubble-1"></div>
<div class="bg-glow-bubble-2"></div>

<div class="login-wrapper">
    <!-- Left Panel -->
    <div class="info-panel">
        <!-- 3D Academic Decorative Elements -->
        <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/svg/1f393.svg" alt="Graduation Cap" class="floating-academic-1">
        <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/svg/1f4da.svg" alt="Books" class="floating-academic-2">
        <img src="https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/svg/1f52c.svg" alt="Microscope" class="floating-academic-3">

        <div class="info-content">
            <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="SKJ Logo" class="school-logo">
            <h1 class="system-title">Teacher SKJ Online</h1>
            <p class="system-subtitle">ระบบบริหารจัดการสารสนเทศและงานวิชาการสำหรับครู</p>

            <ul class="feature-list">
                <li class="feature-item">
                    <div class="feature-icon"><i class="bi bi-clipboard-data"></i></div>
                    <div class="feature-text">
                        <h6>งานวัดผลและวิชาการ</h6>
                        <p>บันทึกคะแนน, เรียนซ้ำ, ประเมินการอ่านคิดวิเคราะห์ และคุณลักษณะฯ</p>
                    </div>
                </li>
                <li class="feature-item">
                    <div class="feature-icon"><i class="bi bi-file-earmark-text"></i></div>
                    <div class="feature-text">
                        <h6>งานหลักสูตรและพัฒนาครู</h6>
                        <p>จัดการแผนการจัดการเรียนรู้ และงานวิจัยในชั้นเรียน</p>
                    </div>
                </li>
                <li class="feature-item">
                    <div class="feature-icon"><i class="bi bi-people"></i></div>
                    <div class="feature-text">
                        <h6>กิจกรรมพัฒนาผู้เรียน</h6>
                        <p>บันทึกเวลาเรียนและประเมินผลกิจกรรมชุมนุม</p>
                    </div>
                </li>
                <li class="feature-item">
                    <div class="feature-icon"><i class="bi bi-person-badge"></i></div>
                    <div class="feature-text">
                        <h6>งานบุคลากร</h6>
                        <p>SKJ Check-In เข้า-ออกงาน และระบบการลาออนไลน์</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="login-panel">
        <div class="login-card">
            <div class="text-center mb-4">
                <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="SKJ Logo" class="login-header-logo">
                <h3 class="fw-black text-primary mb-2" style="letter-spacing: -0.02em;">ลงชื่อเข้าใช้งาน</h3>
                <p class="text-muted small mb-0">ระบบสารสนเทศและบริหารงานวิชาการครู สกจ.</p>
            </div>

            <?php if (session()->getFlashdata('msg')): ?>
                <div class="alert alert-danger mb-4 shadow-sm border-0 d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div><?= session()->getFlashdata('msg') ?></div>
                </div>
            <?php endif; ?>


            <a href="<?= $google_login_url ?>" class="btn-google-login">
                <img src="https://www.gstatic.com/images/branding/product/2x/googleg_48dp.png" alt="Google Logo">
                <span>เข้าสู่ระบบด้วย @skj.ac.th</span>
            </a>

            <div class="login-divider">
                <span>Academic Information Management</span>
            </div>

            <div class="info-note">
                <div class="d-flex align-items-start">
                    <i class="bi bi-info-circle-fill me-2 mt-1" style="font-size: 1.15rem; flex-shrink: 0;"></i>
                    <div>
                        <strong>หมายเหตุ:</strong> หากไม่สามารถเข้าสู่ระบบได้ กรุณาตรวจสอบว่าใช้บัญชีอีเมลโรงเรียน
                        (@skj.ac.th) หรือติดต่อผู้ดูแลระบบสารสนเทศ
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

<script>
    $(function () {
        // Loading for Google Login Link
        $('.btn-google-login').on('click', function (e) {
            const $btn = $(this);
            if ($btn.hasClass('disabled')) return;

            $btn.addClass('disabled');
            $btn.css('min-width', $btn.outerWidth() + 'px');
            $btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span> กำลังนำคุณไปยังหน้าเข้าสู่ระบบ...');
        });

        // Global Form Submit (if any)
        $(document).on('submit', 'form', function () {
            const $btn = $(this).find('button[type="submit"], input[type="submit"]').not(':disabled');
            $btn.each(function () {
                const $b = $(this);
                $b.prop('disabled', true);
                $b.css('min-width', $b.outerWidth() + 'px');
                $b.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span> กำลังดำเนินการ...');
            });
        });
    });
</script>
</body>

</html>