@extends('layouts.app')

@section('title', 'Penyerahan Obat')

@section('content')
<h1>Penyerahan Obat</h1>
<p>Antrean baris resep berstatus <span class="status status-diresepkan">Diresepkan</span>.
   Tombol Serahkan memvalidasi stok, mengurangi stok, dan menandai resep sebagai
   <span class="status status-diserahkan">Diserahkan</span>.</p>

<table>
  <tr>
    <th>ID</th><th>Rekam Medis</th><th>Tanggal</th><th>Pasien</th><th>Dokter</th>
    <th>Obat</th><th>Jumlah</th><th>Stok Saat Ini</th><th>Aturan Pakai</th><th>Aksi</th>
  </tr>
  @foreach ($rows as $r)
  <tr>
    <td>{{ $r->id }}</td>
    <td><a href="{{ route('resep.index', $r->rekam_medis_id) }}">#{{ $r->rekam_medis_id }}</a></td>
    <td>{{ $r->rekamMedis->tanggal }}</td>
    <td>{{ $r->rekamMedis->pasien->nama }}</td>
    <td>{{ $r->rekamMedis->dokter->nama }}</td>
    <td>{{ $r->obat->nama_obat }}</td>
    <td>{{ $r->jumlah }} {{ $r->obat->satuan }}</td>
    <td class="{{ $r->obat->stok < $r->jumlah ? 'low-stock' : '' }}">
      {{ $r->obat->stok }} {{ $r->obat->satuan }}
      @if ($r->obat->stok < $r->jumlah)(kurang)@endif
    </td>
    <td>{{ $r->aturan_pakai }}</td>
    <td>
      <form method="post" action="{{ route('penyerahan.serahkan', $r) }}" class="inline-form"
            onsubmit="return confirm('Serahkan obat ini? Stok akan dikurangi.');">
        @csrf
        <button type="submit">Serahkan</button>
      </form>
    </td>
  </tr>
  @endforeach
  @if ($rows->isEmpty())
  <tr><td colspan="10">Tidak ada resep yang menunggu penyerahan.</td></tr>
  @endif
</table>
@endsection
