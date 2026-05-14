<?php

use App\Http\Controllers\AdminPembimbingController;
use App\Http\Controllers\AdminSiswaController;
use App\Http\Controllers\AdminGuruController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaDashboardController;
use App\Http\Controllers\SiswaKegiatanController;
use App\Http\Controllers\SiswaPengajuanController;
use App\Http\Controllers\SiswaProfilController;
use App\Http\Controllers\SiswaLaporanController;
use App\Http\Controllers\PembimbingSiswaController;
use App\Http\Controllers\PembimbingEvaluasiController;
use App\Http\Controllers\PembimbingPengajuanController;
use App\Http\Controllers\PembimbingLaporanController;
use App\Http\Controllers\PembimbingProfilController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\GuruSiswaController;
use App\Http\Controllers\GuruVerifikasiController;
use App\Http\Controllers\GuruPenilaianController;
use App\Http\Controllers\GuruLaporanController;
use App\Http\Controllers\GuruProfilController;
use App\Http\Controllers\PimpinanDashboardController;


use App\Http\Controllers\PimpinanAdminController;
use App\Http\Controllers\PimpinanMonitoringController;
use App\Http\Controllers\PimpinanRekapController;
use App\Http\Controllers\PimpinanProfilController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\PembimbingController;
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
    Route::get('/siswa/siswa', [SiswaDashboardController::class, 'dashboard'])->name('siswa.siswa');
    Route::get('/siswa/absensi', [SiswaKegiatanController::class, 'absensiKegiatan'])->name('siswa.absensi');
    Route::get('/siswa/absensi/detail', [SiswaKegiatanController::class, 'getAbsensiDetail'])->name('siswa.absensi.detail');
    Route::get('/siswa/logbook/detail', [SiswaKegiatanController::class, 'getLogbookDetail'])->name('siswa.logbook.detail');
    Route::post('/siswa/absensi', [SiswaKegiatanController::class, 'storeAbsensi'])->name('siswa.absensi.store');
    Route::post('/siswa/logbook', [SiswaKegiatanController::class, 'storeLogbook'])->name('siswa.logbook.store');

    // Pengajuan Lupa Absensi / Kegiatan
    Route::get('/siswa/pengajuan', [SiswaPengajuanController::class, 'pengajuan'])->name('siswa.pengajuan');
    Route::post('/siswa/pengajuan', [SiswaPengajuanController::class, 'storePengajuan'])->name('siswa.pengajuan.store');

    // Profil Siswa
    Route::get('/siswa/profil', [SiswaProfilController::class, 'showProfile'])->name('siswa.profil');
    Route::put('/siswa/profil', [SiswaProfilController::class, 'updateProfile'])->name('siswa.profil.update');
    Route::put('/siswa/profil/password', [SiswaProfilController::class, 'updatePassword'])->name('siswa.profil.password');

    // Laporan
    Route::get('/siswa/laporan', [SiswaLaporanController::class, 'laporan'])->name('siswa.laporan');
    Route::post('/siswa/laporan/upload', [SiswaLaporanController::class, 'uploadLaporanAkhir'])->name('siswa.laporan.upload');
    Route::get('/siswa/laporan/download-akhir', [SiswaLaporanController::class, 'previewLaporanAkhir'])->name('siswa.laporan.downloadAkhir');
    Route::get('/siswa/laporan/download/jurnal-mingguan', [SiswaLaporanController::class, 'downloadJurnalMingguan'])->name('siswa.rekap.jurnal');
    Route::get('/siswa/laporan/download/rekap-individu', [SiswaLaporanController::class, 'downloadRekapAbsensiIndividu'])->name('siswa.rekap.individu');
    Route::get('/siswa/laporan/download/rekap-kelompok', [SiswaLaporanController::class, 'downloadRekapAbsensiKelompok'])->name('siswa.rekap.kelompok');
    Route::get('/siswa/penilaian/cetak', [SiswaLaporanController::class, 'cetakPenilaian'])->name('siswa.penilaian.cetak');
    Route::get('/siswa/laporan/sertifikat', [SiswaLaporanController::class, 'cetakSertifikat'])->name('siswa.sertifikat.cetak');




    Route::get('/guru/guru', [GuruDashboardController::class, 'dashboard'])->name('guru.guru');
    Route::get('/guru/siswa', [GuruSiswaController::class, 'daftarSiswa'])->name('guru.siswa');
    Route::get('/guru/siswa/{nisn}/logbook', [GuruSiswaController::class, 'logbookSiswa'])->name('guru.logbook');
    Route::post('/guru/logbook/{id}/verifikasi', [GuruSiswaController::class, 'verifikasiLogbook'])->name('guru.logbook.verifikasi');
    Route::get('/guru/siswa/{nisn}/absensi', [GuruSiswaController::class, 'absensiSiswa'])->name('guru.absensi');
    
    Route::get('/guru/verifikasi', [GuruVerifikasiController::class, 'verifikasiLaporan'])->name('guru.verifikasi');
    Route::get('/guru/verifikasi/{id}', [GuruVerifikasiController::class, 'showVerifikasiLaporan'])->name('guru.verifikasi.show');
    Route::post('/guru/verifikasi/{id}', [GuruVerifikasiController::class, 'updateVerifikasiLaporan'])->name('guru.verifikasi.update');

    Route::get('/guru/penilaian', [GuruPenilaianController::class, 'daftarPenilaian'])->name('guru.penilaian');
    Route::get('/guru/penilaian/{nisn}', [GuruPenilaianController::class, 'inputPenilaian'])->name('guru.penilaian.input');
    Route::post('/guru/penilaian/{nisn}', [GuruPenilaianController::class, 'storePenilaian'])->name('guru.penilaian.store');
    Route::get('/guru/penilaian/{nisn}/export', [GuruLaporanController::class, 'exportPenilaian'])->name('guru.penilaian.export');

    // Manajemen Kriteria Penilaian Guru (Dynamic)
    Route::post('/guru/kriteria', [GuruPenilaianController::class, 'storeKriteria'])->name('guru.kriteria.store');
    Route::put('/guru/kriteria/{id}', [GuruPenilaianController::class, 'updateKriteria'])->name('guru.kriteria.update');
    Route::delete('/guru/kriteria/{id}', [GuruPenilaianController::class, 'destroyKriteria'])->name('guru.kriteria.destroy');

    // Claim Siswa (Teacher side)
    Route::post('/guru/siswa/{nisn}/claim', [GuruSiswaController::class, 'claimSiswa'])->name('guru.siswa.claim');

    // Profil Guru
    Route::get('/guru/profil', [GuruProfilController::class, 'profil'])->name('guru.profil');
    Route::post('/guru/profil', [GuruProfilController::class, 'updateProfil'])->name('guru.profil.update');

    Route::get('/guru/siswa/{nisn}/download-jurnal', [GuruLaporanController::class, 'downloadJurnalMingguan'])->name('guru.rekap.jurnal');
    Route::get('/guru/siswa/{nisn}/download-absensi', [GuruLaporanController::class, 'downloadRekapAbsensiIndividu'])->name('guru.rekap.absensi');
    Route::get('/guru/siswa/{nisn}/download-rekap-kelompok', [GuruLaporanController::class, 'downloadRekapAbsensiKelompok'])->name('guru.rekap.kelompok');

    // Tambahan Aksi Riwayat Siswa (Guru)
    Route::get('/guru/siswa/{nisn}/cetak-penilaian-pembimbing', [GuruLaporanController::class, 'cetakPenilaianPembimbing'])->name('guru.siswa.cetakPenilaianPembimbing');
    Route::get('/guru/siswa/{nisn}/cetak-sertifikat', [GuruLaporanController::class, 'cetakSertifikatSiswa'])->name('guru.siswa.cetakSertifikat');
    Route::get('/guru/siswa/{nisn}/cetak-laporan-akhir', [GuruLaporanController::class, 'cetakLaporanAkhir'])->name('guru.siswa.cetakLaporan');




    Route::get('/pembimbing/pembimbing', [AuthController::class, 'pembimbing'])->name('pembimbing.pembimbing');
    Route::get('/pembimbing/siswa', [PembimbingSiswaController::class, 'daftarSiswa'])->name('pembimbing.siswa');
    Route::get('/pembimbing/siswa/{nisn}/absensi', [PembimbingSiswaController::class, 'absensiSiswa'])->name('pembimbing.absensi');
    Route::post('/pembimbing/absensi/{id}/validasi', [PembimbingSiswaController::class, 'validasiAbsensi'])->name('pembimbing.absensi.validasi');
    Route::get('/pembimbing/siswa/{nisn}/logbook', [PembimbingSiswaController::class, 'logbookSiswa'])->name('pembimbing.logbook');
    Route::post('/pembimbing/logbook/{id}/validasi', [PembimbingSiswaController::class, 'validasiLogbook'])->name('pembimbing.logbook.validasi');
    
    // Bulk Validation
    Route::post('/pembimbing/logbook/siswa/{nisn}/validasi-semua', [PembimbingSiswaController::class, 'validasiSemuaLogbook'])->name('pembimbing.logbook.validasi-semua');
    Route::post('/pembimbing/absensi/siswa/{nisn}/validasi-semua', [PembimbingSiswaController::class, 'validasiSemuaAbsensi'])->name('pembimbing.absensi.validasi-semua');
    
    Route::get('/pembimbing/evaluasi', [PembimbingEvaluasiController::class, 'evaluasiSiswa'])->name('pembimbing.evaluasi');
    Route::get('/pembimbing/evaluasi/input/{nisn}', [PembimbingEvaluasiController::class, 'inputEvaluasi'])->name('pembimbing.evaluasi.input');
    Route::post('/pembimbing/evaluasi', [PembimbingEvaluasiController::class, 'storeEvaluasi'])->name('pembimbing.evaluasi.store');
    
    // Pengajuan Siswa
    Route::get('/pembimbing/pengajuan', [PembimbingPengajuanController::class, 'pengajuanSiswa'])->name('pembimbing.pengajuan');
    Route::post('/pembimbing/pengajuan/{id}', [PembimbingPengajuanController::class, 'updatePengajuan'])->name('pembimbing.pengajuan.update');

    // Reporting
    Route::get('/pembimbing/siswa/{nisn}/cetak-jurnal', [PembimbingLaporanController::class, 'cetakJurnalSiswa'])->name('pembimbing.siswa.cetakJurnal');
    Route::get('/pembimbing/siswa/{nisn}/cetak-absensi', [PembimbingLaporanController::class, 'cetakAbsensiSiswa'])->name('pembimbing.siswa.cetakAbsensi');
    
    Route::get('/pembimbing/laporan/{nisn}/cetak', [PembimbingLaporanController::class, 'cetakLaporanSiswa'])->name('pembimbing.laporan.cetak');
    Route::get('/pembimbing/siswa/{nisn}/cetak-penilaian-guru', [PembimbingLaporanController::class, 'cetakPenilaianGuru'])->name('pembimbing.siswa.cetakPenilaianGuru');
    Route::get('/pembimbing/siswa/{nisn}/cetak-laporan-akhir', [PembimbingLaporanController::class, 'cetakLaporanAkhir'])->name('pembimbing.siswa.cetakLaporan');
    Route::get('/pembimbing/siswa/{nisn}/cetak-sertifikat', [PembimbingLaporanController::class, 'cetakSertifikatSiswa'])->name('pembimbing.siswa.cetakSertifikat');

    Route::get('/pembimbing/profil', [PembimbingProfilController::class, 'profil'])->name('pembimbing.profil');
    Route::post('/pembimbing/profil', [PembimbingProfilController::class, 'updateProfil'])->name('pembimbing.profil.update');

    // Manajemen Kriteria Penilaian (Dynamic)
    Route::get('/pembimbing/kriteria', [PembimbingEvaluasiController::class, 'kriteriaPenilaian'])->name('pembimbing.kriteria');
    Route::post('/pembimbing/kriteria', [PembimbingEvaluasiController::class, 'storeKriteria'])->name('pembimbing.kriteria.store');
    Route::put('/pembimbing/kriteria/{id}', [PembimbingEvaluasiController::class, 'updateKriteria'])->name('pembimbing.kriteria.update');
    Route::delete('/pembimbing/kriteria/{id}', [PembimbingEvaluasiController::class, 'destroyKriteria'])->name('pembimbing.kriteria.destroy');


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
    
    // Tambahan Rekap Riwayat (Guru, Pembimbing, Laporan, Sertifikat)
    Route::get('/admin/siswa/{nisn}/cetak-penilaian-guru', [AdminSiswaController::class, 'cetakPenilaianGuru'])->name('admin.rekap.nilaiGuru');
    Route::get('/admin/siswa/{nisn}/cetak-penilaian-pembimbing', [AdminSiswaController::class, 'cetakPenilaianPembimbing'])->name('admin.rekap.nilaiPembimbing');
    Route::get('/admin/siswa/{nisn}/cetak-laporan-akhir', [AdminSiswaController::class, 'cetakLaporanAkhir'])->name('admin.rekap.laporanAkhir');
    Route::get('/admin/siswa/{nisn}/cetak-sertifikat', [AdminSiswaController::class, 'cetakSertifikatSiswa'])->name('admin.rekap.sertifikat');

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
    Route::put('/admin/konfigurasi-laporan', [AdminMasterDataController::class, 'updateKonfigurasiLaporan'])->name('admin.updateKonfigurasiLaporan');
    Route::get('/admin/konfigurasi-laporan/preview-sertifikat', [AdminMasterDataController::class, 'previewSertifikat'])->name('admin.konfigurasiLaporan.previewSertifikat');
    
    // Profil Admin
    Route::get('/admin/profil', [AuthController::class, 'adminProfil'])->name('admin.profil');
    Route::put('/admin/profil/update', [AuthController::class, 'updateAdminProfil'])->name('admin.profil.update');
    Route::put('/admin/profil/password', [AuthController::class, 'updateAdminPassword'])->name('admin.profil.password');
});

