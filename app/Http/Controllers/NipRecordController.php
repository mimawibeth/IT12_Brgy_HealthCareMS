<?php

namespace App\Http\Controllers;

use App\Models\NipRecord;
use App\Models\NipVisit;
use Illuminate\Http\Request;

class NipRecordController extends Controller
{
    public function index()
    {
        $records = NipRecord::with('visits')->orderByDesc('created_at')->paginate(10);

        return view('health-programs.nip', compact('records'));
    }

    protected function mapRecordData(Request $request): array
    {
        return [
            'date' => $request->input('nip_date') ?: null,
            'child_name' => $request->input('child_name'),
            'dob' => $request->input('dob') ?: null,
            'address' => $request->input('address'),
            'mother_name' => $request->input('mother_name'),
            'father_name' => $request->input('father_name'),
            'contact' => $request->input('contact'),
            'place_delivery' => $request->input('place_delivery'),
            'attended_by' => $request->input('attended_by'),
            'sex_baby' => $request->input('sex_baby'),
            'nhts_4ps_id' => $request->input('nhts_4ps_id'),
            'phic_id' => $request->input('phic_id'),
            'tt_status_mother' => $request->input('tt_status_mother'),
            'birth_length' => $request->input('birth_length'),
            'birth_weight' => $request->input('birth_weight'),
            'delivery_type' => $request->input('delivery_type'),
            'initiated_breastfeeding' => $request->input('initiated_breastfeeding'),
            'birth_order' => $request->input('birth_order') ?: null,
            'newborn_screening_date' => $request->input('newborn_screening_date') ?: null,
            'newborn_screening_result' => $request->input('newborn_screening_result'),
            'hearing_test_screened' => $request->input('hearing_test_screened'),
            'vit_k' => $request->input('vit_k'),
            'bcg' => $request->input('bcg'),
            'hepa_b_24h' => $request->input('hepa_b_24h'),
        ];
    }

    protected function syncVisits(NipRecord $record, array $visits): void
    {
        foreach ($visits as $visit) {
            // Check if visit has any meaningful data
            if (empty(array_filter($visit, function($value) {
                return $value !== null && $value !== '';
            }))) {
                continue;
            }

            NipVisit::create([
                'nip_record_id' => $record->id,
                'visit_date' => $visit['date'] ?? null,
                'age_months' => $visit['age'] ?? null,
                'weight' => $visit['weight'] ?? null,
                'length' => $visit['length'] ?? null,
                'status' => $visit['status'] ?? null,
                'breastfeeding' => $visit['breast'] ?? null,
                'temperature' => $visit['temp'] ?? null,
                'vaccine' => $visit['vaccine'] ?? null,
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip_date' => ['required', 'date'],
            'child_name' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'date'],
            'address' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'contact' => ['required', 'string', 'max:50'],
            'place_delivery' => ['required', 'string', 'max:255'],
            'attended_by' => ['required', 'string', 'max:255'],
            'sex_baby' => ['required', 'in:M,F'],
            'tt_status_mother' => ['required', 'string', 'max:255'],
            'birth_weight' => ['required', 'string', 'max:50'],
            'delivery_type' => ['required', 'string', 'max:50'],
            'initiated_breastfeeding' => ['required', 'string', 'max:10'],
            'newborn_screening_date' => ['required', 'date'],
            'newborn_screening_result' => ['required', 'string', 'max:255'],
            'hearing_test_screened' => ['required', 'string', 'max:50'],
            'vit_k' => ['required', 'string', 'max:50'],
            'bcg' => ['required', 'string', 'max:50'],
            'hepa_b_24h' => ['required', 'string', 'max:50'],
            'birth_order' => ['nullable', 'integer', 'min:1'],
        ]);

        $record = NipRecord::create($this->mapRecordData($request));

        if (!$record->record_no) {
            $record->record_no = 'NIP-' . str_pad((string) $record->id, 3, '0', STR_PAD_LEFT);
            $record->save();
        }

        $visits = $request->input('visits', []);
        $this->syncVisits($record, $visits);

        return redirect()
            ->route('health-programs.nip-view')
            ->with('success', 'NIP record saved successfully.');
    }

