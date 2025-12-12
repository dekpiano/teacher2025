<style>
.table tbody tr td {
    border: 1px solid;
}

.table {
    width: 100%;
    border-collapse: collapse;
    border: 2px solid;
}

.p-2 {
    padding-left: 150px;
}

.float-left {
    float: left;
}

.text-center {
    text-align: center;
    font-size: 28px;
}

.b {
    font-weight: bold;
}
</style>
<table>
    <tr>
        <th style="padding-left: 900px;padding-top: -20px;font-size: 24px;">ปถ.05:เรียนซ้ำ</th>
    </tr>
</table>
<div class="text-center b">
    <img src="public/uploads/banner/LogoSKJ_2.png" style="width: 15%;" alt="" srcset="">
    <div>แบบบันทึกผลการพัฒนาคุณภาพผู้เรียนรายวิชา</div>
    <div>โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์</div>
    <div>อำเภอเมืองนครสวรรค์ จังหวัดนครสวรรค์ สังกัดองค์การบริหารส่วนจังหวัดนครสวรรค์</div>
</div>

<table class="b" style="width: 100%;margin-top: 10px;">
    <tbody>
        <tr>
            <?php $sub_year = explode("/",$CheckRepeat[0]->onoff_year);?>
            <td style="width: 50%;text-align: right;padding-right: 10px;">ภาคเรียนที่ <?=$sub_year[0];?></td>
            <td style="padding-left: 10px;">ปีการศึกษา <?=$sub_year[1];?></td>
        </tr>
        <tr>
            <td style="width: 50%;text-align: right;padding-right: 10px;">ชั้นมัธยมศึกษาปีที่
                <?php $sub_room = explode(".",$re_room); //echo $sub_room[1];?></td>
            <td style="padding-left: 10px;">
                ระดับมัธยมศึกษา<?php 
            $sub_level = explode("/",$sub_room[1]);
           echo $sub_level[0] >= 4?"ตอนปลาย":"ตอนต้น";
            ?>
            </td>
        </tr>
    </tbody>
</table>

<table class="b" style="width: 100%;margin-top: 30px;">
    <tbody>
        <tr>
            <td>กลุ่มสาระการเรียนรู้ <?php $FirstGroup = explode("/",$re_subjuct[0]->FirstGroup); echo $FirstGroup[1];?>
            </td>
            <td>สาระการเรียนรู้<?php $SubjectType = explode("/",$re_subjuct[0]->SubjectType); echo $SubjectType[1];?>
            </td>
        </tr>
        <tr>
            <td>รายวิชา <?=$re_subjuct[0]->SubjectName?></td>
            <td>รหัสวิชา <?=$re_subjuct[0]->SubjectCode?></td>
        </tr>
        <tr>
            <td>เวลาเรียน <?=$re_subjuct[0]->SubjectHour?> ชั่วโมงต่อภาคเรียน</td>
            <td>เวลาเรียน <?=($re_subjuct[0]->SubjectHour)/20?> ชั่วโมงต่อสัปดาห์</td>
        </tr>
    </tbody>
</table>

<table style="width: 100%;margin-top: 10px;">
    <tbody>
        <tr>
            <td style="width: 50px"> </td>
            <td style="width: 100px">ครูผู้สอน </td>
            <td><?=session()->get('fullname');?></td>
        </tr>
        <tr>
            <td> </td>
            <td>ครูที่ปรึกษา</td>
            <td>
                <?php if($re_teacher != ""):
                foreach ($re_teacher as $key => $v_re_teacher): 
                    echo $v_re_teacher->pers_prefix.$v_re_teacher->pers_firstname.' '.$v_re_teacher->pers_lastname?>
                &nbsp;&nbsp;
                <?php endforeach; endif; ?>
            </td>
        </tr>
    </tbody>
</table>

<?php 
$count_all = 0;
$grade4 = 0;$grade35 = 0;$grade3 = 0;$grade25 = 0;$grade2 = 0;$grade15 = 0;$grade1 = 0;$grade0 = 0;$gradeR=0;$gradeMS=0;


foreach ($check_student as $key => $v_check_student) {
   

    if($v_check_student->Grade_Type != ''){
        $count_all += 1;
        if($v_check_student->Grade == "มส"){
            $gradeMS += 1;
       }else if($v_check_student->Grade == "ร"){
        $gradeR += 1;
        }else{
            $sum = floatval($v_check_student->Grade);
            if (($sum > 100) || ($sum < 0)) {
                $grade_error = "ไม่สามารถคิดเกรดได้ คะแนนเกิน";
                } else if (($sum == 4)) {
                    $grade4 += 1;
                } else if (($sum == 3.5)) {
                    $grade35 += 1;
                } else if (($sum == 3)) {
                    $grade3 += 1;
                } else if (($sum == 2.5)) {
                    $grade25 += 1;
                } else if (($sum == 2)) {
                    $grade2 += 1;
                } else if (($sum == 1.5)) {
                    $grade15 += 1;
                } else if (($sum == 1)) {
                    $grade1 += 1;
                } else if ($sum == 0) {
                    $grade0 += 1;
                }
        }

      
    }
  
} 