// ─────────────────────────────────────────────────────────────────────────
// RUTE PIMPINAN
// ─────────────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('pimpinan')->name('pimpinan.')->group(function () {
    Route::get('/home', [PimpinanDashboardController::class, 'index'])->name('home');
    
    // Kelola Akun (Admin, Siswa, Guru, Pembimbing)
    Route::get('/admin', [PimpinanAdminController::class, 'kelolaAdmin'])->name('admin');
    Route::post('/admin', [PimpinanAdminController::class, 'storeAdmin'])->name('storeAdmin');
    Route::put('/admin/{id}', [PimpinanAdminController::class, 'updateAdmin'])->name('updateAdmin');
    Route::delete('/admin/{id}', [PimpinanAdminController::class, 'destroyAdmin'])->name('destroyAdmin');

    Route::get('/siswa', [PimpinanMonitoringController::class, 'siswa'])->name('siswa');
    Route::get('/guru', [PimpinanMonitoringController::class, 'guru'])->name('guru');
    Route::get('/pembimbing', [PimpinanMonitoringController::class, 'pembimbing'])->name('pembimbing');
    
    Route::get('/rekap', [PimpinanRekapController::class, 'rekap'])->name('rekap');
    Route::get('/rekap/stats', [PimpinanRekapController::class, 'rekapStats'])->name('rekap.stats');
    
    // Profil Pimpinan
    Route::get('/profil', [PimpinanProfilController::class, 'profil'])->name('profil');
    Route::put('/profil/update', [PimpinanProfilController::class, 'updateProfil'])->name('profil.update');
    Route::put('/profil/password', [PimpinanProfilController::class, 'updatePassword'])->name('profil.password');
});