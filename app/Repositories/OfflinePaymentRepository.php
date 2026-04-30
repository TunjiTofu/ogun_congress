<?php

namespace App\Repositories;

use App\Enums\OfflinePaymentStatus;
use App\Models\OfflinePayment;
use App\Repositories\Interfaces\OfflinePaymentRepositoryInterface;

class OfflinePaymentRepository extends BaseRepository implements OfflinePaymentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new OfflinePayment());
    }

    public function findPendingById(int $id): ?OfflinePayment
    {
        return OfflinePayment::where('id', $id)
            ->where('status', OfflinePaymentStatus::PENDING)
            ->first();
    }

    public function confirm(OfflinePayment $payment, int $confirmedByUserId): OfflinePayment
    {
        $payment->update([
            'status'       => OfflinePaymentStatus::CONFIRMED,
            'confirmed_by' => $confirmedByUserId,
            'confirmed_at' => now(),
        ]);

        return $payment->refresh();
    }

    public function reject(OfflinePayment $payment, int $rejectedByUserId, string $reason): OfflinePayment
    {
        $payment->update([
            'status'           => OfflinePaymentStatus::REJECTED,
            'confirmed_by'     => $rejectedByUserId,
            'confirmed_at'     => now(),
            'rejection_reason' => $reason,
        ]);

        return $payment->refresh();
    }
}
