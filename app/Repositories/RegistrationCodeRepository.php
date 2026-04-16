<?php

namespace App\Repositories;

use App\Enums\CodeStatus;
use App\Models\RegistrationCode;
use App\Repositories\Interfaces\RegistrationCodeRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RegistrationCodeRepository extends BaseRepository implements RegistrationCodeRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new RegistrationCode());
    }

    public function findByCode(string $code): ?RegistrationCode
    {
        return RegistrationCode::where('code', $code)->first();
    }

    public function findByCodeOrFail(string $code): RegistrationCode
    {
        return RegistrationCode::where('code', $code)->firstOrFail();
    }

    /**
     * Pessimistic lock to prevent race conditions when two requests
     * try to claim the same code simultaneously.
     * Must be called inside a DB transaction.
     */
    public function lockForUpdate(string $code): ?RegistrationCode
    {
        return RegistrationCode::where('code', $code)
            ->lockForUpdate()
            ->first();
    }

    public function findByPaystackReference(string $reference): ?RegistrationCode
    {
        return RegistrationCode::where('paystack_reference', $reference)->first();
    }

    public function markAsActive(RegistrationCode $code, float $amount): RegistrationCode
    {
        $code->update([
            'status'       => CodeStatus::ACTIVE,
            'amount_paid'  => $amount,
            'activated_at' => now(),
            'expires_at'   => now()->addDays(
                (int) config('camp.code_expiry_days', 14)
            ),
        ]);

        return $code->refresh();
    }

    public function markAsClaimed(RegistrationCode $code): RegistrationCode
    {
        $code->update([
            'status'     => CodeStatus::CLAIMED,
            'claimed_at' => now(),
        ]);

        return $code->refresh();
    }

    public function isCodeUnique(string $code): bool
    {
        return ! RegistrationCode::where('code', $code)->exists();
    }
}
