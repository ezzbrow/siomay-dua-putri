# PRD — Siomay Dua Putri: Sistem Pemesanan & Pembayaran QRIS

**Versi:** 0.5
**Status:** Draft untuk lomba — fokus penilaian utama pada modul transaksi & laporan (lihat bagian 10). Payment gateway sudah diputuskan (lihat bagian 5 & 9), perhitungan pajak dibuat opsional (lihat F18), strategi jaringan demo sudah ditetapkan (bagian 9.3).

---

## 1. Latar Belakang

Siomay Dua Putri adalah UMKM siomay milik keluarga pemilik project ini, beroperasi sejak 2018. Selama ini transaksi sepenuhnya manual: pemesanan lewat telepon, pencatatan di kertas, dan pembayaran cash saja. Sistem ini dibangun untuk mendigitalisasi alur pemesanan dan pembayaran (QRIS) tanpa mengubah cara kerja penjual yang sudah terbiasa manual dan tidak familiar teknologi, sekaligus memenuhi standar penilaian lomba yang menitikberatkan pada **keandalan transaksi** dan **kelengkapan laporan**.

## 2. Tujuan

1. Menyediakan pembayaran QRIS untuk pembeli yang beli di tempat maupun pesan antar/ambil sendiri.
2. Mempromosikan layanan pesan-antar ke pembeli yang datang langsung.
3. Menggantikan pencatatan manual dengan pencatatan transaksi digital yang akurat.
4. Menyediakan laporan transaksi yang dapat diandalkan sebagai alat kontrol bisnis harian bagi penjual.
5. Memenuhi kriteria penilaian lomba: instalasi tanpa error, integrasi QRIS berjalan baik, fitur inti lengkap, antarmuka intuitif, akurasi nominal & laporan, keamanan data, performa cepat, dan stabilitas saat demo.

## 3. Role Sistem

Sistem memiliki **dua role** dengan model akses yang berbeda secara sengaja:

| Role | Autentikasi | Titik akses | Alasan desain |
|---|---|---|---|
| **Pembeli** | Tanpa login (guest) | Scan QR publik yang ditempel di gerobak | Meniru pengalaman pemesanan cepat ala QR-order (mis. Gacoan); mempercepat transaksi, tidak butuh identitas permanen |
| **Admin/Penjual** | Wajib registrasi akun + login | URL privat, disimpan sebagai shortcut "Tambahkan ke Layar Utama" di HP penjual | Data yang diakses (total QRIS, riwayat transaksi, laporan, nomor HP pelanggan) bersifat sensitif dan harus dilindungi |

Pemisahan ini juga yang membuat sistem memenuhi kriteria "Login berhasil" dan "Registrasi pengguna berjalan" dari sisi admin, sementara sisi pembeli tetap tanpa friksi.

**Keputusan final**: pembeli tetap tanpa login/registrasi. Ini bukan fitur yang kurang, melainkan **nilai jual inovasi** sistem — menunjukkan bagaimana teknologi benar-benar membantu UMKM riil tanpa menambah hambatan bagi pembeli jalanan yang terbiasa transaksi cepat. Poin ini sebaiknya disampaikan eksplisit saat presentasi ke juri sebagai keputusan desain yang disengaja, bukan celah yang terlewat.

## 4. Alur Pengguna

### 4.1 Alur Umum
```
Scan QR di gerobak (Pembeli)
        │
        ▼
 Pilihan utama:
 ┌─────────────────┬───────────────────────┐
 │  Bayar QRIS      │  Pesan Antar/Banyak   │
 │  (makan di tempat)│  (min. Rp100.000)    │
 └─────────────────┴───────────────────────┘
```

### 4.2 Jalur A — Bayar QRIS (Makan di Tempat)
1. Tampilkan gambar QRIS merchant, tombol unduh, instruksi pembayaran.
2. Pembeli bayar lewat aplikasi apapun yang mendukung QRIS.
3. Transaksi ini **anonim** — tidak masuk ke riwayat pesanan berbasis pelanggan, tapi tetap **wajib tercatat sebagai bagian dari total omzet harian** untuk kebutuhan laporan (lihat bagian 6), karena dana tetap masuk ke rekening/merchant QRIS yang sama.

