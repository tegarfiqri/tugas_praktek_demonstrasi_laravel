<?php

if (! function_exists('rupiah')) {
    /** Format angka sebagai rupiah, mis. 150000 -> "Rp 150.000". */
    function rupiah($n): string
    {
        return 'Rp '.number_format((int) $n, 0, ',', '.');
    }
}

if (! function_exists('jk_label')) {
    /** Label jenis kelamin. */
    function jk_label(string $jk): string
    {
        return $jk === 'L' ? 'Laki-laki' : 'Perempuan';
    }
}

if (! function_exists('role_label')) {
    /** Label role pengguna. */
    function role_label(string $role): string
    {
        return [
            'admin' => 'Administrator',
            'dokter' => 'Dokter',
            'apoteker' => 'Apoteker',
            'kasir' => 'Kasir',
        ][$role] ?? $role;
    }
}

if (! function_exists('status_resep_label')) {
    /** Label status baris resep. */
    function status_resep_label(string $status): string
    {
        return $status === 'diserahkan' ? 'Diserahkan' : 'Diresepkan';
    }
}

if (! function_exists('status_bayar_label')) {
    /** Label status pembayaran. */
    function status_bayar_label(string $status): string
    {
        return $status === 'lunas' ? 'Lunas' : 'Belum Lunas';
    }
}
