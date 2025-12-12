<?= $this->extend('teacher/layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'เช็คชื่อโฮมรูม') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>เช็คชื่อโฮมรูม ห้อง <?= esc($teacher->Reg_Class ?? '') ?></h4>
                    <p>วันที่: <?= thai_date(strtotime(date('Y-m-d'))) ?></p>
                </div>
                <div class="card-body">
                    <form action="<?= esc($Action) ?>" method="post">
                        <input type="hidden" name="chk_home_teacher" value="<?= esc(session('person_id')) ?>">
                        <input type="hidden" name="chk_home_room" value="<?= esc($teacher->Reg_Class ?? '') ?>">
                        <!-- Add other necessary hidden fields like term and year -->

                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>เลขที่</th>
                                    <th>ชื่อ-สกุล</th>
                                    <th>มา</th>
                                    <th>ขาด</th>
                                    <th>ลา</th>
                                    <th>สาย</th>
                                    <th>กิจกรรม</th>
                                    <th>หนี</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $statuses = ['ma', 'khad', 'la', 'sahy', 'kid', 'hnee'];
                                $existing_students = [];
                                if(isset($existingRecord)){
                                    foreach($statuses as $status){
                                        $codes = explode('|', $existingRecord['chk_home_'.$status]);
                                        foreach($codes as $code){
                                            $existing_students[$code] = ucfirst($status);
                                        }
                                    }
                                }
                                ?>

                                <?php foreach ($studentAdd as $student): ?>
                                    <tr>
                                        <td><?= esc($student->StudentNumber) ?></td>
                                        <td><?= esc($student->StudentPrefix . $student->StudentFirstName . ' ' . $student->StudentLastName) ?></td>
                                        
                                        <?php 
                                        $current_status = $existing_students[$student->StudentCode] ?? 'Ma'; // Default to 'Ma'
                                        ?>

                                        <td><input type="radio" name="status[<?= esc($student->StudentCode) ?>]" value="มา" <?= ($current_status == 'Ma') ? 'checked' : '' ?>></td>
                                        <td><input type="radio" name="status[<?= esc($student->StudentCode) ?>]" value="ขาด" <?= ($current_status == 'Khad') ? 'checked' : '' ?>></td>
                                        <td><input type="radio" name="status[<?= esc($student->StudentCode) ?>]" value="ลา" <?= ($current_status == 'La') ? 'checked' : '' ?>></td>
                                        <td><input type="radio" name="status[<?= esc($student->StudentCode) ?>]" value="สาย" <?= ($current_status == 'Sahy') ? 'checked' : '' ?>></td>
                                        <td><input type="radio" name="status[<?= esc($student->StudentCode) ?>]" value="กิจกรรม" <?= ($current_status == 'Kid') ? 'checked' : '' ?>></td>
                                        <td><input type="radio" name="status[<?= esc($student->StudentCode) ?>]" value="หนี" <?= ($current_status == 'Hnee') ? 'checked' : '' ?>></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><?= esc($ButtonName) ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
