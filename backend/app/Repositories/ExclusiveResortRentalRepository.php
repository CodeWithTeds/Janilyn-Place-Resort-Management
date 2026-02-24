<?php

namespace App\Repositories;

use App\Models\ExclusiveResortRental;
use Illuminate\Database\Eloquent\Collection;

class ExclusiveResortRentalRepository
{
    public function getAll(): Collection
    {
        return ExclusiveResortRental::latest()->get();
    }

    public function create(array $data): ExclusiveResortRental
    {
        return ExclusiveResortRental::create($data);
    }

    public function update(ExclusiveResortRental $rental, array $data): bool
    {
        return $rental->update($data);
    }

    public function delete(ExclusiveResortRental $rental): bool
    {
        return $rental->delete();
    }
}