### 4.3 Jalur B — Pesan Antar/Pesanan Banyak
1. **Etalase**: Siomay, Batagor, Lumpia (Frozen/Digoreng khusus Lumpia). Produk nonaktif tampil abu-abu, tidak bisa dipilih.
2. Kontrol jumlah (+/-) per produk, bisa lintas kategori dalam satu transaksi.
3. **Kolom catatan** untuk permintaan rasa saja — sistem menampilkan instruksi eksplisit bahwa kolom ini bukan untuk alamat.
4. **Validasi minimum Rp100.000** — tombol lanjut nonaktif dan muncul peringatan jika belum tercapai.
5. Ringkasan pesanan menampilkan **subtotal → pajak (opsional, default 10% jika diaktifkan admin) → total bayar** — total inilah yang dikirim ke Midtrans untuk dibuatkan kode QRIS.
6. Pilih metode penerimaan: **Ambil Sendiri** atau **Diantar**.

#### 4.3.a Ambil Sendiri
1. Isi nama + nomor HP (nomor HP **wajib**, dipakai admin untuk kontak balik dan tercatat di dashboard).
2. Tampilkan alamat & lokasi UMKM di peta.
3. Lanjut ke **halaman pembayaran QRIS**.
4. Setelah pembayaran terverifikasi **Lunas** → pesanan otomatis masuk ke dashboard admin.
5. Tampilkan **halaman bukti transaksi** (lihat bagian 5).
6. Layar status "Menunggu pesanan Anda" + tombol "Hubungi Penjual" (WA) dengan pesan otomatis: *"Jam berapa bisa saya ambil?"*

#### 4.3.b Diantar
1. Isi nama penerima, nomor HP, alamat lengkap, pilih lokasi via peta.
2. Lanjut ke halaman pembayaran QRIS.
3. Setelah **Lunas** → masuk ke dashboard admin.
4. Tampilkan halaman bukti transaksi.
5. Tombol "Hubungi Penjual" (WA) dengan pesan otomatis: *"Kapan pesanan saya diantarkan?"*
6. Di sisi admin: tombol "Pesan Maxim" membuka Maxim dengan alamat pelanggan sudah terisi (sandbox API Maxim).

### 4.4 Navigasi
Setiap halaman punya tombol Kembali & Lanjut; data yang sudah diisi tidak hilang saat pembeli bolak-balik halaman.

## 5. Modul Transaksi (prioritas penilaian tinggi)

Karena lomba menitikberatkan pada keandalan transaksi, modul ini dirinci lebih detail dari versi PRD sebelumnya:

| Komponen | Detail |
|---|---|
| **Gateway pembayaran** | **Midtrans (mode Sandbox)** — dipilih karena sandbox bisa langsung dipakai untuk pengembangan & demo lomba tanpa menunggu verifikasi produksi, dokumentasi paling lengkap untuk CodeIgniter, dan mendukung akun individual (KTP + rekening pribadi) untuk jalur produksi setelah lomba. Duitku menjadi opsi cadangan jika ada kendala di Midtrans (lihat bagian 9) |
| **Biaya MDR** | Ditanggung **merchant (papa)**, bukan pembeli — dilarang keras dibebankan ke pembeli/surcharge sesuai PBI No. 23/6/PBI/2021 Pasal 52. Untuk kategori Usaha Mikro (UMI): **0%** untuk transaksi ≤Rp500.000, **0,3%** di atasnya. Karena minimum order Rp100.000, sebagian besar transaksi kemungkinan kena MDR 0% — perlu dikonfirmasi kategori usaha saat registrasi Midtrans |
| **Perhitungan pajak** | **Opsional** — admin bisa mengaktifkan/nonaktifkan lewat Pengaturan. Jika aktif: Subtotal → Pajak (persentase dapat diatur, default 10%) → Total bayar, ditampilkan sebagai baris terpisah di checkout & struk. **Total setelah pajak** (jika aktif) atau **subtotal** (jika nonaktif) yang dikirim ke Midtrans untuk dibuatkan kode QRIS. Berbeda dari MDR, pajak ini SAH ditampilkan ke pembeli (mirip PB1/pajak restoran, bukan biaya payment gateway) |
| **Status transaksi** | `pending` → `lunas` / `gagal` / `kedaluwarsa`. Hanya transaksi berstatus `lunas` yang masuk dashboard admin dan dihitung dalam laporan |
| **Validasi nominal** | Sistem menghitung ulang total dari data item + harga di database (bukan dari input klien) sebelum membuat kode QRIS, untuk mencegah manipulasi nominal dari sisi pembeli |
| **Bukti transaksi** | Setelah status `lunas`, sistem menampilkan halaman struk berisi: nomor transaksi unik, daftar item & jumlah, catatan, metode penerimaan, total bayar, waktu transaksi. Bisa di-screenshot/unduh sebagai PDF sederhana |
| **Idempotensi** | Satu kode pembayaran QRIS hanya bisa dipakai untuk satu transaksi; sistem menolak pemrosesan ganda jika notifikasi pembayaran diterima lebih dari sekali |
| **Riwayat transaksi (admin)** | Daftar seluruh transaksi `lunas`: nama, nomor HP, item, catatan, metode, alamat (jika diantar), total, waktu — bisa difilter per tanggal/status |
| **Keamanan transaksi** | Endpoint pembayaran diverifikasi lewat token/signature dari payment gateway (bukan sekadar redirect client-side), untuk mencegah pemalsuan status "lunas" dari sisi klien |

