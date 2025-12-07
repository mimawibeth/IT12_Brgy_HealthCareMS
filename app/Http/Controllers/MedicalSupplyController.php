<?php

namespace App\Http\Controllers;

use App\Models\MedicalSupply;
use App\Models\SupplyHistory;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalSupplyController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicalSupply::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $supplies = $query->orderBy('item_name')->paginate(10)->appends($request->query());

        $categories = MedicalSupply::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');

        return view('medical-supplies.index', compact('supplies', 'categories'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $supplies = MedicalSupply::where('item_name', 'like', '%' . $query . '%')
            ->limit(10)
            ->get()
            ->map(function ($supply) {
                return [
                    'id' => $supply->id,
                    'item_name' => $supply->item_name,
                    'category' => $supply->category,
                    'description' => $supply->description,
                    'unit_of_measure' => $supply->unit_of_measure,
                ];
            });

        return response()->json($supplies);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit_of_measure' => ['nullable', 'string', 'max:50'],
            'quantity' => ['required', 'integer', 'min:1'],
            'received_from' => ['nullable', 'string', 'max:255'],
            'date_received' => ['required', 'date'],
        ]);

        DB::beginTransaction();
        try {
            // Check if item already exists
            $supply = MedicalSupply::where('item_name', $validated['item_name'])->first();

            if ($supply) {
                // Item exists - just add to quantity
                $supply->quantity_on_hand += $validated['quantity'];
                $supply->save();
            } else {
                // New item - create it
                $supply = MedicalSupply::create([
                    'item_name' => $validated['item_name'],
                    'category' => $validated['category'],
                    'description' => $validated['description'],
                    'unit_of_measure' => $validated['unit_of_measure'],
                    'quantity_on_hand' => $validated['quantity'],
                ]);
            }

            // Record in supply history
            SupplyHistory::create([
                'medical_supply_id' => $supply->id,
                'item_name' => $supply->item_name,
                'quantity' => $validated['quantity'],
                'received_from' => $validated['received_from'],
                'date_received' => $validated['date_received'],
                'handled_by' => auth()->user()->name ?? 'System',
            ]);

            // Audit log
            AuditLog::create([
                'user_id' => auth()->id(),
                'user_role' => auth()->user()->role ?? null,
                'action' => 'create',
                'module' => 'Medical Supplies',
                'description' => 'Added supply: ' . $supply->item_name . ' (Qty: ' . $validated['quantity'] . ')',
                'ip_address' => $request->ip(),
                'status' => 'success',
            ]);

            DB::commit();
            return redirect()->route('medical-supplies.index')->with('success', 'Supply added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error adding supply: ' . $e->getMessage());
        }
    }

    public function show(MedicalSupply $supply)
    {
        return response()->json($supply->load('supplyHistory'));
    }

    public function dispense(Request $request, MedicalSupply $supply)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'date_dispensed' => ['required', 'date'],
        ]);

        DB::beginTransaction();
        try {
            if ($supply->quantity_on_hand < $validated['quantity']) {
                return redirect()->back()->with('error', 'Not enough stock to dispense the requested quantity.');
            }

            $supply->quantity_on_hand -= $validated['quantity'];
            $supply->save();

            $description = $validated['remarks'] ?? 'Dispensed';

            SupplyHistory::create([
                'medical_supply_id' => $supply->id,
                'item_name' => $supply->item_name,
                'quantity' => -$validated['quantity'],
                'received_from' => $description,
                'date_received' => $validated['date_dispensed'],
                'handled_by' => auth()->user()->name ?? 'System',
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_role' => auth()->user()->role ?? null,
                'action' => 'update',
                'module' => 'Medical Supplies',
                'description' => 'Dispensed supply: ' . $supply->item_name . ' (Qty: ' . $validated['quantity'] . ')',
                'ip_address' => $request->ip(),
                'status' => 'success',
            ]);

            DB::commit();
            return redirect()->route('medical-supplies.index')->with('success', 'Supply dispensed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error dispensing supply: ' . $e->getMessage());
        }
    }

    public function history(Request $request)
    {
        $query = SupplyHistory::with('medicalSupply');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('received_from', 'like', "%{$search}%")
                    ->orWhere('handled_by', 'like', "%{$search}%");
            });
        }

        // Source filter
        if ($request->filled('source')) {
            $query->where('received_from', 'like', '%' . $request->input('source') . '%');
        }

        // Date range filter
        if ($request->filled('date_range')) {
            $range = $request->input('date_range');
            $now = now();

            switch ($range) {
                case 'today':
                    $query->whereDate('date_received', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('date_received', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('date_received', $now->month)
                        ->whereYear('date_received', $now->year);
                    break;
                case 'quarter':
                    $query->whereBetween('date_received', [$now->startOfQuarter(), $now->endOfQuarter()]);
                    break;
                case 'year':
                    $query->whereYear('date_received', $now->year);
                    break;
            }
        }

        $history = $query->orderBy('date_received', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        $sources = SupplyHistory::select('received_from')
            ->distinct()
            ->whereNotNull('received_from')
            ->orderBy('received_from')
            ->pluck('received_from');

        return view('medicine.barangay-supply-history', compact('history', 'sources'));
    }
}
