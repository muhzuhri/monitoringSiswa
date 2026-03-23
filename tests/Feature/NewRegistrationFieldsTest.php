<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewRegistrationFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_with_npsn_and_surat_balasan()
    {
        Storage::fake('public');

        $suratBalasan = UploadedFile::fake()->create('surat_balasan.pdf', 100);

        $userData = [
            'name' => 'Test Siswa New',
            'email' => 'testsiswanew@example.com',
            'no_hp' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'siswa',
            'nisn' => '1122334455',
            'kelas' => 'XII RPL 2',
            'jurusan' => 'Rekayasa Perangkat Lunak',
            'sekolah_siswa' => 'SMK Maju Jaya',
            'npsn_siswa' => '12345678',
            'surat_balasan' => $suratBalasan,
            'perusahaan' => 'Tech Solutions',
            'jenis_kelamin' => 'P',
            'tgl_mulai_magang' => '2024-02-01',
            'tgl_selesai_magang' => '2024-07-01',
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $siswa = Siswa::where('email', 'testsiswanew@example.com')->first();
        $this->assertNotNull($siswa);
        $this->assertEquals('12345678', $siswa->npsn);
        $this->assertNotNull($siswa->surat_balasan);

        Storage::disk('public')->assertExists($siswa->surat_balasan);
    }

    public function test_teacher_can_register_with_npsn()
    {
        $userData = [
            'name' => 'Test Guru New',
            'email' => 'testgurunew@example.com',
            'no_hp' => '081234567891',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'guru',
            'nip' => '9988776655',
            'mapel' => 'Matematika',
            'sekolah_guru' => 'SMK Maju Jaya',
            'npsn_guru' => '12345678',
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $guru = Guru::where('email', 'testgurunew@example.com')->first();
        $this->assertNotNull($guru);
        $this->assertEquals('12345678', $guru->npsn);
    }

    public function test_registration_fails_if_required_fields_missing()
    {
        $response = $this->post(route('register'), [
            'role' => 'siswa',
            // missing other fields
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'no_hp', 'password', 'nisn', 'npsn_siswa', 'surat_balasan']);
    }
}