## 6. Modul Laporan Transaksi (prioritas penilaian tinggi)

Modul baru — sebelumnya belum ada di draft awal, ditambahkan khusus untuk memenuhi kriteria "laporan transaksi dapat dihasilkan".

| Fitur | Detail |
|---|---|
| **Ringkasan harian** | Total omzet hari ini, jumlah transaksi, breakdown Bayar-di-Tempat vs Pesan Antar |
| **Filter rentang tanggal** | Admin bisa pilih rentang tanggal (hari ini, minggu ini, custom range) untuk melihat total omzet & jumlah transaksi pada periode tersebut |
| **Breakdown per metode** | Total & jumlah transaksi untuk: Bayar di Tempat, Ambil Sendiri, Diantar |
| **Breakdown produk terlaris** | Jumlah terjual per produk (Siomay/Batagor/Lumpia) dalam periode yang dipilih — berguna untuk kontrol stok |
| **Export laporan** | Unduh laporan sebagai PDF atau Excel/CSV untuk periode yang dipilih (memudahkan pembukuan manual penjual jika dibutuhkan) |
| **Tampilan ringkas untuk admin non-teknis** | Angka besar & jelas (total omzet hari ini) di bagian paling atas dashboard, bukan tabel rumit — sesuai kebutuhan papa yang bukan pengguna teknologi mahir |

## 7. Registrasi & Login Admin

1. **Registrasi (setup awal, sekali saja)**: form isi nama toko, username, password, nomor HP admin.
2. **Login**: username + password, dengan opsi "tetap masuk" (remember me) supaya admin tidak perlu login ulang tiap buka shortcut di HP.
3. **Proteksi**: password di-hash (bukan disimpan plain text), rate-limit percobaan login untuk mencegah brute force sederhana.
4. **Lupa password**: mekanisme reset minimal (misal lewat kombinasi keamanan sederhana), karena tidak ada email institusional — perlu didiskusikan mekanismenya (lihat bagian 9).

## 8. Fitur & Requirement Lengkap

| # | Fitur | Role |
|---|---|---|
| F1 | Landing page via QR, tanpa login | Pembeli |
| F2 | Bayar QRIS di tempat | Pembeli |
| F3 | Etalase menu dinamis (aktif/nonaktif per produk) | Pembeli / Admin (kelola) |
| F4 | Keranjang lintas kategori + validasi minimum Rp100.000 | Pembeli |
| F5 | Checkbox varian produk (Lumpia) | Pembeli |
| F6 | Kolom catatan pesanan dengan instruksi jelas | Pembeli |
| F7 | Pilihan metode penerimaan (Ambil Sendiri/Diantar) | Pembeli |
| F8 | Pembayaran QRIS untuk pesan antar (gerbang sebelum masuk dashboard) | Pembeli |
| F9 | Halaman bukti transaksi/struk | Pembeli |
| F10 | Tombol hubungi penjual via WA dengan pesan otomatis | Pembeli |
| F11 | Registrasi & login admin | Admin |
| F12 | Dashboard: pesanan berstatus lunas real-time | Admin |
| F13 | Riwayat transaksi dengan filter | Admin |
| F14 | Modul laporan (ringkasan, filter tanggal, export) | Admin |
| F15 | Tombol hubungi pelanggan via WA | Admin |
| F16 | Tombol "Pesan Maxim" dengan alamat otomatis terisi | Admin |
| F17 | Navigasi back persisten tanpa kehilangan data form | Pembeli |
| F18 | Perhitungan pajak opsional (aktif/nonaktif + persentase diatur admin) | Pembeli (tampil jika aktif) / Admin (atur) |