$avg4 = round(($grade4*100)/$count_all,2);
$avg35 = round(($grade35*100)/$count_all,2);
$avg3 = round(($grade3*100)/$count_all,2);
$avg25 = round(($grade25*100)/$count_all,2);
$avg2 = round(($grade2*100)/$count_all,2);
$avg15 = round(($grade15*100)/$count_all,2);
$avg1 = round(($grade1*100)/$count_all,2);
$avg0 = round(($grade0*100)/$count_all,2);
$avgR = round(($gradeR*100)/$count_all,2);;
$avgMS=round(($gradeMS*100)/$count_all,2);

$avg3_up = $avg3 + $avg35 + $avg4;
?>


<table class="table" style="width: 100%;margin-top: 10px; border-collapse: collapse;border: 2px solid;">
    <tbody>
        <tr>
            <td rowspan="3" style="width: 16.7401%; text-align: center; vertical-align: middle;">
                <strong>จำนวนนักเรียนทั้งหมด</strong>
            </td>
            <td colspan="10" style="width: 73.6784%; text-align: center; vertical-align: middle;">
                <strong>สรุปผลการพัฒนาคุณภาพผู้เรียน</strong>
            </td>
            <td rowspan="3" style="width: 8.1773%; text-align: center; vertical-align: middle;">
                <strong>หมายเหตุ</strong>
            </td>
        </tr>
        <tr>
            <td colspan="10" style="width: 73.6784%; text-align: center; vertical-align: middle;">
                <strong>จำนวนนักเรียนที่ได้ระดับผลการเรียน</strong>
            </td>
        </tr>
        <tr>
            <td style="width: 7.1585%; text-align: center; vertical-align: middle;"><strong>4</strong></td>
            <td style="width: 7.1587%; text-align: center; vertical-align: middle;"><strong>3.5</strong></td>
            <td style="width: 7.0037%; text-align: center; vertical-align: middle;"><strong>3</strong></td>
            <td style="width: 7.0382%; text-align: center; vertical-align: middle;"><strong>2.5</strong></td>
            <td style="width: 8.0208%; text-align: center; vertical-align: middle;"><strong>2</strong></td>
            <td style="width: 8.6299%; text-align: center; vertical-align: middle;"><strong>1.5</strong></td>
            <td style="width: 7.439%; text-align: center; vertical-align: middle;"><strong>1</strong></td>
            <td style="width: 6.2672%; text-align: center; vertical-align: middle;"><strong>0</strong></td>
            <td style="width: 6.7525%; text-align: center; vertical-align: middle;"><strong>ร</strong></td>
            <td style="width: 6.1639%; text-align: center; vertical-align: middle;"><strong>มส</strong></td>
        </tr>
        <tr>
            <td style="width: 16.7401%; text-align: center; vertical-align: middle;"><strong><?=$count_all;?></strong>
            </td>
            <td style="width: 7.1585%; text-align: center; vertical-align: middle;"><strong><?=$grade4;?></strong></td>
            <td style="width: 7.1587%; text-align: center; vertical-align: middle;"><strong><?=$grade35;?></strong></td>
            <td style="width: 7.0037%; text-align: center; vertical-align: middle;"><strong><?=$grade3;?></strong></td>
            <td style="width: 7.0382%; text-align: center; vertical-align: middle;"><strong><?=$grade25;?></strong></td>
            <td style="width: 8.0208%; text-align: center; vertical-align: middle;"><strong><?=$grade2;?></strong></td>
            <td style="width: 8.6299%; text-align: center; vertical-align: middle;"><strong><?=$grade15;?></strong></td>
            <td style="width: 7.439%; text-align: center; vertical-align: middle;"><strong><?=$grade1;?></strong></td>
            <td style="width: 6.2672%; text-align: center; vertical-align: middle;"><strong><?=$grade0;?></strong></td>
            <td style="width: 6.7525%; text-align: center; vertical-align: middle;"><strong><?=$gradeR;?></strong></td>
            <td style="width: 6.1639%; text-align: center; vertical-align: middle;"><strong><?=$gradeMS;?></strong></td>
            <td style="width: 8.1773%; text-align: center; vertical-align: middle;"><strong><br></strong></td>
        </tr>
        <tr>
            <td style="width: 16.7401%; text-align: center; vertical-align: middle;"><strong>คิดเป็นร้อยละ</strong></td>
            <td style="width: 7.1585%; text-align: center; vertical-align: middle;"><strong><?=$avg4;?></strong></td>
            <td style="width: 7.1587%; text-align: center; vertical-align: middle;"><strong><?=$avg35;?></strong></td>
            <td style="width: 7.0037%; text-align: center; vertical-align: middle;"><strong><?=$avg3;?></strong></td>
            <td style="width: 7.0382%; text-align: center; vertical-align: middle;"><strong><?=$avg25;?></strong></td>
            <td style="width: 8.0208%; text-align: center; vertical-align: middle;"><strong><?=$avg2;?></strong></td>
            <td style="width: 8.6299%; text-align: center; vertical-align: middle;"><strong><?=$avg15;?></strong></td>
            <td style="width: 7.439%; text-align: center; vertical-align: middle;"><strong><?=$avg1;?></strong></td>
            <td style="width: 6.2672%; text-align: center; vertical-align: middle;"><strong><?=$avg0;?></strong></td>
            <td style="width: 6.7525%; text-align: center; vertical-align: middle;"><strong><?=$avgR;?></strong></td>
            <td style="width: 6.1639%; text-align: center; vertical-align: middle;"><strong><?=$avgMS;?></strong></td>
            <td style="width: 8.1773%; text-align: center; vertical-align: middle;"><strong></strong></td>
        </tr>
        <tr>
            <td colspan="5" style="width: 45.2832%;"><strong>ร้อยละของนักเรียนที่ได้ผลการเรียนระดับดี(3)
                    ขึ้นไป&nbsp;</strong></td>
            <td style="width: 8.0208%; text-align: center; vertical-align: middle;"><strong><?=$avg3_up;?></strong></td>
            <td colspan="5" style="width: 37.1541%;"><strong>ผลการเรียนเฉลี่ยของรายวิชา</strong></td>
            <td style="width: 8.1773%; text-align: center; vertical-align: middle;"><strong>0.00</strong></td>
        </tr>
        <tr>
            <td colspan="5" style="width: 45.2832%;"><strong>ร้อยละของนักเรียนที่ไม่ผ่านการประเมิน</strong></td>
            <td style="width: 8.0208%; text-align: center; vertical-align: middle;"><strong>0.00</strong></td>
            <td colspan="6" style="width: 46.6255%;"><strong>ส่วนเบี่ยงเบนมาตรฐาน</strong></td>
        </tr>
    </tbody>
