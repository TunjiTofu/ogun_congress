<?php

namespace App\Filament\Resources\BulkRegistrationBatchResource\Pages;

use App\Filament\Resources\BulkRegistrationBatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListBulkBatches extends ListRecords
{
    protected static string $resource = BulkRegistrationBatchResource::class;

    protected function getHeaderActions(): array
    {
        // Check toggle + auto-close date, parsed in the app timezone (not UTC).
        $regClosed = setting('registration_open', '1') !== '1'
            || (setting('registration_closes_at')
                && now()->gt(\Illuminate\Support\Carbon::parse(
                    setting('registration_closes_at'),
                    'Africa/Lagos'
                )));
        $isClosedForCoordinator = auth()->user()->hasRole('church_coordinator') && $regClosed;

        return [
            Actions\CreateAction::make()
                ->label('New Bulk Registration')
                ->disabled($isClosedForCoordinator)
                ->tooltip($isClosedForCoordinator
                    ? 'Registration is currently closed. Contact the conference office for assistance.'
                    : null),
        ];
    }

    public function getTabs(): array
    {
        $user          = auth()->user();
        $isCoordinator = $user->hasRole('church_coordinator');

        $draftCount = \App\Models\BulkRegistrationBatch::where('status', 'draft')
            ->when($isCoordinator, fn ($query) => $query->where('created_by', $user->id))
            ->count();

        $pendingCount = \App\Models\BulkRegistrationBatch::where('status', 'pending_payment')
            ->when($isCoordinator, fn ($query) => $query->where('created_by', $user->id))
            ->count();

        $tabs = [];

        // Coordinators see Drafts first so they notice unsent batches immediately.
        // Accountants start on Pending Payment — they don't need to see drafts at all.
        if ($isCoordinator) {
            $tabs['draft'] = Tab::make('Drafts — Action Required')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'draft'))
                ->badge($draftCount ?: null)
                ->badgeColor('danger')
                ->icon('heroicon-o-exclamation-triangle');
        } else {
            $tabs['all'] = Tab::make('All');
        }

        $tabs['pending_payment'] = Tab::make('Pending Payment')
            ->modifyQueryUsing(fn ($query) => $query->where('status', 'pending_payment'))
            ->badge($pendingCount ?: null)
            ->badgeColor('warning');

        $tabs['confirmed'] = Tab::make('Confirmed')
            ->modifyQueryUsing(fn ($query) => $query->where('status', 'confirmed'));

        $tabs['rejected'] = Tab::make('Rejected')
            ->modifyQueryUsing(fn ($query) => $query->where('status', 'rejected'));

        if (! $isCoordinator) {
            $tabs['draft'] = Tab::make('Draft')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'draft'));
        }

        return $tabs;
    }

    public function getDefaultActiveTab(): string | int | null
    {
        if (auth()->user()->hasRole('church_coordinator')) {
            return 'draft';
        }
        return 'pending_payment';
    }
}

