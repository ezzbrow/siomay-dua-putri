# CLAUDE.md — Siomay Dua Putri

> **WAJIB dibaca ulang di setiap sesi baru sebelum mulai kerja apa pun.**
> Ini proyek baru, terpisah total dari EcoPalu/GasKerja. Jangan bawa konteks/kode/pola dari proyek lain kecuali disebutkan eksplisit di file ini.

---

## 1. Latar Belakang Bisnis

Siomay Dua Putri adalah UMKM siomay milik keluarga pemilik proyek, beroperasi sejak 2018. Selama ini transaksi 100% manual: pemesanan lewat telepon, catatan di kertas, pembayaran cash saja.

Sistem ini dibangun untuk mendigitalisasi alur pemesanan dan pembayaran (QRIS) **tanpa mengubah cara kerja penjual** yang tidak familiar teknologi — sekaligus memenuhi kriteria penilaian lomba yang menitikberatkan pada **keandalan transaksi** dan **kelengkapan laporan**.

Ini proyek lomba **solo** (bukan tim), diselenggarakan oleh BRIDA dan Bank Indonesia. Fokus penilaian utama: modul transaksi & laporan.

### 1.1 Menu & Harga (Final)

| Produk | Kategori | Harga | Varian |
|---|---|---|---|
| Somay Sapi | Somay Sapi | Rp1.000 / pcs | — |
| Lumpia | Lumpia | Rp2.000 / pcs | Frozen / Digoreng |
| Pentol Goreng | Pentol Goreng | Rp1.000 / pcs | — |

**Catatan**: ini menggantikan daftar kategori awal (Siomay/Batagor/Lumpia) yang ada di draft PRD sebelumnya — Batagor dihapus, digantikan Pentol Goreng, dan nama "Siomay" diubah jadi "Somay Sapi" untuk konsistensi dengan menu asli UMKM.

### 1.2 Referensi Desain Visual (dari proyek frontend tim)

Tim membuat proyek CI4 paralel (`somay-order-app`) berisi desain visual (Tailwind CSS + Material Symbols icons, design token seperti `on-surface`/`primary`/`rounded-card`). **Hanya bagian visual/styling-nya yang dipakai sebagai referensi** untuk halaman **etalase produk** — struktur kode (controller/model/migration) proyek itu TIDAK dipakai langsung karena skema datanya beda dari §9. Styling diterapkan ulang manual ke view CI4 proyek ini, menyesuaikan data dari skema `produk`/`pesanan` sendiri.

---

## 2. Dua Role & Kenapa Desainnya Begitu

> **⚠️ REVISI 28 Juli 2026 (v1)**: Keputusan awal "pembeli tanpa login" **DIBATALKAN**. Ketentuan lomba mengharuskan sistem punya fitur registrasi & login untuk pengguna.
> **⚠️ REVISI 28 Juli 2026 (v2, lebih lanjut)**: Jalur A (Bayar di Tempat) **DIHAPUS TOTAL** (lihat §3) — jadi pengecualian "tidak perlu login untuk Jalur A" di v1 sudah tidak relevan lagi. **Semua pembeli wajib login**, tanpa kecuali.

| Role | Autentikasi | Titik akses | Alasan desain |
|---|---|---|---|
| **Pembeli** | **Wajib registrasi + login untuk semua pemesanan** (browsing etalase tetap bebas tanpa login, login baru diwajibkan pas checkout) | Scan QR publik di gerobak → diarahkan login/daftar dulu begitu mau checkout | Ketentuan lomba mewajibkan ada fitur regis & login pengguna; akun juga dipakai untuk riwayat pesanan pembeli (F20) |
| **Admin/Penjual** | Wajib registrasi + login | URL privat, shortcut "Add to Home Screen" di HP penjual | Data yang diakses (total QRIS, riwayat transaksi, nomor HP pelanggan) sensitif |

**Keputusan final (terbaru)**: pembeli **wajib** punya akun untuk memesan — tidak ada lagi jalur tanpa akun sejak Jalur A dihapus. Browsing menu/etalase tetap bebas tanpa login, tapi **login diwajibkan sebelum checkout** (submit pesanan, baik pesanan reguler §3.2 maupun pesanan acara §3.3) dan untuk mengakses riwayat pesanan sendiri.

---

## 3. Alur Lengkap

