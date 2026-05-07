<?php

use App\Http\Controllers\AdminPembimbingController;
use App\Http\Controllers\AdminSiswaController;
use App\Http\Controllers\AdminGuruController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\PembimbingController;
use App\Http\Controllers\PimpinanController;
use App\Http\Controllers\AdminMasterDataController;
use App\Http\Controllers\Api\SchoolApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'index'])
    ->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/api/schools/{npsn}', [SchoolApiController::class, 'getSchoolByNpsn'])->name('api.schools.show');
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
    Route::get('/siswa/laporan/sertifikat', [SiswaController::class, 'cetakSertifikat'])->name('siswa.sertifikat.cetak');



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

    Route::get('/guru/siswa/{nisn}/download-jurnal', [GuruController::class, 'downloadJurnalMingguan'])->name('guru.rekap.jurnal');
    Route::get('/guru/siswa/{nisn}/download-absensi', [GuruController::class, 'downloadRekapAbsensiIndividu'])->name('guru.rekap.absensi');
    Route::get('/guru/siswa/{nisn}/download-rekap-kelompok', [GuruController::class, 'downloadRekapAbsensiKelompok'])->name('guru.rekap.kelompok');

    // Tambahan Aksi Riwayat Siswa (Guru)
    Route::get('/guru/siswa/{nisn}/cetak-penilaian-pembimbing', [GuruController::class, 'cetakPenilaianPembimbing'])->name('guru.siswa.cetakPenilaianPembimbing');
    Route::get('/guru/siswa/{nisn}/cetak-sertifikat', [GuruController::class, 'cetakSertifikatSiswa'])->name('guru.siswa.cetakSertifikat');
    Route::get('/guru/siswa/{nisn}/cetak-laporan-akhir', [GuruController::class, 'cetakLaporanAkhir'])->name('guru.siswa.cetakLaporan');



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
    Route::get('/pembimbing/siswa/{nisn}/cetak-penilaian-guru', [PembimbingController::class, 'cetakPenilaianGuru'])->name('pembimbing.siswa.cetakPenilaianGuru');
    Route::get('/pembimbing/siswa/{nisn}/cetak-laporan-akhir', [PembimbingController::class, 'cetakLaporanAkhir'])->name('pembimbing.siswa.cetakLaporan');
    Route::get('/pembimbing/siswa/{nisn}/cetak-sertifikat', [PembimbingController::class, 'cetakSertifikatSiswa'])->name('pembimbing.siswa.cetakSertifikat');
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

    // Manajemen Lokasi Absensi oleh admin
    Route::get('/admin/lokasi', [App\Http\Controllers\AdminSettingController::class, 'index'])->name('admin.kelolaLokasi');
    Route::post('/admin/lokasi', [App\Http\Controllers\AdminSettingController::class, 'store'])->name('admin.storeLokasi');
    Route::put('/admin/lokasi/{id}', [App\Http\Controllers\AdminSettingController::class, 'update'])->name('admin.updateLokasi');
    Route::delete('/admin/lokasi/{id}', [App\Http\Controllers\AdminSettingController::class, 'destroy'])->name('admin.destroyLokasi');

    // Manajemen Rekap Baru oleh admin
    Route::get('/admin/rekap', [App\Http\Controllers\AdminRekapController::class, 'index'])->name('admin.rekap');
    Route::get('/admin/rekap/stats', [App\Http\Controllers\AdminRekapController::class, 'getStats'])->name('admin.rekap.stats');
    Route::get('/admin/rekap/siswa-aktif', [App\Http\Controllers\AdminRekapController::class, 'rekapSiswaAktif'])->name('admin.rekap.siswaAktif');
    Route::get('/admin/rekap/siswa-selesai', [App\Http\Controllers\AdminRekapController::class, 'rekapSiswaSelesai'])->name('admin.rekap.siswaSelesai');
    Route::get('/admin/rekap/siswa-total', [App\Http\Controllers\AdminRekapController::class, 'rekapSiswaTotal'])->name('admin.rekap.siswaTotal');
    Route::get('/admin/rekap/guru', [App\Http\Controllers\AdminRekapController::class, 'rekapGuru'])->name('admin.rekap.guru');

    // Master Data (Sekolah & Periode)
    Route::get('/admin/master-data', [AdminMasterDataController::class, 'index'])->name('admin.masterData');
    Route::post('/admin/sekolah', [AdminMasterDataController::class, 'storeSekolah'])->name('admin.storeSekolah');
    Route::put('/admin/sekolah/{id}', [AdminMasterDataController::class, 'updateSekolah'])->name('admin.updateSekolah');
    Route::delete('/admin/sekolah/{id}', [AdminMasterDataController::class, 'destroySekolah'])->name('admin.destroySekolah');

    Route::post('/admin/periode', [AdminMasterDataController::class, 'storePeriode'])->name('admin.storePeriode');
    Route::put('/admin/periode/{id}', [AdminMasterDataController::class, 'updatePeriode'])->name('admin.updatePeriode');
    Route::delete('/admin/periode/{id}', [AdminMasterDataController::class, 'destroyPeriode'])->name('admin.destroyPeriode');

    // Informasi Dashboard
    Route::put('/admin/informasi', [AdminMasterDataController::class, 'updateInformasi'])->name('admin.updateInformasi');

    // Program Studi
    Route::post('/admin/prodi', [AdminMasterDataController::class, 'storeProdi'])->name('admin.storeProdi');
    Route::put('/admin/prodi/{id}', [AdminMasterDataController::class, 'updateProdi'])->name('admin.updateProdi');
    Route::delete('/admin/prodi/{id}', [AdminMasterDataController::class, 'destroyProdi'])->name('admin.destroyProdi');
    
    // Profil Admin
    Route::get('/admin/profil', [AuthController::class, 'adminProfil'])->name('admin.profil');
    Route::put('/admin/profil/update', [AuthController::class, 'updateAdminProfil'])->name('admin.profil.update');
    Route::put('/admin/profil/password', [AuthController::class, 'updateAdminPassword'])->name('admin.profil.password');
});

// ─────────────────────────────────────────────────────────────────────────
// RUTE PIMPINAN
// ─────────────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('pimpinan')->name('pimpinan.')->group(function () {
    Route::get('/home', [PimpinanController::class, 'index'])->name('home');
    
    // Kelola Akun (Admin, Siswa, Guru, Pembimbing)
    Route::get('/admin', [PimpinanController::class, 'kelolaAdmin'])->name('admin');
    Route::post('/admin', [PimpinanController::class, 'storeAdmin'])->name('storeAdmin');
    Route::put('/admin/{id}', [PimpinanController::class, 'updateAdmin'])->name('updateAdmin');
    Route::delete('/admin/{id}', [PimpinanController::class, 'destroyAdmin'])->name('destroyAdmin');

    Route::get('/siswa', [PimpinanController::class, 'siswa'])->name('siswa');
    Route::get('/guru', [PimpinanController::class, 'guru'])->name('guru');
    Route::get('/pembimbing', [PimpinanController::class, 'pembimbing'])->name('pembimbing');
    
    Route::get('/rekap', [PimpinanController::class, 'rekap'])->name('rekap');
    Route::get('/rekap/stats', [PimpinanController::class, 'rekapStats'])->name('rekap.stats');
    
    // Profil Pimpinan
    Route::get('/profil', [PimpinanController::class, 'profil'])->name('profil');
    Route::put('/profil/update', [PimpinanController::class, 'updateProfil'])->name('profil.update');
    Route::put('/profil/password', [PimpinanController::class, 'updatePassword'])->name('profil.password');
});