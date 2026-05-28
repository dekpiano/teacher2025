<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $DBGroup      = 'personnel';
    protected $table        = 'tb_attendance';
    protected $primaryKey   = 'att_id';
    protected $allowedFields = [
        'pers_id',
        'att_date',
        'check_in',
        'check_in_lat',
        'check_in_lng',
        'check_in_photo',
        'check_out',
        'check_out_lat',
        'check_out_lng',
        'check_out_photo',
        'status',
    ];

    /**
     * ดึง record วันนี้ของบุคลากร
     */
    public function getTodayRecord(string $persId): ?array
    {
        return $this->where('pers_id', $persId)
                    ->where('att_date', date('Y-m-d'))
                    ->first();
    }

    /**
     * ดึง record ตามวันที่กำหนด
     */
    public function getRecordByDate(string $persId, string $date): ?array
    {
        return $this->where('pers_id', $persId)
                    ->where('att_date', $date)
                    ->first();
    }

    /**
     * บันทึกเช็คชื่อเข้า
     */
    public function insertCheckIn(array $data): int
    {
        $this->insert($data);
        return (int) $this->getInsertID();
    }

    /**
     * บันทึกเช็คชื่อออก
     */
    public function updateCheckOut(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * ดึงตั้งค่าพื้นที่โรงเรียน
     */
    public function getLocationSettings(): ?object
    {
        $db = \Config\Database::connect('personnel');
        return $db->table('tb_attendance_location')
                  ->where('loc_id', 1)
                  ->get()
                  ->getRow();
    }

    /**
     * ตรวจสอบว่าอยู่ในรัศมีที่อนุญาตหรือไม่ (Haversine formula)
     * คืนค่าเป็นเมตร
     */
    public function calculateDistance(float $fromLat, float $fromLng, float $toLat, float $toLng): float
    {
        $earthRadius = 6371000; // รัศมีโลก (เมตร)

        $dLat = deg2rad($toLat - $fromLat);
        $dLng = deg2rad($toLng - $fromLng);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($fromLat)) * cos(deg2rad($toLat))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // คืนค่าเป็นเมตร
    }

    /**
     * ดึงประวัติเช็คชื่อย้อนหลัง
     */
    public function getHistory(string $persId, ?string $month = null, ?string $year = null): array
    {
        $builder = $this->where('pers_id', $persId);

        if ($month && $year) {
            $builder->where('MONTH(att_date)', $month)
                    ->where('YEAR(att_date)', $year);
        }

        return $builder->orderBy('att_date', 'DESC')
                       ->findAll();
    }

    /**
     * สรุปผลรายเดือน
     */
    public function getMonthlySummary(string $persId, string $month, string $year): array
    {
        $records = $this->select('status, COUNT(*) as cnt')
                        ->where('pers_id', $persId)
                        ->where('MONTH(att_date)', $month)
                        ->where('YEAR(att_date)', $year)
                        ->groupBy('status')
                        ->get()
                        ->getResultArray();

        $summary = [
            'ปกติ' => 0,
            'มาสาย' => 0,
            'ขาด'  => 0,
        ];

        foreach ($records as $row) {
            if (isset($summary[$row['status']])) {
                $summary[$row['status']] = (int) $row['cnt'];
            }
        }

        return $summary;
    }

    /**
     * ดึงรายการประวัติแบบละเอียดสำหรับรายงาน
     */
    public function getHistoryDetailed(string $persId, string $month, string $year): array
    {
        return $this->where('pers_id', $persId)
                    ->where('MONTH(att_date)', $month)
                    ->where('YEAR(att_date)', $year)
                    ->orderBy('att_date', 'DESC')
                    ->findAll();
    }
}
