<x-filament::page>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    {{-- Custom CSS for Map Responsiveness, Layering, and Modal --}}
    <style>
        #map {
            height: 400px;
            z-index: 0 !important;
            border-radius: 12px;
        }
        @media (min-width: 768px) {
            #map {
                height: 600px;
            }
        }
        .leaflet-top, .leaflet-bottom {
            z-index: 400 !important;
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
        }
        #imagePreviewModal img {
            max-width: 90%;
            max-height: 90%;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(255,255,255,0.2);
        }
        #imagePreviewModal .close {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>

    {{-- Map --}}
    <div class="w-full">
        <div id="map"></div>
    </div>

    {{-- Modal Preview --}}
    <div id="imagePreviewModal" onclick="this.style.display='none'">
        <span class="close">&times;</span>
        <img id="previewImg" src="">
    </div>

    {{-- Ambil data --}}
    @php
    $mapData = $this->getMapData();
    @endphp

    {{-- Inject data ke JS (FIX ERROR CONST) --}}
    <script>
        window.mapData = {!! json_encode($mapData) !!};

        function openPreview(src) {
            const modal = document.getElementById('imagePreviewModal');
            const img = document.getElementById('previewImg');
            img.src = src;
            modal.style.display = 'flex';
        }
    </script>

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const data = window.mapData || [];

            // INIT MAP
            const map = L.map('map').setView([-6.2, 106.8], 11);

            // TILE
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            const bounds = [];

            data.forEach(function (item) {

                if (!item.latitude || !item.longitude) return;

                const lat = Number(item.latitude);
                const lng = Number(item.longitude);

                if (isNaN(lat) || isNaN(lng)) return;

                const marker = L.marker([lat, lng]).addTo(map);

                bounds.push([lat, lng]);

                let imageHtml = '';

                if (item.fotos && item.fotos.length > 0) {
                    const maxPhotos = 3;
                    const displayedPhotos = item.fotos.slice(0, maxPhotos);
                    const remaining = item.fotos.length - maxPhotos;

                    imageHtml = '<div style="margin-top:10px; display:flex; flex-wrap:wrap; gap:5px; justify-content:center;">';
                    displayedPhotos.forEach(function(foto) {
                        imageHtml += `
                            <img
                                src="/storage/${foto}"
                                style="width:70px; height:70px; object-fit:cover; border-radius:4px; cursor:zoom-in; border:1px solid #ddd;"
                                onclick="openPreview(this.src)"
                                title="Klik untuk memperbesar"
                            />
                        `;
                    });

                    if (remaining > 0) {
                        imageHtml += `
                            <div style="width:70px; height:70px; background:#f3f4f6; border:1px solid #ddd; border-radius:4px; display:flex; align-items:center; justify-content:center; font-weight:bold; color:#666; font-size:14px;" title="${remaining} foto lainnya">
                                +${remaining}
                            </div>
                        `;
                    }

                    imageHtml += '</div><div style="font-size:10px; color:#666; margin-top:5px; text-align:center;">Klik foto untuk memperbesar</div>';
                }

                const popupContent = `
                    <div style="min-width:250px; font-family: Arial, sans-serif;">
                        <h3 style="margin: 0 0 10px 0; color: #1a56db; font-size: 14px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Informasi Menara</h3>
                        <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
                            <tr><td style="padding: 2px 0; color: #666;">TGL PENGISIAN</td><td style="padding: 2px 0; font-weight: bold;">: ${item.tanggal ?? '-'}</td></tr>
                            <tr><td style="padding: 2px 0; color: #666;">LOKASI</td><td style="padding: 2px 0; font-weight: bold;">: ${item.lokasi ?? '-'}</td></tr>
                            <tr><td style="padding: 2px 0; color: #666;">DETAIL</td><td style="padding: 2px 0;">: ${item.detail ?? '-'}</td></tr>
                            <tr><td style="padding: 2px 0; color: #666;">KECAMATAN</td><td style="padding: 2px 0;">: ${item.kecamatan ?? '-'}</td></tr>
                            <tr><td style="padding: 2px 0; color: #666;">KELURAHAN</td><td style="padding: 2px 0;">: ${item.kelurahan ?? '-'}</td></tr>
                            <tr><td style="padding: 2px 0; color: #666;">LETAK TITIK</td><td style="padding: 2px 0;">: ${item.letak ?? '-'}</td></tr>
                            <tr><td style="padding: 2px 0; color: #666;">LATITUDE</td><td style="padding: 2px 0;">: ${item.latitude ?? '-'}</td></tr>
                            <tr><td style="padding: 2px 0; color: #666;">LONGITUDE</td><td style="padding: 2px 0;">: ${item.longitude ?? '-'}</td></tr>
                        </table>
                        ${imageHtml}
                    </div>
                `;

                marker.bindPopup(popupContent);
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds);
            }

        });
    </script>

</x-filament::page>
