<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    private string $cloudName;
    private string $apiKey;
    private string $apiSecret;
    private string $uploadPreset;

    public function __construct()
    {
        $this->cloudName    = config('cloudinary.cloud_name');
        $this->apiKey       = config('cloudinary.api_key');
        $this->apiSecret    = config('cloudinary.api_secret');
        $this->uploadPreset = config('cloudinary.upload_preset', 'ogun_congress');
    }

    /**
     * Upload a file to Cloudinary.
     * Returns array with: url, public_id, resource_type, width, height, format, bytes
     */
    public function upload(
        UploadedFile $file,
        string $folder = 'ogun-congress',
        array $options = []
    ): array {
        $resourceType = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image';

        $timestamp = time();
        $params    = array_merge([
            'folder'         => $folder,
            'resource_type'  => $resourceType,
            'timestamp'      => $timestamp,
        ], $options);

        // Build signature (exclude file, api_key from params)
        // These params must NOT be included in the signature
        $excludeFromSig = ['file', 'api_key', 'resource_type', 'cloud_name'];
        $signParams     = array_diff_key($params, array_flip($excludeFromSig));
        ksort($signParams);

        $sigString = collect($signParams)
                ->map(fn ($v, $k) => "{$k}={$v}")
                ->implode('&') . $this->apiSecret;
        $signature = sha1($sigString);

        $response = Http::attach('file', fopen($file->getRealPath(), 'r'), $file->getClientOriginalName())
            ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/{$resourceType}/upload", array_merge($params, [
                'api_key'   => $this->apiKey,
                'signature' => $signature,
            ]));

        if (! $response->successful()) {
            Log::error('cloudinary.upload_failed', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('Cloudinary upload failed: ' . ($response->json('error.message') ?? $response->body()));
        }

        $data = $response->json();

        return [
            'url'           => $data['secure_url'],
            'public_id'     => $data['public_id'],
            'resource_type' => $resourceType,
            'width'         => $data['width'] ?? null,
            'height'        => $data['height'] ?? null,
            'format'        => $data['format'] ?? null,
            'bytes'         => $data['bytes'] ?? null,
            'thumbnail_url' => $resourceType === 'video'
                ? ($data['secure_url'] ?? null) // use poster frame URL for videos
                : null,
        ];
    }

    /**
     * Delete a file from Cloudinary by public_id.
     */
    public function delete(string $publicId, string $resourceType = 'image'): bool
    {
        $timestamp = time();
        $sigString = "public_id={$publicId}&timestamp={$timestamp}" . $this->apiSecret;
        $signature = sha1($sigString);

        $response = Http::post("https://api.cloudinary.com/v1_1/{$this->cloudName}/{$resourceType}/destroy", [
            'public_id' => $publicId,
            'api_key'   => $this->apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ]);

        return $response->json('result') === 'ok';
    }

    /**
     * Build an optimised transformation URL.
     * e.g. auto format, quality, resize to fit.
     */
    public function transform(string $publicId, array $transforms = []): string
    {
        $t = array_merge(['f_auto', 'q_auto'], $transforms);
        $tStr = implode(',', $t);
        return "https://res.cloudinary.com/{$this->cloudName}/image/upload/{$tStr}/{$publicId}";
    }
}
