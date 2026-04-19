@php $s = $this->getStatsData(); @endphp

<x-filament-widgets::widget>
    <style>
        @keyframes berandaFadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes berandaShine {
            0%   { transform: translateX(-120%) skewX(-20deg); }
            60%  { transform: translateX(220%) skewX(-20deg); }
            100% { transform: translateX(220%) skewX(-20deg); }
        }
        @keyframes berandaPulse {
            0%, 100% { box-shadow: 0 0 0 0 var(--b-pulse, rgba(14,165,233,0.35)); }
            50%      { box-shadow: 0 0 0 8px rgba(14,165,233,0); }
        }

        .beranda-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 0.875rem;
        }
        @media (min-width: 640px) {
            .beranda-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.125rem; }
        }

        .beranda-card {
            --b-c1: #0ea5e9;
            --b-c2: #0369a1;
            --b-tint: rgba(14,165,233,0.14);
            position: relative;
            background:
                radial-gradient(140% 80% at 100% 0%, var(--b-tint) 0%, transparent 55%),
                #ffffff;
            border-radius: 1.125rem;
            padding: 1.375rem 1.375rem 1.25rem;
            border: 1px solid #eef2f7;
            box-shadow: 0 1px 2px rgba(15,23,42,0.04);
            overflow: hidden;
            transition: transform 0.25s cubic-bezier(.2,.8,.2,1), box-shadow 0.25s ease, border-color 0.25s ease;
            animation: berandaFadeUp 0.55s cubic-bezier(.2,.8,.2,1) both;
        }
        .beranda-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 1px 1px, rgba(15,23,42,0.05) 1px, transparent 0);
            background-size: 14px 14px;
            opacity: 0.45;
            pointer-events: none;
            mask-image: linear-gradient(160deg, #000 0%, transparent 55%);
            -webkit-mask-image: linear-gradient(160deg, #000 0%, transparent 55%);
        }
        .beranda-card::after {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 40%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.55), transparent);
            transform: translateX(-120%) skewX(-20deg);
            pointer-events: none;
        }
        .beranda-card:hover::after { animation: berandaShine 1.2s ease forwards; }
        .beranda-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 34px -18px rgba(15,23,42,0.25);
            border-color: #e2e8f0;
        }
        .dark .beranda-card {
            background:
                radial-gradient(140% 80% at 100% 0%, var(--b-tint) 0%, transparent 55%),
                #26262b;
            border-color: #2f2f35;
            box-shadow: 0 1px 2px rgba(0,0,0,0.25);
        }
        .dark .beranda-card::before {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.05) 1px, transparent 0);
        }
        .dark .beranda-card:hover { border-color: #3a3a42; }

        .beranda-card--menara   { --b-c1:#38bdf8; --b-c2:#0369a1; --b-tint: rgba(14,165,233,0.14); --b-pulse: rgba(14,165,233,0.35); animation-delay: 0.00s; }
        .beranda-card--inspeksi { --b-c1:#34d399; --b-c2:#059669; --b-tint: rgba(16,185,129,0.14); --b-pulse: rgba(16,185,129,0.35); animation-delay: 0.07s; }
        .beranda-card--bulan    { --b-c1:#a78bfa; --b-c2:#6d28d9; --b-tint: rgba(139,92,246,0.14); --b-pulse: rgba(139,92,246,0.35); animation-delay: 0.14s; }

        .beranda-top {
            position: relative;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .beranda-icon {
            width: 3rem; height: 3rem;
            border-radius: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--b-c1), var(--b-c2));
            color: #ffffff;
            box-shadow: 0 12px 24px -10px var(--b-c2);
            flex-shrink: 0;
            position: relative;
        }
        .beranda-icon::after {
            content: "";
            position: absolute;
            inset: -4px;
            border-radius: inherit;
            background: linear-gradient(135deg, var(--b-c1), var(--b-c2));
            opacity: 0.25;
            filter: blur(10px);
            z-index: -1;
        }
        .beranda-icon svg {
            width: 1.5rem; height: 1.5rem;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .beranda-main { min-width: 0; flex: 1; }
        .beranda-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .dark .beranda-label { color: #a1a1aa; }
        .beranda-value {
            margin-top: 0.25rem;
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.03em;
            font-variant-numeric: tabular-nums;
            background: linear-gradient(135deg, var(--b-c2), var(--b-c1));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .beranda-foot {
            margin-top: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #64748b;
        }
        .dark .beranda-foot { color: #a1a1aa; }
        .beranda-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            background: var(--b-tint);
            color: var(--b-c2);
            font-weight: 700;
            font-size: 0.6875rem;
            font-variant-numeric: tabular-nums;
        }
        .dark .beranda-pill { color: var(--b-c1); }
        .beranda-pill svg {
            width: 0.75rem; height: 0.75rem;
            stroke: currentColor;
            stroke-width: 2.5;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .beranda-dot {
            width: 0.5rem; height: 0.5rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, var(--b-c1), var(--b-c2));
            animation: berandaPulse 2.2s ease-in-out infinite;
        }
    </style>

    <div class="beranda-grid">
        {{-- Total Menara --}}
        <div class="beranda-card beranda-card--menara">
            <div class="beranda-top">
                <div class="beranda-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 21h18"/>
                        <path d="M5 21V8l7-5 7 5v13"/>
                        <path d="M9 9h.01M15 9h.01M9 13h.01M15 13h.01M9 17h.01M15 17h.01"/>
                    </svg>
                </div>
                <div class="beranda-main">
                    <div class="beranda-label">Total Menara</div>
                    <div class="beranda-value">{{ number_format($s['total_menara']) }}</div>
                </div>
            </div>
            <div class="beranda-foot">
                <span>Jumlah menara terdaftar</span>
                <span class="beranda-dot"></span>
            </div>
        </div>

        {{-- Total Inspeksi --}}
        <div class="beranda-card beranda-card--inspeksi">
            <div class="beranda-top">
                <div class="beranda-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M9 4h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                        <path d="M9 4a2 2 0 012-2h2a2 2 0 012 2"/>
                        <path d="M9 14l2 2 4-4"/>
                    </svg>
                </div>
                <div class="beranda-main">
                    <div class="beranda-label">Total Inspeksi</div>
                    <div class="beranda-value">{{ number_format($s['total_inspeksi']) }}</div>
                </div>
            </div>
            <div class="beranda-foot">
                <span>Seluruh riwayat inspeksi</span>
                <span class="beranda-dot"></span>
            </div>
        </div>

        {{-- Inspeksi Bulan Ini --}}
        <div class="beranda-card beranda-card--bulan">
            <div class="beranda-top">
                <div class="beranda-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M16 3v4M8 3v4M3 10h18"/>
                        <path d="M9 15h2v2H9z" fill="currentColor" stroke="none"/>
                    </svg>
                </div>
                <div class="beranda-main">
                    <div class="beranda-label">Inspeksi Bulan Ini</div>
                    <div class="beranda-value">{{ number_format($s['inspeksi_bulan_ini']) }}</div>
                </div>
            </div>
            <div class="beranda-foot">
                <span>Bulan {{ $s['bulan_label'] }}</span>
                <span class="beranda-dot"></span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
