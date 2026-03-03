<?php

namespace App\Repositories;

use App\Enums\UserRole;
use App\Models\StaffAttendance;
use App\Models\StaffPerformance;
use App\Models\StaffSchedule;
use App\Models\StaffTask;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class StaffManagementRepository
{
    public function getStaffMembers(): Collection
    {
        return User::where('role', UserRole::STAFF)->get();
    }

    public function getStaffSchedules(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = StaffSchedule::with('user');

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['date'])) {
            $query->whereDate('start_time', $filters['date']);
        }

        return $query->orderBy('start_time', 'asc')->paginate($perPage);
    }

    public function createSchedule(array $data): StaffSchedule
    {
        return StaffSchedule::create($data);
    }

    public function updateSchedule(StaffSchedule $schedule, array $data): bool
    {
        return $schedule->update($data);
    }

    public function deleteSchedule(StaffSchedule $schedule): bool
    {
        return $schedule->delete();
    }

    public function getStaffAttendance(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = StaffAttendance::with('user');

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        return $query->orderBy('date', 'desc')->paginate($perPage);
    }

    public function createAttendance(array $data): StaffAttendance
    {
        return StaffAttendance::create($data);
    }

    public function updateAttendance(StaffAttendance $attendance, array $data): bool
    {
        return $attendance->update($data);
    }

    public function getStaffPerformance(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = StaffPerformance::with(['user', 'reviewer']);

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->orderBy('review_date', 'desc')->paginate($perPage);
    }

    public function createPerformance(array $data): StaffPerformance
    {
        return StaffPerformance::create($data);
    }

    public function getStaffTasks(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = StaffTask::with(['assignedTo', 'createdBy']);

        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('due_date', 'asc')->paginate($perPage);
    }

    public function createTask(array $data): StaffTask
    {
        return StaffTask::create($data);
    }

    public function updateTask(StaffTask $task, array $data): bool
    {
        return $task->update($data);
    }
}
