<!doctype html>

<html
  lang="th"
  class="light-style layout-wide customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="<?= base_url('public/assets/sneat/') ?>"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>เข้าสู่ระบบ | ระบบบริหารจัดการงานวิชาการ</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('public/assets/sneat/img/favicon/favicon.ico') ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=K2D:wght@400;500;700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/fonts/boxicons.css') ?>" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/css/core.css') ?>" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/css/theme-default.css') ?>" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/css/demo.css') ?>" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="<?= base_url('public/assets/sneat/vendor/css/pages/page-auth.css') ?>" />

    <style>
      body, h1, h2, h3, h4, h5, h6, p, div, span, input, textarea, select, button, .form-control, .btn {
        font-family: 'K2D', sans-serif;
      }
      .btn-google {
        background-color: #DB4437 !important;
        color: white !important;
      }
    </style>

    <!-- Helpers -->
    <script src="<?= base_url('public/assets/sneat/vendor/js/helpers.js') ?>"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?= base_url('public/assets/sneat/js/config.js') ?>"></script>
  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
                <div class="app-brand-logo d-flex justify-content-center">
                    <img src="https://skj.ac.th/uploads/logoSchool/LogoSKJ_4.png" alt="LogoSKJ_4" style="height: 80px;" />
                  </div>
              <div class="app-brand justify-content-center">
                <a href="index.html" class="app-brand-link gap-2">
                 
                  <span class="app-brand-text demo text-body fw-bolder">Teacher SKJ</span>
                </a>
              </div>
              <!-- /Logo -->
              <h5 class="mb-2 text-center">ยินดีต้อนรับสู่ระบบครู สกจ.9👋</h5>

              <div id="alert-message" class="alert alert-danger" style="display: none;" role="alert"></div>


              <div class="d-flex justify-content-center">
                <a href="<?= $google_login_url ?>" class="btn btn-lg btn-google me-3 d-flex align-items-center">
                  <i class="bx bxl-google me-2"></i>เข้าสู่ระบบด้วย Email โรงเรียน @skj.ac.th 
                </a>
              </div>
            </div>
          </div>
          <!-- /Register -->
        </div>
      </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js public/assets/vendor/js/core.js -->

    <script src="<?= base_url('public/assets/sneat/vendor/libs/jquery/jquery.js') ?>"></script>
    <script src="<?= base_url('public/assets/sneat/vendor/libs/popper/popper.js') ?>"></script>
    <script src="<?= base_url('public/assets/sneat/vendor/js/bootstrap.js') ?>"></script>
    <script src="<?= base_url('public/assets/sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') ?>"></script>
    <script src="<?= base_url('public/assets/sneat/vendor/js/menu.js') ?>"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="<?= base_url('public/assets/sneat/js/main.js') ?>"></script>

    <!-- Page JS -->
    <script>
    $(document).ready(function() {
        $('#formAuthentication').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        window.location.href = '<?= base_url('home') ?>';
                    } else {
                        $('#alert-message').text(response.message).show();
                    }
                },
                error: function() {
                    $('#alert-message').text('เกิดข้อผิดพลาดในการเชื่อมต่อ').show();
                }
            });
        });
    });
    </script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>