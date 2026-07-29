<?= $this->include('partials/header') ?>

<main>
    <?= $this->include('partials/progress', ['current' => 5]) ?>

    <h1 class="title">Pilih Metode Penerimaan</h1>
    <p class="lead">Mau ambil sendiri di toko atau diantar ke tempatmu?</p>

    <form method="post" action="<?= base_url('checkout/metode') ?>" id="form-metode">
        <?= csrf_field() ?>
        <input type="hidden" name="metode" id="metode-input" value="">

        <div class="metode-grid">
            <button type="button" class="metode-card" data-value="ambil_sendiri" onclick="document.getElementById('metode-input').value='ambil_sendiri';document.getElementById('form-metode').submit();">
                <div class="icon"><span class="material-symbols-outlined">storefront</span></div>
                <div class="text">
                    <div class="judul">Ambil Sendiri</div>
                    <div class="deskripsi">Ambil langsung di toko, gratis ongkir</div>
                </div>
            </button>
            <button type="button" class="metode-card" data-value="diantar" onclick="document.getElementById('metode-input').value='diantar';document.getElementById('form-metode').submit();">
                <div class="icon"><span class="material-symbols-outlined">moped</span></div>
                <div class="text">
                    <div class="judul">Diantar</div>
                    <div class="deskripsi">Pesanan diantar ke alamatmu</div>
                </div>
            </button>
        </div>

        <p class="back"><a href="<?= base_url('checkout/tanggal') ?>">← Kembali</a></p>
    </form>
</main>

<?= $this->include('partials/footer') ?>

<style>
    body { font-family: "Be Vietnam Pro", system-ui, sans-serif; background: #F8F6FF; color: #1d1a22; margin: 0; }
    main { max-width: 640px; margin: 0 auto; padding: 32px 16px 80px; }
    .title { font-size: 1.5rem; font-weight: 800; color: #4C1D95; margin: 0 0 8px; text-align: center; }
    .lead { color: #4a4452; text-align: center; margin: 0 0 24px; }
    .metode-grid { display: grid; gap: 12px; }
    .metode-card { display: flex; align-items: center; gap: 16px; padding: 20px; background: #fff; border: 1.5px solid #e7e0eb; border-radius: 16px; cursor: pointer; text-align: left; font-family: inherit; transition: border-color 180ms, transform 180ms; }
    .metode-card:hover { border-color: #4C1D95; transform: translateY(-1px); }
    .metode-card .icon { width: 56px; height: 56px; background: #ebddff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #4C1D95; flex-shrink: 0; }
    .metode-card .icon .material-symbols-outlined { font-size: 32px; }
    .metode-card .judul { font-weight: 700; font-size: 1.05rem; color: #1d1a22; }
    .metode-card .deskripsi { font-size: 0.9rem; color: #4a4452; margin-top: 2px; }
    .back { text-align: center; margin-top: 24px; }
    .back a { color: #4C1D95; text-decoration: none; font-weight: 600; }
</style>
