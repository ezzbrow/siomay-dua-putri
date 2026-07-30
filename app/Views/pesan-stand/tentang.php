<?= $this->include('partials/header') ?>

<main class="stand-main">

    <!-- Hero Section -->
    <section class="stand-hero">
        <div class="stand-hero-blob stand-hero-blob-1" aria-hidden="true"></div>
        <div class="stand-hero-blob stand-hero-blob-2" aria-hidden="true"></div>
        <div class="stand-hero-content">
            <span class="stand-badge">
                <span class="material-symbols-outlined" aria-hidden="true">celebration</span>
                Tersedia untuk Acara &amp; Kegiatan
            </span>
            <h1 class="stand-hero-title">
                Bawa <span class="text-primary">Siomay Dua Putri</span><br>ke Acara Anda
            </h1>
            <p class="stand-hero-tagline">
                Kami siap hadir di pernikahan, arisan, seminar, dan berbagai kegiatan lainnya —
                lengkap dengan stand, menu siap saji, dan pelayanan profesional.
            </p>
            <a href="<?= base_url('pesan-stand/form') ?>" class="stand-cta-btn" id="btn-booking-sekarang">
                <span class="material-symbols-outlined" aria-hidden="true">edit_calendar</span>
                Booking Sekarang
            </a>
        </div>
    </section>

    <!-- Kategori Acara yang Dilayani -->
    <section class="stand-acara-section" aria-label="Kategori acara yang kami layani">
        <h2 class="stand-section-title">Cocok untuk Berbagai Acara</h2>
        <div class="stand-acara-grid">
            <div class="stand-acara-card">
                <span class="material-symbols-outlined stand-acara-icon" aria-hidden="true">favorite</span>
                <div class="stand-acara-label">Pernikahan</div>
            </div>
            <div class="stand-acara-card">
                <span class="material-symbols-outlined stand-acara-icon" aria-hidden="true">cake</span>
                <div class="stand-acara-label">Ulang Tahun</div>
            </div>
            <div class="stand-acara-card">
                <span class="material-symbols-outlined stand-acara-icon" aria-hidden="true">groups</span>
                <div class="stand-acara-label">Arisan</div>
            </div>
            <div class="stand-acara-card">
                <span class="material-symbols-outlined stand-acara-icon" aria-hidden="true">mosque</span>
                <div class="stand-acara-label">Pengajian</div>
            </div>
            <div class="stand-acara-card">
                <span class="material-symbols-outlined stand-acara-icon" aria-hidden="true">school</span>
                <div class="stand-acara-label">Seminar</div>
            </div>
            <div class="stand-acara-card">
                <span class="material-symbols-outlined stand-acara-icon" aria-hidden="true">store</span>
                <div class="stand-acara-label">Grand Opening</div>
            </div>
            <div class="stand-acara-card">
                <span class="material-symbols-outlined stand-acara-icon" aria-hidden="true">more_horiz</span>
                <div class="stand-acara-label">Dan Lainnya</div>
            </div>
        </div>
    </section>

    <!-- Keunggulan -->
    <section class="stand-why-section" aria-label="Keunggulan stand Siomay Dua Putri">
        <h2 class="stand-section-title">Mengapa Pilih Stand Kami?</h2>
        <div class="stand-why-grid">
            <div class="stand-why-item">
                <div class="stand-why-icon">
                    <span class="material-symbols-outlined" aria-hidden="true">restaurant</span>
                </div>
                <div class="stand-why-text">
                    <div class="stand-why-judul">Menu Beragam</div>
                    <div class="stand-why-deskripsi">14 pilihan menu siap saji — siomay, lumpia, pentol, snack, hingga minuman</div>
                </div>
            </div>
            <div class="stand-why-item">
                <div class="stand-why-icon">
                    <span class="material-symbols-outlined" aria-hidden="true">local_shipping</span>
                </div>
                <div class="stand-why-text">
                    <div class="stand-why-judul">Datang ke Lokasi</div>
                    <div class="stand-why-deskripsi">Tim kami hadir langsung di venue acara Anda</div>
                </div>
            </div>
            <div class="stand-why-item">
                <div class="stand-why-icon">
                    <span class="material-symbols-outlined" aria-hidden="true">qr_code_2</span>
                </div>
                <div class="stand-why-text">
                    <div class="stand-why-judul">Bayar Mudah via QRIS</div>
                    <div class="stand-why-deskripsi">Pembayaran booking 100% digital, aman dan transparan</div>
                </div>
            </div>
            <div class="stand-why-item">
                <div class="stand-why-icon">
                    <span class="material-symbols-outlined" aria-hidden="true">verified</span>
                </div>
                <div class="stand-why-text">
                    <div class="stand-why-judul">Terpercaya Sejak 2018</div>
                    <div class="stand-why-deskripsi">Pengalaman bertahun-tahun melayani berbagai kalangan di Palu</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Langkah Booking -->
    <section class="stand-steps-section" aria-label="Cara booking stand">
        <h2 class="stand-section-title">Cara Booking Stand</h2>
        <div class="stand-steps-list">
            <div class="stand-step">
                <div class="stand-step-num">1</div>
                <div class="stand-step-text">
                    <div class="stand-step-judul">Isi Data Acara</div>
                    <div class="stand-step-desc">Jenis, tanggal, lokasi, dan estimasi tamu</div>
                </div>
            </div>
            <div class="stand-step-line" aria-hidden="true"></div>
            <div class="stand-step">
                <div class="stand-step-num">2</div>
                <div class="stand-step-text">
                    <div class="stand-step-judul">Pilih Menu</div>
                    <div class="stand-step-desc">Sesuaikan menu dengan kebutuhan acara</div>
                </div>
            </div>
            <div class="stand-step-line" aria-hidden="true"></div>
            <div class="stand-step">
                <div class="stand-step-num">3</div>
                <div class="stand-step-text">
                    <div class="stand-step-judul">Bayar via QRIS</div>
                    <div class="stand-step-desc">DP booking melalui QRIS, konfirmasi oleh admin</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA bawah -->
    <section class="stand-cta-bottom">
        <h2 class="stand-cta-title">Siap Meriahkan Acara Anda?</h2>
        <p class="stand-cta-desc">Booking sekarang sebelum tanggal acara terambil.</p>
        <a href="<?= base_url('pesan-stand/form') ?>" class="stand-cta-btn" id="btn-booking-bawah">
            <span class="material-symbols-outlined" aria-hidden="true">edit_calendar</span>
            Booking Sekarang
        </a>
    </section>

