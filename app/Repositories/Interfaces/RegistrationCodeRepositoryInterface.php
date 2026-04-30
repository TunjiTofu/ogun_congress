<?php

namespace App\Repositories\Interfaces;

use App\Enums\CodeStatus;
use App\Models\RegistrationCode;

interface RegistrationCodeRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCode(string $code): ?RegistrationCode;

    public function findByCodeOrFail(string $code): RegistrationCode;

    /**
     * Lock the code row for update (prevents race conditions during registration).
     */
    public function lockForUpdate(string $code): ?RegistrationCode;

    public function findByPaystackReference(string $reference): ?RegistrationCode;

    public function markAsActive(RegistrationCode $code, float $amount): RegistrationCode;

    public function markAsClaimed(RegistrationCode $code): RegistrationCode;

    public function isCodeUnique(string $code): bool;
}
