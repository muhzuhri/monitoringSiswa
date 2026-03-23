<?php

namespace App\Models;

use App\Contracts\HasRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Siswa extends Authenticatable implements HasRole
{
    use HasFactory, Notifiable;

    protected $table = 'siswa';
    protected $primaryKey = 'nisn';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nisn',
        'nama',
        'email',
        'no_hp',
        'password',
        'kelas',
        'jurusan',
        'sekolah',
        'npsn',
        'surat_balasan',
        'perusahaan',
        'jenis_kelamin',
        'status',
        'foto_profil',
        'id_guru',
        'id_pembimbing',
        'tipe_magang',
        'nisn_ketua',
        'id_tahun_ajaran',
    ];

    public function ketua()
    {
        return $this->belongsTo(Siswa::class, 'nisn_ketua', 'nisn');
    }

    public function anggotaKelompok()
    {
        return $this->hasMany(Siswa::class, 'nisn_ketua', 'nisn_ketua');
    }

    public function isLeader()
    {
        return $this->nisn === $this->nisn_ketua;
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAuthIdentifierName(): string
    {
        return 'siswa_id';
    }

    public function getAuthIdentifier(): string
    {
        return 'siswa_' . $this->getKey();
    }

    public function getRole(): string
    {
        return 'siswa';
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'nisn', 'nisn');
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class, 'nisn', 'nisn');
    }

    public function penilaians()
    {
        return $this->hasMany(Penilaian::class, 'nisn', 'nisn');
    }

    public function laporanAkhir()
    {
        return $this->hasOne(LaporanAkhir::class, 'nisn', 'nisn')->latestOfMany('id_laporan');
    }

    public function laporanAkhirs()
    {
        return $this->hasMany(LaporanAkhir::class, 'nisn', 'nisn');
    }

    public function pembimbing()
    {
        return $this->belongsTo(Pembimbing::class, 'id_pembimbing', 'id_pembimbing');
    }
}
