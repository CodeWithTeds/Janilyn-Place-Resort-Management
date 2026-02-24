<?php

namespace App\Services;

use App\Enums\HousekeepingStatus;
use App\Enums\UnitCleaningStatus;
use App\Models\HousekeepingTask;
use App\Models\ResortUnit;
use App\Repositories\HousekeepingRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class HousekeepingService
{
    public function __construct(
        protected HousekeepingRepository $housekeepingRepository
    ) {}

    public function getTasks(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->housekeepingRepository->getTasks($filters, $perPage);
    }

    public function createTask(array $data): HousekeepingTask
    {
        return $this->housekeepingRepository->createTask($data);
    }

    public function updateTask(HousekeepingTask $task, array $data): bool
    {
        // If status is completed, set completed_at
        if (isset($data['status']) && $data['status'] === HousekeepingStatus::COMPLETED->value && $task->status !== HousekeepingStatus::COMPLETED) {
            $data['completed_at'] = Carbon::now();
        }

        return $this->housekeepingRepository->updateTask($task, $data);
    }

    public function deleteTask(HousekeepingTask $task): bool
    {
        return $this->housekeepingRepository->deleteTask($task);
    }

    public function getStaffMembers(): Collection
    {
        return $this->housekeepingRepository->getStaffMembers();
    }

    public function updateUnitCleaningStatus(ResortUnit $unit, UnitCleaningStatus $status): bool
    {
        $unit->cleaning_status = $status;
        return $unit->save();
    }

    public function getAllUnits(): Collection
    {
        return $this->housekeepingRepository->getAllUnits();
    }
}
