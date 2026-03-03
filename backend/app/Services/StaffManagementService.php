<?php

namespace App\Services;

use App\Models\StaffAttendance;
use App\Models\StaffPerformance;
use App\Models\StaffSchedule;
use App\Models\StaffTask;
use App\Models\User;
use App\Repositories\StaffManagementRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class StaffManagementService
{
    public function __construct(
        protected StaffManagementRepository $repository
    ) {}

    public function getStaffMembers(): Collection
    {
        return $this->repository->getStaffMembers();
    }

    public function getStaffSchedules(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->getStaffSchedules($filters, $perPage);
    }

    public function createSchedule(array $data): StaffSchedule
    {
        return $this->repository->createSchedule($data);
    }

    public function updateSchedule(StaffSchedule $schedule, array $data): bool
    {
        return $this->repository->updateSchedule($schedule, $data);
    }

    public function deleteSchedule(StaffSchedule $schedule): bool
    {
        return $this->repository->deleteSchedule($schedule);
    }

    public function getStaffAttendance(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->getStaffAttendance($filters, $perPage);
    }

    public function createAttendance(array $data): StaffAttendance
    {
        return $this->repository->createAttendance($data);
    }

    public function updateAttendance(StaffAttendance $attendance, array $data): bool
    {
        return $this->repository->updateAttendance($attendance, $data);
    }

    public function getStaffPerformance(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->getStaffPerformance($filters, $perPage);
    }

    public function createPerformance(array $data): StaffPerformance
    {
        return $this->repository->createPerformance($data);
    }

    public function getStaffTasks(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->getStaffTasks($filters, $perPage);
    }

    public function createTask(array $data): StaffTask
    {
        return $this->repository->createTask($data);
    }

    public function updateTask(StaffTask $task, array $data): bool
    {
        return $this->repository->updateTask($task, $data);
    }
}
