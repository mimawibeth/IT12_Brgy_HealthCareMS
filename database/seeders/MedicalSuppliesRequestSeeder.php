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
                    'request_no' => 'MS-' . str_pad((string) $requestNum, 4, '0', STR_PAD_LEFT),
                    'item_name' => $item,
                    'quantity' => $quantity,
                    'unit' => $this->getUnit($item),
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
                    'submitted_by' => $bhwUser->id,
                    'submitted_at' => $requestDate->format('Y-m-d H:i:s'),
                    'created_at' => $requestDate->format('Y-m-d H:i:s'),
                    'updated_at' => $requestDate->format('Y-m-d H:i:s'),
                ];

                // Add admin and superadmin approval details based on status
                if ($status === 'approved_by_admin' || $status === 'approved_by_superadmin' || $status === 'rejected_by_admin') {
                    $data['forwarded_by'] = $adminUser?->id;
                    $data['forwarded_at'] = $requestDate->copy()->addDays(fake()->numberBetween(1, 3))->format('Y-m-d H:i:s');
                    $data['admin_remarks'] = fake()->optional(0.7)->randomElement([
                        'Verified. Item is in approved list. Approved for procurement.',
                        'Quantity seems excessive. Recommending reduction to ' . max(10, $quantity / 2) . ' units.',
                        'Request approved. Standard supply item.',
                        'Additional documentation needed for approval.',
                        'Forwarded to superadmin for budget approval.',
                    ]);
                }

                if ($status === 'approved_by_superadmin' || $status === 'rejected_by_admin') {
                    if ($status === 'approved_by_superadmin') {
                        $data['approved_by'] = $superadminUser?->id;
                        $data['approved_at'] = $data['forwarded_at'] ? Carbon::parse($data['forwarded_at'])->addDays(fake()->numberBetween(1, 2))->format('Y-m-d H:i:s') : null;
                        $data['superadmin_remarks'] = fake()->optional(0.8)->randomElement([
                            'Approved for procurement. Budget allocated.',
                            'Approved. Recommend vendor: Medical Supply Co.',
                            'Approved with condition to consolidate with other requests.',
                            'Approved for emergency purchase.',
                        ]);
                    } else {
                        $data['rejected_by'] = $adminUser?->id;
                        $data['rejected_at'] = $data['forwarded_at'] ?? $requestDate->copy()->addDays(fake()->numberBetween(1, 3))->format('Y-m-d H:i:s');
                        $data['rejection_reason'] = fake()->randomElement([
                            'Item not in approved supplies list',
                            'Quantity exceeds approved threshold',
                            'Budget allocation insufficient',
                            'Similar item already in stock',
                            'Item specification does not meet requirements',
                        ]);
                    }
                }

                MedicalSuppliesRequest::create($data);
            }
        }
    }

    private function getUnit(string $item): string
    {
        return match (true) {
            str_contains($item, 'Box') => 'Box',
            str_contains($item, 'Kit') => 'Kit',
            str_contains($item, 'Solution') => 'Bottle',
            str_contains($item, 'Tablets') => 'Bottle',
            str_contains($item, 'Cuff') => 'Unit',
            str_contains($item, 'Stethoscope') => 'Unit',
            str_contains($item, 'Light') => 'Unit',
            default => 'Pack',
        };
    }
}
