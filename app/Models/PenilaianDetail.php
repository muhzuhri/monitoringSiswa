<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianDetail extends Model
{
    use HasFactory;

    protected $table = 'penilaian_detail';
    protected $primaryKey = 'id_penilaian_detail';

    protected $fillable = [
        'id_penilaian',
        'id_kriteria',
        'skor',
    ];

    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class, 'id_penilaian', 'id_penilaian');
    }

    public function kriteria()
    {
        return $this->belongsTo(KriteriaPenilaian::class, 'id_kriteria', 'id_kriteria');
    }
}