> **⚠️ REVISI 28 Juli 2026**: Jalur A (Bayar QRIS di Tempat/dine-in) **DIHAPUS TOTAL**. Sekarang cuma ada dua jalur: **Pesan Antar/Banyak** (eks-Jalur B, sekarang satu-satunya jalur pemesanan produk reguler) dan **Pesanan Acara/Kegiatan** (jalur baru, custom nego). Konsekuensinya: **SEMUA pemesanan sekarang wajib login** — tidak ada lagi jalur tanpa akun.

### 3.1 Alur Umum
Scan QR di gerobak → pilih salah satu:
- **Pesan Antar/Banyak** (minimum Rp100.000) — checkout otomatis via QRIS, lihat §3.2
- **Pesanan Acara/Kegiatan** — form inquiry, admin follow-up manual via WA, lihat §3.3

~~Bayar QRIS (makan di tempat)~~ — **DIHAPUS**, lihat catatan revisi di atas.

### 3.2 Pesan Antar/Pesanan Banyak (eks-Jalur B)

**Berlaku pre-order untuk kedua metode (Ambil Sendiri maupun Diantar) — sistem TIDAK menerima pesanan untuk hari yang sama.**

**Wajib login untuk semua pemesanan** (lihat §2, REVISI) — browsing etalase & isi keranjang tetap bebas tanpa login, tapi begitu mau lanjut ke tahap checkout (setelah validasi minimum tercapai), sistem mewajibkan pembeli login/daftar dulu sebelum bisa lanjut ke pemilihan tanggal.

1. **Etalase**: Somay Sapi, Lumpia, Pentol Goreng (varian Frozen/Digoreng khusus Lumpia). Produk nonaktif tampil abu-abu, tidak bisa dipilih.
2. Kontrol jumlah (+/-) per produk, bisa lintas kategori dalam satu transaksi.
3. **Kolom catatan** untuk permintaan rasa saja — instruksi eksplisit bahwa kolom ini BUKAN untuk alamat.
4. **Validasi minimum Rp100.000** — tombol lanjut nonaktif + peringatan jika belum tercapai.
5. **Wajib login/registrasi** — kalau pembeli belum login, redirect ke halaman login/daftar. Setelah berhasil, kembali otomatis ke keranjang (isi keranjang dipertahankan, jangan hilang).
6. **Pilih tanggal pesanan dibutuhkan** via kalender — hanya tanggal **setelah hari ini** yang bisa dipilih (H+1 minimal, tidak menerima pesanan untuk hari yang sama).
7. Sistem menampilkan **ringkasan pesanan**: subtotal → pajak (opsional, default 10% jika admin aktifkan) → total, beserta tanggal kebutuhan yang dipilih — total inilah yang dikirim ke Midtrans untuk generate kode QRIS.
8. Pilih metode penerimaan: **Ambil Sendiri** atau **Diantar**.

**3.2.a Ambil Sendiri**
1. Isi nama + nomor HP (nomor HP wajib) — bisa auto-fill dari data akun pembeli.
2. Tampilkan alamat & lokasi UMKM di peta.
3. Lanjut ke halaman pembayaran QRIS.
4. Setelah status `lunas` → pesanan otomatis masuk dashboard admin (dengan tanggal kebutuhan tampil untuk perencanaan produksi).
5. Tampilkan halaman bukti transaksi.
6. Layar status "Menunggu pesanan Anda" + tombol WA otomatis: *"Jam berapa bisa saya ambil?"*

**3.2.b Diantar**
1. Isi nama penerima, nomor HP, alamat lengkap, pilih lokasi via peta.
2. Lanjut ke pembayaran QRIS.
3. Setelah `lunas` → masuk dashboard admin (dengan tanggal kebutuhan tampil untuk perencanaan produksi).
4. Tampilkan halaman bukti transaksi.
5. Tombol WA otomatis: *"Kapan pesanan saya diantarkan?"*
6. Di sisi admin: tombol "Pesan Maxim" membuka Maxim dengan alamat pelanggan sudah terisi (sandbox API Maxim — dikonfirmasi tersedia, lihat §7).

### 3.3 Pesanan Acara/Kegiatan (Jalur C, BARU — F21)

Untuk pesanan skala besar/custom (mis. bawa stand ke acara/hajatan/festival) — **BUKAN checkout otomatis**, karena kuantitas & harga perlu dinego manual antara admin dan pemesan.

