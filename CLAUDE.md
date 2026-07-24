# CLAUDE.md — Siomay Dua Putri

> **WAJIB dibaca ulang di setiap sesi baru sebelum mulai kerja apa pun.**
> Ini proyek baru, terpisah total dari EcoPalu/GasKerja. Jangan bawa konteks/kode/pola dari proyek lain kecuali disebutkan eksplisit di file ini.

---

## 1. Latar Belakang Bisnis

Siomay Dua Putri adalah UMKM siomay milik keluarga pemilik proyek, beroperasi sejak 2018. Selama ini transaksi 100% manual: pemesanan lewat telepon, catatan di kertas, pembayaran cash saja.

Sistem ini dibangun untuk mendigitalisasi alur pemesanan dan pembayaran (QRIS) **tanpa mengubah cara kerja penjual** yang tidak familiar teknologi — sekaligus memenuhi kriteria penilaian lomba yang menitikberatkan pada **keandalan transaksi** dan **kelengkapan laporan**.

Ini proyek lomba **solo** (bukan tim), diselenggarakan oleh BRIDA dan Bank Indonesia. Fokus penilaian utama: modul transaksi & laporan.

---

## 2. Dua Role & Kenapa Desainnya Begitu

| Role | Autentikasi | Titik akses | Alasan desain |
|---|---|---|---|
| **Pembeli** | Tanpa login (guest) | Scan QR publik di gerobak | Meniru pengalaman QR-order cepat ala Gacoan; tidak butuh identitas permanen |
| **Admin/Penjual** | Wajib registrasi + login | URL privat, shortcut "Add to Home Screen" di HP penjual | Data yang diakses (total QRIS, riwayat transaksi, nomor HP pelanggan) sensitif |

**Keputusan final — JANGAN DIUBAH**: pembeli tetap tanpa login/registrasi. Ini bukan fitur yang kurang, ini **nilai jual inovasi** — disampaikan eksplisit ke juri sebagai keputusan desain sengaja, bukan celah terlewat.

---

## 3. Alur Lengkap

### 3.1 Alur Umum
Scan QR di gerobak → pilih salah satu:
- **Bayar QRIS** (makan di tempat), atau
- **Pesan Antar/Banyak** (minimum Rp100.000)

### 3.2 Jalur A — Bayar QRIS di Tempat
1. Tampilkan gambar QRIS merchant, tombol unduh, instruksi bayar.
2. Pembeli bayar via aplikasi apapun yang support QRIS.
3. Transaksi **anonim** — tidak masuk riwayat pesanan berbasis pelanggan, tapi **wajib tercatat sebagai bagian dari total omzet harian** di laporan (dana tetap masuk ke rekening/merchant QRIS yang sama).

### 3.3 Jalur B — Pesan Antar/Pesanan Banyak
1. **Etalase**: Siomay, Batagor, Lumpia (varian Frozen/Digoreng khusus Lumpia). Produk nonaktif tampil abu-abu, tidak bisa dipilih.
2. Kontrol jumlah (+/-) per produk, bisa lintas kategori dalam satu transaksi.
3. **Kolom catatan** untuk permintaan rasa saja — instruksi eksplisit bahwa kolom ini BUKAN untuk alamat.
4. **Validasi minimum Rp100.000** — tombol lanjut nonaktif + peringatan jika belum tercapai.
5. Ringkasan: **subtotal → pajak (opsional, default 10% jika admin aktifkan) → total bayar** — total inilah yang dikirim ke Midtrans untuk generate kode QRIS.
6. Pilih metode penerimaan: **Ambil Sendiri** atau **Diantar**.

**4.3.a Ambil Sendiri**
1. Isi nama + nomor HP (nomor HP wajib).
2. Tampilkan alamat & lokasi UMKM di peta.
3. Lanjut ke halaman pembayaran QRIS.
4. Setelah status `lunas` → pesanan otomatis masuk dashboard admin.
5. Tampilkan halaman bukti transaksi.
6. Layar status "Menunggu pesanan Anda" + tombol WA otomatis: *"Jam berapa bisa saya ambil?"*

