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

        // Generate 50 pallets
        $pallets = [];
        $statuses = ['packing', 'shipped', 'delivered'];
        $locations = ['Warehouse A', 'Warehouse B', 'Warehouse C', 'Distribution Center', 'Loading Dock', 'Storage Facility'];
        $cities = ['Berkeley', 'Stanford', 'Los Angeles', 'Pasadena', 'San Diego', 'Davis', 'Irvine', 'San Francisco', 'Oakland', 'Fresno'];
        $states = ['CA', 'NY', 'TX', 'FL', 'IL', 'PA', 'OH', 'GA', 'NC', 'MI'];
        
        for ($i = 1; $i <= 50; $i++) {
            $palletNumber = 'PAL-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $status = $statuses[array_rand($statuses)];
            $location = $locations[array_rand($locations)];
            $city = $cities[array_rand($cities)];
            $state = $states[array_rand($states)];
            $zip = rand(10000, 99999);
            
            $pallet = [
                'pallet_number' => $palletNumber,
                'status' => $status,
                'location' => $location,
                'lot' => 'LOT-2024-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'school_id' => $schools->random()->id,
                'shipping_address_1' => rand(100, 9999) . ' ' . $this->getRandomStreetName(),
                'shipping_city' => $city,
                'shipping_state' => $state,
                'shipping_zip' => $zip,
                'notes' => rand(1, 3) === 1 ? $this->getRandomPalletNote() : null,
            ];
            
            $pallets[] = $pallet;
        }

        foreach ($pallets as $palletData) {
            $pallet = Pallet::create(array_merge($palletData, [
                'created_by' => $user->id,
            ]));
        }
    }

    private function getRandomStreetName()
    {
        $streets = ['Main St', 'Oak Ave', 'Pine St', 'Elm Dr', 'Maple Ln', 'Cedar Rd', 'Birch St', 'Spruce Ave', 'First St', 'Second Ave', 'Park Rd', 'University Blvd', 'College St', 'Campus Dr'];
        return $streets[array_rand($streets)];
    }

    private function getRandomPalletNote()
    {
        $notes = [
            'Priority pallet for rush orders',
            'Contains fragile items',
            'Successfully delivered to campus',
            'Standard processing',
            'In transit to destination',
            'New pallet being prepared',
            'Special handling required',
            'Heavy items - use forklift',
            'Temperature controlled storage',
            'Customer requested expedited delivery'
        ];
        return $notes[array_rand($notes)];
    }
}
