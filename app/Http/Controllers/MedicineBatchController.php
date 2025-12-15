<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use Illuminate\Http\Request;

class MedicineBatchController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicineBatch::with('medicine')
            ->orderBy('medicine_id')
            ->orderBy('expiry_date')
            ->orderByDesc('date_received');

        if ($request->filled('medicine_id')) {
            $query->where('medicine_id', $request->input('medicine_id'));
        }

        if ($request->input('filter') === 'expiring') {
            $query->whereDate('expiry_date', '>=', now())
                ->whereDate('expiry_date', '<=', now()->copy()->addDays(30));
        } elseif ($request->input('filter') === 'expired') {
            $query->whereDate('expiry_date', '<', now());
        }

        $allBatches = $query->get()->groupBy('medicine_id');

        // Convert grouped collection to flat array for pagination
        $batchesArray = [];
        foreach ($allBatches as $medicineId => $medicineBatches) {
            $batchesArray[] = [
                'medicine_id' => $medicineId,
                'batches' => $medicineBatches
            ];
        }

        // Paginate the grouped batches
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $paginatedItems = array_slice($batchesArray, $offset, $perPage);

        $batches = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            count($batchesArray),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        if ($request->wantsJson()) {
            return response()->json($batches);
        }

        $medicines = Medicine::orderBy('name')->get();

        return view('medicine.batches', compact('batches', 'medicines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => ['required', 'exists:medicines,id'],
            'batch_code' => ['nullable', 'string', 'max:100'],
            'quantity_on_hand' => ['required', 'integer', 'min:0'],
            'expiry_date' => ['required', 'date'],
            'date_received' => ['required', 'date'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $batch = MedicineBatch::create($validated);

        if ($request->wantsJson()) {
            return response()->json($batch, 201);
        }

        return redirect()->route('medicine.batches.index')
            ->with('success', 'Medicine batch added successfully');
    }

    public function update(Request $request, MedicineBatch $batch)
    {
        $validated = $request->validate([
            'batch_code' => ['nullable', 'string', 'max:100'],
            'quantity_on_hand' => ['required', 'integer', 'min:0'],
            'expiry_date' => ['required', 'date'],
            'date_received' => ['required', 'date'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $batch->update($validated);

        if ($request->wantsJson()) {
            return response()->json($batch);
        }

        return redirect()->route('medicine.batches.index')
            ->with('success', 'Medicine batch updated successfully');
    }

    public function destroy(MedicineBatch $batch)
    {
        $batch->delete();

        if (request()->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('medicine.batches.index')
            ->with('success', 'Medicine batch deleted successfully');
    }
}
