<?php

namespace App\Http\Controllers;

use App\Enums\UnitCleaningStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreHousekeepingTaskRequest;
use App\Http\Requests\UpdateHousekeepingTaskRequest;
use App\Models\HousekeepingTask;
use App\Models\ResortUnit;
use App\Models\User;
use App\Services\HousekeepingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OwnerHousekeepingController extends Controller
{
    public function __construct(
        protected HousekeepingService $housekeepingService
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->input('status'),
            'priority' => $request->input('priority'),
            'assigned_to' => $request->input('assigned_to'),
            'date' => $request->input('date'),
        ];

        $tasks = $this->housekeepingService->getTasks($filters);
        $staffMembers = $this->housekeepingService->getStaffMembers();
        $units = $this->housekeepingService->getAllUnits();

        return view('owner.housekeeping.index', compact('tasks', 'staffMembers', 'units'));
    }

    public function store(StoreHousekeepingTaskRequest $request): RedirectResponse
    {  
        $this->housekeepingService->createTask($request->validated());

        return redirect()->back()->with('success', 'Task created successfully.');
    }

    public function update(UpdateHousekeepingTaskRequest $request, HousekeepingTask $task): RedirectResponse
    {
        $this->housekeepingService->updateTask($task, $request->validated());

        return redirect()->back()->with('success', 'Task updated successfully.');
    }

    public function destroy(HousekeepingTask $task): RedirectResponse
    {
        $this->housekeepingService->deleteTask($task);

        return redirect()->back()->with('success', 'Task deleted successfully.');
    }

    public function updateUnitStatus(Request $request, ResortUnit $unit): RedirectResponse
    {
        $request->validate([
            'cleaning_status' => ['required', Rule::enum(UnitCleaningStatus::class)],
        ]);

        $this->housekeepingService->updateUnitCleaningStatus($unit, UnitCleaningStatus::from($request->input('cleaning_status')));

        return redirect()->back()->with('success', 'Unit cleaning status updated.');
    }

    public function schedules(): View
    {
        $units = ResortUnit::with(['housekeepingTasks' => function ($query) {
            $query->where('status', '!=', \App\Enums\HousekeepingStatus::COMPLETED)
                  ->orderBy('due_date', 'asc');
        }])->get();

        $staffMembers = $this->housekeepingService->getStaffMembers();

        return view('owner.housekeeping.schedules', compact('units', 'staffMembers'));
    }

    public function staff(): View
    {
        $staffMembers = $this->housekeepingService->getStaffMembers();
        return view('owner.housekeeping.staff', compact('staffMembers'));
    }

    public function createStaff(): View
    {
        return view('owner.housekeeping.create-staff');
    }

    public function editStaff(User $staff): View
    {
        return view('owner.housekeeping.edit-staff', compact('staff'));
    }

    public function storeStaff(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role' => UserRole::STAFF,
        ]);

        if ($request->hasFile('photo')) {
            $user->updateProfilePhoto($request->file('photo'));
        }

        return redirect()->route('owner.housekeeping.staff')->with('success', 'Staff account created successfully.');
    }

    public function updateStaff(Request $request, User $staff): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($staff->id)],
            'phone_number' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->phone_number = $request->phone_number;

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            $staff->updateProfilePhoto($request->file('photo'));
        }

        $staff->save();

        return redirect()->route('owner.housekeeping.staff')->with('success', 'Staff account updated successfully.');
    }

    public function destroyStaff(User $staff): RedirectResponse
    {
        if ($staff->role !== UserRole::STAFF) {
            return redirect()->back()->with('error', 'Cannot delete non-staff users.');
        }

        $staff->delete();

        return redirect()->back()->with('success', 'Staff account deleted successfully.');
    }
}
