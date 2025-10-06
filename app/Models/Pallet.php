<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pallet extends Model
{
    protected $fillable = [
        'pallet_number',
        'status',
        'location',
        'lot',
        'shipping_address',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_address_3',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'school_id',
        'created_by',
        'notes'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function getRouteKeyName()
    {
        return 'pallet_number';
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PalletPhoto::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(PalletAction::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'pallet_number', 'pallet_number');
    }

    public function palletOrders(): HasMany
    {
        return $this->hasMany(PalletOrder::class);
    }

    /**
     * Get formatted shipping address
     */
    public function getFormattedShippingAddressAttribute()
    {
        $address = [];
        
        if ($this->shipping_address_1) {
            $address[] = $this->shipping_address_1;
        }
        if ($this->shipping_address_2) {
            $address[] = $this->shipping_address_2;
        }
        if ($this->shipping_address_3) {
            $address[] = $this->shipping_address_3;
        }
        
        $cityStateZip = [];
        if ($this->shipping_city) {
            $cityStateZip[] = $this->shipping_city;
        }
        if ($this->shipping_state) {
            $cityStateZip[] = $this->shipping_state;
        }
        if ($this->shipping_zip) {
            $cityStateZip[] = $this->shipping_zip;
        }
        
        if (!empty($cityStateZip)) {
            $address[] = implode(', ', $cityStateZip);
        }
        
        return implode("\n", $address);
    }
}
