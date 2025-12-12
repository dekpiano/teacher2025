<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ลงทะเบียนวิชาการสอน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>




        <div class="container-fluid">
            <form class="needs-validation" novalidate id="form_insert_plan" action="<?= site_url('curriculum/insert-plan') ?>" method="post">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">ข้อมูลเบื้องต้น</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label" for="seplan_namesubject">ชื่อวิชา</label>
                                    <input type="text" id="seplan_namesubject" name="seplan_namesubject" placeholder="เช่น วิทยาศาสตร์"
                                        class="form-control" required>
                                    <div class="invalid-feedback">กรุณากรอกชื่อวิชา</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="seplan_coursecode">รหัสวิชา</label>
                                    <input type="text" id="seplan_coursecode" name="seplan_coursecode" placeholder="เช่น ว21101"
                                        class="form-control" required>
                                    <div class="invalid-feedback">กรุณากรอกรหัสวิชา</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ประเภทวิชา</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="seplan_typesubject" id="seplan_typesubject_base" value="พื้นฐาน" required>
                                            <label class="form-check-label" for="seplan_typesubject_base">พื้นฐาน</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="seplan_typesubject" id="seplan_typesubject_extra" value="เพิ่มเติม" required>
                                            <label class="form-check-label" for="seplan_typesubject_extra">เพิ่มเติม</label>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback d-block">กรุณาเลือกประเภทวิชา</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="seplan_gradelevel">ระดับชั้น</label>
                                    <select id="seplan_gradelevel" name="seplan_gradelevel" class="form-select"
                                        required>
                                        <option value="" selected disabled>เลือก...</option>
                                        <option value="1">ม.1</option>
                                        <option value="2">ม.2</option>
                                        <option value="3">ม.3</option>
                                        <option value="4">ม.4</option>
                                        <option value="5">ม.5</option>
                                        <option value="6">ม.6</option>
                                    </select>
                                    <div class="invalid-feedback">กรุณาเลือกระดับชั้น</div>
                                </div>
                            </div>
                            <div class="col-12 text-center mb-4">
                        <?php if(isset($OnOff[0]) && $OnOff[0]->seplanset_status == "on"):?>

                        <input type="submit" value="ลงทะเบียน" class="btn btn-primary btn-block">

                        <?php else: ?>

                        <button type="button" class="btn btn-primary btn-block" disabled>หมดเวลาส่ง</button>

                        <?php endif; ?>
                    </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">คำแนะนำ</div>
                            <div class="card-body">
                                <h4 class="card-title">การลงทะเบียนวิชาก่อนเพิ่มงาน</h4>
                                <p class="card-text">
                                    ให้คุณครูเพิ่มข้อมูลแต่ละวิชาให้เรียบร้อย และกดปุ่มลงทะเบียน  แล้วระบบจะพาไปยังหน้าเพิ่มไฟล์ต่าง ๆ ที่กำหนดไว้
                                </p>
                                <h4 class="card-title">ระบบอัพเดทเป็นเวอร์ชั่นที่ 2</h4>
                                <p class="card-text">
                                    ระบบพัฒนาให้สามารถใช้งานในการเพิ่มไฟล์สะดวกขึ้น และดูการส่งงานได้ง่ายขึ้น
                                </p>                          
                            </div>
                        </div>
                        <div class="card ">
                            <div class="card-header text-white bg-danger">คำเตือน</div>
                            <div class="card-body">
                                <h4 class="card-title">เมื่อการลงทะเบียนแล้วนั้น ไม่สามารถแก้ข้อมูลได้อีก กรุณาตรวจสอบข้อมูลให้ถูกต้อง</h4>
                            </div>
                        </div>
                    </div>
                   
            </form>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    // Handle form submission via AJAX for insert_plan
    $('#form_insert_plan').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: '<?= site_url('curriculum/insert-plan') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.msg === 'OK') {
                    Swal.fire('สำเร็จ', 'ลงทะเบียนวิชาสำเร็จ', 'success').then(() => {
                        // Redirect to edit page after successful registration
                        window.location.href = '<?= site_url('curriculum/edit-plan/') ?>' + response[0][0].seplan_ID;
                    });
                } else if (response === 2) {
                    Swal.fire('ผิดพลาด', 'รหัสวิชานี้ถูกลงทะเบียนแล้ว', 'error');
                } else {
                    Swal.fire('ผิดพลาด', 'ไม่สามารถลงทะเบียนวิชาได้', 'error');
                }
            },
            error: function() {
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการส่งข้อมูล', 'error');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
