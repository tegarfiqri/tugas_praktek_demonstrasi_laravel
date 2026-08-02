# SIRS — Sistem Informasi Rumah Sakit (Laravel)

Aplikasi SIRS.
4 role (hand-rolled, tanpa package eksternal). Tanpa registrasi; akun dikelola
admin lewat menu Pengguna.

## Kredensial & role

| Email            | Password    | Role      | Keterangan                                  |
|------------------|-------------|-----------|---------------------------------------------|
| admin@rs.test    | admin123    | admin     | Superuser: lolos semua pemeriksaan role     |
| dokter@rs.test   | dokter123   | dokter    | Tertaut ke dokter #1 (dr. Rina Kartika)     |
| apoteker@rs.test | apoteker123 | apoteker  |                                             |
| kasir@rs.test    | kasir123    | kasir     |                                             |

## Matriks hak akses

| Modul              | admin | dokter                          | apoteker      | kasir         |
|--------------------|-------|---------------------------------|---------------|---------------|
| Pengguna           | CRUD  | -                               | -             | -             |
| Pasien             | CRUD  | lihat + tambah                  | lihat         | lihat + tambah|
| Master dokter      | CRUD  | lihat                           | -             | -             |
| Obat               | CRUD  | lihat                           | CRUD          | -             |
| Rekam medis        | CRUD  | CRUD miliknya sendiri           | lihat         | lihat         |
| Resep (baris)      | kelola| kelola pada RM miliknya         | lihat         | lihat         |
| Penyerahan obat    | ya    | -                               | ya            | -             |
| Pembayaran         | ya    | -                               | -             | ya            |
| Lap. pasien        | ya    | ya                              | -             | -             |
| Lap. obat          | ya    | -                               | ya            | -             |
| Lap. pembayaran    | ya    | -                               | -             | ya            |

## Alur resep dua tahap (peresepan vs penyerahan)

1. Dokter (pada rekam medis miliknya) atau admin menambah baris resep —
   status `diresepkan`, **stok tidak berubah**, tetapi total tagihan langsung
   dihitung ulang (penagihan terjadi saat peresepan).
2. Apoteker (atau admin) membuka halaman **Penyerahan Obat** berisi antrean
   semua baris `diresepkan`, lalu menekan **Serahkan**: stok divalidasi dan
   dikurangi dalam satu transaksi DB, status menjadi `diserahkan`. Stok kurang
   → ditolak dengan pesan error, tidak ada perubahan.
3. Baris `diresepkan` boleh dihapus dokter (RM sendiri) / admin tanpa efek stok;
   baris `diserahkan` hanya boleh dihapus admin dan stoknya dikembalikan.

## Menjalankan

Database `rumahsakit_laravel` sudah dimigrasi dan di-seed. Cukup:

```bash
php artisan serve
```

lalu buka http://127.0.0.1:8000 dan login dengan kredensial di atas.

Untuk mereset data ke kondisi awal:

```bash
php artisan migrate:fresh --seed
```

## Catatan database (unix socket)

MySQL lokal hanya mendengarkan lewat unix socket, bukan TCP 3306. Karena itu
`.env` memakai `DB_SOCKET=/tmp/mysql.sock` (lihat bagian `DB_*`). Jika socket
MySQL Anda berbeda, sesuaikan nilai `DB_SOCKET`.

## Ringkasan fitur

- Login/logout (hand-rolled, `Auth::attempt`), semua halaman lain wajib login.
- Dashboard: ringkasan jumlah data, pembayaran belum lunas, peringatan stok < 10.
- CRUD + pencarian: pasien, dokter, obat (hapus diblokir bila data masih dipakai).
- Rekam medis: membuat tagihan pembayaran otomatis (`belum_lunas`, total = biaya
  periksa); edit biaya menghitung ulang total; hapus ikut menghapus tagihan dan
  diblokir bila masih ada resep.
- Resep per rekam medis: tambah (validasi & pengurangan stok) / hapus (stok
  dikembalikan); total pembayaran = biaya periksa + SUM(jumlah x harga obat),
  dihitung ulang dalam transaksi DB.
- Pembayaran: filter tanggal, ubah status lunas/belum lunas per baris.
- Laporan pasien, stok obat, dan pembayaran (filter rentang tanggal, subtotal
  lunas/belum, total keseluruhan) — semuanya dengan tombol Cetak (`window.print()`).
