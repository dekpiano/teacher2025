<?php

namespace App\Models;

use CodeIgniter\Model;

class ClubModel extends Model
{
    protected $table = 'tb_clubs';
    protected $primaryKey = 'club_id';
    protected $returnType = 'object'; // Explicitly set return type to object
    protected $allowedFields = [
        'club_name',
        'club_description',
        'club_faculty_advisor',
        'club_group',
        'club_level',
        'club_established_date',
        'club_max_participants',
        'club_status',
        'club_year',
        'club_trem'
    ];

    /**
     * Fetches all clubs advised by a specific teacher.
     *
     * @param string $teacherId The ID of the teacher.
     * @return array An array of club objects.
     */
    public function getClubsByTeacher(string $teacherId, ?string $year = null, ?string $term = null): array
    {
        $builder = $this->select('tb_clubs.*, COUNT(tcm.member_student_id) AS member_count')
                        ->join('tb_club_members tcm', 'tcm.member_club_id = tb_clubs.club_id', 'left')
                        ->groupStart()
                        ->where('club_faculty_advisor', $teacherId) // Exact match
                        ->orLike('club_faculty_advisor', $teacherId . '|', 'after') // Starts with teacherId|
                        ->orLike('club_faculty_advisor', '|' . $teacherId, 'before') // Ends with |teacherId
                        ->orLike('club_faculty_advisor', '|' . $teacherId . '|', 'both') // Contains |teacherId|
                        ->groupEnd();

        if ($year) {
            $builder->where('club_year', $year);
        }
        if ($term) {
            $builder->where('club_trem', $term);
        }
        
        $builder->groupBy('tb_clubs.club_id'); // Group by club_id to get correct counts

        return $builder->findAll();
    }

    /**
     * Fetches all student members for a given club, along with their details.
     *
     * @param int $clubId The ID of the club.
     * @return array An array of student objects.
     */
    public function getClubMembers(int $clubId): array
    {
        // First, get the student IDs and roles from the tb_club_members table.
        $member_data = $this->db->table('tb_club_members')
                                     ->select('member_student_id, member_role')
                                     ->where('member_club_id', $clubId)
                                     ->get()
                                     ->getResultArray();

        if (empty($member_data)) {
            return [];
        }

        // Extract just the student IDs into a simple array.
        $studentCodes = array_column($member_data, 'member_student_id');
        $memberRoles = array_column($member_data, 'member_role', 'member_student_id'); // Map member_student_id to member_role

        // Now, fetch the student details from the tb_students table.
        $students = $this->db->table('tb_students')
                             ->select('StudentID, StudentNumber, StudentClass, StudentCode, StudentPrefix, StudentFirstName, StudentLastName')
                             ->whereIn('StudentID', $studentCodes)
                             ->orderBy('StudentClass', 'ASC')
                             ->orderBy('StudentNumber', 'ASC')
                             ->get()
                             ->getResult();

        // Merge member_role into student objects
        foreach ($students as $student) {
            $student->member_role = $memberRoles[$student->StudentID] ?? 'Member';
        }

        return $students;
    }

    /**
     * Updates a member's role in a specific club.
     *
     * @param int $clubId The ID of the club.
     * @param string $studentId The ID of the student.
     * @param string $newRole The new role for the member (e.g., 'Leader', 'Member').
     * @return bool
     */
    public function updateMemberRole(int $clubId, string $studentId, string $newRole): bool
    {
        return $this->db->table('tb_club_members')
                        ->where('member_club_id', $clubId)
                        ->where('member_student_id', $studentId)
                        ->set('member_role', $newRole)
                        ->update();
    }

    /**
     * Removes a member from a specific club.
     *
     * @param int $clubId The ID of the club.
     * @param string $studentId The ID of the student.
     * @return bool
     */
    public function removeMember(int $clubId, string $studentId): bool
    {
        return $this->db->table('tb_club_members')
                        ->where('member_club_id', $clubId)
                        ->where('member_student_id', $studentId)
                        ->delete();
    }

    // --- ClubScheduleModel methods ---

    /**
     * Fetches schedules for a specific club.
     *
     * @param int $clubId The ID of the club.
     * @return array An array of schedule objects.
     */
    public function getSchedulesByYear(string $year, string $term, int $clubId): array
    {
        return $this->db->table('tb_club_settings_schedule tcs')
                        ->select('tcs.*, tca.act_name, tca.act_description, tca.act_location, tca.act_start_time, tca.act_end_time, tca.act_number_of_periods')
                        ->join('tb_club_activities tca', 'tca.act_date = tcs.tcs_start_date AND tca.act_club_id = ' . $this->db->escape($clubId), 'left')
                        ->where('tcs.tcs_academic_year', $year)
                        ->where('tcs.tcs_academic_trem', $term)
                        ->orderBy('tcs.tcs_week_number', 'ASC')
                        ->get()
                        ->getResult();
    }