## 9. Keputusan Terbuka / Perlu Riset Lanjutan

### 9.1 Sudah diputuskan — Payment gateway QRIS

Hasil riset perbandingan Midtrans, Xendit, Duitku, Tripay, dan QRIS aplikasi merchant biasa (GoPay Merchant/native bank):

| Provider | Syarat akun individu | Kecepatan approval | Catatan |
|---|---|---|---|
| Midtrans | KTP + buku tabungan pribadi | Sandbox langsung aktif; produksi 1–14 hari | Dokumentasi paling lengkap, banyak referensi CodeIgniter |
| Duitku | KTP (kebijakan berubah-ubah, beberapa laporan kini minta NPWP) | Dilaporkan cepat, <3 hari | Opsi cadangan yang solid |
| Xendit | Ada opsi individual, tapi laporan penolakan tanpa alasan jelas untuk akun personal | Bervariasi | Kurang konsisten untuk individu |
| Tripay | KTP, ada laporan proses ribet | — | Ada laporan sedang menutup pendaftaran merchant baru — berisiko untuk lomba |
| GoPay Merchant/QRIS native | KTP, aktif <10 menit | Instan | Hanya notifikasi app, **bukan API developer** — tidak bisa auto-update status di dashboard web |

**Keputusan**: pakai **Midtrans mode Sandbox** sebagai basis pengembangan dan demo lomba. Sandbox mensimulasikan seluruh alur (bikin kode QRIS → callback/webhook status `pending`→`settlement`) sama persis seperti produksi tanpa uang sungguhan berpindah, sehingga tidak perlu menunggu verifikasi dokumen sebelum hari-H. Pengajuan verifikasi akun individual (KTP + rekening papa) untuk mode produksi bisa diajukan paralel, tidak menghalangi pengembangan. **Duitku** disiapkan sebagai cadangan jika ada kendala teknis/approval di Midtrans. **Kategori usaha: Usaha Mikro (UMI)** — dikonfirmasi, sehingga MDR 0% untuk transaksi ≤Rp500.000 dan 0,3% di atasnya.

### 9.2 Masih terbuka

1. **Skema export laporan** — format PDF/Excel mana yang diprioritaskan untuk demo lomba. **(Sudah diputuskan, lihat bagian 6)**
2. **Definisi "grey out" etalase** — toggle manual admin, otomatis berdasarkan jam operasional, atau kombinasi.
3. **Stack teknis final** — CodeIgniter 4 (sama dengan project sebelumnya), database, hosting untuk keperluan webhook pembayaran Midtrans.
4. **Format demo ke panitia lomba** — apakah panitia mensyaratkan demo transaksi QRIS secara live-online, atau menerima video rekaman sebagai cadangan jika ada kendala jaringan di lokasi (lihat bagian 9.3 & 9.4).

### 9.3 Strategi jaringan saat demo lomba

**Keputusan**: aplikasi di-deploy ke hosting publik (Railway/campus/alternatif lain) **sebelum** hari demo — bukan dijalankan lokal dari laptop. Dengan begitu, koneksi internet yang dipakai saat demo (baik WiFi venue maupun **hotspot dari HP ke laptop**) hanya berfungsi sebagai akses klien ke aplikasi yang sudah live, bukan sebagai jalur masuk webhook Midtrans. Ini membuat hotspot HP **aman dan justru lebih dapat diandalkan** dibanding WiFi venue/kampus yang kadang membatasi koneksi keluar via firewall institusi. Yang perlu diperhatikan hanya: pastikan sinyal seluler stabil di lokasi demo (venue ramai bisa memperlambat koneksi data), dan siapkan WiFi venue sebagai cadangan jika hotspot bermasalah.

### 9.4 API Maxim — dikonfirmasi tersedia

User telah melakukan riset mandiri dan mengonfirmasi tersedia sandbox untuk integrasi Maxim. Item ini dipindahkan dari "keputusan terbuka" ke "siap diimplementasikan" — dokumentasi teknis persisnya (endpoint, autentikasi, format payload alamat) akan dirujuk langsung saat implementasi di Claude Code.

### 9.5 Ketergantungan internet untuk fitur pembayaran — batasan yang perlu dipahami

**Pertanyaan penting yang perlu dikonfirmasi ke panitia lomba**: apakah demo *wajib* live-online, atau boleh pakai video rekaman sebagai cadangan?