**Referensi desain**: kompetitor "Siomay Leeloo" (siomayleeloo.com) punya landing page sejenis — banner foto makanan, judul ajakan ("siap meramaikan acara Anda"), daftar kategori acara dalam bentuk bullet list, tombol CTA merah "PESAN SEKARANG". Pola ini dipakai sebagai referensi visual/struktur untuk halaman F21, disesuaikan dengan identitas visual §12.

1. Pembeli (wajib login, sama seperti §3.2) isi form:
   - **Jenis acara** (dropdown/pilihan, bukan teks bebas) — kategori: Ulang Tahun, Pernikahan, Acara Perusahaan, Pembukaan Kantor/Toko, Arisan, Acara Keagamaan/Sosial, Acara Kedukaan, Lainnya (kalau pilih "Lainnya", muncul field teks bebas)
   - Nama acara
   - Tanggal acara
   - Lokasi acara
   - Estimasi jumlah porsi/tamu
   - Catatan detail permintaan
2. Submit form **TIDAK langsung checkout/bayar** — sistem simpan sebagai catatan permintaan (status awal: `baru`) dan generate pesan WA otomatis berisi ringkasan permintaan, lalu redirect pembeli ke WhatsApp admin (mirip pola tombol WA di F10/F15).
3. Admin lihat daftar permintaan acara ini di dashboard (bagian dari F21, sisi admin) — bisa update status (`baru` → `dihubungi` → `deal` / `batal`) secara manual setelah nego selesai di luar sistem (lewat WA/telepon).
4. Kalau deal, admin **tidak wajib** membuatkan record di tabel `pesanan`/`transaksi` standar (karena pembayaran mungkin di luar Midtrans, misal transfer manual/cash di lokasi acara) — cukup update status `pesanan_acara` jadi `deal`. Ini keputusan sadar untuk menyederhanakan, bukan celah — pesanan acara sifatnya custom/negosiasi, beda dari alur checkout produk reguler.

### 3.4 Navigasi
Setiap halaman punya tombol Kembali & Lanjut; data yang sudah diisi **tidak boleh hilang** saat pembeli bolak-balik halaman.

### 3.5 Riwayat Pesanan Pembeli (F20)
Pembeli yang sudah login bisa lihat riwayat pesanan miliknya sendiri (nomor pesanan, tanggal kebutuhan, status, total) — halaman sederhana, tidak perlu fitur canggih, cukup daftar & detail per pesanan. Cakupan: pesanan reguler (§3.2) saja, pesanan acara (§3.3) punya halaman terpisah kalau dibutuhkan (opsional, tidak prioritas).

---

## 4. Modul Transaksi (prioritas penilaian tinggi)

| Komponen | Detail |
|---|---|
| **Gateway** | **Midtrans mode Sandbox**. Duitku sebagai cadangan jika ada kendala. |
| **Biaya MDR** | Ditanggung merchant (papa), TIDAK BOLEH dibebankan ke pembeli (PBI No. 23/6/PBI/2021 Pasal 52). Kategori UMI: 0% untuk transaksi ≤Rp500.000, 0,3% di atasnya. |
| **Pajak** | Opsional, toggle di Pengaturan admin. Jika aktif: Subtotal → Pajak (% diatur admin, default 10%) → Total, tampil sebagai baris terpisah di checkout & struk. Total setelah pajak (atau subtotal jika nonaktif) yang dikirim ke Midtrans. Beda dari MDR — pajak ini SAH ditampilkan ke pembeli (mirip PB1). |
| **Status transaksi** | `pending` → `lunas` / `gagal` / `kedaluwarsa`. Hanya `lunas` yang masuk dashboard & laporan. |
| **Validasi nominal** | Hitung ulang total dari item + harga di **database** (bukan input klien) sebelum generate kode QRIS — cegah manipulasi nominal. |
| **Bukti transaksi** | Setelah `lunas`: halaman struk (nomor transaksi unik, item, catatan, metode, total, waktu), bisa di-screenshot/unduh PDF sederhana. |
| **Idempotensi** | Satu kode QRIS hanya untuk satu transaksi; tolak proses ganda jika notifikasi pembayaran diterima >1 kali. |
| **Riwayat transaksi (admin)** | Daftar seluruh transaksi `lunas` — filter per tanggal/status. |
| **Keamanan** | Endpoint pembayaran diverifikasi via token/signature dari Midtrans (bukan sekadar redirect client-side) — cegah pemalsuan status "lunas" dari sisi klien. |

