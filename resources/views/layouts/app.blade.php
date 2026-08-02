<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'SIRS') - SIRS</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
@php($u = auth()->user())
<nav class="topnav">
  <div class="container nav-inner">
    <a class="brand" href="{{ url('/') }}">SIRS</a>
    <div class="nav-links">
      @auth
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('pasien.index') }}">Pasien</a>
        @if ($u->isAdmin() || $u->isDokter())
          <a href="{{ route('dokter.index') }}">Dokter</a>
        @endif
        @if ($u->isAdmin() || $u->isApoteker() || $u->isDokter())
          <a href="{{ route('obat.index') }}">Obat</a>
        @endif
        <a href="{{ route('rekam-medis.index') }}">Rekam Medis</a>
        @if ($u->isAdmin() || $u->isApoteker())
          <a href="{{ route('penyerahan.index') }}">Penyerahan</a>
        @endif
        @if ($u->isAdmin() || $u->isKasir())
          <a href="{{ route('pembayaran.index') }}">Pembayaran</a>
        @endif
        @if ($u->isAdmin() || $u->isDokter())
          <a href="{{ route('laporan.pasien') }}">Lap. Pasien</a>
        @endif
        @if ($u->isAdmin() || $u->isApoteker())
          <a href="{{ route('laporan.obat') }}">Lap. Obat</a>
        @endif
        @if ($u->isAdmin() || $u->isKasir())
          <a href="{{ route('laporan.pembayaran') }}">Lap. Pembayaran</a>
        @endif
        @if ($u->isAdmin())
          <a href="{{ route('users.index') }}">Pengguna</a>
        @endif
        <span class="nav-user">Hai, {{ $u->name }} ({{ role_label($u->role) }})</span>
        <form method="post" action="{{ route('logout') }}" class="inline-form">
          @csrf
          <button type="submit" class="link-button">Keluar</button>
        </form>
      @else
        <a href="{{ route('login') }}">Login</a>
      @endauth
    </div>
  </div>
</nav>
<main class="container">
@if (session('success'))
  <div class="flash flash-success">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div class="flash flash-error">{{ session('error') }}</div>
@endif
@if ($errors->any())
  <div class="flash flash-error">
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
@yield('content')
</main>
<footer class="container footer">
  SIRS &mdash; Sistem Informasi Rumah Sakit (demo sederhana)
</footer>
</body>
</html>