Faktanya secara teknis:
- **Fitur QRIS/Midtrans TIDAK BISA berjalan tanpa internet sama sekali** — termasuk mode sandbox, karena Midtrans adalah layanan cloud pihak ketiga. Tidak ada cara menjalankan generate-kode-QRIS atau callback status pembayaran secara lokal/offline murni. ini bukan soal localhost vs hosting publik, tapi soal koneksi ke server Midtrans itu sendiri yang wajib ada.
- **Fitur lain (etalase, dashboard admin, riwayat transaksi, laporan) BISA berjalan sepenuhnya offline/localhost** karena tidak bergantung pada layanan eksternal.
- Jadi kalau lokasi demo benar-benar tanpa internet sama sekali (bukan sekadar tidak stabil), maka bagian "Integrasi QRIS berjalan dengan baik" tidak bisa didemonstrasikan secara live — perlu disiapkan **video rekaman cadangan** dari sesi testing sebelumnya yang menunjukkan alur pembayaran lengkap, untuk dipakai kalau kondisi darurat (bukan pengganti utama, karena juri kemungkinan lebih menghargai demo live).
- **Rekomendasi**: pastikan minimal ada satu sumber internet yang bisa diandalkan saat demo (hotspot HP sebagai andalan, WiFi venue sebagai cadangan atau sebaliknya) — kemungkinan benar-benar tanpa internet sama sekali di lokasi demo sangat kecil, tapi video cadangan tetap baik disiapkan sebagai mitigasi risiko.

## 10. Pemetaan ke Kriteria Penilaian Lomba

| Kriteria penilaian | Dipenuhi oleh |
|---|---|
| Instalasi & jalan tanpa error | Testing menyeluruh sebelum demo |
| Integrasi QRIS/dompet digital berjalan baik | Modul Transaksi (bagian 5) |
| Fitur utama lengkap (registrasi, login, pembayaran, laporan) | F1–F17 |
| Antarmuka intuitif | Alur QR tanpa login untuk pembeli, dashboard ringkas untuk admin |
| Nominal & laporan akurat | Validasi nominal server-side, perhitungan pajak eksplisit (F18), modul laporan (bagian 6) |
| Autentikasi, proteksi data, validasi transaksi | Bagian 5 & 7 |
| Respons cepat, tanpa lag | Kebutuhan non-fungsional performa (perlu ditegaskan saat implementasi) |
| Stabil saat demo, tidak crash/kehilangan data | Testing end-to-end sebelum demo, idempotensi transaksi |

## 11. Identitas Visual

- **Nama brand**: Siomay Dua Putri (sejak 2018)
- **Palet warna**: ungu muda (latar utama `#C9A8E0`), biru (`#1D4ED8`, wordmark "Siomay"), merah (`#DC2626`, wordmark "Dua Putri" & aksen), kuning (`#FACC15`/`#F5B301`, aksen highlight)
- **Logo**: emblem bulat berisi ilustrasi keranjang steamer dengan dumpling, sudah dibuat dalam draft awal — siap diekspor ke aset final (SVG/PNG) saat implementasi.

---

*Perubahan utama dari versi 0.1 ke 0.2: penambahan struktur 2 role, modul transaksi diperkuat (validasi server-side, idempotensi, bukti transaksi), modul laporan transaksi baru, registrasi & login admin, dan pemetaan eksplisit ke kriteria penilaian lomba.*

*Perubahan utama dari versi 0.2 ke 0.3: keputusan final payment gateway (Midtrans Sandbox, Duitku sebagai cadangan) berdasarkan riset perbandingan provider, bagian 9 dipecah jadi "sudah diputuskan" dan "masih terbuka".*

*Perubahan utama dari versi 0.3 ke 0.4: koreksi kategori MDR (Usaha Mikro 0%/0,3%, bukan 0,7% yang berlaku untuk kategori lebih besar — MDR dilarang dibebankan ke pembeli per PBI 23/6/PBI/2021), penambahan fitur perhitungan pajak (F18) sebagai baris terpisah di checkout & struk sesuai kebutuhan peraturan lomba.*

*Perubahan utama dari versi 0.4 ke 0.5: pajak dijadikan fitur opsional (toggle admin), keputusan final pembeli tetap tanpa registrasi/login dengan alasan sebagai nilai jual inovasi, penambahan strategi jaringan demo (bagian 9.3) yang mengonfirmasi hotspot HP aman dipakai selama aplikasi sudah di-hosting publik.*
