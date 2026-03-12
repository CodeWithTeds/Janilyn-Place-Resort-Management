<?php

namespace App\Http\Controllers;

use App\Models\StaffTask;
use App\Models\HousekeepingTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffTaskController extends Controller
{
    /**
     * Update the status of the specified task (StaffTask or HousekeepingTask).
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,in_progress,completed'],
        ]);

        // Try finding as StaffTask first
        $staffTask = StaffTask::find($id);
        if ($staffTask && $staffTask->assigned_to === Auth::id()) {
            $staffTask->update([
                'status' => $validated['status'],
                'completed_at' => $validated['status'] === 'completed' ? now() : null,
            ]);
            return redirect()->back()->with('success', 'Task status updated successfully.');
        }

        // Try finding as HousekeepingTask
        $housekeepingTask = HousekeepingTask::find($id);
        if ($housekeepingTask && $housekeepingTask->assigned_to === Auth::id()) {
            $housekeepingTask->update([
                'status' => $validated['status'],
                'completed_at' => $validated['status'] === 'completed' ? now() : null,
            ]);
            return redirect()->back()->with('success', 'Housekeeping task status updated successfully.');
        }

        abort(404, 'Task not found or unauthorized.');
    }
}
