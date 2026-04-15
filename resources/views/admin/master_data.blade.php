@extends('layouts.nav.admin')

@section('title', 'Data Master - Monitoring Siswa Magang')
@section('body-class', 'dashboard-page admin-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-siswa.css') }}">
    <style>
        .nav-pills .nav-link {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            color: var(--p-text-secondary);
            background: #fff;
            border: 1px solid #e2e8f0;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        .nav-pills .nav-link.active {
            background: var(--p-primary);
            color: #fff;
            border-color: var(--p-primary);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }
        .nav-pills .nav-link i {
            margin-right: 8px;
        }
        .card-master {
            background: #fff;
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card-master:hover {
            transform: translateY(-5px);
        }
        .data-table-wrapper {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('body')
    <div class="management-container">
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Edit Sekolah Modal
                document.querySelectorAll('.btn-edit-sekolah').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        document.getElementById('formEditSekolah').action = `/admin/sekolah/${id}`;
                        document.getElementById('edit_sekolah_npsn').value = this.getAttribute('data-npsn');
                        document.getElementById('edit_sekolah_nama').value = this.getAttribute('data-nama');
                        document.getElementById('edit_sekolah_jenjang').value = this.getAttribute('data-jenjang');
                        document.getElementById('edit_sekolah_status').value = this.getAttribute('data-status');
                        document.getElementById('edit_sekolah_alamat').value = this.getAttribute('data-alamat');
                    });
                });

                // Edit Periode Modal
                document.querySelectorAll('.btn-edit-periode').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        document.getElementById('formEditPeriode').action = `/admin/periode/${id}`;
                        document.getElementById('edit_ta_tahun').value = this.getAttribute('data-tahun');
                        document.getElementById('edit_ta_mulai').value = this.getAttribute('data-mulai');
                        document.getElementById('edit_ta_selesai').value = this.getAttribute('data-selesai');
                        document.getElementById('edit_ta_status').value = this.getAttribute('data-status');
                    });
                });
            });
        </script>
    @endpush
@endsection
