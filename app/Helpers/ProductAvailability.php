<?php

namespace App\Helpers;

class ProductAvailability
{
    public static function tokoSedangBuka(?string $jamBuka, ?string $jamTutup, string $now): bool
    {
        return self::resolve($jamBuka, $jamTutup, $now)['tokoBuka'];
    }

    public static function resolve(?string $jamBuka, ?string $jamTutup, string $now): array
    {
        if ($jamBuka === null || $jamTutup === null || $jamBuka === '' || $jamTutup === '') {
            return ['tokoBuka' => true, 'alasan' => null];
        }

        $buka  = strtotime('1970-01-01 ' . $jamBuka);
        $tutup = strtotime('1970-01-01 ' . $jamTutup);
        $nowT  = strtotime('1970-01-01 ' . $now);

        if ($buka === false || $tutup === false || $nowT === false) {
            return ['tokoBuka' => true, 'alasan' => null];
        }

        if ($buka <= $tutup) {
            $bukaSekarang = ($nowT >= $buka && $nowT <= $tutup);
        } else {
            $bukaSekarang = ($nowT >= $buka || $nowT <= $tutup);
        }

        if ($bukaSekarang) {
            return ['tokoBuka' => true, 'alasan' => null];
        }

        $alasan = sprintf('Di luar jam operasional (%s–%s).', substr($jamBuka, 0, 5), substr($jamTutup, 0, 5));
        return ['tokoBuka' => false, 'alasan' => $alasan];
    }

    public static function isProductTersedia(array $produk, bool $tokoBuka): bool
    {
        $aktif = (int) ($produk['status_aktif'] ?? 0) === 1;
        return $aktif && $tokoBuka;
    }
}
