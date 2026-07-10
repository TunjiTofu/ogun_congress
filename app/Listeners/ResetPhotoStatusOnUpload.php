<?php

namespace App\Listeners;

use App\Models\Camper;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class ResetPhotoStatusOnUpload
{
    /**
     * When a new photo is added to the 'photo' collection on a Camper,
     * reset the review status to 'pending' so the admin sees it again.
     *
     * This fires regardless of whether the photo was rejected before —
     * a re-upload by the coordinator always triggers a fresh review cycle.
     */
    public function handle(MediaHasBeenAddedEvent $event): void
    {
        $media = $event->media;

        // Only act on the 'photo' collection
        if ($media->collection_name !== 'photo') {
            return;
        }

        $model = $media->model;

        // Only act on Camper models that are not already pending
        if (! ($model instanceof Camper) || $model->photo_status === 'pending') {
            return;
        }

        $model->update([
            'photo_status'           => 'pending',
            'photo_rejection_reason' => null,
        ]);
    }
}
