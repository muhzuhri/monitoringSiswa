<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KriteriaPenilaian;

class GuruCriteriaSeeder extends Seeder
{
    public function run()
    {
        $criteria = [
            ['nama_kriteria' => 'DISIPLIN', 'tipe' => 'guru_kepribadian', 'urutan' => 1],
            ['nama_kriteria' => 'INISIATIF', 'tipe' => 'guru_kepribadian', 'urutan' => 2],
            ['nama_kriteria' => 'KREATIVITAS', 'tipe' => 'guru_kepribadian', 'urutan' => 3],
            ['nama_kriteria' => 'KESEHATAN DAN KESELAMATAN KERJA', 'tipe' => 'guru_kepribadian', 'urutan' => 4],
            ['nama_kriteria' => 'TEORI', 'tipe' => 'guru_kemampuan', 'urutan' => 1],
            ['nama_kriteria' => 'PRAKTEK', 'tipe' => 'guru_kemampuan', 'urutan' => 2],
        ];

        foreach ($criteria as $data) {
            KriteriaPenilaian::updateOrCreate(
                ['nama_kriteria' => $data['nama_kriteria'], 'tipe' => $data['tipe']],
                ['urutan' => $data['urutan']]
            );
        }
    }
}
