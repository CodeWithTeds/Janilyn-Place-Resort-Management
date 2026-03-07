<?php

namespace App\Http\Controllers;

use App\Models\DamageReport;
use App\Models\ResortUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StaffDamageReportController extends Controller
{
    public function index(): View
    {
        $reports = DamageReport::with(['resortUnit'])
            ->where('reported_by', Auth::id())
            ->latest()
            ->paginate(10);

        return view('staff.damage-reports.index', compact('reports'));
    }

    public function create(): View
    {
        $units = ResortUnit::orderBy('name')->get();
        return view('staff.damage-reports.create', compact('units'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'resort_unit_id' => 'required|exists:resort_units,id',
            'item_name' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:Low,Medium,High,Critical',
            'cost_estimate' => 'nullable|numeric|min:0',
        ]);

        $validated['reported_by'] = Auth::id();
        $validated['status'] = 'Reported';

        DamageReport::create($validated);

        return redirect()->route('staff.damage-reports.index')->with('success', 'Incident reported successfully.');
    }
}

