<?php

namespace Database\Seeders;

use App\Models\MedicalSuppliesRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MedicalSuppliesRequestSeeder extends Seeder
{
    private array $items = [
        'First Aid Kit',
        'Antiseptic Solution (Chlorhexidine)',
        'Sterile Gauze Pads',
        'Elastic Bandages',
        'Surgical Masks (Box of 50)',
        'Medical Gloves (Nitrile)',
        'Syringe and Needles (various sizes)',
        'Disposable Thermometers',
        'Blood Pressure Cuff',
        'Stethoscope',
        'Antiseptic Ointment (Bacitracin)',
        'Adhesive Medical Tape',
        'Cotton Balls and Swabs',
        'Alcohol Prep Pads',
        'Triangular Bandages',
        'Antihistamine Tablets',
        'Antacid Tablets',
        'Paracetamol Tablets',
        'Povidone Iodine Solution',
        'Emergency Light (Torch)',
        'Disinfectant Solution',
        'Latex-Free Gloves',
        'Sterile Saline Solution',
        'Vitamin C Tablets',
        'Iron Supplement Tablets',
    ];

    public function run(): void
    {
        $bhwUser = User::where('role', 'bhw')->first();
        $adminUser = User::where('role', 'admin')->first();
        $superadminUser = User::where('role', 'super_admin')->first();

        if (!$bhwUser) {
            return;
        }

        $statuses = [
            'pending' => 4,
            'approved_by_admin' => 4,
            'approved_by_superadmin' => 3,
            'rejected_by_admin' => 2,
        ];

        $requestNum = 0;
        foreach ($statuses as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $requestNum++;
                $item = fake()->randomElement($this->items);
                $quantity = fake()->numberBetween(5, 200);
                $requestDate = fake()->dateTimeBetween('-45 days', 'now');

                $data = [
                    'user_id' => $bhwUser->id,
                    'item_name' => $item,
                    'quantity' => $quantity,
                    'reason' => fake()->randomElement([
                        'Stock replenishment - Regular supply',
                        'Emergency stock needed for clinic operation',
                        'Support for immunization program',
                        'Prenatal and maternal health program',
                        'Pediatric care supplies',
                        'Emergency preparation for natural disaster',
                        'Health campaign supplies',
                        'Family planning program support',
                    ]),
                    'description' => fake()->paragraph(2),
                    'status' => $status,
                    'submitted_at' => $requestDate->format('Y-m-d H:i:s'),
                    'created_at' => $requestDate->format('Y-m-d H:i:s'),
                    'updated_at' => $requestDate->format('Y-m-d H:i:s'),
                ];

                // Add admin and superadmin approval details based on status
                if ($status === 'approved_by_admin' || $status === 'approved_by_superadmin' || $status === 'rejected_by_admin') {
                    $data['admin_id'] = $adminUser?->id;
                    $adminReviewDate = Carbon::parse($requestDate)->addDays(fake()->numberBetween(1, 3));
                    $data['admin_reviewed_at'] = $adminReviewDate->format('Y-m-d H:i:s');
                    $data['admin_notes'] = fake()->optional(0.7)->randomElement([
                        'Verified. Item is in approved list. Approved for procurement.',
                        'Quantity seems excessive. Recommending reduction to ' . max(10, $quantity / 2) . ' units.',
                        'Request approved. Standard supply item.',
                        'Additional documentation needed for approval.',
                        'Forwarded to superadmin for budget approval.',
                    ]);
                }

                if ($status === 'approved_by_superadmin') {
                    $data['superadmin_id'] = $superadminUser?->id;
                    $superadminReviewDate = isset($adminReviewDate) ? Carbon::parse($adminReviewDate)->addDays(fake()->numberBetween(1, 2)) : Carbon::parse($requestDate)->addDays(fake()->numberBetween(4, 6));
                    $data['superadmin_reviewed_at'] = $superadminReviewDate->format('Y-m-d H:i:s');
                    $data['superadmin_notes'] = fake()->optional(0.8)->randomElement([
                        'Approved for procurement. Budget allocated.',
                        'Approved. Recommend vendor: Medical Supply Co.',
                        'Approved with condition to consolidate with other requests.',
                        'Approved for emergency purchase.',
                    ]);
                }

                MedicalSuppliesRequest::create($data);
            }
        }
    }
}
