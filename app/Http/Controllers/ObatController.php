<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        return view('obat.index', [
            'rows' => $this->cari($request),
            'editing' => null,
            'q' => trim((string) $request->query('q', '')),
        ]);
    }

    public function edit(Request $request, Obat $obat)
    {
        return view('obat.index', [
            'rows' => $this->cari($request),
            'editing' => $obat,
            'q' => trim((string) $request->query('q', '')),
        ]);
    }

    public function store(Request $request)
    {
        Obat::create($this->validated($request));

        return redirect()->route('obat.index')->with('success', 'Data obat ditambahkan.');
    }

    public function update(Request $request, Obat $obat)
    {
        $obat->update($this->validated($request));

        return redirect()->route('obat.index')->with('success', 'Data obat diperbarui.');
    }

    public function destroy(Obat $obat)
    {
        if ($obat->resep()->exists()) {
            return redirect()->route('obat.index')
                ->with('error', 'Tidak dapat menghapus: obat ini masih dipakai pada resep.');
        }

        $obat->delete();

        return redirect()->route('obat.index')->with('success', 'Data obat dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama_obat' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'integer', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'satuan' => ['required', 'string', 'max:20'],
        ]);
    }

    private function cari(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        return Obat::query()
            ->when($q !== '', fn ($query) => $query->where('nama_obat', 'like', "%{$q}%"))
            ->orderBy('nama_obat')
            ->get();
    }
}
