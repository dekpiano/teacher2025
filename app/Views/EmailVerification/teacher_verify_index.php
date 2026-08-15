<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'รีเซ็ตรหัสผ่านครูและบุคลากร' ?> | โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</title>
    <link rel="icon" type="image/x-icon" href="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" />
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/css/theme-default.css') ?>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
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
            font-family: 'K2D', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 25px 15px;
            color: #f8fafc;
            position: relative;
            overflow-x: hidden;
        }

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
            filter: blur(100px);
            pointer-events: none;
            opacity: 0.5;
            animation: orbFloat 12s ease-in-out infinite alternate;
        }

        .bg-orb-1 {
            width: 480px;
            height: 480px;
            background: rgba(105, 108, 255, 0.65);
            top: -120px;
            left: -120px;
        }

        .bg-orb-2 {
            width: 520px;
            height: 520px;
            background: rgba(99, 102, 241, 0.5);
            bottom: -150px;
            right: -150px;
            animation-delay: -6s;
        }

        @keyframes orbFloat {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(45px, 35px) scale(1.1); }
        }

        .verify-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 28px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
            color: #0f172a;
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            position: relative;
            z-index: 2;
        }

        .verify-header {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--primary-dark) 100%);
            padding: 35px 25px 30px;
            text-align: center;
            color: #ffffff;
            position: relative;
        }

        .logo-img {
            width: 85px;
            height: 85px;
            object-fit: contain;
            filter: drop-shadow(0 6px 15px rgba(0,0,0,0.3));
            margin-bottom: 12px;
        }

        .badge-step {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            color: #ffffff;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 5px 16px;
            border-radius: 20px;
            margin-top: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .form-body {
            padding: 30px 28px;
        }

        .custom-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-custom .input-icon {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            font-size: 1.25rem;
            z-index: 4;
        }

        .input-group-custom .form-control {
            padding-left: 48px;
            height: 52px;
            border-radius: 16px;
            border: 1.5px solid #cbd5e1;
            font-size: 1rem;
            background: #f8fafc;
            color: #0f172a;
            transition: all 0.25s ease;
        }

        .input-group-custom .form-control:focus {
            background: #ffffff;
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 4px rgba(105, 108, 255, 0.15);
        }

        .btn-submit-verify {
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--primary-dark) 100%);
            border: none;
            color: white;
            height: 54px;
            border-radius: 16px;
            font-size: 1.05rem;
            font-weight: 700;
            width: 100%;
            box-shadow: 0 8px 24px rgba(105, 108, 255, 0.4);
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(105, 108, 255, 0.5);
            color: white;
        }

        .result-box {
            display: none;
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 20px;
            padding: 24px 20px;
            margin-top: 24px;
            text-align: center;
            animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .email-display-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 16px;
            margin: 14px 0;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        }

        .email-address {
            font-family: monospace, sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-dark);
            word-break: break-all;
        }

        .btn-copy-custom {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 7px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-google-login {
            background-color: #ffffff;
            color: #3c4043;
            border: 1px solid #dadce0;
            border-radius: 14px;
            padding: 13px 20px;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .back-home-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 0.9rem;
            margin-top: 20px;
            position: relative;
            z-index: 2;
            padding: 8px 18px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
        }
    </style>
</head>
<body>

    <div class="bg-animation-container">
        <div class="bg-orb bg-orb-1"></div>
        <div class="bg-orb bg-orb-2"></div>
    </div>

    <div class="d-flex flex-column align-items-center w-100">
        <div class="verify-card">
            <!-- Header -->
            <div class="verify-header">
                <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="SKJ Logo" class="logo-img">
                <h4 class="fw-bold mb-1 text-white">ระบบรีเซ็ตรหัสผ่านครูและบุคลากร</h4>
                <p class="mb-0 text-white-50 small">โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</p>
                <div class="badge-step">
                    <i class="bi bi-key-fill"></i> รีเซ็ตรหัสผ่านผ่านระบบ Google Workspace (@skj.ac.th)
                </div>
            </div>

            <!-- Form Body -->
            <div class="form-body">
                <form id="resetForm">
                    <div class="mb-3">
                        <label for="email_input" class="custom-label">
                            <i class="bi bi-envelope-at text-primary"></i> ระบุอีเมลโรงเรียนของคุณ (@skj.ac.th)
                        </label>
                        <div class="input-group-custom">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" class="form-control" id="email_input" name="email" placeholder="ตัวอย่าง: teacher@skj.ac.th" required autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="phone_input" class="custom-label">
                            <i class="bi bi-telephone-fill text-primary"></i> ระบุเบอร์โทรศัพท์ของคุณ (ที่บันทึกในระบบ)
                        </label>
                        <div class="input-group-custom">
                            <i class="bi bi-telephone input-icon"></i>
                            <input type="tel" class="form-control" id="phone_input" name="phone" placeholder="ตัวอย่าง: 0812345678" maxlength="12" required autocomplete="off">
                        </div>
                        <div class="form-text text-muted small mt-2">
                            <i class="bi bi-shield-lock-fill text-success me-1"></i> ยืนยัน 2 ชั้นด้วยอีเมลและเบอร์โทรศัพท์เพื่อความปลอดภัยสูงสุด
                        </div>
                    </div>

                    <button type="submit" class="btn btn-submit-verify" id="btnSubmitReset">
                        <i class="bi bi-arrow-counterclockwise fs-5"></i> ตรวจสอบและสุ่มรีเซ็ตรหัสผ่านใหม่
                    </button>
                </form>

                <!-- Result Box -->
                <div class="result-box" id="resultBox">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle p-2 mb-2" style="width: 46px; height: 46px;">
                        <i class="bi bi-check-lg fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-success mb-1">รีเซ็ตรหัสผ่านสำเร็จ!</h5>
                    <p class="text-muted small mb-3">รหัสผ่านใหม่ของคุณได้รับการอัปเดตในระบบเรียบร้อยแล้ว</p>

                    <div class="text-start bg-white p-3 rounded-3 mb-3 border">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">ชื่อ-นามสกุล:</span>
                            <strong id="resName" class="text-dark">-</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">ตำแหน่ง:</span>
                            <strong id="resPosition" class="text-dark">-</strong>
                        </div>
                    </div>

                    <div class="email-display-card">
                        <div class="text-muted small mb-1">อีเมลประจำตัวของคุณ:</div>
                        <div class="email-address mb-2" id="resEmail">@skj.ac.th</div>
                        <button class="btn btn-copy-custom me-2" type="button" id="btnCopyEmail">
                            <i class="bi bi-clipboard"></i> คัดลอกอีเมล
                        </button>
                    </div>

                    <div class="email-display-card bg-light border-warning" id="passwordCard">
                        <div class="text-muted small mb-1">รหัสผ่านใหม่สำหรับเข้าใช้งาน:</div>
                        <div class="email-address text-danger mb-2" id="resPassword">xxxxxx</div>
                        <button class="btn btn-copy-custom" type="button" id="btnCopyPassword">
                            <i class="bi bi-key"></i> คัดลอกรหัสผ่านใหม่
                        </button>
                    </div>

                    <div class="alert alert-info text-start small mb-3" role="alert">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>ข้อแนะนำ:</strong><br>
                        นำอีเมลและรหัสผ่านใหม่ไปเข้าสู่ระบบที่หน้าเว็บครู หรือ <strong>Gmail</strong> เพื่อเริ่มใช้งานได้ทันที
                    </div>

                    <a href="<?= base_url('login') ?>" class="btn btn-google-login">
                        <img src="https://www.gstatic.com/images/branding/product/2x/googleg_48dp.png" alt="Google Logo" style="height:20px;">
                        กลับไปหน้าเข้าสู่ระบบครู
                    </a>
                </div>
            </div>
        </div>

        <a href="<?= base_url('login') ?>" class="back-home-btn">
            <i class="bi bi-arrow-left"></i> กลับสู่หน้าเข้าสู่ระบบครู
        </a>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('public/assets/sneat/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= base_url('public/assets/sneat/vendor/js/bootstrap.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Submit Reset Form
        $('#resetForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#btnSubmitReset');
            const origHtml = btn.html();

            Swal.fire({
                title: 'ยืนยันการรีเซ็ตรหัสผ่าน?',
                text: 'ระบบจะทำการสุ่มตั้งรหัสผ่านใหม่ให้กับบัญชีอีเมลของคุณทันที',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ยืนยันรีเซ็ต',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#696cff'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> กำลังรีเซ็ตรหัสผ่าน...');

                    $.ajax({
                        url: '<?= base_url('verify-email/reset-password') ?>',
                        type: 'POST',
                        data: $('#resetForm').serialize(),
                        dataType: 'json',
                        success: function(res) {
                            btn.prop('disabled', false).html(origHtml);
                            if (res.status === 1) {
                                $('#resName').text(res.data.person_name);
                                $('#resPosition').text(res.data.position);
                                $('#resEmail').text(res.data.email);
                                $('#resPassword').text(res.data.new_password);
                                $('#passwordCard').show();

                                $('#resultBox').slideDown();
                                $('html, body').animate({ scrollTop: $('#resultBox').offset().top }, 500);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'รีเซ็ตรหัสผ่านสำเร็จ!',
                                    text: 'รหัสผ่านใหม่ของคุณคือ ' + res.data.new_password,
                                    confirmButtonColor: '#696cff'
                                });
                            } else {
                                $('#resultBox').hide();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'รีเซ็ตไม่สำเร็จ',
                                    text: res.message,
                                    confirmButtonColor: '#696cff'
                                });
                            }
                        },
                        error: function() {
                            btn.prop('disabled', false).html(origHtml);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ กรุณาลองใหม่',
                                confirmButtonColor: '#696cff'
                            });
                        }
                    });
                }
            });
        });

        // Copy Email
        $('#btnCopyEmail').on('click', function() {
            const email = $('#resEmail').text();
            navigator.clipboard.writeText(email).then(() => {
                const btn = $(this);
                const orig = btn.html();
                btn.html('<i class="bi bi-check-lg"></i> คัดลอกแล้ว!').addClass('btn-success').removeClass('btn-copy-custom');
                setTimeout(() => {
                    btn.html(orig).addClass('btn-copy-custom').removeClass('btn-success');
                }, 2000);
            });
        });

        // Copy Password
        $('#btnCopyPassword').on('click', function() {
            const pass = $('#resPassword').text();
            navigator.clipboard.writeText(pass).then(() => {
                const btn = $(this);
                const orig = btn.html();
                btn.html('<i class="bi bi-check-lg"></i> คัดลอกรหัสแล้ว!').addClass('btn-success').removeClass('btn-copy-custom');
                setTimeout(() => {
                    btn.html(orig).addClass('btn-copy-custom').removeClass('btn-success');
                }, 2000);
            });
        });
    </script>
</body>
</html>
