<!doctype html>

<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="<?= base_url('public/assets/sneat') ?>/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?= $this->renderSection('title') ?> | ระบบงานครู สกจ.9</title>

    <meta name="description" content="ระบบบริหารจัดการข้อมูลสำหรับครู โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์ ช่วยในการจัดการงานวิชาการ งานวัดผล และงานหลักสูตร" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <!-- Boxicons (required for Sneat template menu icons) -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/css/demo.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />
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
        body{
            background-color: #696cff26 !important;
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
            0% { transform: scale(1); }
            50% { transform: scale(0.98); opacity: 0.9; }
            100% { transform: scale(1); }
        }

        .btn-loading:not(.disabled) {
            animation: btn-pulse 1.5s infinite ease-in-out;
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

</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="<?= base_url() ?>" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="LogoSKJ_4" style="height: 35px;" />
                        </span>
                        <span class="app-brand-text demo menu-text  ms-2">งานครู สกจ.9</span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

<?php
    $currentUri = service('uri');
    $segments = $currentUri->getSegments();

    // Helper: Check active menu link accurately
    function is_active_segment($expected_segments, $current_segments) {
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

    // Helper: Check parent menu open/active state
    function is_open_segment($expected_parent_segments, $current_segments) {
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
        'attendance' => 'SKJ Check-In',
        'leave' => 'ระบบการลา',
        'evaluation' => 'ประเมินผลการปฏิบัติงาน (PA)',
        'portfolio' => 'ประวัติการอบรมและผลงาน',
        'assessment-head' => 'หัวหน้ากลุ่มสาระ',
        'check-plan' => 'ตรวจแผนการสอน',
        'check-score' => 'ตรวจสอบคะแนน'
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
                            <li class="menu-item <?= is_active_segment(['assessment', 'save-score-normal'], $segments) || is_active_segment(['assessment', 'save-score-add'], $segments) ? 'active' : '' ?>">
                                <a href="<?= base_url('assessment/save-score-normal') ?>" class="menu-link">
                                    <div data-i18n="บันทึกผลการเรียน(ปกติ)">บันทึกผลการเรียน(ปกติ)</div>
                                </a>
                            </li>
                            <li class="menu-item <?= is_active_segment(['assessment', 'save-score-repeat'], $segments) || is_active_segment(['assessment', 'save-score-repeat-add'], $segments) ? 'active' : '' ?>">
                                <a href="<?= base_url('assessment/save-score-repeat') ?>" class="menu-link">
                                    <div data-i18n="บันทึกผลการเรียน(ซ้ำ)">บันทึกผลการเรียน(ซ้ำ)</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- งานหลักสูตร -->
                     <li class="menu-item <?= is_open_segment([['curriculum'], ['research']], $segments) ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bi-book-fill"></i>
                            <div data-i18n="งานหลักสูตร">งานหลักสูตร</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item <?= is_active_segment(['curriculum'], $segments) && !in_array('download-plan', $segments) ? 'active' : '' ?>">
                                <a href="<?= base_url('curriculum') ?>" class="menu-link">
                                    <div data-i18n="ส่งแผนการสอน">ส่งแผนการสอน</div>
                                </a>
                            </li>
                             <li class="menu-item <?= is_active_segment(['curriculum', 'download-plan'], $segments) ?>">
                                <a href="<?= base_url('curriculum/download-plan') ?>" class="menu-link">
                                    <div data-i18n="ดาวโหลดแผนการสอน">ดาวโหลดแผนการสอน</div>
                                </a>
                            </li>
                             <li class="menu-item <?= is_active_segment(['research'], $segments) && !in_array('load-research', $segments) && !in_array('setting', $segments) ? 'active' : '' ?>">
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
                    <li class="menu-item <?= is_open_segment([['teacher', 'reading_assessment'], ['teacher', 'desirable_assessment']], $segments) ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bi-clipboard-check"></i>
                            <div data-i18n="งานประเมินนักเรียน">งานประเมินนักเรียน</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item <?= is_active_segment(['teacher', 'reading_assessment'], $segments) ?>">
                                <a href="<?= base_url('teacher/reading_assessment') ?>" class="menu-link">
                                    <div data-i18n="แบบประเมินอ่านคิดวิเคราะห์">แบบประเมินอ่านคิดวิเคราะห์</div>
                                </a>
                            </li>
                            <li class="menu-item <?= is_active_segment(['teacher', 'desirable_assessment'], $segments) ?>">
                                <a href="<?= base_url('teacher/desirable_assessment') ?>" class="menu-link">
                                    <div data-i18n="คุณลักษณะอันพึงประสงค์">คุณลักษณะอันพึงประสงค์</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- งานพัฒนาผู้เรียน -->
                    <li class="menu-item <?= is_open_segment([['club']], $segments) ?>">
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
                        </ul>
                    </li>

                    <li class="menu-item <?= is_open_segment([['leave'], ['evaluation'], ['generate-leave-form'], ['portfolio'], ['attendance']], $segments) ?>">
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
                            <li class="menu-item <?= is_active_segment(['leave'], $segments) || is_active_segment(['generate-leave-form'], $segments) ? 'active' : '' ?>">
                                <a href="<?= base_url('leave') ?>" class="menu-link">
                                    <i class="bi bi-calendar-check me-2"></i>
                                    <div data-i18n="ระบบการลา">ระบบการลา</div>
                                </a>
                            </li>
                            <li class="menu-item <?= is_active_segment(['evaluation'], $segments) ? 'active' : '' ?>">
                                <a href="<?= base_url('evaluation') ?>" class="menu-link">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>
                                    <div data-i18n="ประเมินผลการปฏิบัติงาน (PA)">ประเมินผลการปฏิบัติงาน (PA)</div>
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
                    <li class="menu-header small text-uppercase"><span class="menu-header-text">หัวหน้ากลุ่มสาระ</span></li>
                    <li class="menu-item <?= is_open_segment([['assessment-head']], $segments) ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bi-shield-check"></i>
                            <div data-i18n="เมนูหัวหน้ากลุ่มสาระ">เมนูหัวหน้ากลุ่มสาระ</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item <?= is_active_segment(['assessment-head', 'check-plan'], $segments) || is_active_segment(['assessment-head', 'check-plan-detail'], $segments) ? 'active' : '' ?>">
                                <a href="<?= base_url('assessment-head/check-plan') ?>" class="menu-link">
                                    <div data-i18n="ตรวจแผนการสอน">ตรวจแผนการสอน</div>
                                </a>
                            </li>
                            <li class="menu-item <?= is_active_segment(['assessment-head', 'check-score'], $segments) || is_active_segment(['assessment-head', 'check-score-detail'], $segments) || is_active_segment(['assessment-head', 'check-score-student'], $segments) ? 'active' : '' ?>">
                                <a href="<?= base_url('assessment-head/check-score') ?>" class="menu-link">
                                    <div data-i18n="ตรวจสอบคะแนน">ตรวจสอบคะแนน</div>
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
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Global Quick Search -->
                        <div class="navbar-nav align-items-center me-auto position-relative" style="max-width: 320px; width: 100%;">
                            <div class="nav-item d-flex align-items-center w-100 bg-light rounded-pill px-3 py-1 border border-transparent focus-ring">
                                <i class="bx bx-search fs-4 lh-0 text-muted me-2"></i>
                                <input type="text" id="global-search-input" class="form-control border-0 shadow-none bg-transparent p-0" placeholder="ค้นหาเมนู, บันทึกคะแนน, แผนการสอน..." aria-label="Search..." autocomplete="off" />
                            </div>
                            <!-- Search Result Dropdown -->
                            <div id="search-results-dropdown" class="dropdown-menu shadow-lg border-0 w-100 p-2 position-absolute start-0" style="top: 105%; max-height: 350px; overflow-y: auto; display: none; z-index: 1090;">
                                <div class="text-muted small px-3 py-1 border-bottom fw-bold"><i class="bi bi-compass me-1"></i> เมนูและเครื่องมือที่ค้นพบ</div>
                                <div id="search-results-list" class="list-group list-group-flush mt-1"></div>
                            </div>
                        </div>
                        <!-- /Global Quick Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- User Info Badge on Top Navbar -->
                            <li class="nav-item me-3 d-none d-md-flex flex-column text-end">
                                <span class="fw-bold text-dark lh-1" style="font-size: 0.95rem;">ครู<?= esc(session()->get('fullname') ?? '') ?></span>
                                <small class="text-muted mt-1" style="font-size: 0.78rem;">
                                    <i class="bi bi-briefcase me-1 text-primary"></i><?= esc(session()->get('position') ?? 'ครูผู้สอน') ?>
                                </small>
                            </li>

                            <!-- User Dropdown -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= session()->get('person_img') ?>" alt class="w-px-40 h-auto rounded-circle border shadow-sm" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                                    <li>
                                        <a class="dropdown-item py-2" href="#">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= session()->get('person_img') ?>" alt class="w-px-40 h-auto rounded-circle border" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-bold d-block text-dark">ครู<?= esc(session()->get('fullname')) ?></span>
                                                    <span class="badge bg-label-primary mt-1">
                                                        <i class="bi bi-person-badge me-1"></i><?= esc(session()->get('position') ?? 'ครูผู้สอน') ?>
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
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
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
                                                if (is_numeric($seg) || strpos($seg, '-') !== false) continue;
                                                
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
                            <div class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
                                <div class="mb-2 mb-md-0">
                                    <strong>ระบบบริหารจัดการข้อมูลสำหรับครู</strong> © <script>document.write(new Date().getFullYear())</script>
                                    โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                                </div>
                                <div>
                                    พัฒนาโดย <a href="https://facebook.com/dekpiano" target="_blank" class="footer-link fw-bolder">Dekpiano</a>
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

    <?= $this->renderSection('scripts') ?>

    <script>
        $(function() {
            // Global file availability check for curriculum downloads/views
            $(document).on('click', 'a[href*="download-plan-file"]', function(e) {
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
                    success: function(response) {
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
                    error: function(jqXHR) {
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
            
            // Function to apply loading state to a button
            function applyLoading($btn, text = 'กำลังดำเนินการ...') {
                if ($btn.hasClass('btn-loading') || $btn.hasClass('no-loader')) return;
                
                $btn.data('original-text', $btn.html());
                $btn.data('original-width', $btn.outerWidth());
                $btn.css('min-width', $btn.outerWidth() + 'px');
                
                const isLink = $btn.is('a');
                if (isLink) {
                    $btn.addClass('btn-loading disabled');
                } else {
                    $btn.prop('disabled', true).addClass('btn-loading');
                }
                
                $btn.html(`<span class="spinner-border spinner-border-sm me-2" role="status"></span> ${text}`);
            }

            // 1) Form Submit
            $(document).on('submit', 'form', function(e) {
                const $form = $(this);
                if ($form.hasClass('no-loader')) return;

                const $submitBtn = $form.find('button[type="submit"], input[type="submit"]').not(':disabled');
                $submitBtn.each(function() {
                    applyLoading($(this), 'กำลังบันทึก...');
                });
            });

            // 2) General Search Buttons (Detected by ID, text, or icon)
            $(document).on('click', 'button, .btn', function(e) {
                const $btn = $(this);
                const text = $btn.text().trim();
                const id = $btn.attr('id') || '';
                const hasSearchIcon = $btn.find('.bi-search, .bx-search, .bi-person-search').length > 0;
                
                // Target search buttons
                if (id.toLowerCase().includes('search') || text.includes('ค้นหา') || hasSearchIcon) {
                    // Check if it's meant to trigger something (not just a toggle)
                    if (!$btn.attr('data-bs-toggle') && !$btn.hasClass('no-loader')) {
                        // For non-submit buttons, we only show loading if it's likely to cause a page change or heavy AJAX
                        // If it's type="submit", it's already handled by the form submit handler
                        if ($btn.attr('type') !== 'submit') {
                            applyLoading($btn, 'กำลังค้นหา...');
                        }
                    }
                }
            });

            // 3) Link Buttons & Navigation
            $(document).on('click', 'a.btn[href]', function(e) {
                const $btn = $(this);
                const href = $btn.attr('href');

                if (!href || href === '#' || href.startsWith('javascript:') 
                    || $btn.hasClass('disabled') || $btn.hasClass('no-loader')
                    || $btn.attr('target') === '_blank'
                    || $btn.attr('data-bs-toggle')) return;

                applyLoading($btn, 'กำลังโหลด...');
            });

            // 4) AJAX Global Handler
            $(document).ajaxSend(function(event, jqxhr, settings) {
                const $activeBtn = $(document.activeElement);
                if (($activeBtn.is('button') || $activeBtn.hasClass('btn')) && !$activeBtn.hasClass('no-loader') && !$activeBtn.data('ajax-loading')) {
                    applyLoading($activeBtn, 'กำลังประมวลผล...');
                    $activeBtn.data('ajax-loading', true);
                    jqxhr._loadingBtn = $activeBtn;
                }
            });

            $(document).ajaxComplete(function(event, jqxhr) {
                if (jqxhr._loadingBtn) {
                    const $btn = jqxhr._loadingBtn;
                    $btn.prop('disabled', false).removeClass('btn-loading disabled');
                    $btn.html($btn.data('original-text'));
                    $btn.removeData('ajax-loading');
                    $btn.css('min-width', '');
                }
            });

            // 5) Safety: Restore buttons on pageshow
            $(window).on('pageshow', function() {
                $('.btn-loading').each(function() {
                    const $btn = $(this);
                    const originalText = $btn.data('original-text');
                    if (originalText) {
                        $btn.prop('disabled', false).removeClass('btn-loading disabled');
                        $btn.html(originalText);
                        $btn.css('min-width', '');
                    }
                });
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
                { title: 'ประเมินผลการปฏิบัติงาน (PA)', desc: 'ส่งและติดตามผลการประเมิน PA', icon: 'bi-file-earmark-pdf text-danger', url: '<?= base_url("evaluation") ?>' },
                { title: 'ประวัติการอบรมและผลงาน', desc: 'บันทึก Portfolio และเกียรติบัตร', icon: 'bi-person-workspace text-info', url: '<?= base_url("portfolio") ?>' },
                <?php if (session()->get('pers_groupleade') !== null && session()->get('pers_groupleade') !== ''): ?>
                { title: 'ตรวจแผนการสอน (หัวหน้าหมวด)', desc: 'อนุมัติและตรวจแผนการสอนของครูในกลุ่มสาระ', icon: 'bi-shield-check text-danger', url: '<?= base_url("assessment-head/check-plan") ?>' },
                { title: 'ตรวจสอบคะแนน (หัวหน้าหมวด)', desc: 'ตรวจสอบการบันทึกคะแนนครูในกลุ่มสาระ', icon: 'bi-patch-check-fill text-success', url: '<?= base_url("assessment-head/check-score") ?>' },
                <?php endif; ?>
            ];

            const $searchInput = $('#global-search-input');
            const $searchDropdown = $('#search-results-dropdown');
            const $searchList = $('#search-results-list');

            $searchInput.on('input focus', function() {
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
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.navbar-nav.me-auto').length) {
                    $searchDropdown.hide();
                }
            });

            <?php if (session()->getFlashdata('success')) : ?>
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: '<?= session()->getFlashdata('success') ?>',
                    showConfirmButton: false,
                    timer: 1500
                });
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')) : ?>
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด!',
                    text: '<?= session()->getFlashdata('error') ?>'
                });
            <?php endif; ?>
        });


    </script>

</body>

</html>