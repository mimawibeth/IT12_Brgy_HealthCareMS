<?php

namespace Database\Seeders;

use App\Models\FinancialAssistanceRequest;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FinancialAssistanceRequestSeeder extends Seeder
{
    private array $types = [
        'Emergency',
        'Medical',
        'Educational',
        'Livelihood',
        'Housing',
    ];

    private array $reasons = [
        'Emergency hospital bill for accident victim',
        'Medication for chronic disease management',
        'Dialysis treatment for kidney patient',
        'Cancer treatment and chemotherapy',
        'Child\'s educational expenses',
        'Emergency repair of family home',
        'Livelihood capital for small business',
        'Major surgery recovery expenses',
        'Post-natal care and complications',
        'Medical emergency hospitalization',
        'Rehabilitation therapy sessions',
        'Insulin and diabetes management supplies',
    ];

    public function run(): void
    {
        $bhwUser = User::where('role', 'bhw')->first();
        $adminUser = User::where('role', 'admin')->first();
        $superadminUser = User::where('role', 'super_admin')->first();
        $patientIds = Patient::pluck('PatientID')->all();

        if (!$bhwUser || empty($patientIds)) {
            return;
        }

        $statuses = [
            'pending' => 5,
            'approved_by_admin' => 5,
            'approved_by_superadmin' => 3,
            'rejected_by_admin' => 2,
        ];

        $requestNum = 0;
        foreach ($statuses as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $requestNum++;
                $patientId = fake()->randomElement($patientIds);
                $amount = fake()->randomElement([2000, 3500, 5000, 7500, 10000, 12000, 15000, 18000, 20000]);
                $requestDate = fake()->dateTimeBetween('-60 days', 'now');
                $type = fake()->randomElement($this->types);

                $data = [
                    'user_id' => $bhwUser->id,
                    'type' => $type,
                    'amount' => $amount,
                    'reason' => fake()->randomElement($this->reasons),
                    'description' => fake()->paragraph(3),
                    'status' => $status,
                    'submitted_at' => $requestDate->format('Y-m-d H:i:s'),
                    'created_at' => $requestDate->format('Y-m-d H:i:s'),
                    'updated_at' => $requestDate->format('Y-m-d H:i:s'),
                ];

                // Add admin and superadmin approval details based on status
                if ($status === 'approved_by_admin' || $status === 'approved_by_superadmin' || $status === 'rejected_by_admin') {
                    $data['admin_id'] = $adminUser?->id;
                    $adminReviewDate = Carbon::parse($requestDate)->addDays(fake()->numberBetween(1, 5));
                    $data['admin_reviewed_at'] = $adminReviewDate->format('Y-m-d H:i:s');
                    $data['admin_notes'] = fake()->optional(0.7)->randomElement([
                        'Document verified. Amount is reasonable for stated need.',
                        'Approved. Patient has genuine medical emergency.',
                        'Incomplete documentation. Requesting more details.',
                        'Request denied due to insufficient fund allocation.',
                        'Approved for processing to superadmin.',
                    ]);
                }

                if ($status === 'approved_by_superadmin') {
                    $data['superadmin_id'] = $superadminUser?->id;
                    $superadminReviewDate = isset($adminReviewDate) ? Carbon::parse($adminReviewDate)->addDays(fake()->numberBetween(1, 3)) : Carbon::parse($requestDate)->addDays(fake()->numberBetween(6, 10));
                    $data['superadmin_reviewed_at'] = $superadminReviewDate->format('Y-m-d H:i:s');
                    $data['superadmin_notes'] = fake()->optional(0.8)->randomElement([
                        'Approved for financial assistance.',
                        'Approved. Recommend emergency fund allocation.',
                        'Approved with condition for monthly follow-up.',
                    ]);
                }

                FinancialAssistanceRequest::create($data);
            }
        }
    }
}
