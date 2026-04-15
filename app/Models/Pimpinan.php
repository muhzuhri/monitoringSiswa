<?php

namespace App\Models;

use App\Contracts\HasRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pimpinan extends Authenticatable implements HasRole
{
    use HasFactory, Notifiable;

    protected $table = 'pimpinan';
    protected $primaryKey = 'id_pimpinan';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'jabatan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getAuthIdentifierName(): string
    {
        return 'id_pimpinan';
    }

    public function getAuthIdentifier(): string
    {
        return 'pimpinan_' . $this->getKey();
    }

    public function getRole(): string
    {
        return 'pimpinan';
    }
}
