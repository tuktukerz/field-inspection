<x-filament::page>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    {{-- Custom CSS for Map Responsiveness and Layering --}}
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
        /* Ensure Leaflet controls don't override Filament headers if necessary */
        .leaflet-top, .leaflet-bottom {
            z-index: 400 !important;
        }
    </style>

    {{-- Map --}}
    <div class="w-full">
        <div id="map"></div>
    </div>

    {{-- Ambil data --}}
    @php
    $mapData = $this->getMapData();
    @endphp

    {{-- Inject data ke JS (FIX ERROR CONST) --}}
    <script>
        window.mapData = {!! json_encode($mapData) !!};
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

                if (item.foto) {
                    imageHtml = `
                        <img
                            src="/storage/${item.foto}"
                            style="width:100%;border-radius:8px;margin-top:8px;"
                        />
                    `;
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
