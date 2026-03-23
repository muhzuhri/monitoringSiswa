<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KriteriaPenilaian;

class KriteriaPenilaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sikapKerja = [
            'Disiplin',
            'Tanggung Jawab',
            'Kerjasama',
            'Kejujuran',
            'Ketaatan',
        ];

        foreach ($sikapKerja as $index => $nama) {
            KriteriaPenilaian::updateOrCreate(
                ['nama_kriteria' => $nama, 'tipe' => 'sikap_kerja'],
                ['urutan' => $index + 1]
            );
        }

        $kompetensiKeahlian = [
            'K3LH',
            'Memahami Simulasi dan Komunikasi Digital',
            'Memahami Sistem Komputer',
            'Memahami Komputer dan Jaringan Dasar',
            'Memahami Pemrograman Dasar',
            'Memahami dan Menerapkan Desain Grafis Percetakan',
            'Menggabungkan Animasi 2D dan 3D',
            'Menerapkan Desain Media Interaktif',
            'Menerapkan Teknik Pengolahan Audio dan Video',
        ];

        foreach ($kompetensiKeahlian as $index => $nama) {
            KriteriaPenilaian::updateOrCreate(
                ['nama_kriteria' => $nama, 'tipe' => 'kompetensi_keahlian'],
                ['urutan' => $index + 1]
            );
        }
    }
}
