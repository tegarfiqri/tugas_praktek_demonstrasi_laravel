@extends('layouts.app')

@section('title', 'Rekam Medis')

@section('content')
@php($u = auth()->user())
<h1>Transaksi Rekam Medis</h1>

@if ($u->isAdmin() || $u->isDokter())
<h2>{{ $editing ? 'Edit rekam medis #'.$editing->id : 'Tambah rekam medis' }}</h2>
<form method="post"
      action="{{ $editing ? route('rekam-medis.update', $editing) : route('rekam-medis.store') }}"
      class="stacked-form">
  @csrf
  @if ($editing)
    @method('PUT')
  @endif
  <label>Pasien
    <select name="pasien_id" required>
      <option value="">-- pilih pasien --</option>
      @foreach ($daftarPasien as $p)
      <option value="{{ $p->id }}" @selected((int) old('pasien_id', $editing->pasien_id ?? 0) === $p->id)>
        {{ $p->nama }}
      </option>
      @endforeach
    </select>
  </label>
  @if ($u->isAdmin())
  <label>Dokter
    <select name="dokter_id" required>
      <option value="">-- pilih dokter --</option>
      @foreach ($daftarDokter as $d)
      <option value="{{ $d->id }}" @selected((int) old('dokter_id', $editing->dokter_id ?? 0) === $d->id)>
        {{ $d->nama }} ({{ $d->spesialis }})
      </option>
      @endforeach
    </select>
  </label>
  @else
  <label>Dokter
    <input type="text" value="{{ $u->dokter->nama ?? '(akun belum tertaut ke dokter)' }}" disabled>
  </label>
  @endif
  <label>Tanggal
    <input type="date" name="tanggal" value="{{ old('tanggal', $editing->tanggal ?? date('Y-m-d')) }}" required>
  </label>
  <label>Keluhan
    <textarea name="keluhan" rows="2" required>{{ old('keluhan', $editing->keluhan ?? '') }}</textarea>
  </label>
  <label>Diagnosa
    <textarea name="diagnosa" rows="2" required>{{ old('diagnosa', $editing->diagnosa ?? '') }}</textarea>
  </label>
  <label>Tindakan
    <textarea name="tindakan" rows="2" required>{{ old('tindakan', $editing->tindakan ?? '') }}</textarea>
  </label>
  <label>Biaya Periksa (Rp)
    <input type="number" name="biaya_periksa" min="0" value="{{ old('biaya_periksa', $editing->biaya_periksa ?? '') }}" required>
  </label>
  <button type="submit">{{ $editing ? 'Simpan Perubahan' : 'Tambah' }}</button>
  @if ($editing)<a href="{{ route('rekam-medis.index') }}">Batal</a>@endif
</form>
@endif

<h2>Daftar rekam medis</h2>
<form method="get" action="{{ route('rekam-medis.index') }}" class="search-form">
  <input type="text" name="q" value="{{ $q }}" placeholder="Cari pasien / dokter / diagnosa">
  <button type="submit">Cari</button>
  @if ($q !== '')<a href="{{ route('rekam-medis.index') }}">Reset</a>@endif
</form>

<table>
  <tr>
    <th>ID</th><th>Tanggal</th><th>Pasien</th><th>Dokter</th>
    <th>Diagnosa</th><th>Biaya Periksa</th><th>Aksi</th>
  </tr>
  @foreach ($rows as $rm)
  <tr>
    <td>{{ $rm->id }}</td>
    <td>{{ $rm->tanggal }}</td>
    <td>{{ $rm->pasien->nama }}</td>
    <td>{{ $rm->dokter->nama }}</td>
    <td>{{ $rm->diagnosa }}</td>
    <td>{{ rupiah($rm->biaya_periksa) }}</td>
    <td>
      <a href="{{ route('resep.index', $rm) }}">Resep</a>
      @if ($u->isAdmin() || ($u->isDokter() && $u->dokter_id === $rm->dokter_id))
      <a href="{{ route('rekam-medis.edit', $rm) }}">Edit</a>
      <form method="post" action="{{ route('rekam-medis.destroy', $rm) }}" class="inline-form"
            onsubmit="return confirm('Hapus rekam medis ini beserta tagihannya?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">Hapus</button>
      </form>
      @endif
    </td>
  </tr>
  @endforeach
  @if ($rows->isEmpty())
  <tr><td colspan="7">Tidak ada rekam medis.</td></tr>
  @endif
</table>
@endsection
