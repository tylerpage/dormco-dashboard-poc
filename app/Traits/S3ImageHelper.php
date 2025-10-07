<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait S3ImageHelper
{
    /**
     * Generate a signed URL for a private S3 image
     *
     * @param string $path
     * @param int $expirationMinutes
     * @return string
     */
    public function getSignedImageUrl($path, $expirationMinutes = 60)
    {
        if (empty($path)) {
            return null;
        }

        try {
            return Storage::disk('public')->temporaryUrl($path, now()->addMinutes($expirationMinutes));
        } catch (\Exception $e) {
            // Fallback to regular URL if signed URL generation fails
            return Storage::disk('public')->url($path);
        }
    }

    /**
     * Generate a signed URL for multiple images
     *
     * @param array $paths
     * @param int $expirationMinutes
     * @return array
     */
    public function getSignedImageUrls($paths, $expirationMinutes = 60)
    {
        $urls = [];
        foreach ($paths as $path) {
            $urls[] = $this->getSignedImageUrl($path, $expirationMinutes);
        }
        return $urls;
    }
}
