<?php

namespace App\Http\Controllers;

use App\Models\Pallet;
use App\Models\Order;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pallet::with(['school', 'creator', 'photos']);

        // School users cannot access pallets
        $user = Auth::user();
        if ($user->role === 'school') {
            abort(403, 'School users cannot access pallet management.');
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('pallet_number', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('lot', 'like', '%' . $request->search . '%');
            });
        }

        $pallets = $query->orderBy('created_at', 'desc')->paginate(20);
        $schools = School::where('is_active', true)->get();

        return view('pallets.index', compact('pallets', 'schools'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $schools = School::where('is_active', true)->get();
        return view('pallets.create', compact('schools'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pallet_number' => 'required|string|max:255|unique:pallets',
            'school_id' => 'nullable|exists:schools,id',
            'location' => 'nullable|string|max:255',
            'lot' => 'nullable|string|max:255',
            'shipping_address_1' => 'required|string|max:255',
            'shipping_address_2' => 'nullable|string|max:255',
            'shipping_address_3' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'required|string|max:255',
            'shipping_zip' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $pallet = Pallet::create([
            'pallet_number' => $request->pallet_number,
            'school_id' => $request->school_id,
            'location' => $request->location,
            'lot' => $request->lot,
            'shipping_address_1' => $request->shipping_address_1,
            'shipping_address_2' => $request->shipping_address_2,
            'shipping_address_3' => $request->shipping_address_3,
            'shipping_city' => $request->shipping_city,
            'shipping_state' => $request->shipping_state,
            'shipping_zip' => $request->shipping_zip,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        // Log the creation action
        $pallet->actions()->create([
            'action_type' => 'pallet_created',
            'description' => 'Pallet created',
            'performed_by' => Auth::id(),
        ]);

        return redirect()->route('pallets.show', $pallet)->with('success', 'Pallet created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pallet $pallet)
    {
        $pallet->load(['school', 'creator', 'photos.uploadedBy', 'actions.performedBy', 'orders.school', 'palletOrders.order', 'palletOrders.verifiedBy']);
        return view('pallets.show', compact('pallet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pallet $pallet)
    {
        $schools = School::where('is_active', true)->get();
        return view('pallets.edit', compact('pallet', 'schools'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pallet $pallet)
    {
        $request->validate([
            'pallet_number' => 'required|string|max:255|unique:pallets,pallet_number,' . $pallet->id,
            'status' => 'required|in:packing,shipped,delivered',
            'school_id' => 'nullable|exists:schools,id',
            'location' => 'nullable|string|max:255',
            'lot' => 'nullable|string|max:255',
            'shipping_address_1' => 'required|string|max:255',
            'shipping_address_2' => 'nullable|string|max:255',
            'shipping_address_3' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'required|string|max:255',
            'shipping_zip' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $pallet->only([
            'pallet_number', 'status', 'school_id', 'location', 
            'lot', 'shipping_address_1', 'shipping_address_2', 'shipping_address_3', 
            'shipping_city', 'shipping_state', 'shipping_zip', 'notes'
        ]);

        $pallet->update($request->only([
            'pallet_number', 'status', 'school_id', 'location', 
            'lot', 'shipping_address_1', 'shipping_address_2', 'shipping_address_3', 
            'shipping_city', 'shipping_state', 'shipping_zip', 'notes'
        ]));

        // Log the update action
        $pallet->actions()->create([
            'action_type' => 'pallet_updated',
            'description' => 'Pallet information updated',
            'old_values' => $oldValues,
            'new_values' => $request->only([
                'pallet_number', 'status', 'school_id', 'location', 
                'lot', 'shipping_address_1', 'shipping_address_2', 'shipping_address_3', 
                'shipping_city', 'shipping_state', 'shipping_zip', 'notes'
            ]),
            'performed_by' => Auth::id(),
        ]);

        return redirect()->route('pallets.show', $pallet)->with('success', 'Pallet updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pallet $pallet)
    {
        // Log the deletion action
        $pallet->actions()->create([
            'action_type' => 'pallet_deleted',
            'description' => 'Pallet deleted',
            'performed_by' => Auth::id(),
        ]);

        $pallet->delete();
        return redirect()->route('pallets.index')->with('success', 'Pallet deleted successfully.');
    }

    /**
     * Import pallet numbers
     */
    public function import(Request $request)
    {
        $request->validate([
            'pallet_numbers' => 'required|string',
            'school_id' => 'nullable|exists:schools,id',
            'location' => 'nullable|string|max:255',
            'lot' => 'nullable|string|max:255',
        ]);

        $palletNumbers = array_filter(array_map('trim', explode("\n", $request->pallet_numbers)));
        $created = 0;
        $errors = [];

        foreach ($palletNumbers as $palletNumber) {
            if (empty($palletNumber)) continue;

            try {
                       $pallet = Pallet::create([
                           'pallet_number' => $palletNumber,
                           'school_id' => $request->school_id,
                           'location' => $request->location,
                           'lot' => $request->lot,
                           'shipping_address_1' => $request->shipping_address_1 ?? '',
                           'shipping_address_2' => $request->shipping_address_2,
                           'shipping_address_3' => $request->shipping_address_3,
                           'shipping_city' => $request->shipping_city ?? '',
                           'shipping_state' => $request->shipping_state ?? '',
                           'shipping_zip' => $request->shipping_zip ?? '',
                           'created_by' => Auth::id(),
                       ]);

                       // Log the import action
                       $pallet->actions()->create([
                           'action_type' => 'pallet_imported',
                           'description' => 'Pallet imported via bulk import',
                           'performed_by' => Auth::id(),
                       ]);
                $created++;
            } catch (\Exception $e) {
                $errors[] = "Failed to create pallet {$palletNumber}: " . $e->getMessage();
            }
        }

        $message = "Successfully created {$created} pallets.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Show upload photo form
     */
    public function showUploadPhoto(Pallet $pallet)
    {
        return view('pallets.upload-photo', compact('pallet'));
    }

    /**
     * Upload photo to pallet
     */
    public function uploadPhoto(Request $request, Pallet $pallet)
    {
        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|max:10240', // 10MB max per photo
            'notes' => 'nullable|string|max:1000',
        ]);

        $uploadedPhotos = [];
        $errors = [];

        foreach ($request->file('photos') as $photo) {
            try {
                // Generate unique filename with random string
                $originalName = $photo->getClientOriginalName();
                $extension = $photo->getClientOriginalExtension();
                $randomString = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
                $filename = pathinfo($originalName, PATHINFO_FILENAME) . '_' . $randomString . '.' . $extension;
                
                $path = $photo->storeAs('pallet-photos', $filename, 'public');

                $pallet->photos()->create([
                    'photo_path' => $path,
                    'notes' => $request->notes,
                    'uploaded_by' => Auth::id(),
                ]);

                $uploadedPhotos[] = $filename;
            } catch (\Exception $e) {
                $errors[] = "Failed to upload {$photo->getClientOriginalName()}: " . $e->getMessage();
            }
        }

        // Log the photo upload action
        if (!empty($uploadedPhotos)) {
            $pallet->actions()->create([
                'action_type' => 'photos_uploaded',
                'description' => "Photos uploaded: " . implode(', ', $uploadedPhotos),
                'performed_by' => Auth::id(),
            ]);
        }

        if (!empty($errors)) {
            $message = count($uploadedPhotos) . ' photos uploaded successfully. Errors: ' . implode('; ', $errors);
            return redirect()->route('pallets.show', $pallet)->with('warning', $message);
        }

        $message = count($uploadedPhotos) > 1 ? 
            count($uploadedPhotos) . ' photos uploaded successfully.' : 
            'Photo uploaded successfully.';

        return redirect()->route('pallets.show', $pallet)->with('success', $message);
    }

    /**
     * Delete a photo from a pallet
     */
    public function deletePhoto(\App\Models\PalletPhoto $photo)
    {
        // Ensure the photo belongs to a pallet the user can access
        $pallet = $photo->pallet;
        
        // Log the photo deletion action
        $pallet->actions()->create([
            'action_type' => 'photo_deleted',
            'description' => "Deleted photo: {$photo->photo_path}",
            'performed_by' => Auth::id(),
        ]);

        $photo->delete(); // Soft delete

        return redirect()->back()->with('success', 'Photo deleted successfully.');
    }

    /**
     * Display orders for a pallet
     */
    public function orders(Pallet $pallet)
    {
        $pallet->load(['orders.school', 'palletOrders.order', 'palletOrders.verifiedBy']);
        return view('pallets.orders', compact('pallet'));
    }

    /**
     * Show QR Scanner page
     */
    public function qrScanner()
    {
        return view('pallets.qr-scanner');
    }

    /**
     * Show Import Pallets page
     */
    public function showImport()
    {
        return view('pallets.import');
    }

    /**
     * Verify an order is on a pallet
     */
    public function verifyOrder(Request $request, Pallet $pallet, Order $order)
    {
        try {
            // Check if the order is assigned to this pallet
            if ($order->pallet_number !== $pallet->pallet_number) {
                return redirect()->back()->with('error', 'Order is not assigned to this pallet.');
            }

            // Create or update the pallet order verification
            $palletOrder = \App\Models\PalletOrder::updateOrCreate(
                [
                    'pallet_id' => $pallet->id,
                    'order_id' => $order->id,
                ],
                [
                    'verified' => true,
                    'verified_at' => now(),
                    'verified_by' => Auth::id(),
                ]
            );

            // Log the verification action
            $pallet->actions()->create([
                'action_type' => 'order_verified',
                'description' => "Order #{$order->order_number} verified as being on pallet",
                'performed_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', "Order #{$order->order_number} verified successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error verifying order: ' . $e->getMessage());
        }
    }

    /**
     * Unverify an order from a pallet
     */
    public function unverifyOrder(Request $request, Pallet $pallet, Order $order)
    {
        try {
            // Find the pallet order record
            $palletOrder = \App\Models\PalletOrder::where('pallet_id', $pallet->id)
                ->where('order_id', $order->id)
                ->first();

            if (!$palletOrder) {
                return redirect()->back()->with('error', 'Order verification not found.');
            }

            // Update the verification status
            $palletOrder->update([
                'verified' => false,
                'verified_at' => null,
                'verified_by' => null,
            ]);

            // Log the unverification action
            $pallet->actions()->create([
                'action_type' => 'order_unverified',
                'description' => "Order #{$order->order_number} unverified from pallet",
                'performed_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', "Order #{$order->order_number} unverified successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error unverifying order: ' . $e->getMessage());
        }
    }

    /**
     * Show all photos for a pallet
     */
    public function photos(Pallet $pallet)
    {
        $pallet->load(['photos.uploadedBy']);
        return view('pallets.photos', compact('pallet'));
    }

    /**
     * Show a specific photo for a pallet
     */
    public function showPhoto(Pallet $pallet, $photoId)
    {
        $photo = $pallet->photos()->findOrFail($photoId);
        $pallet->load(['photos.uploadedBy']);
        
        // Get current photo index for navigation
        $photos = $pallet->photos;
        $currentIndex = $photos->search(function($item) use ($photo) {
            return $item->id === $photo->id;
        });
        
        return view('pallets.photo-detail', compact('pallet', 'photo', 'currentIndex', 'photos'));
    }
}
