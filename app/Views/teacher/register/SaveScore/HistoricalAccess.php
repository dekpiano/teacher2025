<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid historical-access-page">
    <div class="card border-0 shadow-sm access-hero mb-4">
        <div class="card-body d-flex flex-column flex-md-row align-items-md-center gap-3 p-4">
            <div class="access-icon"><i class="bi bi-shield-lock-fill"></i></div>
            <div>
                <h3 class="mb-1">จัดการสิทธิ์แก้ไขคะแนนย้อนหลัง</h3>
                <p class="mb-0">อนุมัติหรือเพิกถอนสิทธิ์เป็นรายครูและรายปี/ภาคเรียน การเพิกถอนมีผลทันที</p>
            </div>
            <a href="<?= site_url('assessment/save-score-normal') ?>" class="btn btn-light ms-md-auto text-nowrap">
                <i class="bi bi-arrow-left me-1"></i>กลับหน้าบันทึกคะแนน
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('message')) : ?>
        <div class="alert alert-success d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('message')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="alert alert-info border-0 d-flex align-items-start rounded-3">
        <i class="bi bi-info-circle-fill me-3 fs-5"></i>
        <div>ครูจะเห็นและแก้ไขได้เฉพาะรายวิชาที่ตนรับผิดชอบ เมื่อได้รับอนุมัติสำหรับปี/ภาคเรียนเดียวกันเท่านั้น</div>
    </div>

    <div class="card border-0 shadow-sm access-table-card">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-person-lock me-2 text-primary"></i>สิทธิ์ตามครูและปี/ภาคเรียน</h5>
            <span class="badge rounded-pill bg-label-primary"><?= count($pairs) ?> รายการ</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">ครูผู้สอน</th>
                        <th>ปี/ภาคเรียน</th>
                        <th>สิทธิ์ปัจจุบัน</th>
                        <th>ประวัติการดำเนินการ</th>
                        <th class="text-end pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pairs)) : ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">ไม่พบรายวิชาในปีการศึกษาย้อนหลัง</td></tr>
                    <?php else : ?>
                        <?php foreach ($pairs as $pair) : ?>
                            <?php
                                $teacherId = (string) $pair['TeacherID'];
                                $registerYear = (string) $pair['RegisterYear'];
                                $grantKey = $teacherId . '|' . $registerYear;
                                $grant = $grant_states[$grantKey] ?? null;
                                $isEnabled = (int) ($grant['is_enabled'] ?? 0) === 1;
                                $periodParts = explode('/', $registerYear, 2);
                                $periodLabel = count($periodParts) === 2
                                    ? 'ภาคเรียน ' . $periodParts[0] . ' · ปีการศึกษา ' . $periodParts[1]
                                    : $registerYear;
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold text-dark"><?= esc($teacher_names[$teacherId] ?? 'ไม่พบชื่อครู') ?></div>
                                    <small class="text-muted">รหัส <?= esc($teacherId) ?></small>
                                </td>
                                <td class="text-nowrap"><span class="period-pill"><?= esc($periodLabel) ?></span></td>
                                <td>
                                    <?php if ($isEnabled) : ?>
                                        <span class="badge bg-success-subtle text-success-emphasis rounded-pill"><i class="bi bi-unlock me-1"></i>อนุมัติให้แก้ไข</span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill"><i class="bi bi-lock me-1"></i>ดูได้อย่างเดียว</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
                                    <?php if ($isEnabled && !empty($grant['granted_at'])) : ?>
                                        อนุมัติโดย <?= esc($grant['granted_by']) ?> · <?= esc($grant['granted_at']) ?>
                                    <?php elseif (!empty($grant['revoked_at'])) : ?>
                                        เพิกถอนโดย <?= esc($grant['revoked_by']) ?> · <?= esc($grant['revoked_at']) ?>
                                    <?php else : ?>
                                        ยังไม่เคยอนุมัติ
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <form method="post" action="<?= site_url('assessment/historical-score-access/update') ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="teacher_id" value="<?= esc($teacherId) ?>">
                                        <input type="hidden" name="register_year" value="<?= esc($registerYear) ?>">
                                        <input type="hidden" name="enabled" value="<?= $isEnabled ? '0' : '1' ?>">
                                        <button type="submit" class="btn btn-sm <?= $isEnabled ? 'btn-outline-danger' : 'btn-primary' ?>"
                                            onclick="return confirm('<?= $isEnabled ? 'ยืนยันเพิกถอนสิทธิ์แก้ไขคะแนนย้อนหลัง?' : 'ยืนยันปลดล็อกคะแนนย้อนหลังให้ครูและปี/ภาคเรียนนี้?' ?>')">
                                            <i class="bi <?= $isEnabled ? 'bi-lock-fill' : 'bi-unlock-fill' ?> me-1"></i><?= $isEnabled ? 'เพิกถอนสิทธิ์' : 'อนุมัติแก้ไข' ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .historical-access-page .access-hero {
        color: #fff;
        background: linear-gradient(115deg, #4f46e5 0%, #6d5dfc 55%, #8b5cf6 100%);
        border-radius: 1rem;
    }
    .access-hero h3 { color: #fff; font-weight: 750; }
    .access-hero p { color: rgba(255,255,255,.84); }
    .access-icon { width: 3.25rem; height: 3.25rem; display: grid; place-items: center; border-radius: .9rem; background: rgba(255,255,255,.18); font-size: 1.4rem; }
    .access-table-card { border-radius: 1rem; overflow: hidden; }
    .access-table-card thead th { background: #f7f8fc; color: #65748b; font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; white-space: nowrap; }
    .access-table-card tbody td { border-color: #eef1f6; }
    .period-pill { display: inline-block; padding: .42rem .7rem; border: 1px solid #e3e8f1; border-radius: .65rem; background: #f8faff; color: #394c67; font-size: .85rem; }
</style>
<?= $this->endSection() ?>
