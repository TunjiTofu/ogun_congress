<?php

namespace App\Observers;

use App\Models\OfflinePayment;
use App\Models\RegistrationCode;
use App\Models\BulkRegistrationBatch;
use Illuminate\Support\Facades\Auth;

/**
 * Logs key accountant + super-admin actions to the spatie activity_log table.
 * Registered in AppServiceProvider.
 */
class AdminActivityObserver
{
    // ── OfflinePayment ─────────────────────────────────────────────────────

    public function offlinePaymentCreated(OfflinePayment $payment): void
    {
        activity('offline_payments')
            ->performedOn($payment)
            ->causedBy(Auth::user())
            ->withProperties([
                'amount'   => $payment->amount,
                'name'     => $payment->payer_name,
                'phone'    => $payment->payer_phone,
                'category' => $payment->category,
            ])
            ->log('Offline payment created');
    }

    public function offlinePaymentUpdated(OfflinePayment $payment): void
    {
        $dirty = $payment->getDirty();
        if (empty($dirty)) {
            return;
        }

        $description = match (true) {
            array_key_exists('status', $dirty) && $dirty['status'] === 'confirmed' => 'Offline payment confirmed — code generated',
            array_key_exists('status', $dirty) && $dirty['status'] === 'rejected'  => 'Offline payment rejected',
            default => 'Offline payment updated',
        };

        activity('offline_payments')
            ->performedOn($payment)
            ->causedBy(Auth::user())
            ->withProperties(['changes' => $dirty])
            ->log($description);
    }

    // ── RegistrationCode ───────────────────────────────────────────────────

    public function registrationCodeUpdated(RegistrationCode $code): void
    {
        $dirty = $code->getDirty();
        if (empty($dirty) || ! array_key_exists('status', $dirty)) {
            return;
        }

        activity('registration_codes')
            ->performedOn($code)
            ->causedBy(Auth::user())
            ->withProperties([
                'code'       => $code->code,
                'old_status' => $code->getOriginal('status'),
                'new_status' => $dirty['status'],
            ])
            ->log('Registration code status changed to ' . $dirty['status']);
    }

    // ── BulkRegistrationBatch ──────────────────────────────────────────────

    public function bulkBatchUpdated(BulkRegistrationBatch $batch): void
    {
        $dirty = $batch->getDirty();
        if (empty($dirty) || ! array_key_exists('status', $dirty)) {
            return;
        }

        activity('bulk_batches')
            ->performedOn($batch)
            ->causedBy(Auth::user())
            ->withProperties([
                'batch_id'       => $batch->id,
                'church'         => $batch->church?->name,
                'old_status'     => $batch->getOriginal('status'),
                'new_status'     => $dirty['status'],
                'expected_total' => $batch->expected_total,
            ])
            ->log('Bulk batch status changed to ' . $dirty['status']);
    }
    // ── CampMedia ──────────────────────────────────────────────────────────

    public function campMediaCreated(\App\Models\CampMedia $media): void
    {
        activity('camp_media')
            ->performedOn($media)
            ->causedBy(Auth::user())
            ->withProperties(['title' => $media->title, 'district' => $media->district?->name])
            ->log('Camp media uploaded');
    }

    public function campMediaUpdated(\App\Models\CampMedia $media): void
    {
        $dirty = $media->getDirty();
        if (empty($dirty)) return;

        $description = match(true) {
            array_key_exists('status', $dirty) && $dirty['status'] === 'approved' => 'Camp media approved — now visible on album',
            array_key_exists('status', $dirty) && $dirty['status'] === 'rejected' => 'Camp media rejected',
            default => 'Camp media updated',
        };

        activity('camp_media')
            ->performedOn($media)
            ->causedBy(Auth::user())
            ->withProperties(['changes' => $dirty])
            ->log($description);
    }

    public function campMediaDeleted(\App\Models\CampMedia $media): void
    {
        activity('camp_media')
            ->causedBy(Auth::user())
            ->withProperties(['title' => $media->title, 'cloudinary_id' => $media->cloudinary_public_id])
            ->log('Camp media deleted');
    }

    // ── YoutubeHighlight ───────────────────────────────────────────────────

    public function youtubeHighlightCreated(\App\Models\YoutubeHighlight $yt): void
    {
        activity('youtube_highlights')
            ->performedOn($yt)
            ->causedBy(Auth::user())
            ->withProperties(['title' => $yt->title, 'youtube_id' => $yt->youtube_id])
            ->log('YouTube highlight added');
    }

    public function youtubeHighlightUpdated(\App\Models\YoutubeHighlight $yt): void
    {
        $dirty = $yt->getDirty();
        if (empty($dirty)) return;
        activity('youtube_highlights')
            ->performedOn($yt)
            ->causedBy(Auth::user())
            ->withProperties(['changes' => $dirty])
            ->log('YouTube highlight updated');
    }

    public function youtubeHighlightDeleted(\App\Models\YoutubeHighlight $yt): void
    {
        activity('youtube_highlights')
            ->causedBy(Auth::user())
            ->withProperties(['title' => $yt->title, 'youtube_id' => $yt->youtube_id])
            ->log('YouTube highlight deleted');
    }
}
