<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Resep;
use Illuminate\Support\Facades\DB;

class PenyerahanController extends Controller
{
    /** Antrean semua baris resep berstatus 'diresepkan'. */
    public function index()
    {
        $rows = Resep::query()
            ->where('status', 'diresepkan')
            ->with(['rekamMedis.pasien', 'rekamMedis.dokter', 'obat'])
            ->orderBy('id')
            ->get();

        return view('penyerahan.index', ['rows' => $rows]);
    }

    /**
     * Serahkan obat untuk satu baris resep: validasi stok, kurangi stok,
     * tandai 'diserahkan' — semuanya dalam satu transaksi DB.
     */
    public function serahkan(Resep $resep)
    {
        $gagal = null;

        DB::transaction(function () use ($resep, &$gagal) {
            $resep = Resep::lockForUpdate()->find($resep->id);

            if ($resep->status !== 'diresepkan') {
                $gagal = 'Baris resep ini sudah diserahkan.';

                return;
            }

            $obat = Obat::lockForUpdate()->find($resep->obat_id);

            if ($obat->stok < $resep->jumlah) {
                $gagal = 'Stok '.$obat->nama_obat.' tidak cukup (tersisa '
                    .$obat->stok.' '.$obat->satuan.', dibutuhkan '.$resep->jumlah.').';

                return;
            }

            $obat->decrement('stok', $resep->jumlah);
            $resep->update(['status' => 'diserahkan']);
        });

        if ($gagal !== null) {
            return redirect()->route('penyerahan.index')->with('error', $gagal);
        }

        return redirect()->route('penyerahan.index')
            ->with('success', 'Obat diserahkan; stok dikurangi dan status resep menjadi Diserahkan.');
    }
}
