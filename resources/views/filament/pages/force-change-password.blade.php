<x-filament-panels::page>
    <div style="max-width:480px;margin:0 auto;padding:1rem">

        {{-- Warning banner --}}
        <div style="background:#7F1D1D;border:1.5px solid #EF4444;border-radius:12px;
                padding:1.25rem 1.5rem;margin-bottom:1.5rem;text-align:center">
            <p style="font-size:1.5rem;margin-bottom:0.35rem">🔐</p>
            <p style="font-weight:800;color:#FEE2E2;font-size:1rem;margin-bottom:0.25rem">
                Password Change Required
            </p>
            <p style="color:#FCA5A5;font-size:0.82rem;line-height:1.5">
                Your account was created with a temporary password.<br/>
                You must set a new password before you can continue.
            </p>
        </div>

        <form wire:submit.prevent="save">
            {{ $this->form }}
            <div style="margin-top:1.25rem">
                <button type="submit"
                        style="width:100%;padding:0.75rem;background:#0B2455;color:#fff;
                       font-size:0.9rem;font-weight:700;border-radius:100px;border:none;cursor:pointer">
                    Set New Password
                </button>
            </div>
        </form>
    </div>
    <x-filament-actions::modals />
</x-filament-panels::page>
