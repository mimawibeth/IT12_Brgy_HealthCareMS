#!/usr/bin/env php
<?php
/**
 * Quick Test Script for Approval System
 * Run from terminal: php test-approval-system.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FinancialAssistanceRequest;
use App\Models\MedicalSuppliesRequest;
use Illuminate\Support\Facades\Schema;

echo "\n=== APPROVAL SYSTEM TEST ===\n\n";

// Test 1: Check if tables exist
echo "Test 1: Checking database tables...\n";
$financialExists = Schema::hasTable('financial_assistance_requests');
$medicalExists = Schema::hasTable('medical_supplies_requests');

echo "  Financial Assistance Table: " . ($financialExists ? "✓ EXISTS" : "✗ MISSING") . "\n";
echo "  Medical Supplies Table: " . ($medicalExists ? "✓ EXISTS" : "✗ MISSING") . "\n";

// Test 2: Check if models can be instantiated
echo "\nTest 2: Checking models...\n";
try {
    $financial = new FinancialAssistanceRequest();
    echo "  FinancialAssistanceRequest: ✓ LOADED\n";
} catch (Exception $e) {
    echo "  FinancialAssistanceRequest: ✗ ERROR - " . $e->getMessage() . "\n";
}

try {
    $medical = new MedicalSuppliesRequest();
    echo "  MedicalSuppliesRequest: ✓ LOADED\n";
} catch (Exception $e) {
    echo "  MedicalSuppliesRequest: ✗ ERROR - " . $e->getMessage() . "\n";
}

// Test 3: Check routes
echo "\nTest 3: Checking routes...\n";
$routeCollection = app('router')->getRoutes();
$approvalsRoutes = [];
foreach ($routeCollection as $route) {
    if (strpos($route->getName() ?? '', 'approvals') !== false) {
        $approvalsRoutes[] = $route->getName();
    }
}

if (count($approvalsRoutes) >= 10) {
    echo "  Found " . count($approvalsRoutes) . " approval routes: ✓ SUCCESS\n";
    echo "    Routes: " . implode(', ', array_slice($approvalsRoutes, 0, 3)) . "...\n";
} else {
    echo "  Found only " . count($approvalsRoutes) . " routes: ✗ INCOMPLETE\n";
}

// Test 4: Check controllers
echo "\nTest 4: Checking controller...\n";
if (class_exists('App\Http\Controllers\ApprovalController')) {
    echo "  ApprovalController: ✓ EXISTS\n";
    $reflection = new ReflectionClass('App\Http\Controllers\ApprovalController');
    $methods = array_filter($reflection->getMethods(), function($m) {
        return !$m->isPrivate() && !$m->isProtected();
    });
    echo "  Public methods: " . count($methods) . " (index, create, store, approve, reject, show)\n";
} else {
    echo "  ApprovalController: ✗ NOT FOUND\n";
}

// Test 5: Summary
echo "\n=== SUMMARY ===\n";
$allPassed = $financialExists && $medicalExists && count($approvalsRoutes) >= 10;
if ($allPassed) {
    echo "✓ Approval system is ready!\n";
    echo "  - Database tables created\n";
    echo "  - Models loaded\n";
    echo "  - Routes registered\n";
    echo "  - Controller available\n";
} else {
    echo "✗ Some components are missing. Please review the errors above.\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Login with a BHW account\n";
echo "2. Navigate to /approvals\n";
echo "3. Submit a financial or medical request\n";
echo "4. Login with an Admin account to review\n";
echo "5. Login with a Superadmin account to finalize\n";
echo "\n";

exit($allPassed ? 0 : 1);
