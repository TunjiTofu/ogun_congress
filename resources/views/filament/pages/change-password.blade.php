<x-filament-panels::page>
    <div style="max-width:480px;margin:0 auto;padding:1rem">
        <form wire:submit.prevent="save">
            {{ $this->form }}
            <div style="margin-top:1.25rem">
                <button type="submit"
                        style="padding:0.65rem 2rem;background:#0B2455;color:#fff;
                       font-size:0.88rem;font-weight:700;border-radius:100px;border:none;cursor:pointer">
                    Update Password
                </button>
            </div>
        </form>
    </div>
    <x-filament-actions::modals />
</x-filament-panels::page>
