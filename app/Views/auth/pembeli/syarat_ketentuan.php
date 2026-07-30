<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Syarat & Ketentuan — Siomay Dua Putri</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #5731B6;       
            --primary-light: #F4EFFF; 
            --text-main: #1D1A22;
            --text-muted: #6B7280;
            --border-color: #E5E7EB;
            --bg-page: #FBF9FF;
            --t-fast: 200ms ease;
        }

        * { box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: var(--bg-page);
            background-image: url('<?= base_url("bg_2.png") ?>');
            background-size: cover;
            background-position: center;
            color: var(--text-main);
            min-height: 100dvh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .sk-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(87, 49, 182, 0.08);
            max-width: 680px;
            width: 100%;
            position: relative;
        }

        .btn-close {
            position: absolute;
            top: 24px;
            right: 24px;
            width: 36px;
            height: 36px;
            background-color: var(--primary-light);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: background-color var(--t-fast);
        }
        .btn-close:hover { background-color: #E4D8FF; }
        .btn-close .material-symbols-outlined { font-size: 20px; }

        .sk-header {
            text-align: center;
            margin-bottom: 24px;
            padding-right: 40px;
            padding-left: 40px;
        }
        .sk-header h1 {
            color: #2D1A56; 
            margin: 0 0 8px;
            font-size: 1.6rem;
            font-weight: 700;
        }
        .sk-header p {
            color: var(--text-muted);
            margin: 0;
            font-size: 0.85rem;
            line-height: 1.5;
        }

        .sk-content {
            max-height: 380px;
            overflow-y: auto;
            padding-right: 8px;
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sk-content::-webkit-scrollbar { width: 6px; }
        .sk-content::-webkit-scrollbar-track { background: #F1F1F1; border-radius: 10px; }
        .sk-content::-webkit-scrollbar-thumb { background: #D1C4E9; border-radius: 10px; }
        .sk-content::-webkit-scrollbar-thumb:hover { background: var(--primary); }

        .sk-item {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
        }
        .sk-item:last-child { border-bottom: none; padding-bottom: 0; }

        .sk-icon {
            width: 42px;
            height: 42px;
            background-color: var(--primary-light);
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .sk-icon .material-symbols-outlined { font-size: 22px; }

        .sk-text h3 {
            margin: 0 0 4px;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-main);
        }
        .sk-text p {
            margin: 0;
            font-size: 0.82rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .btn-primary {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 48px;
            background: linear-gradient(90deg, #4A1E9E 0%, #6338C4 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            transition: opacity var(--t-fast), transform var(--t-fast);
        }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    </style>
</head>
<body>

    <div class="sk-card">
        <a href="<?= base_url('daftar') ?>" class="btn-close" title="Tutup">
            <span class="material-symbols-outlined">close</span>
        </a>

        <div class="sk-header">
            <h1>Syarat & Ketentuan</h1>
            <p>Dengan mendaftar dan menggunakan layanan Siomay Dua Putri, Anda setuju untuk terikat dengan syarat dan ketentuan berikut.</p>
        </div>

        <div class="sk-content">
            <div class="sk-item">
                <div class="sk-icon"><span class="material-symbols-outlined">assignment</span></div>
                <div class="sk-text">
                    <h3>1. Pemesanan</h3>
                    <p>Pemesanan dapat dilakukan melalui website ini minimal 1 hari sebelum tanggal pengambilan atau pengantaran. Semua pesanan tergantung pada ketersediaan produk.</p>
                </div>
            </div>
            <div class="sk-item">
                <div class="sk-icon"><span class="material-symbols-outlined">payments</span></div>
                <div class="sk-text">
                    <h3>2. Pembayaran</h3>
                    <p>Pembayaran dilakukan melalui QRIS sesuai nominal yang tertera. Pesanan akan diproses setelah pembayaran dikonfirmasi oleh sistem atau admin.</p>
                </div>
            </div>
            <div class="sk-item">
                <div class="sk-icon"><span class="material-symbols-outlined">schedule</span></div>
                <div class="sk-text">
                    <h3>3. Pengambilan & Pengantaran</h3>
                    <p>Untuk pesanan ambil sendiri, harap datang sesuai tanggal yang dipilih. Untuk pengantaran via Maxim, waktu pengantaran mengikuti estimasi dari pihak Maxim.</p>
                </div>
            </div>
            <div class="sk-item">
                <div class="sk-icon"><span class="material-symbols-outlined">package_2</span></div>
                <div class="sk-text">
                    <h3>4. Pembatalan & Perubahan Pesanan</h3>
                    <p>Pembatalan atau perubahan pesanan hanya dapat dilakukan sebelum pembayaran dilakukan. Setelah pembayaran berhasil, pesanan tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="sk-item">
                <div class="sk-icon"><span class="material-symbols-outlined">warning</span></div>
                <div class="sk-text">
                    <h3>5. Produk</h3>
                    <p>Kami berkomitmen untuk menyajikan produk dengan kualitas terbaik. Namun, bentuk, warna, dan rasa dapat sedikit berbeda karena proses produksi harian.</p>
                </div>
            </div>
            <div class="sk-item">
                <div class="sk-icon"><span class="material-symbols-outlined">verified_user</span></div>
                <div class="sk-text">
                    <h3>6. Tanggung Jawab</h3>
                    <p>Siomay Dua Putri tidak bertanggung jawab atas keterlambatan pengantaran yang disebabkan oleh pihak ekspedisi (Maxim) atau keadaan di luar kendali kami.</p>
                </div>
            </div>
            <div class="sk-item">
                <div class="sk-icon"><span class="material-symbols-outlined">lock</span></div>
                <div class="sk-text">
                    <h3>7. Privasi</h3>
                    <p>Data pribadi yang Anda berikan akan kami jaga kerahasiaannya dan hanya digunakan untuk keperluan pemesanan.</p>
                </div>
            </div>
            <div class="sk-item">
                <div class="sk-icon"><span class="material-symbols-outlined">balance</span></div>
                <div class="sk-text">
                    <h3>8. Perubahan Syarat & Ketentuan</h3>
                    <p>Siomay Dua Putri berhak mengubah syarat & ketentuan ini sewaktu-waktu tanpa pemberitahuan terlebih dahulu. Perubahan akan berlaku sejak dipublikasikan di website ini.</p>
                </div>
            </div>
        </div>

        <a href="<?= base_url('daftar') ?>" class="btn-primary">Saya Mengerti</a>
    </div>

</body>
</html>