</main>

<style>
    .stand-main {
        max-width: 768px;
        margin: 0 auto;
        padding: 32px 20px 48px;
    }

    /* Hero */
    .stand-hero {
        position: relative;
        text-align: center;
        padding: 40px 0 48px;
        overflow: hidden;
    }
    .stand-hero-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        pointer-events: none;
        z-index: 0;
    }
    .stand-hero-blob-1 {
        top: 0; left: -64px;
        width: 280px; height: 280px;
        background: var(--primary); opacity: 0.08;
    }
    .stand-hero-blob-2 {
        bottom: 0; right: -64px;
        width: 320px; height: 320px;
        background: var(--secondary); opacity: 0.08;
    }
    .stand-hero-content { position: relative; z-index: 1; }

    .stand-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--secondary-fixed);
        color: var(--primary);
        padding: 6px 16px;
        border-radius: var(--radius-pill);
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
    .stand-badge .material-symbols-outlined { font-size: 18px; }

    .stand-hero-title {
        font-size: clamp(1.7rem, 5vw, 2.8rem);
        font-weight: 800;
        color: var(--on-surface);
        line-height: 1.2;
        margin: 0 0 16px;
    }
    .text-primary { color: var(--primary); }

    .stand-hero-tagline {
        color: var(--on-surface-variant);
        font-size: 1rem;
        max-width: 500px;
        margin: 0 auto 32px;
        line-height: 1.6;
    }

    .stand-cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--primary);
        color: #fff;
        text-decoration: none;
        padding: 14px 28px;
        border-radius: var(--radius-md);
        font-weight: 700;
        font-size: 1.05rem;
        transition: background var(--t-fast), transform var(--t-fast);
        min-height: 48px;
    }
    .stand-cta-btn:hover {
        background: var(--primary-hover);
        color: #fff;
        transform: translateY(-2px);
    }
    .stand-cta-btn .material-symbols-outlined { font-size: 22px; }

    /* Section title */
    .stand-section-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--on-surface);
        text-align: center;
        margin: 0 0 24px;
    }

    /* Acara Grid */
    .stand-acara-section {
        margin-top: 56px;
    }
    .stand-acara-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }
    @media (max-width: 600px) {
        .stand-acara-grid { grid-template-columns: repeat(2, 1fr); }
    }
    .stand-acara-card {
        background: var(--surface);
        border: 1px solid var(--outline-variant);
        border-radius: var(--radius-lg);
        padding: 20px 12px;
        text-align: center;
        box-shadow: var(--card-shadow);
        transition: transform var(--t-fast), box-shadow var(--t-fast);
    }
    .stand-acara-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--card-shadow-hover);
    }
    .stand-acara-icon {
        font-size: 32px;
        color: var(--primary);
        display: block;
        margin-bottom: 8px;
    }
    .stand-acara-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--on-surface);
    }

    /* Why Grid */
    .stand-why-section { margin-top: 56px; }
    .stand-why-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    @media (max-width: 480px) {
        .stand-why-grid { grid-template-columns: 1fr; }
    }
    .stand-why-item {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        background: var(--surface);
        border: 1px solid var(--outline-variant);
        border-radius: var(--radius-lg);
        padding: 18px;
        box-shadow: var(--card-shadow);
    }
    .stand-why-icon {
        width: 48px; height: 48px;
        background: var(--secondary-fixed);
        border-radius: var(--radius-md);
        display: flex; align-items: center; justify-content: center;
        color: var(--primary);
        flex-shrink: 0;
    }
    .stand-why-icon .material-symbols-outlined { font-size: 24px; }
    .stand-why-judul {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--on-surface);
        margin-bottom: 4px;
    }
    .stand-why-deskripsi {
        font-size: 0.82rem;
        color: var(--on-surface-variant);
        line-height: 1.5;
    }

    /* Steps */
    .stand-steps-section { margin-top: 56px; }
    .stand-steps-list {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        flex-wrap: nowrap;
        overflow-x: auto;
    }
    .stand-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex: 1;
        min-width: 120px;
    }
    .stand-step-num {
        width: 48px; height: 48px;
        background: var(--primary);
        color: #fff;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1.2rem;
        margin-bottom: 10px;
        box-shadow: 0 4px 16px rgba(76, 29, 149, 0.25);
    }
    .stand-step-judul {
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--on-surface);
    }
    .stand-step-desc {
        font-size: 0.78rem;
        color: var(--on-surface-variant);
        margin-top: 4px;
        line-height: 1.4;
    }
    .stand-step-line {
        height: 2px;
        width: 40px;
        background: var(--outline-variant);
        margin-bottom: 28px;
        flex-shrink: 0;
    }

    /* CTA Bottom */
    .stand-cta-bottom {
        margin-top: 56px;
        text-align: center;
        background: linear-gradient(135deg, var(--secondary-fixed) 0%, #ede8ff 100%);
        border-radius: var(--radius-lg);
        padding: 40px 24px;
        border: 1px solid var(--outline-variant);
    }
    .stand-cta-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--primary);
        margin: 0 0 8px;
    }
    .stand-cta-desc {
        color: var(--on-surface-variant);
        margin: 0 0 24px;
        font-size: 0.95rem;
    }
</style>

<?= $this->include('partials/footer') ?>
