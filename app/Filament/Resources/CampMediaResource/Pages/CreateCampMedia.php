<?php

namespace App\Filament\Resources\CampMediaResource\Pages;

use App\Filament\Resources\CampMediaResource;
use App\Services\CloudinaryService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateCampMedia extends CreateRecord
{
    protected static string $resource = CampMediaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uploaded_by'] = auth()->id();
        // Remove the virtual upload field — it's not a DB column
        unset($data['upload_file']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->pushToCloudinary($this->record);
    }

    private function pushToCloudinary(\App\Models\CampMedia $record): void
    {
        // getRawState() includes ALL fields, even dehydrated(false) ones
        $raw = $this->form->getRawState()['upload_file'] ?? null;

        if (! $raw) {
            Log::debug('camp_media.no_file', ['record' => $record->id]);
            return;
        }

        // Filament FileUpload stores value as array keyed by uuid
        $tmpPath = is_array($raw) ? array_values($raw)[0] : $raw;

        Log::debug('camp_media.tmp_path', ['path' => $tmpPath]);

        // Livewire temp disk — 'local' disk, files are under livewire-tmp/
        $disk     = Storage::disk('local');
        $fullPath = $disk->path($tmpPath);

        // If not found on local disk, try the public disk
        if (! file_exists($fullPath)) {
            $disk     = Storage::disk('public');
            $fullPath = $disk->path($tmpPath);
        }

        if (! file_exists($fullPath)) {
            Log::error('camp_media.file_not_found', [
                'tmp_path'  => $tmpPath,
                'full_path' => $fullPath,
                'record'    => $record->id,
            ]);
            Notification::make()
                ->title('File not found on server. Please try again.')
                ->danger()
                ->send();
            return;
        }

        try {
            $mime = mime_content_type($fullPath);

            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $fullPath,
                basename($tmpPath),
                $mime,
                null,
                true // test mode — skip is_uploaded_file() check
            );

            $folder = 'ogun-congress/' . now()->year . '/media';
            $result = app(CloudinaryService::class)->upload($uploadedFile, $folder);

            $record->update([
                'cloudinary_url'       => $result['url'],
                'cloudinary_public_id' => $result['public_id'],
                'media_type'           => $result['resource_type'],
                'width'                => $result['width'],
                'height'               => $result['height'],
                'file_size'            => $result['bytes'],
                'thumbnail_url'        => $result['thumbnail_url'],
            ]);

            Notification::make()
                ->title('Uploaded to Cloudinary successfully.')
                ->success()
                ->send();

        } catch (\Throwable $e) {
            Log::error('camp_media.cloudinary_failed', [
                'error'  => $e->getMessage(),
                'record' => $record->id,
            ]);
            Notification::make()
                ->title('Cloudinary upload failed: ' . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        } finally {
            // Clean up temp file
            if (file_exists($fullPath)) {
                $disk->delete($tmpPath);
            }
        }
    }
}
