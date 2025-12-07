<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\FamilyPlanningRecord;
use App\Models\PrenatalRecord;
use App\Models\NipRecord;
use App\Models\Medicine;
use App\Models\MedicineDispense;
use App\Models\MedicalSupply;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function monthly(Request $request)
    {
        if (($request->user()->role ?? null) === 'bhw') {
            abort(403);
        }

        $now = Carbon::now();
        $month = (int) ($request->input('month', $now->month));
        $year = (int) ($request->input('year', $now->year));

        $selectedDate = Carbon::create($year, $month, 1);

        // Summary stats for selected month
        $totalUsers = User::count();
        $totalPatients = Patient::count();
        $newPatientsThisMonth = Patient::whereYear('dateRegistered', $year)
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

        // Total health programs and supplies
        $totalPrenatalRecords = PrenatalRecord::count();
        $totalFPRecords = FamilyPlanningRecord::count();
        $totalNIPRecords = NipRecord::count();
        $totalHealthPrograms = $totalPrenatalRecords + $totalFPRecords + $totalNIPRecords;
        
        $totalMedicines = Medicine::count();
        
        // Calculate low stock count: medicines where total quantity_on_hand <= reorder_level
        $lowStockCount = Medicine::whereHas('batches', function($query) {
            // Has batches
        })->get()->filter(function($medicine) {
            return $medicine->quantity_on_hand <= $medicine->reorder_level;
        })->count();

        // Last 6 months per program for charts
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push($selectedDate->copy()->subMonths($i));
        }

        $monthLabels = $months->map(fn(Carbon $d) => $d->format('M'));

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
        $ageMale = array_map(fn($b) => $b['male'], $ageBuckets);
        $ageFemale = array_map(fn($b) => $b['female'], $ageBuckets);

        // Medicine inventory snapshot

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

        // Medicine dispensing data for selected month
        $medicineDispenses = MedicineDispense::whereYear('dispensed_at', $year)
            ->whereMonth('dispensed_at', $month)
            ->with('medicine')
            ->get();
        
        $totalDispensesThisMonth = $medicineDispenses->sum('quantity');
        $uniqueMedicinesDispensed = $medicineDispenses->pluck('medicine_id')->unique()->count();
        
        // Medicine dispensing trend (last 6 months)
        $medicineDispenseSeries = [];
        foreach ($months as $m) {
            $medicineDispenseSeries[] = MedicineDispense::whereYear('dispensed_at', $m->year)
                ->whereMonth('dispensed_at', $m->month)
                ->sum('quantity');
        }

        // Medical supplies data
        $totalMedicalSupplies = MedicalSupply::count();
        $totalSuppliesQuantity = MedicalSupply::sum('quantity_on_hand');
        $lowStockSupplies = MedicalSupply::where('quantity_on_hand', '<=', 10)->count();
        
        // Get top 5 medicines by dispensing frequency
        $topMedicines = MedicineDispense::whereYear('dispensed_at', $year)
            ->whereMonth('dispensed_at', $month)
            ->selectRaw('medicine_id, SUM(quantity) as total_quantity')
            ->groupBy('medicine_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->with('medicine')
            ->get();
        
        $topMedicineNames = $topMedicines->map(fn($d) => $d->medicine->name ?? 'Unknown')->toArray();
        $topMedicineQuantities = $topMedicines->pluck('total_quantity')->toArray();

        return view('reports.index', [
            'selectedMonthLabel' => $selectedDate->format('F Y'),
            'selectedMonthValue' => $selectedDate->month,
            'selectedYearValue' => $selectedDate->year,
            'totalUsers' => $totalUsers,
            'totalPatients' => $totalPatients,
            'newPatientsThisMonth' => $newPatientsThisMonth,
            'totalHealthPrograms' => $totalHealthPrograms,
            'totalPrenatalRecords' => $totalPrenatalRecords,
            'totalFPRecords' => $totalFPRecords,
            'totalNIPRecords' => $totalNIPRecords,
            'totalMedicines' => $totalMedicines,
            'lowStockCount' => $lowStockCount,
            'prenatalCount' => $prenatalCount,
            'nipCount' => $nipCount,
            'fpCount' => $fpCount,
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
            'totalDispensesThisMonth' => $totalDispensesThisMonth,
            'uniqueMedicinesDispensed' => $uniqueMedicinesDispensed,
            'medicineDispenseSeries' => $medicineDispenseSeries,
            'totalMedicalSupplies' => $totalMedicalSupplies,
            'totalSuppliesQuantity' => $totalSuppliesQuantity,
            'lowStockSupplies' => $lowStockSupplies,
            'topMedicineNames' => $topMedicineNames,
            'topMedicineQuantities' => $topMedicineQuantities,
        ]);
    }

    public function exportPdf(Request $request)
    {
        if (($request->user()->role ?? null) === 'bhw') {
            abort(403);
        }

        // Get the same data as monthly report
        $data = $this->getReportData($request);

        // Generate PDF
        $pdf = Pdf::loadView('reports.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 15)
            ->setOption('margin-left', 15)
            ->setOption('margin-right', 15);

        $filename = 'Monthly_Health_Report_' . ($data['selectedMonthLabel'] ?? 'Report') . '.pdf';
        $filename = str_replace(' ', '_', $filename);

        return $pdf->download($filename);
    }

    public function print(Request $request)
    {
        if (($request->user()->role ?? null) === 'bhw') {
            abort(403);
        }

        // Get the same data as monthly report
        $data = $this->getReportData($request);

        // Return the PDF view for printing
        return view('reports.pdf', $data);
    }

    private function getReportData(Request $request)
    {
        $now = Carbon::now();
        $month = (int) ($request->input('month', $now->month));
        $year = (int) ($request->input('year', $now->year));

        $selectedDate = Carbon::create($year, $month, 1);

        // Summary stats for selected month
        $totalUsers = User::count();
        $totalPatients = Patient::count();
        $newPatientsThisMonth = Patient::whereYear('dateRegistered', $year)
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

        // Total health programs and supplies
        $totalPrenatalRecords = PrenatalRecord::count();
        $totalFPRecords = FamilyPlanningRecord::count();
        $totalNIPRecords = NipRecord::count();
        $totalHealthPrograms = $totalPrenatalRecords + $totalFPRecords + $totalNIPRecords;
        
        $totalMedicines = Medicine::count();
        
        // Calculate low stock count
        $lowStockCount = Medicine::whereHas('batches', function($query) {
            // Has batches
        })->get()->filter(function($medicine) {
            return $medicine->quantity_on_hand <= $medicine->reorder_level;
        })->count();

        // Last 6 months per program for charts
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push($selectedDate->copy()->subMonths($i));
        }

        $monthLabels = $months->map(fn(Carbon $d) => $d->format('M'));

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

        // Gender distribution and age distribution
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
        $ageMale = array_map(fn($b) => $b['male'], $ageBuckets);
        $ageFemale = array_map(fn($b) => $b['female'], $ageBuckets);

        $completedServices = $prenatalCount + $fpCount + $nipCount;
        $pendingServices = max($totalPatients - $completedServices, 0);
        $followupsServices = 0;

        $completionData = [
            $completedServices,
            $pendingServices,
            $followupsServices,
        ];

        $topProgramsData = [
            0,
            $nipCount,
            $prenatalCount,
            $fpCount,
            0,
        ];

        // Medicine dispensing data
        $medicineDispenses = MedicineDispense::whereYear('dispensed_at', $year)
            ->whereMonth('dispensed_at', $month)
            ->with('medicine')
            ->get();
        
        $totalDispensesThisMonth = $medicineDispenses->sum('quantity');
        $uniqueMedicinesDispensed = $medicineDispenses->pluck('medicine_id')->unique()->count();
        
        // Medicine dispensing trend
        $medicineDispenseSeries = [];
        foreach ($months as $m) {
            $medicineDispenseSeries[] = MedicineDispense::whereYear('dispensed_at', $m->year)
                ->whereMonth('dispensed_at', $m->month)
                ->sum('quantity');
        }

        // Medical supplies data
        $totalMedicalSupplies = MedicalSupply::count();
        $totalSuppliesQuantity = MedicalSupply::sum('quantity_on_hand');
        $lowStockSupplies = MedicalSupply::where('quantity_on_hand', '<=', 10)->count();
        
        // Top 5 medicines
        $topMedicines = MedicineDispense::whereYear('dispensed_at', $year)
            ->whereMonth('dispensed_at', $month)
            ->selectRaw('medicine_id, SUM(quantity) as total_quantity')
            ->groupBy('medicine_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->with('medicine')
            ->get();
        
        $topMedicineNames = $topMedicines->map(fn($d) => $d->medicine->name ?? 'Unknown')->toArray();
        $topMedicineQuantities = $topMedicines->pluck('total_quantity')->toArray();

        return [
            'selectedMonthLabel' => $selectedDate->format('F Y'),
            'selectedMonthValue' => $selectedDate->month,
            'selectedYearValue' => $selectedDate->year,
            'totalUsers' => $totalUsers,
            'totalPatients' => $totalPatients,
            'newPatientsThisMonth' => $newPatientsThisMonth,
            'totalHealthPrograms' => $totalHealthPrograms,
            'totalPrenatalRecords' => $totalPrenatalRecords,
            'totalFPRecords' => $totalFPRecords,
            'totalNIPRecords' => $totalNIPRecords,
            'totalMedicines' => $totalMedicines,
            'lowStockCount' => $lowStockCount,
            'prenatalCount' => $prenatalCount,
            'nipCount' => $nipCount,
            'fpCount' => $fpCount,
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
            'totalDispensesThisMonth' => $totalDispensesThisMonth,
            'uniqueMedicinesDispensed' => $uniqueMedicinesDispensed,
            'medicineDispenseSeries' => $medicineDispenseSeries,
            'totalMedicalSupplies' => $totalMedicalSupplies,
            'totalSuppliesQuantity' => $totalSuppliesQuantity,
            'lowStockSupplies' => $lowStockSupplies,
            'topMedicineNames' => $topMedicineNames,
            'topMedicineQuantities' => $topMedicineQuantities,
        ];
    }
}
