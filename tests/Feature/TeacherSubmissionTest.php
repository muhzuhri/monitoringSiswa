<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\PengajuanGuru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_pengajuan_page()
    {
        $siswa = Siswa::factory()->create([
            'nisn' => '12345',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($siswa);

        $response = $this->get(route('siswa.pengajuan'));

        $response->assertStatus(200);
        $response->assertViewIs('siswa.pengajuan');
    }

    public function test_student_can_submit_teacher_proposal()
    {
        $siswa = Siswa::factory()->create([
            'nisn' => '12345',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($siswa);

        $proposalData = [
            'nip' => '198001012023011001',
            'nama' => 'Guru Test',
            'no_hp' => '081234567890',
        ];

        $response = $this->post(route('siswa.pengajuan.store'), $proposalData);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuan_gurus', [
            'siswa_nisn' => '12345',
            'nip' => '198001012023011001',
            'nama' => 'Guru Test',
            'status' => 'pending',
        ]);
    }
}
