<head>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map { height: 500px; }
    </style>
</head>
<body>
    <h1>Peta Persebaran UMKM</h1>
    <div id="map"></div>
    <script>
    // Inisialisasi Peta
    var map = L.map('map').setView([-7.822, 112.01], 13); // Koordinat tengah Kediri

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Ambil data UMKM dari PHP dan tampilkan sebagai marker
    var umkms = @json($umkms);

    umkms.forEach(function(umkm) {
        var marker = L.marker([umkm.latitude, umkm.longitude]).addTo(map);
        marker.bindPopup(
            `<b>${umkm.nama_usaha}</b><br>` +
            `Pemilik: ${umkm.nama_pemilik}<br>` +
            `Sektor: ${umkm.sektor_usaha}`
        );
    });
</script>
</body>