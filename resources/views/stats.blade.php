@php
    $goalCards = [
        ['label' => 'Monthly Goal (₱25k Target)', 'color' => 'text-blue-400',   'amount' => $actualMonthlySales ?? 0, 'pct' => $monthlyPercentage ?? 0, 'stroke' => '#f59e0b'],
        ['label' => 'Yearly Goal',                'color' => 'text-purple-400', 'amount' => null,                     'pct' => $yearlyPercentage ?? 0,  'stroke' => '#3b82f6'],
    ];
@endphp

<div id="dashboard" class="grid grid-cols-1 md:grid-cols-3 gap-5">
    <div class="adm-card">
        <p class="adm-label text-amber-400">YTD Sales Total</p>
        <h3 class="adm-big">₱{{ number_format($ytdSales ?? 0, 2) }}</h3>
        <p class="text-xs text-emerald-400 font-semibold mt-3"><i class="fa-solid fa-arrow-up"></i> Year-to-date revenue</p>
    </div>

    @foreach($goalCards as $g)
        <div class="adm-card flex items-center justify-between gap-4">
            <div>
                <p class="adm-label {{ $g['color'] }}">{{ $g['label'] }}</p>
                <h3 class="adm-big">{{ $g['amount'] !== null ? '₱' . number_format($g['amount'], 2) : round($g['pct']) . '%' }}</h3>
            </div>
            <svg class="adm-ring" viewBox="0 0 64 64" aria-hidden="true">
                <circle cx="32" cy="32" r="26" fill="none" stroke="rgba(148,163,184,.18)" stroke-width="5"/>
                <circle cx="32" cy="32" r="26" fill="none" stroke="{{ $g['stroke'] }}" stroke-width="5" stroke-linecap="round"
                        stroke-dasharray="{{ round($g['pct'] * 1.6336, 2) }} 163.36" transform="rotate(-90 32 32)"/>
                <text x="32" y="36" text-anchor="middle" fill="#fff" font-size="12" font-weight="700">{{ round($g['pct']) }}%</text>
            </svg>
        </div>
    @endforeach
</div>

<div class="adm-card">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-sm font-extrabold text-white">Monthly Sales Performance</h3>
            <p class="text-[11px] text-slate-400">Buwanang benta (Jan - Dec) sa kasalukuyang taon</p>
        </div>
        <span class="adm-chip">{{ $chartYear ?? now()->year }} (Actual)</span>
    </div>
    <div class="relative h-72">
        <canvas id="salesChart"></canvas>
    </div>
</div>