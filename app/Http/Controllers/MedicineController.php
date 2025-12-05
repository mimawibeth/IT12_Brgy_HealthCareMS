<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineDispense;
use App\Models\MedicineBatch;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::with('batches')
            ->orderBy('name')
            ->paginate(10);

        // Calculate statistics based on batches
        $allMedicines = Medicine::with('batches')->get();

        $totalMedicines = $allMedicines->count();
        $totalStock = $allMedicines->sum->quantity_on_hand;
        $lowStockCount = $allMedicines->filter(function ($medicine) {
            return $medicine->reorder_level > 0 && $medicine->quantity_on_hand <= $medicine->reorder_level;
        })->count();

        $expiringSoonCount = MedicineBatch::where('quantity_on_hand', '>', 0)
            ->whereDate('expiry_date', '>=', now())
            ->whereDate('expiry_date', '<=', now()->copy()->addDays(30))
            ->count();

        return view('medicine.index', compact(
            'medicines',
            'totalMedicines',
            'totalStock',
            'lowStockCount',
            'expiringSoonCount'
        ));
    }

    public function show(Medicine $medicine)
    {
        return response()->json($medicine);
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
            'description' => 'Added medicine: ' . $medicine->name,
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
            'reorder_level' => ['nullable', 'integer', 'min:0'],
            'remarks' => ['nullable', 'string'],
        ]);

        $medicine->update($validated);

        AuditLog::create([
            'user_id' => $request->user()->id ?? null,
            'user_role' => $request->user()->role ?? null,
            'action' => 'update',
            'module' => 'Medicine',
            'description' => 'Updated medicine: ' . $medicine->name,
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
            'description' => 'Deleted medicine: ' . $name,
            'ip_address' => request()->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('medicine.index')->with('success', 'Medicine deleted successfully');
    }

    public function dispense(Request $request)
    {
        $medicines = Medicine::orderBy('name')->get();
        $query = MedicineDispense::with('medicine');

        $hasAnyFilter = $request->filled('medicine_id')
            || $request->filled('from_date')
            || $request->filled('to_date')
            || $request->filled('dispensed_to');

        if (! $hasAnyFilter) {
            $request->merge([
                'from_date' => now()->subDays(6)->toDateString(),
                'to_date' => now()->toDateString(),
            ]);
        }

        if ($request->filled('medicine_id')) {
            $query->where('medicine_id', $request->input('medicine_id'));
        }

        if ($request->filled('from_date')) {
            $query->whereDate('dispensed_at', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('dispensed_at', '<=', $request->input('to_date'));
        }

        if ($request->filled('dispensed_to')) {
            $query->where('dispensed_to', 'like', '%' . $request->input('dispensed_to') . '%');
        }

        $dispenses = $query
            ->orderByDesc('dispensed_at')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

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
        
        $insufficientStock = false;
        $availableQuantity = 0;
        $dispense = null;

        DB::transaction(function () use ($validated, $medicine, &$dispense, &$insufficientStock, &$availableQuantity) {
            $requestedQty = $validated['quantity'];
            $today = now()->toDateString();

            $batches = MedicineBatch::where('medicine_id', $medicine->id)
                ->where('quantity_on_hand', '>', 0)
                ->whereDate('expiry_date', '>=', $today)
                ->orderBy('expiry_date')
                ->orderBy('date_received')
                ->lockForUpdate()
                ->get();

            $availableQuantity = $batches->sum('quantity_on_hand');

            if ($availableQuantity < $requestedQty) {
                $insufficientStock = true;
                return;
            }

            $remaining = $requestedQty;
            $usedBatches = [];

            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                $deduct = min($batch->quantity_on_hand, $remaining);
                $batch->quantity_on_hand -= $deduct;
                $batch->save();

                $remaining -= $deduct;

                if ($deduct > 0) {
                    $usedBatches[$batch->id] = ($usedBatches[$batch->id] ?? 0) + $deduct;
                }
            }

            $dispenseData = $validated;
            if (empty($dispenseData['dispensed_at'])) {
                $dispenseData['dispensed_at'] = now()->toDateString();
            }

            $dispense = MedicineDispense::create($dispenseData);

            foreach ($usedBatches as $batchId => $qty) {
                $dispense->batches()->attach($batchId, ['quantity' => $qty]);
            }
        });

        if ($insufficientStock) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Insufficient stock to dispense. Available: ' . $availableQuantity . ' ' . $medicine->unit . '.');
        }

        if (!$dispense) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to complete dispensing request. Please try again.');
        }

        AuditLog::create([
            'user_id' => $request->user()->id ?? null,
            'user_role' => $request->user()->role ?? null,
            'action' => 'dispense',
            'module' => 'Medicine',
            'description' => 'Dispensed ' . $dispense->quantity . ' ' . $medicine->unit . ' of ' . $medicine->name,
            'ip_address' => $request->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('medicine.dispense')->with('success', 'Medicine dispensed successfully');
    }
}
