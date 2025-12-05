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
                    'request_no' => 'FA-' . str_pad((string) $requestNum, 4, '0', STR_PAD_LEFT),
                    'patient_id' => $patientId,
                    'type' => $type,
                    'amount' => $amount,
                    'reason' => fake()->randomElement($this->reasons),
                    'description' => fake()->paragraph(3),
                    'status' => $status,
                    'submitted_by' => $bhwUser->id,
                    'submitted_at' => $requestDate->format('Y-m-d H:i:s'),
                    'created_at' => $requestDate->format('Y-m-d H:i:s'),
                    'updated_at' => $requestDate->format('Y-m-d H:i:s'),
                ];

                // Add admin and superadmin approval details based on status
                if ($status === 'approved_by_admin' || $status === 'approved_by_superadmin' || $status === 'rejected_by_admin') {
                    $data['forwarded_by'] = $adminUser?->id;
                    $data['forwarded_at'] = $requestDate->copy()->addDays(fake()->numberBetween(1, 5))->format('Y-m-d H:i:s');
                    $data['admin_remarks'] = fake()->optional(0.7)->randomElement([
                        'Document verified. Amount is reasonable for stated need.',
                        'Approved. Patient has genuine medical emergency.',
                        'Incomplete documentation. Requesting more details.',
                        'Request denied due to insufficient fund allocation.',
                        'Approved for processing to superadmin.',
                    ]);
                }

                if ($status === 'approved_by_superadmin' || $status === 'rejected_by_admin') {
                    if ($status === 'approved_by_superadmin') {
                        $data['approved_by'] = $superadminUser?->id;
                        $data['approved_at'] = $data['forwarded_at'] ? Carbon::parse($data['forwarded_at'])->addDays(fake()->numberBetween(1, 3))->format('Y-m-d H:i:s') : null;
                        $data['superadmin_remarks'] = fake()->optional(0.8)->randomElement([
                            'Approved for financial assistance.',
                            'Approved. Recommend emergency fund allocation.',
                            'Approved with condition for monthly follow-up.',
                        ]);
                    } else {
                        $data['rejected_by'] = $adminUser?->id;
                        $data['rejected_at'] = $data['forwarded_at'] ?? $requestDate->copy()->addDays(fake()->numberBetween(1, 5))->format('Y-m-d H:i:s');
                        $data['rejection_reason'] = fake()->randomElement([
                            'Unable to verify patient identity',
                            'Insufficient documentation',
                            'Fund allocation exhausted for this period',
                            'Request does not meet assistance criteria',
                        ]);
                    }
                }

                FinancialAssistanceRequest::create($data);
            }
        }
    }
}
