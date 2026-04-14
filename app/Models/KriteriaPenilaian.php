<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KriteriaPenilaian extends Model
{
    use HasFactory;

    protected $table = 'kriteria_penilaian';
    protected $primaryKey = 'id_kriteria';

    protected $fillable = [
        'nama_kriteria',
        'tipe',
        'jurusan',
        'urutan',
        'id_pembimbing',
        'id_guru',
    ];

    public function penilaianDetails()
    {
        return $this->hasMany(PenilaianDetail::class, 'id_kriteria', 'id_kriteria');
    }
}
