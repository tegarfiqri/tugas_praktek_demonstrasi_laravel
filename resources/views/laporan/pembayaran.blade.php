@extends('layouts.app')

@section('title', 'Laporan Pembayaran')

@section('content')
<h1>Laporan Pembayaran</h1>

<form method="get" action="{{ route('laporan.pembayaran') }}" class="search-form no-print">
  <label>Dari: <input type="date" name="dari" value="{{ $dari }}"></label>
  <label>Sampai: <input type="date" name="sampai" value="{{ $sampai }}"></label>
  <button type="submit">Filter</button>
  @if ($dari !== '' || $sampai !== '')<a href="{{ route('laporan.pembayaran') }}">Reset</a>@endif
</form>

<p>
  Periode:
  <strong>{{ $dari !== '' ? $dari : 'awal' }}</strong> s.d.
  <strong>{{ $sampai !== '' ? $sampai : 'akhir' }}</strong>,
  jumlah transaksi: <strong>{{ $rows->count() }}</strong>
  &mdash; dicetak {{ date('d-m-Y H:i') }}
</p>
<p class="no-print"><button type="button" onclick="window.print()">Cetak</button></p>

<table>
  <tr>
    <th>No</th><th>Tanggal</th><th>Rekam Medis</th><th>Pasien</th>
    <th>Status</th><th>Total</th>
  </tr>
  @foreach ($rows as $b)
  <tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $b->tanggal }}</td>
    <td>#{{ $b->rekam_medis_id }}</td>
    <td>{{ $b->rekamMedis->pasien->nama }}</td>
    <td>
      <span class="status status-{{ $b->status }}">
        {{ status_bayar_label($b->status) }}
      </span>
    </td>
    <td>{{ rupiah($b->total) }}</td>
  </tr>
  @endforeach
  @if ($rows->isEmpty())
  <tr><td colspan="6">Tidak ada pembayaran pada periode ini.</td></tr>
  @endif
  <tr class="total-row">
    <th colspan="5">Subtotal Lunas</th>
    <th>{{ rupiah($totalLunas) }}</th>
  </tr>
  <tr class="total-row">
    <th colspan="5">Subtotal Belum Lunas</th>
    <th>{{ rupiah($totalBelum) }}</th>
  </tr>
  <tr class="total-row">
    <th colspan="5">Total Keseluruhan</th>
    <th>{{ rupiah($grandTotal) }}</th>
  </tr>
</table>
@endsection