**4.3.b Diantar**
1. Isi nama penerima, nomor HP, alamat lengkap, pilih lokasi via peta.
2. Lanjut ke pembayaran QRIS.
3. Setelah `lunas` → masuk dashboard admin.
4. Tampilkan halaman bukti transaksi.
5. Tombol WA otomatis: *"Kapan pesanan saya diantarkan?"*
6. Di sisi admin: tombol "Pesan Maxim" membuka Maxim dengan alamat pelanggan sudah terisi (sandbox API Maxim — dikonfirmasi tersedia, lihat §7).

### 3.4 Navigasi
Setiap halaman punya tombol Kembali & Lanjut; data yang sudah diisi **tidak boleh hilang** saat pembeli bolak-balik halaman.

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

## 5. Modul Laporan Transaksi (prioritas penilaian tinggi)

| Fitur | Detail |
|---|---|
| Ringkasan harian | Total omzet hari ini, jumlah transaksi, breakdown Bayar-di-Tempat vs Pesan Antar |
| Filter rentang tanggal | Hari ini / minggu ini / custom range |
| Breakdown per metode | Bayar di Tempat, Ambil Sendiri, Diantar |
| Breakdown produk terlaris | Jumlah terjual per produk dalam periode dipilih |
| Export laporan | PDF atau Excel/CSV |
| Tampilan | Angka besar & jelas di atas dashboard — admin non-teknis (papa) |

---

## 6. Registrasi & Login Admin

1. Registrasi (setup awal, sekali saja): nama toko, username, password, nomor HP.
2. Login: username + password + opsi "tetap masuk".
3. Password di-hash, rate-limit percobaan login.
4. Reset password: masih terbuka — lihat §8.

---

## 7. Keputusan yang Sudah Final (jangan diubah tanpa diskusi ulang)

- **Payment gateway**: Midtrans Sandbox (basis dev & demo). Duitku = cadangan. Kategori usaha: **UMI** (MDR 0%/0,3%).
- **Pajak**: opsional, toggle admin — bukan wajib aktif.
- **Pembeli tanpa login**: final, ini fitur/nilai jual, bukan kekurangan.
- **Strategi jaringan demo**: aplikasi **di-deploy ke hosting publik SEBELUM hari demo** (bukan localhost). Hotspot HP aman dipakai sebagai koneksi klien karena webhook Midtrans tidak lewat jalur ini lagi. WiFi venue sebagai cadangan.
- **API Maxim**: dikonfirmasi tersedia sandbox-nya — siap diimplementasikan, rujuk dokumentasi teknis persis (endpoint, auth, payload alamat) saat implementasi.
- **Stack**: CodeIgniter 4.7.4, XAMPP, path lokal `A:\myrealxampp\htdocs\somayduaputri`.
- **Fitur QRIS/Midtrans TIDAK BISA jalan tanpa internet sama sekali** (termasuk sandbox) — bukan soal localhost vs hosting, tapi koneksi ke server Midtrans wajib ada. Fitur lain (etalase, dashboard, riwayat, laporan) bisa offline/localhost.

---

## 8. Pertanyaan Terbuka — Perlu Keputusan Pengguna Sebelum Diimplementasikan

1. **Definisi "grey out" etalase**: toggle manual admin, otomatis berdasarkan jam operasional, atau kombinasi keduanya?
2. **Mekanisme reset password admin**: tidak ada email institusional — perlu mekanisme alternatif (contoh: kombinasi keamanan sederhana / pertanyaan keamanan). Perlu diputuskan sebelum implementasi F11.
3. **Format demo ke panitia lomba**: apakah wajib live-online, atau video rekaman cadangan diterima jika ada kendala jaringan?

**Jika Claude Code menemui ambiguitas bisnis baru di luar yang tercantum di file ini — STOP dan tanya user dulu, jangan diasumsikan sendiri.**

---

## 9. Skema Database (dari Class Diagram)

7 tabel:

**admin**
- id (PK)
- nama_toko (string)
- username (string, unique)
- password_hash (string)
- nomor_hp (string)
- email (string, unique) — *ditambahkan di luar class diagram awal, untuk keperluan reset password §8*

**produk**
- id (PK)
- nama (string)
- kategori (string) — Siomay / Batagor / Lumpia
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

**pesanan**
- id (PK)
- kode_pesanan (string, unique)
- nama_pembeli (string)
- nomor_hp (string)
- metode (string) — bayar_di_tempat / ambil_sendiri / diantar
- alamat (string, nullable — hanya untuk metode diantar)
- catatan (string, nullable)
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

