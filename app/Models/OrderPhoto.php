<?php

namespace App\Models;

use App\Traits\S3ImageHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPhoto extends Model
{
    use SoftDeletes, S3ImageHelper;

    protected $fillable = [
        'order_id',
        'photo_path',
        'notes',
        'uploaded_by'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the signed URL for the photo
     *
     * @param int $expirationMinutes
     * @return string|null
     */
    public function getSignedUrl($expirationMinutes = 60)
    {
        return $this->getSignedImageUrl($this->photo_path, $expirationMinutes);
    }
}
