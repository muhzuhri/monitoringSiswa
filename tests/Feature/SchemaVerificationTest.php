<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SchemaVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_can_be_created_with_nip()
    {
        $this->withoutExceptionHandling();
        $guru = Guru::create([
            'nip' => 'GURU123',
            'name' => 'Pak Guru',
            'email' => 'guru@example.com',
            'password' => Hash::make('password'),
            'mapel' => 'Rekayasa Perangkat Lunak',
            'sekolah' => 'SMK 1',
        ]);

        $this->assertDatabaseHas('guru', [
            'nip' => 'GURU123',
            'email' => 'guru@example.com',
        ]);

        $this->assertEquals('GURU123', $guru->nip);
        $this->assertEquals('GURU123', $guru->getKey());
    }

    public function test_siswa_can_be_created_with_nisn_and_new_fields()
    {
        $guru = Guru::create([
            'nip' => 'GURU123',
            'name' => 'Pak Guru',
            'email' => 'guru@example.com',
            'password' => Hash::make('password'),
            'mapel' => 'Rekayasa Perangkat Lunak',
            'sekolah' => 'SMK 1',
        ]);

        $siswa = Siswa::create([
            'nisn' => 'SISWA123',
            'name' => 'Murid Teladan',
            'email' => 'siswa@example.com',
            'password' => Hash::make('password'),
            'kelas' => 'XII RPL 1',
            'jurusan' => 'RPL',
            'sekolah' => 'SMK 1',
            'perusahaan' => 'Tech Corp',
            'jenis_kelamin' => 'L',
            'tgl_mulai_magang' => '2024-01-01',
            'tgl_selesai_magang' => '2024-06-30',
            'guru_nip' => $guru->nip,
        ]);

        $this->assertDatabaseHas('siswa', [
            'nisn' => 'SISWA123',
            'email' => 'siswa@example.com',
            'jenis_kelamin' => 'L',
            'guru_nip' => 'GURU123',
        ]);

        $this->assertEquals('SISWA123', $siswa->nisn);
        $this->assertEquals('SISWA123', $siswa->getKey());
        $this->assertEquals('GURU123', $siswa->guru->nip);
    }
}
