@extends('layouts.app')

@section('title', 'Data Obat')

@section('content')
@php($u = auth()->user())
@php($kelolaObat = $u->isAdmin() || $u->isApoteker())
<h1>Data Obat</h1>

@if ($kelolaObat)
<h2>{{ $editing ? 'Edit obat #'.$editing->id : 'Tambah obat' }}</h2>
<form method="post"
      action="{{ $editing ? route('obat.update', $editing) : route('obat.store') }}"
      class="stacked-form">
  @csrf
  @if ($editing)
    @method('PUT')
  @endif
  <label>Nama Obat
    <input type="text" name="nama_obat" value="{{ old('nama_obat', $editing->nama_obat ?? '') }}" required>
  </label>
  <label>Harga (Rp)
    <input type="number" name="harga" min="0" value="{{ old('harga', $editing->harga ?? '') }}" required>
  </label>
  <label>Stok
    <input type="number" name="stok" min="0" value="{{ old('stok', $editing->stok ?? '') }}" required>
  </label>
  <label>Satuan
    <input type="text" name="satuan" maxlength="20" value="{{ old('satuan', $editing->satuan ?? '') }}"
           placeholder="mis. tablet, botol, strip" required>
  </label>
  <button type="submit">{{ $editing ? 'Simpan Perubahan' : 'Tambah' }}</button>
  @if ($editing)<a href="{{ route('obat.index') }}">Batal</a>@endif
</form>
@endif

<h2>Daftar obat</h2>
<form method="get" action="{{ route('obat.index') }}" class="search-form">
  <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama obat">
  <button type="submit">Cari</button>
  @if ($q !== '')<a href="{{ route('obat.index') }}">Reset</a>@endif
</form>

<table>
  <tr><th>ID</th><th>Nama Obat</th><th>Harga</th><th>Stok</th><th>Satuan</th>@if ($kelolaObat)<th>Aksi</th>@endif</tr>
  @foreach ($rows as $o)
  <tr>
    <td>{{ $o->id }}</td>
    <td>{{ $o->nama_obat }}</td>
    <td>{{ rupiah($o->harga) }}</td>
    <td class="{{ $o->stok < 10 ? 'low-stock' : '' }}">{{ $o->stok }}</td>
    <td>{{ $o->satuan }}</td>
    @if ($kelolaObat)
    <td>
      <a href="{{ route('obat.edit', $o) }}">Edit</a>
      <form method="post" action="{{ route('obat.destroy', $o) }}" class="inline-form"
            onsubmit="return confirm('Hapus obat ini?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">Hapus</button>
      </form>
    </td>
    @endif
  </tr>
  @endforeach
  @if ($rows->isEmpty())
  <tr><td colspan="6">Tidak ada data obat.</td></tr>
  @endif
</table>
@endsection
