<!doctype html>

<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="<?= base_url('public/assets/sneat') ?>/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?= $this->renderSection('title') ?: esc($title ?? '') ?> | ระบบงานครู สกจ.9</title>

    <meta name="description"
        content="ระบบบริหารจัดการข้อมูลสำหรับครู โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์ ช่วยในการจัดการงานวิชาการ งานวัดผล และงานหลักสูตร" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <!-- Boxicons (required for Sneat template menu icons) -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/css/demo.css') ?>" />
    <link rel="stylesheet"
        href="<?= base_url('public/assets/sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/libs/apex-charts/apex-charts.css') ?>" />

    <!-- Helpers -->
    <script src="<?= base_url('public/assets/sneat/vendor/js/helpers.js') ?>"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?= base_url('public/assets/sneat/js/config.js') ?>"></script>

    <style>
        body,
        .menu-link,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        div,
        span,
        input,
        textarea,
        select,
        button,
        .form-control,
        .btn,
        .breadcrumb-item {
            font-family: 'K2D', sans-serif;

        }

        body {
            background-color: #f7f8fc !important;
            position: relative;
            min-height: 100vh;
        }

        #layout-menu {
            background-color: #ffffff !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3e%3cpath fill='%23696cff' fill-opacity='0.08' d='M0,160L48,176C96,192,192,224,288,213.3C384,203,480,149,576,133.3C672,117,768,139,864,165.3C960,192,1056,224,1152,218.7C1248,213,1344,171,1392,149.3L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3e%3c/path%3e%3c/svg%3e");
            background-position: bottom;
            background-repeat: no-repeat;
            background-size: 100%;
            z-index: 1080;
        }

        /* ─── Premium Loading State Styling ─── */
        .btn-loading {
            position: relative;
            transition: all 0.3s ease !important;
            pointer-events: none;
            opacity: 0.8;
            box-shadow: none !important;
        }

        .btn-loading .spinner-border {
            width: 1.1rem;
            height: 1.1rem;
            border-width: 0.15em;
        }

        @keyframes btn-pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(0.98);
                opacity: 0.9;
            }

            100% {
                transform: scale(1);
            }
        }

        .btn-loading:not(.disabled) {
            animation: btn-pulse 1.5s infinite ease-in-out;
        }

        /* ─── Global Top Progress Bar & Modern Page Loader ─── */
        .global-top-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3.5px;
            width: 0%;
            background: linear-gradient(90deg, #696cff 0%, #0284c7 50%, #38bdf8 100%);
            z-index: 9999999;
            box-shadow: 0 0 10px rgba(105, 108, 255, 0.8), 0 0 5px rgba(2, 132, 199, 0.6);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
            pointer-events: none;
            opacity: 0;
        }

        .global-top-progress.active {
            opacity: 1;
        }

        .global-page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(248, 250, 252, 0.78);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
            pointer-events: all;
        }

        .global-page-loader.active {
            opacity: 1;
            visibility: visible;
        }

        .global-page-loader .page-loader-card {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 20px 40px -10px rgba(30, 41, 59, 0.18), 0 0 0 1px rgba(105, 108, 255, 0.08);
            border-radius: 1.25rem;
            padding: 2rem 2.25rem;
            text-align: center;
            max-width: 320px;
            width: 90%;
            transform: scale(0.92);
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .global-page-loader.active .page-loader-card {
            transform: scale(1);
        }

        .loader-logo-wrapper {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 1.25rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader-logo-img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            z-index: 2;
            animation: loaderLogoPulse 2s ease-in-out infinite;
        }

        .loader-orbit-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #696cff;
            border-right-color: #696cff;
            animation: loaderOrbit 1.1s linear infinite;
        }

        .loader-orbit-ring-secondary {
            position: absolute;
            inset: 6px;
            border-radius: 50%;
            border: 2px solid transparent;
            border-bottom-color: #0284c7;
            border-left-color: #38bdf8;
            animation: loaderOrbitReverse 1.4s linear infinite;
        }

        @keyframes loaderOrbit {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes loaderOrbitReverse {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(-360deg);
            }
        }

        @keyframes loaderLogoPulse {

            0%,
            100% {
                transform: scale(1);
                filter: drop-shadow(0 2px 8px rgba(105, 108, 255, 0.2));
            }

            50% {
                transform: scale(1.08);
                filter: drop-shadow(0 4px 14px rgba(105, 108, 255, 0.45));
            }
        }

        .loader-spinner-track {
            width: 100%;
            height: 4px;
            background: #f1f5f9;
            border-radius: 4px;
            overflow: hidden;
            margin: 0.75rem 0 0.5rem 0;
            position: relative;
        }

        .loader-spinner-bar {
            width: 45%;
            height: 100%;
            background: linear-gradient(90deg, #696cff, #0284c7);
            border-radius: 4px;
            position: absolute;
            animation: loaderIndeterminate 1.5s ease-in-out infinite;
        }

        @keyframes loaderIndeterminate {
            0% {
                left: -45%;
            }

            100% {
                left: 100%;
            }
        }

        .loader-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -0.01em;
        }

        .loader-subtitle {
            font-size: 0.82rem;
            color: #64748b;
            line-height: 1.4;
        }

        /* ─── Ambient Animated Background (Modern Subtle Floating Aesthetic) ─── */
        .bg-ambient-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
            background: linear-gradient(135deg, #f8f9fe 0%, #f1f4fb 50%, #f6f8fd 100%);
        }

        .layout-wrapper {
            position: relative;
            z-index: 1;
            background: transparent !important;
        }

        /* Glowing Aurora Ambient Blobs */
        .ambient-orb {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            will-change: transform;
        }

        .ambient-orb-1 {
            width: 580px;
            height: 580px;
            top: -100px;
            left: 15%;
            background: radial-gradient(circle, rgba(105, 108, 255, 0.24) 0%, rgba(105, 108, 255, 0.05) 60%, transparent 100%);
            filter: blur(85px);
            animation: orbFloat1 22s ease-in-out infinite alternate;
        }

        .ambient-orb-2 {
            width: 520px;
            height: 520px;
            bottom: -60px;
            right: 10%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.20) 0%, rgba(56, 189, 248, 0.04) 60%, transparent 100%);
            filter: blur(90px);
            animation: orbFloat2 26s ease-in-out infinite alternate;
        }

        .ambient-orb-3 {
            width: 440px;
            height: 440px;
            top: 42%;
            left: 40%;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.16) 0%, rgba(217, 70, 239, 0.03) 60%, transparent 100%);
            filter: blur(80px);
            animation: orbFloat3 30s ease-in-out infinite alternate;
        }

        .ambient-orb-4 {
            width: 380px;
            height: 380px;
            top: 12%;
            right: 18%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.18) 0%, rgba(129, 140, 248, 0.03) 60%, transparent 100%);
            filter: blur(75px);
            animation: orbFloat4 20s ease-in-out infinite alternate;
        }

        @keyframes orbFloat1 {
            0% {
                transform: translate3d(0, 0, 0) scale(1);
            }

            50% {
                transform: translate3d(60px, 45px, 0) scale(1.12);
            }

            100% {
                transform: translate3d(-30px, 25px, 0) scale(0.95);
            }
        }

        @keyframes orbFloat2 {
            0% {
                transform: translate3d(0, 0, 0) scale(1);
            }

            50% {
                transform: translate3d(-55px, -50px, 0) scale(1.1);
            }

            100% {
                transform: translate3d(35px, -20px, 0) scale(0.92);
            }
        }

        @keyframes orbFloat3 {
            0% {
                transform: translate3d(0, 0, 0) scale(0.95);
            }

            50% {
                transform: translate3d(45px, -40px, 0) scale(1.08);
            }

            100% {
                transform: translate3d(-50px, 35px, 0) scale(1);
            }
        }

        @keyframes orbFloat4 {
            0% {
                transform: translate3d(0, 0, 0) scale(1.05);
            }

            50% {
                transform: translate3d(-40px, 50px, 0) scale(0.94);
            }

            100% {
                transform: translate3d(50px, -30px, 0) scale(1.08);
            }
        }

        /* Subtle Dot Grid Matrix */
        .ambient-grid-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(105, 108, 255, 0.09) 1.2px, transparent 1.2px);
            background-size: 32px 32px;
            mask-image: radial-gradient(ellipse at 50% 45%, black 30%, transparent 85%);
            -webkit-mask-image: radial-gradient(ellipse at 50% 45%, black 30%, transparent 85%);
            pointer-events: none;
            opacity: 0.75;
            animation: gridPulse 14s ease-in-out infinite alternate;
        }

        @keyframes gridPulse {
            0% {
                opacity: 0.55;
            }

            100% {
                opacity: 0.85;
            }
        }

        /* Floating Micro Elements (สไตล์มินิมอล สบายตา ดูเพลินๆ) */
        .ambient-shape {
            position: absolute;
            pointer-events: none;
            will-change: transform, opacity;
            opacity: 0.65;
        }

        .ambient-ring-1 {
            width: 48px;
            height: 48px;
            border: 1.8px solid rgba(105, 108, 255, 0.22);
            border-radius: 50%;
            top: 22%;
            left: 18%;
            animation: shapeMotion1 22s ease-in-out infinite;
        }

        .ambient-ring-2 {
            width: 34px;
            height: 34px;
            border: 1.5px dashed rgba(14, 165, 233, 0.26);
            border-radius: 50%;
            bottom: 24%;
            right: 16%;
            animation: shapeMotion2 26s ease-in-out infinite reverse;
        }

        .ambient-dot-1 {
            width: 10px;
            height: 10px;
            background: rgba(105, 108, 255, 0.35);
            border-radius: 50%;
            box-shadow: 0 0 14px rgba(105, 108, 255, 0.45);
            top: 55%;
            left: 14%;
            animation: shapeMotion3 18s ease-in-out infinite;
        }

        .ambient-dot-2 {
            width: 8px;
            height: 8px;
            background: rgba(56, 189, 248, 0.4);
            border-radius: 50%;
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.5);
            top: 18%;
            right: 28%;
            animation: shapeMotion4 20s ease-in-out infinite;
        }

        .ambient-diamond-1 {
            width: 22px;
            height: 22px;
            border: 1.6px solid rgba(168, 85, 247, 0.22);
            border-radius: 5px;
            bottom: 32%;
            left: 32%;
            animation: shapeMotion5 24s ease-in-out infinite;
        }

        .ambient-pill-1 {
            width: 44px;
            height: 18px;
            border: 1.5px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            top: 72%;
            right: 30%;
            animation: shapeMotion6 25s ease-in-out infinite;
        }

        @keyframes shapeMotion1 {

            0%,
            100% {
                transform: translate3d(0, 0, 0) rotate(0deg);
                opacity: 0.35;
            }

            50% {
                transform: translate3d(30px, -35px, 0) rotate(180deg);
                opacity: 0.75;
            }
        }

        @keyframes shapeMotion2 {

            0%,
            100% {
                transform: translate3d(0, 0, 0) rotate(0deg);
                opacity: 0.3;
            }

            50% {
                transform: translate3d(-25px, 30px, 0) rotate(-180deg);
                opacity: 0.7;
            }
        }

        @keyframes shapeMotion3 {

            0%,
            100% {
                transform: translate3d(0, 0, 0) scale(1);
                opacity: 0.28;
            }

            50% {
                transform: translate3d(-20px, -30px, 0) scale(1.3);
                opacity: 0.75;
            }
        }

        @keyframes shapeMotion4 {

            0%,
            100% {
                transform: translate3d(0, 0, 0) scale(1);
                opacity: 0.3;
            }

            50% {
                transform: translate3d(25px, 25px, 0) scale(1.25);
                opacity: 0.8;
            }
        }

        @keyframes shapeMotion5 {

            0%,
            100% {
                transform: translate3d(0, 0, 0) rotate(45deg);
                opacity: 0.35;
            }

            50% {
                transform: translate3d(20px, -28px, 0) rotate(225deg);
                opacity: 0.75;
            }
        }

        @keyframes shapeMotion6 {

            0%,
            100% {
                transform: translate3d(0, 0, 0) rotate(-25deg);
                opacity: 0.3;
            }

            50% {
                transform: translate3d(-30px, -20px, 0) rotate(65deg);
                opacity: 0.7;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .ambient-orb,
            .ambient-shape,
            .ambient-grid-pattern {
                animation: none !important;
            }
        }
    </style>

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.css' rel='stylesheet' />
    <!-- Flatpickr CSS & Thai Buddhist Era Theme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
    <style>
        .flatpickr-calendar {
            font-family: inherit;
            border-radius: 0.85rem !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
            border: 1px solid #e2e8f0 !important;
        }

        .flatpickr-calendar.hasTime .flatpickr-time {
            border-top: 1px solid #f1f5f9 !important;
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange {
            background: #6366f1 !important;
            border-color: #6366f1 !important;
        }

        .flatpickr-day:hover {
            background: #e0e7ff !important;
        }

        .flatpickr-current-month .cur-month {
            font-weight: 700 !important;
            color: #1e1b4b !important;
        }

        .flatpickr-year-be-select {
            font-size: 0.95rem;
            font-weight: 700;
            color: #4338ca;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 2px 8px;
            background: #ffffff;
            cursor: pointer;
            outline: none;
            margin-left: 6px;
        }

        .flatpickr-year-be-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .flatpickr-input[readonly] {
            background-color: #ffffff !important;
            cursor: pointer;
        }
    </style>

    <?= $this->renderSection('styles') ?>

</head>

<body>
    <!-- Global Top Progress Bar -->
    <div id="global-top-progress" class="global-top-progress"></div>

    <!-- Global Modern Page Loader Overlay -->
    <div id="global-page-loader" class="global-page-loader" aria-hidden="true">
        <div class="page-loader-card">
            <div class="loader-logo-wrapper">
                <div class="loader-orbit-ring"></div>
                <div class="loader-orbit-ring-secondary"></div>
                <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="Logo SKJ" class="loader-logo-img" />
            </div>
            <h6 class="loader-title mb-1" id="globalLoaderTitle">กำลังโหลดข้อมูล...</h6>
            <p class="loader-subtitle mb-3 text-muted" id="globalLoaderSubtitle">กรุณารอสักครู่
                ระบบกำลังจัดเตรียมหน้านี้</p>
            <div class="loader-spinner-track">
                <div class="loader-spinner-bar"></div>
            </div>
        </div>
    </div>

    <!-- Ambient Animated Background Canvas (Subtle & Relaxing Motion) -->
    <div class="bg-ambient-wrapper" aria-hidden="true">
        <!-- Floating Soft Aurora Blobs -->
        <div class="ambient-orb ambient-orb-1"></div>
        <div class="ambient-orb ambient-orb-2"></div>
        <div class="ambient-orb ambient-orb-3"></div>
        <div class="ambient-orb ambient-orb-4"></div>

        <!-- Subtle Dot Grid Matrix -->
        <div class="ambient-grid-pattern"></div>

        <!-- Floating Micro Elements -->
        <div class="ambient-shape ambient-ring-1"></div>
        <div class="ambient-shape ambient-ring-2"></div>
        <div class="ambient-shape ambient-dot-1"></div>
        <div class="ambient-shape ambient-dot-2"></div>
        <div class="ambient-shape ambient-diamond-1"></div>
        <div class="ambient-shape ambient-pill-1"></div>
    </div>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="<?= base_url() ?>" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="LogoSKJ_4"
                                style="height: 35px;" />
                        </span>
                        <span class="app-brand-text demo menu-text  ms-2">งานครู สกจ.9</span>
                    </a>

                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <?php
                $currentUri = service('uri');
                $segments = $currentUri->getSegments();

                // Helper: Check active menu link accurately
                if (!function_exists('is_active_segment')) {
                    function is_active_segment($expected_segments, $current_segments)
                    {
                        // Home page check (URL is '/' or '/home')
                        if (empty($expected_segments)) {
                            if (empty($current_segments) || (count($current_segments) === 1 && $current_segments[0] === 'home')) {
                                return 'active';
                            }
                            return '';
                        }

                        if (count($current_segments) < count($expected_segments)) {
                            return '';
                        }

                        for ($i = 0; $i < count($expected_segments); $i++) {
                            if ($expected_segments[$i] !== $current_segments[$i]) {
                                return '';
                            }
                        }
                        return 'active';
                    }
                }

                // Helper: Check parent menu open/active state
                if (!function_exists('is_open_segment')) {
                    function is_open_segment($expected_parent_segments, $current_segments)
                    {
                        foreach ($expected_parent_segments as $parent_segment_array) {
                            $match = true;
                            for ($i = 0; $i < count($parent_segment_array); $i++) {
                                if (!isset($current_segments[$i]) || $parent_segment_array[$i] !== $current_segments[$i]) {
                                    $match = false;
                                    break;
                                }
                            }
                            if ($match) {
                                return 'open active';
                            }
                        }
                        return '';
                    }
                }

                // Map URI paths to friendly Thai Breadcrumb names
                $breadcrumbMap = [
                    'assessment' => 'งานวัดผล',
                    'save-score-normal' => 'บันทึกผลการเรียน (ปกติ)',
                    'save-score-repeat' => 'บันทึกผลการเรียน (ซ้ำ)',
                    'save-score-add' => 'บันทึกผลการเรียน',
                    'curriculum' => 'งานหลักสูตร',
                    'send-plan' => 'ส่งแผนการสอน',
                    'download-plan' => 'ดาวน์โหลดแผนการสอน',
                    'research' => 'งานวิจัยในชั้นเรียน',
                    'teacher' => 'งานครู',
                    'reading_assessment' => 'แบบประเมินอ่านคิดวิเคราะห์',
                    'desirable_assessment' => 'คุณลักษณะอันพึงประสงค์',
                    'club' => 'งานพัฒนาผู้เรียน / บันทึกชุมนุม',
                    'scout' => 'งานพัฒนาผู้เรียน / บันทึกลูกเสือ',
                    'attendance' => 'SKJ Check-In',
                    'leave' => 'ระบบการลา',
                    'evaluation' => 'การประเมินผลการปฏิบัติงาน',
                    'pa-agreement' => 'การประเมินผลการพัฒนางานตามข้อตกลง (PA)',
                    'portfolio' => 'ประวัติการอบรมและผลงาน',
                    'assessment-head' => 'หัวหน้ากลุ่มสาระ',
                    'check-plan' => 'ตรวจแผนการสอน',
                    'check-score' => 'ตรวจสอบคะแนน',
                    'teaching-schedule' => 'จัดตารางสอนของกลุ่มสาระ',
                    'my' => 'ตารางสอนรายบุคคล'
                ];
                ?>
                <ul class="menu-inner py-1">
                    <li class="menu-item <?= is_active_segment([], $segments) ?>">
                        <a href="<?= base_url() ?>" class="menu-link">
                            <i class="menu-icon tf-icons bi-house-door-fill"></i>
                            <div data-i18n="หน้าหลัก">หน้าหลัก</div>
                        </a>
                    </li>
                    <li class="menu-header small text-uppercase"><span class="menu-header-text">งานวิชาการ</span></li>

                    <!-- งานวัดผล -->
                    <li class="menu-item <?= is_open_segment([['assessment']], $segments) ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bi-file-earmark-ruled-fill"></i>
                            <div data-i18n="งานวัดผล">งานวัดผล</div>
                        </a>
                        <ul class="menu-sub">
                            <li
                                class="menu-item <?= is_active_segment(['assessment', 'save-score-normal'], $segments) || is_active_segment(['assessment', 'save-score-add'], $segments) ? 'active' : '' ?>">
                                <a href="<?= base_url('assessment/save-score-normal') ?>" class="menu-link">
                                    <div data-i18n="บันทึกผลการเรียน(ปกติ)">บันทึกผลการเรียน(ปกติ)</div>
                                </a>
                            </li>
                            <li
                                class="menu-item <?= is_active_segment(['assessment', 'save-score-repeat'], $segments) || is_active_segment(['assessment', 'save-score-repeat-add'], $segments) ? 'active' : '' ?>">
                                <a href="<?= base_url('assessment/save-score-repeat') ?>" class="menu-link">
                                    <div data-i18n="บันทึกผลการเรียน(ซ้ำ)">บันทึกผลการเรียน(ซ้ำ)</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- งานหลักสูตร -->
                    <li
                        class="menu-item <?= is_open_segment([['curriculum'], ['research']], $segments) && !in_array('teaching-schedule', $segments) ? 'open active' : '' ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bi-book-fill"></i>
                            <div data-i18n="งานหลักสูตร">งานหลักสูตร</div>
                        </a>
                        <ul class="menu-sub">
                            <li
                                class="menu-item <?= is_active_segment(['curriculum'], $segments) && !in_array('download-plan', $segments) && !in_array('teaching-schedule', $segments) ? 'active' : '' ?>">
                                <a href="<?= base_url('curriculum') ?>" class="menu-link">
                                    <div data-i18n="ส่งแผนการสอน">ส่งแผนการสอน</div>
                                </a>
                            </li>
                            <li class="menu-item <?= is_active_segment(['curriculum', 'download-plan'], $segments) ?>">
                                <a href="<?= base_url('curriculum/download-plan') ?>" class="menu-link">
                                    <div data-i18n="ดาวโหลดแผนการสอน">ดาวโหลดแผนการสอน</div>
                                </a>
                            </li>
                            <li
                                class="menu-item <?= is_active_segment(['research'], $segments) && !in_array('load-research', $segments) && !in_array('setting', $segments) ? 'active' : '' ?>">
                                <a href="<?= base_url('research') ?>" class="menu-link">
                                    <div data-i18n="ส่งงานวิจัยในชั้นเรียน">ส่งงานวิจัยในชั้นเรียน</div>
                                </a>
                            </li>
                            <!-- <li class="menu-item <?= is_active_segment(['research', 'load-research'], $segments) ?>">
                                <a href="<?= base_url('research/load-research') ?>" class="menu-link">
                                    <div data-i18n="ดาวน์โหลดงานวิจัย">ดาวน์โหลดงานวิจัย</div>
                                </a>
                            </li> -->
                            <!-- <li class="menu-item <?= is_active_segment(['research', 'setting'], $segments) ?>">
                                <a href="<?= base_url('research/setting') ?>" class="menu-link">
                                    <div data-i18n="ตั้งค่าส่งงานวิจัย">ตั้งค่าส่งงานวิจัย</div>
                                </a>
                            </li> -->
                        </ul>
                    </li>

                    <!-- งานประเมินนักเรียน -->
                    <li
                        class="menu-item <?= is_open_segment([['teacher', 'reading_assessment'], ['teacher', 'desirable_assessment']], $segments) ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bi-clipboard-check"></i>
                            <div data-i18n="งานประเมินนักเรียน">งานประเมินนักเรียน</div>
                        </a>
                        <ul class="menu-sub">
                            <li
                                class="menu-item <?= is_active_segment(['teacher', 'reading_assessment'], $segments) ?>">
                                <a href="<?= base_url('teacher/reading_assessment') ?>" class="menu-link">
                                    <div data-i18n="แบบประเมินอ่านคิดวิเคราะห์">แบบประเมินอ่านคิดวิเคราะห์</div>
                                </a>
                            </li>
                            <li
                                class="menu-item <?= is_active_segment(['teacher', 'desirable_assessment'], $segments) ?>">
                                <a href="<?= base_url('teacher/desirable_assessment') ?>" class="menu-link">
                                    <div data-i18n="คุณลักษณะอันพึงประสงค์">คุณลักษณะอันพึงประสงค์</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- งานพัฒนาผู้เรียน -->
                    <li class="menu-item <?= is_open_segment([['club'], ['scout']], $segments) ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bi-person-arms-up"></i>
                            <div data-i18n="งานพัฒนาผู้เรียน">งานพัฒนาผู้เรียน</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item <?= is_active_segment(['club'], $segments) ?>">
                                <a href="<?= base_url('club') ?>" class="menu-link">
                                    <div data-i18n="บันทึกชุมนุม">บันทึกชุมนุม</div>
                                </a>
                            </li>
                            <li class="menu-item <?= is_active_segment(['scout'], $segments) ?>">
                                <a href="<?= base_url('scout') ?>" class="menu-link">
                                    <div data-i18n="บันทึกลูกเสือ">บันทึกลูกเสือ</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li
                        class="menu-item <?= is_open_segment([['leave'], ['evaluation'], ['pa-agreement'], ['generate-leave-form'], ['portfolio'], ['attendance']], $segments) ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bi-person-badge-fill"></i>
                            <div data-i18n="งานบุคลากร">งานบุคลากร</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item <?= is_active_segment(['attendance'], $segments) ?>">
                                <a href="<?= base_url('attendance') ?>" class="menu-link">
                                    <i class="bi bi-clock-history me-2"></i>
                                    <div data-i18n="SKJ Check-In">SKJ Check-In</div>
                                </a>
                            </li>
                            <li
                                class="menu-item <?= is_active_segment(['leave'], $segments) || is_active_segment(['generate-leave-form'], $segments) ? 'active' : '' ?>">
                                <a href="<?= base_url('leave') ?>" class="menu-link">
                                    <i class="bi bi-calendar-check me-2"></i>
                                    <div data-i18n="ระบบการลา">ระบบการลา</div>
                                </a>
                            </li>
                            <?php /* ปิดชั่วคราว - evaluation menu
                       <li class="menu-item <?= is_active_segment(['evaluation'], $segments) ? 'active' : '' ?>">
                           <a href="<?= base_url('evaluation') ?>" class="menu-link">
                               <i class="bi bi-file-earmark-pdf me-2"></i>
                               <div data-i18n="การประเมินผลการปฏิบัติงาน">การประเมินผลการปฏิบัติงาน</div>
                           </a>
                       </li>
                       */ ?>
                            <li class="menu-item <?= is_active_segment(['pa-agreement'], $segments) ? 'active' : '' ?>">
                                <a href="<?= base_url('pa-agreement') ?>" class="menu-link">
                                    <i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>
                                    <div data-i18n="การประเมินผลการพัฒนางานตามข้อตกลง (PA)">
                                        การประเมินผลการพัฒนางานตามข้อตกลง (PA)</div>
                                </a>
                            </li>
                            <li class="menu-item <?= is_active_segment(['portfolio'], $segments) ? 'active' : '' ?>">
                                <a href="<?= base_url('portfolio') ?>" class="menu-link">
                                    <i class="bi bi-person-workspace me-2"></i>
                                    <div data-i18n="ประวัติการอบรมและผลงาน">ประวัติการอบรมและผลงาน</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php if (session()->get('pers_groupleade') !== null && session()->get('pers_groupleade') !== ''): ?>
                        <li class="menu-header small text-uppercase"><span class="menu-header-text">หัวหน้ากลุ่มสาระ</span>
                        </li>
                        <li class="menu-item <?= is_open_segment([['assessment-head']], $segments) ?>">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons bi-shield-check"></i>
                                <div data-i18n="ตรวจงานกลุ่มสาระ">ตรวจงานกลุ่มสาระ</div>
                            </a>
                            <ul class="menu-sub">
                                <li
                                    class="menu-item <?= is_active_segment(['assessment-head', 'check-plan'], $segments) || is_active_segment(['assessment-head', 'check-plan-detail'], $segments) ? 'active' : '' ?>">
                                    <a href="<?= base_url('assessment-head/check-plan') ?>" class="menu-link">
                                        <div data-i18n="ตรวจแผนการสอน">ตรวจแผนการสอน</div>
                                    </a>
                                </li>
                                <li
                                    class="menu-item <?= is_active_segment(['assessment-head', 'check-score'], $segments) || is_active_segment(['assessment-head', 'check-score-detail'], $segments) || is_active_segment(['assessment-head', 'check-score-student'], $segments) ? 'active' : '' ?>">
                                    <a href="<?= base_url('assessment-head/check-score') ?>" class="menu-link">
                                        <div data-i18n="ตรวจสอบคะแนน">ตรวจสอบคะแนน</div>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- จัดตารางสอนของกลุ่มสาระ (เมนูใหญ่) -->
                        <li class="menu-item <?= is_open_segment([['curriculum', 'teaching-schedule']], $segments) ?>">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons bi-calendar3"></i>
                                <div data-i18n="จัดตารางสอนของกลุ่มสาระ">จัดตารางสอนของกลุ่มสาระ</div>
                            </a>
                            <ul class="menu-sub">
                                <li
                                    class="menu-item <?= is_active_segment(['curriculum', 'teaching-schedule'], $segments) && !in_array('my', $segments) && !in_array('teacher', $segments) ? 'active' : '' ?>">
                                    <a href="<?= base_url('curriculum/teaching-schedule') ?>" class="menu-link">
                                        <div data-i18n="ตารางสอนรวม">ตารางสอนรวม</div>
                                    </a>
                                </li>
                                <li
                                    class="menu-item <?= is_active_segment(['curriculum', 'teaching-schedule'], $segments) && (in_array('my', $segments) || in_array('teacher', $segments)) ? 'active' : '' ?>">
                                    <a href="<?= base_url('curriculum/teaching-schedule/my') ?>" class="menu-link">
                                        <div data-i18n="ตารางสอนรายบุคคล">ตารางสอนรายบุคคล</div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Global Quick Search -->
                        <div class="navbar-nav align-items-center me-auto position-relative"
                            style="max-width: 320px; width: 100%;">
                            <div
                                class="nav-item d-flex align-items-center w-100 bg-light rounded-pill px-3 py-1 border border-transparent focus-ring">
                                <i class="bx bx-search fs-4 lh-0 text-muted me-2"></i>
                                <input type="text" id="global-search-input"
                                    class="form-control border-0 shadow-none bg-transparent p-0"
                                    placeholder="ค้นหาเมนู, บันทึกคะแนน, แผนการสอน..." aria-label="Search..."
                                    autocomplete="off" />
                            </div>
                            <!-- Search Result Dropdown -->
                            <div id="search-results-dropdown"
                                class="dropdown-menu shadow-lg border-0 w-100 p-2 position-absolute start-0"
                                style="top: 105%; max-height: 350px; overflow-y: auto; display: none; z-index: 1090;">
                                <div class="text-muted small px-3 py-1 border-bottom fw-bold"><i
                                        class="bi bi-compass me-1"></i> เมนูและเครื่องมือที่ค้นพบ</div>
                                <div id="search-results-list" class="list-group list-group-flush mt-1"></div>
                            </div>
                        </div>
                        <!-- /Global Quick Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- User Info Badge on Top Navbar -->
                            <li class="nav-item me-3 d-none d-md-flex flex-column text-end">
                                <span class="fw-bold text-dark lh-1"
                                    style="font-size: 0.95rem;">ครู<?= esc(session()->get('fullname') ?? '') ?></span>
                                <small class="text-muted mt-1" style="font-size: 0.78rem;">
                                    <i
                                        class="bi bi-briefcase me-1 text-primary"></i><?= esc(session()->get('position') ?? 'ครูผู้สอน') ?>
                                </small>
                            </li>

                            <!-- User Dropdown -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= session()->get('person_img') ?>"
                                            alt class="w-px-40 h-auto rounded-circle border shadow-sm" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                                    <li>
                                        <a class="dropdown-item py-2" href="#">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= session()->get('person_img') ?>"
                                                            alt class="w-px-40 h-auto rounded-circle border" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span
                                                        class="fw-bold d-block text-dark">ครู<?= esc(session()->get('fullname')) ?></span>
                                                    <span class="badge bg-label-primary mt-1">
                                                        <i
                                                            class="bi bi-person-badge me-1"></i><?= esc(session()->get('position') ?? 'ครูผู้สอน') ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">My Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= base_url('logout') ?>">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-12">

                                <div class="card p-3 mb-4 shadow-sm border-0 bg-white rounded-3">
                                    <div
                                        class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                                        <h5 class="mb-0 text-primary fw-bold">
                                            <i class="bi bi-geo-alt-fill me-2"></i><?= esc($title ?? 'หน้าหลัก') ?>
                                        </h5>
                                        <nav aria-label="breadcrumb">
                                            <ol class="breadcrumb mb-0">
                                                <li class="breadcrumb-item">
                                                    <a href="<?= base_url() ?>" class="text-decoration-none">
                                                        <i class="bi bi-house-door me-1"></i>หน้าหลัก
                                                    </a>
                                                </li>
                                                <?php
                                                $builtPath = '';
                                                $totalSegs = count($segments);
                                                foreach ($segments as $idx => $seg) {
                                                    // Skip numeric parameters or room params for cleaner breadcrumbs
                                                    if (is_numeric($seg) || strpos($seg, '-') !== false)
                                                        continue;

                                                    $builtPath .= '/' . $seg;
                                                    $label = $breadcrumbMap[$seg] ?? ucfirst($seg);

                                                    if ($idx === $totalSegs - 1 || ($idx === $totalSegs - 2 && is_numeric($segments[$totalSegs - 1]))) {
                                                        echo '<li class="breadcrumb-item active" aria-current="page">' . esc($label) . '</li>';
                                                    } else {
                                                        echo '<li class="breadcrumb-item"><a href="' . base_url($builtPath) . '" class="text-decoration-none">' . esc($label) . '</a></li>';
                                                    }
                                                }
                                                ?>
                                            </ol>
                                        </nav>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <?= $this->renderSection('content') ?>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column gap-2">
                                <div class="mb-2 mb-md-0">
                                    <strong>ระบบบริหารจัดการข้อมูลสำหรับครู</strong> ©
                                    <script>document.write(new Date().getFullYear())</script>
                                    โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <?php
                                    $gitVer = function_exists('get_git_version') ? get_git_version() : null;
                                    if ($gitVer):
                                        ?>
                                        <span class="badge bg-label-primary px-2.5 py-1 d-inline-flex align-items-center"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="ระบบอัปเดตล่าสุด: <?= esc($gitVer['datetime']) ?>">
                                            <i class="bx bx-shield-quarter text-primary me-1"></i>
                                            <span class="fw-bold"><?= esc($gitVer['version']) ?></span>
                                            <span class="text-muted ms-1 fw-normal">(อัปเดต:
                                                <?= esc($gitVer['date']) ?>)</span>
                                        </span>
                                    <?php endif; ?>

                                    <div>
                                        พัฒนาโดย <a href="https://erc.nsnpao.go.th/itsupport/portfolio" target="_blank"
                                            class="footer-link fw-bolder">Dekpiano</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <!-- <div class="layout-overlay layout-menu-toggle"></div> -->
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="<?= base_url('public/assets/sneat/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= base_url('public/assets/sneat/vendor/libs/popper/popper.js') ?>"></script>
    <script src="<?= base_url('public/assets/sneat/vendor/js/bootstrap.js') ?>"></script>
    <script src="<?= base_url('public/assets/sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') ?>"></script>
    <script src="<?= base_url('public/assets/sneat/vendor/js/menu.js') ?>"></script>

    <!-- Vendors JS -->
    <script src="<?= base_url('public/assets/sneat/vendor/libs/apex-charts/apexcharts.js') ?>"></script>

    <!-- Main JS -->
    <script src="<?= base_url('public/assets/sneat/js/main.js') ?>"></script>

    <!-- Page JS -->
    <script src="<?= base_url('public/assets/sneat/js/dashboards-analytics.js') ?>"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js'></script>
    <!-- Flatpickr JS & Thai Localization -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>

    <script>
        // Global Thai Buddhist Era Date & Time Utilities
        window.thaiMonthsFull = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
        window.thaiMonthsShort = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

        window.formatThaiBE = function (date, format = 'short') {
            if (!date) return '';
            if (typeof date === 'string') {
                date = new Date(date);
            }
            if (isNaN(date.getTime())) return '';
            const d = String(date.getDate()).padStart(2, '0');
            const m = format === 'full' ? window.thaiMonthsFull[date.getMonth()] : window.thaiMonthsShort[date.getMonth()];
            const y = date.getFullYear() + 543;
            return `${d} ${m} ${y}`;
        };

        window.initThaiDatePickers = function (container = document) {
            if (typeof flatpickr === 'undefined') return;

            // 1. Date Only Pickers (.flatpickr-date, [data-thai-datepicker])
            const dateInputs = container.querySelectorAll ? container.querySelectorAll('.flatpickr-date:not(.flatpickr-input), [data-thai-datepicker]:not(.flatpickr-input)') : [];
            dateInputs.forEach(el => {
                if (el._flatpickr || el.classList.contains('flatpickr-input')) return; // already initialized
                flatpickr(el, {
                    locale: 'th',
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'j F Y',
                    altInputClass: 'form-control bg-white',
                    allowInput: true,
                    formatDate: function (date, format, locale) {
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();
                        if (format === 'Y-m-d') {
                            return `${year}-${month}-${day}`;
                        }
                        const monthShort = window.thaiMonthsShort[date.getMonth()];
                        const yearBE = year + 543;
                        return `${day} ${monthShort} ${yearBE}`;
                    },
                    parseDate: function (dateStr, format) {
                        if (!dateStr) return null;
                        if (/^\d{4}-\d{2}-\d{2}/.test(dateStr)) return new Date(dateStr);
                        const parts = dateStr.split(/[\/\-]/);
                        if (parts.length === 3) {
                            let d = parseInt(parts[0], 10);
                            let m = parseInt(parts[1], 10) - 1;
                            let y = parseInt(parts[2], 10);
                            if (y > 2400) y -= 543;
                            return new Date(y, m, d);
                        }
                        return new Date(dateStr);
                    },
                    onReady: function (selectedDates, dateStr, instance) {
                        renderThaiYearDropdown(instance);
                        instance.config.onMonthChange.push(() => renderThaiYearDropdown(instance));
                        instance.config.onYearChange.push(() => renderThaiYearDropdown(instance));
                    }
                });
            });

            // 2. Time Pickers (.flatpickr-time, [data-thai-timepicker])
            const timeInputs = container.querySelectorAll ? container.querySelectorAll('.flatpickr-time, [data-thai-timepicker]') : [];
            timeInputs.forEach(el => {
                if (el._flatpickr) return;
                flatpickr(el, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    minuteIncrement: 5,
                    allowInput: true
                });
            });

            // 3. Date & Time Pickers (.flatpickr-datetime, [data-thai-datetimepicker])
            const datetimeInputs = container.querySelectorAll ? container.querySelectorAll('.flatpickr-datetime:not(.flatpickr-input), [data-thai-datetimepicker]:not(.flatpickr-input)') : [];
            datetimeInputs.forEach(el => {
                if (el._flatpickr || el.classList.contains('flatpickr-input')) return;
                flatpickr(el, {
                    locale: 'th',
                    enableTime: true,
                    time_24hr: true,
                    dateFormat: 'Y-m-d H:i',
                    altInput: true,
                    altFormat: 'j F Y H:i',
                    altInputClass: 'form-control bg-white',
                    allowInput: true,
                    formatDate: function (date, format, locale) {
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();
                        const hours = String(date.getHours()).padStart(2, '0');
                        const mins = String(date.getMinutes()).padStart(2, '0');
                        if (format === 'Y-m-d H:i') {
                            return `${year}-${month}-${day} ${hours}:${mins}`;
                        }
                        const monthShort = window.thaiMonthsShort[date.getMonth()];
                        const yearBE = year + 543;
                        return `${day} ${monthShort} ${yearBE} ${hours}:${mins} น.`;
                    },
                    parseDate: function (dateStr, format) {
                        if (!dateStr) return null;
                        if (/^\d{4}-\d{2}-\d{2}/.test(dateStr)) return new Date(dateStr);
                        return new Date(dateStr);
                    },
                    onReady: function (selectedDates, dateStr, instance) {
                        renderThaiYearDropdown(instance);
                        instance.config.onMonthChange.push(() => renderThaiYearDropdown(instance));
                        instance.config.onYearChange.push(() => renderThaiYearDropdown(instance));
                    }
                });
            });

            function renderThaiYearDropdown(instance) {
                if (!instance || !instance.calendarContainer) return;
                const currentYearAD = instance.currentYear;
                const container = instance.calendarContainer;
                const numInputWrapper = container.querySelector('.numInputWrapper');

                const baseYear = new Date().getFullYear();
                let optionsHtml = '';
                for (let y = baseYear - 5; y <= baseYear + 5; y++) {
                    const yBE = y + 543;
                    const isSelected = (y === currentYearAD) ? 'selected' : '';
                    optionsHtml += `<option value="${y}" ${isSelected}>${yBE}</option>`;
                }

                if (numInputWrapper) {
                    numInputWrapper.style.display = 'none';
                }

                let yearSelect = container.querySelector('.flatpickr-year-be-select');
                const curMonthEl = container.querySelector('.flatpickr-current-month');
                if (!curMonthEl) return;

                if (!yearSelect) {
                    yearSelect = document.createElement('select');
                    yearSelect.className = 'flatpickr-year-be-select';
                    yearSelect.setAttribute('aria-label', 'เลือกปี พ.ศ.');
                    yearSelect.innerHTML = optionsHtml;
                    curMonthEl.appendChild(yearSelect);

                    yearSelect.addEventListener('change', function (e) {
                        e.stopPropagation();
                        const chosenYearAD = parseInt(this.value, 10);
                        instance.changeYear(chosenYearAD);
                    });
                } else {
                    yearSelect.innerHTML = optionsHtml;
                    yearSelect.value = currentYearAD;
                }
            }
        };

        $(function () {
            window.initThaiDatePickers(document);
            $(document).on('shown.bs.modal', function (e) {
                window.initThaiDatePickers(e.target);
            });
        });
    </script>

    <?= $this->renderSection('scripts') ?>

    <script>
        $(function () {
            // Global file availability check for curriculum downloads/views
            $(document).on('click', 'a[href*="download-plan-file"]', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                const $link = $(this);
                const href = $link.attr('href');
                if (!href || href === '#' || href.startsWith('javascript:')) return;

                // Extract ID from URL
                const id = href.substring(href.lastIndexOf('/') + 1);

                Swal.fire({
                    title: 'กำลังตรวจสอบไฟล์...',
                    text: 'กรุณารอสักครู่',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `<?= site_url('curriculum/check-file-exists/') ?>${id}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        Swal.close();
                        if (response.status === 'success') {
                            window.open(href, '_blank');
                        } else {
                            let htmlContent = `<div class="text-start">`;
                            htmlContent += `<p class="mb-2"><strong>สาเหตุ:</strong> ${response.message || 'ไม่ทราบสาเหตุ'}</p>`;

                            if (response.reason) {
                                const reasonMap = {
                                    'NO_ID': '❌ ไม่มี Plan ID ส่งมา',
                                    'NO_RECORD': '🗑️ ข้อมูลแผนถูกลบออกจากฐานข้อมูลแล้ว',
                                    'NO_FILE_UPLOADED': '📁 ครูส่งแผนแล้ว แต่ยังไม่ได้อัปโหลดไฟล์เข้ามา',
                                    'FILE_NOT_ON_SERVER': '🌐 มีชื่อไฟล์ในฐานข้อมูล แต่ไฟล์ไม่อยู่บน Server',
                                    'NO_CONFIG': '⚙️ ระบบยังไม่ได้ตั้งค่า'
                                };
                                htmlContent += `<p class="mb-2 text-muted small"><strong>ประเภท:</strong> ${reasonMap[response.reason] || response.reason}</p>`;
                            }

                            if (response.debug) {
                                htmlContent += `<hr><details class="small"><summary class="fw-bold text-secondary">🔍 ข้อมูล Debug (สำหรับ Admin)</summary>`;
                                htmlContent += `<div class="mt-2 p-2 bg-light rounded text-start" style="font-size:0.78rem; word-break:break-all;">`;
                                htmlContent += `<div><strong>Plan ID:</strong> ${response.debug.seplan_ID}</div>`;
                                htmlContent += `<div><strong>ชื่อไฟล์ใน DB:</strong> ${response.debug.seplan_file || '<span class=text-danger>ว่าง</span>'}</div>`;
                                htmlContent += `<div><strong>วิชา:</strong> ${response.debug.seplan_namesubject}</div>`;
                                htmlContent += `<div><strong>ประเภทแผน:</strong> ${response.debug.seplan_typeplan}</div>`;
                                htmlContent += `<div><strong>ครูผู้ส่ง:</strong> ${response.debug.seplan_usersend}</div>`;
                                htmlContent += `<div><strong>HTTP Status:</strong> ${response.debug.http_status}</div>`;
                                htmlContent += `<div class="mt-1"><strong>URL:</strong><br><a href="${response.debug.file_url}" target="_blank" class="text-primary">${response.debug.file_url}</a></div>`;
                                htmlContent += `</div></details>`;
                            }
                            htmlContent += `</div>`;

                            Swal.fire({
                                icon: 'error',
                                title: 'ไม่พบไฟล์',
                                html: htmlContent,
                                confirmButtonColor: '#696cff',
                                width: '600px'
                            });
                        }
                    },
                    error: function (jqXHR) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'เชื่อมต่อระบบไม่ได้',
                            html: `<p>ไม่สามารถเชื่อมต่อระบบตรวจสอบไฟล์ได้</p><p class="small text-muted">HTTP ${jqXHR.status}: ${jqXHR.statusText}</p>`,
                            confirmButtonColor: '#696cff'
                        });
                    }
                });
            });

            // ─── Global Submit & Search Button Loading System ───

            // Capture original button HTML on mousedown/click before any custom script modifies it
            $(document).on('mousedown click', 'button, .btn, input[type="submit"]', function () {
                const $btn = $(this);
                if (!$btn.data('original-text') && !$btn.find('.spinner-border, .spinner-grow').length) {
                    $btn.data('original-text', $btn.html());
                }
            });

            // Global helper to restore button(s) from loading/disabled state
            window.resetButtonLoading = function (target) {
                const $scope = target ? $(target) : $(document);
                const $elements = $scope.find('.btn-loading, button:disabled, input[type="submit"]:disabled, .btn[disabled]').addBack('.btn-loading, button:disabled, input[type="submit"]:disabled, .btn[disabled]');

                $elements.each(function () {
                    const $btn = $(this);
                    // Don't enable buttons that are permanently disabled by server permissions
                    if ($btn.hasClass('perm-disabled') || $btn.attr('data-permanent-disabled')) return;

                    const originalText = $btn.data('original-text');
                    if (originalText !== undefined && originalText !== null) {
                        $btn.html(originalText);
                    } else if ($btn.find('.spinner-border, .spinner-grow').length > 0) {
                        // Fallback: If button has a spinner but original-text was lost by custom script, clean up spinner
                        $btn.find('.spinner-border, .spinner-grow').remove();
                        let currentHtml = $btn.html()
                            .replace(/กำลังบันทึก\.\.\.?/g, 'บันทึกข้อมูล')
                            .replace(/กำลังอัปโหลด\.\.\.?/g, 'อัปโหลด')
                            .replace(/กำลังดำเนินการ\.\.\.?/g, 'ตกลง')
                            .replace(/กำลังค้นหา\.\.\.?/g, 'ค้นหา')
                            .replace(/กำลังโหลด\.\.\.?/g, 'โหลดข้อมูล')
                            .trim();
                        $btn.html(currentHtml);
                    }
                    $btn.prop('disabled', false).removeClass('btn-loading disabled');
                    $btn.css('min-width', '');
                    $btn.removeData('original-text');
                    $btn.removeData('ajax-loading');
                });
            };

            // Function to apply loading state to a button
            function applyLoading($btn, text = 'กำลังดำเนินการ...') {
                if ($btn.hasClass('btn-loading') || $btn.hasClass('no-loader')) return;

                if (!$btn.data('original-text')) {
                    $btn.data('original-text', $btn.html());
                }
                $btn.data('original-width', $btn.outerWidth());
                $btn.css('min-width', $btn.outerWidth() + 'px');

                const isLink = $btn.is('a');
                if (isLink) {
                    $btn.addClass('btn-loading disabled');
                } else {
                    $btn.prop('disabled', true).addClass('btn-loading');
                }

                $btn.html(`<span class="spinner-border spinner-border-sm me-2" role="status"></span> ${text}`);

                // Auto-safety timer per button: never stay stuck in loading more than 8 seconds
                setTimeout(() => {
                    if ($btn.hasClass('btn-loading')) {
                        window.resetButtonLoading($btn);
                    }
                }, 8000);
            }

            // ─── Global Modern Page Loader System ───
            let pageLoaderSafetyTimer = null;
            let topProgressTimer = null;

            window.showPageLoader = function (title = 'กำลังโหลดข้อมูล...', subtitle = 'กรุณารอสักครู่ ระบบกำลังจัดเตรียมหน้านี้') {
                const $loader = $('#global-page-loader');
                const $progress = $('#global-top-progress');

                if (title) $('#globalLoaderTitle').text(title);
                if (subtitle) $('#globalLoaderSubtitle').text(subtitle);

                // Start top progress bar animation
                if (topProgressTimer) clearInterval(topProgressTimer);
                $progress.addClass('active').css({ width: '25%', opacity: '1' });

                let currentWidth = 25;
                topProgressTimer = setInterval(() => {
                    if (currentWidth < 88) {
                        currentWidth += Math.random() * 12;
                        if (currentWidth > 88) currentWidth = 88;
                        $progress.css('width', currentWidth + '%');
                    }
                }, 200);

                $loader.addClass('active');

                // Safety timeout: auto-hide after 10 seconds in case navigation is interrupted/cancelled
                if (pageLoaderSafetyTimer) clearTimeout(pageLoaderSafetyTimer);
                pageLoaderSafetyTimer = setTimeout(function () {
                    window.hidePageLoader();
                }, 10000);
            };

            window.hidePageLoader = function () {
                if (pageLoaderSafetyTimer) {
                    clearTimeout(pageLoaderSafetyTimer);
                    pageLoaderSafetyTimer = null;
                }
                if (topProgressTimer) {
                    clearInterval(topProgressTimer);
                    topProgressTimer = null;
                }

                const $progress = $('#global-top-progress');
                $progress.css('width', '100%');

                setTimeout(() => {
                    $progress.css('opacity', '0');
                    setTimeout(() => {
                        $progress.removeClass('active').css('width', '0%');
                    }, 300);
                }, 180);

                $('#global-page-loader').removeClass('active');
            };

            // Auto-hide page loader on ready and window load
            setTimeout(() => window.hidePageLoader(), 100);
            $(window).on('load', function () {
                window.hidePageLoader();
            });

            // 1) Form Submit Interception
            $(document).on('submit', 'form', function (e) {
                const form = this;
                const $form = $(this);
                if ($form.hasClass('no-loader') || $form.attr('data-no-loader')) return;

                // Check HTML5 validation first: if any field is invalid, DO NOT lock button or page
                if (form.checkValidity && !form.checkValidity()) {
                    window.resetButtonLoading(form);
                    window.hidePageLoader();
                    return;
                }

                // If event was default-prevented by JS (custom validation or AJAX handler), DO NOT lock submit buttons!
                if (e.isDefaultPrevented()) {
                    window.resetButtonLoading(form);
                    window.hidePageLoader();
                    return;
                }

                const $submitBtn = $form.find('button[type="submit"], input[type="submit"]').not(':disabled');
                $submitBtn.each(function () {
                    applyLoading($(this), 'กำลังบันทึก...');
                });

                // Show page loader for full-page form submissions
                window.showPageLoader('กำลังบันทึกข้อมูล...', 'กรุณารอสักครู่ ระบบกำลังประมวลผลข้อมูล');
            });

            // If browser HTML5 validation marks an input invalid, restore buttons & loader immediately
            document.addEventListener('invalid', function (e) {
                const form = e.target.closest('form');
                if (form) {
                    window.resetButtonLoading(form);
                    window.hidePageLoader();
                }
            }, true);

            // Auto-unlock button and loader whenever user focuses, types, or edits form fields
            $(document).on('input change focusin keydown', 'form input, form select, form textarea', function () {
                const form = this.closest('form');
                window.resetButtonLoading(form || document);
                window.hidePageLoader();
            });

            // Auto-restore button when SweetAlert shows or closes
            if (window.Swal && typeof window.Swal.fire === 'function') {
                const _origSwalFire = window.Swal.fire;
                window.Swal.fire = function (...args) {
                    let shouldReset = false;
                    if (args.length > 0) {
                        const first = args[0];
                        if (typeof first === 'object' && first !== null) {
                            if (['warning', 'error', 'info', 'question'].includes(first.icon)) {
                                shouldReset = true;
                            }
                        } else if (typeof args[2] === 'string') {
                            if (['warning', 'error', 'info', 'question'].includes(args[2])) {
                                shouldReset = true;
                            }
                        }
                    }
                    if (shouldReset) {
                        window.resetButtonLoading();
                        window.hidePageLoader();
                    }
                    const res = _origSwalFire.apply(this, args);
                    if (res && typeof res.then === 'function') {
                        res.then((val) => {
                            if (val && !val.isConfirmed) {
                                window.resetButtonLoading();
                                window.hidePageLoader();
                            }
                        });
                    }
                    return res;
                };
            }

            // 2) General Search Buttons (Detected by ID, text, or icon)
            $(document).on('click', 'button, .btn', function (e) {
                const $btn = $(this);
                const text = $btn.text().trim();
                const id = $btn.attr('id') || '';
                const hasSearchIcon = $btn.find('.bi-search, .bx-search, .bi-person-search').length > 0;

                // Target search buttons
                if (id.toLowerCase().includes('search') || text.includes('ค้นหา') || hasSearchIcon) {
                    if (!$btn.attr('data-bs-toggle') && !$btn.hasClass('no-loader')) {
                        if ($btn.attr('type') !== 'submit') {
                            applyLoading($btn, 'กำลังค้นหา...');
                        }
                    }
                }
            });

            // 3) Global Link & Page Navigation Interception
            $(document).on('click', 'a[href]', function (e) {
                // Ignore if not primary mouse click or if modifier keys pressed (Ctrl/Cmd/Shift/Alt for new tab)
                if (e.which !== 1 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;

                const $link = $(this);
                const href = $link.attr('href');

                if (!href) return;
                const trimmedHref = href.trim();
                if (trimmedHref === '' || trimmedHref === '#' || trimmedHref.startsWith('javascript:')
                    || trimmedHref.startsWith('mailto:') || trimmedHref.startsWith('tel:')) return;

                // Ignore links opening in new tab/target
                if ($link.attr('target') === '_blank') return;

                // Ignore elements with no-loader or download attributes
                if ($link.hasClass('no-loader') || $link.hasClass('btn-download') || $link.attr('download') !== undefined) return;

                // Ignore Bootstrap toggles (modal, collapse, dropdown, tab, offcanvas, pill)
                if ($link.attr('data-bs-toggle') || $link.attr('data-toggle')) return;

                // Ignore internal anchor jumps on current page
                try {
                    const targetUrl = new URL($link.prop('href'), window.location.href);
                    if (targetUrl.origin === window.location.origin &&
                        targetUrl.pathname === window.location.pathname &&
                        targetUrl.search === window.location.search &&
                        targetUrl.hash) {
                        return;
                    }
                } catch (err) { }

                // If event was already prevented by a previous handler
                if (e.isDefaultPrevented()) return;

                // Ignore direct file downloads
                const cleanUrl = trimmedHref.split('?')[0].toLowerCase();
                const downloadExts = ['.pdf', '.xlsx', '.xls', '.docx', '.doc', '.zip', '.rar', '.csv', '.png', '.jpg', '.jpeg'];
                if (downloadExts.some(ext => cleanUrl.endsWith(ext))) return;

                // Button loading if it's a styled button
                if ($link.hasClass('btn') && !$link.hasClass('disabled')) {
                    applyLoading($link, 'กำลังโหลด...');
                }

                // Activate global page loader
                window.showPageLoader('กำลังโหลดข้อมูล...', 'กรุณารอสักครู่ ระบบกำลังจัดเตรียมหน้านี้');
            });

            // 4) AJAX Global Handler
            $(document).ajaxSend(function (event, jqxhr, settings) {
                const $activeBtn = $(document.activeElement);
                if (($activeBtn.is('button') || $activeBtn.hasClass('btn')) && !$activeBtn.hasClass('no-loader') && !$activeBtn.data('ajax-loading')) {
                    applyLoading($activeBtn, 'กำลังประมวลผล...');
                    $activeBtn.data('ajax-loading', true);
                    jqxhr._loadingBtn = $activeBtn;
                }
            });

            $(document).ajaxComplete(function (event, jqxhr) {
                if (jqxhr._loadingBtn) {
                    const $btn = jqxhr._loadingBtn;
                    $btn.prop('disabled', false).removeClass('btn-loading disabled');
                    if ($btn.data('original-text')) {
                        $btn.html($btn.data('original-text'));
                    }
                    $btn.removeData('ajax-loading');
                    $btn.removeData('original-text');
                    $btn.css('min-width', '');
                }
                // Safety: Restore any remaining btn-loading elements
                $('.btn-loading[data-ajax-loading]').each(function () {
                    const $btn = $(this);
                    $btn.prop('disabled', false).removeClass('btn-loading disabled');
                    if ($btn.data('original-text')) {
                        $btn.html($btn.data('original-text'));
                    }
                    $btn.removeData('ajax-loading');
                    $btn.removeData('original-text');
                    $btn.css('min-width', '');
                });
            });

            // 5) Safety: Restore buttons and hide loader on pageshow, modal show/hide, and sweetalert close
            $(window).on('pageshow', function () {
                window.resetButtonLoading();
                window.hidePageLoader();
            });

            $(document).on('shown.bs.modal hidden.bs.modal', function (e) {
                window.resetButtonLoading(e.target);
                window.hidePageLoader();
            });

            $(document).on('click', '.swal2-confirm, .swal2-cancel, .swal2-close, .swal2-deny', function () {
                setTimeout(() => {
                    window.resetButtonLoading();
                    window.hidePageLoader();
                }, 50);
            });

            // ─── Global Live Quick Menu Search Logic ───
            const searchableItems = [
                { title: 'หน้าหลักระบบ', desc: 'หน้าแรกของระบบครู สกจ.9', icon: 'bi-house-door-fill text-primary', url: '<?= base_url() ?>' },
                { title: 'บันทึกผลการเรียน (ปกติ)', desc: 'บันทึกคะแนนสอบ และเวลาเรียนวิชาปกติ', icon: 'bi-file-earmark-ruled-fill text-info', url: '<?= base_url("assessment/save-score-normal") ?>' },
                { title: 'บันทึกผลการเรียน (เรียนซ้ำ)', desc: 'บันทึกคะแนนนักเรียนลงเรียนซ้ำ', icon: 'bi-arrow-repeat text-warning', url: '<?= base_url("assessment/save-score-repeat") ?>' },
                { title: 'ส่งแผนการสอน', desc: 'อัปโหลดและจัดการเอกสารแผนการจัดการเรียนรู้', icon: 'bi-book-fill text-success', url: '<?= base_url("curriculum") ?>' },
                { title: 'ดาวน์โหลดแผนการสอน', desc: 'ดาวน์โหลดแผนการสอนของครูในโรงเรียน', icon: 'bi-download text-primary', url: '<?= base_url("curriculum/download-plan") ?>' },
                { title: 'ส่งงานวิจัยในชั้นเรียน', desc: 'อัปโหลดและจัดการไฟล์รายงานงานวิจัย', icon: 'bi-journal-bookmark-fill text-danger', url: '<?= base_url("research") ?>' },
                { title: 'แบบประเมินอ่านคิดวิเคราะห์', desc: 'ประเมินความสามารถด้านการอ่าน คิด วิเคราะห์', icon: 'bi-clipboard-check text-info', url: '<?= base_url("teacher/reading_assessment") ?>' },
                { title: 'คุณลักษณะอันพึงประสงค์', desc: 'ประเมินคุณลักษณะ 8 ประการของนักเรียน', icon: 'bi-star-fill text-warning', url: '<?= base_url("teacher/desirable_assessment") ?>' },
                { title: 'บันทึกชุมนุม', desc: 'จัดการกิจกรรม เช็คชื่อ และบันทึกผลชุมนุม', icon: 'bi-person-arms-up text-purple', url: '<?= base_url("club") ?>' },
                { title: 'SKJ Check-In (เช็คชื่อเข้างาน)', desc: 'ระบบลงเวลาเข้า-ออกงานบุคลากร', icon: 'bi-clock-history text-success', url: '<?= base_url("attendance") ?>' },
                { title: 'ระบบการลา', desc: 'ยื่นใบลา และตรวจสอบประวัติการลา', icon: 'bi-calendar-check text-primary', url: '<?= base_url("leave") ?>' },
                // ปิดชั่วคราว - evaluation search
                // { title: 'ประเมินผลการปฏิบัติงาน (PA)', desc: 'ส่งและติดตามผลการประเมิน PA', icon: 'bi-file-earmark-pdf text-danger', url: '<?= base_url("evaluation") ?>' },
                { title: 'ประวัติการอบรมและผลงาน', desc: 'บันทึก Portfolio และเกียรติบัตร', icon: 'bi-person-workspace text-info', url: '<?= base_url("portfolio") ?>' },
                <?php if (session()->get('pers_groupleade') !== null && session()->get('pers_groupleade') !== ''): ?>
                    { title: 'ตรวจแผนการสอน (หัวหน้าหมวด)', desc: 'อนุมัติและตรวจแผนการสอนของครูในกลุ่มสาระ', icon: 'bi-shield-check text-danger', url: '<?= base_url("assessment-head/check-plan") ?>' },
                    { title: 'ตรวจสอบคะแนน (หัวหน้าหมวด)', desc: 'ตรวจสอบการบันทึกคะแนนครูในกลุ่มสาระ', icon: 'bi-patch-check-fill text-success', url: '<?= base_url("assessment-head/check-score") ?>' },
                <?php endif; ?>
            ];

            const $searchInput = $('#global-search-input');
            const $searchDropdown = $('#search-results-dropdown');
            const $searchList = $('#search-results-list');

            $searchInput.on('input focus', function () {
                const query = $(this).val().trim().toLowerCase();
                if (query.length === 0) {
                    $searchDropdown.hide();
                    return;
                }

                const filtered = searchableItems.filter(item =>
                    item.title.toLowerCase().includes(query) ||
                    item.desc.toLowerCase().includes(query)
                );

                if (filtered.length > 0) {
                    let html = '';
                    filtered.forEach(item => {
                        html += `
                            <a href="${item.url}" class="list-group-item list-group-item-action border-0 rounded-2 p-2 mb-1 d-flex align-items-center">
                                <div class="avatar avatar-sm bg-label-secondary rounded me-2 d-flex align-items-center justify-content-center">
                                    <i class="bi ${item.icon} fs-5"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-bold text-dark text-truncate" style="font-size:0.88rem;">${item.title}</div>
                                    <small class="text-muted d-block text-truncate" style="font-size:0.75rem;">${item.desc}</small>
                                </div>
                                <i class="bi bi-chevron-right text-muted small ms-2"></i>
                            </a>
                        `;
                    });
                    $searchList.html(html);
                    $searchDropdown.show();
                } else {
                    $searchList.html(`
                        <div class="text-center py-3 text-muted small">
                            <i class="bi bi-search display-6 d-block mb-1 opacity-50"></i>
                            ไม่พบเมนูหรือเครื่องมือที่ตรงกับ "${query}"
                        </div>
                    `);
                    $searchDropdown.show();
                }
            });

            // Close search dropdown on click outside
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.navbar-nav.me-auto').length) {
                    $searchDropdown.hide();
                }
            });

            <?php if (session()->getFlashdata('error_gov_teacher_only')): ?>
                Swal.fire({
                    icon: 'warning',
                    title: 'สงวนสิทธิ์เฉพาะข้าราชการครู',
                    html: `
                        <div class="text-center py-2">
                            <i class="bi bi-shield-lock-fill text-warning display-4 d-block mb-3"></i>
                            <p class="mb-2 fs-6 fw-semibold text-dark">
                                <?= session()->getFlashdata('error_gov_teacher_only') ?>
                            </p>
                            <p class="small text-muted mb-0">
                                ผู้ใช้งานตำแหน่งครูอัตราจ้าง / เจ้าหน้าที่ / บุคลากรอื่น ๆ ไม่จำเป็นต้องส่งแบบประเมินในส่วนนี้
                            </p>
                        </div>
                    `,
                    confirmButtonText: '<i class="bi bi-check-lg me-1"></i> รับทราบ',
                    confirmButtonColor: '#696cff',
                    customClass: {
                        popup: 'rounded-4 shadow-lg p-4'
                    }
                });
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: '<?= session()->getFlashdata('success') ?>',
                    showConfirmButton: false,
                    timer: 1500
                });
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด!',
                    text: '<?= session()->getFlashdata('error') ?>'
                });
            <?php endif; ?>

            // ─── Initialize Bootstrap Tooltips Globally ───
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });


    </script>

</body>

</html>