    /**
     * Inserts a new schedule.
     *
     * @param array $data The data for the new schedule.
     * @return bool
     */
    public function insertSchedule(array $data): bool
    {
        return $this->db->table('tb_club_settings_schedule')->insert($data);
    }

    /**
     * Finds a schedule by its ID.
     *
     * @param int $scheduleId The ID of the schedule.
     * @return object|null The schedule data or null if not found.
     */
    public function findSchedule(int $scheduleId): ?object
    {
        return $this->db->table('tb_club_settings_schedule')->where('tcs_schedule_id', $scheduleId)->get()->getRow();
    }

    // --- ClubAttendanceModel methods ---

    /**
     * Fetches attendance records for a specific schedule.
     *
     * @param int $scheduleId The ID of the schedule.
     * @return array An array of attendance records.
     */
    public function getAttendanceBySchedule(int $scheduleId): array
    {
        return $this->db->table('tb_club_record_activity')
                        ->where('trca_schedule_id', $scheduleId)
                        ->get()
                        ->getResult();
    }

    public function getAttendanceByScheduleIds(array $scheduleIds): array
    {
        if (empty($scheduleIds)) {
            return [];
        }
        return $this->db->table('tb_club_record_activity')
                        ->whereIn('trca_schedule_id', $scheduleIds)
                        ->get()
                        ->getResult();
    }

    /**
     * Fetches attendance status for a specific student in a specific schedule.
     *
     * @param int $scheduleId The ID of the schedule.
     * @param int $studentId The ID of the student.
     * @return string|null The attendance status or null if not found.
     */
        public function getStudentAttendanceStatus(int $scheduleId, int $studentId): ?string
        {
                        $record = $this->db->table('tb_club_record_activity')
                               ->where('trca_schedule_id', $scheduleId)
                               ->where('StudentID', $studentId)
                               ->get()->getRow();
            return $record->status ?? null;
        }

    /**
     * Saves or updates an attendance record.
     *
     * @param int $scheduleId The ID of the schedule.
     * @param int $studentId The ID of the student.
     * @param string $status The attendance status.
     * @return bool
     */
    public function saveScheduleAttendance(array $data): bool
    {
        // Check if a record already exists for this schedule
        $existingRecord = $this->db->table('tb_club_record_activity')
                                   ->where('trca_schedule_id', $data['trca_schedule_id'])
                                   ->get()->getRow();

        if ($existingRecord) {
            // Update existing record
            return $this->db->table('tb_club_record_activity')
                            ->where('tcra_id', $existingRecord->tcra_id)
                            ->update($data);
        } else {
            // Insert new record
            return $this->db->table('tb_club_record_activity')->insert($data);
        }
    }

    // --- ClubActivityModel methods ---

    /**
     * Fetches activities for a specific club.
     *
     * @param int $clubId The ID of the club.
     * @return array An array of activity objects.
     */
    public function getActivitiesByClub(int $clubId): array
    {
        return $this->db->table('tb_club_activities')
                        ->where('act_club_id', $clubId)
                        ->orderBy('act_date', 'DESC')
                        ->get()
                        ->getResult();
    }

    /**
     * Inserts a new activity.
     *
     * @param array $data The data for the new activity.
     * @return bool
     */
    public function insertActivity(array $data): bool
    {
        return $this->db->table('tb_club_activities')->insert($data);
    }

    /**
     * Inserts or updates an activity based on club_id and activity_date.
     *
     * @param array $data The data for the activity.
     * @return bool
     */
    public function upsertActivity(array $data): bool
    {
        $existing = $this->db->table('tb_club_activities')
                             ->where('act_club_id', $data['act_club_id'])
                             ->where('act_date', $data['act_date'])
                             ->get()
                             ->getRow();

        if ($existing) {
            // Update
            return $this->db->table('tb_club_activities')
                            ->where('act_id', $existing->act_id)
                            ->update($data);
        } else {
            // Insert
            return $this->db->table('tb_club_activities')->insert($data);
        }
    }

    /**
     * Finds an activity by its ID.
     *
     * @param int $activityId The ID of the activity.
     * @return object|null The activity data or null if not found.
     */
    public function findActivity(int $activityId): ?object
    {
        return $this->db->table('tb_club_activities')->where('act_id', $activityId)->get()->getRow();
    }

