<?php

namespace App\Filament\Resources\CampMediaResource\Pages;

use App\Filament\Resources\CampMediaResource;
use App\Services\CloudinaryService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditCampMedia extends EditRecord
{
    protected static string $resource = CampMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    if ($this->record->cloudinary_public_id) {
                        try {
                            app(CloudinaryService::class)->delete(
                                $this->record->cloudinary_public_id,
                                $this->record->media_type ?? 'image'
                            );
                        } catch (\Throwable $e) {
                            Log::warning('cloudinary.delete_failed', ['error' => $e->getMessage()]);
                        }
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['upload_file']);
        return $data;
    }

    protected function afterSave(): void
    {
        $raw = $this->form->getRawState()['upload_file'] ?? null;

        if (! $raw) {
            return; // metadata-only edit, no new file
        }

        $tmpPath = is_array($raw) ? array_values($raw)[0] : $raw;

        Log::debug('camp_media.edit_tmp_path', ['path' => $tmpPath]);

        $disk     = Storage::disk('local');
        $fullPath = $disk->path($tmpPath);

        if (! file_exists($fullPath)) {
            $disk     = Storage::disk('public');
            $fullPath = $disk->path($tmpPath);
        }

        if (! file_exists($fullPath)) {
            Log::error('camp_media.edit_file_not_found', ['tmp_path' => $tmpPath]);
            return;
        }

        // Delete old Cloudinary asset before replacing
        if ($this->record->cloudinary_public_id) {
            try {
                app(CloudinaryService::class)->delete(
                    $this->record->cloudinary_public_id,
                    $this->record->media_type ?? 'image'
                );
            } catch (\Throwable $e) {
                Log::warning('cloudinary.delete_old_failed', ['error' => $e->getMessage()]);
            }
        }

        try {
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $fullPath,
                basename($tmpPath),
                mime_content_type($fullPath),
                null,
                true
            );

            $folder = 'ogun-congress/' . now()->year . '/media';
            $result = app(CloudinaryService::class)->upload($uploadedFile, $folder);

            $this->record->update([
                'cloudinary_url'       => $result['url'],
                'cloudinary_public_id' => $result['public_id'],
                'media_type'           => $result['resource_type'],
                'width'                => $result['width'],
                'height'               => $result['height'],
                'file_size'            => $result['bytes'],
                'thumbnail_url'        => $result['thumbnail_url'],
            ]);

            Notification::make()->title('File replaced on Cloudinary.')->success()->send();

        } catch (\Throwable $e) {
            Log::error('cloudinary.replace_failed', ['error' => $e->getMessage()]);
            Notification::make()
                ->title('Cloudinary upload failed: ' . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        } finally {
            if (file_exists($fullPath)) {
                $disk->delete($tmpPath);
            }
        }
    }
}
