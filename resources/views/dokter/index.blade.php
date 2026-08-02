@extends('layouts.app')

@section('title', 'Data Dokter')

@section('content')
@php($u = auth()->user())
<h1>Data Dokter</h1>

@if ($u->isAdmin())
<h2>{{ $editing ? 'Edit dokter #'.$editing->id : 'Tambah dokter' }}</h2>
<form method="post"
      action="{{ $editing ? route('dokter.update', $editing) : route('dokter.store') }}"
      class="stacked-form">
  @csrf
  @if ($editing)
    @method('PUT')
  @endif
  <label>Nama
    <input type="text" name="nama" value="{{ old('nama', $editing->nama ?? '') }}" required>
  </label>
  <label>Spesialis
    <input type="text" name="spesialis" value="{{ old('spesialis', $editing->spesialis ?? '') }}"
           placeholder="mis. Umum, Anak, Penyakit Dalam" required>
  </label>
  <label>Telepon
    <input type="text" name="telepon" maxlength="15" value="{{ old('telepon', $editing->telepon ?? '') }}" required>
  </label>
  <button type="submit">{{ $editing ? 'Simpan Perubahan' : 'Tambah' }}</button>
  @if ($editing)<a href="{{ route('dokter.index') }}">Batal</a>@endif
</form>
@endif

<h2>Daftar dokter</h2>
<form method="get" action="{{ route('dokter.index') }}" class="search-form">
  <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama / spesialis">
  <button type="submit">Cari</button>
  @if ($q !== '')<a href="{{ route('dokter.index') }}">Reset</a>@endif
</form>

<table>
  <tr><th>ID</th><th>Nama</th><th>Spesialis</th><th>Telepon</th>@if ($u->isAdmin())<th>Aksi</th>@endif</tr>
  @foreach ($rows as $d)
  <tr>
    <td>{{ $d->id }}</td>
    <td>{{ $d->nama }}</td>
    <td>{{ $d->spesialis }}</td>
    <td>{{ $d->telepon }}</td>
    @if ($u->isAdmin())
    <td>
      <a href="{{ route('dokter.edit', $d) }}">Edit</a>
      <form method="post" action="{{ route('dokter.destroy', $d) }}" class="inline-form"
            onsubmit="return confirm('Hapus dokter ini?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">Hapus</button>
      </form>
    </td>
    @endif
  </tr>
  @endforeach
  @if ($rows->isEmpty())
  <tr><td colspan="5">Tidak ada data dokter.</td></tr>
  @endif
</table>
@endsection
