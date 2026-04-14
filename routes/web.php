<?php

use App\Http\Controllers\AdminPembimbingController;
use App\Http\Controllers\AdminSiswaController;
use App\Http\Controllers\AdminGuruController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\PembimbingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'index'])
    ->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Halaman peran (siswa, guru, pembimbing, admin)
Route::middleware('auth')->group(function () {
    Route::get('/siswa/siswa', [SiswaController::class, 'dashboard'])->name('siswa.siswa');
    Route::get('/siswa/absensi', [SiswaController::class, 'absensiKegiatan'])->name('siswa.absensi');
    Route::get('/siswa/absensi/detail', [SiswaController::class, 'getAbsensiDetail'])->name('siswa.absensi.detail');
    Route::get('/siswa/logbook/detail', [SiswaController::class, 'getLogbookDetail'])->name('siswa.logbook.detail');
    Route::post('/siswa/absensi', [SiswaController::class, 'storeAbsensi'])->name('siswa.absensi.store');
    Route::post('/siswa/logbook', [SiswaController::class, 'storeLogbook'])->name('siswa.logbook.store');

    // Pengajuan Lupa Absensi / Kegiatan
    Route::get('/siswa/pengajuan', [SiswaController::class, 'pengajuan'])->name('siswa.pengajuan');
    Route::post('/siswa/pengajuan', [SiswaController::class, 'storePengajuan'])->name('siswa.pengajuan.store');

    // Profil Siswa
    Route::get('/siswa/profil', [SiswaController::class, 'showProfile'])->name('siswa.profil');
    Route::put('/siswa/profil', [SiswaController::class, 'updateProfile'])->name('siswa.profil.update');
    Route::put('/siswa/profil/password', [SiswaController::class, 'updatePassword'])->name('siswa.profil.password');

    // Laporan
    Route::get('/siswa/laporan', [SiswaController::class, 'laporan'])->name('siswa.laporan');
    Route::post('/siswa/laporan/upload', [SiswaController::class, 'uploadLaporanAkhir'])->name('siswa.laporan.upload');
    Route::get('/siswa/laporan/download-akhir', [SiswaController::class, 'previewLaporanAkhir'])->name('siswa.laporan.downloadAkhir');
    Route::get('/siswa/laporan/download/jurnal-mingguan', [SiswaController::class, 'downloadJurnalMingguan'])->name('siswa.rekap.jurnal');
    Route::get('/siswa/laporan/download/rekap-individu', [SiswaController::class, 'downloadRekapAbsensiIndividu'])->name('siswa.rekap.individu');
    Route::get('/siswa/laporan/download/rekap-kelompok', [SiswaController::class, 'downloadRekapAbsensiKelompok'])->name('siswa.rekap.kelompok');
    Route::get('/siswa/penilaian/cetak', [SiswaController::class, 'cetakPenilaian'])->name('siswa.penilaian.cetak');


    Route::get('/guru/guru', [GuruController::class, 'dashboard'])->name('guru.guru');
    Route::get('/guru/siswa', [GuruController::class, 'daftarSiswa'])->name('guru.siswa');
    Route::get('/guru/siswa/{nisn}/logbook', [GuruController::class, 'logbookSiswa'])->name('guru.logbook');
    Route::post('/guru/logbook/{id}/verifikasi', [GuruController::class, 'verifikasiLogbook'])->name('guru.logbook.verifikasi');
    Route::get('/guru/siswa/{nisn}/absensi', [GuruController::class, 'absensiSiswa'])->name('guru.absensi');
    Route::get('/guru/siswa/{nisn}/absensi/export', [GuruController::class, 'exportAbsensiSiswa'])->name('guru.absensi.export');
    Route::get('/guru/verifikasi', [GuruController::class, 'verifikasiLaporan'])->name('guru.verifikasi');
    Route::get('/guru/verifikasi/{id}', [GuruController::class, 'showVerifikasiLaporan'])->name('guru.verifikasi.show');
    Route::post('/guru/verifikasi/{id}', [GuruController::class, 'updateVerifikasiLaporan'])->name('guru.verifikasi.update');

    Route::get('/guru/penilaian', [GuruController::class, 'daftarPenilaian'])->name('guru.penilaian');
    Route::get('/guru/penilaian/{nisn}', [GuruController::class, 'inputPenilaian'])->name('guru.penilaian.input');
    Route::post('/guru/penilaian/{nisn}', [GuruController::class, 'storePenilaian'])->name('guru.penilaian.store');
    Route::get('/guru/penilaian/{nisn}/export', [GuruController::class, 'exportPenilaian'])->name('guru.penilaian.export');

    // Manajemen Kriteria Penilaian Guru (Dynamic)
    Route::post('/guru/kriteria', [GuruController::class, 'storeKriteria'])->name('guru.kriteria.store');
    Route::put('/guru/kriteria/{id}', [GuruController::class, 'updateKriteria'])->name('guru.kriteria.update');
    Route::delete('/guru/kriteria/{id}', [GuruController::class, 'destroyKriteria'])->name('guru.kriteria.destroy');

    // Claim Siswa (Teacher side)
    Route::post('/guru/siswa/{nisn}/claim', [GuruController::class, 'claimSiswa'])->name('guru.siswa.claim');

    // Profil Guru
    Route::get('/guru/profil', [GuruController::class, 'profil'])->name('guru.profil');
    Route::post('/guru/profil', [GuruController::class, 'updateProfil'])->name('guru.profil.update');

    // Download Reports (Guru)
    Route::get('/guru/siswa/{nisn}/download-jurnal', [GuruController::class, 'downloadJurnalMingguan'])->name('guru.rekap.jurnal');
    Route::get('/guru/siswa/{nisn}/download-absensi', [GuruController::class, 'downloadRekapAbsensiIndividu'])->name('guru.rekap.absensi');
    Route::get('/guru/siswa/{nisn}/download-rekap-kelompok', [GuruController::class, 'downloadRekapAbsensiKelompok'])->name('guru.rekap.kelompok');



    Route::get('/pembimbing/pembimbing', [AuthController::class, 'pembimbing'])->name('pembimbing.pembimbing');
    Route::get('/pembimbing/siswa', [PembimbingController::class, 'daftarSiswa'])->name('pembimbing.siswa');
    Route::get('/pembimbing/siswa/{nisn}/absensi', [PembimbingController::class, 'absensiSiswa'])->name('pembimbing.absensi');
    Route::post('/pembimbing/absensi/{id}/validasi', [PembimbingController::class, 'validasiAbsensi'])->name('pembimbing.absensi.validasi');
    Route::get('/pembimbing/siswa/{nisn}/logbook', [PembimbingController::class, 'logbookSiswa'])->name('pembimbing.logbook');
    Route::post('/pembimbing/logbook/{id}/validasi', [PembimbingController::class, 'validasiLogbook'])->name('pembimbing.logbook.validasi');
    
    // Bulk Validation
    Route::post('/pembimbing/logbook/siswa/{nisn}/validasi-semua', [PembimbingController::class, 'validasiSemuaLogbook'])->name('pembimbing.logbook.validasi-semua');
    Route::post('/pembimbing/absensi/siswa/{nisn}/validasi-semua', [PembimbingController::class, 'validasiSemuaAbsensi'])->name('pembimbing.absensi.validasi-semua');
    Route::get('/pembimbing/evaluasi', [PembimbingController::class, 'evaluasiSiswa'])->name('pembimbing.evaluasi');
    Route::get('/pembimbing/evaluasi/input/{nisn}', [PembimbingController::class, 'inputEvaluasi'])->name('pembimbing.evaluasi.input');
    Route::post('/pembimbing/evaluasi', [PembimbingController::class, 'storeEvaluasi'])->name('pembimbing.evaluasi.store');
    
    // Pengajuan Siswa
    Route::get('/pembimbing/pengajuan', [PembimbingController::class, 'pengajuanSiswa'])->name('pembimbing.pengajuan');
    Route::post('/pembimbing/pengajuan/{id}', [PembimbingController::class, 'updatePengajuan'])->name('pembimbing.pengajuan.update');

    // Reporting
    Route::get('/pembimbing/siswa/{nisn}/cetak-jurnal', [PembimbingController::class, 'cetakJurnalSiswa'])->name('pembimbing.siswa.cetakJurnal');
    Route::get('/pembimbing/siswa/{nisn}/cetak-absensi', [PembimbingController::class, 'cetakAbsensiSiswa'])->name('pembimbing.siswa.cetakAbsensi');
    
    Route::get('/pembimbing/laporan/{nisn}/cetak', [PembimbingController::class, 'cetakLaporanSiswa'])->name('pembimbing.laporan.cetak');
    Route::get('/pembimbing/profil', [PembimbingController::class, 'profil'])->name('pembimbing.profil');
    Route::post('/pembimbing/profil', [PembimbingController::class, 'updateProfil'])->name('pembimbing.profil.update');

    // Manajemen Kriteria Penilaian (Dynamic)
    Route::get('/pembimbing/kriteria', [PembimbingController::class, 'kriteriaPenilaian'])->name('pembimbing.kriteria');
    Route::post('/pembimbing/kriteria', [PembimbingController::class, 'storeKriteria'])->name('pembimbing.kriteria.store');
    Route::put('/pembimbing/kriteria/{id}', [PembimbingController::class, 'updateKriteria'])->name('pembimbing.kriteria.update');
    Route::delete('/pembimbing/kriteria/{id}', [PembimbingController::class, 'destroyKriteria'])->name('pembimbing.kriteria.destroy');

    Route::get('/admin/admin', [AuthController::class, 'admin'])->name('admin.admin');

    Route::get('/admin/pembimbing', [AdminPembimbingController::class, 'kelolaPembimbing'])->name('admin.kelolaPembimbing');
    Route::post('/admin/pembimbing', [AdminPembimbingController::class, 'store'])->name('admin.storePembimbing');
    Route::put('/admin/pembimbing/{id}', [AdminPembimbingController::class, 'update'])->name('admin.updatePembimbing');
    Route::delete('/admin/pembimbing/{id}', [AdminPembimbingController::class, 'destroy'])->name('admin.destroyPembimbing');

    // Manajemen siswa oleh admin
    Route::get('/admin/siswa', [AdminSiswaController::class, 'kelolaSiswa'])->name('admin.kelolaSiswa');
    Route::post('/admin/siswa', [AdminSiswaController::class, 'store'])->name('admin.storeSiswa');
    Route::put('/admin/siswa/{id}', [AdminSiswaController::class, 'update'])->name('admin.updateSiswa');
    Route::delete('/admin/siswa/{id}', [AdminSiswaController::class, 'destroy'])->name('admin.destroySiswa');
    Route::get('/admin/siswa/{nisn}/absensi', [AdminSiswaController::class, 'absensiSiswa'])->name('admin.siswa.absensi');
    Route::get('/admin/siswa/{nisn}/logbook', [AdminSiswaController::class, 'logbookSiswa'])->name('admin.siswa.logbook');

    // Admin Rekap Routes
    Route::get('/admin/siswa/{nisn}/download-jurnal', [AdminSiswaController::class, 'downloadJurnalMingguan'])->name('admin.rekap.jurnal');
    Route::get('/admin/siswa/{nisn}/download-absensi', [AdminSiswaController::class, 'downloadRekapAbsensiIndividu'])->name('admin.rekap.absensi');
    Route::get('/admin/siswa/{nisn}/download-rekap-kelompok', [AdminSiswaController::class, 'downloadRekapAbsensiKelompok'])->name('admin.rekap.kelompok');

    // Manajemen guru oleh admin
    Route::get('/admin/guru', [AdminGuruController::class, 'kelolaGuru'])->name('admin.kelolaGuru');
    Route::post('/admin/guru', [AdminGuruController::class, 'store'])->name('admin.storeGuru');
    Route::put('/admin/guru/{id}', [AdminGuruController::class, 'update'])->name('admin.updateGuru');
    Route::delete('/admin/guru/{id}', [AdminGuruController::class, 'destroy'])->name('admin.destroyGuru');
});