<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function pasien()
    {
        return view('laporan.pasien', [
            'rows' => Pasien::orderBy('nama')->get(),
        ]);
    }

    public function obat()
    {
        $rows = Obat::orderBy('nama_obat')->get();

        return view('laporan.obat', [
            'rows' => $rows,
            'totalNilai' => $rows->sum(fn ($o) => $o->stok * $o->harga),
            'jumlahMenipis' => $rows->where('stok', '<', 10)->count(),
        ]);
    }

    public function pembayaran(Request $request)
    {
        $dari = trim((string) $request->query('dari', ''));
        $sampai = trim((string) $request->query('sampai', ''));

        $rows = Pembayaran::query()
            ->with('rekamMedis.pasien')
            ->when($dari !== '', fn ($query) => $query->where('tanggal', '>=', $dari))
            ->when($sampai !== '', fn ($query) => $query->where('tanggal', '<=', $sampai))
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        return view('laporan.pembayaran', [
            'rows' => $rows,
            'dari' => $dari,
            'sampai' => $sampai,
            'totalLunas' => $rows->where('status', 'lunas')->sum('total'),
            'totalBelum' => $rows->where('status', 'belum_lunas')->sum('total'),
            'grandTotal' => $rows->sum('total'),
        ]);
    }
}
