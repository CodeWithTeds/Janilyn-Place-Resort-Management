<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\StaffAttendance;
use App\Models\StaffPerformance;
use App\Models\StaffSchedule;
use App\Models\StaffTask;
use App\Models\User;
use App\Services\StaffManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OwnerStaffManagementController extends Controller
{
    public function __construct(
        protected StaffManagementService $staffService
    ) {}

    public function index(): View
    {
        $staffMembers = $this->staffService->getStaffMembers();
        return view('owner.staff-management.index', compact('staffMembers'));
    }

    public function create(): View
    {
        return view('owner.staff-management.create');
    }

    public function store(Request $request): RedirectResponse
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

        return redirect()->route('owner.staff-management.index')->with('success', 'Staff account created successfully.');
    }

    public function edit(User $staff): View
    {
        return view('owner.staff-management.edit', compact('staff'));
    }

    public function update(Request $request, User $staff): RedirectResponse
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

        return redirect()->route('owner.staff-management.index')->with('success', 'Staff account updated successfully.');
    }

    public function destroy(User $staff): RedirectResponse
    {
        if ($staff->role !== UserRole::STAFF) {
            return redirect()->back()->with('error', 'Cannot delete non-staff users.');
        }

        $staff->delete();

        return redirect()->back()->with('success', 'Staff account deleted successfully.');
    }

    public function schedules(Request $request): View
    {
        $schedules = $this->staffService->getStaffSchedules($request->all());
        $staffMembers = $this->staffService->getStaffMembers();
        return view('owner.staff-management.schedules', compact('schedules', 'staffMembers'));
    }

    public function storeSchedule(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'type' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $this->staffService->createSchedule($request->all());

        return redirect()->back()->with('success', 'Schedule created successfully.');
    }

    public function updateSchedule(Request $request, StaffSchedule $schedule): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'type' => 'required|string',
            'notes' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $this->staffService->updateSchedule($schedule, $request->all());

        return redirect()->back()->with('success', 'Schedule updated successfully.');
    }

    public function deleteSchedule(StaffSchedule $schedule): RedirectResponse
    {
        $this->staffService->deleteSchedule($schedule);
        return redirect()->back()->with('success', 'Schedule deleted successfully.');
    }

    public function attendance(Request $request): View
    {
        $attendances = $this->staffService->getStaffAttendance($request->all());
        $staffMembers = $this->staffService->getStaffMembers();
        return view('owner.staff-management.attendance', compact('attendances', 'staffMembers'));
    }

    public function storeAttendance(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'clock_in' => 'nullable|date',
            'clock_out' => 'nullable|date|after:clock_in',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $this->staffService->createAttendance($request->all());

        return redirect()->back()->with('success', 'Attendance record created successfully.');
    }

    public function performance(Request $request): View
    {
        $performances = $this->staffService->getStaffPerformance($request->all());
        $staffMembers = $this->staffService->getStaffMembers();
        return view('owner.staff-management.performance', compact('performances', 'staffMembers'));
    }

    public function storePerformance(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
            'review_date' => 'required|date',
        ]);

        $data = $request->all();
        $data['reviewer_id'] = auth()->id();

        $this->staffService->createPerformance($data);

        return redirect()->back()->with('success', 'Performance review added successfully.');
    }

    public function tasks(Request $request): View
    {
        $tasks = $this->staffService->getStaffTasks($request->all());
        $staffMembers = $this->staffService->getStaffMembers();
        return view('owner.staff-management.tasks', compact('tasks', 'staffMembers'));
    }

    public function storeTask(Request $request): RedirectResponse
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['status'] = 'pending';

        $this->staffService->createTask($data);

        return redirect()->back()->with('success', 'Task assigned successfully.');
    }
}
