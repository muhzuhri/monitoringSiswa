<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonfigurasiLaporan extends Model
{
    protected $fillable = [
        'tipe_laporan',
        'header_1',
        'header_2',
        'header_3',
        'header_4',
        'header_5',
        'color_primary',
        'color_secondary',
        'background_color',
        'template_isi',
    ];
}
