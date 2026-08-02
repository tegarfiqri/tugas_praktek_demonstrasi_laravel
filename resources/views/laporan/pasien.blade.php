@extends('layouts.app')

@section('title', 'Laporan Pasien')

@section('content')
<h1>Laporan Data Pasien</h1>
<p>Jumlah pasien terdaftar: <strong>{{ $rows->count() }}</strong>
   &mdash; dicetak {{ date('d-m-Y H:i') }}</p>
<p class="no-print"><button type="button" onclick="window.print()">Cetak</button></p>

<table>
  <tr>
    <th>No</th><th>Nama</th><th>Jenis Kelamin</th><th>Tanggal Lahir</th>
    <th>Alamat</th><th>Telepon</th>
  </tr>
  @foreach ($rows as $p)
  <tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $p->nama }}</td>
    <td>{{ jk_label($p->jenis_kelamin) }}</td>
    <td>{{ $p->tanggal_lahir }}</td>
    <td>{{ $p->alamat }}</td>
    <td>{{ $p->telepon }}</td>
  </tr>
  @endforeach
  @if ($rows->isEmpty())
  <tr><td colspan="6">Tidak ada data pasien.</td></tr>
  @endif
</table>
@endsection
