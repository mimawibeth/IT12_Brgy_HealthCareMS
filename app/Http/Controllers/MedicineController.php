<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineDispense;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::orderBy('name')->paginate(10);

        return view('medicine.index', compact('medicines'));
    }

    public function create()
    {
        return view('medicine.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'generic_name' => ['nullable', 'string', 'max:255'],
            'dosage_form' => ['nullable', 'string', 'max:100'],
            'strength' => ['nullable', 'string', 'max:100'],
            'unit' => ['nullable', 'string', 'max:50'],
            'quantity_on_hand' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['nullable', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
        ]);

        $medicine = Medicine::create($validated);

        AuditLog::create([
            'user_id' => $request->user()->id ?? null,
            'user_role' => $request->user()->role ?? null,
            'action' => 'create',
            'module' => 'Medicine',
            'description' => 'Added medicine: '.$medicine->name,
            'ip_address' => $request->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('medicine.index')->with('success', 'Medicine added successfully');
    }

    public function edit(Medicine $medicine)
    {
        return view('medicine.edit', compact('medicine'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'generic_name' => ['nullable', 'string', 'max:255'],
            'dosage_form' => ['nullable', 'string', 'max:100'],
            'strength' => ['nullable', 'string', 'max:100'],
            'unit' => ['nullable', 'string', 'max:50'],
            'quantity_on_hand' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['nullable', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
        ]);

        $medicine->update($validated);

        AuditLog::create([
            'user_id' => $request->user()->id ?? null,
            'user_role' => $request->user()->role ?? null,
            'action' => 'update',
            'module' => 'Medicine',
            'description' => 'Updated medicine: '.$medicine->name,
            'ip_address' => $request->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('medicine.index')->with('success', 'Medicine updated successfully');
    }

    public function destroy(Medicine $medicine)
    {
        $name = $medicine->name;
        $medicine->delete();

        AuditLog::create([
            'user_id' => request()->user()->id ?? null,
            'user_role' => request()->user()->role ?? null,
            'action' => 'delete',
            'module' => 'Medicine',
            'description' => 'Deleted medicine: '.$name,
            'ip_address' => request()->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('medicine.index')->with('success', 'Medicine deleted successfully');
    }

    public function dispense()
    {
        $medicines = Medicine::orderBy('name')->get();
        $dispenses = MedicineDispense::with('medicine')
            ->orderByDesc('dispensed_at')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('medicine.dispense', compact('medicines', 'dispenses'));
    }

    public function storeDispense(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => ['required', 'exists:medicines,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'dispensed_to' => ['nullable', 'string', 'max:255'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'dispensed_at' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
        ]);

        $medicine = Medicine::findOrFail($validated['medicine_id']);

        if ($validated['quantity'] > $medicine->quantity_on_hand) {
            return back()->with('error', 'Not enough stock available for this medicine.')->withInput();
        }

        $dispenseData = $validated;
        if (empty($dispenseData['dispensed_at'])) {
            $dispenseData['dispensed_at'] = now()->toDateString();
        }

        $dispense = MedicineDispense::create($dispenseData);

        $medicine->decrement('quantity_on_hand', $validated['quantity']);

        AuditLog::create([
            'user_id' => $request->user()->id ?? null,
            'user_role' => $request->user()->role ?? null,
            'action' => 'dispense',
            'module' => 'Medicine',
            'description' => 'Dispensed '.$dispense->quantity.' '.$medicine->unit.' of '.$medicine->name,
            'ip_address' => $request->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('medicine.dispense')->with('success', 'Medicine dispensed successfully');
    }
}