</table>

<div class="text-center b" style="margin-top: 2rem;font-size: 24px;">
    <div>การอนุมัติผลการพัฒนาคุณภาพผู้เรียน</div>
</div>

<table class="" style="width: 100%;font-size: 18px;">
    <tbody>
        <tr>
            <td style="width: 55%;">ลงชื่อ..........................................ครูผู้สอน</td>
            <td>ลงชื่อ..........................................หัวหน้ากลุ่มสาระการเรียนรู้</td>
        </tr>
        <tr>
            <td>ลงชื่อ..........................................หัวหน้างานวัดผล</td>
            <td>ลงชื่อ..........................................หัวหน้าฝ่ายวิชาการ</td>
        </tr>
    </tbody>
</table>


<table class="table" style="width: 100%;font-size: 18px;">
    <tbody>
        <tr>
            <td>
                <div class="b">เรียนเสนอเพื่อโปรดพิจารณา</div>
                <div style="height: 100px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;〇 เห็นควรอนุมัติ
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;〇 เห็นควรนำไปปรับปรุงแก้ไข</div>
                <br>
                <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ.......................................... รองผู้อำนวยการสถานศึกษา
                </p>
                <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    (นางสาวศรินทร์ทิพย์  กริมเขียว)
                </p>
            </td>
            <td style="text-align: center;">
                <div class="b" style="text-align: left;">การพิจารณา/สั่งการ</div>
                <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;〇 อนุมัติ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;〇
                    ไม่อนุมัติ/ให้นำไปปรับปรุงแก้ไข</div>
                <br>
                <!-- <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
                ลงชื่อ..........................................
                </p>
                <p>
                    <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
                    (นายพงษ์ศักดิ์ เงินสันเทียะ)<br>
                <div>
                    <small>                    
                    ผู้อำนวยการสถานศึกษา โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์
                    </small>
                    
                </div>

                </p>
                <p>
                    <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
                    ......../........../..........
                </p>
            </td>
        </tr>
    </tbody>
</table>