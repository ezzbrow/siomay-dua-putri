<?php

namespace App\Services;

use App\Helpers\ProductAvailability;
use App\Models\PengaturanModel;
use App\Models\ProdukModel;
use App\Models\VarianProdukModel;

class CartService
{
    public const SESSION_KEY = 'cart';
    public const CATATAN_KEY = 'cart_catatan';

    public static function get(): array
    {
        $cart = session()->get(self::SESSION_KEY);
        return is_array($cart) ? $cart : [];
    }

    public static function getCatatan(): string
    {
        return (string) (session()->get(self::CATATAN_KEY) ?? '');
    }

    public static function setCatatan(string $catatan): void
    {
        session()->set(self::CATATAN_KEY, $catatan);
    }

    public static function clear(): void
    {
        session()->remove([self::SESSION_KEY, self::CATATAN_KEY]);
    }

    public static function lineKey(int $produkId, ?int $varianId): string
    {
        return $produkId . ':' . ($varianId ?? '0');
    }

    public static function add(int $produkId, ?int $varianId, float $jumlah, ProdukModel $produkModel, VarianProdukModel $varianModel): array
    {
        $produk = $produkModel->find($produkId);
        if (! $produk) {
            return ['ok' => false, 'error' => 'Produk tidak ditemukan.'];
        }

        $pengaturan = (new PengaturanModel())->getSingleton();
        $now        = date('H:i:s');
        $avail      = ProductAvailability::resolve(
            $pengaturan['jam_buka'] ?? null,
            $pengaturan['jam_tutup'] ?? null,
            $now
        );
        $tersedia  = ProductAvailability::isProductTersedia($produk, $avail['tokoBuka']);
        if (! $tersedia) {
            $alasan = ! empty($avail['alasan']) ? ' ' . $avail['alasan'] : '';
            return ['ok' => false, 'error' => 'Produk "' . $produk['nama'] . '" sedang tidak tersedia.' . $alasan];
        }

        $isLumpia = ($produk['kategori'] ?? '') === 'Lumpia';
        $varian   = null;
        if ($isLumpia) {
            if (! $varianId) {
                return ['ok' => false, 'error' => 'Pilih varian (Frozen/Digoreng) untuk Lumpia terlebih dahulu.'];
            }
            $varian = $varianModel->find((int) $varianId);
            if (! $varian || (int) $varian['produk_id'] !== $produkId) {
                return ['ok' => false, 'error' => 'Varian tidak valid untuk produk ini.'];
            }
        } else {
            $varianId = null;
        }

        $jumlah = max(0.01, (float) $jumlah);
        $key    = self::lineKey($produkId, $varianId ? (int) $varianId : null);
        $cart   = self::get();
        $cart[$key] = [
            'produk_id' => (int) $produkId,
            'varian_id' => $varianId ? (int) $varianId : null,
            'jumlah'    => (float) (($cart[$key]['jumlah'] ?? 0)) + $jumlah,
        ];
        session()->set(self::SESSION_KEY, $cart);
        return ['ok' => true, 'key' => $key];
    }

    public static function decrement(int $produkId, ?int $varianId, float $jumlah = 1.0): void
    {
        $key  = self::lineKey($produkId, $varianId);
        $cart = self::get();
        if (! isset($cart[$key])) {
            return;
        }
        $cart[$key]['jumlah'] = max(0.0, (float) $cart[$key]['jumlah'] - max(0.01, (float) $jumlah));
        if ($cart[$key]['jumlah'] <= 0.0001) {
            unset($cart[$key]);
        }
        session()->set(self::SESSION_KEY, $cart);
    }

    public static function remove(int $produkId, ?int $varianId): void
    {
        $key  = self::lineKey($produkId, $varianId);
        $cart = self::get();
        unset($cart[$key]);
        session()->set(self::SESSION_KEY, $cart);
    }

    public static function hydrate(ProdukModel $produkModel, VarianProdukModel $varianModel, PengaturanModel $pengaturanModel): array
    {
        $cart    = self::get();
        $rows    = [];
        $total   = 0.0;
        foreach ($cart as $key => $line) {
            $produk = $produkModel->find((int) $line['produk_id']);
            if (! $produk) {
                unset($cart[$key]);
                continue;
            }
            $varian = null;
            if (! empty($line['varian_id'])) {
                $varian = $varianModel->find((int) $line['varian_id']);
                if (! $varian || (int) $varian['produk_id'] !== (int) $line['produk_id']) {
                    unset($cart[$key]);
                    continue;
                }
            }
            $harga = (float) $produk['harga'];
            $jumlah = (float) $line['jumlah'];
            $subtotal = $harga * $jumlah;
            $total += $subtotal;
            $rows[] = [
                'key'       => $key,
                'produk'    => $produk,
                'varian'    => $varian,
                'jumlah'    => $jumlah,
                'harga'     => $harga,
                'subtotal'  => $subtotal,
            ];
        }
        session()->set(self::SESSION_KEY, $cart);
        $minOrder = (float) ($pengaturanModel->getSingleton()['minimum_order'] ?? 100000);
        return [
            'rows'      => $rows,
            'total'     => $total,
            'minOrder'  => $minOrder,
            'canCheckout' => $total >= $minOrder && count($rows) > 0,
            'kekurangan' => max(0.0, $minOrder - $total),
        ];
    }
}
