{{-- Add this to your Filament login view or use the extraLoginAttributes --}}
{{-- File: resources/views/filament/auth/login.blade.php --}}
<x-filament-panels::page.simple>
    @if(session('camp_over') || setting('camp_over', '0') === '1')
        <div style="background:#7F1D1D;border:1.5px solid #EF4444;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.25rem;text-align:center">
            <p style="font-size:1.2rem;margin-bottom:0.25rem">🔒</p>
            <p style="font-weight:800;color:#FEE2E2;font-size:0.95rem">Camp Is Over</p>
            <p style="color:#FCA5A5;font-size:0.82rem;margin-top:4px">
                Administrative access has been disabled.<br/>
                Only the super administrator can log in.
            </p>
        </div>
    @endif

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page.simple>
