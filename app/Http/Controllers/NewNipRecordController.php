<?php

namespace App\Http\Controllers;

use App\Models\NipRecord;
use App\Models\NipVisit;
use Illuminate\Http\Request;

class NewNipRecordController extends Controller
{
    public function index()
    {
        $records = NipRecord::with('visits')->orderByDesc('created_at')->paginate(10);

        return view('health-programs.newnip', compact('records'));
    }

    protected function mapRecordData(Request $request): array
    {
        return [
            'date' => $request->input('nip_date') ?: null,
            'child_name' => $request->input('nip_child_name'),
            'dob' => $request->input('nip_dob') ?: null,
            'address' => $request->input('nip_address'),
            'mother_name' => $request->input('nip_mother_name'),
            'father_name' => $request->input('nip_father_name'),
            'contact' => $request->input('nip_contact'),
            'place_delivery' => $request->input('nip_place_delivery'),
            'attended_by' => $request->input('nip_attended_by'),
            'sex_baby' => $request->input('nip_sex_baby'),
            'nhts_4ps_id' => $request->input('nip_nhts_4ps_id'),
            'phic_id' => $request->input('nip_phic_id'),
            'tt_status_mother' => $request->input('nip_tt_status_mother'),
            'birth_length' => $request->input('nip_birth_length'),
            'birth_weight' => $request->input('nip_birth_weight'),
            'delivery_type' => $request->input('nip_delivery_type'),
            'initiated_breastfeeding' => $request->input('nip_initiated_breastfeeding'),
            'birth_order' => $request->input('nip_birth_order') ?: null,
            'newborn_screening_date' => $request->input('nip_newborn_screening_date') ?: null,
            'newborn_screening_result' => $request->input('nip_newborn_screening_result'),
            'hearing_test_screened' => $request->input('nip_hearing_test_screened'),
            'vit_k' => $request->input('nip_vit_k'),
            'bcg' => $request->input('nip_bcg'),
            'hepa_b_24h' => $request->input('nip_hepa_b_24h'),
        ];
    }

    protected function syncVisits(NipRecord $record, array $visits): void
    {
        foreach ($visits as $visit) {
            if (empty(array_filter($visit, function ($value) {
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
            'nip_child_name' => ['required', 'string', 'max:255'],
            'nip_dob' => ['required', 'date'],
            'nip_address' => ['required', 'string', 'max:255'],
            'nip_mother_name' => ['required', 'string', 'max:255'],
            'nip_contact' => ['required', 'string', 'max:50'],
            'nip_place_delivery' => ['required', 'string', 'max:255'],
            'nip_attended_by' => ['required', 'string', 'max:255'],
            'nip_sex_baby' => ['required', 'in:M,F'],
            'nip_tt_status_mother' => ['required', 'string', 'max:255'],
            'nip_birth_weight' => ['required', 'string', 'max:50'],
            'nip_delivery_type' => ['required', 'string', 'max:50'],
            'nip_initiated_breastfeeding' => ['required', 'string', 'max:10'],
            'nip_newborn_screening_date' => ['required', 'date'],
            'nip_newborn_screening_result' => ['required', 'string', 'max:255'],
            'nip_hearing_test_screened' => ['required', 'string', 'max:50'],
            'nip_vit_k' => ['required', 'string', 'max:50'],
            'nip_bcg' => ['required', 'string', 'max:50'],
            'nip_hepa_b_24h' => ['required', 'string', 'max:50'],
            'nip_birth_order' => ['nullable', 'integer', 'min:1'],
        ]);

        $record = NipRecord::create($this->mapRecordData($request));

        if (!$record->record_no) {
            $record->record_no = 'NIP-' . str_pad((string) $record->id, 3, '0', STR_PAD_LEFT);
            $record->save();
        }

        $visits = $request->input('new_visits', []);
        $this->syncVisits($record, $visits);

        return redirect()
            ->route('health-programs.new-nip-view')
            ->with('success', 'NIP record saved successfully.');
    }

    public function create()
    {
        return view('health-programs.newnip-edit');
    }

    public function edit(NipRecord $record)
    {
        $record->load('visits');

        return view('health-programs.newnip-edit', compact('record'));
    }

    public function update(Request $request, NipRecord $record)
    {
        $request->validate([
            'nip_date' => ['required', 'date'],
            'nip_child_name' => ['required', 'string', 'max:255'],
            'nip_dob' => ['required', 'date'],
            'nip_address' => ['required', 'string', 'max:255'],
            'nip_mother_name' => ['required', 'string', 'max:255'],
            'nip_contact' => ['required', 'string', 'max:50'],
            'nip_place_delivery' => ['required', 'string', 'max:255'],
            'nip_attended_by' => ['required', 'string', 'max:255'],
            'nip_sex_baby' => ['required', 'in:M,F'],
            'nip_tt_status_mother' => ['required', 'string', 'max:255'],
            'nip_birth_weight' => ['required', 'string', 'max:50'],
            'nip_delivery_type' => ['required', 'string', 'max:50'],
            'nip_initiated_breastfeeding' => ['required', 'string', 'max:10'],
            'nip_newborn_screening_date' => ['required', 'date'],
            'nip_newborn_screening_result' => ['required', 'string', 'max:255'],
            'nip_hearing_test_screened' => ['required', 'string', 'max:50'],
            'nip_vit_k' => ['required', 'string', 'max:50'],
            'nip_bcg' => ['required', 'string', 'max:50'],
            'nip_hepa_b_24h' => ['required', 'string', 'max:50'],
            'nip_birth_order' => ['nullable', 'integer', 'min:1'],
        ]);

        $record->update($this->mapRecordData($request));

        $visits = $request->input('new_visits', []);
        $existingVisitIds = [];

        foreach ($visits as $visit) {
            $visitData = array_filter($visit, function ($key) {
                return $key !== 'id';
            }, ARRAY_FILTER_USE_KEY);

            if (empty(array_filter($visitData, function ($value) {
                return $value !== null && $value !== '';
            }))) {
                continue;
            }

            $visitId = $visit['id'] ?? null;

            if ($visitId) {
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

        if (!empty($existingVisitIds)) {
            $record->visits()->whereNotIn('id', $existingVisitIds)->delete();
        } else {
            $record->visits()->delete();
        }

        return redirect()
            ->route('health-programs.new-nip-view')
            ->with('success', 'NIP record updated successfully.');
    }
}
