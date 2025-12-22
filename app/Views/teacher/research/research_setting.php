<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'ตั้งค่าระบบงานวิจัย') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .settings-container {
        max-width: 900px;
        margin: 0 auto;
    }
    .settings-header {
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-right: 6px solid #696cff;
    }
    .luxe-settings-card {
        background: #fff;
        border-radius: 1.25rem;
        padding: 2.5rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.01);
    }
    .input-group-text-luxe {
        background-color: transparent;
        border-right: none;
        color: #696cff;
    }
    .form-control-luxe {
        border-left: none;
    }
</style>

<div class="settings-container py-3">
    <!-- Page Header -->
    <div class="settings-header">
        <div>
            <h4 class="fw-bold mb-0">ตั้งค่าระบบงานวิจัย</h4>
            <p class="text-muted mb-0 small">กำหนดช่วงเวลาและเงื่อนไขการส่งงานวิจัยในชั้นเรียน</p>
        </div>
        <div>
            <i class="bi bi-gear-wide-connected fs-1 text-primary opacity-25"></i>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="luxe-settings-card">
        <form action="<?= site_url('research/setting-update') ?>" method="post">
            
            <h6 class="fw-bold text-dark mb-4"><i class="bi bi-calendar-event me-2 text-primary"></i>กำหนดช่วงเวลาเปิด-ปิดรับงาน</h6>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="datetime-local" class="form-control" id="seres_setup_startdate" name="seres_setup_startdate" 
                               value="<?= date('Y-m-d\TH:i', strtotime($setup->seres_setup_startdate)) ?>" required>
                        <label for="seres_setup_startdate">วันที่เริ่มต้น (Start Date)</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="datetime-local" class="form-control" id="seres_setup_enddate" name="seres_setup_enddate" 
                               value="<?= date('Y-m-d\TH:i', strtotime($setup->seres_setup_enddate)) ?>" required>
                        <label for="seres_setup_enddate">วันที่สิ้นสุด (End Date)</label>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold text-dark mb-4"><i class="bi bi-info-circle me-2 text-primary"></i>ข้อมูลประจำภาคเรียนและสถานะ</h6>
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="number" class="form-control" id="seres_setup_year" name="seres_setup_year" 
                               value="<?= esc($setup->seres_setup_year) ?>" placeholder="25XX" required>
                        <label for="seres_setup_year">ปีการศึกษา</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="seres_setup_term" name="seres_setup_term">
                            <option value="1" <?= ($setup->seres_setup_term == '1') ? 'selected' : '' ?>>ภาคเรียนที่ 1</option>
                            <option value="2" <?= ($setup->seres_setup_term == '2') ? 'selected' : '' ?>>ภาคเรียนที่ 2</option>
                        </select>
                        <label for="seres_setup_term">ภาคเรียน</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" id="seres_setup_status" name="seres_setup_status">
                            <option value="on" <?= ($setup->seres_setup_status == 'on') ? 'selected' : '' ?>>เปิดระบบ (Manual ON)</option>
                            <option value="off" <?= ($setup->seres_setup_status == 'off') ? 'selected' : '' ?>>ปิดระบบ (Manual OFF)</option>
                        </select>
                        <label for="seres_setup_status">สถานะระบบการส่ง</label>
                    </div>
                </div>
            </div>

            <div class="alert alert-label-primary d-flex align-items-center mb-5" role="alert">
                <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                <div class="small">
                    สถานะ <strong>"เปิดระบบ"</strong> จะทำงานร่วมกับช่วงวันที่กำหนด ครูจะส่งงานได้ก็ต่อเมื่ออยู่ในช่วงเวลาและสถานะเป็น "เปิด" เท่านั้น
                </div>
            </div>

            <div class="text-center pt-3">
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                    <i class="bi bi-save-fill me-2"></i> บันทึกการตั้งค่าทั้งหมด
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
