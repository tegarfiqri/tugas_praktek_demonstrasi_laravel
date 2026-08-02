@extends('layouts.app')

@section('title', 'Resep RM #'.$rm->id)

@section('content')
<h1>Resep Obat &mdash; Rekam Medis #{{ $rm->id }}</h1>

<table class="detail-table">
  <tr><th>Pasien</th><td>{{ $rm->pasien->nama }}</td></tr>
  <tr><th>Dokter</th><td>{{ $rm->dokter->nama }}</td></tr>
  <tr><th>Tanggal</th><td>{{ $rm->tanggal }}</td></tr>
  <tr><th>Diagnosa</th><td>{{ $rm->diagnosa }}</td></tr>
  <tr><th>Biaya Periksa</th><td>{{ rupiah($rm->biaya_periksa) }}</td></tr>
</table>

@php($u = auth()->user())
@if ($canManage)
<h2>Tambah baris resep</h2>
<p>Stok obat baru akan dikurangi saat apoteker menyerahkan obat
   (status berubah dari Diresepkan menjadi Diserahkan).</p>
<form method="post" action="{{ route('resep.store', $rm) }}" class="stacked-form">
  @csrf
  <label>Obat
    <select name="obat_id" required>
      <option value="">-- pilih obat --</option>
      @foreach ($daftarObat as $o)
      <option value="{{ $o->id }}">
        {{ $o->nama_obat }} (stok {{ $o->stok }} {{ $o->satuan }}, {{ rupiah($o->harga) }})
      </option>
      @endforeach
    </select>
  </label>
  <label>Jumlah
    <input type="number" name="jumlah" min="1" value="1" required>
  </label>
  <label>Aturan Pakai
    <input type="text" name="aturan_pakai" maxlength="100"
           placeholder="mis. 3x1 sesudah makan" required>
  </label>
  <button type="submit">Tambah Resep</button>
</form>
@endif

<h2>Daftar resep</h2>
<table>
  <tr>
    <th>Obat</th><th>Harga</th><th>Jumlah</th><th>Aturan Pakai</th>
    <th>Status</th><th>Subtotal</th><th>Aksi</th>
  </tr>
  @foreach ($barisResep as $r)
  <tr>
    <td>{{ $r->obat->nama_obat }}</td>
    <td>{{ rupiah($r->obat->harga) }}</td>
    <td>{{ $r->jumlah }} {{ $r->obat->satuan }}</td>
    <td>{{ $r->aturan_pakai }}</td>
    <td>
      <span class="status status-{{ $r->status }}">{{ status_resep_label($r->status) }}</span>
    </td>
    <td>{{ rupiah($r->jumlah * $r->obat->harga) }}</td>
    <td>
      @if ($r->status === 'diresepkan' ? $canManage : $u->isAdmin())
      <form method="post" action="{{ route('resep.destroy', [$rm, $r]) }}" class="inline-form"
            onsubmit="return confirm('{{ $r->status === 'diserahkan'
                ? 'Hapus baris resep ini? Stok obat akan dikembalikan.'
                : 'Hapus baris resep ini? (belum diserahkan, stok tidak berubah)' }}');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">Hapus</button>
      </form>
      @endif
    </td>
  </tr>
  @endforeach
  @if ($barisResep->isEmpty())
  <tr><td colspan="7">Belum ada resep.</td></tr>
  @endif
  <tr class="total-row">
    <th colspan="5">Total obat</th>
    <th colspan="2">{{ rupiah($totalObat) }}</th>
  </tr>
  <tr class="total-row">
    <th colspan="5">Total tagihan (periksa + obat)</th>
    <th colspan="2">
      {{ rupiah($pembayaran->total ?? 0) }}
      @if ($pembayaran)
        <span class="status status-{{ $pembayaran->status }}">
          {{ status_bayar_label($pembayaran->status) }}
        </span>
      @endif
    </th>
  </tr>
</table>

<p><a href="{{ route('rekam-medis.index') }}">&larr; Kembali ke rekam medis</a>
@if ($u->isAdmin() || $u->isKasir())
 | <a href="{{ route('pembayaran.index') }}">Lihat pembayaran</a>
@endif
@if ($u->isAdmin() || $u->isApoteker())
 | <a href="{{ route('penyerahan.index') }}">Penyerahan obat</a>
@endif
</p>
@endsection
