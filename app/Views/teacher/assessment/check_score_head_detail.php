<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .teacher-profile-header {
        background: linear-gradient(135deg, #696cff 0%, #a3a4ff 100%);
        border-radius: 15px;
        padding: 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .subject-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
        transition: transform 0.2s;
    }
    .subject-card:hover {
        transform: translateY(-3px);
    }
    .progress {
        height: 12px;
        border-radius: 10px;
        background-color: #f0f2f4;
    }
    .progress-bar {
        border-radius: 10px;
    }
    .stat-box {
        background: #fcfcfd;
        border-radius: 10px;
        padding: 1rem;
        border: 1px solid #f0f2f4;
    }
    .badge-class {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
</style>

<div class="container-xxl flex-grow-1">
    <!-- Teacher Header -->
    <div class="teacher-profile-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center">
            <div class="avatar avatar-xl me-3 border-3 border-white rounded-circle overflow-hidden shadow">
                 <img src="https://personnel.skj.ac.th/uploads/admin/Personnal/<?= esc($teacher_info->pers_img ?? '') ?>" 
                      alt="Avatar" 
                      class="h-100 w-100 object-fit-cover"
                      onerror="this.onerror=null;this.src='https://placehold.co/100x100/EFEFEF/AAAAAA&text=No+Image';">
            </div>
            <div>
                <h4 class="mb-1 fw-bold text-white"><?= esc($teacher_info->pers_prefix . $teacher_info->pers_firstname . ' ' . $teacher_info->pers_lastname) ?></h4>
                <div class="d-flex align-items-center text-white-50 fs-small">
                    <i class="bi bi-bar-chart-line me-2"></i>
                    <span>ตรวจสอบความคืบหน้าการส่งผลการเรียน</span>
                </div>
            </div>
        </div>
        <div class="bg-white p-2 rounded-3 shadow-sm d-flex align-items-center">
            <i class="bi bi-filter-circle me-2 text-primary"></i>
            <select id="FilterYearTerm" class="form-select border-0 bg-transparent fw-bold" style="width: auto;">
                <?php foreach ($CheckYear as $y): ?>
                <option value="<?= esc($y['year'].'-'.$y['term']) ?>" <?= ($current_year == $y['year'] && $current_term == $y['term']) ? 'selected' : '' ?>>
                    ภาคเรียนที่ <?= esc($y['term']) ?> / <?= esc($y['year']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Subjects Progress -->
    <div class="row">
        <?php if (empty($subjects)): ?>
            <div class="col-12 text-center py-5">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-8867280-7265556.png" height="200" alt="No data">
                <h5 class="mt-4 text-muted">ไม่พบข้อมูลรายวิชาที่สอนในภาคเรียนนี้</h5>
            </div>
        <?php else: ?>
            <?php foreach ($subjects as $subject): ?>
                <?php 
                    $percent = ($subject->total_students > 0) ? round(($subject->graded_students / $subject->total_students) * 100) : 0;
                    $color = 'bg-danger';
                    if ($percent >= 100) $color = 'bg-success';
                    elseif ($percent >= 50) $color = 'bg-warning';
                ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card subject-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <span class="badge bg-label-primary badge-class mb-2">ชั้น <?= esc($subject->classes) ?></span>
                                    <small class="text-muted fw-bold d-block"><?= esc($subject->SubjectCode) ?></small>
                                </div>
                            </div>
                            <h6 class="fw-bold text-dark mb-3" style="min-height: 2.5rem;"><?= esc($subject->SubjectName) ?></h6>
                            
                            <div class="stat-box mb-3">
                                <div class="row text-center">
                                    <div class="col-6 border-end">
                                        <small class="text-muted d-block">นักเรียนทั้งหมด</small>
                                        <span class="h5 fw-bold mb-0"><?= esc($subject->total_students) ?></span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">ตัดเกรดแล้ว</small>
                                        <span class="h5 fw-bold mb-0 text-success"><?= esc($subject->graded_students) ?></span>
                                    </div>
                                </div>
                            </div>

                            <button type="button" 
                                    class="btn btn-primary w-100 rounded-pill btn-view-students"
                                    data-teacher="<?= esc($teacher_info->pers_id) ?>"
                                    data-subject-id="<?= esc($subject->SubjectID) ?>"
                                    data-subject-name="<?= esc($subject->SubjectCode . ' ' . $subject->SubjectName) ?>"
                                    data-class="all"
                                    data-class-display="<?= esc($subject->classes) ?>"
                                    data-year-term="<?= esc($year_term) ?>">
                                <i class="bi bi-people-fill me-2"></i> ดูคะแนนนักเรียน
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal for Student Scores -->
<div class="modal fade" id="ModalStudentScores" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary py-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm bg-white rounded-circle me-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-person-badge text-primary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0" id="ModalTitle">รายวิชา</h5>
                        <small class="text-white" id="ModalSubtitle">ชั้น ม.X/X</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0" id="TableStudentScores">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center py-3" style="width: 60px;">ที่</th>
                                <th class="py-3">ชื่อ-นามสกุล</th>
                                <th class="text-center py-3" style="width: 80px;">ชั้น</th>
                                <th class="text-center py-3" style="width: 100px;">ก่อนกลาง</th>
                                <th class="text-center py-3" style="width: 100px;">กลางภาค</th>
                                <th class="text-center py-3" style="width: 100px;">หลังกลาง</th>
                                <th class="text-center py-3" style="width: 100px;">ปลายภาค</th>
                                <th class="text-center py-3" style="width: 80px;">รวม</th>
                                <th class="text-center py-3" style="width: 80px;">เกรด</th>
                            </tr>
                        </thead>
                        <tbody id="ListStudentScores">
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter year/term
    const filterElem = document.getElementById('FilterYearTerm');
    if (filterElem) {
        filterElem.addEventListener('change', function() {
            window.location.href = '<?= site_url('assessment-head/check-score-detail/' . ($teacher_info->pers_id ?? '')) ?>/' + this.value;
        });
    }

    // Modal Student Scores
    const studentModalElem = document.getElementById('ModalStudentScores');
    const studentModal = new bootstrap.Modal(studentModalElem);

    document.querySelectorAll('.btn-view-students').forEach(btn => {
        btn.addEventListener('click', function() {
            const teacherId = this.getAttribute('data-teacher');
            const subjectId = this.getAttribute('data-subject-id');
            const subjectName = this.getAttribute('data-subject-name');
            const className = this.getAttribute('data-class');
            const classDisplay = this.getAttribute('data-class-display');
            const yearTerm = this.getAttribute('data-year-term');

            document.getElementById('ModalTitle').innerText = subjectName;
            document.getElementById('ModalSubtitle').innerText = 'รายชื่อนักเรียน ชั้น ' + classDisplay;
            document.getElementById('ListStudentScores').innerHTML = '<tr><td colspan="9" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">กำลังโหลดข้อมูล...</p></td></tr>';

            studentModal.show();

            // Fetch data via AJAX
            const formData = new URLSearchParams();
            formData.append('teacher_id', teacherId);
            formData.append('subject_id', subjectId);
            formData.append('class', className);
            formData.append('year_term', yearTerm);

            fetch('<?= site_url('assessment-head/ajax-get-student-scores') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData.toString()
            })
            .then(response => response.json())
            .then(data => {
                let html = '';
                if (!data || data.length === 0) {
                    html = '<tr><td colspan="9" class="text-center py-4">ไม่พบข้อมูลนักเรียน</td></tr>';
                } else {
                    data.forEach(item => {
                        const scores = item.Score100 ? item.Score100.split('|') : [];
                        const p1 = scores[0] || '-';
                        const p2 = scores[1] || '-';
                        const p3 = scores[2] || '-';
                        const p4 = scores[3] || '-';
                        
                        let total = 0;
                        scores.forEach(s => { if(!isNaN(s) && s !== '' && s !== null) total += parseFloat(s); });

                        let gradeColor = 'secondary';
                        if (item.Grade !== "" && item.Grade !== null) {
                            const g = item.Grade;
                            if (!isNaN(g)) {
                                if (parseFloat(g) >= 1) gradeColor = 'success';
                                else gradeColor = 'danger';
                            } else {
                                gradeColor = 'warning';
                            }
                        }

                        html += `
                            <tr>
                                <td class="text-center fw-bold text-primary">${item.StudentNumber || '-'}</td>
                                <td>
                                    <div class="fw-bold">${item.StudentPrefix || ''}${item.StudentFirstName || ''} ${item.StudentLastName || ''}</div>
                                    <small class="text-muted">${item.StudentID || ''}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-secondary">${item.RegisterClass || '-'}</span>
                                </td>
                                <td class="text-center fw-semibold">${p1}</td>
                                <td class="text-center fw-semibold">${p2}</td>
                                <td class="text-center fw-semibold">${p3}</td>
                                <td class="text-center fw-semibold">${p4}</td>
                                <td class="text-center fw-bold text-primary fs-5">${(item.Grade !== "" && item.Grade !== null) ? total : '-'}</td>
                                <td class="text-center">
                                    ${(item.Grade !== "" && item.Grade !== null) ? `<span class="badge bg-${gradeColor} shadow-none" style="width: 45px;">${item.Grade}</span>` : '<span class="text-muted">-</span>'}
                                </td>
                            </tr>
                        `;
                    });
                }
                document.getElementById('ListStudentScores').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('ListStudentScores').innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>';
            });
        });
    });
});
</script>

<?= $this->endSection() ?>
