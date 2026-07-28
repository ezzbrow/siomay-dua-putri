<?= $this->include('partials/header') ?>

<main class="wizard-main">
    <?= $this->include('partials/progress', ['current' => 3]) ?>

    <div class="wizard-heading">
        <h1 class="wizard-title">
            <span class="material-symbols-outlined" aria-hidden="true">edit_note</span>
            Catatan Pesanan
        </h1>
        <p class="wizard-tagline">Ada permintaan khusus soal rasa atau cara masak? Tulis di sini ya.</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="wizard-flash wizard-flash-err" role="alert">
            <span class="material-symbols-outlined" aria-hidden="true">error</span>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('checkout/catatan') ?>" method="post" class="wizard-form">
        <?= csrf_field() ?>

        <div class="wizard-card">
            <div class="wizard-field">
                <label for="catatan">Catatan (opsional)</label>
                <textarea id="catatan" name="catatan" rows="4" maxlength="250"
                          placeholder="cth: extra saus, bumbunya dipisah, sambal banyak"
                          oninput="document.getElementById('counter').textContent = this.value.length"><?= esc($catatanValue) ?></textarea>
                <div class="wizard-hint-row">
                    <span class="wizard-hint">
                        <strong>Penting:</strong> kolom ini <strong>BUKAN</strong> untuk alamat — alamat
                        pengiriman akan diminta di langkah berikutnya sesuai metode yang dipilih.
                    </span>
                    <span class="wizard-counter"><span id="counter"><?= strlen($catatanValue) ?></span>/250</span>
                </div>
            </div>
        </div>

        <div class="wizard-actions">
            <a href="<?= base_url('keranjang') ?>" class="wizard-btn wizard-btn-secondary">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                Kembali
            </a>
            <button type="submit" class="wizard-btn wizard-btn-primary">
                Lanjut
                <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
            </button>
        </div>
    </form>
</main>

<style>
    .wizard-main {
        max-width: 640px;
        margin: 0 auto;
        padding: 32px 20px 0;
    }
    .wizard-heading {
        text-align: center;
        margin-bottom: 24px;
    }
    .wizard-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--on-surface);
        margin: 0 0 8px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .wizard-title .material-symbols-outlined { font-size: 28px; color: var(--primary); }
    .wizard-tagline {
        color: var(--on-surface-variant);
        margin: 0;
        font-size: 0.95rem;
    }
    .wizard-flash {
        padding: 12px 16px;
        border-radius: var(--radius-md);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        font-size: 0.9rem;
    }
    .wizard-flash-err { background: var(--err-bg); color: var(--err-fg); border: 1px solid rgba(153, 27, 27, 0.18); }
    .wizard-flash .material-symbols-outlined { font-size: 20px; }

    .wizard-card {
        background: var(--surface);
        border: 1px solid var(--outline-variant);
        border-radius: var(--radius-lg);
        box-shadow: var(--card-shadow);
        padding: 24px;
    }
    .wizard-field { margin-bottom: 4px; }
    .wizard-field label {
        display: block;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--on-surface);
        margin-bottom: 6px;
    }
    .wizard-field textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1.5px solid var(--outline-variant);
        border-radius: var(--radius-md);
        font-family: inherit;
        background: var(--surface);
        color: var(--on-surface);
        font-size: 0.95rem;
        resize: vertical;
        min-height: 100px;
        transition: border-color var(--t-fast), box-shadow var(--t-fast);
    }
    .wizard-field textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(76, 29, 149, 0.18);
    }
    .wizard-hint-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-top: 8px;
    }
    .wizard-hint {
        font-size: 0.85rem;
        color: var(--on-surface-variant);
        line-height: 1.5;
    }
    .wizard-counter {
        font-size: 0.8rem;
        color: var(--on-surface-variant);
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .wizard-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        gap: 12px;
    }
    .wizard-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 11px 20px;
        border-radius: var(--radius-md);
        font-size: 0.95rem;
        font-weight: 600;
        text-decoration: none;
        min-height: 44px;
        cursor: pointer;
        border: none;
        transition: background var(--t-fast), transform var(--t-fast);
    }
    .wizard-btn-primary {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 4px 12px rgba(76, 29, 149, 0.20);
    }
    .wizard-btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); }
    .wizard-btn-secondary {
        background: var(--surface);
        color: var(--on-surface);
        border: 1.5px solid var(--outline-variant);
    }
    .wizard-btn-secondary:hover { background: var(--surface-variant); border-color: var(--secondary-light); }
    .wizard-btn .material-symbols-outlined { font-size: 18px; }
</style>

<?= $this->include('partials/footer') ?>
