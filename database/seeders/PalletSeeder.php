<?php

namespace Database\Seeders;

use App\Models\Pallet;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = School::all();
        $users = User::all();
        
        if ($users->isEmpty()) {
            // Create a default user if none exist
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@dormco.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        } else {
            $user = $users->first();
        }

        $pallets = [
            [
                'pallet_number' => 'PAL-001',
                'status' => 'packing',
                'location' => 'Warehouse A',
                'lot' => 'LOT-2024-001',
                'school_id' => $schools->random()->id,
                'shipping_address_1' => '123 Main Street',
                'shipping_city' => 'Berkeley',
                'shipping_state' => 'CA',
                'shipping_zip' => '94720',
                'notes' => 'Priority pallet for rush orders',
            ],
            [
                'pallet_number' => 'PAL-002',
                'status' => 'shipped',
                'location' => 'Warehouse B',
                'lot' => 'LOT-2024-002',
                'school_id' => $schools->random()->id,
                'shipping_address_1' => '456 Oak Avenue',
                'shipping_city' => 'Stanford',
                'shipping_state' => 'CA',
                'shipping_zip' => '94305',
                'notes' => 'Contains fragile items',
            ],
            [
                'pallet_number' => 'PAL-003',
                'status' => 'delivered',
                'location' => 'Warehouse C',
                'lot' => 'LOT-2024-003',
                'school_id' => $schools->random()->id,
                'shipping_address_1' => '789 Pine Street',
                'shipping_city' => 'Los Angeles',
                'shipping_state' => 'CA',
                'shipping_zip' => '90095',
                'notes' => 'Successfully delivered to campus',
            ],
            [
                'pallet_number' => 'PAL-004',
                'status' => 'packing',
                'location' => 'Warehouse A',
                'lot' => 'LOT-2024-004',
                'school_id' => $schools->random()->id,
                'shipping_address_1' => '321 Elm Drive',
                'shipping_city' => 'Pasadena',
                'shipping_state' => 'CA',
                'shipping_zip' => '91125',
                'notes' => 'Standard processing',
            ],
            [
                'pallet_number' => 'PAL-005',
                'status' => 'shipped',
                'location' => 'Warehouse B',
                'lot' => 'LOT-2024-005',
                'school_id' => $schools->random()->id,
                'shipping_address_1' => '654 Maple Lane',
                'shipping_city' => 'San Diego',
                'shipping_state' => 'CA',
                'shipping_zip' => '92093',
                'notes' => 'In transit to destination',
            ],
            [
                'pallet_number' => 'PAL-006',
                'status' => 'packing',
                'location' => 'Warehouse C',
                'lot' => 'LOT-2024-006',
                'school_id' => $schools->random()->id,
                'shipping_address_1' => '987 Cedar Road',
                'shipping_city' => 'Davis',
                'shipping_state' => 'CA',
                'shipping_zip' => '95616',
                'notes' => 'New pallet being prepared',
            ],
        ];

        foreach ($pallets as $palletData) {
            $pallet = Pallet::create(array_merge($palletData, [
                'created_by' => $user->id,
            ]));
        }
    }
}
