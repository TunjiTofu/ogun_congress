<?php

namespace App\Services;

use App\Repositories\Interfaces\RegistrationCodeRepositoryInterface;
use Illuminate\Support\Str;

class CodeGenerationService
{
    /**
     * Characters used in the 6-character segment.
     * Ambiguous characters excluded: 0, O, I, 1
     */
    private const CHARSET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function __construct(
        private readonly RegistrationCodeRepositoryInterface $codeRepository,
    ) {}

    /**
     * Generate a unique registration code.
     *
     * Format: OGN-{YEAR}-{6 chars}
     * Example: OGN-2026-A3F7K2
     *
     * Loops until a unique code is found. Collision probability is
     * ~1 in 2 billion per attempt, so one iteration is virtually
     * always sufficient. The DB unique constraint is the final guard.
     */
    public function generate(): string
    {
        $year = now()->year;

        do {
            $segment = $this->randomSegment(6);
            $code    = "OGN-{$year}-{$segment}";
        } while (! $this->codeRepository->isCodeUnique($code));

        return $code;
    }

    private function randomSegment(int $length): string
    {
        $charset = self::CHARSET;
        $max     = strlen($charset) - 1;
        $result  = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= $charset[random_int(0, $max)];
        }

        return $result;
    }
}
