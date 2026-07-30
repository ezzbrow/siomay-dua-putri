<?= $this->include('partials/header') ?>

<main class="wizard-main">
    <?= $this->include('partials/stand-progress', ['current' => 2]) ?>

    <div class="wizard-heading">
        <h1 class="wizard-title">
            <span class="material-symbols-outlined" aria-hidden="true">edit_calendar</span>
            Data Acara
        </h1>
        <p class="wizard-tagline">Lengkapi informasi acara Anda untuk kami persiapkan.</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="wizard-flash wizard-flash-err" role="alert">
            <span class="material-symbols-outlined" aria-hidden="true">error</span>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="wizard-flash wizard-flash-err" role="alert">
            <span class="material-symbols-outlined" aria-hidden="true">error</span>
            <ul style="margin:0;padding-left:16px;">
                <?php foreach ((array) session()->getFlashdata('errors') as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('pesan-stand/form') ?>" method="post" class="stand-form-card">
        <?= csrf_field() ?>

        <!-- Nama Lengkap -->
        <div class="form-group">
            <label class="form-label" for="nama">
                <span class="material-symbols-outlined form-label-icon" aria-hidden="true">person</span>
                Nama Lengkap Pemesan <span class="form-required">*</span>
            </label>
            <input
                type="text"
                id="nama"
                name="nama"
                class="form-control"
                value="<?= esc(old('nama', $stand_nama)) ?>"
                placeholder="Nama lengkap Anda"
                required
                maxlength="255"
                autocomplete="name"
            >
        </div>

        <!-- Nomor WhatsApp -->
        <div class="form-group">
            <label class="form-label" for="nomor_wa">
                <span class="material-symbols-outlined form-label-icon" aria-hidden="true">phone</span>
                Nomor WhatsApp <span class="form-required">*</span>
            </label>
            <input
                type="tel"
                id="nomor_wa"
                name="nomor_wa"
                class="form-control"
                value="<?= esc(old('nomor_wa', $stand_wa)) ?>"
                placeholder="08xx-xxxx-xxxx"
                required
                maxlength="30"
                autocomplete="tel"
            >
            <div class="form-hint">Admin akan menghubungi nomor ini untuk konfirmasi booking.</div>
        </div>

        <!-- Jenis Acara -->
        <div class="form-group">
            <label class="form-label" for="jenis_acara">
                <span class="material-symbols-outlined form-label-icon" aria-hidden="true">celebration</span>
                Jenis Acara <span class="form-required">*</span>
            </label>
            <select id="jenis_acara" name="jenis_acara" class="form-control" required>
                <option value="">— Pilih Jenis Acara —</option>
                <option value="pernikahan"    <?= old('jenis_acara', $stand_jenis_acara) === 'pernikahan'    ? 'selected' : '' ?>>Pernikahan</option>
                <option value="ulang_tahun"   <?= old('jenis_acara', $stand_jenis_acara) === 'ulang_tahun'   ? 'selected' : '' ?>>Ulang Tahun</option>
                <option value="arisan"        <?= old('jenis_acara', $stand_jenis_acara) === 'arisan'        ? 'selected' : '' ?>>Arisan</option>
                <option value="pengajian"     <?= old('jenis_acara', $stand_jenis_acara) === 'pengajian'     ? 'selected' : '' ?>>Pengajian</option>
                <option value="seminar"       <?= old('jenis_acara', $stand_jenis_acara) === 'seminar'       ? 'selected' : '' ?>>Seminar</option>
                <option value="grand_opening" <?= old('jenis_acara', $stand_jenis_acara) === 'grand_opening' ? 'selected' : '' ?>>Grand Opening</option>
                <option value="lainnya"       <?= old('jenis_acara', $stand_jenis_acara) === 'lainnya'       ? 'selected' : '' ?>>Lainnya</option>
            </select>
        </div>

        <!-- Nama Acara -->
        <div class="form-group">
            <label class="form-label" for="nama_acara">
                <span class="material-symbols-outlined form-label-icon" aria-hidden="true">label</span>
                Nama Acara <span class="form-required">*</span>
            </label>
            <input
                type="text"
                id="nama_acara"
                name="nama_acara"
                class="form-control"
                value="<?= esc(old('nama_acara', $stand_nama_acara)) ?>"
                placeholder="mis. Pernikahan Budi & Sari, HUT ke-50"
                required
                maxlength="255"
            >
        </div>

        <!-- Tanggal Acara -->
        <div class="form-group">
            <label class="form-label" for="tanggal_acara">
                <span class="material-symbols-outlined form-label-icon" aria-hidden="true">event</span>
                Tanggal Acara <span class="form-required">*</span>
            </label>
            <input
                type="date"
                id="tanggal_acara"
                name="tanggal_acara"
                class="form-control"
                value="<?= esc(old('tanggal_acara', $stand_tanggal_acara)) ?>"
                min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                required
            >
            <div class="form-hint">Minimal tanggal besok. Booking hari yang sama tidak tersedia.</div>
        </div>

        <!-- Lokasi Acara -->
        <div class="form-group">
            <label class="form-label" for="lokasi_acara">
                <span class="material-symbols-outlined form-label-icon" aria-hidden="true">location_on</span>
                Lokasi / Alamat Acara <span class="form-required">*</span>
            </label>
            <textarea
                id="lokasi_acara"
                name="lokasi_acara"
                class="form-control form-textarea"
                placeholder="Nama gedung / alamat lengkap acara"
                required
                maxlength="500"
                rows="2"
            ><?= esc(old('lokasi_acara', $stand_lokasi_acara)) ?></textarea>
        </div>

        <!-- Estimasi Jumlah Tamu -->
        <div class="form-group">
            <label class="form-label" for="estimasi_tamu">
                <span class="material-symbols-outlined form-label-icon" aria-hidden="true">groups</span>
                Estimasi Jumlah Tamu
            </label>
            <input
                type="number"
                id="estimasi_tamu"
                name="estimasi_tamu"
                class="form-control"
                value="<?= esc(old('estimasi_tamu', $stand_estimasi_tamu)) ?>"
                placeholder="mis. 150"
                min="1"
                step="1"
            >
            <div class="form-hint">Opsional — membantu kami mempersiapkan jumlah porsi yang tepat.</div>
        </div>

        <!-- Catatan -->
        <div class="form-group">
            <label class="form-label" for="catatan">
                <span class="material-symbols-outlined form-label-icon" aria-hidden="true">notes</span>
                Catatan / Permintaan Khusus
            </label>
            <textarea
                id="catatan"
                name="catatan"
                class="form-control form-textarea"
                placeholder="Permintaan tambahan, preferensi menu, dll. (opsional)"
                maxlength="1000"
                rows="3"
            ><?= esc(old('catatan', $stand_catatan)) ?></textarea>
        </div>

        <div class="form-actions">
            <a href="<?= base_url('pesan-stand') ?>" class="wizard-btn wizard-btn-outline" id="btn-kembali-tentang">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                Kembali
            </a>
            <button type="submit" class="wizard-btn wizard-btn-primary" id="btn-lanjut-menu">
                Lanjut Pilih Menu
                <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
            </button>
        </div>
    </form>
</main>

<style>
    .wizard-main { max-width: 640px; margin: 0 auto; padding: 32px 20px 48px; }
    .wizard-heading { text-align: center; margin-bottom: 28px; }
    .wizard-title {
        font-size: 1.5rem; font-weight: 800; color: var(--on-surface);
        margin: 0 0 8px; display: inline-flex; align-items: center; gap: 10px;
    }
    .wizard-title .material-symbols-outlined { font-size: 28px; color: var(--primary); }
    .wizard-tagline { color: var(--on-surface-variant); margin: 0; font-size: 0.95rem; }

    .wizard-flash {
        padding: 12px 16px; border-radius: var(--radius-md); margin-bottom: 20px;
        display: flex; align-items: flex-start; gap: 8px; font-weight: 500; font-size: 0.9rem;
    }
    .wizard-flash-err { background: var(--err-bg); color: var(--err-fg); border: 1px solid rgba(153,27,27,0.18); }
    .wizard-flash .material-symbols-outlined { font-size: 20px; flex-shrink: 0; margin-top: 1px; }

    .stand-form-card {
        background: var(--surface);
        border: 1px solid var(--outline-variant);
        border-radius: var(--radius-lg);
        box-shadow: var(--card-shadow);
        padding: 28px 24px;
    }

    .form-group { margin-bottom: 20px; }
    .form-label {
        display: flex; align-items: center; gap: 6px;
        font-weight: 600; font-size: 0.9rem; color: var(--on-surface);
        margin-bottom: 7px;
    }
    .form-label-icon { font-size: 18px; color: var(--primary); }
    .form-required { color: #dc2626; }
    .form-control {
        width: 100%; padding: 10px 14px;
        border: 1.5px solid var(--outline-variant);
        border-radius: var(--radius-md);
        font-size: 0.95rem; color: var(--on-surface);
        background: var(--background);
        transition: border-color var(--t-fast), box-shadow var(--t-fast);
        box-sizing: border-box;
        font-family: inherit;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(76, 29, 149, 0.12);
    }
    .form-textarea { resize: vertical; min-height: 72px; }
    select.form-control { cursor: pointer; }
    .form-hint { font-size: 0.78rem; color: var(--on-surface-variant); margin-top: 5px; }

    .form-actions {
        display: flex; gap: 12px; align-items: center;
        margin-top: 28px;
    }
    .wizard-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        padding: 12px 20px; border-radius: var(--radius-md);
        font-size: 0.95rem; font-weight: 700; text-decoration: none;
        min-height: 46px; cursor: pointer; border: none;
        transition: background var(--t-fast), transform var(--t-fast);
        box-sizing: border-box;
        font-family: inherit;
    }
    .wizard-btn-primary {
        flex: 1; background: var(--primary); color: #fff;
    }
    .wizard-btn-primary:hover { background: var(--primary-hover); color: #fff; transform: translateY(-1px); }
    .wizard-btn-outline {
        background: transparent;
        color: var(--on-surface-variant);
        border: 1.5px solid var(--outline-variant);
        flex-shrink: 0;
    }
    .wizard-btn-outline:hover { background: var(--surface-variant); color: var(--on-surface); }
    .wizard-btn .material-symbols-outlined { font-size: 20px; }
</style>

<?= $this->include('partials/footer') ?>
