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
                'shipping_address_1' => '123 Main Street',
                'shipping_city' => 'Berkeley',
                'shipping_state' => 'CA',
                'shipping_zip' => '94720',
                'notes' => 'Rush order - needs to be processed quickly',
                'verified' => false,
            ],
            [
                'order_number' => 'ORD-2024-002',
                'customer_name' => 'Sarah Johnson',
                'customer_email' => 'sarah.johnson@email.com',
                'school_id' => $schools->random()->id,
                'status' => 'picked',
                'shipping_address_1' => '456 Oak Avenue',
                'shipping_city' => 'Stanford',
                'shipping_state' => 'CA',
                'shipping_zip' => '94305',
                'tracking_number' => 'TRK123456789',
                'verified' => true,
                'verified_at' => now(),
                'verified_by' => $user->id,
            ],
            [
                'order_number' => 'ORD-2024-003',
                'customer_name' => 'Michael Brown',
                'customer_email' => 'michael.brown@email.com',
                'school_id' => $schools->random()->id,
                'status' => 'packed',
                'shipping_address_1' => '789 Pine Street',
                'shipping_city' => 'Los Angeles',
                'shipping_state' => 'CA',
                'shipping_zip' => '90095',
                'pallet_number' => 'PAL-001',
                'verified' => true,
                'verified_at' => now()->subHours(2),
                'verified_by' => $user->id,
            ],
            [
                'order_number' => 'ORD-2024-004',
                'customer_name' => 'Emily Davis',
                'customer_email' => 'emily.davis@email.com',
                'school_id' => $schools->random()->id,
                'status' => 'shipped',
                'shipping_address_1' => '321 Elm Drive',
                'shipping_city' => 'Pasadena',
                'shipping_state' => 'CA',
                'shipping_zip' => '91125',
                'tracking_number' => 'TRK987654321',
                'pallet_number' => 'PAL-002',
                'verified' => true,
                'verified_at' => now()->subDays(1),
                'verified_by' => $user->id,
            ],
            [
                'order_number' => 'ORD-2024-005',
                'customer_name' => 'David Wilson',
                'customer_email' => 'david.wilson@email.com',
                'school_id' => null, // No school assigned
                'status' => 'delivered',
                'shipping_address_1' => '654 Maple Lane',
                'shipping_city' => 'San Diego',
                'shipping_state' => 'CA',
                'shipping_zip' => '92093',
                'tracking_number' => 'TRK456789123',
                'pallet_number' => 'PAL-003',
                'verified' => true,
                'verified_at' => now()->subDays(3),
                'verified_by' => $user->id,
            ],
            [
                'order_number' => 'ORD-2024-006',
                'customer_name' => 'Lisa Anderson',
                'customer_email' => 'lisa.anderson@email.com',
                'school_id' => $schools->random()->id,
                'status' => 'pending',
                'shipping_address_1' => '987 Cedar Road',
                'shipping_city' => 'Davis',
                'shipping_state' => 'CA',
                'shipping_zip' => '95616',
                'notes' => 'Fragile items - handle with care',
                'verified' => false,
            ],
            [
                'order_number' => 'ORD-2024-007',
                'customer_name' => 'Robert Taylor',
                'customer_email' => 'robert.taylor@email.com',
                'school_id' => $schools->random()->id,
                'status' => 'picked',
                'shipping_address_1' => '147 Birch Street',
                'shipping_city' => 'Irvine',
                'shipping_state' => 'CA',
                'shipping_zip' => '92697',
                'verified' => false,
            ],
            [
                'order_number' => 'ORD-2024-008',
                'customer_name' => 'Jennifer Martinez',
                'customer_email' => 'jennifer.martinez@email.com',
                'school_id' => $schools->random()->id,
                'status' => 'packed',
                'shipping_address_1' => '258 Spruce Avenue',
                'shipping_city' => 'Berkeley',
                'shipping_state' => 'CA',
                'shipping_zip' => '94720',
                'pallet_number' => 'PAL-004',
                'verified' => true,
                'verified_at' => now()->subHours(4),
                'verified_by' => $user->id,
            ],
        ];

        foreach ($orders as $orderData) {
            $order = Order::create($orderData);
            
            // Add some order items
            $itemCount = rand(1, 5);
            for ($i = 1; $i <= $itemCount; $i++) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_name' => "Item {$i} - " . $this->getRandomItemName(),
                    'description' => $this->getRandomDescription(),
                    'quantity' => rand(1, 3),
                    'price' => rand(1000, 10000) / 100, // Random price between $10.00 and $100.00
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

    private function getRandomItemName()
    {
        $items = [
            'Textbook', 'Notebook', 'Pen Set', 'Calculator', 'Laptop Case',
            'Desk Lamp', 'Backpack', 'Water Bottle', 'Coffee Mug', 'Sticky Notes',
            'Highlighters', 'Binder', 'Folder', 'Index Cards', 'Erasers'
        ];
        return $items[array_rand($items)];
    }

    private function getRandomDescription()
    {
        $descriptions = [
            'High quality item for academic use',
            'Essential supplies for students',
            'Durable and reliable product',
            'Perfect for classroom environment',
            'Premium quality materials',
            'Student-friendly design',
            'Educational tool for learning',
            'Comfortable and ergonomic design'
        ];
        return $descriptions[array_rand($descriptions)];
    }
}
