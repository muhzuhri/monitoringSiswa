<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insert default data informasi dashboard
        DB::table('informasi_dashboard')->insert([
            'visi' => 'Pada tahun 2025 menjadi institusi yang unggul di bidang ICT, berintegritas dan berjiwa technopreunership',
            'misi' => json_encode([
                'Menyelenggarakan dan mengembangkan pendidikan tinggi dalam upaya menghasilkan manusia terdidik yang dapat menerapkan dan mengembangkan ilmu pengetahuan bidang informasi, komunikasi dan teknologi.',
                'Menyelenggarakan dan mengembangkan penelitian dalam rangka meningkatkan kualitas pembelajaran, ilmu pengetahuan bidang informasi, komunikasi dan teknologi yang memiliki nilai aplikasi dalam pembangunan.',
                'Menyelenggarakan dan mengembangkan pengabdian kepada masyarakat dengan menerapkan ilmu pengetahuan bidang informasi, komunikasi dan teknologi untuk mewujudkan kesejahteraan dan kemajuan masyarakat.',
                'Menyelenggarakan pembinaan dan pengembangan bakat, minat, penalaran, dan kesejahteraan mahasiswa.',
                'Melaksanakan kerjasama (MOA) dengan lembaga lain, baik nasional maupun internasional.',
                'Melaksanakan Manajemen administrasi yang modern profesional, efektif, efesien dan akuntabel.',
            ]),
            'sejarah' => 'Berdirinya Fakultas Ilmu Komputer didahului dengan Program Diploma Komputer (PDK) Unsri baru berdiri pertengahan tahun 2003, tepatnya tanggal 5 September 2003 dan merupakan program pendidikan bidang ICT yang pertama di Universitas Sriwijaya.',
            'jam_operasional' => 'Senin – Jumat: 08.00 – 16.00 WIB',
            'deskripsi_jam_operasional' => 'Sistem monitoring dapat diakses 24 jam oleh seluruh pengguna yang terdaftar.',
            'alamat_lokasi' => 'Gedung Fasilkom, Kampus Utama Universitas. Tersedia ruang konsultasi magang di lantai 2 setiap hari kerja.',
            'link_maps' => 'https://maps.google.com/?q=Fasilkom+Unsri',
            'email_kontak' => 'humas@ilkom.unsri.ac.id',
            'telp_kontak' => '(+62)37249',
            'website_kontak' => 'www.ilkom.unsri.ac.id',
            'nama_fakultas' => 'Fakultas Ilmu Komputer',
            'deskripsi_banner' => 'Universitas yang berkomitmen mencetak lulusan kompeten di bidang teknologi informasi dan ilmu komputer. Sistem ini membantu monitoring kegiatan magang mahasiswa secara terpadu.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert default program studi
        $prodis = [
            ['nama' => 'Sistem Komputer',        'jenjang' => 'S1', 'warna_dot' => '#4e73df', 'urutan' => 1],
            ['nama' => 'Sistem Informasi',        'jenjang' => 'S1', 'warna_dot' => '#1cc88a', 'urutan' => 2],
            ['nama' => 'Teknik Informatika',      'jenjang' => 'S1', 'warna_dot' => '#36b9cc', 'urutan' => 3],
            ['nama' => 'Manajemen Informatika',   'jenjang' => 'D3', 'warna_dot' => '#f6c23e', 'urutan' => 4],
            ['nama' => 'Komputerisasi Akuntansi', 'jenjang' => 'D3', 'warna_dot' => '#e74a3b', 'urutan' => 5],
            ['nama' => 'Teknik Komputer',         'jenjang' => 'D3', 'warna_dot' => '#fd7e14', 'urutan' => 6],
            ['nama' => 'Magister Ilmu Komputer',  'jenjang' => 'S2', 'warna_dot' => '#6f42c1', 'urutan' => 7],
        ];

        foreach ($prodis as $p) {
            DB::table('program_studi')->insert([
                'nama'       => $p['nama'],
                'jenjang'    => $p['jenjang'],
                'warna_dot'  => $p['warna_dot'],
                'urutan'     => $p['urutan'],
                'aktif'      => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('program_studi')->delete();
        DB::table('informasi_dashboard')->delete();
    }
};
