<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\RekamMedis;
use App\Models\Resep;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'jumlahPasien' => Pasien::count(),
            'jumlahDokter' => Dokter::count(),
            'jumlahObat' => Obat::count(),
            'jumlahRekamMedis' => RekamMedis::count(),
            'belumLunas' => Pembayaran::where('status', 'belum_lunas')->count(),
            'menungguSerah' => Resep::where('status', 'diresepkan')->count(),
            'stokMenipis' => Obat::where('stok', '<', 10)->orderBy('stok')->get(),
        ]);
    }
}