**Relasi:**
- Admin 1→* Produk (mengelola)
- Admin 1→1 Pengaturan (mengatur)
- Admin 1→* Pesanan (memantau)
- Produk 1→* VarianProduk (punya)
- Pesanan 1→* ItemPesanan (berisi)
- ItemPesanan *→1 Produk (mereferensi)
- ItemPesanan *→0..1 VarianProduk (varian opsional)
- Pesanan 1→1 Transaksi (menghasilkan)

**Catatan implementasi**: jalur A (Bayar QRIS di tempat) tetap membuat baris `pesanan` + `transaksi` (agar konsisten masuk laporan omzet), tapi dengan `nama_pembeli`/`nomor_hp` bisa diisi placeholder anonim (mis. "Pembeli di Tempat") dan tidak tampil di riwayat pesanan berbasis pelanggan — hanya dihitung di rekap omzet harian.

---

## 10. Fitur Lengkap (F1–F18)

| # | Fitur | Role |
|---|---|---|
| F1 | Landing page via QR, tanpa login | Pembeli |
| F2 | Bayar QRIS di tempat | Pembeli |
| F3 | Etalase menu dinamis (aktif/nonaktif per produk) | Pembeli / Admin |
| F4 | Keranjang lintas kategori + validasi minimum Rp100.000 | Pembeli |
| F5 | Checkbox varian produk (Lumpia) | Pembeli |
| F6 | Kolom catatan pesanan dengan instruksi jelas | Pembeli |
| F7 | Pilihan metode penerimaan (Ambil Sendiri/Diantar) | Pembeli |
| F8 | Pembayaran QRIS untuk pesan antar | Pembeli |
| F9 | Halaman bukti transaksi/struk | Pembeli |
| F10 | Tombol hubungi penjual via WA (otomatis) | Pembeli |
| F11 | Registrasi & login admin | Admin |
| F12 | Dashboard: pesanan berstatus lunas real-time | Admin |
| F13 | Riwayat transaksi dengan filter | Admin |
| F14 | Modul laporan (ringkasan, filter tanggal, export) | Admin |
| F15 | Tombol hubungi pelanggan via WA | Admin |
| F16 | Tombol "Pesan Maxim" dengan alamat otomatis terisi | Admin |
| F17 | Navigasi back persisten tanpa kehilangan data form | Pembeli |
| F18 | Perhitungan pajak opsional (toggle + persentase diatur admin) | Pembeli (tampil jika aktif) / Admin (atur) |

---

## 11. Urutan Pembangunan (untuk progres bertahap & mudah didemoin)

1. Migration database (7 tabel di atas)
2. Etalase produk + varian (CRUD admin, tampilan pembeli) — F3
3. Keranjang + validasi minimum Rp100.000 + catatan — F4, F5, F6
4. Alur checkout (pilih Ambil Sendiri/Diantar) tanpa payment dulu — F7
5. Integrasi Midtrans Sandbox (generate QRIS, webhook, validasi nominal server-side, idempotensi) — F2, F8, F9
6. Registrasi & login admin — F11
7. Dashboard admin real-time + riwayat transaksi — F12, F13
8. Tombol WA otomatis (pembeli→penjual, admin→pelanggan) — F10, F15
9. Modul laporan + export PDF/Excel — F14
10. Integrasi tombol "Pesan Maxim" — F16
11. Pengaturan pajak opsional — F18
12. **Deploy ke hosting publik** — lakukan LEBIH AWAL (setelah langkah 5 selesai berfungsi lokal), jangan ditunda ke akhir, karena webhook Midtrans butuh URL publik untuk testing beneran.

---

## 12. Identitas Visual

- Nama brand: **Siomay Dua Putri** (sejak 2018)
- Palet warna: ungu muda `#C9A8E0` (latar utama), biru `#1D4ED8` (wordmark "Siomay"), merah `#DC2626` (wordmark "Dua Putri" & aksen), kuning `#FACC15`/`#F5B301` (aksen highlight)
- Logo: emblem bulat, ilustrasi keranjang steamer dengan dumpling (draft sudah ada, siap diekspor SVG/PNG saat implementasi)

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
