<?php

namespace App\Http\Controllers;

use App\Models\FamilyPlanningRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FamilyPlanningRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = FamilyPlanningRecord::query();

        // Search by client name or FP number
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                    ->orWhere('record_no', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Filter by client type
        if ($request->filled('client_type')) {
            $query->where('client_type', $request->input('client_type'));
        }

        // Filter by reason
        if ($request->filled('reason')) {
            $reason = $request->input('reason');
            $query->whereJsonContains('reason', $reason);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $records = $query->orderByDesc('created_at')->paginate(10);

        return view('health-programs.family-planning', compact('records'));
    }

    public function show(FamilyPlanningRecord $record)
    {
        return response()->json($record);
    }

    protected function mapRequestToData(Request $request): array
    {
        $dob = $request->input('fp_dob');
        $age = null;

        if ($dob) {
            try {
                $age = Carbon::parse($dob)->age;
            } catch (\Exception $e) {
                $age = null;
            }
        }

        return [
            'client_name' => $request->input('fp_client_name'),
            'dob' => $dob ?: null,
            'age' => $age ?? $request->input('fp_age'),
            'address' => $request->input('fp_address'),
            'contact' => $request->input('fp_contact'),
            'occupation' => $request->input('fp_occupation'),
            'spouse_name' => $request->input('fp_spouse'),
            'spouse_age' => $request->input('fp_spouse_age') ?: null,
            'children_count' => $request->input('fp_children') ?: null,
            'client_type' => $request->input('fp_type'),
            'reason' => $request->input('fp_reason', []),
            'medical_history' => $request->input('fp_med_history', []),
            'gravida' => $request->input('fp_gravida') ?: null,
            'para' => $request->input('fp_para') ?: null,
            'last_delivery' => $request->input('fp_last_delivery') ?: null,
            'last_period' => $request->input('fp_last_period') ?: null,
            'menstrual_flow' => $request->input('fp_menstrual_flow'),
            'dysmenorrhea' => $request->input('fp_dysmenorrhea'),
            'sti_risk' => $request->input('fp_sti', []),
            'vaw_risk' => $request->input('fp_vaw', []),
            'bp' => $request->input('fp_bp'),
            'weight' => $request->input('fp_weight'),
            'height' => $request->input('fp_height'),
            'exam_findings' => $request->input('fp_exam_findings'),
            'counseled_by' => $request->input('fp_counseled_by'),
            'client_signature' => $request->input('fp_client_signature'),
            'consent_date' => $request->input('fp_consent_date') ?: null,
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'fp_client_name' => ['required', 'string', 'max:255'],
            'fp_dob' => ['nullable', 'date'],
        ]);

        $data = $this->mapRequestToData($request);

        $record = FamilyPlanningRecord::create($data);

        if (!$record->record_no) {
            $record->record_no = 'FP-' . str_pad((string) $record->id, 3, '0', STR_PAD_LEFT);
            $record->save();
        }

        return redirect()
            ->route('health-programs.family-planning-view')
            ->with('success', 'Family planning record saved successfully.');
    }

    public function edit(FamilyPlanningRecord $record)
    {
        return view('health-programs.family-planning-edit', compact('record'));
    }

    public function update(Request $request, FamilyPlanningRecord $record)
    {
        $request->validate([
            'fp_client_name' => ['required', 'string', 'max:255'],
            'fp_dob' => ['nullable', 'date'],
        ]);

        $data = $this->mapRequestToData($request);

        $record->update($data);

        return redirect()
            ->route('health-programs.family-planning-view')
            ->with('success', 'Family planning record updated successfully.');
    }
}
