<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\FamilyPlanningRecord;
use App\Models\PrenatalRecord;
use App\Models\NipRecord;
use App\Models\Medicine;
use App\Models\MedicineDispense;
use App\Models\AuditLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role ?? 'bhw';

        $now = Carbon::now();
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push($now->copy()->subMonths($i));
        }

        $monthLabels = $months->map(fn (Carbon $d) => $d->format('M'));

        $prenatalSeries = [];
        $fpSeries = [];
        $nipSeries = [];

        foreach ($months as $m) {
            $year = $m->year;
            $month = $m->month;

            $prenatalSeries[] = PrenatalRecord::whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
            $fpSeries[] = FamilyPlanningRecord::whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
            $nipSeries[] = NipRecord::whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
        }

        $servicesSeries = [];
        foreach (range(0, 5) as $i) {
            $servicesSeries[$i] = ($prenatalSeries[$i] ?? 0) + ($fpSeries[$i] ?? 0) + ($nipSeries[$i] ?? 0);
        }

        $currentIndex = 5;
        $currentMonth = $months[$currentIndex];
        $previousMonth = $months[$currentIndex - 1] ?? $months[$currentIndex];

        $registeredPatients = Patient::count();
        $healthPrograms = PrenatalRecord::count() + FamilyPlanningRecord::count() + NipRecord::count();
        $monthlyServices = $servicesSeries[$currentIndex] ?? 0;
        $medicineStock = Medicine::sum('quantity_on_hand');

        $prenatalTotal = PrenatalRecord::count();
        $familyPlanningTotal = FamilyPlanningRecord::count();
        $nipTotal = NipRecord::count();

        $currentMonthName = $currentMonth->format('F Y');

        // Monthly summary table values
        $summary = [
            'prenatal' => [
                'current' => $prenatalSeries[$currentIndex] ?? 0,
                'previous' => $prenatalSeries[$currentIndex - 1] ?? 0,
            ],
            'fp' => [
                'current' => $fpSeries[$currentIndex] ?? 0,
                'previous' => $fpSeries[$currentIndex - 1] ?? 0,
            ],
            'nip' => [
                'current' => $nipSeries[$currentIndex] ?? 0,
                'previous' => $nipSeries[$currentIndex - 1] ?? 0,
            ],
            'medicine' => [
                'current' => MedicineDispense::whereYear('dispensed_at', $currentMonth->year)
                    ->whereMonth('dispensed_at', $currentMonth->month)
                    ->sum('quantity'),
                'previous' => MedicineDispense::whereYear('dispensed_at', $previousMonth->year)
                    ->whereMonth('dispensed_at', $previousMonth->month)
                    ->sum('quantity'),
            ],
            'patients' => [
                'current' => Patient::whereYear('dateRegistered', $currentMonth->year)
                    ->whereMonth('dateRegistered', $currentMonth->month)
                    ->count(),
                'previous' => Patient::whereYear('dateRegistered', $previousMonth->year)
                    ->whereMonth('dateRegistered', $previousMonth->month)
                    ->count(),
            ],
        ];

        foreach ($summary as &$row) {
            $prev = max($row['previous'], 1);
            $row['variance'] = round((($row['current'] - $row['previous']) / $prev) * 100, 1);
        }

        // Program distribution (current month)
        $programDistribution = [
            'prenatal' => $prenatalSeries[$currentIndex] ?? 0,
            'fp' => $fpSeries[$currentIndex] ?? 0,
            'nip' => $nipSeries[$currentIndex] ?? 0,
        ];

        // Medicine dispensing by week (current month)
        $weeksLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
        $weeksData = [0, 0, 0, 0];

        $dispenses = MedicineDispense::whereYear('dispensed_at', $currentMonth->year)
            ->whereMonth('dispensed_at', $currentMonth->month)
            ->get();

        foreach ($dispenses as $dispense) {
            $day = optional($dispense->dispensed_at)->day ?? optional($dispense->created_at)->day ?? 1;
            $index = (int)floor(($day - 1) / 7);
            $index = max(0, min(3, $index));
            $weeksData[$index] += $dispense->quantity;
        }

        $recentLogs = AuditLog::with('user')->orderByDesc('created_at')->limit(5)->get();

        return view('dashboard.index', [
            'role' => $role,
            'registeredPatients' => $registeredPatients,
            'healthPrograms' => $healthPrograms,
            'monthlyServices' => $monthlyServices,
            'medicineStock' => $medicineStock,
            'prenatalTotal' => $prenatalTotal,
            'familyPlanningTotal' => $familyPlanningTotal,
            'nipTotal' => $nipTotal,
            'currentMonthName' => $currentMonthName,
            'monthLabels' => $monthLabels,
            'servicesSeries' => $servicesSeries,
            'programDistribution' => $programDistribution,
            'weeksLabels' => $weeksLabels,
            'weeksData' => $weeksData,
            'summary' => $summary,
            'recentLogs' => $recentLogs,
        ]);
    }
}
