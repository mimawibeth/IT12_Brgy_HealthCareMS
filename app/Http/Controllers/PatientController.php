<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Assessment;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('patientNo', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%')
                    ->orWhere('contactNumber', 'like', '%' . $search . '%');
            });
        }

        $gender = $request->input('gender');
        if ($gender === 'male') {
            $query->where('sex', 'M');
        } elseif ($gender === 'female') {
            $query->where('sex', 'F');
        }

        $ageGroup = $request->input('age_group');
        if ($ageGroup) {
            switch ($ageGroup) {
                case 'child':
                    $minAge = 0;
                    $maxAge = 12;
                    break;
                case 'teen':
                    $minAge = 13;
                    $maxAge = 19;
                    break;
                case 'adult':
                    $minAge = 20;
                    $maxAge = 59;
                    break;
                case 'senior':
                    $minAge = 60;
                    $maxAge = 200;
                    break;
                default:
                    $minAge = null;
                    $maxAge = null;
                    break;
            }

            if ($minAge !== null) {
                $query->whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN ? AND ?', [$minAge, $maxAge]);
            }
        }

        $patients = $query->orderByDesc('dateRegistered')->paginate(10)->withQueryString();

        return view('patients.index', compact('patients', 'search', 'gender', 'ageGroup'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_registered' => ['required', 'date'],
            'sex' => ['required', 'in:M,F'],
            'name' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string'],
            'nhts_id_no' => ['nullable', 'string', 'max:100'],
            'pwd_id_no' => ['nullable', 'string', 'max:100'],
            'phic_id_no' => ['nullable', 'string', 'max:100'],
            '4ps_cct_id_no' => ['nullable', 'string', 'max:100'],
            'ethnic_group' => ['nullable', 'string', 'max:255'],
        ]);

        $smokingStatus = $request->input('smoking_status');

        $patient = Patient::create([
            'dateRegistered' => $validated['date_registered'],
            'patientNo' => $request->input('patient_no'),
            'sex' => $validated['sex'],
            'name' => $validated['name'],
            'birthday' => $validated['birthday'],
            'contactNumber' => $validated['contact_number'] ?? null,
            'address' => $validated['address'],
            'nhtsIdNo' => $validated['nhts_id_no'] ?? null,
            'pwdIdNo' => $validated['pwd_id_no'] ?? null,
            'phicIdNo' => $validated['phic_id_no'] ?? null,
            'fourPsCctIdNo' => $validated['4ps_cct_id_no'] ?? null,
            'ethnicGroup' => $validated['ethnic_group'] ?? null,

            'diabetesDate' => $request->input('diabetes_date') ?: null,
            'hypertensionDate' => $request->input('hypertension_date') ?: null,
            'copdDate' => $request->input('copd_date') ?: null,
            'asthmaDate' => $request->input('asthma_date') ?: null,
            'cataractDate' => $request->input('cataract_date') ?: null,
            'eorDate' => $request->input('eor_date') ?: null,
            'diabeticRetinopathyDate' => $request->input('diabetic_retinopathy_date') ?: null,
            'otherEyeDiseaseDate' => $request->input('other_eye_disease_date') ?: null,
            'alcoholismDate' => $request->input('alcoholism_date') ?: null,
            'substanceAbuseDate' => $request->input('substance_abuse_date') ?: null,
            'otherMentalDisordersDate' => $request->input('other_mental_disorders_date') ?: null,
            'atRiskSuicideDate' => $request->input('at_risk_suicide_date') ?: null,

            'philpenDate' => $request->input('philpen_date') ?: null,
            'currentSmoker' => $smokingStatus === 'current',
            'passiveSmoker' => $smokingStatus === 'passive',
            'stoppedSmoking' => $smokingStatus === 'stopped',
            'drinksAlcohol' => $request->boolean('drinks_alcohol'),
            'hadFiveDrinks' => $request->boolean('had_5_drinks'),
            'dietaryRiskFactors' => $request->boolean('dietary_risk_factors'),
            'physicalInactivity' => $request->boolean('physical_inactivity'),
            'height' => $request->input('height'),
            'weight' => $request->input('weight'),
            'waistCircumference' => $request->input('waist_circumference'),
            'bmi' => $request->input('bmi'),

            'whoDasDate' => $request->input('who_das_date') ?: null,
            'part1' => $request->input('part1'),
            'part2Score' => $request->input('part2_score'),
            'top1Domain' => $request->input('top1_domain'),
            'top2Domain' => $request->input('top2_domain'),
            'top3Domain' => $request->input('top3_domain'),

            'lengthDiabetes' => $request->input('length_diabetes'),
            'lengthHypertension' => $request->input('length_hypertension'),

            'floaters' => $request->boolean('floaters'),
            'blurredVision' => $request->boolean('blurred_vision'),
            'fluctuatingVision' => $request->boolean('fluctuating_vision'),
            'impairedColorVision' => $request->boolean('impaired_color_vision'),
            'darkEmptyAreas' => $request->boolean('dark_empty_areas'),
            'visionLoss' => $request->boolean('vision_loss'),

            'visualAcuityLeft' => $request->input('visual_acuity_left'),
            'visualAcuityRight' => $request->input('visual_acuity_right'),
            'ophthalmoscopyResults' => $request->input('ophthalmoscopy_results'),
        ]);

        $assessments = $request->input('assessments', []);

        foreach ($assessments as $assessmentData) {
            if (!is_array($assessmentData)) {
                continue;
            }

            $nonEmpty = array_filter($assessmentData, function ($value) {
                return $value !== null && $value !== '';
            });

            if (empty($nonEmpty)) {
                continue;
            }

            $assessmentDate = $assessmentData['date'] ?? null;
            $calculatedAge = null;

            if ($patient->birthday && $assessmentDate) {
                try {
                    $calculatedAge = Carbon::parse($assessmentDate)
                        ->diffInYears(Carbon::parse($patient->birthday));
                } catch (\Exception $e) {
                    $calculatedAge = null;
                }
            }

            Assessment::create([
                'PatientID' => $patient->PatientID,
                'date' => $assessmentDate,
                'age' => $calculatedAge,
                'cvdRisk' => $assessmentData['cvd_risk'] ?? null,
                'bpSystolic' => $assessmentData['bp_systolic'] ?? null,
                'bpDiastolic' => $assessmentData['bp_diastolic'] ?? null,
                'wt' => $assessmentData['wt'] ?? null,
                'ht' => $assessmentData['ht'] ?? null,
                'fbsRbs' => $assessmentData['fbs_rbs'] ?? null,
                'lipidProfile' => $assessmentData['lipid_profile'] ?? null,
                'urineKetones' => $assessmentData['urine_ketones'] ?? null,
                'urineProtein' => $assessmentData['urine_protein'] ?? null,
                'footCheck' => $assessmentData['foot_check'] ?? null,
                'chiefComplaint' => $assessmentData['chief_complaint'] ?? null,
                'historyPhysical' => $assessmentData['history_physical'] ?? null,
                'management' => $assessmentData['management'] ?? null,
            ]);
        }

        return redirect()->route('patients.index')
            ->with('success', 'Patient record created successfully.');
    }

    public function show(Patient $patient)
    {
        $patient->load('assessments');

        return response()->json([
            'patient' => $patient,
            'assessments' => $patient->assessments,
        ]);
    }

    public function edit($id)
    {
        $patient = Patient::with('assessments')->findOrFail($id);
        return view('patients.edit', compact('patient'));
    }

    public function storeAssessments(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $assessments = $request->input('assessments', []);

        foreach ($assessments as $assessmentData) {
            if (!is_array($assessmentData)) {
                continue;
            }

            $nonEmpty = array_filter($assessmentData, function ($value) {
                return $value !== null && $value !== '';
            });

            if (empty($nonEmpty)) {
                continue;
            }

            $assessmentDate = $assessmentData['date'] ?? null;
            $calculatedAge = null;

            // Calculate age based on today's date, not assessment date
            if ($patient->birthday) {
                try {
                    $calculatedAge = Carbon::parse($patient->birthday)->age;
                } catch (\Exception $e) {
                    $calculatedAge = null;
                }
            }

            Assessment::create([
                'PatientID' => $patient->PatientID,
                'date' => $assessmentDate,
                'age' => $calculatedAge ?? $assessmentData['age'] ?? null,
                'cvdRisk' => $assessmentData['cvd_risk'] ?? null,
                'bpSystolic' => $assessmentData['bp_systolic'] ?? null,
                'bpDiastolic' => $assessmentData['bp_diastolic'] ?? null,
                'wt' => $assessmentData['wt'] ?? null,
                'ht' => $assessmentData['ht'] ?? null,
                'fbsRbs' => $assessmentData['fbs_rbs'] ?? null,
                'lipidProfile' => $assessmentData['lipid_profile'] ?? null,
                'urineKetones' => $assessmentData['urine_ketones'] ?? null,
                'urineProtein' => $assessmentData['urine_protein'] ?? null,
                'footCheck' => $assessmentData['foot_check'] ?? null,
                'chiefComplaint' => $assessmentData['chief_complaint'] ?? null,
                'historyPhysical' => $assessmentData['history_physical'] ?? null,
                'management' => $assessmentData['management'] ?? null,
            ]);
        }

        return redirect()->route('patients.edit', $patient->PatientID)
            ->with('success', 'Assessment(s) added successfully.');
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $validated = $request->validate([
            'lastname' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'age' => ['nullable', 'integer', 'min:0'],
            'sex' => ['nullable', 'in:Male,Female'],
            'civil_status' => ['nullable', 'string', 'max:50'],
            'purok' => ['nullable', 'string', 'max:100'],
            'barangay' => ['nullable', 'string', 'max:100'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'contact_no' => ['nullable', 'string', 'max:20'],
            'religion' => ['nullable', 'string', 'max:100'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'phil_member' => ['nullable', 'in:Yes,No'],
            'phil_no' => ['nullable', 'string', 'max:50'],
            '4Ps' => ['nullable', 'in:Yes,No'],
            'nhts' => ['nullable', 'in:Yes,No'],
        ]);

        $patient->update($validated);

        AuditLog::create([
            'user_id' => $request->user()->id ?? null,
            'user_role' => $request->user()->role ?? null,
            'action' => 'update',
            'module' => 'Patient Management',
            'description' => 'Updated patient record: ' . $patient->firstname . ' ' . $patient->lastname,
            'ip_address' => $request->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully');
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patientName = $patient->firstname . ' ' . $patient->lastname;

        $patient->delete();

        AuditLog::create([
            'user_id' => request()->user()->id ?? null,
            'user_role' => request()->user()->role ?? null,
            'action' => 'delete',
            'module' => 'Patient Management',
            'description' => 'Deleted patient record: ' . $patientName,
            'ip_address' => request()->ip(),
            'status' => 'success',
        ]);

        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully');
    }
}
