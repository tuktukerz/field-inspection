<x-filament::page>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    {{-- Map --}}
    <div id="map" style="height: 600px; border-radius: 12px;"></div>

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
                    <div style="min-width:200px">
                        <b>${item.nama ?? '-'}</b><br/>
                        ${item.kelurahan ?? '-'}, ${item.kecamatan ?? '-'}
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
