<?php

namespace App\Filament\Pages;

use App\Enums\CheckinEventType;
use App\Models\Camper;
use App\Models\CheckinEvent;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class CheckInConsole extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-qr-code';
    protected static ?string $navigationGroup = 'Camp Operations';
    protected static ?string $navigationLabel = 'Check-In Console';
    protected static ?int    $navigationSort  = 1;
    protected static string  $view            = 'filament.pages.check-in-console';

    // ── State ─────────────────────────────────────────────────────────────────

    public ?array  $camperData   = null;
    public ?string $scanResult   = null;
    public string  $scanStatus   = 'idle';  // idle | found | already_in | not_found
    public bool    $isScanning   = true;
    public string  $manualSearch = '';

    // ── Auth ──────────────────────────────────────────────────────────────────

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['secretariat', 'security']);
    }

    // ── Livewire: receives QR result from JS jsQR scan ────────────────────────

    #[On('qr-scanned')]
    public function onQrScanned(string $code): void
    {
        // Strip the "OGN:" prefix that the ID card QR encodes
        $camperNumber = str_starts_with($code, 'OGN:')
            ? substr($code, 4)
            : $code;

        $this->lookupCamper($camperNumber);
    }

    // ── Manual search ─────────────────────────────────────────────────────────

    public function searchManually(): void
    {
        if (blank($this->manualSearch)) {
            return;
        }

        $this->lookupCamper(trim($this->manualSearch));
    }

    // ── Core lookup logic ─────────────────────────────────────────────────────

    private function lookupCamper(string $camperNumber): void
    {
        $this->camperData = null;
        $this->scanResult = $camperNumber;

        $camper = Camper::with(['church.district', 'checkinEvents' => fn ($q) => $q->latest('scanned_at')->limit(1)])
            ->where('camper_number', $camperNumber)
            ->first();

        if (! $camper) {
            $this->scanStatus = 'not_found';
            return;
        }

        $alreadyCheckedIn = $camper->checkinEvents
            ->where('event_type', CheckinEventType::CHECK_IN)
            ->isNotEmpty();

        $this->scanStatus = $alreadyCheckedIn ? 'already_in' : 'found';

        $this->camperData = [
            'id'                => $camper->id,
            'camper_number'     => $camper->camper_number,
            'full_name'         => $camper->full_name,
            'category'          => $camper->category->label(),
            'badge_color'       => $camper->badge_color,
            'church'            => $camper->church?->name,
            'district'          => $camper->church?->district?->name,
            'photo_url'         => $camper->getFirstMediaUrl('photo', 'thumb'),
            'consent_collected' => $camper->consent_collected,
            'consent_required'  => $camper->requiresConsentForm(),
            'already_checked_in'=> $alreadyCheckedIn,
        ];
    }

    // ── Confirm check-in ──────────────────────────────────────────────────────

    public function confirmCheckIn(bool $consentCollected = false): void
    {
        if (! $this->camperData) {
            return;
        }

        $camper = Camper::find($this->camperData['id']);

        if (! $camper) {
            Notification::make()->title('Camper not found.')->danger()->send();
            return;
        }

        CheckinEvent::create([
            'uuid'              => (string) Str::uuid(),
            'camper_id'         => $camper->id,
            'event_type'        => CheckinEventType::CHECK_IN,
            'scanned_by'        => auth()->id(),
            'scanned_at'        => now(),
            'consent_collected' => $consentCollected,
        ]);

        // Update camper-level consent flag if collected here
        if ($consentCollected && ! $camper->consent_collected) {
            $camper->update(['consent_collected' => true]);
        }

        Notification::make()
            ->title("✓ {$camper->full_name} checked in successfully.")
            ->success()
            ->send();

        $this->resetScan();
    }

    public function confirmCheckOut(): void
    {
        if (! $this->camperData) {
            return;
        }

        CheckinEvent::create([
            'uuid'       => (string) Str::uuid(),
            'camper_id'  => $this->camperData['id'],
            'event_type' => CheckinEventType::CHECK_OUT,
            'scanned_by' => auth()->id(),
            'scanned_at' => now(),
        ]);

        Notification::make()
            ->title("{$this->camperData['full_name']} checked out.")
            ->warning()
            ->send();

        $this->resetScan();
    }

    public function resetScan(): void
    {
        $this->camperData   = null;
        $this->scanResult   = null;
        $this->scanStatus   = 'idle';
        $this->isScanning   = true;
        $this->manualSearch = '';
    }
}
