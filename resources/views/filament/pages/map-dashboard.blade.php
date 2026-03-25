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

    {{-- Map Container --}}
    <div class="relative w-full">
        <div id="map"></div>
        
        {{-- Floating Legend --}}
        <div class="map-legend absolute bottom-5 right-5 z-[500] hidden md:block">
            <h4 class="font-bold mb-2 text-gray-700">Status Inspeksi</h4>
            <div class="legend-item">
                <div class="legend-color" style="background: #10b981;"></div>
                <span class="text-gray-600">Terpantau (< 30 hari)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #facc15;"></div>
                <span class="text-gray-600">Perlu Cek (31 - 90 hari)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #ef4444;"></div>
                <span class="text-gray-600">Urgent (> 90 hari / Belum)</span>
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
                                src="/storage/${foto}"
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
