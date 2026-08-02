<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Illuminate\Http\Request;

class PasienController extends Controller
{
    public function index(Request $request)
    {
        return view('pasien.index', [
            'rows' => $this->cari($request),
            'editing' => null,
            'q' => trim((string) $request->query('q', '')),
        ]);
    }

    public function edit(Request $request, Pasien $pasien)
    {
        return view('pasien.index', [
            'rows' => $this->cari($request),
            'editing' => $pasien,
            'q' => trim((string) $request->query('q', '')),
        ]);
    }

    public function store(Request $request)
    {
        Pasien::create($this->validated($request));

        return redirect()->route('pasien.index')->with('success', 'Data pasien ditambahkan.');
    }

    public function update(Request $request, Pasien $pasien)
    {
        $pasien->update($this->validated($request));

        return redirect()->route('pasien.index')->with('success', 'Data pasien diperbarui.');
    }

    public function destroy(Pasien $pasien)
    {
        if ($pasien->rekamMedis()->exists()) {
            return redirect()->route('pasien.index')
                ->with('error', 'Tidak dapat menghapus: pasien ini masih memiliki rekam medis.');
        }

        $pasien->delete();

        return redirect()->route('pasien.index')->with('success', 'Data pasien dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required', 'string'],
            'telepon' => ['required', 'string', 'max:15'],
        ]);
    }

    private function cari(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        return Pasien::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nama', 'like', "%{$q}%")
                        ->orWhere('alamat', 'like', "%{$q}%")
                        ->orWhere('telepon', 'like', "%{$q}%");
                });
            })
            ->orderBy('nama')
            ->get();
    }
}
