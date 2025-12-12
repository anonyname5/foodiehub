<?php

if (!function_exists('image_url')) {
    /**
     * Get the correct URL for an image path
     * Handles both external URLs and local storage paths
     * 
     * @param string|null $path
     * @return string
     */
    function image_url(?string $path): string
    {
        if (empty($path)) {
            return asset('assets/images/icons/Default User Icon.png');
        }

        // If it's already a full URL (http:// or https://), return as-is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Remove any leading /storage/ or storage/ to avoid duplication
        $cleanPath = preg_replace('#^/?storage/#', '', $path);
        
        // Now prepend storage/ to get the correct asset path
        return asset('storage/' . $cleanPath);
    }
}