    /**
     * Updates an activity.
     *
     * @param int $activityId The ID of the activity.
     * @param array $data The data to update.
     * @return bool
     */
    public function updateActivity(int $activityId, array $data): bool
    {
        return $this->db->table('tb_club_activities')
                        ->where('act_id', $activityId)
                        ->update($data);
    }

    /**
     * Deletes an activity.
     *
     * @param int $activityId The ID of the activity.
     * @return bool
     */
    public function deleteActivity(int $activityId): bool
    {
        return $this->db->table('tb_club_activities')
                        ->where('act_id', $activityId)
                        ->delete();
    }

    /**
     * Checks if attendance has been recorded for a specific schedule.
     *
     * @param int $scheduleId The ID of the schedule.
     * @return bool True if attendance is recorded, false otherwise.
     */
    public function hasAttendanceRecorded(int $scheduleId): bool
    {
        $record = $this->db->table('tb_club_record_activity')
                           ->where('trca_schedule_id', $scheduleId)
                           ->get()->getRow();
        return !empty($record);
    }

    // --- Club Objectives Methods ---

    public function getObjectivesByClub(int $clubId): array
    {
        return $this->db->table('tb_club_objectives')
                        ->where('club_id', $clubId)
                        ->orderBy('objective_order', 'ASC')
                        ->get()
                        ->getResult();
    }

    public function getClubStudentProgress(int $clubId): array
    {
        $progressData = $this->db->table('tb_club_student_progress')
                                 ->where('club_id', $clubId)
                                 ->get()
                                 ->getResult();

        $progressMap = [];
        foreach ($progressData as $progress) {
            $progressMap[$progress->student_id][$progress->objective_id] = $progress;
        }

        return $progressMap;
    }

    public function saveStudentProgress(int $clubId, string $teacherId, ?array $progressData): bool
    {
        $members = $this->getClubMembers($clubId);
        $objectives = $this->getObjectivesByClub($clubId);

        if (empty($members) || empty($objectives)) {
            return true; // Nothing to save
        }

        $batchData = [];
        foreach ($members as $member) {
            foreach ($objectives as $objective) {
                $status = isset($progressData[$member->StudentID][$objective->objective_id]) ? 1 : 0;
                
                $batchData[] = [
                    'club_id' => $clubId,
                    'student_id' => $member->StudentID,
                    'objective_id' => $objective->objective_id,
                    'status' => $status,
                    'updated_by' => $teacherId,
                ];
            }
        }

        if (empty($batchData)) {
            return true;
        }

        // Use upsertBatch for batch insert/update
        $builder = $this->db->table('tb_club_student_progress');
        $builder->upsertBatch($batchData);

        return $this->db->affectedRows() > 0;
    }

    // --- Club Objective Definition Methods ---

    public function addObjective(array $data): bool
    {
        return $this->db->table('tb_club_objectives')->insert($data);
    }

    public function updateObjective(int $objectiveId, array $data): bool
    {
        return $this->db->table('tb_club_objectives')
                        ->where('objective_id', $objectiveId)
                        ->update($data);
    }

    public function deleteObjective(int $objectiveId): bool
    {
        // Also delete related student progress records
        $this->db->table('tb_club_student_progress')->where('objective_id', $objectiveId)->delete();
        return $this->db->table('tb_club_objectives')->where('objective_id', $objectiveId)->delete();
    }

    public function getClubOnOffSettings(string $year, string $term): ?object
    {
        return $this->db->table('tb_club_onoff')
                        ->where('c_onoff_year', $year)
                        ->where('c_onoff_term', $term)
                        ->get()
                        ->getRow();
    }

    public function getPersonnelFullName(string $pers_id)
    {
        $db = db_connect('personnel'); // Connect to the personnel database
        return $db->table('tb_personnel')
                    ->select('pers_prefix, pers_firstname, pers_lastname')
                    ->where('pers_id', $pers_id)
                    ->get()
                    ->getRowArray();
    }

    public function getAdminPersonnelIdByRoleName(string $roleName)
    {
        $db = db_connect('default'); // Assuming 'default' is the DBGroup for skjacth_academic
        $result = $db->table('tb_admin_rloes')
                       ->select('admin_rloes_userid')
                       ->like('admin_rloes_academic_position', $roleName, 'both') // Use LIKE to find role in a combined string
                       ->get()
                       ->getRowArray();
        return $result ? $result['admin_rloes_userid'] : null;
    }
}

