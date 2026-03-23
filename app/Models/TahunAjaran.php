<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';
    protected $primaryKey = 'id_tahun_ajaran';

    protected $fillable = [
        'tahun_ajaran',
        'tgl_mulai',
        'tgl_selesai',
        'status',
    ];

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }

    public function gurus()
    {
        return $this->hasMany(Guru::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }
}
