<?php

namespace App\Helpers;

use Throwable;

class ErrorParser
{
    public static function parse(Throwable $e)
    {
        $message = $e->getMessage();

        // 1. Penanganan Data Duplikat (MySQL 1062)
        if (str_contains($message, '1062') || str_contains($message, 'Duplicate entry')) {
            preg_match("/Duplicate entry '(.*)' for key/", $message, $matches);
            $value = $matches[1] ?? 'tersebut';

            return "Gagal memproses: Data '$value' sudah terdaftar di sistem. Mohon periksa kembali inputan Anda; jika data sudah ada, Anda dapat melakukan pembaruan (edit) atau menghapus data lama sebelum menginput ulang.";
        }

        // 2. Penanganan Relasi Data (Foreign Key Constraint 1451)
        if (str_contains($message, '1451')) {
            return "Data tidak dapat dihapus atau diubah karena masih digunakan oleh bagian lain dalam sistem.";
        }

        // 3. Penanganan Data Terlalu Besar (Misal: Input terlalu panjang)
        if (str_contains($message, '1406')) {
            return "Input terlalu panjang. Mohon periksa kembali batas karakter pada form Anda.";
        }

        // 4. Fallback: Membersihkan pesan error teknis
        // Mengambil teks sebelum tanda kurung untuk membuang detail SQL Query
        $cleanMessage = explode('(', $message)[0];
        $cleanMessage = str_replace('SQLSTATE[23000]: Integrity constraint violation:', 'Kesalahan Validasi Data:', $cleanMessage);

        return "Terjadi kendala sistem: " . trim($cleanMessage);
    }
}
