<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'จัดการชุมนุม') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h3 class="card-title mb-0"><?= esc($title ?? 'จัดการชุมนุม') ?></h3>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#clubHelpModal">
                            <i class="bi bi-question-circle"></i> คำแนะนำ
                        </button>
                        <a href="<?= site_url('club/schedule/' . $club->club_id) ?>" class="btn btn-success btn-sm">
                            <i class="bi bi-calendar-event"></i> บันทึกเวลากิจกรรม
                        </a>
                         <a href="<?= site_url('club/objectives/' . $club->club_id) ?>" class="btn btn-secondary btn-sm">
                            <i class="bi bi-list-check"></i> จุดประสงค์กิจกรรม
                        </a>
                        <a href="<?= site_url('club/activities/' . $club->club_id) ?>" class="btn btn-info btn-sm">
                            <i class="bi bi-bar-chart-line"></i> รายงานกิจกรรม
                        </a>
                       
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editClubModal">
                            <i class="bi bi-pencil-square"></i> แก้ไขข้อมูลชุมนุม
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($club)): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ชื่อชุมนุม:</strong> <?= esc($club->club_name) ?></p>
                                <p><strong>คำอธิบาย:</strong> <?= esc($club->club_description) ?></p>
                                <p><strong>จำนวนรับสูงสุด:</strong> <?= esc($club->club_max_participants) ?></p>
                                <p><strong>ระดับชั้น:</strong> <?= esc($club->club_level) ?></p>
                                <div><strong>สถานะ:</strong>
                                    <?php if ($club->club_status === 'open'): ?>
                                        <div class="alert alert-success py-1 px-2 d-inline-block">เปิดรับสมัคร</div>
                                    <?php else: ?>
                                        <div class="alert alert-danger py-1 px-2 d-inline-block">ปิดรับสมัคร</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <p><strong>ปีการศึกษา:</strong> <?= esc($club->club_year) ?></p>
                                <p><strong>ภาคเรียน:</strong> <?= esc($club->club_trem) ?></p>
                                <p><strong>วันที่ก่อตั้ง:</strong> <?= esc($club->club_established_date) ?></p>
                            </div>
                        </div>

                        <h4 class="mt-4">สมาชิกในชุมนุม (จำนวน: <?= !empty($members) ? count($members) : 0 ?> คน)</h4>
                        <?php if (!empty($members) && is_array($members)): ?>
                            <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>รหัสนักเรียน</th>
                                        <th>ชื่อ-นามสกุล</th>
                                        <th>ชั้น</th>
                                        <th>เลขที่</th>
                                        <th>บทบาท</th>
                                        <th style="width: 200px;">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    <?php foreach ($members as $member): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= esc($member->StudentID) ?></td>
                                            <td><?= esc($member->StudentPrefix . $member->StudentFirstName . ' ' . $member->StudentLastName) ?></td>
                                            <td><?= esc($member->StudentClass) ?></td>
                                            <td><?= esc($member->StudentNumber) ?></td>
                                            <td><?= esc($member->member_role) ?></td>
                                            <td class="text-wrap">
                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#assignRoleModal"
                                                        data-studentid="<?= esc($member->StudentID) ?>" data-currentrole="<?= esc($member->member_role) ?>">
                                                    <i class="bi bi-person-badge"></i> กำหนดบทบาท
                                                </button>
                                                <!-- <form action="<?= site_url('club/removeMember/' . $club->club_id . '/' . $member->StudentID) ?>" method="post" class="d-inline remove-member-form">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="bi bi-trash"></i> ลบ
                                                    </button>
                                                </form> -->
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                ไม่พบสมาชิกในชุมนุมนี้
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="alert alert-danger">
                            ไม่พบข้อมูลชุมนุม
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Club Modal -->
<div class="modal fade" id="editClubModal" tabindex="-1" role="dialog" aria-labelledby="editClubModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url('club/update/' . $club->club_id) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="editClubModalLabel">แก้ไขข้อมูลชุมนุม</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="club_name" name="club_name" placeholder="ชื่อชุมนุม" value="<?= esc($club->club_name) ?>" required>
                        <label for="club_name">ชื่อชุมนุม</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="club_description" name="club_description" placeholder="คำอธิบายชุมนุม" style="height: 100px"><?= esc($club->club_description) ?></textarea>
                        <label for="club_description">คำอธิบายชุมนุม</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="club_max_participants" name="club_max_participants" placeholder="จำนวนรับสูงสุด (คน)" value="<?= esc($club->club_max_participants) ?>" required>
                        <label for="club_max_participants">จำนวนรับสูงสุด (คน)</label>
                    </div>
                    <div class="form-floating">
                        <select class="form-select" id="club_status" name="club_status">
                            <option value="open" <?= $club->club_status === 'open' ? 'selected' : '' ?>>เปิดรับสมัคร</option>
                            <option value="closed" <?= $club->club_status === 'closed' ? 'selected' : '' ?>>ปิดรับสมัคร</option>
                        </select>
                        <label for="club_status">สถานะชุมนุม</label>
                    </div>
                    <div class="form-floating mt-3">
                        <select class="form-select" id="club_level" name="club_level" required>
                            <option value="ม.ต้น" <?= (isset($club->club_level) && $club->club_level === 'ม.ต้น') ? 'selected' : '' ?>>ม.ต้น</option>
                            <option value="ม.ปลาย" <?= (isset($club->club_level) && $club->club_level === 'ม.ปลาย') ? 'selected' : '' ?>>ม.ปลาย</option>
                            <option value="ม.ต้น หรือ ม.ปลาย" <?= (isset($club->club_level) && $club->club_level === 'ม.ต้น หรือ ม.ปลาย') ? 'selected' : '' ?>>ม.ต้น หรือ ม.ปลาย</option>
                        </select>
                        <label for="club_level">ระดับชั้น</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Role Modal -->
