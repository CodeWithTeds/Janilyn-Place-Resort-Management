<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExclusiveResortRentalRequest;
use App\Http\Requests\UpdateExclusiveResortRentalRequest;
use App\Models\ExclusiveResortRental;
use App\Repositories\ExclusiveResortRentalRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OwnerExclusiveResortRentalController extends Controller
{
    public function __construct(
        protected ExclusiveResortRentalRepository $repository
    ) {}

    public function index(): View
    {
        $rentals = $this->repository->getAll();
        return view('owner.resort-management.exclusive-resort-rentals.index', compact('rentals'));
    }

    public function create(): View
    {
        return view('owner.resort-management.exclusive-resort-rentals.create');
    }

    public function store(StoreExclusiveResortRentalRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('exclusive-rentals', 'public');
        }

        $this->repository->create($data);

        return redirect()->route('owner.resort-management.exclusive-resort-rentals.index')
            ->with('success', 'Exclusive Resort Rental created successfully.');
    }

    public function edit(ExclusiveResortRental $exclusiveResortRental): View
    {
        return view('owner.resort-management.exclusive-resort-rentals.edit', [
            'rental' => $exclusiveResortRental
        ]);
    }

    public function update(UpdateExclusiveResortRentalRequest $request, ExclusiveResortRental $exclusiveResortRental): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('exclusive-rentals', 'public');
        }

        $this->repository->update($exclusiveResortRental, $data);

        return redirect()->route('owner.resort-management.exclusive-resort-rentals.index')
            ->with('success', 'Exclusive Resort Rental updated successfully.');
    }

    public function destroy(ExclusiveResortRental $exclusiveResortRental): RedirectResponse
    {
        $this->repository->delete($exclusiveResortRental);

        return redirect()->route('owner.resort-management.exclusive-resort-rentals.index')
            ->with('success', 'Exclusive Resort Rental deleted successfully.');
    }
}
