<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Get image URL with fallback
     * 
     * @param string|null $path Path to image in storage
     * @param string|null $fallbackUrl Fallback URL if image doesn't exist
     * @return string
     */
    public static function getImageUrl(?string $path, ?string $fallbackUrl = null): string
    {
        if (empty($path)) {
            return $fallbackUrl ?? self::getPlaceholderImage();
        }

        // Check if it's a full URL
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // Check if file exists in storage
        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        // Return fallback or placeholder
        return $fallbackUrl ?? self::getPlaceholderImage();
    }

    /**
     * Get placeholder image SVG
     * 
     * @return string
     */
    public static function getPlaceholderImage(): string
    {
        return 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><rect width="200" height="200" fill="#f3f4f6"/><text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#9ca3af" font-family="sans-serif" font-size="14">Foto tidak ditemukan</text></svg>');
    }

    /**
     * Check if image exists
     * 
     * @param string|null $path
     * @return bool
     */
    public static function imageExists(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return true; // Assume external URL exists
        }

        return Storage::disk('public')->exists($path);
    }
}
