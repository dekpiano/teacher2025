<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'งานวิจัยในชั้นเรียน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$setupData = $setup ?? null;
$is_system_on = false;
$deadline = null;
if ($setupData) {
    $tiemstart = strtotime($setupData->seres_setup_startdate);
    $tiemEnd = strtotime($setupData->seres_setup_enddate);
    $timeNow = time();
    $is_system_on = ($tiemstart < $timeNow && $tiemEnd > $timeNow && $setupData->seres_setup_status == "on");
    $deadline = $setupData->seres_setup_enddate;
}
?>

<style>
    .research-header {
        background: linear-gradient(135deg, #696cff 0%, #9092ff 100%);
        border-radius: 1.5rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(105, 108, 255, 0.2);
    }
    .research-header::after {
        content: '';
        position: absolute;
        right: -50px;
        top: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    .status-pill {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        padding: 0.5rem 1.25rem;
        border-radius: 2rem;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .research-card {
        border: none;
        border-radius: 1.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        height: 100%;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.02);
    }
    .research-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(105, 108, 255, 0.12);
        border-color: rgba(105, 108, 255, 0.2);
    }
    .research-icon {
        width: 60px;
        height: 60px;
        background: rgba(105, 108, 255, 0.08);
        color: #696cff;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
    }
    .badge-check { background-color: #71dd37; color: #fff; }
    .badge-sent { background-color: #696cff; color: #fff; }
    .badge-waiting { background-color: #ffab00; color: #fff; }
    
    .action-bar {
        background: #f8f9fa;
        padding: 1rem;
        border-top: 1px solid rgba(0,0,0,0.05);
        display: flex;
        gap: 0.5rem;
    }
    .subject-info {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #677788;
        margin-bottom: 0.5rem;
    }
</style>

<div class="container-xxl flex-grow-1">
    <!-- Header Hero -->
    <div class="research-header">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h1 class="display-6 fw-bold text-white mb-2">ศูนย์ส่งงานวิจัยในชั้นเรียน</h1>
                <p class="text-white-50 mb-4">พื้นที่สำหรับรวบรวมผลงานวิจัยเพื่อการพัฒนาการเรียนรู้ของผู้เรียน</p>
                <div class="d-flex flex-wrap gap-2">
                    <div class="status-pill">
                        <i class="bi bi-calendar-check"></i> ภาคเรียน <?= esc($setup->seres_setup_term.'/'.$setup->seres_setup_year) ?>
                    </div>
                    <?php if($is_system_on): ?>
                        <div class="status-pill text-white">
                            <i class="bi bi-unlock-fill"></i> ระบบเปิดรับงาน
                        </div>
                    <?php else: ?>
                        <div class="status-pill bg-danger text-white border-0">
                            <i class="bi bi-lock-fill"></i> ระบบปิดรับงาน
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-5 text-md-end mt-4 mt-md-0">
                <?php if($is_system_on): ?>
                    <a href="<?= base_url('research/send') ?>" class="btn btn-white btn-lg rounded-pill shadow-lg px-4 py-3 text-primary fw-bold">
                        <i class="bi bi-plus-circle-fill me-2"></i> ส่งงานวิจัยใหม่
                    </a>
                    <div class="mt-2 small text-white-50">สิ้นสุดโหมดเปิดรับ: <?= thai_date_and_time(strtotime($deadline)) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <?php if (empty($research)): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 py-5 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-journal-x display-1 text-light"></i>
                        </div>
                        <h4 class="fw-bold">ยังไม่มีข้อมูลการส่งงานวิจัย</h4>
                        <p class="text-muted">คุณยังไม่ได้ทำการส่งงานวิจัยสำหรับภาคเรียนนี้</p>
                        <?php if($is_system_on): ?>
                             <a href="<?= base_url('research/send') ?>" class="btn btn-primary rounded-pill mt-2">เริ่มส่งงานวิจัยครั้งแรก</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($research as $v_research) : ?>
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="research-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="research-icon">
                                    <i class="bi bi-mortarboard"></i>
                                </div>
                                <?php 
                                    $status = trim($v_research['seres_status']);
                                    $badgeClass = 'badge-waiting';
                                    if($status == 'ส่งแล้ว') $badgeClass = 'badge-sent';
                                    if($status == 'ตรวจแล้ว') $badgeClass = 'badge-check';
                                ?>
                                <span class="badge rounded-pill <?= $badgeClass ?> px-3 py-2"><?= $status ?></span>
                            </div>
                            
                            <div class="subject-info">
                                <i class="bi bi-tag-fill me-1"></i> <?= esc($v_research['seres_coursecode']) ?> | ชั้น ม.<?= esc($v_research['seres_gradelevel']) ?>
                            </div>
                            <h5 class="fw-bold text-dark mb-2 text-truncate-2" title="<?= esc($v_research['seres_research_name']) ?>">
                                <?= esc($v_research['seres_research_name']) ?>
                            </h5>
                            <p class="small text-muted mb-0">
                                <i class="bi bi-book me-1"></i> <?= esc($v_research['seres_namesubject']) ?>
                            </p>
                            
                            <?php if(!empty($v_research['seres_sendcomment'])): ?>
                                <div class="mt-3 p-2 bg-light rounded small text-muted text-truncate-2">
                                    "<?= esc($v_research['seres_sendcomment']) ?>"
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="action-bar justify-content-center">
                            <?php if (!empty($v_research['seres_file'])) : ?>
                                <a target="_blank" href="<?= env('upload.server.baseurl.research') . $v_research['seres_year'] . '/' . $v_research['seres_term'] . '/' . rawurlencode($v_research['seres_file']) ?>" 
                                   class="btn btn-sm btn-label-secondary flex-grow-1" title="ดูไฟล์">
                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> เปิดไฟล์
                                </a>
                            <?php endif; ?>
                            <a href="<?= site_url('research/edit-research/' . esc($v_research['seres_ID'])) ?>" 
                               class="btn btn-sm btn-label-primary flex-grow-1">
                                <i class="bi bi-pencil-square me-1"></i> แก้ไข
                            </a>
                            <button class="btn btn-sm btn-label-danger delete_research_btn" 
                                    data-seres-id="<?= esc($v_research['seres_ID']) ?>">
                                <i class="bi bi-trash me-1"></i> ลบ
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Delete Research ---
    document.querySelectorAll('.delete_research_btn').forEach(button => {
        button.addEventListener('click', function() {
            const seresId = this.dataset.seresId;
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "คุณต้องการลบงานวิจัยนี้ใช่หรือไม่ ข้อมูลและไฟล์จะถูกลบถาวร!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff3e1d',
                cancelButtonColor: '#8592a3',
                confirmButtonText: 'ยืนยันการลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'กำลังลบ...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`<?= site_url('research/delete-research/') ?>${seresId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ!',
                                text: data.message,
                                timer: 1500
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('ผิดพลาด!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('ผิดพลาด!', 'เกิดข้อผิดพลาดในการลบงานวิจัย', 'error');
                    });
                }
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
