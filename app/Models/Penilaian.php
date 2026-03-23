<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';
    protected $primaryKey = 'id_penilaian';

    protected $fillable = [
        'nisn',
        'pemberi_nilai',
        'nilai_teknis',
        'nilai_non_teknis',
        'kedisiplinan',
        'keterampilan',
        'sikap',
        'kerjasama',
        'nilai_instansi',
        'rata_rata',
        'komentar',
        'saran',
        'kategori',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nisn', 'nisn');
    }

    public function penilaianDetails()
    {
        return $this->hasMany(PenilaianDetail::class, 'id_penilaian', 'id_penilaian');
    }
}
