<!DOCTYPE html>
<html lang="th" class="light-style customizer-hide" dir="ltr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>คู่มือการใช้งานระบบสำหรับครู | โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</title>
    <meta name="description" content="คู่มือการเข้าใช้งานระบบสารสนเทศและบริหารงานวิชาการครู และการรีเซ็ตรหัสผ่าน (@skj.ac.th)" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" />

    <!-- Google Fonts: K2D & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=K2D:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/css/theme-default.css') ?>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        :root {
            --primary-purple: #696cff;
            --primary-dark: #3f4191;
            --primary-light: #8592a3;
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 35%, #312e81 70%, #4338ca 100%);
        }

        html {
            overflow-x: hidden;
            width: 100%;
        }

        body {
            font-family: 'K2D', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            margin: 0;
            padding: clamp(16px, 4vw, 36px) clamp(12px, 3vw, 24px);
            color: #f8fafc;
            position: relative;
            overflow-x: hidden;
        }

        /* Fixed Background Animation Container */
        .bg-animation-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .bg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            z-index: 0;
            pointer-events: none;
            opacity: 0.55;
            animation: orbFloat 12s ease-in-out infinite alternate;
        }

        .bg-orb-1 {
            width: clamp(280px, 45vw, 500px);
            height: clamp(280px, 45vw, 500px);
            background: rgba(105, 108, 255, 0.7);
            top: -100px;
            left: -100px;
        }

        .bg-orb-2 {
            width: clamp(300px, 50vw, 550px);
            height: clamp(300px, 50vw, 550px);
            background: rgba(99, 102, 241, 0.6);
            bottom: -120px;
            right: -120px;
            animation-delay: -6s;
        }

        @keyframes orbFloat {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(35px, 25px) scale(1.12); }
        }

        /* Main Glass Container */
        .guide-container {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: clamp(20px, 4vw, 32px);
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.5), 0 0 40px rgba(105, 108, 255, 0.25);
            color: #1e293b;
            overflow: hidden;
            animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .guide-header {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--primary-dark) 100%);
            padding: clamp(28px, 6vw, 42px) clamp(20px, 4vw, 32px);
            text-align: center;
            color: #ffffff;
            position: relative;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .brand-logo {
            width: clamp(75px, 16vw, 95px);
            height: clamp(75px, 16vw, 95px);
            object-fit: contain;
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.3));
            margin-bottom: 12px;
        }

        .guide-body {
            padding: clamp(24px, 5vw, 40px) clamp(18px, 4vw, 36px);
        }

        /* Step Card Styling */
        .step-card {
            background: #ffffff;
            border-radius: 22px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
            padding: clamp(20px, 4vw, 30px);
            margin-bottom: 28px;
            position: relative;
            transition: all 0.3s ease;
        }

        .step-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 32px rgba(0, 0, 0, 0.08);
            border-color: var(--primary-purple);
        }

        .step-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 16px;
        }

        .step-badge-1 {
            background: linear-gradient(135deg, #696cff 0%, #3f4191 100%);
            box-shadow: 0 4px 14px rgba(105, 108, 255, 0.4);
        }

        .step-badge-2 {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            box-shadow: 0 4px 14px rgba(6, 182, 212, 0.4);
        }

        .step-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .step-list li {
            position: relative;
            padding-left: 36px;
            margin-bottom: 16px;
            font-size: 1rem;
            line-height: 1.6;
            color: #334155;
        }

        .step-list li:last-child {
            margin-bottom: 0;
        }

        .step-number {
            position: absolute;
            left: 0;
            top: 2px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #f1f5f9;
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--primary-purple);
        }

        .info-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-left: 4px solid #3b82f6;
            border-radius: 14px;
            padding: 16px clamp(14px, 3vw, 20px);
            margin: 16px 0;
        }

        .tip-box {
            background: linear-gradient(135deg, #fefce8 0%, #fef08a 100%);
            border-left: 4px solid #eab308;
            border-radius: 14px;
            padding: 16px clamp(14px, 3vw, 20px);
            margin: 16px 0;
            color: #713f12;
        }

        /* FAQ Accordion Styling */
        .accordion-item {
            border: 1.5px solid #e2e8f0;
            border-radius: 16px !important;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .accordion-button {
            font-weight: 700;
            color: #1e293b;
            background: #f8fafc;
            padding: 16px 20px;
            font-size: 1rem;
        }

        .accordion-button:not(.collapsed) {
            color: var(--primary-dark);
            background: #e0e7ff;
            box-shadow: none;
        }

        .accordion-body {
            background: #ffffff;
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Action Buttons */
        .btn-action-primary {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            border: none;
            border-radius: 16px;
            padding: 14px 24px;
            font-size: 1.05rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(105, 108, 255, 0.4);
            transition: all 0.25s ease;
        }

        .btn-action-primary:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(105, 108, 255, 0.55);
        }

        .btn-action-secondary {
            background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%);
            color: #ffffff;
            border: none;
            border-radius: 16px;
            padding: 14px 24px;
            font-size: 1.05rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(6, 182, 212, 0.4);
            transition: all 0.25s ease;
        }

        .btn-action-secondary:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(6, 182, 212, 0.55);
        }

        .back-home-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 10px 20px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            transition: all 0.25s ease;
            margin-top: 20px;
        }

        .back-home-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.22);
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <!-- Background Orbs -->
    <div class="bg-animation-container">
        <div class="bg-orb bg-orb-1"></div>
        <div class="bg-orb bg-orb-2"></div>
    </div>

    <!-- Main Container -->
    <div class="guide-container">

        <div class="glass-card">
            <!-- Header -->
            <div class="guide-header">
                <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="SKJ Logo" class="brand-logo" />
                <h3 class="fw-bold mb-1 text-white">คู่มือการใช้งานระบบบริหารงานครู SKJ</h3>
                <p class="mb-0 text-white-50 fs-6">สำหรับครูและบุคลากร โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</p>
            </div>

            <!-- Body -->
            <div class="guide-body">

                <!-- Welcome Text -->
                <div class="alert alert-light border-0 shadow-sm p-3 mb-4 rounded-4 text-center">
                    <span class="fs-5">👨‍🏫👩‍🏫</span> <strong>สวัสดีคุณครูและบุคลากรทุกท่านครับ!</strong><br>
                    คู่มือนี้จะแนะนำขั้นตอนการเข้าใช้งานระบบงานครูด้วย <strong>Google Account (@skj.ac.th)</strong> รวมถึงวิธีแก้ปัญหาเมื่อลืมรหัสผ่านด้วยตนเองครับ
                </div>

                <!-- METHOD 1: Google Login -->
                <div class="step-card">
                    <span class="step-badge step-badge-1">
                        <i class="bi bi-google fs-5"></i> การเข้าสู่ระบบด้วย Google (@skj.ac.th)
                    </span>

                    <div class="info-box">
                        <div class="fw-bold text-primary mb-1"><i class="bi bi-shield-check me-1"></i> เข้าใช้งานง่ายและปลอดภัยสูงสุด</div>
                        <div class="small">ระบบงานครูรองรับการยืนยันตัวตนผ่าน Google Single Sign-On (SSO) ด้วยอีเมลประจำตัว <code>@skj.ac.th</code> ไม่ต้องจำชื่อผู้ใช้งานหลายชุด</div>
                    </div>

                    <ol class="step-list">
                        <li>
                            <span class="step-number">1</span>
                            เปิดหน้าหลักของ <strong>ระบบบริหารจัดการงานครู</strong>
                        </li>
                        <li>
                            <span class="step-number">2</span>
                            กดที่ปุ่มสีน้ำเงินป๊อบอัพปุ่มใหญ่ <span class="badge bg-primary text-white p-2"><i class="bi bi-google me-1"></i> เข้าสู่ระบบด้วย @skj.ac.th</span>
                        </li>
                        <li>
                            <span class="step-number">3</span>
                            เลือกหรือระบุบัญชีอีเมลโรงเรียนของท่าน (<code>@skj.ac.th</code>) พร้อมใส่รหัสผ่าน
                        </li>
                        <li>
                            <span class="step-number">4</span>
                            🎉 <strong>เข้าสู่ระบบสำเร็จ!</strong> ระบบจะพาท่านเข้าสู่หน้าแดชบอร์ดงานวิชาการ การบันทึกคะแนน แผนการสอน และระบบการลาออนไลน์ทันที
                        </li>
                    </ol>

                    <div class="mt-3 text-end">
                        <a href="<?= base_url('login') ?>" class="btn-action-primary">
                            <i class="bi bi-box-arrow-in-right"></i> ไปที่หน้าเข้าสู่ระบบครู &rarr;
                        </a>
                    </div>
                </div>

                <!-- METHOD 2: Forgot / Reset Password -->
                <div class="step-card">
                    <span class="step-badge step-badge-2">
                        <i class="bi bi-key-fill fs-5"></i> การสุ่มรีเซ็ตรหัสผ่านด้วยตนเอง (เมื่อลืมรหัสผ่าน)
                    </span>

                    <div class="info-box" style="background: #ecfeff; border-color: #06b6d4; color: #155e75;">
                        <div class="fw-bold mb-1"><i class="bi bi-lightning-charge me-1"></i> รีเซ็ตรหัสผ่านได้ตลอด 24 ชั่วโมง</div>
                        <div class="small">หากจำรหัสผ่านเข้าใช้งานไม่ได้ สามารถใช้ระบบรีเซ็ตรหัสผ่านเพื่อสุ่มรับรหัสผ่านใหม่ได้ทันทีโดยไม่ต้องรอเจ้าหน้าที่</div>
                    </div>

                    <ol class="step-list">
                        <li>
                            <span class="step-number">1</span>
                            ไปที่หน้าแรก แล้วกดปุ่ม <span class="badge bg-light text-primary border border-primary font-weight-bold px-2 py-1"><i class="bi bi-key me-1"></i> "ลืมรหัสผ่าน / รีเซ็ตรหัสผ่านครู"</span>
                        </li>
                        <li>
                            <span class="step-number">2</span>
                            ระบุ ✉️ <strong>อีเมลโรงเรียน</strong> (เช่น <code>teacher@skj.ac.th</code>) และ 📱 <strong>เบอร์โทรศัพท์</strong> ที่ลงทะเบียนไว้ในระบบ
                        </li>
                        <li>
                            <span class="step-number">3</span>
                            กดปุ่ม <span class="badge bg-primary">"ตรวจสอบและสุ่มรีเซ็ตรหัสผ่านใหม่"</span> และยืนยันการทำรายการ
                        </li>
                        <li>
                            <span class="step-number">4</span>
                            <strong>คัดลอกรหัสผ่านใหม่ที่ได้:</strong> ระบบจะตรวจสอบความถูกต้องของอีเมลและเบอร์โทรศัพท์ หากตรงกันจะแสดงรหัสผ่านใหม่ที่สุ่มขึ้นมา พร้อมอัปเดตลงในระบบ Google Workspace ให้อัตโนมัติ สามารถนำรหัสนี้ไปใช้เข้าสู่ระบบได้ทันที
                        </li>
                    </ol>

                    <div class="mt-3 text-end">
                        <a href="<?= base_url('verify-email') ?>" class="btn-action-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> ไปที่หน้ารีเซ็ตรหัสผ่านครู &rarr;
                        </a>
                    </div>
                </div>

                <!-- FAQ SECTION -->
                <div class="mt-4">
                    <h5 class="fw-bold text-dark mb-3">❓ คำถามที่พบบ่อย (FAQ)</h5>

                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    📧 1. ใช้อีเมลส่วนตัว (@gmail.com) เข้าสู่ระบบได้หรือไม่?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    ไม่ได้ครับ ระบบจำกัดสิทธิ์ให้เฉพาะบัญชีอีเมลโดเมนโรงเรียน <code>@skj.ac.th</code> เท่านั้น หากท่านยังไม่มีอีเมลโรงเรียน สามารถติดต่อศูนย์เทคโนโลยีสารสนเทศเพื่อออกบัญชีให้ได้ครับ
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    🔒 2. สุ่มรีเซ็ตรหัสผ่านแล้ว สามารถนำไปล็อกอินที่ไหนได้บ้าง?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    รหัสผ่านใหม่ที่ได้จากการสุ่มรีเซ็ต สามารถนำไปล็อกอินเข้า <strong>Gmail, Google Workspace, Google Classroom</strong> และ <strong>ระบบบริหารงานครู SKJ</strong> ได้ทันทีครับ
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    📞 3. หากพบปัญหาในการเข้าใช้งาน ต้องติดต่อใคร?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    หากไม่พบข้อมูลอีเมลในระบบ หรือพ้นสภาพการใช้งาน สามารถติดต่อผู้ดูแลระบบสารสนเทศ งานเทคโนโลยีสารสนเทศ โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์ ได้โดยตรงครับ
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Back to Login Page Link -->
        <div class="text-center mb-4">
            <a href="<?= base_url('login') ?>" class="back-home-link">
                <i class="bi bi-arrow-left"></i> กลับสู่หน้าหลักเข้าสู่ระบบครู
            </a>
        </div>

    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
