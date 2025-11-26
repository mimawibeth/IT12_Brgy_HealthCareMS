<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\FamilyPlanningRecord;
use App\Models\PrenatalRecord;
use App\Models\NipRecord;
use App\Models\Medicine;
use App\Models\MedicineDispense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function monthly(Request $request)
    {
        if (($request->user()->role ?? null) === 'bhw') {
            abort(403);
        }

        $now = Carbon::now();
        $month = (int)($request->input('month', $now->month));
        $year = (int)($request->input('year', $now->year));

        $selectedDate = Carbon::create($year, $month, 1);

        // Summary stats for selected month
        $totalPatients = Patient::whereYear('dateRegistered', $year)
            ->whereMonth('dateRegistered', $month)
            ->count();

        $prenatalCount = PrenatalRecord::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $nipCount = NipRecord::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $fpCount = FamilyPlanningRecord::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        // Last 6 months per program for charts
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push($selectedDate->copy()->subMonths($i));
        }

        $monthLabels = $months->map(fn (Carbon $d) => $d->format('M'));

        $prenatalSeries = [];
        $fpSeries = [];
        $nipSeries = [];

        foreach ($months as $m) {
            $y = $m->year;
            $mo = $m->month;

            $prenatalSeries[] = PrenatalRecord::whereYear('created_at', $y)->whereMonth('created_at', $mo)->count();
            $fpSeries[] = FamilyPlanningRecord::whereYear('created_at', $y)->whereMonth('created_at', $mo)->count();
            $nipSeries[] = NipRecord::whereYear('created_at', $y)->whereMonth('created_at', $mo)->count();
        }

        $servicesSeries = [];
        foreach (range(0, 5) as $i) {
            $servicesSeries[$i] = ($prenatalSeries[$i] ?? 0) + ($fpSeries[$i] ?? 0) + ($nipSeries[$i] ?? 0);
        }

        // Program distribution for selected month
        $programDistribution = [
            'prenatal' => $prenatalCount,
            'fp' => $fpCount,
            'nip' => $nipCount,
        ];

        // Gender distribution and age distribution (overall, not by month for now)
        $patients = Patient::all(['sex', 'birthday']);

        $genderCounts = [
            'M' => $patients->where('sex', 'M')->count(),
            'F' => $patients->where('sex', 'F')->count(),
        ];

        $ageBuckets = [
            '0-5' => ['min' => 0, 'max' => 5, 'male' => 0, 'female' => 0],
            '6-12' => ['min' => 6, 'max' => 12, 'male' => 0, 'female' => 0],
            '13-19' => ['min' => 13, 'max' => 19, 'male' => 0, 'female' => 0],
            '20-59' => ['min' => 20, 'max' => 59, 'male' => 0, 'female' => 0],
            '60+' => ['min' => 60, 'max' => 200, 'male' => 0, 'female' => 0],
        ];

        foreach ($patients as $patient) {
            if (!$patient->birthday) {
                continue;
            }
            $age = Carbon::parse($patient->birthday)->age;
            $sex = $patient->sex === 'M' ? 'male' : 'female';

            foreach ($ageBuckets as $label => &$bucket) {
                if ($age >= $bucket['min'] && $age <= $bucket['max']) {
                    $bucket[$sex]++;
                    break;
                }
            }
        }

        $ageLabels = array_keys($ageBuckets);
        $ageMale = array_map(fn ($b) => $b['male'], $ageBuckets);
        $ageFemale = array_map(fn ($b) => $b['female'], $ageBuckets);

        // Medicine inventory snapshot
        $medicineTotalItems = Medicine::sum('quantity_on_hand');

        $completedServices = $prenatalCount + $fpCount + $nipCount;
        $pendingServices = max($totalPatients - $completedServices, 0);
        $followupsServices = 0;

        $completionData = [
            $completedServices,
            $pendingServices,
            $followupsServices,
        ];

        $topProgramsData = [
            0, // Deworming (not yet tracked)
            $nipCount,
            $prenatalCount,
            $fpCount,
            0, // Nutrition (not yet tracked)
        ];

        return view('reports.index', [
            'selectedMonthLabel' => $selectedDate->format('F Y'),
            'selectedMonthValue' => $selectedDate->month,
            'selectedYearValue' => $selectedDate->year,
            'totalPatients' => $totalPatients,
            'prenatalCount' => $prenatalCount,
            'nipCount' => $nipCount,
            'fpCount' => $fpCount,
            'medicineTotalItems' => $medicineTotalItems,
            'monthLabels' => $monthLabels,
            'servicesSeries' => $servicesSeries,
            'prenatalSeries' => $prenatalSeries,
            'fpSeries' => $fpSeries,
            'nipSeries' => $nipSeries,
            'programDistribution' => $programDistribution,
            'genderCounts' => $genderCounts,
            'ageLabels' => $ageLabels,
            'ageMale' => $ageMale,
            'ageFemale' => $ageFemale,
            'completionData' => $completionData,
            'topProgramsData' => $topProgramsData,
        ]);
    }
}
