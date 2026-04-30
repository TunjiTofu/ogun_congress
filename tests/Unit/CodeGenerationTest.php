<?php

use App\Enums\CamperCategory;
use App\Enums\CodeStatus;
use App\Services\CodeGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ════════════════════════════════════════════════════════════════════════════
// CODE GENERATION
// ════════════════════════════════════════════════════════════════════════════

describe('CodeGenerationService', function () {

    it('generates a code in the correct format', function () {
        $service = app(CodeGenerationService::class);
        $code    = $service->generate();

        expect($code)->toMatch('/^OGN-\d{4}-[A-Z2-9]{6}$/');
    });

    it('excludes ambiguous characters 0, O, I, 1', function () {
        $service = app(CodeGenerationService::class);

        // Generate many codes and check none contain ambiguous chars
        $codes = collect(range(1, 200))->map(fn () => $service->generate());

        foreach ($codes as $code) {
            $segment = substr($code, 9); // Extract the 6-char segment
            expect($segment)->not->toContain('0')
                ->and($segment)->not->toContain('O')
                ->and($segment)->not->toContain('I')
                ->and($segment)->not->toContain('1');
        }
    });

    it('generates unique codes', function () {
        $service = app(CodeGenerationService::class);

        $codes = collect(range(1, 100))->map(fn () => $service->generate())->toArray();

        expect(count(array_unique($codes)))->toBe(100);
    });

    it('includes the current year in the code', function () {
        $service = app(CodeGenerationService::class);
        $code    = $service->generate();
        $year    = now()->year;

        expect($code)->toContain("OGN-{$year}-");
    });

});

// ════════════════════════════════════════════════════════════════════════════
// CODE STATUS USER MESSAGES
// ════════════════════════════════════════════════════════════════════════════

describe('CodeStatus enum messages', function () {

    it('returns a user-facing message for every non-ACTIVE status', function () {
        $statuses = [
            CodeStatus::PENDING,
            CodeStatus::CLAIMED,
            CodeStatus::EXPIRED,
            CodeStatus::VOID,
        ];

        foreach ($statuses as $status) {
            expect($status->userMessage())->toBeString()->not->toBeEmpty();
        }
    });

    it('CLAIMED message mentions secretariat', function () {
        expect(CodeStatus::CLAIMED->userMessage())->toContain('secretariat');
    });

    it('EXPIRED message mentions accountant', function () {
        expect(CodeStatus::EXPIRED->userMessage())->toContain('accountant');
    });

});

// ════════════════════════════════════════════════════════════════════════════
// CAMPER CATEGORY ENUM
// ════════════════════════════════════════════════════════════════════════════

describe('CamperCategory::fromAge()', function () {

    it('correctly maps all boundary ages', function () {
        expect(CamperCategory::fromAge(6))->toBe(CamperCategory::ADVENTURER);
        expect(CamperCategory::fromAge(9))->toBe(CamperCategory::ADVENTURER);
        expect(CamperCategory::fromAge(10))->toBe(CamperCategory::PATHFINDER);
        expect(CamperCategory::fromAge(15))->toBe(CamperCategory::PATHFINDER);
        expect(CamperCategory::fromAge(16))->toBe(CamperCategory::SENIOR_YOUTH);
        expect(CamperCategory::fromAge(35))->toBe(CamperCategory::SENIOR_YOUTH);
    });

    it('throws for ages below 6', function () {
        expect(fn () => CamperCategory::fromAge(5))->toThrow(\InvalidArgumentException::class);
        expect(fn () => CamperCategory::fromAge(0))->toThrow(\InvalidArgumentException::class);
    });

    it('requiresParentalConsent is true for adventurers and pathfinders only', function () {
        expect(CamperCategory::ADVENTURER->requiresParentalConsent())->toBeTrue();
        expect(CamperCategory::PATHFINDER->requiresParentalConsent())->toBeTrue();
        expect(CamperCategory::SENIOR_YOUTH->requiresParentalConsent())->toBeFalse();
    });

});