---

## 5. Modul Laporan Transaksi & Keuangan (prioritas penilaian tinggi)

| Fitur | Detail |
|---|---|
| Ringkasan harian | Total omzet hari ini, jumlah transaksi |
| Filter rentang tanggal | Hari ini / minggu ini / custom range |
| Breakdown per metode | Ambil Sendiri, Diantar (Bayar di Tempat DIHAPUS, lihat §3) |
| Breakdown produk terlaris | Jumlah terjual per produk dalam periode dipilih |
| Export laporan | PDF atau Excel/CSV |
| Tampilan | Angka besar & jelas di atas dashboard — admin non-teknis (papa) |

### 5.1 Pencatatan Keuangan / Pengeluaran (BARU, REVISI 28 Juli 2026 — F22)

Ketentuan lomba mengharuskan ada pencatatan keuangan, bukan cuma laporan penjualan. Tambahan modul:

| Fitur | Detail |
|---|---|
| CRUD pengeluaran | Admin catat pengeluaran manual: tanggal, kategori (bahan baku / operasional / lainnya), deskripsi, jumlah |
| Laporan laba-rugi | Total pemasukan (dari transaksi `lunas`) **dikurangi** total pengeluaran pada periode yang sama = laba/rugi bersih |
| Filter periode | Sama seperti laporan transaksi — hari ini / minggu ini / custom range |
| Tampilan di dashboard | Ringkasan laba-rugi ditampilkan berdampingan dengan ringkasan omzet, supaya admin (papa) langsung lihat untung-rugi tanpa hitung manual |

**Catatan implementasi**: ini modul sederhana (bukan sistem akuntansi lengkap) — cukup CRUD pengeluaran + kalkulasi pemasukan-pengeluaran, tidak perlu double-entry bookkeeping atau kategori pajak pengeluaran yang rumit.

---

## 6. Registrasi & Login Admin

1. Registrasi (setup awal, sekali saja): nama toko, username, password, nomor HP.
2. Login: username + password + opsi "tetap masuk".
3. Password di-hash, rate-limit percobaan login.
4. **Reset password**: tidak ada mekanisme di aplikasi — lihat §8.2 (skip, fallback manual via database).

---

## 7. Keputusan yang Sudah Final (jangan diubah tanpa diskusi ulang)

- **Payment gateway**: Midtrans Sandbox (basis dev & demo). Duitku = cadangan. Kategori usaha: **UMI** (MDR 0%/0,3%).
- **Pajak**: opsional, toggle admin — bukan wajib aktif.
- **Pembeli wajib registrasi + login untuk SEMUA pemesanan** (REVISI 28 Juli 2026, membatalkan keputusan awal "tanpa login"): mengikuti ketentuan lomba. Jalur A (Bayar di Tempat) sudah DIHAPUS total, jadi tidak ada lagi pengecualian. Lihat §2 & §3.
- **Jalur A (Bayar di Tempat/dine-in) DIHAPUS** (REVISI 28 Juli 2026): sekarang hanya ada Pesan Antar/Banyak (§3.2) dan Pesanan Acara/Kegiatan (§3.3, baru).
- **Pesanan Acara/Kegiatan** (REVISI 28 Juli 2026, F21): alur custom, BUKAN checkout otomatis — pembeli isi form, sistem generate link WA ke admin untuk nego harga manual. Tidak wajib buat record `pesanan`/`transaksi` standar kalau deal (cukup update status di `pesanan_acara`).
- **Pencatatan keuangan/pengeluaran** (REVISI 28 Juli 2026, F22): modul baru di dashboard admin — CRUD pengeluaran + laporan laba-rugi (pemasukan dari transaksi lunas dikurangi pengeluaran), sesuai ketentuan lomba.
- **Strategi jaringan demo**: aplikasi **di-deploy ke hosting publik SEBELUM hari demo** (bukan localhost). Hotspot HP aman dipakai sebagai koneksi klien karena webhook Midtrans tidak lewat jalur ini lagi. WiFi venue sebagai cadangan.
- **API Maxim**: dikonfirmasi tersedia sandbox-nya — siap diimplementasikan, rujuk dokumentasi teknis persis (endpoint, auth, payload alamat) saat implementasi.
- **Pre-order wajib**: sistem TIDAK menerima pesanan untuk hari yang sama, berlaku untuk kedua metode (Ambil Sendiri & Diantar). Pembeli wajib pilih tanggal kebutuhan (minimal H+1) via kalender sebelum memilih metode penerimaan. Tidak berlaku untuk jalur Bayar QRIS di Tempat (itu transaksi langsung/walk-in).
- **Stack**: CodeIgniter 4.7.4, XAMPP, path lokal `A:\myrealxampp\htdocs\somayduaputri`.
- **Fitur QRIS/Midtrans TIDAK BISA jalan tanpa internet sama sekali** (termasuk sandbox) — bukan soal localhost vs hosting, tapi koneksi ke server Midtrans wajib ada. Fitur lain (etalase, dashboard, riwayat, laporan) bisa offline/localhost.

