</main>

<footer class="footer-section">
    <div class="footer-grid">
        <div>
            <div class="footer-brand">Siomay Dua Putri</div>
            <p class="footer-desc">
                <?= esc($footerDeskripsi ?? 'Siomay segar, dibuat setiap hari dengan bahan berkualitas.') ?>
            </p>
        </div>

        <div>
            <h4 class="footer-heading">Tautan Cepat</h4>
            <ul class="footer-list">
                <li><a href="<?= base_url('/') ?>">Home</a></li>
                <li><a href="<?= base_url('etalase') ?>">Pesan Antar / Jemput</a></li>
                <li>
                    <?php if (! empty($pesananAcaraUrl)): ?>
                        <a href="<?= esc($pesananAcaraUrl) ?>">Pesanan Acara / Kegiatan</a>
                    <?php else: ?>
                        <span class="footer-soon">Pesanan Acara (segera hadir)</span>
                    <?php endif; ?>
                </li>
            </ul>
        </div>

        <div>
            <h4 class="footer-heading">Kontak</h4>
            <ul class="footer-list">
                <?php if (! empty($kontakAlamat)): ?>
                    <li class="footer-contact-line">
                        <span class="material-symbols-outlined" aria-hidden="true">location_on</span>
                        <span><?= esc($kontakAlamat) ?></span>
                    </li>
                <?php else: ?>
                    <li class="footer-contact-line footer-soon">
                        <span class="material-symbols-outlined" aria-hidden="true">location_on</span>
                        <span>Alamat UMKM — segera diperbarui</span>
                    </li>
                <?php endif; ?>
                <?php if (! empty($kontakHp)): ?>
                    <li class="footer-contact-line">
                        <span class="material-symbols-outlined" aria-hidden="true">call</span>
                        <span><?= esc($kontakHp) ?></span>
                    </li>
                <?php else: ?>
                    <li class="footer-contact-line footer-soon">
                        <span class="material-symbols-outlined" aria-hidden="true">call</span>
                        <span>Nomor HP admin — akan diperbarui</span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <span>© <?= date('Y') ?> Siomay Dua Putri. Dibuat dengan sepenuh hati.</span>
        <a href="<?= base_url('admin/login') ?>" class="footer-admin-link">
            <span class="material-symbols-outlined" aria-hidden="true">admin_panel_settings</span>
            Masuk sebagai Admin
        </a>
    </div>
</footer>

<style>
    /* === Footer (Purple theme, konsisten dengan header etalase) === */
    .footer-section {
        background: var(--surface-variant);
        width: 100%;
        padding: 48px 24px 24px;
        margin-top: 48px;
        border-top: 1px solid var(--outline-variant);
    }
    .footer-grid {
        max-width: 1024px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr;
        gap: 32px;
        padding-top: 32px;
        border-top: 1px solid var(--outline-variant);
    }
    @media (min-width: 768px) {
        .footer-grid { grid-template-columns: 1.4fr 1fr 1.2fr; }
    }
    .footer-brand {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 8px;
    }
    .footer-desc {
        color: var(--on-surface-variant);
        font-size: 0.9rem;
        margin: 0;
        line-height: 1.5;
    }
    .footer-heading {
        font-weight: 700;
        color: var(--on-surface);
        margin: 0 0 12px;
        font-size: 0.95rem;
    }
    .footer-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-size: 0.9rem;
    }
    .footer-list a {
        color: var(--on-surface-variant);
        transition: color var(--t-fast);
    }
    .footer-list a:hover { color: var(--secondary); }
    .footer-contact-line {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--on-surface-variant);
    }
    .footer-contact-line .material-symbols-outlined { font-size: 18px; }
    .footer-soon { color: var(--on-surface-variant); font-style: italic; opacity: 0.7; }
    .footer-bottom {
        max-width: 1024px;
        margin: 32px auto 0;
        text-align: center;
        color: var(--on-surface-variant);
        font-size: 0.8rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .footer-admin-link {
        color: var(--on-surface-variant);
        opacity: 0.6;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: opacity var(--t-fast), color var(--t-fast);
    }
    .footer-admin-link:hover { opacity: 1; color: var(--primary); }
    .footer-admin-link .material-symbols-outlined { font-size: 14px; }
</style>

</body>
</html>
