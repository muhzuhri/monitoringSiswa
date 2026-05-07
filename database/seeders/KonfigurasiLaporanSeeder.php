<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KonfigurasiLaporanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'tipe_laporan' => 'absensi_individu',
                'header_1' => 'REKAP ABSENSI SISWA',
                'header_2' => 'PROGRAM PRAKTIK KERJA LAPANGAN (PKL)',
            ],
            [
                'tipe_laporan' => 'absensi_kelompok',
                'header_1' => 'ABSENSI SISWA MAGANG / PRAKERIN',
                'header_2' => 'PROGRAM STUDI KEAHLIAN TEKNIK JARINGAN KOMPUTER DAN TELEKOMUNIKASI',
                'header_3' => '{sekolah}',
                'header_4' => 'TAHUN PELAJARAN {tahun}',
            ],
            [
                'tipe_laporan' => 'kegiatan_mingguan',
                'header_1' => 'JURNAL KEGIATAN MINGGUAN SISWA',
                'header_2' => 'PROGRAM PRAKTIK KERJA LAPANGAN (PKL)',
            ],
            [
                'tipe_laporan' => 'penilaian_guru',
                'header_1' => 'FAKULTAS ILMU KOMPUTER UNIVERSITAS SRIWIJAYA',
                'header_2' => 'LEMBAR PENILAIAN SISWA MAGANG',
                'header_3' => 'TAHUN PELAJARAN {tahun}',
            ],
            [
                'tipe_laporan' => 'penilaian_pembimbing',
                'header_1' => 'FAKULTAS ILMU KOMPUTER UNIVERSITAS SRIWIJAYA',
                'header_2' => 'LEMBAR PENILAIAN SISWA MAGANG',
                'header_3' => 'TAHUN PELAJARAN {tahun}',
            ],
            [
                'tipe_laporan' => 'sertifikat',
                'header_1' => 'SERTIFIKAT',
                'header_2' => 'DIBERIKAN KEPADA :',
                'header_3' => 'Dekan,',
                'header_4' => 'Prof. Dr. Erwin, S.Si., M.Si.',
                'header_5' => 'NIP. 197412122000031002',
                'template_isi' => 'Atas partisipasinya sebagai Peserta Magang di Fakultas Ilmu Komputer Universitas Sriwijaya selama periode {tgl_mulai} sampai dengan {tgl_selesai}, serta telah menyelesaikan kegiatan magang dengan baik dan menunjukkan kinerja yang disiplin dan bertanggung jawab.',
                'color_primary' => '#1a56db',
                'color_secondary' => '#fbc02d',
                'background_color' => '#fdfaf2',
            ],
        ];

        foreach ($data as $item) {
            \App\Models\KonfigurasiLaporan::updateOrCreate(
                ['tipe_laporan' => $item['tipe_laporan']],
                $item
            );
        }
    }
}
