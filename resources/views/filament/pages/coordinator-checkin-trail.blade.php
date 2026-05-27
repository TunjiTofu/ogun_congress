<x-filament-panels::page>

    {{-- Tab switcher --}}
    <div style="display:flex;gap:0.5rem;background:#F1F5F9;border-radius:10px;padding:4px;margin-bottom:1.25rem;width:fit-content">
        <button
            wire:click="setTab('checkin')"
            style="padding:0.5rem 1.1rem;border-radius:8px;font-size:0.82rem;font-weight:700;border:none;cursor:pointer;transition:all 0.15s;
        {{ $activeTab === 'checkin' ? 'background:#fff;color:#0B2455;box-shadow:0 1px 3px rgba(0,0,0,0.1)' : 'background:transparent;color:#64748B' }}">
            🚪 Check-In / Check-Out
        </button>
        <button
            wire:click="setTab('attendance')"
            style="padding:0.5rem 1.1rem;border-radius:8px;font-size:0.82rem;font-weight:700;border:none;cursor:pointer;transition:all 0.15s;
        {{ $activeTab === 'attendance' ? 'background:#fff;color:#0B2455;box-shadow:0 1px 3px rgba(0,0,0,0.1)' : 'background:transparent;color:#64748B' }}">
            📋 Programme Attendance
        </button>
    </div>

    {{-- Description for current tab --}}
    @if($activeTab === 'checkin')
        <p style="font-size:0.78rem;color:#64748B;margin-bottom:0.75rem">
            One row per camper. Click <strong>View Trail</strong> to see their full check-in and check-out history.
        </p>
    @else
        <p style="font-size:0.78rem;color:#64748B;margin-bottom:0.75rem">
            Programme attendance events, filterable by session.
        </p>
    @endif

    {{ $this->table }}

</x-filament-panels::page>
