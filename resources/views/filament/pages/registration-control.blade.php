<x-filament-panels::page>
    @php
        $isOpen    = setting('registration_open', '1') === '1';
        $closesAt  = setting('registration_closes_at');
        $autoClosed= $closesAt && now()->gt(\Illuminate\Support\Carbon::parse($closesAt));
        $effOpen   = $isOpen && ! $autoClosed;
        $campOver  = setting('camp_over', '0') === '1';
    @endphp

    {{-- Camp Over status --}}
    <div style="border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.25rem;
    background:{{ $campOver ? '#450A0A' : '#052E16' }};
    border:1.5px solid {{ $campOver ? '#EF4444' : '#22C55E' }}">
        <div style="display:flex;align-items:center;gap:0.75rem">
            <span style="font-size:1.4rem">{{ $campOver ? '🔒' : '✅' }}</span>
            <div>
                <p style="font-size:0.92rem;font-weight:800;color:{{ $campOver ? '#FCA5A5' : '#86EFAC' }}">
                    Camp is {{ $campOver ? 'OVER — All staff locked out' : 'Active — Staff can log in' }}
                </p>
                @if($campOver)
                    <p style="font-size:0.75rem;color:#FCA5A5;margin-top:2px;opacity:0.8">
                        All non-super_admin logins are currently disabled.
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Registration status --}}
    <div style="border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.5rem;
    background:{{ $effOpen ? '#052E16' : '#450A0A' }};
    border:1.5px solid {{ $effOpen ? '#22C55E' : '#EF4444' }}">
        <div style="display:flex;align-items:center;gap:0.75rem">
            <span style="font-size:1.4rem">{{ $effOpen ? '🟢' : '🔴' }}</span>
            <div>
                <p style="font-size:0.88rem;font-weight:700;color:{{ $effOpen ? '#86EFAC' : '#FCA5A5' }}">
                    Registration is currently {{ $effOpen ? 'OPEN' : 'CLOSED' }}
                </p>
                @if($autoClosed)
                    <p style="font-size:0.75rem;color:#FCA5A5;margin-top:2px">
                        Auto-closed on {{ \Illuminate\Support\Carbon::parse($closesAt)->format('d M Y, g:i A') }}
                    </p>
                @elseif($closesAt && $isOpen)
                    <p style="font-size:0.75rem;color:#86EFAC;margin-top:2px">
                        Will auto-close on {{ \Illuminate\Support\Carbon::parse($closesAt)->format('d M Y, g:i A') }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save">
        {{ $this->form }}
        <div style="margin-top:1.5rem">
            <button type="submit" style="background:#0B2455;color:#fff;font-size:0.9rem;font-weight:700;padding:0.75rem 1.5rem;border-radius:100px;border:none;cursor:pointer">
                Save Settings
            </button>
        </div>
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
