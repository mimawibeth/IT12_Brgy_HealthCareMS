<?php

namespace App\Http\Controllers;

use App\Models\PrenatalRecord;
use App\Models\PrenatalVisit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrenatalRecordController extends Controller
{
    public function index()
    {
        $records = PrenatalRecord::orderByDesc('created_at')->get();

        return view('health-programs.prenatal', compact('records'));
    }

    protected function mapRecordData(Request $request): array
    {
        $dob = $request->input('dob');
        $age = null;

        if ($dob) {
            try {
                $age = Carbon::parse($dob)->age;
            } catch (\Exception $e) {
                $age = null;
            }
        }

        return [
            'mother_name' => $request->input('mother_name'),
            'purok' => $request->input('purok'),
            'age' => $age ?? $request->input('age'),
            'dob' => $dob ?: null,
            'occupation' => $request->input('occupation'),
            'education' => $request->input('education'),
            'is_4ps' => $request->boolean('is_4ps'),
            'four_ps_no' => $request->input('four_ps_no'),
            'cell' => $request->input('cell'),
            'lmp' => $request->input('lmp') ?: null,
            'edc' => $request->input('edc') ?: null,
            'urinalysis' => $request->input('urinalysis'),
            'gravida' => $request->input('gravida') ?: null,
            'para' => $request->input('para') ?: null,
            'abortion' => $request->input('abortion') ?: null,
            'delivery_count' => $request->input('delivery_count') ?: null,
            'last_delivery_date' => $request->input('last_delivery_date') ?: null,
            'delivery_type' => $request->input('delivery_type'),
            'hemoglobin_first' => $request->input('hemoglobin_first'),
            'hemoglobin_second' => $request->input('hemoglobin_second'),
            'blood_type' => $request->input('blood_type'),
            'urinalysis_protein' => $request->input('urinalysis_protein'),
            'urinalysis_sugar' => $request->input('urinalysis_sugar'),
            'husband_name' => $request->input('husband_name'),
            'husband_occupation' => $request->input('husband_occupation'),
            'husband_education' => $request->input('husband_education'),
            'family_religion' => $request->input('family_religion'),
            'amount_prepared' => $request->input('amount_prepared'),
            'philhealth_member' => $request->input('philhealth_member'),
            'delivery_location' => $request->input('delivery_location'),
            'delivery_partner' => $request->input('delivery_partner'),
            'td1' => $request->input('td1') ?: null,
            'td2' => $request->input('td2') ?: null,
            'td3' => $request->input('td3') ?: null,
            'td4' => $request->input('td4') ?: null,
            'td5' => $request->input('td5') ?: null,
            'tdl' => $request->input('tdl') ?: null,
            'fbs' => $request->input('fbs'),
            'rbs' => $request->input('rbs'),
            'ogtt' => $request->input('ogtt'),
            'vdrl' => $request->input('vdrl'),
            'hbsag' => $request->input('hbsag'),
            'hiv' => $request->input('hiv'),
            'extra' => null,
        ];
    }

    protected function syncVisits(PrenatalRecord $record, array $visits): void
    {
        foreach ($visits as $visit) {
            if (empty(array_filter($visit))) {
                continue;
            }

            PrenatalVisit::create([
                'prenatal_record_id' => $record->id,
                'date' => $visit['date'] ?? null,
                'trimester' => $visit['trimester'] ?? null,
                'risk' => $visit['risk'] ?? null,
                'first_visit' => $visit['first_visit'] ?? null,
                'subjective' => $visit['subjective'] ?? null,
                'aog' => $visit['aog'] ?? null,
                'weight' => $visit['weight'] ?? null,
                'height' => $visit['height'] ?? null,
                'bp' => $visit['bp'] ?? null,
                'pr' => $visit['pr'] ?? null,
                'fh' => $visit['fh'] ?? null,
                'fht' => $visit['fht'] ?? null,
                'presentation' => $visit['presentation'] ?? null,
                'bmi' => $visit['bmi'] ?? null,
                'rr' => $visit['rr'] ?? null,
                'hr' => $visit['hr'] ?? null,
                'assessment' => $visit['assessment'] ?? null,
                'plan' => $visit['plan'] ?? null,
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'mother_name' => ['required', 'string', 'max:255'],
            'dob' => ['nullable', 'date'],
        ]);

        $record = PrenatalRecord::create($this->mapRecordData($request));

        if (!$record->record_no) {
            $record->record_no = 'PT-' . str_pad((string) $record->id, 3, '0', STR_PAD_LEFT);
            $record->save();
        }

        $visits = $request->input('visits', []);
        $this->syncVisits($record, $visits);

        return redirect()
            ->route('health-programs.prenatal-view')
            ->with('success', 'Prenatal record saved successfully.');
    }

    public function edit(PrenatalRecord $record)
    {
        $record->load('visits');

        return view('health-programs.prenatal-edit', compact('record'));
    }

    public function storeVisits(Request $request, PrenatalRecord $record)
    {
        $newVisits = $request->input('new_visits', []);
        $this->syncVisits($record, $newVisits);

        return redirect()
            ->route('health-programs.prenatal-edit', $record)
            ->with('success', 'Follow-up visits saved successfully.');
    }
}
