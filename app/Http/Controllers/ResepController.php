<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\RekamMedis;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResepController extends Controller
{
    public function index(Request $request, RekamMedis $rekamMedis)
    {
        $rekamMedis->load(['pasien', 'dokter', 'pembayaran']);

        $barisResep = $rekamMedis->resep()->with('obat')->orderBy('id')->get();
        $user = $request->user();

        return view('resep.index', [
            'rm' => $rekamMedis,
            'barisResep' => $barisResep,
            'daftarObat' => Obat::orderBy('nama_obat')->get(),
            'pembayaran' => $rekamMedis->pembayaran,
            'totalObat' => $barisResep->sum(fn ($r) => $r->jumlah * $r->obat->harga),
            'canManage' => $user->isAdmin()
                || ($user->isDokter() && $user->dokter_id !== null
                    && $user->dokter_id === $rekamMedis->dokter_id),
        ]);
    }

    /**
     * Meresepkan obat (dokter pada RM miliknya, atau admin).
     * TIDAK menyentuh stok: validasi & pengurangan stok terjadi saat
     * apoteker menyerahkan obat (halaman Penyerahan Obat).
     */
    public function store(Request $request, RekamMedis $rekamMedis)
    {
        $this->authorizeKelola($request, $rekamMedis);

        $data = $request->validate([
            'obat_id' => ['required', 'integer', 'exists:obat,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'aturan_pakai' => ['required', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($rekamMedis, $data) {
            $rekamMedis->resep()->create($data + ['status' => 'diresepkan']);
            $rekamMedis->recomputePembayaran();
        });

        $pesan = 'Resep ditambahkan (status: Diresepkan); total pembayaran diperbarui. '
            .'Stok akan dikurangi saat obat diserahkan apoteker.';

        $obat = Obat::find($data['obat_id']);
        if ($obat->stok < $data['jumlah']) {
            $pesan .= ' Perhatian: jumlah melebihi stok saat ini ('
                .$obat->stok.' '.$obat->satuan.').';
        }

        return redirect()->route('resep.index', $rekamMedis)->with('success', $pesan);
    }

    /**
     * Hapus baris resep.
     * - 'diresepkan': dokter (RM sendiri) atau admin; stok tidak berubah.
     * - 'diserahkan': hanya admin; stok dikembalikan.
     */
    public function destroy(Request $request, RekamMedis $rekamMedis, Resep $resep)
    {
        $this->authorizeKelola($request, $rekamMedis);

        if ($resep->rekam_medis_id !== $rekamMedis->id) {
            return redirect()->route('resep.index', $rekamMedis)
                ->with('error', 'Baris resep tidak ditemukan.');
        }

        if ($resep->status === 'diserahkan') {
            if (! $request->user()->isAdmin()) {
                abort(403, 'Baris resep yang sudah diserahkan hanya dapat dihapus admin.');
            }

            DB::transaction(function () use ($rekamMedis, $resep) {
                $resep->delete();
                Obat::whereKey($resep->obat_id)->increment('stok', $resep->jumlah);
                $rekamMedis->recomputePembayaran();
            });

            return redirect()->route('resep.index', $rekamMedis)
                ->with('success', 'Baris resep (sudah diserahkan) dihapus; stok obat dikembalikan dan total pembayaran diperbarui.');
        }

        DB::transaction(function () use ($rekamMedis, $resep) {
            $resep->delete();
            $rekamMedis->recomputePembayaran();
        });

        return redirect()->route('resep.index', $rekamMedis)
            ->with('success', 'Baris resep dihapus (belum diserahkan, stok tidak berubah); total pembayaran diperbarui.');
    }

    /** Dokter hanya boleh mengelola resep pada rekam medis miliknya; admin bebas. */
    private function authorizeKelola(Request $request, RekamMedis $rekamMedis): void
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->dokter_id === null || $rekamMedis->dokter_id !== $user->dokter_id) {
            abort(403, 'Anda hanya dapat mengelola resep pada rekam medis Anda sendiri.');
        }
    }
}
