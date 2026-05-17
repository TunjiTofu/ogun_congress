<?php

namespace App\Filament\Pages;

use App\Models\Camper;
use App\Models\Church;
use App\Models\District;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class CoordinatorCampersPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'My Campers';
    protected static ?string $navigationGroup = 'Campers';
    protected static ?int    $navigationSort  = 1;
    protected static string  $view            = 'filament.pages.coordinator-campers';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['church_coordinator', 'district_coordinator']);
    }

    public function getViewData(): array
    {
        $q = $this->scopedQuery();
        return [
            'totalCount'    => (clone $q)->count(),
            'approvedCount' => (clone $q)->where('photo_status', 'approved')->count(),
            'pendingCount'  => (clone $q)->where('photo_status', 'pending')->count(),
            'rejectedCount' => (clone $q)->where('photo_status', 'rejected')->count(),
        ];
    }

    protected function scopedQuery()
    {
        $user  = auth()->user();
        $query = Camper::query()->with(['church.district', 'contacts', 'health', 'media', 'registrationCode']);

        if ($user->hasRole('church_coordinator') && $user->church_id) {
            return $query->where('church_id', $user->church_id);
        }

        if ($user->hasRole('district_coordinator') && $user->district_id) {
            $churchIds = Church::where('district_id', $user->district_id)->pluck('id');
            return $query->whereIn('church_id', $churchIds);
        }

        return $query;
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->query($this->scopedQuery())
            ->heading(
                $user->hasRole('church_coordinator')
                    ? ($user->church?->name ?? 'My Church') . ' — Registered Campers'
                    : ($user->district?->name ?? 'My District') . ' — Registered Campers'
            )
            ->defaultSort('full_name')
            ->headerActions([
                Tables\Actions\Action::make('export_list')
                    ->label('Export Camper List PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->form([
                        \Filament\Forms\Components\Select::make('category')
                            ->label('Filter by Department (optional)')
                            ->options(collect(\App\Enums\CamperCategory::cases())
                                ->mapWithKeys(fn ($e) => [$e->value => $e->label()])->toArray())
                            ->placeholder('All departments'),
                    ])
                    ->action(function (array $data) {
                        $user   = auth()->user();
                        $params = array_filter([
                            'category'    => $data['category'] ?? null,
                            'church_id'   => $user->hasRole('church_coordinator') ? $user->church_id : null,
                            'district_id' => $user->hasRole('district_coordinator') ? $user->district_id : null,
                        ]);
                        return redirect()->away(route('exports.campers', $params));
                    })
                    ->modalSubmitActionLabel('Export PDF'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('photo_thumb')
                    ->label('Photo')
                    ->getStateUsing(fn ($record) => $record->getFirstMedia('photo')
                        ? route('camper.photo', $record->id) : null)
                    ->formatStateUsing(fn ($state): HtmlString => $state
                        ? new HtmlString('<img src="' . e($state) . '" style="width:44px;height:56px;object-fit:cover;object-position:top center;border-radius:6px;border:1px solid #E2E8F0">')
                        : new HtmlString('<div style="width:44px;height:56px;background:#F1F5F9;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#94A3B8;font-size:1.3rem;border:1px solid #E2E8F0">👤</div>'))
                    ->html(),

                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()->weight('bold')->sortable(),

                Tables\Columns\TextColumn::make('camper_number')
                    ->label('Code')->fontFamily('mono')->copyable()->searchable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()->formatStateUsing(fn ($state) => $state?->label())->sortable(),

                Tables\Columns\TextColumn::make('club_rank')
                    ->label('Rank')->placeholder('—'),

                Tables\Columns\TextColumn::make('church.name')
                    ->label('Church')->sortable()
                    ->visible(fn () => $user->hasRole('district_coordinator')),

                Tables\Columns\BadgeColumn::make('photo_status')
                    ->label('Photo')
                    ->colors(['warning' => 'pending', 'success' => 'approved', 'danger' => 'rejected'])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'approved' => '✅ Approved',
                        'rejected' => '❌ Rejected',
                        default    => '⏳ Pending',
                    }),

                Tables\Columns\IconColumn::make('consent_collected')
                    ->label('Consent')->boolean()->trueColor('success')->falseColor('warning'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(\App\Enums\CamperCategory::class),
                Tables\Filters\SelectFilter::make('photo_status')
                    ->label('Photo Status')
                    ->options(['pending' => '⏳ Pending', 'approved' => '✅ Approved', 'rejected' => '❌ Rejected']),
                Tables\Filters\TernaryFilter::make('consent_collected')->label('Consent'),
                Tables\Filters\SelectFilter::make('church_id')
                    ->label('Church')
                    ->options(fn () => $user->hasRole('district_coordinator') && $user->district_id
                        ? Church::where('district_id', $user->district_id)->orderBy('name')->pluck('name', 'id')
                        : [])
                    ->visible(fn () => $user->hasRole('district_coordinator')),
            ])
            ->actions([
                // ── Full details view ──────────────────────────────────────
                Tables\Actions\Action::make('view_details')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn ($record) => $record->full_name . ' — Details')
                    ->modalContent(fn (Camper $record) => $this->buildDetailHtml($record))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('2xl'),

                // ── Upload photo — church_coordinator ONLY ─────────────────
                Tables\Actions\Action::make('upload_photo')
                    ->label(fn ($record) => $record->photo_status === 'rejected' ? '📷 Replace Photo' : '📷 Upload Photo')
                    ->icon('heroicon-o-camera')
                    ->color(fn ($record) => $record->photo_status === 'rejected' ? 'danger' : 'warning')
                    ->visible(fn ($record) =>
                        auth()->user()->hasRole('church_coordinator')
                        && (! $record->getFirstMedia('photo') || $record->photo_status === 'rejected')
                    )
                    ->form(fn (Camper $record) => $this->buildUploadForm($record))
                    ->action(fn (Camper $record, array $data) => $this->handlePhotoUpload($record, $data)),

                // ── Download consent form ──────────────────────────────────
                Tables\Actions\Action::make('download_consent')
                    ->label('Consent Form')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->visible(fn ($record) => $record->requiresConsentForm() && $record->consent_form_path)
                    ->url(fn ($record) => route('documents.download', base64_encode($record->consent_form_path)))
                    ->openUrlInNewTab(),
            ])
            ->paginated([25, 50, 100]);
    }

    // ── Upload form ───────────────────────────────────────────────────────────

    private function buildUploadForm(Camper $record): array
    {
        $fields = [];

        if ($record->photo_status === 'rejected' && $record->photo_rejection_reason) {
            $fields[] = Placeholder::make('rejection_notice')
                ->label('')
                ->content(new HtmlString(
                    '<div style="background:#FEF2F2;border:1px solid #FCA5A5;border-radius:8px;padding:10px 14px;color:#991B1B;font-size:0.85rem">'
                    . '<strong>❌ Photo rejected:</strong> ' . e($record->photo_rejection_reason) . '</div>'
                ));
        }

        $fields[] = FileUpload::make('photo')
            ->label('Passport Photo')
            ->image()
            ->required()
            ->maxSize(5120)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->helperText('Clear passport-style photo with plain background. Max 5MB.');

        return $fields;
    }

    // ── Upload handler ────────────────────────────────────────────────────────

    public function handlePhotoUpload(Camper $record, array $data): void
    {
        $uploaded = $data['photo'] ?? null;

        Log::info('handlePhotoUpload called', [
            'camper_id' => $record->id,
            'has_value' => ! is_null($uploaded),
            'type'      => gettype($uploaded),
            'class'     => is_object($uploaded) ? get_class($uploaded) : 'n/a',
            'value'     => is_string($uploaded) ? $uploaded : 'object',
        ]);

        if (! $uploaded) {
            Notification::make()->title('No photo selected.')->danger()->send();
            return;
        }

        try {
            $raw = $this->readUploadedBytes($uploaded);

            if (! $raw || strlen($raw) === 0) {
                throw new \RuntimeException('File content is empty.');
            }

            // Convert to JPEG
            $jpeg = $this->toJpeg($raw);
            if (! $jpeg || strlen($jpeg) === 0) {
                $jpeg = $raw;
            }

            // Clear old photo and save new one
            $record->clearMediaCollection('photo');

            $record->addMediaFromString($jpeg)
                ->usingFileName('photo-' . $record->id . '-' . time() . '.jpg')
                ->toMediaCollection('photo', 'public');

            $record->update([
                'photo_status'           => 'pending',
                'photo_rejection_reason' => null,
                'id_card_path'           => null,
            ]);

            Log::info('Photo uploaded successfully', [
                'camper_id'   => $record->id,
                'media_count' => $record->fresh()->getMedia('photo')->count(),
            ]);

            Notification::make()
                ->title('Photo uploaded. Awaiting admin approval.')
                ->success()
                ->send();

        } catch (\Throwable $e) {
            Log::error('handlePhotoUpload failed', [
                'camper_id' => $record->id,
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);
            Notification::make()
                ->title('Upload failed: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function readUploadedBytes(mixed $uploaded): ?string
    {
        // Case 1: Object (Livewire TemporaryUploadedFile)
        if (is_object($uploaded)) {
            if (method_exists($uploaded, 'get')) {
                return $uploaded->get() ?: null;
            }
            if (method_exists($uploaded, 'getContent')) {
                return $uploaded->getContent() ?: null;
            }
            if (method_exists($uploaded, 'getRealPath')) {
                $path = $uploaded->getRealPath();
                if ($path && file_exists($path)) {
                    return file_get_contents($path) ?: null;
                }
            }
        }

        // Case 2: String path
        // Filament FileUpload moves the file from Livewire temp storage to the
        // configured disk BEFORE the action runs. The value is the filename on
        // that disk. Default Filament disk is 'public'.
        if (is_string($uploaded)) {
            $filename = $uploaded;

            // Try disks in order: public first (Filament default), then local (Livewire temp)
            $attempts = [
                ['disk' => 'public', 'path' => $filename],
                ['disk' => 'public', 'path' => 'livewire-tmp/' . $filename],
                ['disk' => 'local',  'path' => $filename],
                ['disk' => 'local',  'path' => 'livewire-tmp/' . $filename],
            ];

            foreach ($attempts as $attempt) {
                if (Storage::disk($attempt['disk'])->exists($attempt['path'])) {
                    $content = Storage::disk($attempt['disk'])->get($attempt['path']);
                    Log::info('Photo read successfully', [
                        'disk'  => $attempt['disk'],
                        'path'  => $attempt['path'],
                        'bytes' => strlen($content ?? ''),
                    ]);
                    return $content ?: null;
                }
            }

            Log::warning('Photo file not found on any disk', ['filename' => $filename]);
        }

        return null;
    }

    // ── Detail modal HTML ─────────────────────────────────────────────────────

    private function buildDetailHtml(Camper $record): HtmlString
    {
        $record->loadMissing(['church.district', 'contacts', 'health', 'registrationCode', 'media']);
        $photoUrl = $record->getFirstMedia('photo') ? route('camper.photo', $record->id) : null;

        $html = '<div style="font-family:inherit">';

        // Photo + identity header
        $html .= '<div style="display:flex;gap:1.25rem;align-items:flex-start;margin-bottom:1.25rem;padding-bottom:1.25rem;border-bottom:1px solid #F1F5F9">';
        $html .= $photoUrl
            ? '<img src="' . e($photoUrl) . '" style="width:90px;height:112px;object-fit:cover;object-position:top center;border-radius:10px;border:2px solid #E2E8F0;flex-shrink:0">'
            : '<div style="width:90px;height:112px;background:#F1F5F9;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:#CBD5E1;flex-shrink:0">👤</div>';
        $html .= '<div style="flex:1">';
        $html .= '<h2 style="font-size:1.1rem;font-weight:800;color:#0B2455;margin:0 0 0.25rem">' . e($record->full_name) . '</h2>';
        $html .= '<p style="font-family:monospace;font-size:0.78rem;color:#64748B;margin:0 0 0.4rem">' . e($record->camper_number) . '</p>';
        $html .= '<span style="background:#0B2455;color:#fff;font-size:0.65rem;font-weight:700;padding:0.2rem 0.7rem;border-radius:100px">' . e($record->category?->label() ?? '—') . '</span>';
        if ($record->club_rank) {
            $html .= ' <span style="background:#F1F5F9;color:#475569;font-size:0.65rem;font-weight:600;padding:0.2rem 0.7rem;border-radius:100px">' . e($record->club_rank) . '</span>';
        }
        if ($record->photo_status === 'rejected' && $record->photo_rejection_reason) {
            $html .= '<div style="margin-top:0.4rem;background:#FEF2F2;border:1px solid #FCA5A5;padding:0.35rem 0.6rem;border-radius:6px;font-size:0.72rem;color:#991B1B">❌ ' . e($record->photo_rejection_reason) . '</div>';
        }
        $html .= '</div></div>';

        $section = fn (string $title, string $body) =>
            '<div style="margin-bottom:1rem"><p style="font-size:0.6rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#94A3B8;margin:0 0 0.4rem">' . $title . '</p>' . $body . '</div>';

        $row = fn (string $lbl, string $val) =>
            '<div style="display:grid;grid-template-columns:130px 1fr;padding:0.35rem 0;border-bottom:1px solid #F8FAFC;font-size:0.82rem">'
            . '<span style="color:#94A3B8">' . $lbl . '</span>'
            . '<span style="font-weight:600;color:#1C2340">' . $val . '</span></div>';

        // Personal
        $personal  = $row('Gender', ucfirst($record->gender?->value ?? '—'));
        $personal .= $row('Date of Birth', $record->date_of_birth ? $record->date_of_birth->format('d M Y') : '—');
        $personal .= $row('Phone', e($record->phone ?? '—'));
        $personal .= $row('Address', e($record->home_address ?? '—'));
        $html .= $section('Personal Details', $personal);

        // Church
        $church  = $row('Church', e($record->church?->name ?? '—'));
        $church .= $row('District', e($record->church?->district?->name ?? '—'));
        $church .= $row('Ministry', e($record->ministry ?? '—'));
        $html .= $section('Church & Ministry', $church);

        // Registration
        $reg  = $row('Code', '<span style="font-family:monospace">' . e($record->registrationCode?->code ?? '—') . '</span>');
        $reg .= $row('Payment', match($record->registrationCode?->payment_type?->value ?? '') {
            'online'  => 'Online (Paystack)',
            'offline' => 'Bank Transfer',
            default   => '—',
        });
        $reg .= $row('Registered', $record->created_at->format('d M Y, g:i A'));
        $reg .= $row('Consent', $record->consent_collected ? '✅ Collected' : '⚠️ Not collected');
        $html .= $section('Registration', $reg);

        // Contacts
        $contacts = $record->contacts;
        if ($contacts->isNotEmpty()) {
            $cHtml = '<div style="display:grid;gap:0.65rem">';
            foreach ($contacts as $c) {
                $type  = is_string($c->type) ? $c->type : $c->type?->value;
                $isP   = $type === 'parent_guardian';
                $bg    = $isP ? '#EEF2FF' : '#FEF2F2';
                $bc    = $isP ? '#C7D2FE' : '#FCA5A5';
                $lbl   = $isP ? '👨‍👩‍👧 Parent / Guardian' : '🆘 Emergency Contact';
                $cHtml .= '<div style="border:1px solid ' . $bc . ';border-radius:8px;padding:0.65rem 0.85rem">';
                $cHtml .= '<span style="font-size:0.65rem;font-weight:700;background:' . $bg . ';color:' . ($isP ? '#3730A3' : '#991B1B') . ';padding:0.15rem 0.6rem;border-radius:100px;display:inline-block;margin-bottom:0.4rem">' . $lbl . '</span>';
                $cHtml .= '<div style="display:grid;grid-template-columns:1fr 1fr;gap:0.3rem 1rem;font-size:0.78rem">';
                $cHtml .= '<div><span style="color:#94A3B8;font-size:0.65rem;display:block">Name</span><strong>' . e($c->full_name) . '</strong></div>';
                if ($c->relationship) $cHtml .= '<div><span style="color:#94A3B8;font-size:0.65rem;display:block">Relationship</span>' . e($c->relationship) . '</div>';
                if ($c->phone) $cHtml .= '<div><span style="color:#94A3B8;font-size:0.65rem;display:block">Phone</span>' . e($c->phone) . '</div>';
                if ($c->email) $cHtml .= '<div><span style="color:#94A3B8;font-size:0.65rem;display:block">Email</span>' . e($c->email) . '</div>';
                $cHtml .= '</div></div>';
            }
            $cHtml .= '</div>';
            $html .= $section('Parent / Guardian & Emergency Contacts', $cHtml);
        }

        // Health
        $health = $record->health;
        if ($health) {
            $hHtml  = $row('Medical Conditions', e($health->medical_conditions ?? 'None'));
            $hHtml .= $row('Medications', e($health->medications ?? 'None'));
            $hHtml .= $row('Allergies', e($health->allergies ?? 'None'));
            $hHtml .= $row('Doctor Name', e($health->doctor_name ?? '—'));
            $hHtml .= $row('Doctor Phone', e($health->doctor_phone ?? '—'));
            $html .= $section('Health & Medical', $hHtml);
        }

        $html .= '</div>';
        return new HtmlString($html);
    }

    private function toJpeg(string $raw): string
    {
        if (! extension_loaded('gd')) return $raw;
        try {
            $src = @imagecreatefromstring($raw);
            if (! $src) return $raw;
            ob_start();
            imagejpeg($src, null, 90);
            $jpeg = ob_get_clean();
            imagedestroy($src);
            return $jpeg ?: $raw;
        } catch (\Throwable) {
            return $raw;
        }
    }
}
