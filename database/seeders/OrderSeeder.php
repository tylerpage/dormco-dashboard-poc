<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
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

        $orders = [
            [
                'order_number' => 'ORD-2024-001',
                'customer_name' => 'John Smith',
                'customer_email' => 'john.smith@email.com',
                'school_id' => $schools->random()->id,
                'status' => 'pending',
                'shipping_address' => "123 Main Street\nBerkeley, CA 94720",
                'notes' => 'Rush order - needs to be processed quickly',
            ],
            [
                'order_number' => 'ORD-2024-002',
                'customer_name' => 'Sarah Johnson',
                'customer_email' => 'sarah.johnson@email.com',
                'school_id' => $schools->random()->id,
                'status' => 'picked',
                'shipping_address' => "456 Oak Avenue\nStanford, CA 94305",
                'tracking_number' => 'TRK123456789',
            ],
            [
                'order_number' => 'ORD-2024-003',
                'customer_name' => 'Michael Brown',
                'customer_email' => 'michael.brown@email.com',
                'school_id' => $schools->random()->id,
                'status' => 'packed',
                'shipping_address' => "789 Pine Street\nLos Angeles, CA 90095",
                'pallet_number' => 'PAL-001',
            ],
            [
                'order_number' => 'ORD-2024-004',
                'customer_name' => 'Emily Davis',
                'customer_email' => 'emily.davis@email.com',
                'school_id' => $schools->random()->id,
                'status' => 'shipped',
                'shipping_address' => "321 Elm Drive\nPasadena, CA 91125",
                'tracking_number' => 'TRK987654321',
                'pallet_number' => 'PAL-002',
            ],
            [
                'order_number' => 'ORD-2024-005',
                'customer_name' => 'David Wilson',
                'customer_email' => 'david.wilson@email.com',
                'school_id' => null, // No school assigned
                'status' => 'delivered',
                'shipping_address' => "654 Maple Lane\nSan Diego, CA 92093",
                'tracking_number' => 'TRK456789123',
                'pallet_number' => 'PAL-003',
            ],
            [
                'order_number' => 'ORD-2024-006',
                'customer_name' => 'Lisa Anderson',
                'customer_email' => 'lisa.anderson@email.com',
                'school_id' => $schools->random()->id,
                'status' => 'pending',
                'shipping_address' => "987 Cedar Road\nDavis, CA 95616",
                'notes' => 'Fragile items - handle with care',
            ],
            [
                'order_number' => 'ORD-2024-007',
                'customer_name' => 'Robert Taylor',
                'customer_email' => 'robert.taylor@email.com',
                'school_id' => $schools->random()->id,
                'status' => 'picked',
                'shipping_address' => "147 Birch Street\nIrvine, CA 92697",
            ],
            [
                'order_number' => 'ORD-2024-008',
                'customer_name' => 'Jennifer Martinez',
                'customer_email' => 'jennifer.martinez@email.com',
                'school_id' => $schools->random()->id,
                'status' => 'packed',
                'shipping_address' => "258 Spruce Avenue\nBerkeley, CA 94720",
                'pallet_number' => 'PAL-004',
            ],
        ];

        foreach ($orders as $orderData) {
            $order = Order::create($orderData);
            
            // Add some order items
            $itemCount = rand(1, 5);
            for ($i = 1; $i <= $itemCount; $i++) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_name' => "Item {$i} - " . fake()->words(2, true),
                    'description' => fake()->sentence(),
                    'quantity' => rand(1, 3),
                    'price' => fake()->randomFloat(2, 10, 100),
                ]);
            }
            
            // Update item count
            $order->update(['item_count' => $itemCount]);
            
            // Add some order actions
            $order->actions()->create([
                'action_type' => 'order_created',
                'description' => 'Order created',
                'performed_by' => $user->id,
            ]);
            
            if ($order->status !== 'pending') {
                $order->actions()->create([
                    'action_type' => 'status_updated',
                    'description' => "Status updated to {$order->status}",
                    'performed_by' => $user->id,
                ]);
            }
        }
    }
}
