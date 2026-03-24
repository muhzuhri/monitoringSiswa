<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Monitoring Siswa Magang</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS Khusus Halaman Register -->
    <link href="{{ asset('assets/css/auth/register.css') }}" rel="stylesheet">
</head>

<body class="bg-light register-page">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card shadow-sm">

                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Registrasi Akun</h4>
                        <small>Monitoring Siswa Magang</small>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="post" action="{{ route('register') }}" enctype="multipart/form-data">
                            @csrf

                            <!-- Data Umum -->
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 081234567890"
                                    required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Daftar Sebagai</label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="">-- Pilih Peran --</option>
                                    <option value="siswa">Siswa Magang</option>
                                    <option value="guru">Guru Pembimbing</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tahun Ajaran</label>
                                <select name="id_tahun_ajaran" class="form-select" required>
                                    <option value="">-- Pilih Tahun Ajaran --</option>
                                    @foreach($tahunAjarans as $ta)
                                        <option value="{{ $ta->id_tahun_ajaran }}">{{ $ta->tahun_ajaran }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Form khusus Siswa -->
                            <div id="form-siswa" class="role-form d-none">
                                <h6 class="fw-semibold mb-3">Data Siswa Magang</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NISN</label>
                                        <input type="text" name="nisn" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" class="form-select">
                                            <option value="">-- Pilih --</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Kelas</label>
                                        <input type="text" name="kelas" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jurusan</label>
                                        <input type="text" name="jurusan" class="form-control">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Asal Sekolah</label>
                                    <input type="text" name="sekolah_siswa" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Lokasi Magang</label>
                                    <input type="text" name="perusahaan" class="form-control">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Mulai Magang</label>
                                        <input type="date" name="tgl_mulai_magang" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Selesai Magang</label>
                                        <input type="date" name="tgl_selesai_magang" class="form-control" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NPSN Sekolah</label>
                                        <input type="text" name="npsn_siswa" class="form-control"
                                            placeholder="Masukkan NPSN Sekolah">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Upload Surat Balasan <span
                                                class="text-muted">(PDF/JPG/PNG)</span></label>
                                        <input type="file" name="surat_balasan" class="form-control"
                                            accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tipe Magang</label>
                                        <select name="tipe_magang" id="tipe_magang" class="form-select">
                                            <option value="individu">Individu</option>
                                            <option value="kelompok">Kelompok</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3 d-none" id="group-leader-section">
                                        <label class="form-label">NISN Ketua Kelompok</label>
                                        <input type="text" name="nisn_ketua" class="form-control" placeholder="Masukkan NISN Ketua">
                                        <small class="text-muted">Jika Anda ketua, isi dengan NISN Anda sendiri.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Form khusus Guru -->
                            <div id="form-guru" class="role-form d-none">
                                <h6 class="fw-semibold mb-3">Data Guru Pembimbing</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIP</label>
                                        <input type="text" name="id_guru" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jabatan</label>
                                        <input type="text" name="jabatan" class="form-control">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Asal Sekolah</label>
                                        <input type="text" name="sekolah_guru" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NPSN Sekolah</label>
                                        <input type="text" name="npsn_guru" class="form-control" placeholder="">
                                    </div>
                                </div>
                            </div>

                            <!-- Form khusus Dosen -->
                            <div id="form-dosen" class="role-form d-none">
                                <h6 class="fw-semibold mb-3">Data Dosen Pembimbing</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jabatan</label>
                                        <input type="text" name="jabatan" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Instansi</label>
                                        <select name="instansi" class="form-select">
                                            <option value="">-- Pilih Instansi --</option>
                                            <option value="Fasilkom Unsri Indralaya">Fasilkom Unsri Indralaya</option>
                                            <option value="Fasilkom Unsri Bukit">Fasilkom Unsri Bukit</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">No. Telpon</label>
                                    <input type="text" name="no_telp" class="form-control">
                                </div>
                            </div>

                            <!-- Form khusus Admin -->
                            <div id="form-admin" class="role-form d-none">
                                <h6 class="fw-semibold mb-3">Data Admin</h6>

                                <div class="mb-3">
                                    <label class="form-label">Instansi</label>
                                    <input type="text" name="instansi" class="form-control">
                                </div>
                            </div>


                            <div class="d-flex justify-content-between mt-3">
                                <a href="{{ route('login') }}">Sudah punya akun? Login</a>
                                <button type="submit" class="btn btn-primary">Daftar</button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/register.js') }}"></script>
</body>

</html>