<x-filament::page>

    {{-- Leaflet CSS & Plugins --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Custom CSS for Map Responsiveness, Layering, and Modal --}}
    <style>
        #map {
            height: 400px;
            z-index: 0 !important;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        @media (min-width: 768px) {
            #map {
                height: 650px;
            }
        }
        .leaflet-top, .leaflet-bottom {
            z-index: 400 !important;
        }

        /* Custom Marker */
        .custom-marker {
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            border: 2px solid white;
        }
        .marker-pin {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Legend */
        .map-legend {
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-size: 11px;
            font-family: 'Inter', sans-serif;
            border: 1px solid #eee;
        }
        .dark .map-legend {
            background: #111827;
            border-color: #374151;
            box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        }
        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            gap: 8px;
        }
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        /* Modal Styles */
        #imagePreviewModal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }
        #imagePreviewModal img {
            max-width: 90%;
            max-height: 90%;
            border-radius: 12px;
            box-shadow: 0 0 30px rgba(255,255,255,0.1);
        }
        #imagePreviewModal .close {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>

    {{-- Counter Cards --}}
    @php
        $stats = $this->getStats();
        $total = max($stats['total'], 1);
        $pctTerpantau = round($stats['terpantau'] / $total * 100);
        $pctPerlu = round($stats['perlu_pemeriksaan'] / $total * 100);
        $pctPrioritas = round($stats['prioritas_tinggi'] / $total * 100);
    @endphp
    <style>
        @keyframes statFadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes statShine {
            0%   { transform: translateX(-120%) skewX(-20deg); }
            60%  { transform: translateX(220%) skewX(-20deg); }
            100% { transform: translateX(220%) skewX(-20deg); }
        }
        @keyframes statPulse {
            0%, 100% { box-shadow: 0 0 0 0 var(--stat-pulse-color, rgba(14, 165, 233, 0.35)); }
            50%      { box-shadow: 0 0 0 8px rgba(14, 165, 233, 0); }
        }
        @keyframes statRingDraw {
            from { stroke-dasharray: 0 339.292; }
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.875rem;
            margin-bottom: 1.25rem;
        }
        @media (min-width: 768px) {
            .stat-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1.125rem; }
        }

        .stat-card {
            --stat-c1: #0ea5e9;
            --stat-c2: #0369a1;
            --stat-tint: rgba(14, 165, 233, 0.08);
            position: relative;
            background:
                radial-gradient(140% 80% at 100% 0%, var(--stat-tint) 0%, transparent 55%),
                #ffffff;
            border-radius: 1.125rem;
            padding: 1.25rem 1.3rem 1.1rem;
            border: 1px solid #eef2f7;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            overflow: hidden;
            transition: transform 0.25s cubic-bezier(.2,.8,.2,1), box-shadow 0.25s ease, border-color 0.25s ease;
            animation: statFadeUp 0.55s cubic-bezier(.2,.8,.2,1) both;
        }
        .stat-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 1px 1px, rgba(15,23,42,0.05) 1px, transparent 0);
            background-size: 14px 14px;
            opacity: 0.45;
            pointer-events: none;
            mask-image: linear-gradient(160deg, #000 0%, transparent 55%);
            -webkit-mask-image: linear-gradient(160deg, #000 0%, transparent 55%);
        }
        .stat-card::after {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 40%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.55), transparent);
            transform: translateX(-120%) skewX(-20deg);
            pointer-events: none;
        }
        .stat-card:hover::after {
            animation: statShine 1.2s ease forwards;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 34px -18px rgba(15, 23, 42, 0.25);
            border-color: #e2e8f0;
        }
        .dark .stat-card {
            background:
                radial-gradient(140% 80% at 100% 0%, var(--stat-tint) 0%, transparent 55%),
                #26262b;
            border-color: #2f2f35;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
        }
        .dark .stat-card::before {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.05) 1px, transparent 0);
        }
        .dark .stat-card:hover { border-color: #3a3a42; }

        .stat-card--total     { --stat-c1:#38bdf8; --stat-c2:#0369a1; --stat-tint: rgba(14,165,233,0.14); --stat-pulse-color: rgba(14,165,233,0.35); animation-delay: 0.00s; }
        .stat-card--ok        { --stat-c1:#34d399; --stat-c2:#059669; --stat-tint: rgba(16,185,129,0.14); --stat-pulse-color: rgba(16,185,129,0.35); animation-delay: 0.07s; }
        .stat-card--warn      { --stat-c1:#fde047; --stat-c2:#ca8a04; --stat-tint: rgba(250,204,21,0.16); --stat-pulse-color: rgba(250,204,21,0.4);  animation-delay: 0.14s; }
        .stat-card--danger    { --stat-c1:#f87171; --stat-c2:#b91c1c; --stat-tint: rgba(239,68,68,0.14);  --stat-pulse-color: rgba(239,68,68,0.4);   animation-delay: 0.21s; }

        .stat-top {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.875rem;
        }

        .stat-ring {
            position: relative;
            width: 3.5rem;
            height: 3.5rem;
            flex-shrink: 0;
        }
        .stat-ring svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }
        .stat-ring .track {
            fill: none;
            stroke: currentColor;
            stroke-width: 8;
            opacity: 0.12;
        }
        .stat-ring .progress {
            fill: none;
            stroke: url(--stat-grad);
            stroke-width: 8;
            stroke-linecap: round;
            stroke-dasharray: 339.292;
            stroke-dashoffset: 0;
            animation: statRingDraw 0.9s cubic-bezier(.2,.8,.2,1) both;
        }
        .stat-ring-inner {
            position: absolute; inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--stat-c2);
        }
        .stat-ring-inner svg {
            width: 1.35rem;
            height: 1.35rem;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            transform: none;
        }

        .stat-icon-badge {
            width: 2.75rem; height: 2.75rem;
            border-radius: 0.875rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--stat-c1), var(--stat-c2));
            color: #ffffff;
            box-shadow: 0 10px 20px -8px var(--stat-c2);
            flex-shrink: 0;
        }
        .stat-icon-badge svg {
            width: 1.375rem; height: 1.375rem;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .stat-main { min-width: 0; flex: 1; }
        .stat-label {
            font-size: 0.6875rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .dark .stat-label { color: #a1a1aa; }
        .stat-value {
            margin-top: 0.125rem;
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.03em;
            font-variant-numeric: tabular-nums;
            background: linear-gradient(135deg, var(--stat-c2), var(--stat-c1));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-foot {
            margin-top: 0.875rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.6875rem;
            font-weight: 600;
            color: #64748b;
        }
        .dark .stat-foot { color: #a1a1aa; }
        .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.1875rem 0.5rem;
            border-radius: 9999px;
            background: var(--stat-tint);
            color: var(--stat-c2);
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }
        .dark .stat-pill { color: var(--stat-c1); }
        .stat-dot {
            width: 0.375rem; height: 0.375rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, var(--stat-c1), var(--stat-c2));
            animation: statPulse 2.2s ease-in-out infinite;
        }
    </style>

    <div class="stat-grid">
        {{-- Total Menara --}}
        <div class="stat-card stat-card--total">
            <div class="stat-top">
                <div class="stat-icon-badge">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 21V9l7-6 7 6v12"/><path d="M9 21v-6h6v6"/>
                    </svg>
                </div>
                <div class="stat-main">
                    <div class="stat-label">Total Menara</div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                </div>
            </div>
            <div class="stat-foot">
                <span>Seluruh menara terdaftar</span>
                <span class="stat-dot"></span>
            </div>
        </div>

        {{-- Terpantau --}}
        <div class="stat-card stat-card--ok">
            <div class="stat-top">
                <div class="stat-ring" style="color: #10b981;">
                    <svg viewBox="0 0 120 120">
                        <defs>
                            <linearGradient id="grad-ok" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#34d399"/><stop offset="100%" stop-color="#059669"/>
                            </linearGradient>
                        </defs>
                        <circle class="track" cx="60" cy="60" r="54"/>
                        <circle cx="60" cy="60" r="54"
                                fill="none" stroke="url(#grad-ok)" stroke-width="8" stroke-linecap="round"
                                stroke-dasharray="{{ 339.292 * ($pctTerpantau/100) }} 339.292"/>
                    </svg>
                    <div class="stat-ring-inner">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
                    </div>
                </div>
                <div class="stat-main">
                    <div class="stat-label">Terpantau</div>
                    <div class="stat-value">{{ $stats['terpantau'] }}</div>
                </div>
            </div>
            <div class="stat-foot">
                <span>&lt; 30 hari</span>
                <span class="stat-pill">{{ $pctTerpantau }}%</span>
            </div>
        </div>

        {{-- Perlu Pemeriksaan --}}
        <div class="stat-card stat-card--warn">
            <div class="stat-top">
                <div class="stat-ring" style="color: #ca8a04;">
                    <svg viewBox="0 0 120 120">
                        <defs>
                            <linearGradient id="grad-warn" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#fde047"/><stop offset="100%" stop-color="#ca8a04"/>
                            </linearGradient>
                        </defs>
                        <circle class="track" cx="60" cy="60" r="54"/>
                        <circle cx="60" cy="60" r="54"
                                fill="none" stroke="url(#grad-warn)" stroke-width="8" stroke-linecap="round"
                                stroke-dasharray="{{ 339.292 * ($pctPerlu/100) }} 339.292"/>
                    </svg>
                    <div class="stat-ring-inner">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    </div>
                </div>
                <div class="stat-main">
                    <div class="stat-label">Perlu Pemeriksaan</div>
                    <div class="stat-value">{{ $stats['perlu_pemeriksaan'] }}</div>
                </div>
            </div>
            <div class="stat-foot">
                <span>31 - 90 hari</span>
                <span class="stat-pill">{{ $pctPerlu }}%</span>
            </div>
        </div>

        {{-- Prioritas Tinggi --}}
        <div class="stat-card stat-card--danger">
            <div class="stat-top">
                <div class="stat-ring" style="color: #ef4444;">
                    <svg viewBox="0 0 120 120">
                        <defs>
                            <linearGradient id="grad-danger" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#f87171"/><stop offset="100%" stop-color="#b91c1c"/>
                            </linearGradient>
                        </defs>
                        <circle class="track" cx="60" cy="60" r="54"/>
                        <circle cx="60" cy="60" r="54"
                                fill="none" stroke="url(#grad-danger)" stroke-width="8" stroke-linecap="round"
                                stroke-dasharray="{{ 339.292 * ($pctPrioritas/100) }} 339.292"/>
                    </svg>
                    <div class="stat-ring-inner">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><path d="M12 9v4M12 17h.01"/></svg>
                    </div>
                </div>
                <div class="stat-main">
                    <div class="stat-label">Prioritas Tinggi</div>
                    <div class="stat-value">{{ $stats['prioritas_tinggi'] }}</div>
                </div>
            </div>
            <div class="stat-foot">
                <span>&gt; 90 hari</span>
                <span class="stat-pill">{{ $pctPrioritas }}%</span>
            </div>
        </div>
    </div>

    {{-- Map Container --}}
    <div class="relative w-full">
        <div id="map"></div>
        
        {{-- Floating Legend --}}
        <div class="map-legend absolute bottom-5 right-5 z-[500] hidden md:block">
            <h4 class="font-bold mb-2 text-gray-700 dark:text-gray-200">Status Inspeksi</h4>
            <div class="legend-item">
                <div class="legend-color" style="background: #10b981;"></div>
                <span class="text-gray-600 dark:text-gray-400">Terpantau (&lt; 30 hari)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #facc15;"></div>
                <span class="text-gray-600 dark:text-gray-400">Perlu Pemeriksaan (31 - 90 hari)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #ef4444;"></div>
                <span class="text-gray-600 dark:text-gray-400">Prioritas Tinggi (&gt; 90 hari)</span>
            </div>
        </div>
    </div>

    {{-- Modal Preview --}}
    <div id="imagePreviewModal" onclick="this.style.display='none'">
        <span class="close">&times;</span>
        <img id="previewImg" src="">
    </div>

    {{-- Data Injection --}}
    @php
    $mapData = $this->getMapData();
    @endphp

    <script>
        window.mapData = {!! json_encode($mapData) !!};

        function openPreview(src) {
            const modal = document.getElementById('imagePreviewModal');
            const img = document.getElementById('previewImg');
            img.src = src;
            modal.style.display = 'flex';
        }
    </script>

    {{-- Script Dependencies --}}
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const data = window.mapData || [];
            
            // Map Styling Constants
            const colors = {
                green: '#10b981',
                yellow: '#facc15',
                red: '#ef4444'
            };

            // INIT MAP
            const map = L.map('map', {
                zoomControl: false 
            }).setView([-6.2, 106.8], 11);

            L.control.zoom({ position: 'topright' }).addTo(map);

            // PREMIUM TILES (CartoDB Voyager)
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            const markers = L.markerClusterGroup({
                showCoverageOnHover: false,
                iconCreateFunction: function(cluster) {
                    const count = cluster.getChildCount();
                    return L.divIcon({
                        html: `<div style="background:#1e40af; width:35px; height:35px; border-radius:50%; border:3px solid white; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; box-shadow:0 2px 10px rgba(0,0,0,0.2);">${count}</div>`,
                        className: 'cluster-icon',
                        iconSize: L.point(35, 35)
                    });
                }
            });

            const bounds = [];

            data.forEach(function (item) {
                if (!item.latitude || !item.longitude) return;

                const lat = Number(item.latitude);
                const lng = Number(item.longitude);
                if (isNaN(lat) || isNaN(lng)) return;

                // Status Color based on Data
                const color = colors[item.status_color] || colors.red;

                // CUSTOM ICON (SVG Tower)
                const towerIcon = L.divIcon({
                    className: 'custom-marker',
                    html: `
                        <div class="marker-pin" style="background: ${color}; color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    `,
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                    popupAnchor: [0, -15]
                });

                const marker = L.marker([lat, lng], { icon: towerIcon });
                bounds.push([lat, lng]);

                // PHOTO SECTION (MODERN)
                let imageHtml = '';
                if (item.latest_fotos && item.latest_fotos.length > 0) {
                    imageHtml = '<div style="margin-top:10px; display:flex; flex-wrap:nowrap; overflow-x:auto; gap:8px; padding-bottom:5px; scrollbar-width: thin; -webkit-overflow-scrolling: touch;">';
                    item.latest_fotos.forEach(function(foto) {
                        imageHtml += `
                            <img
                                src="${foto}"
                                style="width:85px; height:85px; object-fit:cover; border-radius:8px; cursor:zoom-in; border:2px solid ${color}; flex-shrink:0;"
                                onclick="openPreview(this.src)"
                            />
                        `;
                    });
                    imageHtml += '</div>';
                }

                // HISTORY SECTION
                let historyHtml = '';
                if (item.history && item.history.length > 0) {
                    historyHtml = `
                        <h4 style="margin: 15px 0 8px 0; color: ${color}; font-size: 13px; font-weight:700; border-bottom: 2px solid #f3f4f6; padding-bottom: 4px;">Riwayat Patroli Terakhir</h4>
                        <div style="background:#f9fafb; border-radius:8px; padding:2px; border:1px solid #f3f4f6;">
                            <table style="width: 100%; font-size: 10px; border-collapse: collapse;">
                                <tbody>
                    `;
                    item.history.forEach(function(visit) {
                        historyHtml += `
                            <tr>
                                <td style="padding: 6px; border-bottom: 1px solid #fff; color:#4b5563;">${visit.tanggal}</td>
                                <td style="padding: 6px; border-bottom: 1px solid #fff; color:#111827; font-weight:600; text-align:right;">${visit.visitor}</td>
                            </tr>
                        `;
                    });
                    historyHtml += '</tbody></table></div>';
                }

                const popupContent = `
                    <div style="min-width:300px; font-family: 'Inter', sans-serif; padding:10px 5px;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px; background: ${color}; padding: 12px; border-radius: 12px; color: white; box-shadow: 0 4px 15px ${color}44;">
                            <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 10px; backdrop-filter: blur(4px);">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px; color: white;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h3 style="margin: 0; font-size: 14px; font-weight: 800; text-transform:uppercase;">${item.tower_id ?? '-'}</h3>
                                <div style="font-size: 11px; opacity: 0.9; font-weight: 500;">${item.kecamatan} • ${item.kelurahan}</div>
                            </div>
                        </div>

                        <div style="margin-bottom: 12px;">
                            <div style="background:#f0f7ff; padding:12px; border-radius:12px; border:1px solid #dbeafe; position:relative;">
                                <div style="display:flex; align-items:center; gap:6px; margin-bottom:6px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px; height:14px; color:#2563eb;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span style="font-size:10px; color:#1e40af; font-weight:800; text-transform:uppercase; letter-spacing:0.05em;">Lokasi & Titik Koordinat</span>
                                </div>
                                <div style="font-size:13px; font-weight:700; color:#111827; line-height:1.2; margin-bottom:2px;">${item.lokasi ?? '-'}</div>
                                <div style="font-size:11px; color:#4b5563; line-height:1.4; margin-bottom:8px;">${item.detail ?? '-'}</div>
                                
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-top:8px; padding-top:8px; border-top:1px dashed #bfdbfe;">
                                    <div style="font-size:10px; font-family:'Monaco', monospace; color:#2563eb; font-weight:600;">
                                        ${Number(item.latitude).toFixed(7)}, ${Number(item.longitude).toFixed(7)}
                                    </div>
                                    <a href="https://www.google.com/maps/search/?api=1&query=${item.latitude},${item.longitude}" 
                                       target="_blank" 
                                       onclick="if(window.innerWidth < 768) { map.closePopup(); }"
                                       style="display:flex; align-items:center; gap:4px; font-size:9px; color:#1e40af; text-decoration:none; background:white; padding:3px 8px; border-radius:20px; border:1px solid #bfdbfe; font-weight:700; transition: all 0.2s;">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:10px; height:10px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.447-.894L15 4m0 13V4m0 0L9 7" />
                                        </svg>
                                        Maps
                                    </a>
                                </div>
                            </div>
                        </div>

                        ${historyHtml}
                        ${imageHtml}
                        
                        <div style="margin-top: 15px; display:flex; gap:8px;">
                            <a href="/admin/towers/${item.id}" style="flex:1; text-align:center; padding:10px; background: ${color}; color: white; text-decoration: none; border-radius: 10px; font-size: 11px; font-weight: 700; box-shadow: 0 4px 10px ${color}33;">Buka Detail Unit</a>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent, { maxWidth: 350 });
                markers.addLayer(marker);
            });

            map.addLayer(markers);

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        });
    </script>
</x-filament::page>
