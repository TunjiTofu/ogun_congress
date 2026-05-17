<?php

namespace App\Filament\Resources;

use App\Enums\CamperCategory;
use App\Enums\Gender;
use App\Filament\Resources\CamperResource\Pages;
use App\Models\Camper;
use App\Models\Church;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class CamperResource extends Resource
{
    protected static ?string $model           = Camper::class;
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Campers';
    protected static ?string $navigationLabel = 'All Campers';
    protected static ?int    $navigationSort  = 1;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'secretariat', 'camp_director']);
    }

    public static function getEloquentQuery(): Builder
    {
        // Eager-load everything needed for the infolist view
        return parent::getEloquentQuery()
            ->with(['church.district', 'contacts', 'health', 'media', 'registrationCode']);
    }

    // ── Infolist ──────────────────────────────────────────────────────────────

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            // ── Identity ──────────────────────────────────────────────────
            Infolists\Components\Section::make('Identity')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('photo_display')
                        ->label('Passport Photo')
                        ->columnSpanFull()
                        ->getStateUsing(function (Camper $record): string {
                            $url = $record->getFirstMedia('photo')
                                ? route('camper.photo', $record->id) : null;
                            if (! $url) {
                                return '<div style="width:120px;height:150px;border-radius:10px;background:#F1F5F9;'
                                    . 'display:flex;align-items:center;justify-content:center;font-size:3rem;'
                                    . 'color:#94A3B8;border:2px solid #E2E8F0">👤</div>';
                            }
                            return '<img src="' . e($url) . '" style="width:120px;height:150px;'
                                . 'object-fit:cover;object-position:top center;border-radius:10px;'
                                . 'border:2px solid #E2E8F0" alt="Photo">';
                        })
                        ->html(),

                    Infolists\Components\TextEntry::make('full_name')->label('Full Name')->weight('bold'),
                    Infolists\Components\TextEntry::make('phone'),
                    Infolists\Components\TextEntry::make('camper_number')
                        ->label('Camper Number')->fontFamily('mono')->copyable(),
                    Infolists\Components\TextEntry::make('category')
                        ->label('Department')->badge()
                        ->formatStateUsing(fn ($state) => $state instanceof CamperCategory
                            ? $state->label() : $state),
                    Infolists\Components\TextEntry::make('photo_status')
                        ->label('Photo Status')->badge()
                        ->formatStateUsing(fn ($state) => ucfirst($state ?? 'pending'))
                        ->color(fn ($state) => match ($state) {
                            'approved' => 'success', 'rejected' => 'danger', default => 'warning',
                        }),
                    Infolists\Components\TextEntry::make('photo_rejection_reason')
                        ->label('Rejection Reason')->color('danger')->columnSpanFull()
                        ->visible(fn (Camper $record) => $record->photo_status === 'rejected'
                            && $record->photo_rejection_reason),
                ]),

            // ── Personal Details ──────────────────────────────────────────
            Infolists\Components\Section::make('Personal Details')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('gender')
                        ->formatStateUsing(fn ($state) => $state instanceof Gender
                            ? ucfirst($state->value) : ucfirst($state ?? '—')),
                    Infolists\Components\TextEntry::make('date_of_birth')->date('d M Y')->placeholder('—'),
                    Infolists\Components\TextEntry::make('club_rank')->label('Club Rank')->placeholder('—'),
                    Infolists\Components\TextEntry::make('ministry')->label('Ministry')->placeholder('—'),
                    Infolists\Components\TextEntry::make('home_address')->placeholder('—')->columnSpanFull(),
                ]),

            // ── Church ────────────────────────────────────────────────────
            Infolists\Components\Section::make('Church & Ministry')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('church.name')->label('Church'),
                    Infolists\Components\TextEntry::make('church.district.name')->label('District'),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Registered On')->dateTime('d M Y, g:i A'),
                ]),

            // ── Health & Medical — super_admin only ───────────────────────
            Infolists\Components\Section::make('Health & Medical')
                ->columns(2)
                ->visible(fn () => auth()->user()->hasRole('super_admin'))
                ->schema([
                    Infolists\Components\TextEntry::make('health.medical_conditions')
                        ->label('Medical Conditions')->placeholder('None recorded')->columnSpanFull(),
                    Infolists\Components\TextEntry::make('health.medications')
                        ->label('Current Medications')->placeholder('None')->columnSpanFull(),
                    Infolists\Components\TextEntry::make('health.allergies')
                        ->label('Allergies')->placeholder('None')->columnSpanFull(),
                    Infolists\Components\TextEntry::make('health.doctor_name')
                        ->label('Doctor Name')->placeholder('—'),
                    Infolists\Components\TextEntry::make('health.doctor_phone')
                        ->label('Doctor Phone')->placeholder('—'),
                ]),

            // ── Contacts ─────────────────────────────────────────────────
            Infolists\Components\Section::make('Parent / Guardian & Emergency Contacts')
                ->schema([
                    Infolists\Components\TextEntry::make('contacts_html')
                        ->label('')->columnSpanFull()
                        ->getStateUsing(function (Camper $record): string {
                            $contacts = $record->contacts;
                            if (! $contacts || $contacts->isEmpty()) {
                                return '<p style="color:#94A3B8;font-style:italic">No contacts recorded.</p>';
                            }
                            $html = '<div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem">';
                            foreach ($contacts as $c) {
                                $type  = is_string($c->type) ? $c->type : ($c->type?->value ?? '');
                                $isP   = $type === 'parent_guardian';
                                $label = $isP ? '👨‍👩‍👧 Parent / Guardian' : '🆘 Emergency Contact';
                                $bg    = $isP ? '#EEF2FF' : '#FEF2F2';
                                $bc    = $isP ? '#C7D2FE' : '#FCA5A5';
                                $tc    = $isP ? '#3730A3' : '#991B1B';
                                $html .= '<div style="border:1px solid '.$bc.';border-radius:10px;padding:0.85rem 1rem">';
                                $html .= '<span style="background:'.$bg.';color:'.$tc.';font-size:0.68rem;font-weight:700;'
                                    . 'padding:0.2rem 0.65rem;border-radius:100px;display:inline-block;margin-bottom:0.6rem">'
                                    . $label . '</span>';
                                $html .= '<div style="font-size:0.82rem;display:grid;gap:0.3rem">';
                                $html .= '<div><span style="color:#94A3B8;font-size:0.7rem">Name: </span><strong>' . e($c->full_name ?? '—') . '</strong></div>';
                                if ($c->relationship) $html .= '<div><span style="color:#94A3B8;font-size:0.7rem">Relationship: </span>' . e($c->relationship) . '</div>';
                                if ($c->phone) $html       .= '<div><span style="color:#94A3B8;font-size:0.7rem">Phone: </span>' . e($c->phone) . '</div>';
                                if ($c->email) $html       .= '<div><span style="color:#94A3B8;font-size:0.7rem">Email: </span>' . e($c->email) . '</div>';
                                $html .= '</div></div>';
                            }
                            $html .= '</div>';
                            return $html;
                        })
                        ->html(),
                ]),

            // ── Documents ─────────────────────────────────────────────────
            Infolists\Components\Section::make('Documents & Consent')
                ->columns(3)
                ->schema([
                    // Consent collected status
                    Infolists\Components\IconEntry::make('consent_collected')
                        ->label('Consent Collected')->boolean()
                        ->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle')
                        ->trueColor('success')->falseColor('danger'),

                    // ID Card — view + download (super_admin)
                    Infolists\Components\TextEntry::make('id_card_path')
                        ->label('ID Card')
                        ->getStateUsing(fn (Camper $r) => $r->id_card_path ? 'Generated ✓' : 'Not yet generated')
                        ->badge()
                        ->color(fn (Camper $r) => $r->id_card_path ? 'success' : 'warning')
                        ->visible(fn () => auth()->user()->hasRole('super_admin'))
                        ->suffixAction(
                            Infolists\Components\Actions\Action::make('download_id_card')
                                ->label('Download')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->url(fn (Camper $record) => $record->id_card_path
                                    ? route('documents.download', base64_encode($record->id_card_path))
                                    : null)
                                ->openUrlInNewTab()
                                ->visible(fn (Camper $record) => (bool) $record->id_card_path)
                        ),

                    // Consent Form — view + download
                    Infolists\Components\TextEntry::make('consent_form_path')
                        ->label('Consent Form')
                        ->getStateUsing(fn (Camper $r) => $r->consent_form_path ? 'Generated ✓' : 'Not generated')
                        ->badge()
                        ->color(fn (Camper $r) => $r->consent_form_path ? 'success' : 'gray')
                        ->visible(fn (Camper $record) => $record->requiresConsentForm())
                        ->suffixAction(
                            Infolists\Components\Actions\Action::make('download_consent')
                                ->label('Download')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->url(fn (Camper $record) => $record->consent_form_path
                                    ? route('documents.download', base64_encode($record->consent_form_path))
                                    : null)
                                ->openUrlInNewTab()
                                ->visible(fn (Camper $record) => (bool) $record->consent_form_path)
                        ),
                ]),

            // ── Registration ──────────────────────────────────────────────
            Infolists\Components\Section::make('Registration Details')
                ->columns(2)->collapsed()
                ->schema([
                    Infolists\Components\TextEntry::make('registrationCode.code')
                        ->label('Code')->fontFamily('mono')->copyable(),
                    Infolists\Components\TextEntry::make('registrationCode.payment_type')
                        ->label('Payment')
                        ->formatStateUsing(fn ($state) => match ($state?->value ?? $state) {
                            'online'  => 'Online (Paystack)',
                            'offline' => 'Bank Transfer',
                            default   => '—',
                        }),
                    Infolists\Components\TextEntry::make('registrationCode.claimed_at')
                        ->label('Registered At')->dateTime('g:i A, d M Y')->placeholder('—'),
                ]),
        ]);
    }

    // ── Form ──────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identity')
                ->columns(2)
                ->schema([
                    Forms\Components\Placeholder::make('photo_display')
                        ->label('Passport Photo')
                        ->columnSpanFull()
                        ->content(function (?Camper $record): HtmlString {
                            if (! $record?->id || ! $record->getFirstMedia('photo')) {
                                return new HtmlString('<div style="width:100px;height:125px;border-radius:10px;'
                                    . 'background:#F1F5F9;display:flex;align-items:center;justify-content:center;'
                                    . 'font-size:2.5rem;color:#94A3B8;border:2px solid #E2E8F0">👤</div>');
                            }
                            return new HtmlString('<img src="' . e(route('camper.photo', $record->id)) . '" '
                                . 'style="width:100px;height:125px;border-radius:10px;object-fit:cover;'
                                . 'object-position:top center;border:2px solid #E2E8F0">');
                        }),
                    Forms\Components\TextInput::make('full_name')->disabled()
                        ->helperText('Set from payment record.'),
                    Forms\Components\TextInput::make('phone')->disabled()
                        ->helperText('Set from payment record.'),
                    Forms\Components\TextInput::make('camper_number')->disabled(),
                    Forms\Components\Select::make('category')
                        ->options(collect(CamperCategory::cases())
                            ->mapWithKeys(fn ($e) => [$e->value => $e->label()])->toArray())
                        ->disabled(),
                ]),

            Forms\Components\Section::make('Personal Details')
                ->columns(2)
                ->schema([
                    Forms\Components\DatePicker::make('date_of_birth'),
                    Forms\Components\Select::make('gender')
                        ->options(collect(Gender::cases())
                            ->mapWithKeys(fn ($e) => [$e->value => $e->label()])->toArray()),
                    Forms\Components\Textarea::make('home_address')->rows(2)->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Church & Ministry')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('district_id')
                        ->label('District')
                        ->options(District::orderBy('name')->pluck('name', 'id'))
                        ->live()->afterStateUpdated(fn (Forms\Set $set) => $set('church_id', null))
                        ->dehydrated(false),
                    Forms\Components\Select::make('church_id')
                        ->label('Church')
                        ->options(fn (Get $get) => Church::where('district_id', $get('district_id'))
                            ->orderBy('name')->pluck('name', 'id'))
                        ->searchable(),
                    Forms\Components\TextInput::make('ministry')->maxLength(100),
                    Forms\Components\TextInput::make('club_rank')->label('Club Rank')->maxLength(100),
                ]),

            Forms\Components\Section::make('Consent')
                ->visibleOn('edit')
                ->schema([
                    Forms\Components\Toggle::make('consent_collected')
                        ->label('Consent Form Physically Collected')
                        ->onIcon('heroicon-o-document-check')
                        ->offIcon('heroicon-o-document'),
                ]),
        ]);
    }

    // ── Table ─────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        $isSuperAdmin = auth()->user()->hasRole('super_admin');

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->circular()
                    ->getStateUsing(fn (Camper $r) => $r->getFirstMedia('photo')
                        ? route('camper.photo', $r->id) : null),

                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()->sortable()->weight('bold'),

                Tables\Columns\TextColumn::make('camper_number')
                    ->label('Code')->searchable()->copyable()->fontFamily('mono'),

                Tables\Columns\BadgeColumn::make('category')
                    ->colors([
                        'info'    => CamperCategory::ADVENTURER->value,
                        'success' => CamperCategory::PATHFINDER->value,
                        'warning' => CamperCategory::SENIOR_YOUTH->value,
                    ])
                    ->formatStateUsing(fn ($state) => $state instanceof CamperCategory
                        ? $state->label() : $state),

                Tables\Columns\TextColumn::make('church.name')
                    ->label('Church')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('club_rank')
                    ->label('Rank')->placeholder('—')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('church.district.name')
                    ->label('District')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('photo_status')
                    ->label('Photo')
                    ->colors(['warning' => 'pending', 'success' => 'approved', 'danger' => 'rejected'])
                    ->visible($isSuperAdmin),

                Tables\Columns\IconColumn::make('consent_collected')
                    ->label('Consent')->boolean()
                    ->trueColor('success')->falseColor('danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(collect(CamperCategory::cases())
                        ->mapWithKeys(fn ($e) => [$e->value => $e->label()])->toArray()),

                Tables\Filters\SelectFilter::make('district')
                    ->label('District')
                    ->options(District::orderBy('name')->pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data) => $data['value']
                        ? $query->whereHas('church', fn ($q) => $q->where('district_id', $data['value']))
                        : $query),

                Tables\Filters\SelectFilter::make('church_id')
                    ->label('Church')
                    ->options(Church::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('photo_status')
                    ->label('Photo Status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                    ->visible($isSuperAdmin),

                Tables\Filters\TernaryFilter::make('consent_collected')
                    ->label('Consent')->trueLabel('Collected')->falseLabel('Outstanding')->nullable(),
            ])
            ->headerActions([
                // ── Export ID Cards PDF ───────────────────────────────────
                Tables\Actions\Action::make('export_id_cards')
                    ->label('Export ID Cards PDF')
                    ->icon('heroicon-o-identification')->color('primary')
                    ->visible($isSuperAdmin)
                    ->modalHeading('Export ID Cards PDF')
                    ->modalDescription('Choose filters below. Leave blank to export all campers.')
                    ->modalSubmitActionLabel('Generate & Download')
                    ->form([
                        Forms\Components\Select::make('district_id')
                            ->label('District (optional)')
                            ->options(District::orderBy('name')->pluck('name', 'id'))
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('church_id', null))
                            ->placeholder('All districts'),
                        Forms\Components\Select::make('church_id')
                            ->label('Local Church (optional)')
                            ->options(fn (Get $get) => $get('district_id')
                                ? Church::where('district_id', $get('district_id'))->orderBy('name')->pluck('name', 'id')
                                : Church::orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->placeholder('All churches'),
                        Forms\Components\Select::make('category')
                            ->label('Department (optional)')
                            ->options(collect(CamperCategory::cases())
                                ->mapWithKeys(fn ($e) => [$e->value => $e->label()])->toArray())
                            ->placeholder('All departments'),
                    ])
                    ->action(function (array $data) {
                        $url = route('exports.id-cards', array_filter($data));
                        redirect($url);
                    }),

                // ── Export Camper List PDF ────────────────────────────────
                Tables\Actions\Action::make('export_list')
                    ->label('Export List PDF')
                    ->icon('heroicon-o-document-text')->color('gray')
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'secretariat', 'camp_director']))
                    ->modalHeading('Export Camper List PDF')
                    ->modalDescription('Choose filters below. Leave blank to export all campers.')
                    ->modalSubmitActionLabel('Generate & Download')
                    ->form([
                        Forms\Components\Select::make('district_id')
                            ->label('District (optional)')
                            ->options(District::orderBy('name')->pluck('name', 'id'))
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('church_id', null))
                            ->placeholder('All districts'),
                        Forms\Components\Select::make('church_id')
                            ->label('Local Church (optional)')
                            ->options(fn (Get $get) => $get('district_id')
                                ? Church::where('district_id', $get('district_id'))->orderBy('name')->pluck('name', 'id')
                                : Church::orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->placeholder('All churches'),
                        Forms\Components\Select::make('category')
                            ->label('Department (optional)')
                            ->options(collect(CamperCategory::cases())
                                ->mapWithKeys(fn ($e) => [$e->value => $e->label()])->toArray())
                            ->placeholder('All departments'),
                    ])
                    ->action(function (array $data) {
                        $url = route('exports.campers', array_filter($data));
                        redirect($url);
                    }),

                // ── Regenerate All Documents ──────────────────────────────
                Tables\Actions\Action::make('regenerate_all')
                    ->label('Regenerate All ID Cards')
                    ->icon('heroicon-o-arrow-path')->color('warning')
                    ->visible($isSuperAdmin)
                    ->requiresConfirmation()
                    ->modalHeading('Regenerate All ID Cards & Consent Forms?')
                    ->modalDescription('Queues document generation for every camper. Requires queue worker running. If QUEUE_CONNECTION=sync in .env, run: php artisan queue:work')
                    ->modalSubmitActionLabel('Yes, Regenerate All')
                    ->action(function () {
                        $ids = \App\Models\Camper::pluck('id');
                        foreach ($ids as $id) {
                            \App\Jobs\GenerateCamperDocumentsJob::dispatch($id)->onQueue('documents');
                        }
                        Notification::make()
                            ->title("Queued {$ids->count()} campers for regeneration.")
                            ->success()->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_consent')
                    ->label('Mark Consent')
                    ->icon('heroicon-o-document-check')->color('success')
                    ->visible(fn (Camper $r) => auth()->user()->hasAnyRole(['super_admin', 'secretariat'])
                        && $r->requiresConsentForm() && ! $r->consent_collected)
                    ->requiresConfirmation()
                    ->action(fn (Camper $r) => $r->update(['consent_collected' => true])),

                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'secretariat'])),

                Tables\Actions\Action::make('regenerate')
                    ->label('Regenerate Docs')
                    ->icon('heroicon-o-arrow-path')->color('gray')
                    ->visible($isSuperAdmin)->requiresConfirmation()
                    ->action(function (Camper $r) {
                        \App\Jobs\GenerateCamperDocumentsJob::dispatch($r->id);
                        Notification::make()->title('Queued.')->success()->send();
                    }),

                Tables\Actions\Action::make('approve_photo')
                    ->label('Approve Photo')->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn (Camper $r) => $isSuperAdmin
                        && $r->getFirstMedia('photo') && $r->photo_status !== 'approved')
                    ->requiresConfirmation()
                    ->action(function (Camper $r) {
                        $r->update(['photo_status' => 'approved']);
                        \App\Jobs\GenerateCamperDocumentsJob::dispatch($r->id);
                        Notification::make()->title('Approved. ID card queued.')->success()->send();
                    }),

                Tables\Actions\Action::make('reject_photo')
                    ->label('Reject Photo')->icon('heroicon-o-x-circle')->color('danger')
                    ->visible(fn (Camper $r) => $isSuperAdmin
                        && $r->getFirstMedia('photo') && $r->photo_status !== 'rejected')
                    ->form([
                        Forms\Components\Textarea::make('reason')->label('Reason')->required()->rows(2),
                    ])
                    ->action(function (Camper $record, array $data) {
                        $record->clearMediaCollection('photo');
                        if ($record->id_card_path) {
                            \Illuminate\Support\Facades\Storage::disk('private')
                                ->delete($record->id_card_path);
                        }
                        $record->update([
                            'photo_status'           => 'rejected',
                            'photo_rejection_reason' => $data['reason'],
                            'id_card_path'           => null,
                        ]);
                        \App\Models\User::where('church_id', $record->church_id)
                            ->whereHas('roles', fn ($q) => $q->where('name', 'church_coordinator'))
                            ->each(fn ($u) => Notification::make()
                                ->title('Photo Rejected — ' . $record->full_name)
                                ->body($data['reason'])
                                ->danger()->sendToDatabase($u));
                        Notification::make()->title('Rejected. Coordinator notified.')->warning()->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Photos')->icon('heroicon-o-check-circle')->color('success')
                        ->visible($isSuperAdmin)
                        ->action(function ($records) {
                            $records->each(function ($r) {
                                $r->update(['photo_status' => 'approved']);
                                \App\Jobs\GenerateCamperDocumentsJob::dispatch($r->id);
                            });
                            Notification::make()->title('Photos approved.')->success()->send();
                        }),
                    Tables\Actions\BulkAction::make('bulk_regenerate')
                        ->label('Regenerate Documents')
                        ->icon('heroicon-o-arrow-path')
                        ->color('gray')
                        ->visible($isSuperAdmin)
                        ->requiresConfirmation()
                        ->modalDescription('This will queue document regeneration for all selected campers.')
                        ->action(function ($records) {
                            $records->each(fn ($r) => \App\Jobs\GenerateCamperDocumentsJob::dispatch($r->id));
                            Notification::make()
                                ->title($records->count() . ' campers queued for regeneration.')
                                ->success()->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make()->visible($isSuperAdmin),
                ]),
            ]);
    }

    public static function getRelationManagers(): array
    {
        return [
            \App\Filament\Resources\CamperResource\RelationManagers\ContactsRelationManager::class,
            \App\Filament\Resources\CamperResource\RelationManagers\CheckinEventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCampers::route('/'),
            'create' => Pages\CreateCamper::route('/create'),
            'edit'   => Pages\EditCamper::route('/{record}/edit'),
            'view'   => Pages\ViewCamper::route('/{record}'),
        ];
    }
}
