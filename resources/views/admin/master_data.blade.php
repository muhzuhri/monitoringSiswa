@extends('layouts.nav.admin')

@section('title', 'Data Master - Monitoring Siswa Magang')
@section('body-class', 'dashboard-page admin-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/master-data.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-modals.css') }}">
@endpush

@section('body')
    <div class="management-container" data-active-tab="{{ session('active_tab') }}">
        <div class="admin-content-wrapper">

            <div class="management-header mb-4">
                <div class="header-title">
                    <h5 class="fw-bold"><i class="fas fa-database me-2 text-primary"></i> Data Master</h5>
                    <p class="text-muted">Kelola basis data sekolah dan periode tahun ajaran sistem.</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: #ecfdf5; color: #065f46;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <ul class="nav nav-pills mb-4" id="masterDataTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="sekolah-tab" data-bs-toggle="pill" data-bs-target="#pane-sekolah" type="button" role="tab">
                        <i class="fas fa-school"></i> Master Sekolah
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="periode-tab" data-bs-toggle="pill" data-bs-target="#pane-periode" type="button" role="tab">
                        <i class="fas fa-calendar-alt"></i> Tahun Ajaran
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="informasi-tab" data-bs-toggle="pill" data-bs-target="#pane-informasi" type="button" role="tab">
                        <i class="fas fa-info-circle"></i> Informasi Dashboard
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="masterDataTabsContent">
                {{-- TAB SEKOLAH --}}
                <div class="tab-pane fade show active" id="pane-sekolah" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Daftar Sekolah Terdaftar</h6>
                        <button class="btn btn-primary px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSekolah">
                            <i class="fas fa-plus me-2"></i> Tambah Sekolah
                        </button>
                    </div>

                    <div class="data-table-wrapper">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">NPSN</th>
                                    <th>Nama Sekolah</th>
                                    <th>Jenjang</th>
                                    <th>Status</th>
                                    <th>Alamat</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sekolahs as $s)
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary">{{ $s->npsn }}</td>
                                        <td>{{ $s->nama_sekolah }}</td>
                                        <td><span class="badge bg-info-subtle text-info px-3">{{ $s->jenjang }}</span></td>
                                        <td>
                                            @if($s->status == 'Negeri')
                                                <span class="badge bg-success-subtle text-success px-3">Negeri</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning px-3">Swasta</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">{{ Str::limit($s->alamat, 50) }}</td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-light text-primary border-0 rounded-circle btn-edit-sekolah" 
                                                data-bs-toggle="modal" data-bs-target="#modalEditSekolah"
                                                data-id="{{ $s->id_sekolah }}"
                                                data-npsn="{{ $s->npsn }}"
                                                data-nama="{{ $s->nama_sekolah }}"
                                                data-jenjang="{{ $s->jenjang }}"
                                                data-status="{{ $s->status }}"
                                                data-alamat="{{ $s->alamat }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.destroySekolah', $s->id_sekolah) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light text-danger border-0 rounded-circle" onclick="return confirm('Hapus sekolah ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">Belum ada data sekolah.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB PERIODE --}}
                <div class="tab-pane fade" id="pane-periode" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Daftar Tahun Ajaran</h6>
                        <button class="btn btn-primary px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPeriode">
                            <i class="fas fa-plus me-2"></i> Tambah Periode
                        </button>
                    </div>

                    <div class="data-table-wrapper">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Tahun Ajaran</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tahunAjarans as $ta)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ $ta->tahun_ajaran }}</td>
                                        <td>{{ \Carbon\Carbon::parse($ta->tgl_mulai)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($ta->tgl_selesai)->format('d M Y') }}</td>
                                        <td>
                                            @if($ta->status == 'aktif')
                                                <span class="badge bg-success px-3">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary px-3">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-light text-primary border-0 rounded-circle btn-edit-periode"
                                                data-bs-toggle="modal" data-bs-target="#modalEditPeriode"
                                                data-id="{{ $ta->id_tahun_ajaran }}"
                                                data-tahun="{{ $ta->tahun_ajaran }}"
                                                data-mulai="{{ $ta->tgl_mulai }}"
                                                data-selesai="{{ $ta->tgl_selesai }}"
                                                data-status="{{ $ta->status }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.destroyPeriode', $ta->id_tahun_ajaran) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light text-danger border-0 rounded-circle" onclick="return confirm('Hapus periode ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data periode.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

                {{-- TAB INFORMASI DASHBOARD --}}
                <div class="tab-pane fade" id="pane-informasi" role="tabpanel">
                    <div class="row g-4">
                        <!-- Form Informasi Dashboard -->
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                                    <h6 class="fw-bold mb-0 text-primary"><i class="fas fa-edit me-2"></i> Edit Informasi Dashboard</h6>
                                </div>
                                <div class="card-body p-4">
                                    <form action="{{ route('admin.updateInformasi') }}" method="POST">
                                        @csrf @method('PUT')
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-muted small">Nama Fakultas / Lembaga</label>
                                            <input type="text" name="nama_fakultas" class="form-control rounded-3" value="{{ $informasi->nama_fakultas }}">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-muted small">Deskripsi Banner</label>
                                            <textarea name="deskripsi_banner" class="form-control rounded-3" rows="2">{{ $informasi->deskripsi_banner }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-muted small">Visi</label>
                                            <textarea name="visi" class="form-control rounded-3" rows="2">{{ $informasi->visi }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-muted small d-flex justify-content-between">
                                                <span>Misi</span>
                                                <button type="button" class="btn btn-sm btn-light py-0" onclick="addMisiField()"><i class="fas fa-plus"></i></button>
                                            </label>
                                            <div id="misi-container">
                                                @forelse($informasi->misi_array as $index => $misi)
                                                    <div class="input-group mb-2 misi-item">
                                                        <span class="input-group-text bg-light border-end-0">{{ $index + 1 }}.</span>
                                                        <textarea name="misi[]" class="form-control border-start-0" rows="1">{{ $misi }}</textarea>
                                                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.misi-item').remove()"><i class="fas fa-times"></i></button>
                                                    </div>
                                                @empty
                                                    <div class="input-group mb-2 misi-item">
                                                        <span class="input-group-text bg-light border-end-0">1.</span>
                                                        <textarea name="misi[]" class="form-control border-start-0" rows="1"></textarea>
                                                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.misi-item').remove()"><i class="fas fa-times"></i></button>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-muted small">Sejarah Singkat</label>
                                            <textarea name="sejarah" class="form-control rounded-3" rows="3">{{ $informasi->sejarah }}</textarea>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold text-muted small"><i class="fas fa-clock me-1"></i> Jam Operasional</label>
                                                <input type="text" name="jam_operasional" class="form-control rounded-3" value="{{ $informasi->jam_operasional }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold text-muted small"><i class="fas fa-info-circle me-1"></i> Ket. Jam</label>
                                                <input type="text" name="deskripsi_jam_operasional" class="form-control rounded-3" value="{{ $informasi->deskripsi_jam_operasional }}">
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold text-muted small"><i class="fas fa-map-marker-alt me-1"></i> Alamat Lokasi</label>
                                                <input type="text" name="alamat_lokasi" class="form-control rounded-3" value="{{ $informasi->alamat_lokasi }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold text-muted small"><i class="fas fa-link me-1"></i> Link Maps</label>
                                                <input type="text" name="link_maps" class="form-control rounded-3" value="{{ $informasi->link_maps }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold text-muted small"><i class="fas fa-envelope me-1"></i> Email</label>
                                                <input type="email" name="email_kontak" class="form-control rounded-3" value="{{ $informasi->email_kontak }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold text-muted small"><i class="fas fa-phone-alt me-1"></i> Telepon</label>
                                                <input type="text" name="telp_kontak" class="form-control rounded-3" value="{{ $informasi->telp_kontak }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold text-muted small"><i class="fas fa-globe me-1"></i> Website</label>
                                                <input type="text" name="website_kontak" class="form-control rounded-3" value="{{ $informasi->website_kontak }}">
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="fas fa-save me-2"></i> Simpan Perubahan Dasar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Kelola Program Studi -->
                        <div class="col-lg-5">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-primary"><i class="fas fa-graduation-cap me-2"></i> Program Studi</h6>
                                    <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambahProdi">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button>
                                </div>
                                <div class="card-body p-4">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <tbody>
                                                @forelse($programStudis as $prodi)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <span class="rounded-circle d-inline-block me-3 shadow-sm flex-shrink-0" style="width: 12px; height: 12px; background-color: {{ $prodi->warna_dot }}; border: 2px solid white; outline: 1px solid #e2e8f0;"></span>
                                                                <div>
                                                                    <div class="fw-bold text-dark">{{ $prodi->nama }}</div>
                                                                    <div class="small text-muted">{{ $prodi->jenjang }} <span class="mx-1">•</span> Urutan: {{ $prodi->urutan }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($prodi->aktif)
                                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Aktif</span>
                                                            @else
                                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">Nonaktif</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end" style="white-space: nowrap;">
                                                            <button class="btn btn-sm btn-light text-primary rounded-circle me-1 border-0 btn-edit-prodi" 
                                                                    data-id="{{ $prodi->id }}"
                                                                    data-nama="{{ $prodi->nama }}"
                                                                    data-jenjang="{{ $prodi->jenjang }}"
                                                                    data-warna="{{ $prodi->warna_dot }}"
                                                                    data-urutan="{{ $prodi->urutan }}"
                                                                    data-aktif="{{ $prodi->aktif ? '1' : '0' }}"
                                                                    data-bs-toggle="modal" data-bs-target="#modalEditProdi">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form action="{{ route('admin.destroyProdi', $prodi->id) }}" method="POST" class="d-inline">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-light text-danger rounded-circle border-0" onclick="return confirm('Hapus program studi ini?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center py-4 text-muted">Belum ada data program studi.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALS --}}
    <!-- Modal Tambah Sekolah -->
    <div class="modal fade" id="modalTambahSekolah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Sekolah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.storeSekolah') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">NPSN</label>
                            <input type="text" name="npsn" class="form-control rounded-3" placeholder="Masukkan 8 digit NPSN" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Sekolah</label>
                            <input type="text" name="nama_sekolah" class="form-control rounded-3" placeholder="Contoh: SMK Negeri 1 Palembang" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenjang</label>
                                <select name="jenjang" class="form-select rounded-3" required>
                                    <option value="SMK">SMK</option>
                                    <option value="SMA">SMA</option>
                                    <option value="MA">MA</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select rounded-3" required>
                                    <option value="Negeri">Negeri</option>
                                    <option value="Swasta">Swasta</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat" class="form-control rounded-3" rows="3" placeholder="Alamat lengkap sekolah"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Sekolah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Sekolah -->
    <div class="modal fade" id="modalEditSekolah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Data Sekolah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditSekolah" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">NPSN</label>
                            <input type="text" name="npsn" id="edit_sekolah_npsn" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Sekolah</label>
                            <input type="text" name="nama_sekolah" id="edit_sekolah_nama" class="form-control rounded-3" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenjang</label>
                                <select name="jenjang" id="edit_sekolah_jenjang" class="form-select rounded-3" required>
                                    <option value="SMK">SMK</option>
                                    <option value="SMA">SMA</option>
                                    <option value="MA">MA</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" id="edit_sekolah_status" class="form-select rounded-3" required>
                                    <option value="Negeri">Negeri</option>
                                    <option value="Swasta">Swasta</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat" id="edit_sekolah_alamat" class="form-control rounded-3" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Update Sekolah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Periode -->
    <div class="modal fade" id="modalTambahPeriode" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.storePeriode') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" class="form-control rounded-3" placeholder="Contoh: 2023/2024" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" name="tgl_mulai" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                <input type="date" name="tgl_selesai" class="form-control rounded-3" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="aktif">Aktif</option>
                                <option value="tidak aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Periode</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Periode -->
    <div class="modal fade" id="modalEditPeriode" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditPeriode" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" id="edit_ta_tahun" class="form-control rounded-3" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" name="tgl_mulai" id="edit_ta_mulai" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                <input type="date" name="tgl_selesai" id="edit_ta_selesai" class="form-control rounded-3" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="edit_ta_status" class="form-select rounded-3" required>
                                <option value="aktif">Aktif</option>
                                <option value="tidak aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Update Periode</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Prodi -->
    <div class="modal fade" id="modalTambahProdi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Program Studi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.storeProdi') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Program Studi</label>
                            <input type="text" name="nama" class="form-control rounded-3" placeholder="Contoh: Sistem Informasi" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenjang</label>
                                <input type="text" name="jenjang" class="form-control rounded-3" placeholder="Contoh: S1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Warna Label</label>
                                <input type="color" name="warna_dot" class="form-control rounded-3 form-control-color w-100" value="#4e73df" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Urutan</label>
                            <input type="number" name="urutan" class="form-control rounded-3" value="0">
                            <small class="text-muted">Semakin kecil, semakin atas.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Prodi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Prodi -->
    <div class="modal fade" id="modalEditProdi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Program Studi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditProdi" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Program Studi</label>
                            <input type="text" name="nama" id="edit_prodi_nama" class="form-control rounded-3" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenjang</label>
                                <input type="text" name="jenjang" id="edit_prodi_jenjang" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Warna Label</label>
                                <input type="color" name="warna_dot" id="edit_prodi_warna" class="form-control rounded-3 form-control-color w-100" required>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Urutan</label>
                                <input type="number" name="urutan" id="edit_prodi_urutan" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="aktif" id="edit_prodi_aktif" class="form-select rounded-3">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Update Prodi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/js/admin/masterData.js') }}"></script>
    @endpush
@endsection
