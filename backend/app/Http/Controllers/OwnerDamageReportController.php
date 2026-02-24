<?php

namespace App\Http\Controllers;

use App\Models\DamageReport;
use App\Models\ResortUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerDamageReportController extends Controller
{
    public function index()
    {
        $reports = DamageReport::with(['resortUnit', 'reporter'])->latest()->paginate(10);
        return view('owner.damage-reports.index', compact('reports'));
    }

    public function create()
    {
        $units = ResortUnit::orderBy('name')->get();
        return view('owner.damage-reports.create', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resort_unit_id' => 'required|exists:resort_units,id',
            'item_name' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:Low,Medium,High,Critical',
            'status' => 'required|in:Reported,In Progress,Resolved',
            'cost_estimate' => 'nullable|numeric|min:0',
        ]);

        $validated['reported_by'] = Auth::id();

        DamageReport::create($validated);

        return redirect()->route('owner.damage-reports.index')->with('success', 'Damage report submitted successfully.');
    }

    public function edit(DamageReport $damageReport)
    {
        $units = ResortUnit::orderBy('name')->get();
        return view('owner.damage-reports.edit', compact('damageReport', 'units'));
    }

    public function update(Request $request, DamageReport $damageReport)
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

        return redirect()->route('owner.damage-reports.index')->with('success', 'Damage report updated successfully.');
    }

    public function destroy(DamageReport $damageReport)
    {
        $damageReport->delete();

        return redirect()->route('owner.damage-reports.index')->with('success', 'Damage report deleted successfully.');
    }
}
