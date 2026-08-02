<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\RekamMedis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekamMedisController extends Controller
{
    public function index(Request $request)
    {
        return view('rekam-medis.index', $this->viewData($request, null));
    }

    public function edit(Request $request, RekamMedis $rekamMedis)
    {
        $this->authorizeKelola($request, $rekamMedis);

        return view('rekam-medis.index', $this->viewData($request, $rekamMedis));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $rm = RekamMedis::create($data);

            // Pembayaran otomatis dibuat (belum lunas, total awal = biaya periksa).
            $rm->pembayaran()->create([
                'tanggal' => $data['tanggal'],
                'total' => $data['biaya_periksa'],
                'status' => 'belum_lunas',
            ]);
        });

        return redirect()->route('rekam-medis.index')
            ->with('success', 'Rekam medis ditambahkan; tagihan pembayaran otomatis dibuat.');
    }

    public function update(Request $request, RekamMedis $rekamMedis)
    {
        $this->authorizeKelola($request, $rekamMedis);

        $data = $this->validated($request);

        DB::transaction(function () use ($rekamMedis, $data) {
            $rekamMedis->update($data);
            $rekamMedis->recomputePembayaran();
        });

        return redirect()->route('rekam-medis.index')
            ->with('success', 'Rekam medis diperbarui; total pembayaran dihitung ulang.');
    }

    public function destroy(Request $request, RekamMedis $rekamMedis)
    {
        $this->authorizeKelola($request, $rekamMedis);

        if ($rekamMedis->resep()->exists()) {
            return redirect()->route('rekam-medis.index')
                ->with('error', 'Tidak dapat menghapus: rekam medis ini masih memiliki resep. Hapus resepnya dulu.');
        }

        DB::transaction(function () use ($rekamMedis) {
            $rekamMedis->pembayaran()->delete();
            $rekamMedis->delete();
        });

        return redirect()->route('rekam-medis.index')
            ->with('success', 'Rekam medis (beserta tagihannya) dihapus.');
    }

    /**
     * Dokter hanya boleh mengelola rekam medis milik dokter yang tertaut
     * ke akunnya; admin bebas.
     */
    private function authorizeKelola(Request $request, RekamMedis $rekamMedis): void
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->dokter_id === null || $rekamMedis->dokter_id !== $user->dokter_id) {
            abort(403, 'Anda hanya dapat mengelola rekam medis Anda sendiri.');
        }
    }

    private function validated(Request $request): array
    {
        $user = $request->user();

        $rules = [
            'pasien_id' => ['required', 'integer', 'exists:pasien,id'],
            'tanggal' => ['required', 'date'],
            'keluhan' => ['required', 'string'],
            'diagnosa' => ['required', 'string'],
            'tindakan' => ['required', 'string'],
            'biaya_periksa' => ['required', 'integer', 'min:0'],
        ];

        // Admin memilih dokter dari dropdown; akun dokter dipaksa
        // memakai dokter yang tertaut ke akunnya sendiri.
        if ($user->isAdmin()) {
            $rules['dokter_id'] = ['required', 'integer', 'exists:dokter,id'];
        }

        $data = $request->validate($rules);

        if (! $user->isAdmin()) {
            abort_if($user->dokter_id === null, 403,
                'Akun dokter Anda belum tertaut ke data master dokter.');
            $data['dokter_id'] = $user->dokter_id;
        }

        return $data;
    }

    private function viewData(Request $request, ?RekamMedis $editing): array
    {
        $q = trim((string) $request->query('q', ''));

        $rows = RekamMedis::query()
            ->with(['pasien', 'dokter'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->whereHas('pasien', fn ($p) => $p->where('nama', 'like', "%{$q}%"))
                        ->orWhereHas('dokter', fn ($d) => $d->where('nama', 'like', "%{$q}%"))
                        ->orWhere('diagnosa', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        return [
            'rows' => $rows,
            'editing' => $editing,
            'q' => $q,
            'daftarPasien' => Pasien::orderBy('nama')->get(),
            'daftarDokter' => Dokter::orderBy('nama')->get(),
        ];
    }
}
