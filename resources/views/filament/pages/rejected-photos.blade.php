<x-filament-panels::page>
    {{-- Summary stats strip --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem">
        <div style="background:#FEF2F2;border:1px solid #FCA5A5;border-left:4px solid #DC2626;border-radius:10px;padding:1rem 1.25rem">
            <div style="font-size:0.65rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#991B1B;margin-bottom:0.35rem">Campers Awaiting Re-upload</div>
            <div style="font-size:2rem;font-weight:800;color:#DC2626;line-height:1">{{ $totalRejected }}</div>
        </div>
        <div style="background:#FFF7ED;border:1px solid #FDBA74;border-left:4px solid #F97316;border-radius:10px;padding:1rem 1.25rem">
            <div style="font-size:0.65rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#92400E;margin-bottom:0.35rem">Churches Affected</div>
            <div style="font-size:2rem;font-weight:800;color:#F97316;line-height:1">{{ $distinctChurches }}</div>
        </div>
        <div style="background:#EFF6FF;border:1px solid #93C5FD;border-left:4px solid #3B82F6;border-radius:10px;padding:1rem 1.25rem">
            <div style="font-size:0.65rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#1E40AF;margin-bottom:0.35rem">Districts Affected</div>
            <div style="font-size:2rem;font-weight:800;color:#3B82F6;line-height:1">{{ $distinctDistricts }}</div>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
