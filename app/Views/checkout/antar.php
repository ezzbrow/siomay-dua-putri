<?= $this->include('partials/header') ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

<main>
    <?= $this->include('partials/progress', ['current' => 5]) ?>

    <h1 class="title">Diantar ke Alamatmu</h1>
    <p class="lead">Isi alamat lengkap dan tandai lokasimu di peta.</p>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-err"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="post" action="<?= base_url('checkout/antar') ?>" class="form">
            <?= csrf_field() ?>

            <div class="field">
                <label for="nama">Nama Penerima <span class="req">*</span></label>
                <input type="text" id="nama" name="nama" required maxlength="255"
                       value="<?= esc(old('nama', (string) session()->get('pembeli_nama'))) ?>" autocomplete="name">
            </div>

            <div class="field">
                <label for="nomor_hp">Nomor WhatsApp <span class="req">*</span></label>
                <input type="tel" id="nomor_hp" name="nomor_hp" required maxlength="30"
                       value="<?= esc(old('nomor_hp')) ?>" placeholder="cth: 081234567890" autocomplete="tel">
            </div>

            <div class="field">
                <label for="alamat">Alamat Lengkap <span class="req">*</span></label>
                <textarea id="alamat" name="alamat" rows="3" required maxlength="500"
                          placeholder="Jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota"><?= esc(old('alamat')) ?></textarea>
            </div>

            <div class="field">
                <label>Tandai Lokasi di Peta <span class="req">*</span></label>
                <div class="map-toolbar">
                    <button type="button" id="btn-locate" class="btn-secondary btn-small">
                        <span class="material-symbols-outlined">my_location</span> Gunakan Lokasi Saya
                    </button>
                    <span class="map-hint">Klik peta untuk pilih titik, atau geser marker</span>
                </div>
                <div id="peta" class="peta"></div>
                <input type="hidden" id="alamat_lat" name="alamat_lat" value="<?= esc(old('alamat_lat')) ?>">
                <input type="hidden" id="alamat_lng" name="alamat_lng" value="<?= esc(old('alamat_lng')) ?>">
            </div>

            <div class="actions">
                <a class="btn-secondary" href="<?= base_url('checkout/metode') ?>">← Kembali</a>
                <button class="btn-primary" type="submit">Lanjut →</button>
            </div>
        </form>
    </div>
</main>

<?= $this->include('partials/footer') ?>

<style>
    body { font-family: "Be Vietnam Pro", system-ui, sans-serif; background: #F8F6FF; color: #1d1a22; margin: 0; }
    main { max-width: 720px; margin: 0 auto; padding: 32px 16px 80px; }
    .title { font-size: 1.5rem; font-weight: 800; color: #4C1D95; margin: 0 0 8px; text-align: center; }
    .lead { color: #4a4452; text-align: center; margin: 0 0 24px; }
    .card { background: #fff; border: 1px solid #e7e0eb; border-radius: 20px; padding: 24px; box-shadow: 0 10px 30px rgba(76,29,149,0.08); }
    .form .field { margin-bottom: 14px; }
    .form label { display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; }
    .form .req { color: #e19760; }
    .form input, .form textarea { width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1.5px solid #e7e0eb; border-radius: 12px; font-family: inherit; }
    .form textarea { resize: vertical; }
    .flash { padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; }
    .flash-err { background: #FCE6E6; color: #991B1B; }
    .map-toolbar { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
    .btn-small { padding: 6px 12px; font-size: 0.8rem; min-height: 32px; flex: 0 0 auto; }
    .btn-secondary { background: #fff; color: #4C1D95; border: 1.5px solid #e7e0eb; padding: 11px 20px; border-radius: 12px; font-weight: 600; text-decoration: none; cursor: pointer; font-size: 0.95rem; }
    .map-hint { font-size: 0.85rem; color: #4a4452; }
    .peta { width: 100%; height: 320px; border-radius: 12px; border: 1.5px solid #e7e0eb; }
    .actions { display: flex; gap: 12px; margin-top: 20px; }
    .btn-primary, .btn-secondary { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 11px 20px; border-radius: 12px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; font-size: 0.95rem; min-height: 44px; flex: 1; }
    .btn-primary { background: #4C1D95; color: #fff; }
    .btn-primary:hover { background: #6D28D9; }
    .btn-secondary { background: #fff; color: #4C1D95; border: 1.5px solid #e7e0eb; }
    .btn-secondary:hover { background: #f3ebf6; }
</style>

<script>
(function () {
    // Default center: Palu
    var defaultCenter = [-0.8917, 119.8707];
    var initialLat = parseFloat(document.getElementById('alamat_lat').value) || defaultCenter[0];
    var initialLng = parseFloat(document.getElementById('alamat_lng').value) || defaultCenter[1];
    var hasInitial = !!(document.getElementById('alamat_lat').value && document.getElementById('alamat_lng').value);

    var map = L.map('peta').setView(hasInitial ? [initialLat, initialLng] : defaultCenter, 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var marker = null;
    if (hasInitial) {
        marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);
        marker.on('dragend', function (e) {
            var pos = e.target.getLatLng();
            document.getElementById('alamat_lat').value = pos.lat.toFixed(7);
            document.getElementById('alamat_lng').value = pos.lng.toFixed(7);
        });
    }

    function placeMarker(lat, lng) {
        var pos = L.latLng(lat, lng);
        if (!marker) {
            marker = L.marker(pos, { draggable: true }).addTo(map);
            marker.on('dragend', function (e) {
                var p = e.target.getLatLng();
                document.getElementById('alamat_lat').value = p.lat.toFixed(7);
                document.getElementById('alamat_lng').value = p.lng.toFixed(7);
            });
        } else {
            marker.setLatLng(pos);
        }
        document.getElementById('alamat_lat').value = lat.toFixed(7);
        document.getElementById('alamat_lng').value = lng.toFixed(7);
    }

    map.on('click', function (e) { placeMarker(e.latlng.lat, e.latlng.lng); });

    document.getElementById('btn-locate').addEventListener('click', function () {
        if (!navigator.geolocation) { alert('Browser tidak mendukung geolocation.'); return; }
        navigator.geolocation.getCurrentPosition(function (pos) {
            map.setView([pos.coords.latitude, pos.coords.longitude], 16);
            placeMarker(pos.coords.latitude, pos.coords.longitude);
        }, function () { alert('Tidak bisa akses lokasi browser. Pilih manual di peta.'); });
    });
})();
</script>
