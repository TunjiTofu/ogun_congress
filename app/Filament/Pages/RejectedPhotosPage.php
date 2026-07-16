<?php

namespace App\Filament\Pages;

use App\Models\Camper;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class RejectedPhotosPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-x-circle';
    protected static ?string $navigationLabel = 'Rejected Photos';
    protected static ?string $navigationGroup = 'Campers';
    protected static ?int    $navigationSort  = 3;
    protected static string  $view            = 'filament.pages.rejected-photos';
    protected static ?string $title           = 'Rejected Photos — Awaiting Re-upload';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    // ── Page-level export action ───────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Export PDF Report')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->url(route('exports.rejected-photos'))
                ->openUrlInNewTab(),
        ];
    }

    // ── Stats for the view ────────────────────────────────────────────────────

    public function getViewData(): array
    {
        $base = $this->rejectedQuery();
        return [
            'totalRejected'    => (clone $base)->count(),
            'distinctChurches' => (clone $base)->distinct('church_id')->count('church_id'),
            'distinctDistricts'=> (clone $base)
                ->join('churches', 'campers.church_id', '=', 'churches.id')
                ->distinct('churches.district_id')
                ->count('churches.district_id'),
        ];
    }

    // ── Shared query: rejected photos with no re-upload ───────────────────────

    private function rejectedQuery()
    {
        // photo_status = 'rejected' means clearMediaCollection was called
        // and the coordinator has NOT yet re-uploaded (re-upload resets to 'pending').
        return Camper::query()
            ->where('photo_status', 'rejected')
            ->with(['church.district'])
            ->orderBy('church_id')
            ->orderBy('full_name');
    }

    // ── Table ─────────────────────────────────────────────────────────────────

    public function table(Table $table): Table
    {
        return $table
            ->query($this->rejectedQuery())
            ->heading('Campers with Rejected Photos — Coordinator Re-upload Pending')
            ->columns([
                Tables\Columns\TextColumn::make('church.district.name')
                    ->label('District')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('church.name')
                    ->label('Church')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Camper Name')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label()),

                Tables\Columns\TextColumn::make('photo_rejection_reason')
                    ->label('Rejection Reason')
                    ->wrap()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Rejected At')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->color('danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('church.district')
                    ->label('District')
                    ->relationship('church.district', 'name'),

                Tables\Filters\SelectFilter::make('church_id')
                    ->label('Church')
                    ->relationship('church', 'name')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('category')
                    ->options(\App\Enums\CamperCategory::class),
            ])
            ->defaultSort('church.district.name')
            ->striped()
            ->paginated([25, 50, 100, 'all']);
    }
}
