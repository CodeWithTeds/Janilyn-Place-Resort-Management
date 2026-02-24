<?php

namespace App\Repositories;

use App\Enums\UserRole;
use App\Models\HousekeepingTask;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class HousekeepingRepository
{
    public function getTasks(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = HousekeepingTask::with(['resortUnit', 'assignee'])->latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('due_date', $filters['date']);
        }

        return $query->paginate($perPage);
    }

    public function createTask(array $data): HousekeepingTask
    {
        return HousekeepingTask::create($data);
    }

    public function updateTask(HousekeepingTask $task, array $data): bool
    {
        return $task->update($data);
    }

    public function deleteTask(HousekeepingTask $task): bool
    {
        return $task->delete();
    }

    public function getStaffMembers(): Collection
    {
        return User::where('role', UserRole::STAFF)->get();
    }

    public function getAllUnits(): Collection
    {
        return \App\Models\ResortUnit::all();
    }
}
