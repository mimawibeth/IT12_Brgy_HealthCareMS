<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Patient;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function store(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'date' => ['nullable', 'date'],
            'age' => ['nullable', 'string', 'max:50'],
            'cvd_risk' => ['nullable', 'string', 'max:100'],
            'bp_systolic' => ['nullable', 'string', 'max:50'],
            'bp_diastolic' => ['nullable', 'string', 'max:50'],
            'wt' => ['nullable', 'string', 'max:50'],
            'ht' => ['nullable', 'string', 'max:50'],
            'fbs_rbs' => ['nullable', 'string', 'max:50'],
            'lipid_profile' => ['nullable', 'string', 'max:100'],
            'urine_ketones' => ['nullable', 'string', 'max:100'],
            'urine_protein' => ['nullable', 'string', 'max:100'],
            'foot_check' => ['nullable', 'string', 'max:100'],
            'chief_complaint' => ['nullable', 'string'],
            'history_physical' => ['nullable', 'string'],
            'management' => ['nullable', 'string'],
        ]);

        Assessment::create([
            'PatientID' => $patient->PatientID,
            'date' => $data['date'] ?? null,
            'age' => $data['age'] ?? null,
            'cvdRisk' => $data['cvd_risk'] ?? null,
            'bpSystolic' => $data['bp_systolic'] ?? null,
            'bpDiastolic' => $data['bp_diastolic'] ?? null,
            'wt' => $data['wt'] ?? null,
            'ht' => $data['ht'] ?? null,
            'fbsRbs' => $data['fbs_rbs'] ?? null,
            'lipidProfile' => $data['lipid_profile'] ?? null,
            'urineKetones' => $data['urine_ketones'] ?? null,
            'urineProtein' => $data['urine_protein'] ?? null,
            'footCheck' => $data['foot_check'] ?? null,
            'chiefComplaint' => $data['chief_complaint'] ?? null,
            'historyPhysical' => $data['history_physical'] ?? null,
            'management' => $data['management'] ?? null,
        ]);

        return back()->with('success', 'Assessment saved successfully.');
    }
}
