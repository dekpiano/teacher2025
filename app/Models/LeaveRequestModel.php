<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaveRequestModel extends Model
{
    protected $DBGroup          = 'personnel';
    protected $table            = 'tb_leave_requests';
    protected $primaryKey       = 'leave_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pers_id',
        'leave_type_id',
        'leave_topic',
        'leave_detail',
        'leave_start_date',
        'leave_end_date',
        'leave_total_days',
        'leave_period',
        'leave_contact_address',  // ที่อยู่ที่ติดต่อได้ระหว่างลา
        'leave_contact_phone',    // เบอร์โทรศัพท์ติดต่อ
        'leave_file',
        'leave_status',
        'leave_comment',
        'approved_by',
        'approved_at',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getLeaveWithTypes($pers_id = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('tb_leave_requests.*, tb_leave_types.leave_type_name');
        $builder->join('tb_leave_types', 'tb_leave_types.leave_type_id = tb_leave_requests.leave_type_id');
        if ($pers_id) {
            $builder->where('tb_leave_requests.pers_id', $pers_id);
        }
        $builder->orderBy('tb_leave_requests.leave_start_date', 'DESC');
        return $builder->get()->getResultArray();
    }

    /**
     * ดึงหรือสร้างปีงบประมาณอัตโนมัติตามวันที่ปัจจุบัน
     * ปีงบประมาณราชการ: 1 ต.ค. (ปีก่อน) ถึง 30 ก.ย. (ปีปัจจุบัน)
     */
    public function getOrCreateActiveLeaveYear()
    {
        $db_personnel = \Config\Database::connect('personnel');

        // คำนวณปีงบประมาณที่แท้จริงจากวันที่ปัจจุบัน
        $today = date('Y-m-d');
        $currentMonth = (int)date('n');
        $currentYear = (int)date('Y');

        if ($currentMonth >= 10) {
            // เดือน ต.ค. - ธ.ค. => เป็นปีงบประมาณของปีถัดไป
            $fiscalYearBE = ($currentYear + 1) + 543;
            $startDate = $currentYear . '-10-01';
            $endDate = ($currentYear + 1) . '-09-30';
        } else {
            // เดือน ม.ค. - ก.ย. => เป็นปีงบประมาณของปีนี้
            $fiscalYearBE = $currentYear + 543;
            $startDate = ($currentYear - 1) . '-10-01';
            $endDate = $currentYear . '-09-30';
        }

        // 1. ตรวจสอบว่ามีปีงบประมาณที่ตรงกับปัจจุบันอยู่แล้วหรือไม่ (หรือวันที่ปัจจุบันตกอยู่ในช่วงของปีนั้น)
        $currentFiscalYear = $db_personnel->table('tb_leave_years')
            ->where('ly_name', (string)$fiscalYearBE)
            ->orGroupStart()
                ->where('ly_start_date <=', $today)
                ->where('ly_end_date >=', $today)
            ->groupEnd()
            ->get()
            ->getRow();

        if ($currentFiscalYear) {
            // หากปีปัจจุบันยังไม่ได้เป็น active ให้สลับเป็น active และปิดปีเก่า
            if ($currentFiscalYear->ly_status !== 'active') {
                $db_personnel->table('tb_leave_years')->update(['ly_status' => 'inactive']);
                $db_personnel->table('tb_leave_years')
                    ->where('ly_id', $currentFiscalYear->ly_id)
                    ->update(['ly_status' => 'active']);
                $currentFiscalYear->ly_status = 'active';
            }
            return $currentFiscalYear;
        }

        // 2. ถ้ายังไม่มีในฐานข้อมูล ให้ปิดปีเก่าทั้งหมด แล้วสร้างปีงบประมาณปัจจุบันขึ้นมาใหม่อัตโนมัติ
        $db_personnel->table('tb_leave_years')->update(['ly_status' => 'inactive']);

        $newYearData = [
            'ly_name' => (string)$fiscalYearBE,
            'ly_start_date' => $startDate,
            'ly_end_date' => $endDate,
            'ly_status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $db_personnel->table('tb_leave_years')->insert($newYearData);
            $newYearData['ly_id'] = $db_personnel->insertID();
            return (object)$newYearData;
        } catch (\Exception $e) {
            return (object)$newYearData;
        }
    }

    /**
     * แปลงรูปแบบวันที่ภาษาไทย (เช่น "21 สิงหาคม 2569" หรือ "21/08/2569") ให้เป็น Y-m-d มาตรฐานสากล
     */
    public function parseThaiDateToStandard($dateStr)
    {
        if (empty($dateStr)) return null;
        $dateStr = trim($dateStr);

        // ถ้าเป็น Y-m-d อยู่แล้ว
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
            return $dateStr;
        }

        $thaiMonths = [
            'มกราคม' => '01', 'กุมภาพันธ์' => '02', 'มีนาคม' => '03', 'เมษายน' => '04',
            'พฤษภาคม' => '05', 'มิถุนายน' => '06', 'กรกฎาคม' => '07', 'สิงหาคม' => '08',
            'กันยายน' => '09', 'ตุลาคม' => '10', 'พฤศจิกายน' => '11', 'ธันวาคม' => '12',
            'ม.ค.' => '01', 'ก.พ.' => '02', 'มี.ค.' => '03', 'เม.ย.' => '04',
            'พ.ค.' => '05', 'มิ.ย.' => '06', 'ก.ค.' => '07', 'ส.ค.' => '08',
            'ก.ย.' => '09', 'ต.ค.' => '10', 'พ.ย.' => '11', 'ธ.ค.' => '12',
        ];

        // รูปแบบ "21 สิงหาคม 2569"
        foreach ($thaiMonths as $mName => $mNum) {
            if (strpos($dateStr, $mName) !== false) {
                $parts = preg_split('/\s+/', $dateStr);
                if (count($parts) >= 3) {
                    $day = str_pad(preg_replace('/[^0-9]/', '', $parts[0]), 2, '0', STR_PAD_LEFT);
                    $yearBE = (int)preg_replace('/[^0-9]/', '', end($parts));
                    $yearAD = ($yearBE > 2400) ? ($yearBE - 543) : $yearBE;
                    return sprintf('%04d-%02d-%02d', $yearAD, $mNum, $day);
                }
            }
        }

        // รูปแบบ dd/mm/yyyy (พ.ศ.)
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $dateStr, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $yearBE = (int)$matches[3];
            $yearAD = ($yearBE > 2400) ? ($yearBE - 543) : $yearBE;
            return sprintf('%04d-%02d-%02d', $yearAD, $month, $day);
        }

        try {
            return date('Y-m-d', strtotime($dateStr));
        } catch (\Exception $e) {
            return $dateStr;
        }
    }

    /**
     * คำนวณวันทำการจริง (ไม่รวมวันเสาร์-อาทิตย์ และวันหยุดนักขัตฤกษ์ราชการ)
     */
    public function calculateWorkingDays($startDate, $endDate, $leavePeriod = 'full')
    {
        $startDate = $this->parseThaiDateToStandard($startDate);
        $endDate = $this->parseThaiDateToStandard($endDate);

        if (empty($startDate) || empty($endDate) || strtotime($endDate) < strtotime($startDate)) {
            return 0;
        }

        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $end->modify('+1 day'); // include end date

        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($start, $interval, $end);

        $workingDays = 0;
        $holidays = $this->getThaiPublicHolidays((int)$start->format('Y'), (int)$end->format('Y'));

        foreach ($period as $dt) {
            $dayOfWeek = (int)$dt->format('N'); // 1 (Mon) to 7 (Sun)
            $dateStr = $dt->format('Y-m-d');

            // ข้ามวันเสาร์ (6) และวันอาทิตย์ (7)
            if ($dayOfWeek === 6 || $dayOfWeek === 7) {
                continue;
            }

            // ข้ามวันหยุดนักขัตฤกษ์
            if (in_array($dateStr, $holidays)) {
                continue;
            }

            $workingDays++;
        }

        // กรณีลาครึ่งวัน (เช้า หรือ บ่าย)
        if ($workingDays === 1 && in_array($leavePeriod, ['morning', 'afternoon'])) {
            return 0.5;
        }

        return (float)$workingDays;
    }

    /**
     * รายการวันหยุดนักขัตฤกษ์ราชการประจำปีของไทย
     * ดึงจากฐานข้อมูล `tb_holidays` (หากยังไม่มีตาราง/ข้อมูล ระบบจะสร้างและ Seed ข้อมูลให้อัตโนมัติล่วงหน้า 5+ ปี)
     */
    public function getThaiPublicHolidays($startYear, $endYear = null)
    {
        $years = range($startYear, $endYear ?: $startYear);
        $holidays = [];

        try {
            $db_personnel = \Config\Database::connect('personnel');

            // 1. สร้างตาราง tb_holidays หากยังไม่มี
            $db_personnel->query("
                CREATE TABLE IF NOT EXISTS `tb_holidays` (
                    `holiday_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `holiday_date` DATE NOT NULL,
                    `holiday_name` VARCHAR(150) NOT NULL,
                    `holiday_type` ENUM('public', 'religious', 'special', 'school') DEFAULT 'public',
                    `holiday_year` INT(4) NOT NULL,
                    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`holiday_id`),
                    UNIQUE KEY `idx_holiday_date` (`holiday_date`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // 2. ตรวจสอบและ Seed ข้อมูลวันหยุด (2024 - 2031 ครอบคลุมล่วงหน้า 5+ ปี)
            $check = $db_personnel->query("SELECT COUNT(*) as cnt FROM `tb_holidays`")->getRow();
            if (empty($check) || $check->cnt == 0) {
                $this->seedInitialHolidays($db_personnel);
            }

            // 3. ดึงวันหยุดจากฐานข้อมูล (ดึงตาม YEAR ของ holiday_date)
            $dbHolidays = $db_personnel->query("SELECT holiday_date FROM `tb_holidays` WHERE YEAR(holiday_date) IN (" . implode(',', $years) . ")")->getResultArray();

            foreach ($dbHolidays as $h) {
                $holidays[] = $h['holiday_date'];
            }

            if (!empty($holidays)) {
                return array_unique($holidays);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Holidays Error: ' . $e->getMessage());
        }

        return array_unique($holidays);
    }

    /**
     * Seed ข้อมูลวันหยุดราชการและวันหยุดทางศาสนา/จันทรคติล่วงหน้า (2024 - 2031 / พ.ศ. 2567 - 2574)
     */
    public function seedInitialHolidays($db_personnel)
    {
        $allData = [];

        // กำหนดวันหยุดทางศาสนาและวันหยุดตามปฏิทินจันทรคติ (2024 - 2031)
        $religiousHolidays = [
            // 2024 (2567)
            ['2024-02-24', 'วันมาฆบูชา', 'religious', 2024],
            ['2024-02-26', 'วันหยุดชดเชยวันมาฆบูชา', 'religious', 2024],
            ['2024-05-22', 'วันวิสาขบูชา', 'religious', 2024],
            ['2024-07-20', 'วันอาสาฬหบูชา', 'religious', 2024],
            ['2024-07-22', 'วันหยุดชดเชยวันเข้าพรรษา', 'religious', 2024],

            // 2025 (2568)
            ['2025-02-12', 'วันมาฆบูชา', 'religious', 2025],
            ['2025-05-11', 'วันวิสาขบูชา', 'religious', 2025],
            ['2025-05-12', 'วันหยุดชดเชยวันวิสาขบูชา', 'religious', 2025],
            ['2025-07-10', 'วันอาสาฬหบูชา', 'religious', 2025],
            ['2025-07-11', 'วันเข้าพรรษา', 'religious', 2025],

            // 2026 (2569)
            ['2026-03-03', 'วันมาฆบูชา', 'religious', 2026],
            ['2026-05-31', 'วันวิสาขบูชา', 'religious', 2026],
            ['2026-06-01', 'วันหยุดชดเชยวันวิสาขบูชา', 'religious', 2026],
            ['2026-07-29', 'วันอาสาฬหบูชา', 'religious', 2026],
            ['2026-07-30', 'วันเข้าพรรษา', 'religious', 2026],

            // 2027 (2570)
            ['2027-02-20', 'วันมาฆบูชา', 'religious', 2027],
            ['2027-02-22', 'วันหยุดชดเชยวันมาฆบูชา', 'religious', 2027],
            ['2027-05-20', 'วันวิสาขบูชา', 'religious', 2027],
            ['2027-07-17', 'วันอาสาฬหบูชา', 'religious', 2027],
            ['2027-07-19', 'วันหยุดชดเชยวันเข้าพรรษา', 'religious', 2027],

            // 2028 (2571)
            ['2028-02-09', 'วันมาฆบูชา', 'religious', 2028],
            ['2028-05-08', 'วันวิสาขบูชา', 'religious', 2028],
            ['2028-07-06', 'วันอาสาฬหบูชา', 'religious', 2028],
            ['2028-07-07', 'วันเข้าพรรษา', 'religious', 2028],

            // 2029 (2572)
            ['2029-02-27', 'วันมาฆบูชา', 'religious', 2029],
            ['2029-05-27', 'วันวิสาขบูชา', 'religious', 2029],
            ['2029-05-28', 'วันหยุดชดเชยวันวิสาขบูชา', 'religious', 2029],
            ['2029-07-25', 'วันอาสาฬหบูชา', 'religious', 2029],
            ['2029-07-26', 'วันเข้าพรรษา', 'religious', 2029],

            // 2030 (2573)
            ['2030-02-17', 'วันมาฆบูชา', 'religious', 2030],
            ['2030-02-18', 'วันหยุดชดเชยวันมาฆบูชา', 'religious', 2030],
            ['2030-05-16', 'วันวิสาขบูชา', 'religious', 2030],
            ['2030-07-14', 'วันอาสาฬหบูชา', 'religious', 2030],
            ['2030-07-15', 'วันหยุดชดเชยวันเข้าพรรษา', 'religious', 2030],

            // 2031 (2574)
            ['2031-02-06', 'วันมาฆบูชา', 'religious', 2031],
            ['2031-05-06', 'วันวิสาขบูชา', 'religious', 2031],
            ['2031-07-04', 'วันอาสาฬหบูชา', 'religious', 2031],
            ['2031-07-05', 'วันเข้าพรรษา', 'religious', 2031],
        ];

        foreach ($religiousHolidays as $item) {
            $allData[] = [
                'holiday_date' => $item[0],
                'holiday_name' => $item[1],
                'holiday_type' => $item[2],
                'holiday_year' => $item[3],
            ];
        }

        // วันหยุดราชการประจำปีคงที่ (Fixed Public Holidays สำหรับทุกปี 2024-2031)
        for ($y = 2024; $y <= 2031; $y++) {
            $fixed = [
                ["$y-01-01", 'วันขึ้นปีใหม่', 'public', $y],
                ["$y-04-06", 'วันพระบาทสมเด็จพระพุทธยอดฟ้าจุฬาโลกมหาราช และวันที่ระลึกมหาจักรีบรมราชวงศ์ (วันจักรี)', 'public', $y],
                ["$y-04-13", 'วันสงกรานต์', 'public', $y],
                ["$y-04-14", 'วันสงกรานต์', 'public', $y],
                ["$y-04-15", 'วันสงกรานต์', 'public', $y],
                ["$y-05-01", 'วันแรงงานแห่งชาติ', 'public', $y],
                ["$y-05-04", 'วันฉัตรมงคล', 'public', $y],
                ["$y-06-03", 'วันเฉลิมพระชนมพรรษาสมเด็จพระนางเจ้าฯ พระบรมราชินี', 'public', $y],
                ["$y-07-28", 'วันพระบรมราชสมภพพระบาทสมเด็จพระปรเมนทรรามาธิบดีศรีสินทรมหาวชิราลงกรณ พระวชิรเกล้าเจ้าอยู่หัว', 'public', $y],
                ["$y-08-12", 'วันเฉลิมพระชนมพรรษาสมเด็จพระนางเจ้าสิริกิติ์ พระบรมราชินีนาถ พระบรมราชชนนีพันปีหลวง และวันแม่แห่งชาติ', 'public', $y],
                ["$y-10-13", 'วันนวมินทรมหาราช (วันคล้ายวันสวรรคต ร.9)', 'public', $y],
                ["$y-10-23", 'วันปิยมหาราช', 'public', $y],
                ["$y-12-05", 'วันคล้ายวันพระบรมราชสมภพ ร.9, วันชาติ และวันพ่อแห่งชาติ', 'public', $y],
                ["$y-12-10", 'วันรัฐธรรมนูญ', 'public', $y],
                ["$y-12-31", 'วันสิ้นปี', 'public', $y],
            ];

            foreach ($fixed as $f) {
                $allData[] = [
                    'holiday_date' => $f[0],
                    'holiday_name' => $f[1],
                    'holiday_type' => $f[2],
                    'holiday_year' => $f[3],
                ];
            }
        }

        // Insert Ignore using direct SQL matching exact table schema (holiday_date, holiday_name, created_at, updated_at)
        try {
            $valueStrings = [];
            $now = date('Y-m-d H:i:s');
            foreach ($allData as $row) {
                $d = $db_personnel->escape($row['holiday_date']);
                $n = $db_personnel->escape($row['holiday_name']);
                $valueStrings[] = "($d, $n, '$now', '$now')";
            }

            if (!empty($valueStrings)) {
                $sql = "INSERT IGNORE INTO `tb_holidays` (`holiday_date`, `holiday_name`, `created_at`, `updated_at`) VALUES " . implode(',', $valueStrings);
                $db_personnel->query($sql);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Seed Holidays Error: ' . $e->getMessage());
        }
    }

    /**
     * ดึงข้อมูลรอบการประเมิน/รอบ 6 เดือน ในปีงบประมาณ
     * รอบที่ 1: 1 ต.ค. - 31 มี.ค. (6 เดือนแรก)
     * รอบที่ 2: 1 เม.ย. - 30 ก.ย. (6 เดือนหลัง)
     */
    public function getTermInfo($date = null, $selectedYear = null, $selectedRound = null)
    {
        if ($selectedYear && $selectedRound) {
            $fiscalYearBE = (int)$selectedYear->ly_name;
            $fiscalYearAD = $fiscalYearBE - 543;
            $term = (int)$selectedRound;

            if ($term === 1) {
                $startDate = ($fiscalYearAD - 1) . "-10-01";
                $endDate = "$fiscalYearAD-03-31";
                $termName = "รอบที่ 1/6 เดือน (1 ต.ค. - 31 มี.ค. $fiscalYearBE)";
            } else {
                $startDate = "$fiscalYearAD-04-01";
                $endDate = "$fiscalYearAD-09-30";
                $termName = "รอบที่ 2/6 เดือน (1 เม.ย. - 30 ก.ย. $fiscalYearBE)";
            }

            return [
                'term' => $term,
                'fiscal_year' => $fiscalYearBE,
                'term_name' => $termName,
                'short_name' => "รอบที่ $term ($fiscalYearBE)",
                'start_date' => $startDate,
                'end_date' => $endDate,
                'max_quota_per_term' => 23.0,
            ];
        }

        $standardDate = $date ? $this->parseThaiDateToStandard($date) : null;
        $time = $standardDate ? strtotime($standardDate) : time();
        $month = (int)date('n', $time);
        $year = (int)date('Y', $time);

        if ($month >= 10) {
            // ช่วง ต.ค. - ธ.ค. => รอบที่ 1 ของปีงบประมาณถัดไป (1 ต.ค. - 31 มี.ค.)
            $fiscalYearBE = ($year + 1) + 543;
            $term = 1;
            $startDate = "$year-10-01";
            $endDate = ($year + 1) . "-03-31";
            $termName = "รอบที่ 1/6 เดือน (1 ต.ค. - 31 มี.ค. $fiscalYearBE)";
        } elseif ($month <= 3) {
            // ช่วง ม.ค. - มี.ค. => รอบที่ 1 ของปีงบประมาณปัจจุบัน (1 ต.ค. - 31 มี.ค.)
            $fiscalYearBE = $year + 543;
            $term = 1;
            $startDate = ($year - 1) . "-10-01";
            $endDate = "$year-03-31";
            $termName = "รอบที่ 1/6 เดือน (1 ต.ค. - 31 มี.ค. $fiscalYearBE)";
        } else {
            // ช่วง เม.ย. - ก.ย. => รอบที่ 2 ของปีงบประมาณปัจจุบัน (1 เม.ย. - 30 ก.ย.)
            $fiscalYearBE = $year + 543;
            $term = 2;
            $startDate = "$year-04-01";
            $endDate = "$year-09-30";
            $termName = "รอบที่ 2/6 เดือน (1 เม.ย. - 30 ก.ย. $fiscalYearBE)";
        }

        return [
            'term' => $term,
            'fiscal_year' => $fiscalYearBE,
            'term_name' => $termName,
            'short_name' => "รอบที่ $term ($fiscalYearBE)",
            'start_date' => $startDate,
            'end_date' => $endDate,
            'max_quota_per_term' => 23.0, // กฎ: ในรอบ 6 เดือน ลารวมกันสูงสุดได้แค่ 23 วัน
        ];
    }

    /**
     * คำนวณวันลารวมทั้งหมดที่ใช้ไปในรอบ 6 เดือน
     */
    public function getUsedDaysInTerm($pers_id, $targetDate = null, $selectedYear = null, $selectedRound = null)
    {
        $termInfo = $this->getTermInfo($targetDate, $selectedYear, $selectedRound);
        $db_personnel = \Config\Database::connect('personnel');

        // 1. รวมจาก tb_leave_requests
        $builder = $this->db->table($this->table);
        $builder->selectSum('leave_total_days');
        $builder->where('pers_id', $pers_id);
        $builder->whereIn('leave_status', ['pending', 'approved']);
        $builder->where('leave_start_date >=', $termInfo['start_date']);
        $builder->where('leave_start_date <=', $termInfo['end_date']);
        $result = $builder->get()->getRow();
        $usedNew = $result && $result->leave_total_days ? (float)$result->leave_total_days : 0;

        // 2. รวมจาก tb_personnel_leave (legacy)
        $legacyBuilder = $db_personnel->table('tb_personnel_leave');
        $legacyBuilder->select('leave_start_date, leave_end_date');
        $legacyBuilder->where('pers_id', $pers_id);
        $legacyBuilder->where('leave_start_date >=', $termInfo['start_date']);
        $legacyBuilder->where('leave_start_date <=', $termInfo['end_date']);
        $legacyLeaves = $legacyBuilder->get()->getResultArray();
        $usedLegacy = 0;
        foreach ($legacyLeaves as $leave) {
            $usedLegacy += $this->calculateWorkingDays($leave['leave_start_date'], $leave['leave_end_date']);
        }

        return [
            'term_info' => $termInfo,
            'used_in_term' => $usedNew + $usedLegacy,
            'max_quota' => 23.0,
            'remaining_in_term' => max(0, 23.0 - ($usedNew + $usedLegacy)),
        ];
    }

    public function getCombinedLeaveHistory($pers_id, $selectedYearId = null, $selectedRound = null)
    {
        $db_personnel = \Config\Database::connect('personnel');
        
        // Get target leave year (Selected or Active)
        if ($selectedYearId) {
            $targetYear = $db_personnel->table('tb_leave_years')
                ->where('ly_id', $selectedYearId)
                ->get()
                ->getRow();
        } else {
            $targetYear = $this->getOrCreateActiveLeaveYear();
        }

        // กำหนดช่วงวันที่กรอง (ถ้าเลือกรอบ 1 หรือ 2 ให้ใช้ช่วงของรอบนั้น)
        $filterStartDate = $targetYear ? $targetYear->ly_start_date : null;
        $filterEndDate = $targetYear ? $targetYear->ly_end_date : null;

        if ($targetYear && $selectedRound) {
            $termInfo = $this->getTermInfo(null, $targetYear, $selectedRound);
            $filterStartDate = $termInfo['start_date'];
            $filterEndDate = $termInfo['end_date'];
        }

        $result = [];

        // Get from tb_leave_requests (new system - self-requested)
        $builder = $this->db->table($this->table);
        $builder->select('tb_leave_requests.*, tb_leave_types.leave_type_name, "self" as source');
        $builder->join('tb_leave_types', 'tb_leave_types.leave_type_id = tb_leave_requests.leave_type_id');
        $builder->where('tb_leave_requests.pers_id', $pers_id);
        if ($filterStartDate && $filterEndDate) {
            $builder->where('tb_leave_requests.leave_start_date >=', $filterStartDate);
            $builder->where('tb_leave_requests.leave_start_date <=', $filterEndDate);
        }
        $builder->orderBy('tb_leave_requests.leave_start_date', 'DESC');
        
        $selfLeaves = $builder->get()->getResultArray();
        foreach ($selfLeaves as $leave) {
            $result[] = [
                'id' => $leave['leave_id'],
                'type_name' => $leave['leave_type_name'],
                'topic' => $leave['leave_topic'],
                'detail' => $leave['leave_detail'],
                'start_date' => $leave['leave_start_date'],
                'end_date' => $leave['leave_end_date'],
                'total_days' => $leave['leave_total_days'],
                'status' => $leave['leave_status'],
                'created_at' => $leave['created_at'],
                'source' => 'self',
                'can_cancel' => ($leave['leave_status'] == 'pending'),
            ];
        }

        // Get from tb_personnel_leave (legacy - staff entered)
        if ($filterStartDate && $filterEndDate) {
            $legacyBuilder = $db_personnel->table('tb_personnel_leave');
            $legacyBuilder->where('pers_id', $pers_id);
            $legacyBuilder->where('leave_start_date >=', $filterStartDate);
            $legacyBuilder->where('leave_start_date <=', $filterEndDate);
            $legacyBuilder->orderBy('leave_start_date', 'DESC');
            
            $staffLeaves = $legacyBuilder->get()->getResultArray();
            foreach ($staffLeaves as $leave) {
                // คำนวณวันทำการจริง (ไม่รวมเสาร์-อาทิตย์ และวันหยุดนักขัตฤกษ์)
                $actualDays = $this->calculateWorkingDays($leave['leave_start_date'], $leave['leave_end_date']);
                
                $result[] = [
                    'id' => $leave['id'],
                    'type_name' => $leave['leave_type'],
                    'topic' => $leave['leave_type'],
                    'detail' => $leave['leave_note'],
                    'start_date' => $leave['leave_start_date'],
                    'end_date' => $leave['leave_end_date'],
                    'total_days' => $actualDays,
                    'status' => 'approved', // Legacy data is considered approved
                    'created_at' => $leave['created_at'],
                    'source' => 'staff',
                    'can_cancel' => false,
                ];
            }
        }

        // Sort by start_date DESC
        usort($result, function($a, $b) {
            return strtotime($b['start_date']) - strtotime($a['start_date']);
        });

        return $result;
    }

    public function getUsedDays($pers_id, $leave_type_id, $selectedYearId = null)
    {
        $db_personnel = \Config\Database::connect('personnel');
        
        // Get target leave year (Selected or Active)
        if ($selectedYearId) {
            $targetYear = $db_personnel->table('tb_leave_years')->where('ly_id', $selectedYearId)->get()->getRow();
        } else {
            $targetYear = $this->getOrCreateActiveLeaveYear();
        }

        if (!$targetYear) return 0;

        // Get leave type name for matching with legacy table
        $leaveType = $db_personnel->table('tb_leave_types')
            ->where('leave_type_id', $leave_type_id)
            ->get()
            ->getRow();
        
        $leaveTypeName = $leaveType ? $leaveType->leave_type_name : '';

        // Count from tb_leave_requests (new system)
        $builder = $this->db->table($this->table);
        $builder->selectSum('leave_total_days');
        $builder->where('pers_id', $pers_id);
        $builder->where('leave_type_id', $leave_type_id);
        
        // Only count leaves that are approved or pending (not rejected or cancelled)
        $builder->whereIn('leave_status', ['pending', 'approved']);
        
        // Standardize date comparison for the target leave year
        $builder->groupStart()
            ->where('leave_start_date >=', $targetYear->ly_start_date)
            ->where('leave_start_date <=', $targetYear->ly_end_date)
        ->groupEnd();
        
        $result = $builder->get()->getRow();
        $usedFromNewSystem = $result && $result->leave_total_days ? (float)$result->leave_total_days : 0;

        // Count from tb_personnel_leave (legacy data entered by staff)
        // Calculate actual working days excluding weekends and public holidays
        $legacyBuilder = $db_personnel->table('tb_personnel_leave');
        $legacyBuilder->select('leave_start_date, leave_end_date');
        $legacyBuilder->where('pers_id', $pers_id);
        $legacyBuilder->where('leave_type', $leaveTypeName);
        $legacyBuilder->groupStart()
            ->where('leave_start_date >=', $targetYear->ly_start_date)
            ->where('leave_start_date <=', $targetYear->ly_end_date)
        ->groupEnd();
        
        $legacyLeaves = $legacyBuilder->get()->getResultArray();
        $usedFromLegacy = 0;
        foreach ($legacyLeaves as $leave) {
            $usedFromLegacy += $this->calculateWorkingDays($leave['leave_start_date'], $leave['leave_end_date']);
        }

        return $usedFromNewSystem + $usedFromLegacy;
    }

    /**
     * Get count of late arrivals for the target leave year
     */
    public function getLateCount($pers_id, $selectedYearId = null)
    {
        $db_personnel = \Config\Database::connect('personnel');
        
        // Get target leave year (Selected or Active)
        if ($selectedYearId) {
            $targetYear = $db_personnel->table('tb_leave_years')->where('ly_id', $selectedYearId)->get()->getRow();
        } else {
            $targetYear = $this->getOrCreateActiveLeaveYear();
        }

        if (!$targetYear) return 0;

        return $db_personnel->table('tb_personnel_attendance')
            ->where('att_person_id', $pers_id)
            ->where('att_status', 'สาย')
            ->where('att_date >=', $targetYear->ly_start_date)
            ->where('att_date <=', $targetYear->ly_end_date)
            ->countAllResults();
    }

    /**
     * Get specific dates of late arrivals for the target leave year
     */
    public function getLateDetails($pers_id, $selectedYearId = null)
    {
        $db_personnel = \Config\Database::connect('personnel');
        
        // Get target leave year (Selected or Active)
        if ($selectedYearId) {
            $targetYear = $db_personnel->table('tb_leave_years')->where('ly_id', $selectedYearId)->get()->getRow();
        } else {
            $targetYear = $this->getOrCreateActiveLeaveYear();
        }

        if (!$targetYear) return [];

        return $db_personnel->table('tb_personnel_attendance')
            ->where('att_person_id', $pers_id)
            ->where('att_status', 'สาย')
            ->where('att_date >=', $targetYear->ly_start_date)
            ->where('att_date <=', $targetYear->ly_end_date)
            ->orderBy('att_date', 'DESC')
            ->get()
            ->getResultArray();
    }
}
