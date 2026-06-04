<?php

namespace App\Media;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class CustomMediaUrlGenerator extends DefaultUrlGenerator
{
    /**
     * Return a URL that goes through our storage-serve route instead of
     * relying on the public symlink (which does not exist on cPanel).
     *
     * Route definition (web.php):
     *   Route::get('/storage/{path}', [StorageController::class, 'serve'])
     *       ->where('path', '.*');
     *
     * Spatie stores files at e.g.:
     *   storage/app/public/1/photo-1-1234567890.jpg
     *
     * The path relative to storage/app/public/ is:
     *   1/photo-1-1234567890.jpg
     *
     * So the URL becomes:
     *   https://yourdomain.com/storage/1/photo-1-1234567890.jpg
     */
    public function getUrl(): string
    {
        // getPathRelativeToRoot() returns the full path inside storage/app/
        // e.g. "public/1/photo-1-1234567890.jpg"
        $pathRelativeToRoot = $this->getPathRelativeToRoot();

        // Strip the leading "public/" prefix — our route serves from storage/app/public/
        $relativePath = ltrim(str_replace('public/', '', $pathRelativeToRoot), '/');

        return url('/storage/' . $relativePath);
    }

    public function getTemporaryUrl(\DateTimeInterface $expiration, array $options = []): string
    {
        // Fall back to the default for temporary URLs (private disk)
        return parent::getTemporaryUrl($expiration, $options);
    }

    public function getResponsiveImagesDirectoryUrl(): string
    {
        $pathRelativeToRoot = $this->media->getPath();
        $directory = rtrim(str_replace(basename($pathRelativeToRoot), '', $pathRelativeToRoot), '/');
        $directory = str_replace(storage_path('app/public/'), '', $directory);

        return url('/storage/' . ltrim($directory, '/')) . '/';
    }
}
