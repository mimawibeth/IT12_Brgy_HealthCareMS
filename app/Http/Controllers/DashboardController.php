<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Assessment;
use App\Models\FamilyPlanningRecord;
use App\Models\PrenatalRecord;
use App\Models\NipRecord;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\MedicineDispense;
use App\Models\MedicalSupply;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Event;
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

        // Super Admin - System Oversight Metrics
        $totalSystemUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();
        $newUsersThisMonth = User::whereYear('created_at', $currentMonth->year)
            ->whereMonth('created_at', $currentMonth->month)
            ->count();

        // Patient and Health Program Stats
        $totalPatients = Patient::count();
        $newPatientsThisMonth = Patient::whereYear('dateRegistered', $currentMonth->year)
            ->whereMonth('dateRegistered', $currentMonth->month)
            ->count();
        
        $totalPrenatalRecords = PrenatalRecord::count();
        $totalFPRecords = FamilyPlanningRecord::count();
        $totalNIPRecords = NipRecord::count();
        $totalHealthPrograms = $totalPrenatalRecords + $totalFPRecords + $totalNIPRecords;

        // System-wide activities
        $systemActivitiesThisMonth = AuditLog::whereYear('created_at', $currentMonth->year)
            ->whereMonth('created_at', $currentMonth->month)
            ->count();

        // Critical actions logged
        $criticalActions = AuditLog::whereIn('action', ['delete', 'update'])
            ->whereYear('created_at', $currentMonth->year)
            ->whereMonth('created_at', $currentMonth->month)
            ->count();

        // Reports status (assuming you have a reports table, adjust if needed)
        $reportsGenerated = 0; // Placeholder - adjust based on your reports system
        $reportsPending = 0; // Placeholder - adjust based on your reports system

        // Upcoming events
        $upcomingEvents = 0; // Placeholder - adjust based on your events system

        // Recent audit logs for monitoring
        $recentLogs = AuditLog::with('user')->orderByDesc('created_at')->limit(20)->get();

        // Chart Data for Super Admin - Health Program Focused
        // Patient Registration Trend (last 6 months)
        $userTrendLabels = [];
        $userTrendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $userTrendLabels[] = $month->format('M');
            $userTrendData[] = Patient::whereYear('dateRegistered', $month->year)
                ->whereMonth('dateRegistered', $month->month)
                ->count();
        }

        // Health Programs Distribution (pie chart)
        $activityTypes = [
            'Prenatal Care' => $totalPrenatalRecords,
            'Family Planning' => $totalFPRecords,
            'Immunization (NIP)' => $totalNIPRecords
        ];

        // Weekly Health Records Created (bar chart)
        $weekLabels = [];
        $weekActivityData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $weekLabels[] = $day->format('D');
            $prenatalCount = PrenatalRecord::whereDate('created_at', $day->toDateString())->count();
            $fpCount = FamilyPlanningRecord::whereDate('created_at', $day->toDateString())->count();
            $nipCount = NipRecord::whereDate('created_at', $day->toDateString())->count();
            $weekActivityData[] = $prenatalCount + $fpCount + $nipCount;
        }

        return view('dashboard.super-admin', compact(
            'totalSystemUsers',
            'activeUsers',
            'inactiveUsers',
            'newUsersThisMonth',
            'totalPatients',
            'newPatientsThisMonth',
            'totalPrenatalRecords',
            'totalFPRecords',
            'totalNIPRecords',
            'totalHealthPrograms',
            'systemActivitiesThisMonth',
            'criticalActions',
            'reportsGenerated',
            'reportsPending',
            'upcomingEvents',
            'currentMonthName',
            'recentLogs',
            'userTrendLabels',
            'userTrendData',
            'activityTypes',
            'weekLabels',
            'weekActivityData'
        ));
    }

    private function adminDashboard()
    {
        $now = Carbon::now();
        $currentMonth = $now;
        $currentMonthName = $currentMonth->format('F Y');
        $today = $now->format('Y-m-d');

        // Admin - Daily Operations Metrics
        // ITR Records (Assessments)
        $startOfWeek = $now->copy()->startOfWeek();
        $newITRThisWeek = Assessment::whereBetween('date', [$startOfWeek, $now])->count();
        $totalITRRecords = Assessment::count();
        $activePrenatalWomen = PrenatalRecord::count(); // All prenatal records
        $activeFPUsers = FamilyPlanningRecord::count(); // All FP records
        $childrenUnderNIP = NipRecord::count(); // All NIP records

        // Health Programs This Month
        $prenatalThisMonth = PrenatalRecord::whereYear('created_at', $currentMonth->year)
            ->whereMonth('created_at', $currentMonth->month)->count();
        $fpThisMonth = FamilyPlanningRecord::whereYear('created_at', $currentMonth->year)
            ->whereMonth('created_at', $currentMonth->month)->count();
        $nipThisMonth = NipRecord::whereYear('created_at', $currentMonth->year)
            ->whereMonth('created_at', $currentMonth->month)->count();
        $totalProgramsThisMonth = $prenatalThisMonth + $fpThisMonth + $nipThisMonth;
        $totalProgramsAll = $activePrenatalWomen + $activeFPUsers + $childrenUnderNIP;

        // Missed schedules - Set to 0 since tables don't have next visit date columns
        $missedPrenatalSchedules = 0;
        $missedFPSchedules = 0;
        $missedNIPSchedules = 0;

        // Inventory
        $medicinesInStock = Medicine::whereHas('batches', function($q) {
            $q->where('quantity_on_hand', '>', 0);
        })->count();
        
        $medicalSuppliesInStock = MedicalSupply::where('quantity_on_hand', '>', 0)->count();
        
        $allMedicines = Medicine::with('batches')->get();
        $lowStockMedicines = $allMedicines->filter(function ($medicine) {
            return $medicine->reorder_level > 0 && $medicine->quantity_on_hand > 0 && $medicine->quantity_on_hand <= $medicine->reorder_level;
        })->count();

        $expiringMedicines = MedicineBatch::where('expiry_date', '<=', $now->copy()->addDays(30))
            ->where('expiry_date', '>', $now)
            ->where('quantity_on_hand', '>', 0)
            ->count();

        $suppliesInStock = 0; // Placeholder - adjust based on your supplies system

        // BHW Performance
        $mostActiveBHW = User::where('role', 'bhw')
            ->withCount(['auditLogs' => function($q) use ($currentMonth) {
                $q->whereYear('created_at', $currentMonth->year)
                  ->whereMonth('created_at', $currentMonth->month);
            }])
            ->orderByDesc('audit_logs_count')
            ->first();

        $bhwProductivity = User::where('role', 'bhw')
            ->where('status', 'active')
            ->withCount(['auditLogs' => function($q) use ($currentMonth) {
                $q->whereYear('created_at', $currentMonth->year)
                  ->whereMonth('created_at', $currentMonth->month);
            }])
            ->get();

        // Reports Status
        $prenatalReportStatus = 'pending'; // Adjust based on your reports system
        $fpReportStatus = 'pending';
        $nipReportStatus = 'pending';
        $inventoryReportStatus = 'pending';

        // Audit
        $recentActivities = AuditLog::with('user')->orderByDesc('created_at')->limit(20)->get();

        // Events - Fetch upcoming events this month
        $eventsThisMonth = Event::whereYear('start_date', $currentMonth->year)
            ->whereMonth('start_date', $currentMonth->month)
            ->where('start_date', '>=', $now->toDateString())
            ->orderBy('start_date', 'asc')
            ->get();

        // Chart Data for Admin
        // Patient Registration Trend (last 6 months)
        $patientTrendLabels = [];
        $patientTrendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $patientTrendLabels[] = $month->format('M');
            $patientTrendData[] = Patient::whereYear('dateRegistered', $month->year)
                ->whereMonth('dateRegistered', $month->month)
                ->count();
        }

        // Health Programs Distribution (doughnut chart)
        $programDistribution = [
            'prenatal' => PrenatalRecord::count(),
            'fp' => FamilyPlanningRecord::count(),
            'nip' => NipRecord::count()
        ];

        // Medicine Dispensing This Week (bar chart)
        $medicineWeekLabels = [];
        $medicineWeekData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $medicineWeekLabels[] = $day->format('D');
            $medicineWeekData[] = MedicineDispense::whereDate('dispensed_at', $day->toDateString())
                ->sum('quantity');
        }

        // Monthly Records Created (line chart)
        $recordsLabels = [];
        $prenatalRecords = [];
        $fpRecords = [];
        $nipRecords = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $recordsLabels[] = $month->format('M');
            $prenatalRecords[] = PrenatalRecord::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $fpRecords[] = FamilyPlanningRecord::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $nipRecords[] = NipRecord::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return view('dashboard.admin', compact(
            'newITRThisWeek',
            'totalITRRecords',
            'activePrenatalWomen',
            'activeFPUsers',
            'childrenUnderNIP',
            'prenatalThisMonth',
            'fpThisMonth',
            'nipThisMonth',
            'totalProgramsThisMonth',
            'totalProgramsAll',
            'medicalSuppliesInStock',
            'missedPrenatalSchedules',
            'missedFPSchedules',
            'missedNIPSchedules',
            'medicinesInStock',
            'lowStockMedicines',
            'expiringMedicines',
            'suppliesInStock',
            'mostActiveBHW',
            'bhwProductivity',
            'prenatalReportStatus',
            'fpReportStatus',
            'nipReportStatus',
            'inventoryReportStatus',
            'recentActivities',
            'eventsThisMonth',
            'currentMonthName',
            'patientTrendLabels',
            'patientTrendData',
            'programDistribution',
            'medicineWeekLabels',
            'medicineWeekData',
            'recordsLabels',
            'prenatalRecords',
            'fpRecords',
            'nipRecords'
        ));
    }

    private function bhwDashboard()
    {
        $now = Carbon::now();
        $currentMonth = $now;
        $currentMonthName = $currentMonth->format('F Y');
        $today = $now->format('Y-m-d');
        $bhwId = auth()->id();
        $startOfWeek = $now->copy()->startOfWeek();

        // BHW - Weekly Stats
        $patientsRegisteredWeekly = Patient::whereBetween('dateRegistered', [$startOfWeek, $now])->count();
        $totalPatients = Patient::count();

        $prenatalRecordsWeekly = PrenatalRecord::whereBetween('created_at', [$startOfWeek, $now])->count();
        $totalPrenatalRecords = PrenatalRecord::count();

        $fpRecordsWeekly = FamilyPlanningRecord::whereBetween('created_at', [$startOfWeek, $now])->count();
        $totalFPRecords = FamilyPlanningRecord::count();

        $immunizationRecordsWeekly = NipRecord::whereBetween('created_at', [$startOfWeek, $now])->count();
        $totalImmunizationRecords = NipRecord::count();

        // Events - Fetch upcoming events this month
        $eventsThisMonth = Event::whereYear('start_date', $currentMonth->year)
            ->whereMonth('start_date', $currentMonth->month)
            ->where('start_date', '>=', $now->toDateString())
            ->orderBy('start_date', 'asc')
            ->get();

        return view('dashboard.bhw', compact(
            'patientsRegisteredWeekly',
            'totalPatients',
            'prenatalRecordsWeekly',
            'totalPrenatalRecords',
            'fpRecordsWeekly',
            'totalFPRecords',
            'immunizationRecordsWeekly',
            'totalImmunizationRecords',
            'eventsThisMonth',
            'currentMonthName'
        ));
    }
}
