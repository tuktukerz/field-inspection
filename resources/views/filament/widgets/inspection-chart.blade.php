@php
    $data = $this->getChartData();
    $segments = $data['segments'];
    $total = $data['total'];
    $topSegment = $segments[0] ?? null;

    // Donut geometry
    $radius = 70;
    $circumference = 2 * M_PI * $radius; // 439.82
    $gap = 2; // degrees between segments for visual separation

    // Precompute stroke-dasharray per segment
    $processed = [];
    foreach ($segments as $i => $seg) {
        $portion = ($seg['percent'] / 100) * $circumference;
        // Subtract a tiny gap from the drawn length (only if multiple segments)
        $segLen = count($segments) > 1
            ? max($portion - ($gap / 360) * $circumference, 0.1)
            : $portion;
        $gapLen = $circumference - $segLen;
        $offset = ($seg['start'] / 100) * $circumference;
        $processed[] = array_merge($seg, [
            'seg_len' => $segLen,
            'gap_len' => $gapLen,
            'offset' => -$offset, // negative for clockwise draw
            'index' => $i,
        ]);
    }
@endphp

<x-filament-widgets::widget>
    <style>
        @keyframes chartFadeIn {
            from { opacity: 0; transform: scale(0.9) rotate(-8deg); }
            to   { opacity: 1; transform: scale(1) rotate(0); }
        }
        @keyframes chartDrawSeg {
            from { stroke-dashoffset: calc(var(--seg-len) * -1); opacity: 0; }
            to   { stroke-dashoffset: var(--seg-offset); opacity: 1; }
        }
        @keyframes chartLegendIn {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes chartOrbit {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        @keyframes chartCenterFade {
            from { opacity: 0; transform: scale(0.8); }
            to   { opacity: 1; transform: scale(1); }
        }

        .chart-card {
            position: relative;
            background: #ffffff;
            border-radius: 1.25rem;
            padding: 1.5rem;
            border: 1px solid #eef2f7;
            box-shadow: 0 1px 2px rgba(15,23,42,0.04);
            overflow: hidden;
        }
        .dark .chart-card {
            background: #26262b;
            border-color: #2f2f35;
        }
        .chart-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 1px 1px, rgba(15,23,42,0.04) 1px, transparent 0);
            background-size: 16px 16px;
            mask-image: linear-gradient(160deg, #000 0%, transparent 60%);
            -webkit-mask-image: linear-gradient(160deg, #000 0%, transparent 60%);
            pointer-events: none;
        }
        .dark .chart-card::before {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.04) 1px, transparent 0);
        }

        .chart-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
            position: relative;
        }
        .chart-title-wrap { min-width: 0; }
        .chart-title {
            font-size: 1.0625rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.01em;
        }
        .dark .chart-title { color: #f4f4f5; }
        .chart-subtitle {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
        .dark .chart-subtitle { color: #a1a1aa; }
        .chart-count-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, rgba(14,165,233,0.12), rgba(3,105,161,0.12));
            color: #0369a1;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            border: 1px solid rgba(14,165,233,0.2);
            flex-shrink: 0;
        }
        .dark .chart-count-chip {
            background: linear-gradient(135deg, rgba(14,165,233,0.18), rgba(3,105,161,0.18));
            color: #7dd3fc;
            border-color: rgba(14,165,233,0.3);
        }
        .chart-count-chip svg {
            width: 0.875rem; height: 0.875rem;
            stroke: currentColor; stroke-width: 2.2;
            fill: none; stroke-linecap: round; stroke-linejoin: round;
        }

        .chart-top-stat {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.625rem 0.875rem;
            border-radius: 0.875rem;
            background: linear-gradient(135deg, rgba(56,189,248,0.08), rgba(3,105,161,0.04));
            border: 1px solid rgba(14,165,233,0.15);
            margin-bottom: 1.25rem;
        }
        .dark .chart-top-stat {
            background: linear-gradient(135deg, rgba(56,189,248,0.12), rgba(3,105,161,0.06));
            border-color: rgba(14,165,233,0.25);
        }
        .chart-top-icon {
            width: 2rem; height: 2rem;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #fbbf24, #d97706);
            display: inline-flex; align-items: center; justify-content: center;
            color: #ffffff; flex-shrink: 0;
            box-shadow: 0 6px 12px -6px rgba(251,191,36,0.6);
        }
        .chart-top-icon svg {
            width: 1rem; height: 1rem;
            stroke: currentColor; stroke-width: 2;
            fill: none; stroke-linecap: round; stroke-linejoin: round;
        }
        .chart-top-meta { min-width: 0; flex: 1; }
        .chart-top-label {
            font-size: 0.625rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .dark .chart-top-label { color: #a1a1aa; }
        .chart-top-value {
            font-size: 0.875rem;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dark .chart-top-value { color: #f4f4f5; }

        .chart-body {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            align-items: center;
        }
        @media (min-width: 640px) {
            .chart-body {
                grid-template-columns: auto 1fr;
                gap: 1.75rem;
            }
        }

        .donut-wrapper {
            position: relative;
            width: 15rem;
            height: 15rem;
            margin: 0 auto;
            animation: chartFadeIn 0.7s cubic-bezier(.2,.8,.2,1) both;
        }
        .donut-orbit {
            position: absolute;
            inset: -8px;
            border-radius: 9999px;
            border: 1.5px dashed rgba(148,163,184,0.35);
            animation: chartOrbit 40s linear infinite;
            pointer-events: none;
        }
        .dark .donut-orbit { border-color: rgba(113,113,122,0.35); }
        .donut-svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
            filter: drop-shadow(0 12px 24px rgba(15,23,42,0.15));
        }
        .dark .donut-svg {
            filter: drop-shadow(0 12px 24px rgba(0,0,0,0.4));
        }
        .donut-track {
            fill: none;
            stroke: rgba(148,163,184,0.14);
            stroke-width: 16;
        }
        .dark .donut-track { stroke: rgba(113,113,122,0.18); }
        .donut-seg {
            fill: none;
            stroke-width: 16;
            stroke-linecap: round;
            cursor: pointer;
            transition: stroke-width 0.2s ease, opacity 0.2s ease;
            animation: chartDrawSeg 0.9s cubic-bezier(.2,.8,.2,1) both;
        }
        .donut-wrapper:hover .donut-seg:not(:hover) { opacity: 0.42; }
        .donut-seg:hover { stroke-width: 20; }

        .donut-center {
            position: absolute;
            inset: 22%;
            border-radius: 9999px;
            background:
                radial-gradient(circle at 30% 30%, rgba(255,255,255,0.9), #ffffff 60%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow:
                inset 0 2px 6px rgba(15,23,42,0.06),
                0 4px 12px rgba(15,23,42,0.05);
            animation: chartCenterFade 0.6s 0.3s cubic-bezier(.2,.8,.2,1) both;
        }
        .dark .donut-center {
            background: radial-gradient(circle at 30% 30%, #2e2e34, #26262b 60%);
            box-shadow:
                inset 0 2px 6px rgba(0,0,0,0.25),
                0 4px 12px rgba(0,0,0,0.3);
        }
        .donut-total-label {
            font-size: 0.625rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }
        .dark .donut-total-label { color: #71717a; }
        .donut-total-value {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.04em;
            font-variant-numeric: tabular-nums;
            background: linear-gradient(135deg, #0369a1, #38bdf8);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-top: 0.25rem;
        }
        .donut-total-sub {
            font-size: 0.6875rem;
            color: #64748b;
            margin-top: 0.25rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        .dark .donut-total-sub { color: #a1a1aa; }

        .legend-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            max-height: 18rem;
            overflow-y: auto;
            padding-right: 0.25rem;
        }
        .legend-list::-webkit-scrollbar { width: 4px; }
        .legend-list::-webkit-scrollbar-track { background: transparent; }
        .legend-list::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 9999px; }
        .dark .legend-list::-webkit-scrollbar-thumb { background: #3a3a42; }

        .legend-item {
            position: relative;
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.875rem;
            border-radius: 0.75rem;
            background: #f8fafc;
            border: 1px solid #eef2f7;
            cursor: pointer;
            transition: transform 0.15s ease, background 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
            animation: chartLegendIn 0.4s cubic-bezier(.2,.8,.2,1) both;
            overflow: hidden;
        }
        .legend-item:hover {
            transform: translateX(2px);
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px -4px rgba(15,23,42,0.12);
        }
        .dark .legend-item {
            background: #2c2c31;
            border-color: #34343a;
        }
        .dark .legend-item:hover {
            background: #32323a;
            border-color: #4a4a54;
        }
        .legend-item::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: var(--pct, 0%);
            background: var(--bar-bg, rgba(14,165,233,0.08));
            transition: width 0.5s cubic-bezier(.2,.8,.2,1);
            border-radius: inherit;
            z-index: 0;
        }
        .legend-item > * { position: relative; z-index: 1; }

        .legend-rank {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, var(--rank-from), var(--rank-to));
            color: #ffffff;
            font-size: 0.6875rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 10px -4px var(--rank-to);
        }
        .legend-main { min-width: 0; }
        .legend-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dark .legend-label { color: #e4e4e7; }
        .legend-count-pill {
            font-size: 0.8125rem;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            color: #ffffff;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, var(--rank-from), var(--rank-to));
            box-shadow: 0 4px 10px -4px var(--rank-to);
            flex-shrink: 0;
            min-width: 2.25rem;
            text-align: center;
        }

        .chart-empty {
            padding: 3rem 1rem;
            text-align: center;
            color: #94a3b8;
            font-size: 0.875rem;
        }
        .dark .chart-empty { color: #71717a; }
    </style>

    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title-wrap">
                <div class="chart-title">Distribusi Menara Per Kecamatan</div>
                <div class="chart-subtitle">Sebaran data menara berdasarkan wilayah administratif</div>
            </div>
            <span class="chart-count-chip">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 21h18M5 21V10l7-5 7 5v11M9 21v-6h6v6"/></svg>
                {{ count($segments) }} Kecamatan
            </span>
        </div>

        @if ($total === 0)
            <div class="chart-empty">Belum ada data menara untuk ditampilkan.</div>
        @else
            <div class="chart-body">
                <div class="donut-wrapper">
                    <div class="donut-orbit"></div>
                    <svg class="donut-svg" viewBox="0 0 180 180">
                        <defs>
                            @foreach ($processed as $seg)
                                <linearGradient id="donut-grad-{{ $seg['index'] }}" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="{{ $seg['from'] }}"/>
                                    <stop offset="100%" stop-color="{{ $seg['to'] }}"/>
                                </linearGradient>
                            @endforeach
                        </defs>
                        <circle class="donut-track" cx="90" cy="90" r="{{ $radius }}"/>
                        @foreach ($processed as $seg)
                            <circle class="donut-seg"
                                    cx="90" cy="90" r="{{ $radius }}"
                                    stroke="url(#donut-grad-{{ $seg['index'] }})"
                                    stroke-dasharray="{{ $seg['seg_len'] }} {{ $seg['gap_len'] }}"
                                    stroke-dashoffset="{{ $seg['offset'] }}"
                                    style="--seg-len: {{ $seg['seg_len'] }}px; --seg-offset: {{ $seg['offset'] }}px; animation-delay: {{ 0.2 + $seg['index'] * 0.08 }}s;">
                                <title>{{ $seg['label'] }}: {{ number_format($seg['count']) }} menara</title>
                            </circle>
                        @endforeach
                    </svg>
                    <div class="donut-center">
                        <div class="donut-total-label">Total</div>
                        <div class="donut-total-value">{{ number_format($total) }}</div>
                        <div class="donut-total-sub">Menara</div>
                    </div>
                </div>

                <ul class="legend-list">
                    @foreach ($segments as $i => $seg)
                        <li class="legend-item"
                            style="animation-delay: {{ 0.1 + $i * 0.05 }}s;
                                   --pct: {{ $seg['percent'] }}%;
                                   --bar-bg: {{ $seg['solid'] }}14;
                                   --rank-from: {{ $seg['from'] }};
                                   --rank-to: {{ $seg['to'] }};">
                            <span class="legend-rank">{{ $i + 1 }}</span>
                            <div class="legend-main">
                                <div class="legend-label">{{ $seg['label'] }}</div>
                            </div>
                            <span class="legend-count-pill">{{ number_format($seg['count']) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
