<?php

namespace Tests\Feature;

use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationWithoutGuruTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_without_guru_and_login()
    {
        $this->withoutExceptionHandling();
        $userData = [
            'name' => 'Test Siswa',
            'email' => 'testsiswa@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'siswa',
            'nisn' => '1234567890',
            'kelas' => 'XII RPL 1',
            'jurusan' => 'Rekayasa Perangkat Lunak',
            'sekolah_siswa' => 'SMK Alice',
            'perusahaan' => 'Tech Corp',
            'jenis_kelamin' => 'L',
            'tgl_mulai_magang' => '2024-01-01',
            'tgl_selesai_magang' => '2024-06-01',
            'guru_nip' => null, 
        ];

        $response = $this->post(route('register'), $userData);

        if ($response->status() !== 302) {
             dump($response->getContent());
        }

        $response->assertStatus(302);
        
        // Follow redirect or check session
        // $response->assertRedirect(route('login'));
        
        if (session('errors')) {
            dump(session('errors')->all());
        }
        
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('siswa', [
            'email' => 'testsiswa@example.com',
            'guru_nip' => null,
        ]);

        // Attempt Login
        $loginData = [
            'email' => 'testsiswa@example.com',
            'password' => 'password',
        ];

        $loginResponse = $this->post(route('login'), $loginData);
        
        if ($loginResponse->status() !== 302) {
             dump($loginResponse->getContent());
        }
        
        $loginResponse->assertRedirect(route('siswa.siswa'));
        $this->assertAuthenticated('web');
    }
}
