<!doctype html>

<html lang="en" class="layout-menu-fixed layout-compact">

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

    <!-- Helpers -->
    <script src="<?= base_url('public/assets/sneat/vendor/js/helpers.js') ?>"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?= base_url('public/assets/sneat/js/config.js') ?>"></script>

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/css/core.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/css/demo.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/libs/apex-charts/apex-charts.css') ?>" />

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
            background-color: #696cff26 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3e%3cpath fill='%23696cff' fill-opacity='0.3' d='M0,160L48,176C96,192,192,224,288,213.3C384,203,480,149,576,133.3C672,117,768,139,864,165.3C960,192,1056,224,1152,218.7C1248,213,1344,171,1392,149.3L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3e%3c/path%3e%3c/svg%3e");
            background-position: bottom;
            background-repeat: no-repeat;
            background-size: 100%; /* Adjusted size */
        }
    </style>

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/main.min.css' rel='stylesheet' />

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
    // Get all segments
    $segments = $currentUri->getSegments();

    // Function to check if a menu item is active
    function is_active_segment($expected_segments, $current_segments) {
        // Home page check
        if (empty($expected_segments) && empty($current_segments)) {
            return 'active';
        }
        if (empty($expected_segments)) {
            return '';
        }

        // Check if the current path starts with the expected path
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

    // Function to check if a parent menu item should be open
    function is_open_segment($expected_parent_segments, $current_segments) {
        foreach ($expected_parent_segments as $parent_segment_array) {
            // Check if the current URI starts with the parent path
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
                            <li class="menu-item <?= is_active_segment(['assessment', 'save-score-normal'], $segments) ?>">
                                <a href="<?= base_url('assessment/save-score-normal') ?>" class="menu-link">
                                    <div data-i18n="บันทึกผลการเรียน(ปกติ)">บันทึกผลการเรียน(ปกติ)</div>
                                </a>
                            </li>
                            <li class="menu-item <?= is_active_segment(['assessment', 'save-score-repeat'], $segments) ?>">
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
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <i class="bx bx-search fs-4 lh-0"></i>
                                <input type="text" class="form-control border-0 shadow-none" placeholder="Search..." aria-label="Search..." />
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= session()->get('person_img') ?>" alt class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= session()->get('person_img') ?>" alt class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">ครู<?= session()->get('fullname') ?></span>
                                                    <small class="text-muted">กำลังใช้งาน</small>
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
                              
<!--                                     
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0"><?= esc($title ?? 'หน้าหลัก') ?></h5>
                                            <nav aria-label="breadcrumb">
                                                <ol class="breadcrumb mb-0">
                                                    <li class="breadcrumb-item"><a href="<?= base_url() ?>">หน้าหลัก</a></li>
                                                    <?php if (strpos(current_url(), 'club') !== false) : ?>
                                                        <li class="breadcrumb-item"><a href="<?= base_url('club') ?>">บันทึกชุมนุม</a></li>
                                                    <?php endif; ?>
                                                    <li class="breadcrumb-item active" aria-current="page"><?= esc($title ?? 'หน้าหลัก') ?></li>
                                                </ol>
                                            </nav>
                                        </div> -->
                                    
                               
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
        <div class="layout-overlay layout-menu-toggle"></div>
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