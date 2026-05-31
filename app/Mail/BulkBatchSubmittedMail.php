<?php

namespace App\Mail;

use App\Models\BulkRegistrationBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BulkBatchSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BulkRegistrationBatch $batch,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Action Required] New Batch Payment Submitted — ' . $this->batch->church?->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bulk-batch-submitted',
            with: [
                'batch'    => $this->batch,
                'church'   => $this->batch->church,
                'district' => $this->batch->church?->district,
                'adminUrl' => url('/admin/offline-payments'),
                'total'    => number_format($this->batch->expected_total ?? 0, 2),
                'count'    => $this->batch->entries()->count(),
            ],
        );
    }
}
