<?php

namespace App\Models;

use App\Contracts\HasRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pembimbing extends Authenticatable implements HasRole
{
    use HasFactory, Notifiable;

    protected $table = 'pembimbing';
    protected $primaryKey = 'id_pembimbing';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pembimbing',
        'nama',
        'email',
        'password',
        'jabatan',
        'instansi',
        'no_telp',
    ];

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
        return 'pembimbing_id';
    }

    public function getAuthIdentifier(): string
    {
        return 'pembimbing_' . $this->getKey();
    }

    public function getRole(): string
    {
        return 'pembimbing';
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'id_pembimbing', 'id_pembimbing');
    }
}
