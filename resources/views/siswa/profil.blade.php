@extends('layouts.nav.siswa')

@section('title', 'Profil Saya - SIM Magang Fasilkom Unsri')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/siswa/profil-siswa.css') }}">
@endpush

@section('body')
    <div class="profil-wrapper">
        <div class="profil-layout">

            {{-- Kartu Sidebar Profil --}}
            <div class="profil-sidebar">
                <div class="profile-card">
                    <div class="profile-header-gradient"></div>
                    <div class="profile-img-container">
                        <div class="profile-img-wrapper">
                            <img src="{{ $user->foto_profil ? asset('storage/' . $user->foto_profil) : asset('assets/img/default-avatar.png') }}"
                                alt="Avatar" id="profile-preview">
                            <label for="foto_profil" class="btn-edit-photo" title="Ubah Foto">
                                <i class="fas fa-camera"></i>
                            </label>
                        </div>
                    </div>
                    <div class="profile-body">
                        <h4 class="profile-name">{{ $user->nama }}</h4>
                        <p class="profile-nisn">{{ $user->nisn }}</p>
                        <div class="role-badge">
                            <i class="fas fa-user-graduate"></i> SISWA MAGANG
                        </div>

                        <hr class="profile-divider">

                        <div class="profile-info-list">
                            <div class="profile-info-item">
                                <div class="icon-box icon-blue">
                                    <i class="fas fa-school"></i>
                                </div>
                                <div>
                                    <small class="info-label">Sekolah</small>
                                    <span class="info-value">{{ $user->sekolah }}</span>
                                </div>
                            </div>
                            <div class="profile-info-item">
                                <div class="icon-box icon-green">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <small class="info-label">Perusahaan</small>
                                    <span class="info-value">{{ $user->perusahaan }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Konten Utama --}}
            <div class="profil-main">
                <div class="profile-card">
                    <div class="profile-card-header">
                        <nav class="profile-nav" id="profileTab" role="tablist">
                            <button class="profile-nav-link active" id="data-diri-tab" data-bs-toggle="tab"
                                data-bs-target="#data-diri" type="button" role="tab" aria-selected="true">
                                <i class="fas fa-id-card"></i>
                                <span>DATA DIRI</span>
                            </button>
                            <button class="profile-nav-link" id="pembimbing-tab" data-bs-toggle="tab"
                                data-bs-target="#pembimbing" type="button" role="tab" aria-selected="false">
                                <i class="fas fa-user-tie"></i>
                                <span>PEMBIMBING</span>
                            </button>
                            <button class="profile-nav-link" id="security-tab" data-bs-toggle="tab"
                                data-bs-target="#security" type="button" role="tab" aria-selected="false">
                                <i class="fas fa-shield-alt"></i>
                                <span>KEAMANAN</span>
                            </button>
                        </nav>
                    </div>

                    <div class="profile-card-body">
                        @if(session('success'))
                            <div class="ui-alert ui-alert-success">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="ui-alert ui-alert-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                <ul class="alert-list">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="tab-content" id="profileTabContent">
                            {{-- Tab Data Diri --}}
                            <div class="tab-pane fade show active" id="data-diri" role="tabpanel"
                                aria-labelledby="data-diri-tab">
                                <form action="{{ route('siswa.profil.update') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="file" name="foto_profil" id="foto_profil" style="display:none"
                                        accept="image/*" onchange="previewImage(event)">

                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label class="form-label">Nama Lengkap</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-user"></i></span>
                                                <input type="text" name="nama" class="form-field"
                                                    value="{{ old('nama', $user->nama) }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Email</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-envelope"></i></span>
                                                <input type="email" name="email" class="form-field"
                                                    value="{{ old('email', $user->email) }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">NISN</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon input-icon-muted"><i
                                                        class="fas fa-address-card"></i></span>
                                                <input type="text" class="form-field form-field-readonly"
                                                    value="{{ $user->nisn }}" readonly>
                                            </div>
                                            <small class="form-help">NISN tidak dapat diubah.</small>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Jenis Kelamin</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-venus-mars"></i></span>
                                                <select name="jenis_kelamin" class="form-field" required>
                                                    <option value="L" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                                    <option value="P" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Kelas</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-chalkboard"></i></span>
                                                <input type="text" name="kelas" class="form-field"
                                                    value="{{ old('kelas', $user->kelas) }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Jurusan</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-graduation-cap"></i></span>
                                                <input type="text" name="jurusan" class="form-field"
                                                    value="{{ old('jurusan', $user->jurusan) }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-full">
                                            <label class="form-label">Sekolah / Instansi</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-school"></i></span>
                                                <input type="text" name="sekolah" class="form-field"
                                                    value="{{ old('sekolah', $user->sekolah) }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-full">
                                            <label class="form-label">Perusahaan Tempat Magang</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-building"></i></span>
                                                <input type="text" name="perusahaan" class="form-field"
                                                    value="{{ old('perusahaan', $user->perusahaan) }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">No. WhatsApp / HP</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fab fa-whatsapp"></i></span>
                                                <input type="text" name="no_hp" class="form-field"
                                                    value="{{ old('no_hp', $user->no_hp) }}" placeholder="Contoh: 08123456789">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">NPSN Sekolah</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon input-icon-muted"><i class="fas fa-hashtag"></i></span>
                                                <input type="text" class="form-field form-field-readonly"
                                                    value="{{ $user->npsn }}" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Tipe Magang</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon input-icon-muted"><i class="fas fa-briefcase"></i></span>
                                                <input type="text" class="form-field form-field-readonly"
                                                    value="{{ ucfirst($user->tipe_magang) }}" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Tahun Ajaran</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon input-icon-muted"><i class="fas fa-calendar-alt"></i></span>
                                                <input type="text" class="form-field form-field-readonly"
                                                    value="{{ $user->tahunAjaran->tahun_ajaran ?? '-' }}" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Tanggal Mulai</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon input-icon-muted"><i class="fas fa-clock"></i></span>
                                                <input type="text" class="form-field form-field-readonly"
                                                    value="{{ $user->tahunAjaran ? \Carbon\Carbon::parse($user->tahunAjaran->tgl_mulai)->format('d M Y') : '-' }}" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Tanggal Selesai</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon input-icon-muted"><i class="fas fa-calendar-check"></i></span>
                                                <input type="text" class="form-field form-field-readonly"
                                                    value="{{ $user->tahunAjaran ? \Carbon\Carbon::parse($user->tahunAjaran->tgl_selesai)->format('d M Y') : '-' }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn-save">
                                            <i class="fas fa-save"></i> SIMPAN PERUBAHAN
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            {{-- Tab Pembimbing --}}
                            <div class="tab-pane fade" id="pembimbing" role="tabpanel" aria-labelledby="pembimbing-tab">
                                <div class="supervisor-grid">
                                    {{-- Guru Pembimbing --}}
                                    <div class="supervisor-card">
                                        <div class="supervisor-badge">GURU PEMBIMBING</div>
                                        @if($user->guru)
                                            <div class="supervisor-avatar">
                                                <img src="{{ asset('assets/img/default-avatar.png') }}" alt="Guru">
                                            </div>
                                            <h5 class="supervisor-name">{{ $user->guru->nama }}</h5>
                                            <p class="supervisor-id">ID: {{ $user->guru->id_guru }}</p>
                                            
                                            <div class="supervisor-contacts">
                                                <div class="s-contact-item">
                                                    <i class="fab fa-whatsapp"></i>
                                                    <span>{{ $user->guru->no_hp ?? '-' }}</span>
                                                </div>
                                                <div class="s-contact-item">
                                                    <i class="fas fa-envelope"></i>
                                                    <span>{{ $user->guru->email }}</span>
                                                </div>
                                                <div class="s-contact-item">
                                                    <i class="fas fa-briefcase"></i>
                                                    <span>{{ $user->guru->jabatan }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="no-supervisor">
                                                <i class="fas fa-user-slash"></i>
                                                <p>Belum ada Guru Pembimbing yang ditugaskan.</p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Pembimbing Lapangan --}}
                                    <div class="supervisor-card field-supervisor">
                                        <div class="supervisor-badge">PEMBIMBING LAPANGAN</div>
                                        @if($user->pembimbing)
                                            <div class="supervisor-avatar">
                                                <img src="{{ asset('assets/img/default-avatar.png') }}" alt="Dosen">
                                            </div>
                                            <h5 class="supervisor-name">{{ $user->pembimbing->nama }}</h5>
                                            <p class="supervisor-id">ID: {{ $user->pembimbing->id_pembimbing }}</p>
                                            
                                            <div class="supervisor-contacts">
                                                <div class="s-contact-item">
                                                    <i class="fab fa-whatsapp"></i>
                                                    <span>{{ $user->pembimbing->no_telp ?? '-' }}</span>
                                                </div>
                                                <div class="s-contact-item">
                                                    <i class="fas fa-envelope"></i>
                                                    <span>{{ $user->pembimbing->email }}</span>
                                                </div>
                                                <div class="s-contact-item">
                                                    <i class="fas fa-building"></i>
                                                    <span>{{ $user->pembimbing->instansi }}</span>
                                                </div>
                                                <div class="s-contact-item">
                                                    <i class="fas fa-briefcase"></i>
                                                    <span>{{ $user->pembimbing->jabatan }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="no-supervisor">
                                                <i class="fas fa-user-slash"></i>
                                                <p>Belum ada Pembimbing Lapangan yang ditugaskan.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Tab Keamanan --}}
                            <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                                <form action="{{ route('siswa.profil.password') }}" method="POST" class="security-section">
                                    @csrf
                                    @method('PUT')

                                    <h6 class="section-title">UBAH PASSWORD</h6>

                                    <div class="form-group form-group-full">
                                        <label class="form-label">Password Saat Ini</label>
                                        <div class="input-wrapper">
                                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                                            <input type="password" name="current_password" class="form-field"
                                                placeholder="Masukkan password sekarang" required>
                                        </div>
                                    </div>

                                    <div class="form-grid" style="margin-top: 1rem;">
                                        <div class="form-group">
                                            <label class="form-label">Password Baru</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-key"></i></span>
                                                <input type="password" name="new_password" class="form-field"
                                                    placeholder="Minimal 6 karakter" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Konfirmasi Password Baru</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-check-double"></i></span>
                                                <input type="password" name="new_password_confirmation" class="form-field"
                                                    placeholder="Ulangi password baru" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn-save btn-warning-save">
                                            <i class="fas fa-shield-alt"></i> PERBARUI PASSWORD
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('profile-preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection