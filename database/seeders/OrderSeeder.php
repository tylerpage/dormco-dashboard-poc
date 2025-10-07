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

        // Generate 100 orders
        $orders = [];
        $statuses = ['pending', 'picked', 'packed', 'shipped', 'delivered'];
        $cities = ['Berkeley', 'Stanford', 'Los Angeles', 'Pasadena', 'San Diego', 'Davis', 'Irvine', 'San Francisco', 'Oakland', 'Fresno'];
        $states = ['CA', 'NY', 'TX', 'FL', 'IL', 'PA', 'OH', 'GA', 'NC', 'MI'];
        
        for ($i = 1; $i <= 100; $i++) {
            $orderNumber = 'ORD-2024-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $status = $statuses[array_rand($statuses)];
            $city = $cities[array_rand($cities)];
            $state = $states[array_rand($states)];
            $zip = rand(10000, 99999);
            
            $order = [
                'order_number' => $orderNumber,
                'customer_name' => $this->getRandomName(),
                'customer_email' => $this->getRandomEmail(),
                'school_id' => rand(1, 10) <= 8 ? $schools->random()->id : null, // 80% have schools
                'status' => $status,
                'shipping_address_1' => rand(100, 9999) . ' ' . $this->getRandomStreetName(),
                'shipping_city' => $city,
                'shipping_state' => $state,
                'shipping_zip' => $zip,
                'notes' => rand(1, 3) === 1 ? $this->getRandomNote() : null,
                'verified' => rand(1, 3) === 1, // 33% verified
            ];
            
            // Add tracking number for shipped/delivered orders
            if (in_array($status, ['shipped', 'delivered'])) {
                $order['tracking_number'] = 'TRK' . rand(100000000, 999999999);
            }
            
            // Add pallet number for packed/shipped/delivered orders
            if (in_array($status, ['packed', 'shipped', 'delivered'])) {
                $order['pallet_number'] = 'PAL-' . str_pad(rand(1, 50), 3, '0', STR_PAD_LEFT);
            }
            
            // Add verification details for verified orders
            if ($order['verified']) {
                $order['verified_at'] = now()->subDays(rand(0, 30))->subHours(rand(0, 23));
                $order['verified_by'] = $user->id;
            }
            
            $orders[] = $order;
        }

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

    private function getRandomName()
    {
        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Robert', 'Lisa', 'James', 'Jennifer', 'William', 'Ashley', 'Richard', 'Jessica', 'Charles', 'Amanda', 'Thomas', 'Melissa', 'Christopher', 'Deborah'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin'];
        
        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    private function getRandomEmail()
    {
        $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'email.com', 'student.edu'];
        $name = strtolower(str_replace(' ', '.', $this->getRandomName()));
        return $name . '@' . $domains[array_rand($domains)];
    }

    private function getRandomStreetName()
    {
        $streets = ['Main St', 'Oak Ave', 'Pine St', 'Elm Dr', 'Maple Ln', 'Cedar Rd', 'Birch St', 'Spruce Ave', 'First St', 'Second Ave', 'Park Rd', 'University Blvd', 'College St', 'Campus Dr'];
        return $streets[array_rand($streets)];
    }

    private function getRandomNote()
    {
        $notes = [
            'Rush order - needs to be processed quickly',
            'Fragile items - handle with care',
            'Priority shipping requested',
            'Special handling required',
            'Customer requested expedited processing',
            'Contains electronics - handle carefully',
            'Large order - may require special packaging',
            'Customer notes: Please call before delivery'
        ];
        return $notes[array_rand($notes)];
    }
}
