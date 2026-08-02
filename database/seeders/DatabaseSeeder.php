<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\RekamMedis;
use App\Models\Resep;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Data awal SIRS — mengikuti database.sql aplikasi plain PHP,
     * ditambah akun per role (dokter/apoteker/kasir).
     */
    public function run(): void
    {
        // Admin: admin@rs.test / admin123
        User::create([
            'name' => 'Administrator RS',
            'email' => 'admin@rs.test',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        Pasien::create(['nama' => 'Budi Santoso', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '1990-05-14', 'alamat' => 'Jl. Merdeka No. 12, Bandung', 'telepon' => '081234567001']);
        Pasien::create(['nama' => 'Siti Aminah', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '1985-11-02', 'alamat' => 'Jl. Cihampelas No. 45, Bandung', 'telepon' => '081234567002']);
        Pasien::create(['nama' => 'Andi Wijaya', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2001-03-27', 'alamat' => 'Jl. Sudirman No. 8, Cimahi', 'telepon' => '081234567003']);
        Pasien::create(['nama' => 'Dewi Lestari', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '1997-08-19', 'alamat' => 'Jl. Dago Asri No. 3, Bandung', 'telepon' => '081234567004']);

        Dokter::create(['nama' => 'dr. Rina Kartika, Sp.A', 'spesialis' => 'Anak', 'telepon' => '081298765001']);
        Dokter::create(['nama' => 'dr. Bambang Sutrisno, Sp.PD', 'spesialis' => 'Penyakit Dalam', 'telepon' => '081298765002']);
        Dokter::create(['nama' => 'dr. Maya Puspita', 'spesialis' => 'Umum', 'telepon' => '081298765003']);

        // Akun per role. Akun dokter tertaut ke dokter #1 (dr. Rina Kartika).
        User::create([
            'name' => 'dr. Rina Kartika, Sp.A',
            'email' => 'dokter@rs.test',
            'password' => Hash::make('dokter123'),
            'role' => 'dokter',
            'dokter_id' => 1,
        ]);
        User::create([
            'name' => 'Apoteker RS',
            'email' => 'apoteker@rs.test',
            'password' => Hash::make('apoteker123'),
            'role' => 'apoteker',
        ]);
        User::create([
            'name' => 'Kasir RS',
            'email' => 'kasir@rs.test',
            'password' => Hash::make('kasir123'),
            'role' => 'kasir',
        ]);

        // Stok di bawah ini adalah stok SETELAH resep contoh dikeluarkan
        // (baris resep contoh berstatus 'diserahkan').
        Obat::create(['nama_obat' => 'Paracetamol 500 mg', 'harga' => 2000, 'stok' => 100, 'satuan' => 'tablet']);
        Obat::create(['nama_obat' => 'Amoxicillin 500 mg', 'harga' => 3000, 'stok' => 80, 'satuan' => 'kapsul']);
        Obat::create(['nama_obat' => 'OBH Combi Sirup', 'harga' => 15000, 'stok' => 25, 'satuan' => 'botol']);
        Obat::create(['nama_obat' => 'Vitamin C 250 mg', 'harga' => 1500, 'stok' => 200, 'satuan' => 'tablet']);
        Obat::create(['nama_obat' => 'Antasida Doen', 'harga' => 4000, 'stok' => 8, 'satuan' => 'strip']);
        Obat::create(['nama_obat' => 'Betadine 30 ml', 'harga' => 12000, 'stok' => 5, 'satuan' => 'botol']);

        $rm1 = RekamMedis::create([
            'pasien_id' => 1, 'dokter_id' => 2, 'tanggal' => '2026-07-20',
            'keluhan' => 'Demam tinggi 3 hari, batuk berdahak.',
            'diagnosa' => 'ISPA (Infeksi Saluran Pernapasan Akut)',
            'tindakan' => 'Pemeriksaan fisik, pemberian resep obat, anjuran istirahat.',
            'biaya_periksa' => 50000,
        ]);
        $rm2 = RekamMedis::create([
            'pasien_id' => 2, 'dokter_id' => 1, 'tanggal' => '2026-07-25',
            'keluhan' => 'Anak batuk pilek disertai demam ringan.',
            'diagnosa' => 'Common cold',
            'tindakan' => 'Pemeriksaan fisik, pemberian sirup obat batuk.',
            'biaya_periksa' => 75000,
        ]);

        // Status 'diserahkan': obatnya sudah diserahkan apoteker, dan nilai
        // stok obat di atas SUDAH memperhitungkan pengurangan tersebut.
        Resep::create(['rekam_medis_id' => $rm1->id, 'obat_id' => 1, 'jumlah' => 10, 'aturan_pakai' => '3x1 sesudah makan', 'status' => 'diserahkan']);
        Resep::create(['rekam_medis_id' => $rm1->id, 'obat_id' => 2, 'jumlah' => 12, 'aturan_pakai' => '3x1, harus dihabiskan', 'status' => 'diserahkan']);
        Resep::create(['rekam_medis_id' => $rm2->id, 'obat_id' => 3, 'jumlah' => 1, 'aturan_pakai' => '3x1 sendok takar sesudah makan', 'status' => 'diserahkan']);

        // total = biaya_periksa + SUM(jumlah * harga obat)
        // RM 1: 50000 + 10*2000 + 12*3000 = 106000 (lunas)
        // RM 2: 75000 + 1*15000           = 90000  (belum lunas)
        Pembayaran::create(['rekam_medis_id' => $rm1->id, 'tanggal' => '2026-07-20', 'total' => 106000, 'status' => 'lunas']);
        Pembayaran::create(['rekam_medis_id' => $rm2->id, 'tanggal' => '2026-07-25', 'total' => 90000, 'status' => 'belum_lunas']);
    }
}
