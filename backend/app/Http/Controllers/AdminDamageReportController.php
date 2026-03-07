<?php

namespace App\Http\Controllers;

use App\Models\DamageReport;
use App\Models\ResortUnit;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminDamageReportController extends Controller
{
    public function index(): View
    {
        $reports = DamageReport::with(['resortUnit', 'reporter'])->latest()->paginate(10);
        return view('admin.damage-reports.index', compact('reports'));
    }

    public function edit(DamageReport $damageReport): View
    {
        $units = ResortUnit::orderBy('name')->get();
        return view('admin.damage-reports.edit', compact('damageReport', 'units'));
    }

    public function update(Request $request, DamageReport $damageReport): RedirectResponse
    {
        $validated = $request->validate([
            'resort_unit_id' => 'required|exists:resort_units,id',
            'item_name' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:Low,Medium,High,Critical',
            'status' => 'required|in:Reported,In Progress,Resolved',
            'cost_estimate' => 'nullable|numeric|min:0',
        ]);

        if ($request->status === 'Resolved' && !$damageReport->resolved_at) {
            $validated['resolved_at'] = now();
        } elseif ($request->status !== 'Resolved') {
            $validated['resolved_at'] = null;
        }

        $damageReport->update($validated);

        return redirect()->route('admin.damage-reports.index')->with('success', 'Damage report updated successfully.');
    }
}

