<?= $this->include('partials/header') ?>

<main class="landing-main">

    <!-- Hero -->
    <section class="hero">
        <div class="hero-blob hero-blob-primary" aria-hidden="true"></div>
        <div class="hero-blob hero-blob-secondary" aria-hidden="true"></div>

        <div class="hero-content">
            <span class="hero-badge">
                Sejak 2018 &middot; Palu
            </span>
            <h1 class="hero-title">
                <span class="hero-brand">Siomay Dua Putri</span>
            </h1>
            <p class="hero-tagline">
                Dibuat segar setiap hari, siap diantar atau diambil langsung ke tempatmu.
            </p>

            <div class="hero-illustration">
                <img src="<?= base_url('assets/img/gerobak-ungu.svg') ?>" alt="Gerobak Siomay Dua Putri"
                     class="hero-gerobak">
                <img src="<?= base_url('assets/img/mangkok-ungu.svg') ?>" alt="Mangkok Siomay"
                     class="hero-mangkok" aria-hidden="true">
            </div>
        </div>
    </section>

    <!-- Pilihan (2 kartu: Pesan Antar / Jemput + Pesanan Acara / Kegiatan) -->
    <section class="pilihan-section" aria-label="Pilih jenis pesanan">
        <a href="<?= base_url('etalase') ?>" class="pilihan-card">
            <div class="pilihan-icon-wrap">
                <span class="material-symbols-outlined" aria-hidden="true">moped</span>
            </div>
            <div class="pilihan-text">
                <div class="pilihan-judul">Pesan Antar / Jemput</div>
                <div class="pilihan-deskripsi">Untuk pesanan banyak, min. Rp100rb</div>
            </div>
            <span class="material-symbols-outlined pilihan-chevron" aria-hidden="true">chevron_right</span>
        </a>

        <?php if (! empty($pesananAcaraUrl)): ?>
            <a href="<?= esc($pesananAcaraUrl) ?>" class="pilihan-card">
                <div class="pilihan-icon-wrap">
                    <span class="material-symbols-outlined" aria-hidden="true">celebration</span>
                </div>
                <div class="pilihan-text">
                    <div class="pilihan-judul">Pesanan Acara/Kegiatan</div>
                    <div class="pilihan-deskripsi">Untuk acara &amp; hajatan, harga custom via WhatsApp</div>
                </div>
                <span class="material-symbols-outlined pilihan-chevron" aria-hidden="true">chevron_right</span>
            </a>
        <?php else: ?>
            <div class="pilihan-card pilihan-soon" aria-disabled="true">
                <div class="pilihan-icon-wrap">
                    <span class="material-symbols-outlined" aria-hidden="true">celebration</span>
                </div>
                <div class="pilihan-text">
                    <div class="pilihan-judul">Pesanan Acara/Kegiatan</div>
                    <div class="pilihan-deskripsi">Untuk acara &amp; hajatan, harga custom via WhatsApp — <em>segera hadir</em></div>
                </div>
                <span class="material-symbols-outlined pilihan-chevron" aria-hidden="true">hourglass_empty</span>
            </div>
        <?php endif; ?>
    </section>

    <!-- Kenapa Pilih Kami -->
    <section class="why-section" aria-label="Kenapa pilih Siomay Dua Putri">
        <h2 class="why-heading">Kenapa Pilih Kami?</h2>
        <div class="why-grid">
            <div class="why-item">
                <div class="why-icon-circle">
                    <span class="material-symbols-outlined" aria-hidden="true">eco</span>
                </div>
                <div class="why-label">Bahan Segar</div>
            </div>
            <div class="why-item">
                <div class="why-icon-circle">
                    <span class="material-symbols-outlined" aria-hidden="true">sell</span>
                </div>
                <div class="why-label">Harga Bersahabat</div>
            </div>
            <div class="why-item">
                <div class="why-icon-circle">
                    <span class="material-symbols-outlined" aria-hidden="true">qr_code_2</span>
                </div>
                <div class="why-label">QRIS</div>
            </div>
            <div class="why-item">
                <div class="why-icon-circle">
                    <span class="material-symbols-outlined" aria-hidden="true">bolt</span>
                </div>
                <div class="why-label">Cepat</div>
            </div>
        </div>
    </section>

</main>

