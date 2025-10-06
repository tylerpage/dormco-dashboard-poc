<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\School;
use App\Models\SavedView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['school', 'items']);

        // School users can only see orders from their assigned schools
        $user = Auth::user();
        if ($user->role === 'school' && $user->assigned_schools) {
            $query->whereIn('school_id', $user->assigned_schools);
        }

        // Apply filters
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

        if ($request->filled('verified')) {
            if ($request->verified === 'yes') {
                $query->where('verified', true);
            } elseif ($request->verified === 'no') {
                $query->where('verified', false);
            }
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_email', 'like', '%' . $request->search . '%')
                  ->orWhere('order_number', 'like', '%' . $request->search . '%');
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        $schools = School::where('is_active', true)->get();
        $savedViews = SavedView::where('user_id', Auth::id())
            ->where('type', 'orders')
            ->get();

        // Calculate order counts for each saved view
        foreach ($savedViews as $view) {
            $viewQuery = Order::query();
            
            // Apply the saved filters
            if (isset($view->filters['school_id'])) {
                if ($view->filters['school_id'] === 'none') {
                    $viewQuery->whereNull('school_id');
                } else {
                    $viewQuery->where('school_id', $view->filters['school_id']);
                }
            }
            
            if (isset($view->filters['status'])) {
                $viewQuery->where('status', $view->filters['status']);
            }
            
            if (isset($view->filters['search'])) {
                $viewQuery->where(function($q) use ($view) {
                    $q->where('customer_name', 'like', '%' . $view->filters['search'] . '%')
                      ->orWhere('customer_email', 'like', '%' . $view->filters['search'] . '%')
                      ->orWhere('order_number', 'like', '%' . $view->filters['search'] . '%');
                });
            }
            
            $view->orders_count = $viewQuery->count();
        }

        return view('orders.index', compact('orders', 'schools', 'savedViews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $schools = School::where('is_active', true)->get();
        $pallets = \App\Models\Pallet::orderBy('pallet_number')->get();
        return view('orders.create', compact('schools', 'pallets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string|max:255|unique:orders',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'school_id' => 'nullable|exists:schools,id',
            'shipping_address_1' => 'required|string|max:255',
            'shipping_address_2' => 'nullable|string|max:255',
            'shipping_address_3' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'required|string|max:255',
            'shipping_zip' => 'required|string|max:20',
            'pallet_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'verified' => 'nullable|boolean',
        ]);

        $orderData = [
            'order_number' => $request->order_number,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'school_id' => $request->school_id,
            'shipping_address_1' => $request->shipping_address_1,
            'shipping_address_2' => $request->shipping_address_2,
            'shipping_address_3' => $request->shipping_address_3,
            'shipping_city' => $request->shipping_city,
            'shipping_state' => $request->shipping_state,
            'shipping_zip' => $request->shipping_zip,
            'pallet_number' => $request->pallet_number,
            'notes' => $request->notes,
            'item_count' => 0, // Will be updated when items are added
        ];

        // Handle verification for all user types
        if ($request->has('verified')) {
            $orderData['verified'] = $request->boolean('verified');
            if ($request->boolean('verified')) {
                $orderData['verified_at'] = now();
                $orderData['verified_by'] = Auth::id();
            }
        }

        $order = Order::create($orderData);

        // Log the creation action
        $order->actions()->create([
            'action_type' => 'order_created',
            'description' => 'Order created',
            'performed_by' => Auth::id(),
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        // School users can only see orders from their assigned schools
        $user = Auth::user();
        if ($user->role === 'school' && $user->assigned_schools) {
            if (!in_array($order->school_id, $user->assigned_schools)) {
                abort(403, 'You can only view orders from your assigned schools.');
            }
        }

        $order->load(['school', 'items', 'actions.performedBy', 'photos.uploadedBy', 'verifiedBy']);
        $pallets = \App\Models\Pallet::orderBy('pallet_number')->get();
        return view('orders.show', compact('order', 'pallets'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $schools = School::where('is_active', true)->get();
        $pallets = \App\Models\Pallet::orderBy('pallet_number')->get();
        return view('orders.edit', compact('order', 'schools', 'pallets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // School users can only edit customer name, shipping address, and verification
        if ($user->role === 'school') {
            $request->validate([
                'customer_name' => 'required|string|max:255',
                'shipping_address_1' => 'required|string|max:255',
                'shipping_address_2' => 'nullable|string|max:255',
                'shipping_address_3' => 'nullable|string|max:255',
                'shipping_city' => 'required|string|max:255',
                'shipping_state' => 'required|string|max:255',
                'shipping_zip' => 'required|string|max:20',
                'verified' => 'nullable|boolean',
            ]);
        } else {
            $request->validate([
                'order_number' => 'required|string|max:255|unique:orders,order_number,' . $order->id,
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'school_id' => 'nullable|exists:schools,id',
                'status' => 'required|in:pending,picked,packed,shipped,delivered',
                'shipping_address_1' => 'required|string|max:255',
                'shipping_address_2' => 'nullable|string|max:255',
                'shipping_address_3' => 'nullable|string|max:255',
                'shipping_city' => 'required|string|max:255',
                'shipping_state' => 'required|string|max:255',
                'shipping_zip' => 'required|string|max:20',
                'tracking_number' => 'nullable|string|max:255',
                'pallet_number' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'verified' => 'nullable|boolean',
            ]);
        }

        // Determine which fields to track and update based on user role
        if ($user->role === 'school') {
            $allowedFields = ['customer_name', 'shipping_address_1', 'shipping_address_2', 'shipping_address_3', 
                            'shipping_city', 'shipping_state', 'shipping_zip'];
            $oldValues = $order->only($allowedFields);
            
            // Handle verification for school users too
            $updateData = $request->only($allowedFields);
            if ($request->has('verified')) {
                $updateData['verified'] = $request->boolean('verified');
                if ($request->boolean('verified')) {
                    $updateData['verified_at'] = now();
                    $updateData['verified_by'] = $user->id;
                } else {
                    $updateData['verified_at'] = null;
                    $updateData['verified_by'] = null;
                }
            }
            
            $order->update($updateData);
            $newValues = $updateData;
        } else {
            $allowedFields = ['order_number', 'customer_name', 'customer_email', 'school_id', 
                            'status', 'shipping_address_1', 'shipping_address_2', 'shipping_address_3', 
                            'shipping_city', 'shipping_state', 'shipping_zip', 
                            'tracking_number', 'pallet_number', 'notes'];
            $oldValues = $order->only($allowedFields);
            
            // Handle verification separately
            $updateData = $request->only($allowedFields);
            if ($request->has('verified')) {
                $updateData['verified'] = $request->boolean('verified');
                if ($request->boolean('verified')) {
                    $updateData['verified_at'] = now();
                    $updateData['verified_by'] = $user->id;
                } else {
                    $updateData['verified_at'] = null;
                    $updateData['verified_by'] = null;
                }
            }
            
            $order->update($updateData);
            $newValues = $updateData;
        }

        // Log the update action
        $order->actions()->create([
            'action_type' => 'order_updated',
            'description' => 'Order information updated',
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'performed_by' => Auth::id(),
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }

    /**
     * Update shipping information
     */
    public function updateShipping(Request $request, Order $order)
    {
        // School users cannot edit shipping information
        $user = Auth::user();
        if ($user->role === 'school') {
            abort(403, 'School users cannot edit shipping information.');
        }

        $request->validate([
            'tracking_number' => 'nullable|string|max:255',
            'pallet_number' => 'nullable|string|max:255',
            'shipping_address_1' => 'required|string|max:255',
            'shipping_address_2' => 'nullable|string|max:255',
            'shipping_address_3' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'required|string|max:255',
            'shipping_zip' => 'required|string|max:20',
        ]);

        $oldValues = $order->only([
            'tracking_number', 'pallet_number', 
            'shipping_address_1', 'shipping_address_2', 'shipping_address_3', 
            'shipping_city', 'shipping_state', 'shipping_zip'
        ]);
        
        $order->update($request->only([
            'tracking_number', 'pallet_number', 
            'shipping_address_1', 'shipping_address_2', 'shipping_address_3', 
            'shipping_city', 'shipping_state', 'shipping_zip'
        ]));

        // Log the action
        $order->actions()->create([
            'action_type' => 'shipping_updated',
            'description' => 'Shipping information updated',
            'old_values' => $oldValues,
            'new_values' => $request->only([
                'tracking_number', 'pallet_number', 
                'shipping_address_1', 'shipping_address_2', 'shipping_address_3', 
                'shipping_city', 'shipping_state', 'shipping_zip'
            ]),
            'performed_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Shipping information updated successfully.');
    }

    /**
     * Save a filter view
     */
    public function saveView(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'filters' => 'required|string',
            ]);

            // Decode the JSON filters
            $filters = json_decode($request->filters, true);
            
            if (!$filters) {
                $filters = [];
            }

            SavedView::create([
                'name' => $request->name,
                'type' => 'orders',
                'filters' => $filters,
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'View saved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saving view: ' . $e->getMessage());
        }
    }

    /**
     * Load a saved view
     */
    public function loadView(SavedView $view)
    {
        // Ensure the view belongs to the current user
        if ($view->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to saved view.');
        }
        
        $filters = $view->filters;
        return redirect()->route('orders.index', $filters);
    }

    /**
     * Delete a saved view
     */
    public function deleteView(SavedView $view)
    {
        // Ensure the view belongs to the current user
        if ($view->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to saved view.');
        }
        
        $view->delete();
        
        return redirect()->back()->with('success', 'View deleted successfully.');
    }

    /**
     * Upload photos for an order
     */
    public function uploadPhotos(Request $request, Order $order)
    {
        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|max:10240', // 10MB max per file
            'notes' => 'nullable|string|max:1000',
        ]);

        $uploadedCount = 0;
        $errors = [];

        foreach ($request->file('photos') as $photo) {
            try {
                // Generate unique filename with random string
                $originalName = $photo->getClientOriginalName();
                $extension = $photo->getClientOriginalExtension();
                $randomString = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
                $filename = pathinfo($originalName, PATHINFO_FILENAME) . '_' . $randomString . '.' . $extension;
                
                $path = $photo->storeAs('order-photos', $filename, 'public');
                
                $order->photos()->create([
                    'photo_path' => $path,
                    'notes' => $request->notes,
                    'uploaded_by' => Auth::id(),
                ]);
                
                $uploadedCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to upload photo: " . $e->getMessage();
            }
        }

        // Log the action
        $order->actions()->create([
            'action_type' => 'photos_uploaded',
            'description' => "Uploaded {$uploadedCount} photo(s)",
            'performed_by' => Auth::id(),
        ]);

        $message = "Successfully uploaded {$uploadedCount} photo(s).";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Delete a photo from an order
     */
    public function deletePhoto(\App\Models\OrderPhoto $photo)
    {
        // Ensure the photo belongs to an order the user can access
        $order = $photo->order;
        
        // Log the action
        $order->actions()->create([
            'action_type' => 'photo_deleted',
            'description' => "Deleted photo: {$photo->photo_path}",
            'performed_by' => Auth::id(),
        ]);

        $photo->delete(); // Soft delete

        return redirect()->back()->with('success', 'Photo deleted successfully.');
    }

    /**
     * Toggle verification status of an order
     */
    public function toggleVerification(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // School users can only verify orders from their assigned schools
        if ($user->role === 'school' && $user->assigned_schools) {
            if (!in_array($order->school_id, $user->assigned_schools)) {
                abort(403, 'You can only verify orders from your assigned schools.');
            }
        }

        $verified = $request->boolean('verified');
        
        if ($verified) {
            $order->update([
                'verified' => true,
                'verified_at' => now(),
                'verified_by' => $user->id,
            ]);
            
            $message = 'Order verified successfully.';
        } else {
            $order->update([
                'verified' => false,
                'verified_at' => null,
                'verified_by' => null,
            ]);
            
            $message = 'Order verification removed.';
        }

        // Log the action
        $order->actions()->create([
            'action_type' => $verified ? 'order_verified' : 'order_unverified',
            'description' => $verified ? 'Order marked as verified' : 'Order verification removed',
            'performed_by' => $user->id,
        ]);

        return redirect()->back()->with('success', $message);
    }
}