---

## 8. Pertanyaan Terbuka — Perlu Keputusan Pengguna Sebelum Diimplementasikan

1. ~~Definisi "grey out" etalase~~ — **SUDAH DIPUTUSKAN, lihat §8.1.**
2. ~~Mekanisme reset password admin~~ — **SUDAH DIPUTUSKAN, lihat §8.2.**
3. **Format demo ke panitia lomba**: apakah wajib live-online, atau video rekaman cadangan diterima jika ada kendala jaringan?

### 8.1 Keputusan — Definisi "Grey Out" Etalase (final)

**Kombinasi**: produk tampil abu-abu (tidak bisa dipilih) jika **salah satu** dari dua kondisi berikut terpenuhi:
1. Admin menonaktifkan produk secara manual (`produk.status_aktif = 0`), ATAU
2. Waktu saat ini berada **di luar jam operasional toko** (diatur admin lewat Pengaturan)

Logikanya bersifat **OR**, bukan AND — kalau salah satu kondisi bikin produk harus nonaktif, produk itu abu-abu, terlepas dari kondisi yang lain.

**Perubahan skema** (lihat juga §9 yang sudah diupdate): tabel `pengaturan` perlu tambahan 2 kolom:
- `jam_buka` (time) — jam toko mulai buka
- `jam_tutup` (time) — jam toko tutup

**Catatan implementasi**:
- Status aktif "final" produk yang ditampilkan ke pembeli = `status_aktif (dari DB) DAN dalam rentang jam_buka–jam_tutup (dihitung real-time di server)`.
- Jangan cache status ini terlalu lama di sisi klien — minimal refresh saat halaman etalase dimuat ulang, supaya produk otomatis abu-abu begitu lewat jam tutup tanpa perlu aksi admin.
- Kalau `jam_buka`/`jam_tutup` belum diisi admin (nilai NULL), anggap toko selalu buka (skip pengecekan jam) — supaya default behavior tidak tiba-tiba mengunci semua produk sebelum admin sempat isi Pengaturan.

### 8.2 Keputusan — Reset Password Admin (final)

**Skip untuk sekarang.** Tidak ada mekanisme reset password mandiri (tidak ada OTP, tidak ada email, tidak ada pertanyaan keamanan) — ini keputusan sadar untuk konteks lomba (bukan produk produksi jangka panjang, single-admin, risiko rendah).

**Fallback kalau admin lupa password**: reset manual langsung lewat database (update `password_hash` via phpMyAdmin/query, developer/pemilik proyek yang melakukan). Tidak perlu dibuatkan UI atau endpoint untuk ini.

**Implikasi untuk F11**: form registrasi & login admin **tidak perlu** ada link/tombol "Lupa password". Cukup username + password + submit.

**Jika Claude Code menemui ambiguitas bisnis baru di luar yang tercantum di file ini — STOP dan tanya user dulu, jangan diasumsikan sendiri.**

---

## 9. Skema Database (dari Class Diagram)

**10 tabel** (7 tabel asli class diagram + `pembeli` + `pesanan_acara` + `pengeluaran`, semua tambahan dari revisi 28 Juli 2026):

**admin**
- id (PK)
- nama_toko (string)
- username (string, unique)
- password_hash (string)
- nomor_hp (string)
- email (string, unique) — *ditambahkan di luar class diagram awal, untuk keperluan reset password §8*

