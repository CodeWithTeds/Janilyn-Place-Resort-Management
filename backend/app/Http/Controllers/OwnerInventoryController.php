<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class OwnerInventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::orderBy('name')->paginate(10);
        return view('owner.inventory.index', compact('inventories'));
    }

    public function create()
    {
        return view('owner.inventory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'reorder_level' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cost_per_unit' => 'nullable|numeric|min:0',
        ]);

        Inventory::create($validated);

        return redirect()->route('owner.inventory.index')->with('success', 'Item added successfully.');
    }

    public function edit(Inventory $inventory)
    {
        return view('owner.inventory.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'reorder_level' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cost_per_unit' => 'nullable|numeric|min:0',
        ]);

        $inventory->update($validated);

        return redirect()->route('owner.inventory.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('owner.inventory.index')->with('success', 'Item deleted successfully.');
    }
}