<style>
    /* === Landing layout === */
    .landing-main {
        max-width: 1024px;
        margin: 0 auto;
        padding: 32px 20px 0;
    }

    /* === Hero === */
    .hero {
        position: relative;
        padding: 16px 0 40px;
        overflow: hidden;
        text-align: center;
    }
    .hero-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        pointer-events: none;
        z-index: 0;
    }
    .hero-blob-primary {
        top: 40px;
        left: -64px;
        width: 256px;
        height: 256px;
        background: var(--primary);
        opacity: 0.10;
    }
    .hero-blob-secondary {
        bottom: 0;
        right: -64px;
        width: 288px;
        height: 288px;
        background: var(--secondary);
        opacity: 0.10;
    }
    .hero-content {
        position: relative;
        z-index: 1;
    }
    .hero-badge {
        display: inline-block;
        background: var(--secondary-fixed);
        color: var(--primary);
        padding: 4px 14px;
        border-radius: var(--radius-pill);
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 16px;
    }
    .hero-title {
        font-size: clamp(1.75rem, 5vw, 3rem);
        font-weight: 800;
        color: var(--on-surface);
        line-height: 1.15;
        margin: 0 0 12px;
    }
    .hero-brand { color: var(--primary); }
    .hero-tagline {
        color: var(--on-surface-variant);
        max-width: 480px;
        margin: 0 auto 32px;
        font-size: 0.95rem;
    }
    .hero-illustration {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }
    .hero-gerobak {
        width: 100%;
        max-width: 280px;
        filter: drop-shadow(0 20px 30px rgba(76, 29, 149, 0.18));
    }
    .hero-mangkok {
        position: absolute;
        top: -16px;
        right: calc(50% - 110px);
        width: 80px;
        animation: hero-float 3.5s ease-in-out infinite;
    }
    @media (min-width: 768px) {
        .hero-mangkok { right: calc(50% - 200px); width: 96px; }
    }
    @keyframes hero-float {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-10px); }
    }

    /* === Pilihan cards === */
    .pilihan-section {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        max-width: 560px;
        margin: 0 auto;
    }
    .pilihan-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        background: var(--surface);
        border: 1px solid var(--outline-variant);
        border-radius: var(--radius-lg);
        box-shadow: var(--card-shadow);
        text-decoration: none;
        color: var(--on-surface);
        transition: transform var(--t-base), box-shadow var(--t-base);
    }
    .pilihan-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--card-shadow-hover);
        color: var(--on-surface);
    }
    .pilihan-card.pilihan-soon {
        cursor: not-allowed;
        opacity: 0.85;
    }
    .pilihan-card.pilihan-soon:hover {
        transform: none;
        box-shadow: var(--card-shadow);
    }
    .pilihan-icon-wrap {
        width: 56px;
        height: 56px;
        background: var(--secondary-fixed);
        border-radius: var(--radius-md);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        flex-shrink: 0;
        transition: transform var(--t-base);
    }
    .pilihan-card:hover .pilihan-icon-wrap { transform: scale(1.08); }
    .pilihan-icon-wrap .material-symbols-outlined { font-size: 32px; }
    .pilihan-text { flex: 1; min-width: 0; }
    .pilihan-judul {
        font-weight: 700;
        color: var(--on-surface);
        font-size: 1.05rem;
    }
    .pilihan-deskripsi {
        color: var(--on-surface-variant);
        font-size: 0.9rem;
        margin-top: 2px;
    }
    .pilihan-chevron {
        color: var(--outline-variant);
        font-size: 28px;
        flex-shrink: 0;
        transition: color var(--t-fast), transform var(--t-fast);
    }
    .pilihan-card:hover .pilihan-chevron {
        color: var(--primary);
        transform: translateX(2px);
    }

    /* === Kenapa pilih kami === */
    .why-section {
        margin-top: 56px;
        max-width: 768px;
        margin-left: auto;
        margin-right: auto;
    }
    .why-heading {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--on-surface);
        text-align: center;
        margin: 0 0 20px;
    }
    @media (min-width: 768px) {
        .why-heading { font-size: 1.4rem; }
    }
    .why-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    @media (min-width: 768px) {
        .why-grid { grid-template-columns: repeat(4, 1fr); }
    }
    .why-item {
        background: var(--surface);
        padding: 20px 12px;
        border-radius: var(--radius-md);
        border: 1px solid var(--outline-variant);
        text-align: center;
        transition: transform var(--t-fast), box-shadow var(--t-fast);
    }
    .why-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--card-shadow);
    }
    .why-icon-circle {
        width: 48px;
        height: 48px;
        background: var(--secondary-fixed);
        border-radius: 50%;
        margin: 0 auto 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
    }
    .why-icon-circle .material-symbols-outlined { font-size: 24px; }
    .why-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--on-surface);
    }
</style>

<?= $this->include('partials/footer') ?>
