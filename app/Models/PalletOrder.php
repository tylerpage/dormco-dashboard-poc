<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PalletOrder extends Model
{
    protected $fillable = [
        'pallet_id',
        'order_id',
        'verified',
        'verified_at',
        'verified_by',
        'notes'
    ];

    protected $casts = [
        'verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function pallet(): BelongsTo
    {
        return $this->belongsTo(Pallet::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
