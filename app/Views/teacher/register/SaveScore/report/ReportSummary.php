<style>
table,
th,
td {
    border: 1px solid black;
    border-collapse: collapse;
    padding: 3px;
    font-size: 16px;
}


.p-2 {
    padding-left: 150px;
}

.float-left {
    float: left;
}

.text-center {
    text-align: center;
    font-size: 16px;
}

.center {
    text-align: center;
}

.b {
    font-weight: bold;
}
</style>
<div class="text-center b" style="padding-top: -50px;">
    <div>รายวิชา <?=$re_subjuct[0]->SubjectCode?> <?=$re_subjuct[0]->SubjectName?> ครูประจำวิชา
        <?= session('fullname'); ?></div>
    <div>ชั้นมัธยมศึกษาปีที่ <?php $sub_room = explode(".",$re_room); echo $sub_room[1];?>
        <?php $sub_year = explode("/",$re_subjuct[0]->SubjectYear);?>
        ภาคเรียนที่ <?=$sub_year[0];?> ปีการศึกษา <?=$sub_year[1];?></div>
    <div>อำเภอเมืองนครสวรรค์ จังหวัดนครสวรรค์ สังกัดองค์การบริหารส่วนจังหวัดนครสวรรค์</div>
</div>

<table class="table" style="width: 100%;margin-top: 10px;">
    <thead>
        <tr>
            <th colspan="4">ข้อมูลนักเรียน</th>
            <th colspan="7">การประเมินผลการเรียน</th>
        </tr>
        <tr>
            <th rowspan="2" style="width: 5%">เลขที่</th>
            <th rowspan="2" style="width: 4%">เลขประจำตัวนักเรียน</th>
            <th rowspan="2" style="width: 30%">ชื่อ - นามสกุล</th>
            <th rowspan="2" style="width: 8%">เวลาเรียน (<?=$re_subjuct[0]->SubjectHour?>)</th>
            <?php 
                $sum_scoer = 0;
                foreach ($set_score as $key => $v_set_score): 
                    $sum_scoer += $v_set_score->regscore_score;
                ?>
            <th class="h6" style="width: 7.2%">
                <?=$v_set_score->regscore_namework?>
            </th>
            <?php endforeach; ?>
            <th class="h6" style="width: 9%">คะแนนรวม</th>
            <th rowspan="2" style="width: 6%">เกรด</th>
            <th rowspan="2" style="width: 3%">สถานะ<br>นักเรียน</th>
        </tr>
        <tr>
            <?php 
                $sum_scoer = 0;
                foreach ($set_score as $key => $v_set_score): 
                    $sum_scoer += $v_set_score->regscore_score;
                ?>
            <th class="h6">
                <?=$v_set_score->regscore_score?>
            </th>
            <?php endforeach; ?>
            <th class="h6"><?=$sum_scoer?></th>
        </tr>
    </thead>
    <?php if($CheckPrint == ''): ?>
    <tbody>
        <?php foreach ($check_student as $key => $v_check_student) : ?>
            <?php if($v_check_student->Grade_Type != ""): ?>
            <tr>
                <td class="align-middle text-center"><?=$v_check_student->StudentNumber?></td>
                <td class="align-middle text-center"><?=$v_check_student->StudentCode?></td>
                <td class="align-middle">
                    <?=$v_check_student->StudentPrefix?><?=$v_check_student->StudentFirstName?>
                    <?=$v_check_student->StudentLastName?>
                </td>
                <td colspan="7" class="center" style="color: #dc3545 !important;">
                ** นักเรียน เรียนซ้ำ **
                </td>
                <td class="align-middle text-center">
                    <?php 
                    if($v_check_student->StudentBehavior == "ปกติ"){ 
                        echo '<span class="text-success">'.$v_check_student->StudentBehavior.'</span>';
                    }else{
                        echo '<span class="text-danger">'.$v_check_student->StudentBehavior.'</span>';
                    }
                    ?>
                </td>
            </tr>
            <?php else:?>
            <tr>
                <td class="center"><?=$v_check_student->StudentNumber?></td>
                <td class="center"><?=$v_check_student->StudentCode?></td>
                <td><?=$v_check_student->StudentPrefix?><?=$v_check_student->StudentFirstName?>
                    <?=$v_check_student->StudentLastName?>
                </td>
                <td class="center"><?=$v_check_student->StudyTime?></td>
                <?php 
                $s = !empty($v_check_student->Score100) ? explode("|", $v_check_student->Score100) : [];
                foreach ($set_score as $key => $v_set_score): 
                ?>
                <td class="center">
                    <?=isset($s[$key]) ? $s[$key] : "0"?>
                </td>
                <?php endforeach; ?>
                <td class="center"><?=@array_sum($s)?></td>
                <td class="center">
                    <?php if((80*intVal($v_check_student->StudyTime))/100 >= $re_subjuct[0]->SubjectHour || $v_check_student->StudyTime == ""): ?>
                    มส
                    <?php else: ?>
                    <?=$v_check_student->Grade?>
                    <?php endif; ?>
                </td>
                <td class="center"><?=$v_check_student->StudentBehavior?></td>
            </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
    <?php else:?>
    <tbody>
        <?php foreach ($check_student1 as $key => $v_check_student) : 
            if($v_check_student->Grade_Type != ""): ?>
        <tr>
            <td class="align-middle text-center"><?=$v_check_student->StudentNumber?></td>
            <td class="align-middle text-center"><?=$v_check_student->StudentCode?></td>
            <td class="align-middle">
                <?=$v_check_student->StudentPrefix?><?=$v_check_student->StudentFirstName?>
                <?=$v_check_student->StudentLastName?>
            </td>
            <td colspan="7" class="center" style="color: #dc3545 !important;">
            ** นักเรียน เรียนซ้ำ **
            </td>
            <td class="align-middle text-center">
                <?php 
                if($v_check_student->StudentBehavior == "ปกติ"){ 
                    echo '<span class="text-success">'.$v_check_student->StudentBehavior.'</span>';
                }else{
                    echo '<span class="text-danger">'.$v_check_student->StudentBehavior.'</span>';
                }
                ?>
            </td>
        </tr>
        <?php else:?>
        <tr>
            <td class="center"><?=$v_check_student->StudentNumber?></td>
            <td class="center"><?=$v_check_student->StudentCode?></td>
            <td><?=$v_check_student->StudentPrefix?><?=$v_check_student->StudentFirstName?>
                <?=$v_check_student->StudentLastName?>
            </td>
            <td class="center"><?=$v_check_student->StudyTime?></td>
            <?php 
            $s = !empty($v_check_student->Score100) ? explode("|", $v_check_student->Score100) : [];
            foreach ($set_score as $key => $v_set_score): 
            ?>
            <td class="center">
                <?=isset($s[$key]) ? $s[$key] : "0"?>
            </td>
            <?php endforeach; ?>
            <td class="center"><?=@array_sum($s)?></td>
            <td class="center">
                <?php if((80*intVal($v_check_student->StudyTime))/100 >= $re_subjuct[0]->SubjectHour || $v_check_student->StudyTime == ""): ?>
                มส
                <?php else: ?>
                <?=$v_check_student->Grade?>
                <?php endif; ?>
            </td>
            <td class="center"><?=$v_check_student->StudentBehavior?></td>
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
    <?php endif;?>


</table>