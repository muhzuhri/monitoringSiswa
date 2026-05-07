<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Monitoring Siswa Magang</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- CSS Khusus Halaman Register -->
    <link href="<?php echo e(asset('assets/css/auth/register.css')); ?>" rel="stylesheet">
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
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="<?php echo e(route('register')); ?>" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>

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
                                    <div class="input-group">
                                        <input type="password" name="password" id="password" class="form-control" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
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
                                    <?php $__currentLoopData = $tahunAjarans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($ta->id_tahun_ajaran); ?>"><?php echo e($ta->tahun_ajaran); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <!-- Form khusus Siswa -->
                            <div id="form-siswa" class="role-form d-none">
                                <h6 class="fw-semibold mb-3">Data Siswa Magang</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                         <label class="form-label">NISN</label>
                                         <input type="text" name="nisn" class="form-control" required>
                                     </div>
                                    <div class="col-md-6 mb-3">
                                         <label class="form-label">Jenis Kelamin</label>
                                         <select name="jenis_kelamin" class="form-select" required>
                                             <option value="">-- Pilih --</option>
                                             <option value="L">Laki-laki</option>
                                             <option value="P">Perempuan</option>
                                         </select>
                                     </div>
                                </div>

                                 <div class="row">
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">Kelas</label>
                                         <input type="text" name="kelas" class="form-control" required>
                                     </div>
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">Jurusan</label>
                                         <input type="text" name="jurusan" class="form-control" required>
                                     </div>
                                 </div>

                                 <div class="row mb-3">
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">NPSN Sekolah</label>
                                         <input type="text" name="npsn_siswa" id="npsn_siswa" class="form-control"
                                             placeholder="Ketik NPSN Sekolah..." required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Asal Sekolah</label>
                                                <input type="text" name="sekolah_siswa" id="sekolah_siswa" class="form-control" 
                                                placeholder="Terisi otomatis jika NPSN ditemukan" readonly required>
                                            </div>
                                            <small id="npsn_siswa_msg" class="text-muted"></small>
                                 </div>

                                 <div class="mb-3">
                                     <label class="form-label">Lokasi Magang</label>
                                     <select name="perusahaan" class="form-select" required>
                                         <option value="">-- Pilih Lokasi Magang --</option>
                                         <?php $__currentLoopData = $lokasis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                             <option value="<?php echo e($lok->nama_lokasi); ?>"><?php echo e($lok->nama_lokasi); ?></option>
                                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                     </select>
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
                                     <div class="col-md-12 mb-3">
                                         <label class="form-label">Upload Surat Balasan <span
                                                 class="text-muted">(PDF/JPG/PNG)</span></label>
                                         <input type="file" name="surat_balasan" class="form-control"
                                             accept=".pdf,.jpg,.jpeg,.png" required>
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
                                         <input type="text" name="id_guru" class="form-control" required>
                                     </div>
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">Jabatan</label>
                                         <input type="text" name="jabatan" class="form-control" required>
                                     </div>
                                 </div>

                                 <div class="row">
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">NPSN Sekolah</label>
                                         <input type="text" name="npsn_guru" id="npsn_guru" class="form-control" 
                                             placeholder="Ketik NPSN..." required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Asal Sekolah</label>
                                        <input type="text" name="sekolah_guru" id="sekolah_guru" class="form-control" 
                                        placeholder="Terisi otomatis" readonly required>
                                    </div>
                                    <small id="npsn_guru_msg" class="text-muted"></small>
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
                                 <a href="<?php echo e(route('login')); ?>" class="btn btn-link px-0">Sudah punya akun? Login</a>
                                 <button type="submit" class="btn btn-primary px-4" id="btn-daftar">Daftar</button>
                             </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo e(asset('assets/js/register.js')); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButtons = document.querySelectorAll('.toggle-password');
            
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            });

            // NPSN Lookup Logic
            function initNpsnLookup(inputId, outputId, msgId) {
                const npsnInput = document.getElementById(inputId);
                const schoolInput = document.getElementById(outputId);
                const msgEl = document.getElementById(msgId);
                const btnDaftar = document.getElementById('btn-daftar');

                if (!npsnInput) return;

                npsnInput.addEventListener('input', function() {
                    const npsn = this.value;
                    if (npsn.length >= 8) {
                        msgEl.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mencari sekolah...';
                        msgEl.className = 'text-primary';
                        
                        fetch(`/api/schools/${npsn}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    schoolInput.value = data.data.nama_sekolah;
                                    msgEl.innerHTML = '<i class="fas fa-check-circle me-2"></i> Sekolah ditemukan: ' + data.data.nama_sekolah;
                                    msgEl.className = 'text-success';
                                    btnDaftar.disabled = false;
                                } else {
                                    schoolInput.value = '';
                                    msgEl.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Sekolah belum terdaftar. Hubungi Admin.';
                                    msgEl.className = 'text-danger';
                                    btnDaftar.disabled = true;
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching school:', error);
                                msgEl.innerHTML = 'Terjadi kesalahan sistem.';
                                msgEl.className = 'text-danger';
                            });
                    } else {
                        schoolInput.value = '';
                        msgEl.innerHTML = 'NPSN minimal 8 digit.';
                        msgEl.className = 'text-muted';
                        btnDaftar.disabled = false; // Reset to let other roles register if needed
                    }
                });
            }

            initNpsnLookup('npsn_siswa', 'sekolah_siswa', 'npsn_siswa_msg');
            initNpsnLookup('npsn_guru', 'sekolah_guru', 'npsn_guru_msg');
        });
    </script>
</body>

</html><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/auth/register.blade.php ENDPATH**/ ?>