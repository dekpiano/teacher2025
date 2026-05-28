<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
SKJ Check-In
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    /* Premium modern styling */
    .attendance-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0.5rem;
    }
    .attendance-hero {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        border-radius: 1.5rem;
        color: #fff;
        padding: 1.75rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.15);
    }
    .attendance-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 70%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .clock-display {
        font-size: 3rem;
        font-weight: 800;
        letter-spacing: -1px;
        font-variant-numeric: tabular-nums;
        text-shadow: 0 4px 10px rgba(0,0,0,0.15);
        color: #ffffff;
    }
    .date-display {
        font-size: 1rem;
        opacity: 0.9;
        font-weight: 500;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .status-none       { background: rgba(255, 255, 255, 0.2); color: #fff; border: 1px solid rgba(255,255,255,0.3); }
    .status-checked_in { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
    .status-completed  { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }

    /* Biometric Scanning Camera Container */
    .camera-card {
        background: #ffffff;
        border-radius: 1.5rem;
        padding: 1.5rem;
        box-shadow: 0 8px 30px rgba(0,0,0,0.04);
        border: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }
    .camera-wrapper {
        position: relative;
        border-radius: 1.25rem;
        overflow: hidden;
        background: #0b0f19;
        margin: 0 auto;
        aspect-ratio: 4/3;
        box-shadow: inset 0 0 20px rgba(0,0,0,0.8);
    }
    #camera-feed {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scaleX(-1); /* Mirror view for natural interaction */
    }
    #photo-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scaleX(-1);
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 5;
    }
    .camera-placeholder {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #9ca3af;
        z-index: 2;
    }
    
    /* Neon Scanning Overlay Effect */
    .scanner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 6;
        border: 2px solid rgba(79, 70, 229, 0.4);
        border-radius: 1.25rem;
        box-sizing: border-box;
        display: none;
    }
    .scanner-line {
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, rgba(79, 70, 229, 0) 0%, rgba(79, 70, 229, 0.8) 50%, rgba(79, 70, 229, 0) 100%);
        box-shadow: 0 0 10px #4f46e5, 0 0 20px #3b82f6;
        position: absolute;
        top: 0;
        left: 0;
        animation: scan-animation 2.5s linear infinite;
    }
    @keyframes scan-animation {
        0% { top: 0%; }
        50% { top: 100%; }
        100% { top: 0%; }
    }

    /* Target UI box overlay */
    .scanner-target {
        position: absolute;
        top: 15%;
        left: 15%;
        right: 15%;
        bottom: 15%;
        border: 2px dashed rgba(255, 255, 255, 0.25);
        border-radius: 50%;
        pointer-events: none;
        z-index: 4;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-camera-action {
        border-radius: 1rem;
        padding: 0.8rem 1.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .btn-camera-action:active {
        transform: scale(0.96);
    }
    .btn-submit-action {
        font-size: 1.05rem;
        padding: 0.9rem 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .info-card {
        background: #ffffff;
        border-radius: 1.5rem;
        padding: 1.5rem;
        box-shadow: 0 8px 30px rgba(0,0,0,0.04);
        border: 1px solid #f3f4f6;
    }
    .status-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.8rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .status-row:last-child { border-bottom: none; }

    .gps-status {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 1rem;
        border-radius: 2rem;
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }
    .gps-ok      { background: #ecfdf5; color: #059669; }
    .gps-warn    { background: #fffbeb; color: #d97706; }
    .gps-error   { background: #fef2f2; color: #dc2626; }

    /* Interactive animations */
    .pulse-amber {
        animation: pulse-ring-amber 2s infinite;
    }
    @keyframes pulse-ring-amber {
        0% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(217, 119, 6, 0); }
        100% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0); }
    }
</style>

<div class="container-fluid attendance-container">
    <!-- Hero Clock Area -->
    <div class="attendance-hero mb-4 text-center text-md-start">
        <div class="row align-items-center">
            <div class="col-md-7">
                <span class="badge bg-opacity-20  mb-2 px-3 py-2"><i class="bi bi-clock-history me-1"></i> SKJ Check-In</span>
                <div class="clock-display" id="live-clock">00:00:00</div>
                <div class="date-display mt-1" id="live-date">---</div>
                
                <div>
                    <div id="gps-status" class="gps-status gps-warn">
                        <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
                        <span>กำลังระบุตำแหน่งพิกัด...</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5 text-md-end mt-4 mt-md-0">
                <div class="text-white text-opacity-75 small mb-1">สถานะการลงเวลาวันนี้</div>
                <div>
                    <?php if ($status === 'none'): ?>
                        <span class="status-badge status-none"><i class="bi bi-circle-fill me-1"></i>ยังไม่ลงเวลา</span>
                    <?php elseif ($status === 'checked_in'): ?>
                        <span class="status-badge status-checked_in pulse-amber"><i class="bi bi-arrow-right-circle-fill me-1"></i>ลงเวลาเข้าแล้ว</span>
                    <?php else: ?>
                        <span class="status-badge status-completed"><i class="bi bi-patch-check-fill me-1"></i>เสร็จสมบูรณ์</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Mobile-First Stacking Grid -->
    <div class="row g-4">
        <!-- System disabled warning card -->
        <?php if (!$isSystemActive): ?>
        <div class="col-12 col-lg-6 order-1">
            <div class="camera-card text-center p-4">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-4 mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="bi bi-shield-slash-fill fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">ปิดใช้งานการลงเวลาออนไลน์ชั่วคราว</h5>
                <p class="text-secondary small mb-0 px-2">
                    ขณะนี้ผู้ดูแลระบบได้ปิดใช้งานระบบลงเวลางานออนไลน์ (GPS) ชั่วคราว 
                    กรุณาลงเวลาปฏิบัติราชการด้วยวิธีปกติ (เครื่องสแกนลายนิ้วมือ ณ จุดบันทึกเวลา)
                </p>
                <div class="border-top mt-3 pt-3 text-muted small">
                    <i class="bi bi-exclamation-circle me-1 text-warning"></i> ระบบลงเวลานี้เปิดให้บริการเฉพาะกิจตามประกาศโรงเรียนเท่านั้น
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Interactive Camera Interface (Primary Action Focus) -->
        <div class="col-12 col-lg-6 order-1">
            <div class="camera-card text-center">
                <h5 class="mb-3 text-start d-flex align-items-center justify-content-between">
                    <span class="fw-bold"><i class="bi bi-camera-fill me-2 text-primary"></i>ถ่ายรูปยืนยันตัวตน</span>
                    <span class="badge bg-light text-secondary small fw-normal">Biometric Secure</span>
                </h5>

                <div class="camera-wrapper mb-3">
                    <video id="camera-feed" autoplay playsinline></video>
                    <canvas id="camera-canvas" style="display:none;"></canvas>
                    <img id="photo-preview" alt="Preview">

                    <!-- Neon line scanner overlay -->
                    <div class="scanner-overlay" id="scanner-overlay">
                        <div class="scanner-line"></div>
                    </div>

                    <!-- Visual target marker -->
                    <div class="scanner-target" id="scanner-target">
                        <i class="bi bi-plus-lg text-white opacity-25" style="font-size: 2rem;"></i>
                    </div>

                    <!-- Placeholder -->
                    <div class="camera-placeholder" id="camera-placeholder">
                        <div class="bg-light p-3 rounded-circle mb-2" style="background-color: rgba(255,255,255,0.07) !important;">
                            <i class="bi bi-camera-video" style="font-size: 2.5rem; color: rgba(255,255,255,0.6)"></i>
                        </div>
                        <p class="mb-0 text-white text-opacity-75 small">กดปุ่ม "เปิดใช้งานกล้อง" ด้านล่าง</p>
                    </div>
                </div>

                <!-- GPS Info Display -->
                <div id="gps-info" class="alert alert-light border border-dashed mb-3" style="display:none; border-radius: 1rem;">
                    <div class="row small g-2">
                        <div class="col-6 text-start">
                            <span class="text-muted"><i class="bi bi-geo-alt me-1 text-primary"></i>ละติจูด:</span>
                            <strong id="gps-lat" class="text-dark">---</strong>
                        </div>
                        <div class="col-6 text-start">
                            <span class="text-muted"><i class="bi bi-geo-alt me-1 text-primary"></i>ลองจิจูด:</span>
                            <strong id="gps-lng" class="text-dark">---</strong>
                        </div>
                        <div class="col-12 text-start pt-1 border-top mt-2">
                            <div class="d-flex align-items-center gap-1">
                                <span id="gps-distance" class="small fw-semibold"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Controls -->
                <div class="d-grid gap-2">
                    <button type="button" id="btn-start-camera" class="btn btn-outline-primary btn-camera-action py-3">
                        <i class="bi bi-camera-video-fill"></i> เปิดใช้งานกล้อง
                    </button>
                    
                    <button type="button" id="btn-capture" class="btn btn-primary btn-camera-action py-3" style="display:none;">
                        <i class="bi bi-camera-fill"></i> กดเพื่อถ่ายรูปภาพ
                    </button>
                    
                    <button type="button" id="btn-retake" class="btn btn-outline-secondary btn-camera-action py-3" style="display:none;">
                        <i class="bi bi-arrow-counterclockwise"></i> ถ่ายรูปใหม่อีกครั้ง
                    </button>

                    <?php if ($status === 'none'): ?>
                        <button type="button" id="btn-checkin" class="btn btn-success btn-camera-action btn-submit-action py-3 mt-2" style="display:none;">
                            <i class="bi bi-box-arrow-in-right"></i> ยืนยันบันทึกเวลาเข้างาน
                        </button>
                    <?php elseif ($status === 'checked_in'): ?>
                        <button type="button" id="btn-checkout" class="btn btn-warning btn-camera-action btn-submit-action py-3 mt-2" style="display:none;">
                            <i class="bi bi-box-arrow-right"></i> ยืนยันบันทึกเวลาออกงาน
                        </button>
                    <?php else: ?>
                        <div class="alert alert-success d-flex align-items-center justify-content-center gap-2 mb-0 py-3 mt-2" style="border-radius: 1rem;">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <div>ลงเวลาเสร็จสมบูรณ์เรียบร้อยแล้ววันนี้</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- System & Location Settings Info -->
        <div class="col-12 col-lg-6 order-2">
            <!-- Today's Record Details -->
            <div class="info-card mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-journal-text me-2 text-primary"></i>บันทึกเวลาเช็คอิน-เช็คเอาต์วันนี้</h5>

                <div class="status-row">
                    <span class="text-muted d-flex align-items-center gap-2">
                        <span class="p-1 rounded bg-success bg-opacity-10 text-success"><i class="bi bi-box-arrow-in-right"></i></span>
                        เวลาเข้างาน
                    </span>
                    <span class="fw-bold fs-5 text-dark" id="info-checkin">
                        <?= $todayRecord['check_in'] ?? '- - : - -' ?>
                    </span>
                </div>
                
                <div class="status-row">
                    <span class="text-muted d-flex align-items-center gap-2">
                        <span class="p-1 rounded bg-warning bg-opacity-10 text-warning"><i class="bi bi-box-arrow-right"></i></span>
                        เวลาออกงาน
                    </span>
                    <span class="fw-bold fs-5 text-dark" id="info-checkout">
                        <?= $todayRecord['check_out'] ?? '- - : - -' ?>
                    </span>
                </div>
                
                <div class="status-row">
                    <span class="text-muted d-flex align-items-center gap-2">
                        <span class="p-1 rounded bg-info bg-opacity-10 text-info"><i class="bi bi-patch-question"></i></span>
                        สถานะการทำงาน
                    </span>
                    <?php if (empty($todayRecord)): ?>
                        <span class="badge bg-light text-secondary px-3 py-2 rounded-pill">ยังไม่ลงเวลา</span>
                    <?php elseif ($todayRecord['status'] === 'มาสาย'): ?>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="bi bi-exclamation-triangle-fill me-1"></i>มาสาย</span>
                    <?php else: ?>
                        <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>ปกติ</span>
                    <?php endif; ?>
                </div>

                <!-- Selfies preview -->
                <div id="selfie-display" class="mt-4" style="<?= empty($todayRecord['check_in_photo']) && empty($todayRecord['check_out_photo']) ? 'display:none;' : '' ?>">
                    <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-images me-1"></i>รูปภาพยืนยันตัวตนที่จัดเก็บ</h6>
                    <div class="row g-3">
                        <?php if (!empty($todayRecord['check_in_photo'])): ?>
                        <div class="col-6">
                            <div class="bg-light p-2 rounded-3 border">
                                <small class="text-muted d-block text-center mb-1"><i class="bi bi-box-arrow-in-right me-1"></i>รูปสแกนเข้า</small>
                                <img src="<?= esc($todayRecord['check_in_photo']) ?>" class="img-fluid rounded shadow-sm w-100" style="object-fit: cover; aspect-ratio: 4/3; max-height: 120px;">
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($todayRecord['check_out_photo'])): ?>
                        <div class="col-6">
                            <div class="bg-light p-2 rounded-3 border">
                                <small class="text-muted d-block text-center mb-1"><i class="bi bi-box-arrow-right me-1"></i>รูปสแกนออก</small>
                                <img src="<?= esc($todayRecord['check_out_photo']) ?>" class="img-fluid rounded shadow-sm w-100" style="object-fit: cover; aspect-ratio: 4/3; max-height: 120px;">
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Rules & Limits Card -->
            <?php if ($locationSettings): ?>
            <div class="info-card mb-4 bg-light border-0">
                <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-shield-exclamation me-2"></i>ระเบียบและเงื่อนไขพิกัดพื้นที่การทำงาน</h6>
                <div class="row g-3 small">
                    <div class="col-6">
                        <span class="text-muted d-block">ช่วงเวลาลงเข้างาน:</span>
                        <strong class="text-dark fs-6"><?= date('H:i', strtotime($locationSettings->check_in_start)) ?> - <?= date('H:i', strtotime($locationSettings->check_in_end)) ?> น.</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block">ช่วงเวลาลงออกงาน:</span>
                        <strong class="text-dark fs-6"><?= date('H:i', strtotime($locationSettings->check_out_start)) ?> - <?= date('H:i', strtotime($locationSettings->check_out_end)) ?> น.</strong>
                    </div>
                    <div class="col-12 border-top pt-2 mt-2">
                        <div class="d-flex align-items-center gap-1 text-muted">
                            <i class="bi bi-info-circle-fill text-primary"></i>
                            <span>การลงเวลาจำเป็นต้องอยู่ภายในรัศมี <strong><?= $locationSettings->radius_m ?> เมตร</strong> จากสัญลักษณ์พิกัดจุดศูนย์กลางโรงเรียน</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Navigation Links -->
            <div class="row g-2">
                <div class="col-6">
                    <a href="<?= base_url('attendance/history') ?>" class="btn btn-outline-primary w-100 py-2 d-flex align-items-center justify-content-center gap-1" style="border-radius: 10px;">
                        <i class="bi bi-clock-history"></i> ดูประวัติ
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?= base_url('attendance/report') ?>" class="btn btn-outline-info w-100 py-2 d-flex align-items-center justify-content-center gap-1" style="border-radius: 10px;">
                        <i class="bi bi-file-earmark-bar-graph"></i> รายงานเดือนนี้
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // ---- State ----
    let stream = null;
    let capturedPhoto = null;
    let currentLat = null;
    let currentLng = null;
    let withinSchool = false;

    const schoolLat = <?= $locationSettings->lat ?? 15.7060 ?>;
    const schoolLng = <?= $locationSettings->lng ?? 100.1280 ?>;
    const schoolRadius = <?= $locationSettings->radius_m ?? 200 ?>;

    const video = document.getElementById('camera-feed');
    const canvas = document.getElementById('camera-canvas');
    const photoPreview = document.getElementById('photo-preview');
    const placeholder = document.getElementById('camera-placeholder');
    const btnStart = document.getElementById('btn-start-camera');
    const btnCapture = document.getElementById('btn-capture');
    const btnRetake = document.getElementById('btn-retake');
    const btnCheckin = document.getElementById('btn-checkin');
    const btnCheckout = document.getElementById('btn-checkout');
    const gpsStatusEl = document.getElementById('gps-status');

    // ---- Live Clock ----
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('live-clock').textContent = `${h}:${m}:${s}`;

        const thDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
        const thMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                         'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
        const dayName = thDays[now.getDay()];
        const dateStr = now.getDate();
        const monthName = thMonths[now.getMonth()];
        const year = now.getFullYear() + 543; // พุทธศักราช
        document.getElementById('live-date').textContent = `วัน${dayName}ที่ ${dateStr} ${monthName} ${year}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ---- GPS Tracking ----
    function updateGpsStatus(type, message, lat, lng) {
        const icons = { ok: 'bi-geo-alt-fill', warn: 'bi-exclamation-triangle', error: 'bi-x-circle' };
        gpsStatusEl.className = `gps-status gps-${type} mt-2`;
        gpsStatusEl.innerHTML = `<i class="bi ${icons[type] || 'bi-geo-alt'}"></i><span>${message}</span>`;

        if (lat !== undefined && lng !== undefined) {
            document.getElementById('gps-info').style.display = 'block';
            document.getElementById('gps-lat').textContent = lat.toFixed(7);
            document.getElementById('gps-lng').textContent = lng.toFixed(7);
        }
    }

    function haversineDistance(lat1, lng1, lat2, lng2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function handlePosition(position) {
        currentLat = position.coords.latitude;
        currentLng = position.coords.longitude;
        const distance = haversineDistance(currentLat, currentLng, schoolLat, schoolLng);
        withinSchool = schoolRadius === 0 || distance <= schoolRadius;

        if (withinSchool) {
            updateGpsStatus('ok', schoolRadius === 0 
                ? `เปิดใช้งานลงเวลาอิสระ (ห่างจากจุดศูนย์กลาง ${Math.round(distance)} เมตร)` 
                : `อยู่ในพื้นที่โรงเรียน (ระยะ ${Math.round(distance)} เมตร)`, currentLat, currentLng);
        } else {
            updateGpsStatus('warn', `อยู่นอกพื้นที่โรงเรียน (ระยะ ${Math.round(distance)} เมตร)`, currentLat, currentLng);
        }

        const distEl = document.getElementById('gps-distance');
        if (distEl) {
            distEl.textContent = withinSchool
                ? (schoolRadius === 0 
                    ? `✓ เปิดให้ลงเวลานอกพื้นที่แบบไม่มีเงื่อนไข` 
                    : `✓ อยู่ในรัศมี ${schoolRadius} เมตร สามารถเช็คชื่อได️`)
                : `✗ ต้องอยู่ภายในรัศมี ${schoolRadius} เมตร จากจุดศูนย์กลางโรงเรียน`;
        }
    }

    function handleGpsError(error) {
        currentLat = null;
        currentLng = null;
        withinSchool = false;
        let msg = 'ไม่สามารถระบุตำแหน่งได้';
        if (error.code === 1) msg = 'กรุณาอนุญาตเข้าถึงตำแหน่ง GPS';
        if (error.code === 2) msg = 'ไม่พบสัญญาณ GPS';
        if (error.code === 3) msg = 'การระบุตำแหน่งใช้เวลานานเกินไป';
        updateGpsStatus('error', msg);
    }

    // เริ่มติดตาม GPS
    if (<?= $isSystemActive ? 'true' : 'false' ?>) {
        if ('geolocation' in navigator) {
            navigator.geolocation.watchPosition(handlePosition, handleGpsError, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 5000,
            });
        } else {
            updateGpsStatus('error', 'เบราว์เซอร์ไม่รองรับ GPS');
        }
    } else {
        updateGpsStatus('error', 'ระบบลงเวลาเช็คอินออนไลน์ปิดใช้งานชั่วคราว');
    }

    // ---- Camera ----
    const scannerOverlay = document.getElementById('scanner-overlay');

    btnStart.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: 640, height: 480 },
                audio: false,
            });
            video.srcObject = stream;
            placeholder.style.display = 'none';
            btnStart.style.display = 'none';
            btnCapture.style.display = 'inline-block';
            if (scannerOverlay) scannerOverlay.style.display = 'block';
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'ไม่สามารถเปิดกล้องได้',
                text: 'กรุณาอนุญาตเข้าถึงกล้องในเบราว์เซอร์',
            });
        }
    });

    btnCapture.addEventListener('click', () => {
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        
        // Mirror horizontally to match preview
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        ctx.setTransform(1, 0, 0, 1, 0, 0); // reset transformation

        capturedPhoto = canvas.toDataURL('image/jpeg', 0.85);
        photoPreview.src = capturedPhoto;
        photoPreview.style.display = 'block';
        video.style.display = 'none';
        placeholder.style.display = 'none';
        if (scannerOverlay) scannerOverlay.style.display = 'none';

        btnCapture.style.display = 'none';
        btnRetake.style.display = 'inline-block';

        // แสดงปุ่มเช็คชื่อถ้ามี
        if (btnCheckin) btnCheckin.style.display = 'inline-block';
        if (btnCheckout) btnCheckout.style.display = 'inline-block';
    });

    btnRetake.addEventListener('click', () => {
        capturedPhoto = null;
        photoPreview.style.display = 'none';
        photoPreview.src = '';
        video.style.display = 'block';
        if (scannerOverlay) scannerOverlay.style.display = 'block';

        btnRetake.style.display = 'none';
        btnCapture.style.display = 'inline-block';
        if (btnCheckin) btnCheckin.style.display = 'none';
        if (btnCheckout) btnCheckout.style.display = 'none';

        // กลับไปถ่ายใหม่
        video.style.display = 'block';
    });

    // ---- Check In ----
    if (btnCheckin) {
        btnCheckin.addEventListener('click', () => submitAttendance('checkin'));
    }
    if (btnCheckout) {
        btnCheckout.addEventListener('click', () => submitAttendance('checkout'));
    }

    function submitAttendance(type) {
        // ตรวจสอบก่อนเช็คชื่อ
        if (!capturedPhoto) {
            Swal.fire({ icon: 'warning', title: 'กรุณาล่องรูปก่อน' });
            return;
        }
        if (!currentLat || !currentLng) {
            Swal.fire({ icon: 'warning', title: 'ไม่พบข้อมูล GPS', text: 'กรุณาอนุญาตเข้าถึงตำแหน่งและรอสักครู่' });
            return;
        }

        const url = type === 'checkin'
            ? '<?= base_url('attendance/checkin') ?>'
            : '<?= base_url('attendance/checkout') ?>';

        const actionText = type === 'checkin' ? 'เช็คชื่อเข้างาน' : 'เช็คชื่อออกงาน';

        Swal.fire({
            title: actionText + '?',
            text: 'ยืนยัน ' + actionText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            const formData = new FormData();
            formData.append('lat', currentLat);
            formData.append('lng', currentLng);
            formData.append('selfie', capturedPhoto);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData,
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: res.message + '\nเวลา: ' + res.time,
                        confirmButtonText: 'OK',
                    }).then(() => {
                        // รีโหลดหน้าเพื่อแสดงสถานะใหม่
                        window.location.reload();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'ไม่สำเร็จ', text: res.message });
                }
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: 'กรุณาลองใหม่อีกครั้ง' });
            });
        });
    }
</script>
<?= $this->endSection() ?>
