<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use Illuminate\Http\Request;

class DokterController extends Controller
{
    public function index(Request $request)
    {
        return view('dokter.index', [
            'rows' => $this->cari($request),
            'editing' => null,
            'q' => trim((string) $request->query('q', '')),
        ]);
    }

    public function edit(Request $request, Dokter $dokter)
    {
        return view('dokter.index', [
            'rows' => $this->cari($request),
            'editing' => $dokter,
            'q' => trim((string) $request->query('q', '')),
        ]);
    }

    public function store(Request $request)
    {
        Dokter::create($this->validated($request));

        return redirect()->route('dokter.index')->with('success', 'Data dokter ditambahkan.');
    }

    public function update(Request $request, Dokter $dokter)
    {
        $dokter->update($this->validated($request));

        return redirect()->route('dokter.index')->with('success', 'Data dokter diperbarui.');
    }

    public function destroy(Dokter $dokter)
    {
        if ($dokter->rekamMedis()->exists()) {
            return redirect()->route('dokter.index')
                ->with('error', 'Tidak dapat menghapus: dokter ini masih memiliki rekam medis.');
        }

        $dokter->delete();

        return redirect()->route('dokter.index')->with('success', 'Data dokter dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'spesialis' => ['required', 'string', 'max:50'],
            'telepon' => ['required', 'string', 'max:15'],
        ]);
    }

    private function cari(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        return Dokter::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nama', 'like', "%{$q}%")
                        ->orWhere('spesialis', 'like', "%{$q}%");
                });
            })
            ->orderBy('nama')
            ->get();
    }
}
