@extends('layouts.app')

@section('title', 'Data Pasien')

@section('content')
@php($u = auth()->user())
<h1>Data Pasien</h1>

@if ($u->isAdmin() || $u->isDokter() || $u->isKasir())
<h2>{{ $editing ? 'Edit pasien #'.$editing->id : 'Tambah pasien' }}</h2>
<form method="post"
      action="{{ $editing ? route('pasien.update', $editing) : route('pasien.store') }}"
      class="stacked-form">
  @csrf
  @if ($editing)
    @method('PUT')
  @endif
  <label>Nama
    <input type="text" name="nama" value="{{ old('nama', $editing->nama ?? '') }}" required>
  </label>
  <label>Jenis Kelamin
    <select name="jenis_kelamin" required>
      <option value="L" @selected(old('jenis_kelamin', $editing->jenis_kelamin ?? '') === 'L')>Laki-laki</option>
      <option value="P" @selected(old('jenis_kelamin', $editing->jenis_kelamin ?? '') === 'P')>Perempuan</option>
    </select>
  </label>
  <label>Tanggal Lahir
    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $editing->tanggal_lahir ?? '') }}" required>
  </label>
  <label>Alamat
    <textarea name="alamat" rows="2" required>{{ old('alamat', $editing->alamat ?? '') }}</textarea>
  </label>
  <label>Telepon
    <input type="text" name="telepon" maxlength="15" value="{{ old('telepon', $editing->telepon ?? '') }}" required>
  </label>
  <button type="submit">{{ $editing ? 'Simpan Perubahan' : 'Tambah' }}</button>
  @if ($editing)<a href="{{ route('pasien.index') }}">Batal</a>@endif
</form>
@endif

<h2>Daftar pasien</h2>
<form method="get" action="{{ route('pasien.index') }}" class="search-form">
  <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama / alamat / telepon">
  <button type="submit">Cari</button>
  @if ($q !== '')<a href="{{ route('pasien.index') }}">Reset</a>@endif
</form>

<table>
  <tr>
    <th>ID</th><th>Nama</th><th>Jenis Kelamin</th><th>Tanggal Lahir</th>
    <th>Alamat</th><th>Telepon</th>@if ($u->isAdmin())<th>Aksi</th>@endif
  </tr>
  @foreach ($rows as $p)
  <tr>
    <td>{{ $p->id }}</td>
    <td>{{ $p->nama }}</td>
    <td>{{ jk_label($p->jenis_kelamin) }}</td>
    <td>{{ $p->tanggal_lahir }}</td>
    <td>{{ $p->alamat }}</td>
    <td>{{ $p->telepon }}</td>
    @if ($u->isAdmin())
    <td>
      <a href="{{ route('pasien.edit', $p) }}">Edit</a>
      <form method="post" action="{{ route('pasien.destroy', $p) }}" class="inline-form"
            onsubmit="return confirm('Hapus pasien ini?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">Hapus</button>
      </form>
    </td>
    @endif
  </tr>
  @endforeach
  @if ($rows->isEmpty())
  <tr><td colspan="7">Tidak ada data pasien.</td></tr>
  @endif
</table>
@endsection
