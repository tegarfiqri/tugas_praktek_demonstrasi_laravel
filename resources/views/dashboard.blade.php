@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php($u = auth()->user())
<h1>Dashboard</h1>

<div class="cards">
  <div class="card"><div class="num">{{ $jumlahPasien }}</div><div class="label">Pasien</div></div>
  @if ($u->isAdmin() || $u->isDokter())
    <div class="card"><div class="num">{{ $jumlahDokter }}</div><div class="label">Dokter</div></div>
  @endif
  @if ($u->isAdmin() || $u->isApoteker() || $u->isDokter())
    <div class="card"><div class="num">{{ $jumlahObat }}</div><div class="label">Jenis Obat</div></div>
  @endif
  <div class="card"><div class="num">{{ $jumlahRekamMedis }}</div><div class="label">Rekam Medis</div></div>
  @if ($u->isAdmin() || $u->isApoteker())
    <div class="card"><div class="num">{{ $menungguSerah }}</div><div class="label">Resep Menunggu Penyerahan</div></div>
  @endif
  @if ($u->isAdmin() || $u->isKasir())
    <div class="card"><div class="num">{{ $belumLunas }}</div><div class="label">Pembayaran Belum Lunas</div></div>
  @endif
</div>

@if ($u->isAdmin() || $u->isApoteker() || $u->isDokter())
  @if ($stokMenipis->isNotEmpty())
    <h2>Peringatan: stok obat menipis (kurang dari 10)</h2>
    <table>
      <tr><th>Nama Obat</th><th>Stok</th><th>Satuan</th></tr>
      @foreach ($stokMenipis as $o)
      <tr>
        <td>{{ $o->nama_obat }}</td>
        <td class="low-stock">{{ $o->stok }}</td>
        <td>{{ $o->satuan }}</td>
      </tr>
      @endforeach
    </table>
  @else
    <p>Semua stok obat aman (tidak ada yang di bawah 10).</p>
  @endif
@endif

<h2>Menu cepat</h2>
<ul>
  <li><a href="{{ route('pasien.index') }}">Data Pasien</a></li>
  @if ($u->isAdmin() || $u->isDokter())
    <li><a href="{{ route('dokter.index') }}">Data Dokter</a></li>
  @endif
  @if ($u->isAdmin() || $u->isApoteker() || $u->isDokter())
    <li><a href="{{ route('obat.index') }}">Data Obat</a></li>
  @endif
  <li><a href="{{ route('rekam-medis.index') }}">Transaksi Rekam Medis</a></li>
  @if ($u->isAdmin() || $u->isApoteker())
    <li><a href="{{ route('penyerahan.index') }}">Penyerahan Obat</a></li>
  @endif
  @if ($u->isAdmin() || $u->isKasir())
    <li><a href="{{ route('pembayaran.index') }}">Pembayaran</a></li>
  @endif
  @if ($u->isAdmin())
    <li><a href="{{ route('users.index') }}">Manajemen Pengguna</a></li>
  @endif
  @if ($u->isAdmin() || $u->isDokter() || $u->isApoteker() || $u->isKasir())
    <li>
      @if ($u->isAdmin() || $u->isDokter())<a href="{{ route('laporan.pasien') }}">Laporan Pasien</a>@endif
      @if ($u->isAdmin())|@endif
      @if ($u->isAdmin() || $u->isApoteker())<a href="{{ route('laporan.obat') }}">Laporan Stok Obat</a>@endif
      @if ($u->isAdmin())|@endif
      @if ($u->isAdmin() || $u->isKasir())<a href="{{ route('laporan.pembayaran') }}">Laporan Pembayaran</a>@endif
    </li>
  @endif
</ul>
@endsection
