<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiAbsensi extends Model
{
    protected $table = 'lokasi_absensi';

    protected $fillable = [
        'nama_lokasi',
        'latitude',
        'longitude',
        'radius',
        'is_active',
    ];
}