    public function edit(NipRecord $record)
    {
        $record->load('visits');

        return view('health-programs.nip-edit', compact('record'));
    }

    public function update(Request $request, NipRecord $record)
    {
        $request->validate([
            'nip_date' => ['required', 'date'],
            'child_name' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'date'],
            'address' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'contact' => ['required', 'string', 'max:50'],
            'place_delivery' => ['required', 'string', 'max:255'],
            'attended_by' => ['required', 'string', 'max:255'],
            'sex_baby' => ['required', 'in:M,F'],
            'tt_status_mother' => ['required', 'string', 'max:255'],
            'birth_weight' => ['required', 'string', 'max:50'],
            'delivery_type' => ['required', 'string', 'max:50'],
            'initiated_breastfeeding' => ['required', 'string', 'max:10'],
            'newborn_screening_date' => ['required', 'date'],
            'newborn_screening_result' => ['required', 'string', 'max:255'],
            'hearing_test_screened' => ['required', 'string', 'max:50'],
            'vit_k' => ['required', 'string', 'max:50'],
            'bcg' => ['required', 'string', 'max:50'],
            'hepa_b_24h' => ['required', 'string', 'max:50'],
            'birth_order' => ['nullable', 'integer', 'min:1'],
        ]);

        $record->update($this->mapRecordData($request));

        // Handle visits update
        $visits = $request->input('visits', []);
        $existingVisitIds = [];
        
        foreach ($visits as $visit) {
            // Check if visit has any meaningful data (excluding hidden id field)
            $visitData = array_filter($visit, function($key) {
                return $key !== 'id';
            }, ARRAY_FILTER_USE_KEY);
            
            if (empty(array_filter($visitData, function($value) {
                return $value !== null && $value !== '';
            }))) {
                continue;
            }

            $visitId = $visit['id'] ?? null;
            
            if ($visitId) {
                // Update existing visit
                $existingVisit = NipVisit::find($visitId);
                if ($existingVisit && $existingVisit->nip_record_id === $record->id) {
                    $existingVisit->update([
                        'visit_date' => $visit['date'] ?? null,
                        'age_months' => $visit['age'] ?? null,
                        'weight' => $visit['weight'] ?? null,
                        'length' => $visit['length'] ?? null,
                        'status' => $visit['status'] ?? null,
                        'breastfeeding' => $visit['breast'] ?? null,
                        'temperature' => $visit['temp'] ?? null,
                        'vaccine' => $visit['vaccine'] ?? null,
                    ]);
                    $existingVisitIds[] = $visitId;
                }
            } else {
                // Create new visit
                $newVisit = NipVisit::create([
                    'nip_record_id' => $record->id,
                    'visit_date' => $visit['date'] ?? null,
                    'age_months' => $visit['age'] ?? null,
                    'weight' => $visit['weight'] ?? null,
                    'length' => $visit['length'] ?? null,
                    'status' => $visit['status'] ?? null,
                    'breastfeeding' => $visit['breast'] ?? null,
                    'temperature' => $visit['temp'] ?? null,
                    'vaccine' => $visit['vaccine'] ?? null,
                ]);
                $existingVisitIds[] = $newVisit->id;
            }
        }

        // Delete visits that were removed (only if there are existing visit IDs)
        if (!empty($existingVisitIds)) {
            $record->visits()->whereNotIn('id', $existingVisitIds)->delete();
        } else {
            // If no visits were submitted, delete all existing visits
            $record->visits()->delete();
        }

        return redirect()
            ->route('health-programs.nip-view')
            ->with('success', 'NIP record updated successfully.');
    }
}
