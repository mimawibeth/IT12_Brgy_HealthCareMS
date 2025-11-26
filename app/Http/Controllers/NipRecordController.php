<?php

namespace App\Http\Controllers;

use App\Models\NipRecord;
use App\Models\NipVisit;
use Illuminate\Http\Request;

class NipRecordController extends Controller
{
    public function index()
    {
        $records = NipRecord::orderByDesc('created_at')->get();

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
        ];
    }

    protected function syncVisits(NipRecord $record, array $visits): void
    {
        foreach ($visits as $visit) {
            if (empty(array_filter($visit))) {
                continue;
            }

            NipVisit::create([
                'nip_record_id' => $record->id,
                'age_months' => $visit['age'] ?? null,
                'weight' => $visit['weight'] ?? null,
                'length' => $visit['length'] ?? null,
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
        $record->update($this->mapRecordData($request));

        $visitData = [
            'age' => $request->input('edit_visit_age'),
            'vaccine' => $request->input('edit_visit_vaccine'),
        ];

        if (!empty(array_filter($visitData))) {
            $visit = $record->visits()->first();

            if ($visit) {
                $visit->update([
                    'age_months' => $visitData['age'],
                    'vaccine' => $visitData['vaccine'],
                ]);
            } else {
                NipVisit::create([
                    'nip_record_id' => $record->id,
                    'age_months' => $visitData['age'],
                    'vaccine' => $visitData['vaccine'],
                ]);
            }
        }

        return redirect()
            ->route('health-programs.nip-view')
            ->with('success', 'NIP record updated successfully.');
    }
}
