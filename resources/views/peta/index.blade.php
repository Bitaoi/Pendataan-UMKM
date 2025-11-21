<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Persebaran UMKM</title>
    
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        #map { height: 100vh; width: 100%; }
        
        /* Styling Popup Custom agar Rapi */
        .leaflet-popup-content-wrapper {
            border-radius: 8px;
            box-shadow: 0 3px 14px rgba(0,0,0,0.4);
        }
        .leaflet-popup-content {
            margin: 15px;
            line-height: 1.6;
        }
        .popup-header {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .popup-table {
            width: 100%;
            font-size: 13px;
            color: #555;
            border-collapse: collapse;
        }
        .popup-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .popup-label {
            font-weight: 600;
            width: 90px;
            color: #7f8c8d;
        }
        
        /* Badge Status Halal */
        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            color: white;
            display: inline-block;
        }
        .badge-halal { background-color: #27ae60; } /* Hijau */
        .badge-non { background-color: #c0392b; } /* Merah */
        .badge-proses { background-color: #f39c12; } /* Oranye */
        .badge-unknown { background-color: #95a5a6; } /* Abu-abu */
    </style>
</head>
<body>

    {{-- Container Peta --}}
    <div id="map"></div>

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Peta (Default Kediri)
            var map = L.map('map').setView([-7.8228, 112.0119], 13);

            // 2. Tambahkan Tile Layer (Peta Dasar)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // 3. Ambil Data dari Controller
            // Pastikan di Controller anda mengirim compact('umkms') -> PLURAL
            var umkms = @json($umkms);

            // Debugging: Cek di Console Browser (F12) apakah data masuk lengkap
            console.log("Data UMKM Loaded:", umkms);

            // 4. Loop Data untuk Membuat Marker
            umkms.forEach(function(umkm) {
                // Validasi koordinat (cegah error jika null)
                if (umkm.latitude && umkm.longitude) {
                    
                    var marker = L.marker([umkm.latitude, umkm.longitude]).addTo(map);

                    // --- LOGIKA UNTUK MENENTUKAN ISI POPUP ---

                    // Ambil data dengan fallback string kosong jika null
                    let namaUsaha = umkm.nama_usaha || 'Tanpa Nama';
                    let namaPemilik = umkm.nama_pemilik || '-';
                    let sektorUsaha = umkm.sektor_usaha || '-';
                    let alamat = umkm.alamat_lengkap || '-';
                    
                    // Normalisasi kategori (lowercase & trim spasi) untuk perbandingan yang aman
                    let kategori = umkm.kategori_umkm ? umkm.kategori_umkm.toLowerCase().trim() : '';

                    // Variabel html untuk baris status halal
                    let htmlStatusHalal = '';

                    // Cek Kondisi: Jika kategori mengandung kata 'makanan' atau 'minuman'
                    // atau sama persis dengan 'makanan_minuman'
                    if (kategori === 'makanan_minuman' || kategori.includes('makanan') || kategori.includes('minuman')) {
                        
                        let status = umkm.status_halal || 'Belum Info';
                        let badgeClass = 'badge-unknown';

                        // Tentukan warna badge
                        if (status === 'Halal') {
                            badgeClass = 'badge-halal';
                        } else if (status === 'Non Halal') {
                            badgeClass = 'badge-non';
                        } else if (status === 'Sedang Proses') {
                            badgeClass = 'badge-proses';
                        }

                        // Buat baris HTML tabel khusus halal
                        htmlStatusHalal = `
                            <tr>
                                <td class="popup-label">Kehalalan</td>
                                <td>: <span class="badge ${badgeClass}">${status}</span></td>
                            </tr>
                        `;
                    }

                    // Susun HTML Popup Akhir
                    let popupContent = `
                        <div class="popup-header">${namaUsaha}</div>
                        <table class="popup-table">
                            <tr>
                                <td class="popup-label">Pemilik</td>
                                <td>: ${namaPemilik}</td>
                            </tr>
                            <tr>
                                <td class="popup-label">Sektor</td>
                                <td>: ${sektorUsaha}</td>
                            </tr>
                            <tr>
                                <td class="popup-label">Alamat</td>
                                <td>: ${alamat}</td>
                            </tr>
                            ${htmlStatusHalal} 
                        </table>
                    `;

                    // Masukkan konten ke dalam popup marker
                    marker.bindPopup(popupContent);
                }
            });
        });
    </script>
</body>
</html>