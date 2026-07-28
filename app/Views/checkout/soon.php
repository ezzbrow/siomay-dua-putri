<?= $this->include('partials/header') ?>

<main class="wizard-main">
    <div class="wizard-soon">
        <span class="material-symbols-outlined" aria-hidden="true">hourglass_empty</span>
        <h1>Step "<?= esc($stepName) ?>" — segera hadir</h1>
        <p>Halaman ini sedang dalam pengembangan. Tahap 2b & 2c akan menambahkannya.</p>
        <a href="<?= base_url('keranjang') ?>" class="wizard-soon-back">
            <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
            Kembali ke Keranjang
        </a>
    </div>
</main>

<style>
    .wizard-main { max-width: 640px; margin: 0 auto; padding: 64px 20px 0; }
    .wizard-soon {
        background: var(--surface); border: 1px solid var(--outline-variant);
        border-radius: var(--radius-lg); box-shadow: var(--card-shadow);
        padding: 48px 32px; text-align: center;
    }
    .wizard-soon .material-symbols-outlined { font-size: 56px; color: var(--secondary-light); }
    .wizard-soon h1 { color: var(--on-surface); margin: 16px 0 8px; font-size: 1.25rem; }
    .wizard-soon p { color: var(--on-surface-variant); margin: 0 0 24px; }
    .wizard-soon-back {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 20px; border-radius: var(--radius-md);
        background: var(--primary); color: #fff; text-decoration: none;
        font-weight: 600; font-size: 0.95rem;
        transition: background var(--t-fast);
    }
    .wizard-soon-back:hover { background: var(--primary-hover); color: #fff; }
    .wizard-soon-back .material-symbols-outlined { font-size: 18px; color: #fff; }
</style>

<?= $this->include('partials/footer') ?>
