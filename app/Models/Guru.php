<?php

namespace App\Models;

use App\Contracts\HasRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guru extends Authenticatable implements HasRole
{
    use HasFactory, Notifiable;

    protected $table = 'guru';
    protected $primaryKey = 'id_guru';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_guru',
        'nama',
        'email',
        'no_hp',
        'password',
        'jabatan',
        'sekolah',
        'npsn',
        'id_tahun_ajaran',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'id_guru', 'id_guru');
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
        return 'guru_id';
    }

    public function getAuthIdentifier(): string
    {
        return 'guru_' . $this->getKey();
    }

    public function getRole(): string
    {
        return 'guru';
    }
}
