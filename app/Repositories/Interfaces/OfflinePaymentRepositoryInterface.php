<?php

namespace App\Repositories\Interfaces;

use App\Models\OfflinePayment;

interface OfflinePaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function findPendingById(int $id): ?OfflinePayment;

    public function confirm(OfflinePayment $payment, int $confirmedByUserId): OfflinePayment;

    public function reject(OfflinePayment $payment, int $rejectedByUserId, string $reason): OfflinePayment;
}
