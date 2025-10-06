<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'school_id',
        'item_count',
        'status',
        'shipping_address',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_address_3',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'tracking_number',
        'pallet_number',
        'notes',
        'verified',
        'verified_at',
        'verified_by'
    ];

    protected $casts = [
        'status' => 'string',
        'verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(OrderAction::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(OrderPhoto::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
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
