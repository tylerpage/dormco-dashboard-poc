<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Pallet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    /**
     * Display the exports index page
     */
    public function index()
    {
        return view('exports.index');
    }

    /**
     * Export orders to CSV
     */
    public function exportOrders(Request $request)
    {
        $query = Order::with(['school', 'items']);

        // Apply filters if provided
        if ($request->filled('school_id')) {
            if ($request->school_id === 'none') {
                $query->whereNull('school_id');
            } else {
                $query->where('school_id', $request->school_id);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $filename = 'orders_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Order Number',
                'Customer Name',
                'Customer Email',
                'School',
                'Status',
                'Item Count',
                'Tracking Number',
                'Pallet Number',
                'Created Date',
                'Shipping Address'
            ]);

            // CSV Data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->customer_name,
                    $order->customer_email,
                    $order->school ? $order->school->name : 'No School',
                    $order->status,
                    $order->item_count,
                    $order->tracking_number ?: '',
                    $order->pallet_number ?: '',
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->shipping_address
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export pallets to CSV
     */
    public function exportPallets(Request $request)
    {
        $query = Pallet::with(['school', 'creator']);

        // Apply filters if provided
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $pallets = $query->orderBy('created_at', 'desc')->get();

        $filename = 'pallets_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($pallets) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Pallet Number',
                'Status',
                'School',
                'Location',
                'Lot',
                'Created By',
                'Created Date',
                'Shipping Address',
                'Notes'
            ]);

            // CSV Data
            foreach ($pallets as $pallet) {
                fputcsv($file, [
                    $pallet->pallet_number,
                    $pallet->status,
                    $pallet->school ? $pallet->school->name : 'No School',
                    $pallet->location ?: '',
                    $pallet->lot ?: '',
                    $pallet->creator->name,
                    $pallet->created_at->format('Y-m-d H:i:s'),
                    $pallet->shipping_address,
                    $pallet->notes ?: ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