**pembeli** *(tabel baru, mengikuti §2 & referensi schema `customers` dari desain frontend tim — dinamai ulang ke Bahasa Indonesia untuk konsistensi)*
- id (PK)
- nama (string)
- email (string, unique)
- password_hash (string)
- nomor_hp (string, nullable)
- created_at (datetime)
- updated_at (datetime)

**produk**
- id (PK)
- nama (string)
- kategori (string) — Somay Sapi / Lumpia / Pentol Goreng
- harga (decimal)
- status_aktif (bool)

**varian_produk**
- id (PK)
- produk_id (FK → produk)
- nama_varian (string) — mis. Frozen / Digoreng (khusus Lumpia)

**pengaturan**
- id (PK)
- minimum_order (decimal) — default 100000
- pajak_persen (decimal) — default 10
- pajak_aktif (bool)
- alamat_umkm (string)
- jam_buka (time, nullable) — *ditambahkan untuk logika grey-out etalase, lihat §8.1*
- jam_tutup (time, nullable) — *ditambahkan untuk logika grey-out etalase, lihat §8.1*

**pesanan**
- id (PK)
- pembeli_id (FK → pembeli) — *sejak Jalur A dihapus, WAJIB diisi (tidak nullable lagi) — semua pesanan sekarang terasosiasi ke akun pembeli*
- kode_pesanan (string, unique)
- nama_pembeli (string)
- nomor_hp (string)
- metode (string) — ambil_sendiri / diantar *(nilai `bayar_di_tempat` DIHAPUS dari enum, Jalur A sudah tidak ada)*
- alamat (string, nullable — hanya untuk metode diantar)
- catatan (string, nullable)
- tanggal_dibutuhkan (date) — *wajib diisi untuk semua pesanan sekarang (minimal H+1, tidak boleh hari yang sama) — tidak nullable lagi karena tidak ada lagi jalur non-pre-order*
- subtotal (decimal)
- pajak (decimal)
- total (decimal)
- status (string) — pending / lunas / gagal / kedaluwarsa

**item_pesanan**
- id (PK)
- pesanan_id (FK → pesanan)
- produk_id (FK → produk)
- varian_id (FK → varian_produk, nullable)
- jumlah (int)
- harga_satuan (decimal)
- subtotal_item (decimal)

**transaksi**
- id (PK)
- pesanan_id (FK → pesanan, 1:1)
- midtrans_order_id (string, unique)
- status_pembayaran (string)
- mdr_persen (decimal)
- nominal_diterima (decimal)

**pesanan_acara** *(tabel baru, F21 — untuk Jalur C §3.3, custom nego, TERPISAH dari `pesanan` karena alurnya beda: tidak checkout otomatis, tidak selalu berujung transaksi tercatat di sistem)*
- id (PK)
- pembeli_id (FK → pembeli) — pemesan wajib login, sama seperti pesanan reguler
- jenis_acara (string) — Ulang Tahun / Pernikahan / Acara Perusahaan / Pembukaan Kantor-Toko / Arisan / Acara Keagamaan-Sosial / Acara Kedukaan / Lainnya *(referensi kategori dari kompetitor Siomay Leeloo, lihat §3.3)*
- nama_acara (string)
- tanggal_acara (date)
- lokasi_acara (string)
- estimasi_porsi (int, nullable)
- catatan (text, nullable)
- status (string) — baru / dihubungi / deal / batal
- created_at (datetime)

**pengeluaran** *(tabel baru, F22 — untuk modul pencatatan keuangan §5.1)*
- id (PK)
- tanggal (date)
- kategori (string) — bahan_baku / operasional / lainnya
- deskripsi (string)
- jumlah (decimal)
- created_at (datetime)

**Relasi:**
- Admin 1→* Produk (mengelola)
- Admin 1→1 Pengaturan (mengatur)
- Admin 1→* Pesanan (memantau)
- Admin 1→* PesananAcara (memantau) — *baru*
- Admin 1→* Pengeluaran (mencatat) — *baru*
- Pembeli 1→* Pesanan (memesan)
- Pembeli 1→* PesananAcara (memesan) — *baru*
- Produk 1→* VarianProduk (punya)
- Pesanan 1→* ItemPesanan (berisi)
- ItemPesanan *→1 Produk (mereferensi)
- ItemPesanan *→0..1 VarianProduk (varian opsional)
- Pesanan 1→1 Transaksi (menghasilkan)

