<?php

namespace App\Services;

use App\Models\ProdukModel;

/**
 * StandCartService — cart session TERPISAH dari CartService (Pesan Antar).
 *
 * Session key berbeda ('stand_cart') sehingga user bisa memiliki isi
 * keranjang Pesan Antar dan menu Pesan Stand secara bersamaan tanpa tabrakan.
 *
 * Semua produk Pesan Stand bertipe 'pcs' (integer qty), tidak ada varian.
 */
class StandCartService
{
    public const SESSION_KEY = 'stand_cart';

    /**
     * Ambil seluruh item stand cart dari session.
     * Format: [ produk_id => jumlah, ... ]
     */
    public static function get(): array
    {
        $cart = session()->get(self::SESSION_KEY);
        return is_array($cart) ? $cart : [];
    }

    /**
     * Set jumlah item untuk produk tertentu.
     * jumlah 0 = hapus item dari cart.
     */
    public static function set(int $produkId, int $jumlah): void
    {
        $cart = self::get();
        if ($jumlah <= 0) {
            unset($cart[$produkId]);
        } else {
            $cart[$produkId] = $jumlah;
        }
        session()->set(self::SESSION_KEY, $cart);
    }

    /**
     * Tambah 1 pcs untuk produk tertentu.
     */
    public static function increment(int $produkId): void
    {
        $cart = self::get();
        $cart[$produkId] = ($cart[$produkId] ?? 0) + 1;
        session()->set(self::SESSION_KEY, $cart);
    }

    /**
     * Kurang 1 pcs untuk produk tertentu. Hapus jika qty jadi 0.
     */
    public static function decrement(int $produkId): void
    {
        $cart = self::get();
        if (! isset($cart[$produkId])) {
            return;
        }
        $cart[$produkId]--;
        if ($cart[$produkId] <= 0) {
            unset($cart[$produkId]);
        }
        session()->set(self::SESSION_KEY, $cart);
    }

    /**
     * Kosongkan seluruh stand cart.
     */
    public static function clear(): void
    {
        session()->remove(self::SESSION_KEY);
    }

    /**
     * Hydrate cart: gabungkan data session dengan harga dari DB.
     * Harga SELALU dari DB — tidak pernah trust session untuk harga.
     *
     * Return array:
     *   rows    => [ [ produk, jumlah, harga, subtotal_item ], ... ]
     *   subtotal => float
     */
    public static function hydrate(ProdukModel $produkModel): array
    {
        $cart    = self::get();
        $rows    = [];
        $subtotal = 0.0;

        foreach ($cart as $produkId => $jumlah) {
            $produk = $produkModel->where('id', (int) $produkId)
                ->where('tampil_di_pesan_stand', 1)
                ->first();
            if (! $produk) {
                // Produk tidak valid / sudah tidak tampil di stand — hapus dari cart
                self::set((int) $produkId, 0);
                continue;
            }
            $jumlah  = max(1, (int) $jumlah);
            $harga   = (float) $produk['harga'];
            $subItem = $harga * $jumlah;
            $subtotal += $subItem;
            $rows[] = [
                'produk'        => $produk,
                'jumlah'        => $jumlah,
                'harga'         => $harga,
                'subtotal_item' => $subItem,
            ];
        }

        return [
            'rows'     => $rows,
            'subtotal' => $subtotal,
        ];
    }

    /**
     * Kosongkan session data form acara (stand_*).
     */
    public static function clearFormSession(): void
    {
        session()->remove([
            'stand_nama',
            'stand_wa',
            'stand_jenis_acara',
            'stand_nama_acara',
            'stand_tanggal_acara',
            'stand_lokasi_acara',
            'stand_estimasi_tamu',
            'stand_catatan',
        ]);
    }
}
