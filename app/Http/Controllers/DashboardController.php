<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\FamilyPlanningRecord;
use App\Models\PrenatalRecord;
use App\Models\NipRecord;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\MedicineDispense;
use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role ?? 'bhw';

        // Route to appropriate dashboard based on role
        switch ($role) {
            case 'super_admin':
                return $this->superAdminDashboard();
            case 'admin':
                return $this->adminDashboard();
            case 'bhw':
            default:
                return $this->bhwDashboard();
        }
    }

    private function superAdminDashboard()
    {
        $now = Carbon::now();
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push($now->copy()->subMonths($i));
        }

        $monthLabels = $months->map(fn(Carbon $d) => $d->format('M'));
        $currentIndex = 5;
        $currentMonth = $months[$currentIndex];
        $previousMonth = $months[$currentIndex - 1] ?? $months[$currentIndex];
        $currentMonthName = $currentMonth->format('F Y');

        // System-wide statistics
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $registeredPatients = Patient::count();
        $healthPrograms = PrenatalRecord::count() + FamilyPlanningRecord::count() + NipRecord::count();

        // User statistics by role
        $userStats = [
            'super_admin' => User::where('role', 'super_admin')->where('status', 'active')->count(),
            'admin' => User::where('role', 'admin')->where('status', 'active')->count(),
            'bhw' => User::where('role', 'bhw')->where('status', 'active')->count(),
        ];



        // Health program statistics
        $prenatalTotal = PrenatalRecord::count();
        $familyPlanningTotal = FamilyPlanningRecord::count();
        $nipTotal = NipRecord::count();

        // Monthly trends
        $prenatalSeries = [];
        $fpSeries = [];
        $nipSeries = [];
        foreach ($months as $m) {
            $prenatalSeries[] = PrenatalRecord::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
            $fpSeries[] = FamilyPlanningRecord::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
            $nipSeries[] = NipRecord::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
        }

        $servicesSeries = [];
        foreach (range(0, 5) as $i) {
            $servicesSeries[$i] = ($prenatalSeries[$i] ?? 0) + ($fpSeries[$i] ?? 0) + ($nipSeries[$i] ?? 0);
        }

        $monthlyServices = $servicesSeries[$currentIndex] ?? 0;
        $programDistribution = [
            'prenatal' => $prenatalSeries[$currentIndex] ?? 0,
            'fp' => $fpSeries[$currentIndex] ?? 0,
            'nip' => $nipSeries[$currentIndex] ?? 0,
        ];

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

        // Medicine dispensing by week (current month)
        $weeksLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
        $weeksData = [0, 0, 0, 0];
        $dispenses = MedicineDispense::whereYear('dispensed_at', $currentMonth->year)
            ->whereMonth('dispensed_at', $currentMonth->month)
            ->get();

        foreach ($dispenses as $dispense) {
            $day = optional($dispense->dispensed_at)->day ?? optional($dispense->created_at)->day ?? 1;
            $index = (int) floor(($day - 1) / 7);
            $index = max(0, min(3, $index));
            $weeksData[$index] += $dispense->quantity;
        }

        // Medicine statistics
        $totalMedicineStock = MedicineBatch::sum('quantity_on_hand');

        // Recent audit logs
        $recentLogs = AuditLog::with('user')->orderByDesc('created_at')->limit(5)->get();



        return view('dashboard.super-admin', compact(
            'registeredPatients',
            'healthPrograms',
            'monthlyServices',
            'prenatalTotal',
            'familyPlanningTotal',
            'nipTotal',
            'currentMonthName',
            'monthLabels',
            'servicesSeries',
            'programDistribution',
            'weeksLabels',
            'weeksData',
            'summary',
            'totalMedicineStock',
            'recentLogs'
        ));
    }

    private function adminDashboard()
    {
        $now = Carbon::now();
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push($now->copy()->subMonths($i));
        }

        $monthLabels = $months->map(fn(Carbon $d) => $d->format('M'));
        $currentIndex = 5;
        $currentMonth = $months[$currentIndex];
        $previousMonth = $months[$currentIndex - 1] ?? $months[$currentIndex];
        $currentMonthName = $currentMonth->format('F Y');

        // System statistics
        $registeredPatients = Patient::count();
        $healthPrograms = PrenatalRecord::count() + FamilyPlanningRecord::count() + NipRecord::count();

        // Health program statistics
        $prenatalTotal = PrenatalRecord::count();
        $familyPlanningTotal = FamilyPlanningRecord::count();
        $nipTotal = NipRecord::count();

        // Monthly trends
        $prenatalSeries = [];
        $fpSeries = [];
        $nipSeries = [];
        foreach ($months as $m) {
            $prenatalSeries[] = PrenatalRecord::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
            $fpSeries[] = FamilyPlanningRecord::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
            $nipSeries[] = NipRecord::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
        }

        $servicesSeries = [];
        foreach (range(0, 5) as $i) {
            $servicesSeries[$i] = ($prenatalSeries[$i] ?? 0) + ($fpSeries[$i] ?? 0) + ($nipSeries[$i] ?? 0);
        }

        $monthlyServices = $servicesSeries[$currentIndex] ?? 0;
        $programDistribution = [
            'prenatal' => $prenatalSeries[$currentIndex] ?? 0,
            'fp' => $fpSeries[$currentIndex] ?? 0,
            'nip' => $nipSeries[$currentIndex] ?? 0,
        ];

        // Monthly summary
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

        // Medicine statistics
        $totalMedicines = Medicine::count();
        // Sum from MedicineBatch directly since quantity_on_hand is stored there
        $totalMedicineStock = MedicineBatch::sum('quantity_on_hand');
        // For low stock, we need to check each medicine's quantity_on_hand (calculated from batches)
        $allMedicines = Medicine::with('batches')->get();
        $lowStockMedicines = $allMedicines->filter(function ($medicine) {
            return $medicine->reorder_level > 0 && $medicine->quantity_on_hand <= $medicine->reorder_level;
        })->count();

        // Medicine dispensing by week
        $weeksLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
        $weeksData = [0, 0, 0, 0];
        $dispenses = MedicineDispense::whereYear('dispensed_at', $currentMonth->year)
            ->whereMonth('dispensed_at', $currentMonth->month)
            ->get();

        foreach ($dispenses as $dispense) {
            $day = optional($dispense->dispensed_at)->day ?? optional($dispense->created_at)->day ?? 1;
            $index = (int) floor(($day - 1) / 7);
            $index = max(0, min(3, $index));
            $weeksData[$index] += $dispense->quantity;
        }



        // Recent activities
        $recentLogs = AuditLog::with('user')->orderByDesc('created_at')->limit(5)->get();

        return view('dashboard.admin', compact(
            'registeredPatients',
            'healthPrograms',
            'monthlyServices',
            'prenatalTotal',
            'familyPlanningTotal',
            'nipTotal',
            'currentMonthName',
            'monthLabels',
            'servicesSeries',
            'programDistribution',
            'weeksLabels',
            'weeksData',
            'summary',
            'totalMedicines',
            'totalMedicineStock',
            'lowStockMedicines',
            'recentLogs'
        ));
    }

    private function bhwDashboard()
    {
        $now = Carbon::now();
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push($now->copy()->subMonths($i));
        }

        $monthLabels = $months->map(fn(Carbon $d) => $d->format('M'));
        $currentIndex = 5;
        $currentMonth = $months[$currentIndex];
        $previousMonth = $months[$currentIndex - 1] ?? $months[$currentIndex];
        $currentMonthName = $currentMonth->format('F Y');

        // System statistics
        $registeredPatients = Patient::count();
        $healthPrograms = PrenatalRecord::count() + FamilyPlanningRecord::count() + NipRecord::count();

        // Health program statistics
        $prenatalTotal = PrenatalRecord::count();
        $familyPlanningTotal = FamilyPlanningRecord::count();
        $nipTotal = NipRecord::count();

        // Monthly trends
        $prenatalSeries = [];
        $fpSeries = [];
        $nipSeries = [];
        foreach ($months as $m) {
            $prenatalSeries[] = PrenatalRecord::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
            $fpSeries[] = FamilyPlanningRecord::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
            $nipSeries[] = NipRecord::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
        }

        $servicesSeries = [];
        foreach (range(0, 5) as $i) {
            $servicesSeries[$i] = ($prenatalSeries[$i] ?? 0) + ($fpSeries[$i] ?? 0) + ($nipSeries[$i] ?? 0);
        }

        $monthlyServices = $servicesSeries[$currentIndex] ?? 0;
        $programDistribution = [
            'prenatal' => $prenatalSeries[$currentIndex] ?? 0,
            'fp' => $fpSeries[$currentIndex] ?? 0,
            'nip' => $nipSeries[$currentIndex] ?? 0,
        ];

        // Monthly summary
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

        // Medicine dispensing by week (current month)
        $weeksLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
        $weeksData = [0, 0, 0, 0];
        $dispenses = MedicineDispense::whereYear('dispensed_at', $currentMonth->year)
            ->whereMonth('dispensed_at', $currentMonth->month)
            ->get();

        foreach ($dispenses as $dispense) {
            $day = optional($dispense->dispensed_at)->day ?? optional($dispense->created_at)->day ?? 1;
            $index = (int) floor(($day - 1) / 7);
            $index = max(0, min(3, $index));
            $weeksData[$index] += $dispense->quantity;
        }

        // Medicine statistics
        $totalMedicineStock = MedicineBatch::sum('quantity_on_hand');

        // Recent audit logs
        $recentLogs = AuditLog::with('user')->orderByDesc('created_at')->limit(5)->get();

        return view('dashboard.bhw', compact(
            'registeredPatients',
            'healthPrograms',
            'monthlyServices',
            'prenatalTotal',
            'familyPlanningTotal',
            'nipTotal',
            'currentMonthName',
            'monthLabels',
            'servicesSeries',
            'programDistribution',
            'weeksLabels',
            'weeksData',
            'summary',
            'totalMedicineStock',
            'recentLogs'
        ));
    }
}
