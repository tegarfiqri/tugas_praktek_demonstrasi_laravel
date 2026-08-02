@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<h1>Manajemen Pengguna</h1>

<h2>{{ $editing ? 'Edit pengguna #'.$editing->id : 'Tambah pengguna' }}</h2>
<form method="post"
      action="{{ $editing ? route('users.update', $editing) : route('users.store') }}"
      class="stacked-form">
  @csrf
  @if ($editing)
    @method('PUT')
  @endif
  <label>Nama
    <input type="text" name="name" value="{{ old('name', $editing->name ?? '') }}" required>
  </label>
  <label>Email
    <input type="email" name="email" value="{{ old('email', $editing->email ?? '') }}" required>
  </label>
  <label>Password {{ $editing ? '(kosongkan jika tidak diganti)' : '' }}
    <input type="password" name="password" minlength="6" @unless($editing) required @endunless>
  </label>
  <label>Role
    <select name="role" required>
      @foreach (['admin', 'dokter', 'apoteker', 'kasir'] as $role)
      <option value="{{ $role }}" @selected(old('role', $editing->role ?? '') === $role)>
        {{ role_label($role) }}
      </option>
      @endforeach
    </select>
  </label>
  <label>Tautan Data Dokter (hanya dipakai untuk role Dokter)
    <select name="dokter_id">
      <option value="">-- tidak tertaut --</option>
      @foreach ($daftarDokter as $d)
      <option value="{{ $d->id }}" @selected((int) old('dokter_id', $editing->dokter_id ?? 0) === $d->id)>
        {{ $d->nama }} ({{ $d->spesialis }})
      </option>
      @endforeach
    </select>
  </label>
  <button type="submit">{{ $editing ? 'Simpan Perubahan' : 'Tambah' }}</button>
  @if ($editing)<a href="{{ route('users.index') }}">Batal</a>@endif
</form>

<h2>Daftar pengguna</h2>
<table>
  <tr><th>ID</th><th>Nama</th><th>Email</th><th>Role</th><th>Tautan Dokter</th><th>Aksi</th></tr>
  @foreach ($rows as $usr)
  <tr>
    <td>{{ $usr->id }}</td>
    <td>{{ $usr->name }}</td>
    <td>{{ $usr->email }}</td>
    <td>{{ role_label($usr->role) }}</td>
    <td>{{ $usr->dokter->nama ?? '-' }}</td>
    <td>
      <a href="{{ route('users.edit', $usr) }}">Edit</a>
      @if ($usr->id !== auth()->id())
      <form method="post" action="{{ route('users.destroy', $usr) }}" class="inline-form"
            onsubmit="return confirm('Hapus pengguna ini?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">Hapus</button>
      </form>
      @endif
    </td>
  </tr>
  @endforeach
</table>
@endsection
