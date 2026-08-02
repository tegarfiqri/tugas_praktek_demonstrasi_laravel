@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
<h1>Pembayaran</h1>

<form method="get" action="{{ route('pembayaran.index') }}" class="search-form">
  <label>Tanggal:
    <input type="date" name="tanggal" value="{{ $tanggal }}">
  </label>
  <button type="submit">Filter</button>
  @if ($tanggal !== '')<a href="{{ route('pembayaran.index') }}">Reset</a>@endif
</form>

<table>
  <tr>
    <th>ID</th><th>Rekam Medis</th><th>Pasien</th><th>Tanggal</th>
    <th>Total</th><th>Status</th><th>Ubah Status</th>
  </tr>
  @foreach ($rows as $b)
  <tr>
    <td>{{ $b->id }}</td>
    <td>
      <a href="{{ route('resep.index', $b->rekam_medis_id) }}">#{{ $b->rekam_medis_id }}</a>
    </td>
    <td>{{ $b->rekamMedis->pasien->nama }}</td>
    <td>{{ $b->tanggal }}</td>
    <td>{{ rupiah($b->total) }}</td>
    <td>
      <span class="status status-{{ $b->status }}">
        {{ status_bayar_label($b->status) }}
      </span>
    </td>
    <td>
      <form method="post" action="{{ route('pembayaran.status', $b) }}" class="inline-form">
        @csrf
        @method('PATCH')
        <input type="hidden" name="tanggal_filter" value="{{ $tanggal }}">
        <select name="status">
          <option value="belum_lunas" @selected($b->status === 'belum_lunas')>Belum Lunas</option>
          <option value="lunas" @selected($b->status === 'lunas')>Lunas</option>
        </select>
        <button type="submit">Simpan</button>
      </form>
    </td>
  </tr>
  @endforeach
  @if ($rows->isEmpty())
  <tr><td colspan="7">Tidak ada data pembayaran.</td></tr>
  @endif
</table>
@endsection
