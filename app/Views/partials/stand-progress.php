<?php
/**
 * Progress bar Pesan Stand (6 step) — partial terpisah dari partials/progress.php.
 *
 * Usage: <?= $this->include('partials/stand-progress', ['current' => 2]) ?>
 *
 * Step 1 = Tentang, 2 = Data Acara, 3 = Pilih Menu, 4 = Ringkasan, 5 = Pembayaran, 6 = Selesai.
 */
$current = (int) ($current ?? 1);
$steps = [
    1 => ['label' => 'Info',      'icon' => 'celebration'],
    2 => ['label' => 'Data Acara', 'icon' => 'edit_calendar'],
    3 => ['label' => 'Menu',       'icon' => 'restaurant_menu'],
    4 => ['label' => 'Ringkasan',  'icon' => 'receipt_long'],
    5 => ['label' => 'Bayar',      'icon' => 'qr_code_2'],
    6 => ['label' => 'Selesai',    'icon' => 'check_circle'],
];
?>
<div class="checkout-progress" aria-label="Langkah booking stand: <?= $current ?> dari <?= count($steps) ?>">
    <div class="checkout-progress-line" aria-hidden="true"></div>
    <?php foreach ($steps as $i => $s):
        $isActive = ($i === $current);
        $isDone   = ($i < $current);
        $stateClass = $isActive ? 'is-active' : ($isDone ? 'is-done' : 'is-pending');
    ?>
        <div class="checkout-progress-step <?= $stateClass ?>" aria-current="<?= $isActive ? 'step' : 'false' ?>">
            <div class="checkout-progress-circle">
                <?php if ($isDone): ?>
                    <span class="material-symbols-outlined" aria-hidden="true">check</span>
                <?php else: ?>
                    <span class="checkout-progress-num"><?= $i ?></span>
                <?php endif; ?>
            </div>
            <span class="checkout-progress-label"><?= esc($s['label']) ?></span>
        </div>
    <?php endforeach; ?>
</div>

<style>
    /* Reuse gaya yang sama dengan partials/progress.php */
    .checkout-progress {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        max-width: 640px;
        margin: 0 auto 32px;
        position: relative;
        padding: 0 8px;
    }
    .checkout-progress-line {
        position: absolute;
        top: 18px;
        left: 8%;
        right: 8%;
        height: 2px;
        background: var(--outline-variant);
        z-index: 0;
    }
    .checkout-progress-step {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        text-align: center;
    }
    .checkout-progress-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        background: var(--surface);
        border: 2px solid var(--outline-variant);
        color: var(--on-surface-variant);
        transition: all var(--t-base);
    }
    .checkout-progress-circle .material-symbols-outlined {
        font-size: 20px;
        font-weight: 700;
    }
    .checkout-progress-label {
        font-size: 0.7rem;
        margin-top: 6px;
        color: var(--on-surface-variant);
        font-weight: 600;
    }
    .checkout-progress-step.is-done .checkout-progress-circle {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }
    .checkout-progress-step.is-done .checkout-progress-label {
        color: var(--primary);
    }
    .checkout-progress-step.is-active .checkout-progress-circle {
        background: var(--surface);
        border-color: var(--primary);
        color: var(--primary);
        box-shadow: 0 0 0 4px rgba(76, 29, 149, 0.12);
    }
    .checkout-progress-step.is-active .checkout-progress-label {
        color: var(--primary);
    }
    @media (max-width: 480px) {
        .checkout-progress-label { font-size: 0.6rem; }
        .checkout-progress-circle { width: 30px; height: 30px; font-size: 0.75rem; }
    }
</style>
