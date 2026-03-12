<?php

namespace App\Http\Controllers;

use App\Models\StaffTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffTaskController extends Controller
{
    /**
     * Update the status of the specified task.
     */
    public function updateStatus(Request $request, StaffTask $task): RedirectResponse
    {
        // Ensure the task is assigned to the authenticated user
        if ($task->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,in_progress,completed'],
        ]);

        $task->update([
            'status' => $validated['status'],
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Task status updated successfully.');
    }
}
