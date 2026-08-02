@extends('layouts.app')

@section('title', 'Laporan Stok Obat')

@section('content')
<h1>Laporan Stok Obat</h1>
<p>Jumlah jenis obat: <strong>{{ $rows->count() }}</strong>,
   stok menipis (&lt; 10): <strong>{{ $jumlahMenipis }}</strong>
   &mdash; dicetak {{ date('d-m-Y H:i') }}</p>
<p class="no-print"><button type="button" onclick="window.print()">Cetak</button></p>

<table>
  <tr>
    <th>No</th><th>Nama Obat</th><th>Harga</th><th>Stok</th><th>Satuan</th>
    <th>Nilai Stok</th><th>Keterangan</th>
  </tr>
  @foreach ($rows as $o)
  <tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $o->nama_obat }}</td>
    <td>{{ rupiah($o->harga) }}</td>
    <td class="{{ $o->stok < 10 ? 'low-stock' : '' }}">{{ $o->stok }}</td>
    <td>{{ $o->satuan }}</td>
    <td>{{ rupiah($o->stok * $o->harga) }}</td>
    <td>
      @if ($o->stok < 10)
        <span class="low-stock">Stok menipis</span>
      @else
        Aman
      @endif
    </td>
  </tr>
  @endforeach
  @if ($rows->isEmpty())
  <tr><td colspan="7">Tidak ada data obat.</td></tr>
  @endif
  <tr class="total-row">
    <th colspan="5">Total nilai persediaan</th>
    <th colspan="2">{{ rupiah($totalNilai) }}</th>
  </tr>
</table>
@endsection
