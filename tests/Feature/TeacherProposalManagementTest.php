<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\PengajuanGuru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherProposalManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_see_pending_proposals()
    {
        $guru = Guru::factory()->create([
            'nip' => '12345678',
            'password' => bcrypt('password'),
        ]);

        $siswa = Siswa::factory()->create(['nisn' => 'S001']);

        PengajuanGuru::create([
            'siswa_nisn' => 'S001',
            'nip' => '12345678',
            'nama' => $guru->name,
            'no_hp' => '0812345',
            'status' => 'pending',
        ]);

        $this->actingAs($guru, 'guru');

        $response = $this->get(route('guru.siswa'));

        $response->assertStatus(200);
        $response->assertSee($siswa->name);
    }

    public function test_teacher_can_approve_proposal()
    {
        $guru = Guru::factory()->create([
            'nip' => '12345678',
            'password' => bcrypt('password'),
        ]);

        $siswa = Siswa::factory()->create(['nisn' => 'S001', 'guru_nip' => null]);

        $pengajuan = PengajuanGuru::create([
            'siswa_nisn' => 'S001',
            'nip' => '12345678',
            'nama' => $guru->name,
            'no_hp' => '0812345',
            'status' => 'pending',
        ]);

        $this->actingAs($guru, 'guru');

        $response = $this->post(route('guru.pengajuan.setujui', $pengajuan->id));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuan_gurus', [
            'id' => $pengajuan->id,
            'status' => 'disetujui',
        ]);

        $this->assertDatabaseHas('siswa', [
            'nisn' => 'S001',
            'guru_nip' => '12345678',
        ]);
    }

    public function test_teacher_can_reject_proposal()
    {
        $guru = Guru::factory()->create([
            'nip' => '12345678',
            'password' => bcrypt('password'),
        ]);

        $siswa = Siswa::factory()->create(['nisn' => 'S001']);

        $pengajuan = PengajuanGuru::create([
            'siswa_nisn' => 'S001',
            'nip' => '12345678',
            'nama' => $guru->name,
            'no_hp' => '0812345',
            'status' => 'pending',
        ]);

        $this->actingAs($guru, 'guru');

        $response = $this->post(route('guru.pengajuan.tolak', $pengajuan->id));

        $response->assertStatus(302);
        $response->assertSessionHas('warning');

        $this->assertDatabaseHas('pengajuan_gurus', [
            'id' => $pengajuan->id,
            'status' => 'ditolak',
        ]);
    }
}
