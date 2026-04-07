<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanSiswa extends Model
{
    protected $table = 'pengajuan_siswas';
    protected $primaryKey = 'id_pengajuan';

    protected $fillable = [
        'nisn',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'jenis',
        'deskripsi',
        'alasan_terlambat',
        'bukti',
        'status',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nisn', 'nisn');
    }
}