**Catatan implementasi**: ~~jalur A (Bayar QRIS di tempat)...~~ — CATATAN LAMA DIHAPUS, Jalur A sudah tidak ada lagi sejak revisi 28 Juli 2026. Semua baris `pesanan` sekarang selalu punya `pembeli_id` terisi.

---

## 10. Fitur Lengkap

| # | Fitur | Role |
|---|---|---|
| F1 | Landing page via QR (bebas akses, tanpa login untuk browsing) | Pembeli |
| ~~F2~~ | ~~Bayar QRIS di tempat~~ — **DIHAPUS, Jalur A tidak ada lagi (REVISI 28 Juli 2026)** | — |
| F3 | Etalase menu dinamis (aktif/nonaktif per produk) | Pembeli / Admin |
| F4 | Keranjang lintas kategori + validasi minimum Rp100.000 | Pembeli |
| F5 | Checkbox varian produk (Lumpia) | Pembeli |
| F6 | Kolom catatan pesanan dengan instruksi jelas | Pembeli |
| F7 | Pilihan metode penerimaan (Ambil Sendiri/Diantar) | Pembeli |
| F8 | Pembayaran QRIS untuk pesan antar | Pembeli |
| F9 | Halaman bukti transaksi/struk | Pembeli |
| F10 | Tombol hubungi penjual via WA (otomatis) | Pembeli |
| F11 | Registrasi & login admin | Admin |
| F12 | Dashboard: pesanan berstatus lunas real-time, menampilkan tanggal kebutuhan pesanan untuk perencanaan produksi | Admin |
| F13 | Riwayat transaksi dengan filter | Admin |
| F14 | Modul laporan (ringkasan, filter tanggal, export) | Admin |
| F15 | Tombol hubungi pelanggan via WA | Admin |
| F16 | Tombol "Pesan Maxim" dengan alamat otomatis terisi | Admin |
| F17 | Navigasi back persisten tanpa kehilangan data form | Pembeli |
| F18 | Perhitungan pajak opsional (toggle + persentase diatur admin) | Pembeli (tampil jika aktif) / Admin (atur) |
| F19 | Pemilihan tanggal pesanan dibutuhkan (kalender, hanya H+1 ke atas) — wajib untuk SEMUA pesanan sekarang | Pembeli |
| F20 | Registrasi & login pembeli (wajib untuk checkout semua pesanan) + riwayat pesanan pembeli | Pembeli |
| F21 | Pemesanan acara/kegiatan (form custom + kontak WA admin, bukan checkout otomatis) — **BARU** | Pembeli |
| F22 | Pencatatan pengeluaran + laporan laba-rugi (dashboard admin) — **BARU** | Admin |

---

## 11. Urutan Pembangunan (untuk progres bertahap & mudah didemoin)

> **Catatan reorder v1**: Registrasi & Login Admin (langkah 6 / F11) sudah DIKERJAKAN LEBIH DULU secara kronologis, supaya route `/admin/*` segera terlindungi filter auth — status: SELESAI. Registrasi & Login Pembeli (langkah 3.5 / F20) disisipkan sebelum Langkah 4 karena checkout sekarang mewajibkan login.
> **Catatan reorder v2 (REVISI 28 Juli 2026)**: Jalur A dihapus (F2 dihapus dari langkah 5). Ditambahkan langkah baru: Pesanan Acara/Kegiatan (F21) dan Pencatatan Pengeluaran (F22) — keduanya independen dari alur checkout utama, jadi bisa dikerjakan kapan saja setelah dependency masing-masing selesai (F21 butuh login pembeli sudah ada; F22 butuh modul laporan §5 sudah ada). Nomor urut di daftar ini merepresentasikan urutan LOGIS/dependency, bukan urutan kronologis eksekusi — cek status masing-masing langkah secara manual saat sesi baru dimulai (`git log`, `git status`).

