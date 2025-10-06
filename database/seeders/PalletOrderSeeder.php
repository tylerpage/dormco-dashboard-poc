<?php

namespace Database\Seeders;

use App\Models\PalletOrder;
use App\Models\Pallet;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PalletOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pallets = Pallet::all();
        $orders = Order::all();
        $users = User::all();
        
        if ($users->isEmpty()) {
            return; // Skip if no users exist
        }
        
        $user = $users->first();

        // Create some pallet order verifications
        $palletOrderData = [
            [
                'pallet_id' => $pallets->where('pallet_number', 'PAL-001')->first()->id,
                'order_id' => $orders->where('order_number', 'ORD-2024-003')->first()->id,
                'verified' => true,
                'verified_at' => now()->subHours(2),
                'verified_by' => $user->id,
                'notes' => 'Order verified during packing process',
            ],
            [
                'pallet_id' => $pallets->where('pallet_number', 'PAL-002')->first()->id,
                'order_id' => $orders->where('order_number', 'ORD-2024-004')->first()->id,
                'verified' => true,
                'verified_at' => now()->subDays(1),
                'verified_by' => $user->id,
                'notes' => 'Order verified before shipping',
            ],
            [
                'pallet_id' => $pallets->where('pallet_number', 'PAL-003')->first()->id,
                'order_id' => $orders->where('order_number', 'ORD-2024-005')->first()->id,
                'verified' => true,
                'verified_at' => now()->subDays(3),
                'verified_by' => $user->id,
                'notes' => 'Order verified and delivered successfully',
            ],
            [
                'pallet_id' => $pallets->where('pallet_number', 'PAL-004')->first()->id,
                'order_id' => $orders->where('order_number', 'ORD-2024-008')->first()->id,
                'verified' => true,
                'verified_at' => now()->subHours(4),
                'verified_by' => $user->id,
                'notes' => 'Order verified during packing',
            ],
        ];

        foreach ($palletOrderData as $data) {
            // Only create if both pallet and order exist
            if ($data['pallet_id'] && $data['order_id']) {
                PalletOrder::updateOrCreate(
                    [
                        'pallet_id' => $data['pallet_id'],
                        'order_id' => $data['order_id'],
                    ],
                    $data
                );
            }
        }
    }
}