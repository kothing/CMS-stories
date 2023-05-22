<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;

if (! function_exists('is_image')) {
    /**
     * @deprecated since 5.7
     */
    function is_image(string $mimeType): bool
    {
        return RvMedia::isImage($mimeType);
    }
}

if (! function_exists('get_image_url')) {
    /**
     * @deprecated since 5.7
     */
    function get_image_url(string $url, ?string $size = null, bool $relativePath = false, $default = null): string
    {
        return RvMedia::getImageUrl($url, $size, $relativePath, $default);
    }
}

if (! function_exists('get_object_image')) {
    /**
     * @deprecated since 5.7
     */
    function get_object_image(string $image, $size = null, bool $relativePath = false): ?string
    {
        return RvMedia::getImageUrl($image, $size, $relativePath, RvMedia::getDefaultImage());
    }
}

if (! function_exists('rv_media_handle_upload')) {
    /**
     * @deprecated since 5.7
     */
    function rv_media_handle_upload(?UploadedFile $fileUpload, int $folderId = 0, string $path = ''): array|JsonResponse
    {
        return RvMedia::handleUpload($fileUpload, $folderId, $path);
    }
}
