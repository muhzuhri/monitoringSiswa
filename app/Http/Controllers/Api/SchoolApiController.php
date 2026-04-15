<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class SchoolApiController extends Controller
{
    public function getSchoolByNpsn($npsn)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->first();

        if ($sekolah) {
            return response()->json([
                'success' => true,
                'data' => $sekolah
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Sekolah tidak ditemukan'
        ], 404);
    }
}