<div class="modal fade" id="assignRoleModal" tabindex="-1" role="dialog" aria-labelledby="assignRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url('club/updateMemberRole/' . $club->club_id) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="assignRoleModalLabel">กำหนดบทบาทสมาชิก</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="student_id" id="modal_student_id">
                    <div class="form-floating">
                        <select class="form-select" id="member_role" name="member_role">
                            <option value="Member">สมาชิก</option>
                            <option value="Leader">หัวหน้า</option>
                        </select>
                        <label for="member_role">บทบาท</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Help Modal -->
<div class="modal fade" id="clubHelpModal" tabindex="-1" aria-labelledby="clubHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clubHelpModalLabel"><i class="bi bi-question-circle-fill me-2"></i>คำแนะนำการใช้งานระบบชุมนุม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php include('help_modal_content.php'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
                                        
                                        <?= $this->section('scripts') ?>
                                        <script>
                                            $(document).ready(function() {
                                                // When the help modal is shown, activate the correct tab for this page
                                                $('#clubHelpModal').on('show.bs.modal', function () {
                                                    var tab = new bootstrap.Tab(document.querySelector('#pills-manage-tab'));
                                                    tab.show();
                                                });

                                                // Handle Assign Role Modal data population
                                                $('#assignRoleModal').on('show.bs.modal', function (event) {
                                                    var button = $(event.relatedTarget); // Button that triggered the modal
                                                    var studentId = button.data('studentid'); // Extract info from data-* attributes
                                                    var currentRole = button.data('currentrole');
                                        
                                                    var modal = $(this);
                                                    modal.find('#modal_student_id').val(studentId);
                                                    modal.find('#member_role').val(currentRole);
                                                });
                                        
                                                // Handle Remove Member confirmation
                                                $('.remove-member-form').submit(function(e) {
                                                    e.preventDefault(); // Prevent default form submission
                                                    var form = this;
                                                    Swal.fire({
                                                        title: 'คุณแน่ใจหรือไม่?',
                                                        text: "คุณต้องการลบสมาชิกนี้ออกจากชุมนุม!",
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#3085d6',
                                                        cancelButtonColor: '#d33',
                                                        confirmButtonText: 'ใช่, ลบเลย!',
                                                        cancelButtonText: 'ยกเลิก'
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            form.submit(); // Submit the form if confirmed
                                                        }
                                                    });
                                                });
                                            });
                                        </script>
                                        <?= $this->endSection() ?>
