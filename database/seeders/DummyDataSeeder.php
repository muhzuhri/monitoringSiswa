<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat atau ambil Tahun Ajaran pendukung data dummy (Jan - Apr 2026)
        $tahunAjaran = \App\Models\TahunAjaran::updateOrCreate(
            ['tahun_ajaran' => '2026/2027'],
            [
                'tgl_mulai' => '2026-01-01',
                'tgl_selesai' => '2026-12-31',
                'status' => 'aktif'
            ]
        );

        // NISN Peserta dummy
        $nisns = ['123456789', '234567890', '345678901'];
        
        // Rentang Waktu pengujian: 1 Januari 2026 s/d 30 April 2026
        $start = \Carbon\Carbon::create(2026, 1, 1);
        $end = \Carbon\Carbon::create(2026, 4, 30);

        echo "Memulai generasi data dummy (Jan - Apr)...\n";

        foreach ($nisns as $nisn) {
            $siswa = \App\Models\Siswa::where('nisn', $nisn)->first();
            if (!$siswa) {
                echo "Siswa dengan NISN $nisn tidak ditemukan, skip...\n";
                continue;
            }

            // Assign tahun ajaran ke siswa
            $siswa->update(['tahun_ajaran_id' => $tahunAjaran->id_tahunAjaran]);

            $current = $start->copy();
            while ($current <= $end) {
                if (!$current->isWeekend()) {
                    // Simpan Absensi
                    \App\Models\Absensi::updateOrCreate(
                        ['siswa_nisn' => $nisn, 'tanggal' => $current->toDateString()],
                        [
                            'jam_masuk' => '07:30:00',
                            'jam_pulang' => '16:00:00',
                            'status' => 'hadir',
                            'verifikasi' => 'verified'
                        ]
                    );

                    // Khusus Ketua (Muh Zuhri) buatkan logbook
                    if ($nisn === '123456789') {
                        \App\Models\Logbook::updateOrCreate(
                            ['siswa_nisn' => $nisn, 'tanggal' => $current->toDateString()],
                            [
                                'kegiatan' => "Melakukan kegiatan rutin PKL: Pengembangan modul laporan, testing fitur PDF, dan sinkronisasi data absensi kelompok untuk periode " . $current->format('M Y'),
                                'status' => 'verified'
                            ]
                        );
                    }
                }
                $current->addDay();
            }
        }
        echo "Data dummy berhasil ditambahkan!\n";
    }
}
