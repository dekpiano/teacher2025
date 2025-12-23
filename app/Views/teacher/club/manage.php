<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'จัดการชุมนุม') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .manage-header {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }
    .stat-card {
        border: none;
        border-radius: 1rem;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .table-card {
        border-radius: 1rem;
        border: none;
        overflow: hidden;
    }
    .nav-pills .nav-link {
        border-radius: 0.5rem;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
    }
    .badge-status {
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
    }
</style>

<div class="container-fluid py-2">
    <!-- Breadcrumb & Header -->
    <div class="manage-header shadow-sm">
        <div class="row align-items-center text-start">
            <div class="col-md-7">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-light mb-2">
                        <li class="breadcrumb-item"><a href="<?= site_url('home') ?>" class="text-white opacity-75">หน้าหลัก</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('club') ?>" class="text-white opacity-75">ชุมนุม</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">จัดการชุมนุม</li>
                    </ol>
                </nav>
                <h2 class="fw-bold mb-1 text-white"><?= esc($club->club_name) ?></h2>
                <p class="mb-0 opacity-75"><i class="bi bi-calendar3 me-2"></i>ปีการศึกษา <?= esc($club->club_year) ?> ภาคเรียนที่ <?= esc($club->club_trem) ?></p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0 mx-auto">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <button type="button" class="btn btn-white rounded-pill px-3 shadow-sm text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#editClubModal">
                        <i class="bi bi-pencil-square me-1"></i> แก้ไขข้อมูล
                    </button>
                    <button type="button" class="btn btn-outline-light rounded-circle p-2 border-2" data-bs-toggle="modal" data-bs-target="#clubHelpModal">
                        <i class="bi bi-question-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($club)): ?>
        <!-- Quick Stats -->
        <div class="row g-3 mb-4 text-start">
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <div class="text-muted small">สมาชิกปัจจุบัน</div>
                                <h4 class="fw-bold mb-0"><?= !empty($members) ? count($members) : 0 ?> / <?= esc($club->club_max_participants) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-info bg-opacity-10 text-info me-3">
                                <i class="bi bi-layers-fill"></i>
                            </div>
                            <div>
                                <div class="text-muted small">ระดับชั้น</div>
                                <h4 class="fw-bold mb-0"><?= esc($club->club_level) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-toggle-on"></i>
                            </div>
                            <div>
                                <div class="text-muted small">สถานะชุมนุม</div>
                                <span class="badge badge-status bg-<?= $club->club_status === 'open' ? 'success' : 'danger' ?> bg-opacity-10 text-<?= $club->club_status === 'open' ? 'success' : 'danger' ?>">
                                    <?= $club->club_status === 'open' ? 'เปิดรับสมัคร' : 'ปิดรับสมัคร' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-warning bg-opacity-10 text-warning me-3">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <div class="text-muted small">วันที่ก่อตั้ง</div>
                                <h4 class="fw-bold mb-0 small"><?= date('d M Y', strtotime($club->club_established_date)) ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Menu -->
        <div class="card table-card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 text-start">
                    <h5 class="fw-bold mb-0 text-primary">เมนูจัดการกิจกรรม</h5>
                    <div class="d-flex gap-2">
                        <a href="<?= site_url('club/schedule/' . $club->club_id) ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-calendar-event me-2"></i> เช็คชื่อนักเรียน
                        </a>
                        <a href="<?= site_url('club/objectives/' . $club->club_id) ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-list-check me-2"></i> ประเมินผล
                        </a>
                        <a href="<?= site_url('club/activities/' . $club->club_id) ?>" class="btn btn-outline-info rounded-pill px-4">
                            <i class="bi bi-bar-chart-line me-2"></i> รายงานผล
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-start text-nowrap">
                            <tr>
                                <th class="ps-4" style="width: 80px;">ลำดับ</th>
                                <th>รหัสนักเรียน</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>ชั้น / เลขที่</th>
                                <th>บทบาท</th>
                                <th class="text-center" style="width: 250px;">จัดการสมาชิก</th>
                            </tr>
                        </thead>
                        <tbody class="text-start">
                            <?php if (!empty($members)): ?>
                                <?php $i = 1; foreach ($members as $member): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted"><?= $i++ ?></td>
                                        <td><span class="badge bg-light text-dark font-monospace"><?= esc($member->StudentID) ?></span></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-3"><?= esc($member->StudentClass) ?></span>
                                            <span class="ms-1 text-muted small">เลขที่ <?= esc($member->StudentNumber) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($member->member_role === 'Leader'): ?>
                                                <span class="badge bg-warning text-dark rounded-pill px-3"><i class="bi bi-star-fill me-1"></i> หัวหน้า</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted rounded-pill px-3">สมาชิก</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3" 
                                                        data-bs-toggle="modal" data-bs-target="#assignRoleModal"
                                                        data-studentid="<?= esc($member->StudentID) ?>" 
                                                        data-currentrole="<?= esc($member->member_role) ?>">
                                                    <i class="bi bi-shield-lock me-1"></i> บทบาท
                                                </button>
                                                <form action="<?= site_url('club/removeMember/' . $club->club_id . '/' . $member->StudentID) ?>" method="post" class="remove-member-form">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                        <i class="bi bi-person-x me-1"></i> ลบ
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-3"><i class="bi bi-person-dash" style="font-size: 3rem;"></i></div>
                                        <p class="mb-0">ยังไม่มีนักเรียนสมัครเข้าเป็นสมาชิกในชุมนุมนี้</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modals -->
<!-- Edit Club Modal -->
<div class="modal fade" id="editClubModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= site_url('club/update/' . $club->club_id) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold text-white"><i class="bi bi-pencil-square me-2"></i>แก้ไขข้อมูลชุมนุม</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control border-0 bg-light" id="club_name" name="club_name" value="<?= esc($club->club_name) ?>" required>
                        <label for="club_name">ชื่อชุมนุม</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control border-0 bg-light" id="club_description" name="club_description" style="height: 100px"><?= esc($club->club_description) ?></textarea>
                        <label for="club_description">คำอธิบาย</label>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="form-floating text-start">
                                <input type="number" class="form-control border-0 bg-light" id="club_max_participants" name="club_max_participants" 
                                       value="<?= esc($club->club_max_participants) ?>" min="<?= !empty($members) ? count($members) : 0 ?>" required>
                                <label for="club_max_participants">จำนวนรับ (คน)</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating text-start">
                                <select class="form-select border-0 bg-light" id="club_status" name="club_status">
                                    <option value="open" <?= $club->club_status === 'open' ? 'selected' : '' ?>>เปิดรับสมัคร</option>
                                    <option value="closed" <?= $club->club_status === 'closed' ? 'selected' : '' ?>>ปิดรับสมัคร</option>
                                </select>
                                <label for="club_status">สถานะ</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating">
                        <select class="form-select border-0 bg-light" id="club_level" name="club_level" required>
                            <option value="ม.ต้น" <?= ($club->club_level === 'ม.ต้น') ? 'selected' : '' ?>>ม.ต้น</option>
                            <option value="ม.ปลาย" <?= ($club->club_level === 'ม.ปลาย') ? 'selected' : '' ?>>ม.ปลาย</option>
                            <option value="ม.ต้น และ ม.ปลาย" <?= ($club->club_level === 'ม.ต้น และ ม.ปลาย') ? 'selected' : '' ?>>ม.ต้น และ ม.ปลาย</option>
                        </select>
                        <label for="club_level">ระดับชั้น</label>
                    </div>
                    <div class="mt-2 small text-warning"><i class="bi bi-info-circle me-1"></i> จำนวนรับต้องไม่น้อยกว่าสมาชิกปัจจุบัน (<?= !empty($members) ? count($members) : 0 ?> คน)</div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Role Modal -->
<div class="modal fade" id="assignRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="<?= site_url('club/updateMemberRole/' . $club->club_id) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-badge me-2"></i>บทบาทสมาชิก</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <input type="hidden" name="student_id" id="modal_student_id">
                    <label class="form-label small fw-bold text-muted">เลือกบทบาทในทีม</label>
                    <select class="form-select border-0 bg-light py-2" id="member_role" name="member_role">
                        <option value="Member">สมาชิกทั่วไป</option>
                        <option value="Leader">หัวหน้าชุมนุม</option>
                    </select>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold">บันทึกบทบาท</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Help Modal -->
<div class="modal fade" id="clubHelpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="bi bi-question-circle me-2"></i>ศูนย์ช่วยเหลือ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <?php include('help_modal_content.php'); ?>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">เข้าใจแล้ว</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#clubHelpModal').on('show.bs.modal', function () {
            setTimeout(function() {
                var tabId = document.querySelector('#pills-manage-tab');
                if(tabId) {
                    var tab = new bootstrap.Tab(tabId);
                    tab.show();
                }
            }, 300);
        });

        $('#assignRoleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var studentId = button.data('studentid');
            var currentRole = button.data('currentrole');
            var modal = $(this);
            modal.find('#modal_student_id').val(studentId);
            modal.find('#member_role').val(currentRole);
        });

        $('.remove-member-form').submit(function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'ยืนยันการลบสมาชิก?',
                text: "นักเรียนจะถูกคัดออกจากชุมนุมทันที และต้องสมัครใหม่หากต้องการกลับเข้าชุมนุม",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ยืนยัน ลบออกจากทีม',
                cancelButtonText: 'ยกเลิก',
                customClass: {
                    confirmButton: 'btn btn-danger rounded-pill px-4',
                    cancelButton: 'btn btn-light rounded-pill px-4 ms-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