1. Migration database (10 tabel — 7 asli + `pembeli` + `pesanan_acara` + `pengeluaran`)
2. Etalase produk + varian (CRUD admin, tampilan pembeli — styling mengikuti referensi §1.2) — F3
3. Keranjang + validasi minimum Rp100.000 + catatan — F4, F5, F6
3.5. Registrasi & login pembeli + filter `customerAuth` (proteksi checkout, bukan proteksi etalase/keranjang) + riwayat pesanan pembeli — F20
4. Alur checkout (pilih Ambil Sendiri/Diantar) tanpa payment dulu — F7
5. Integrasi Midtrans Sandbox (generate QRIS, webhook, validasi nominal server-side, idempotensi) — F8, F9 *(F2 dihapus, tidak ada lagi Jalur A)*
6. Registrasi & login admin — F11 *(SELESAI, dikerjakan lebih dulu — lihat catatan reorder di atas)*
7. Dashboard admin real-time + riwayat transaksi — F12, F13
8. Tombol WA otomatis (pembeli→penjual, admin→pelanggan) — F10, F15
9. Modul laporan + export PDF/Excel — F14
9.5. **[BARU]** Pencatatan pengeluaran + laporan laba-rugi — F22 (dikerjakan setelah langkah 9, karena numpang di modul laporan yang sama)
10. Integrasi tombol "Pesan Maxim" — F16
10.5. **[BARU]** Pemesanan Acara/Kegiatan — F21 (form pembeli + link WA otomatis + daftar permintaan di dashboard admin; butuh login pembeli dari langkah 3.5 dan pola tombol WA dari langkah 8 sudah ada)
11. Pengaturan pajak opsional — F18
12. **Deploy ke hosting publik** — lakukan LEBIH AWAL (setelah langkah 5 selesai berfungsi lokal), jangan ditunda ke akhir, karena webhook Midtrans butuh URL publik untuk testing beneran.

---

## 12. Identitas Visual

- Nama brand: **Siomay Dua Putri** (sejak 2018)
- Font: **Be Vietnam Pro** (Google Fonts, weight 400/600/700/800)
- Ikon: **Material Symbols Outlined** (Google Fonts variable font)
- Palet warna (design tokens):
  | Token | Hex | Peran |
  |---|---|---|
  | `primary` | `#4C1D95` | Warna utama (tombol, wordmark, ikon aktif) |
  | `primary-hover` | `#6D28D9` | Hover state tombol primary |
  | `secondary` | `#712edd` | Aksen sekunder |
  | `secondary-light` | `#8B5CF6` | Varian terang secondary |
  | `secondary-fixed` | `#ebddff` | Background chip/badge lembut |
  | `background` | `#fef7ff` | Latar halaman |
  | `surface` | `#ffffff` | Latar card/komponen |
  | `surface-variant` | `#f3ebf6` | Latar hover/alternatif |
  | `on-surface` | `#1d1a22` | Teks utama |
  | `on-surface-variant` | `#4a4452` | Teks sekunder |
  | `outline-variant` | `#e7e0eb` | Border/garis pemisah |
  | `accent` | `#e19760` | Aksen hangat (opsional, sparingly) |
- Background halaman: gradient lembut `linear-gradient(135deg, #F8F6FF 0%, #F2ECFF 100%)`
- Radius card: `20px`
- Shadow card: `0 10px 30px rgba(76, 29, 149, 0.08)`, hover: `0 20px 40px rgba(76, 29, 149, 0.12)`
- Logo: emblem bulat, ilustrasi keranjang steamer dengan dumpling (draft sudah ada, siap diekspor SVG/PNG saat implementasi)

**Catatan**: ini menggantikan palet biru/merah/kuning versi sebelumnya dan
gaya "Warm Artisan" di etalase — semua halaman sekarang mengikuti design
tokens di atas.

---

## 13. Konvensi Kerja dengan Claude Code

- Kerjakan **bertahap per langkah** (ikuti urutan §11), laporkan tiap bagian selesai.
- **Jangan commit sebelum direview** oleh user.
- **Test pakai HTTP request nyata** (bukan cuma baca kode) sebelum bilang "selesai".
- Kalau ada ambiguitas bisnis baru di luar file ini — **STOP dan tanya user dulu**, jangan diasumsikan sendiri.
- Commit message pakai gaya natural huruf kecil (bukan conventional-commit prefix seperti `feat:`/`fix:`).
- Kerja di branch `main`, tidak ada workflow branching.
- API keys (Midtrans, Maxim, dll) disimpan di `.env` lokal saja — **JANGAN PERNAH** di-commit ke git.

---

## 14. Info Teknis Proyek

- **Framework**: CodeIgniter 4.7.4
- **Local server**: XAMPP, path `A:\myrealxampp\htdocs\somayduaputri`
- **Repo**: git baru, terpisah dari EcoPalu/GasKerja
- **.gitignore**: standar CI4 (`writable/`, `.env`, `vendor/`)
