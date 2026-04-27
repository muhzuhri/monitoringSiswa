@extends('layouts.nav.admin')

@section('title', 'Kelola Lokasi Absensi - Monitoring Siswa')
@section('body-class', 'dashboard-page admin-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-lokasi.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-modals.css') }}">
@endpush

@section('body')
    <div class="management-container">
        <div class="admin-content-wrapper">
            <div class="management-header">
                <div class="header-title">
                    <h5>Manajemen Lokasi Absensi</h5>
                    <p>Atur titik koordinat dan radius untuk validasi kehadiran siswa.</p>
                </div>
                <div class="header-actions">
                    <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTambahLokasi">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Lokasi</span>
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div class="custom-alert alert-success-custom">
                    <span><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4 mt-2">
                @forelse($lokasis as $l)
                    <div class="col-xl-4 col-md-6">
                        <div class="location-card">
                            <div class="location-status">
                                @if($l->is_active)
                                    <span class="badge bg-success-subtle text-success px-3 rounded-pill">Aktif</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 rounded-pill">Nonaktif</span>
                                @endif
                            </div>
                            <div class="location-icon">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <h5 class="fw-bold mb-3">{{ $l->nama_lokasi }}</h5>
                            
                            <div class="coord-item">
                                <i class="fas fa-latitude"></i>
                                <span>Lat: <strong>{{ $l->latitude }}</strong></span>
                            </div>
                            <div class="coord-item">
                                <i class="fas fa-longitude"></i>
                                <span>Lng: <strong>{{ $l->longitude }}</strong></span>
                            </div>
                            
                            <div class="radius-badge">
                                <i class="fas fa-bullseye me-1"></i> Radius: {{ $l->radius }}m
                            </div>

                            <hr class="my-3 opacity-50">
                            
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-warning rounded-pill px-3 btn-edit"
                                    data-bs-toggle="modal" data-bs-target="#modalEditLokasi"
                                    data-id="{{ $l->id }}"
                                    data-nama="{{ $l->nama_lokasi }}"
                                    data-lat="{{ $l->latitude }}"
                                    data-lng="{{ $l->longitude }}"
                                    data-radius="{{ $l->radius }}"
                                    data-active="{{ $l->is_active }}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3 btn-delete"
                                    data-bs-toggle="modal" data-bs-target="#modalHapusLokasi"
                                    data-url="{{ route('admin.destroyLokasi', $l->id) }}">
                                    <i class="fas fa-trash me-1"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-map-marker-slash"></i></div>
                            <p>Belum ada lokasi absensi yang terdaftar.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambahLokasi" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.storeLokasi') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white py-3 px-4">
                        <h5 class="modal-title fw-bold">Tambah Lokasi Baru</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lokasi</label>
                            <input type="text" name="nama_lokasi" class="form-control rounded-pill px-3" required placeholder="Contoh: Fasilkom Kampus Palembang">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Latitude</label>
                                <input type="text" name="latitude" class="form-control rounded-pill px-3" required placeholder="-2.98472005">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Longitude</label>
                                <input type="text" name="longitude" class="form-control rounded-pill px-3" required placeholder="104.73225951">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label fw-bold">Radius Absen (Meter)</label>
                            <input type="number" name="radius" class="form-control rounded-pill px-3" required value="500">
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Lokasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEditLokasi" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="formEditLokasi" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-warning text-dark py-3 px-4">
                        <h5 class="modal-title fw-bold">Edit Lokasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lokasi</label>
                            <input type="text" name="nama_lokasi" id="edit_nama" class="form-control rounded-pill px-3" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Latitude</label>
                                <input type="text" name="latitude" id="edit_lat" class="form-control rounded-pill px-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Longitude</label>
                                <input type="text" name="longitude" id="edit_lng" class="form-control rounded-pill px-3" required>
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Radius (Meter)</label>
                                <input type="number" name="radius" id="edit_radius" class="form-control rounded-pill px-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status</label>
                                <select name="is_active" id="edit_active" class="form-select rounded-pill px-3">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Update Lokasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="modalHapusLokasi" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-body p-4 text-center">
                    <div class="text-danger mb-3" style="font-size: 50px;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h5 class="fw-bold">Hapus Lokasi?</h5>
                    <p class="text-muted mb-4">Aksi ini tidak dapat dibatalkan.</p>
                    <form id="formHapusLokasi" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger rounded-pill fw-bold">Ya, Hapus</button>
                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Edit Modal Handler
            const editButtons = document.querySelectorAll('.btn-edit');
            const editForm = document.getElementById('formEditLokasi');
            
            editButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    editForm.action = `/admin/lokasi/${id}`;
                    
                    document.getElementById('edit_nama').value = this.dataset.nama;
                    document.getElementById('edit_lat').value = this.dataset.lat;
                    document.getElementById('edit_lng').value = this.dataset.lng;
                    document.getElementById('edit_radius').value = this.dataset.radius;
                    document.getElementById('edit_active').value = this.dataset.active;
                });
            });

            // Delete Modal Handler
            const deleteButtons = document.querySelectorAll('.btn-delete');
            const deleteForm = document.getElementById('formHapusLokasi');

            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    deleteForm.action = this.dataset.url;
                });
            });
        });
    </script>
    @endpush
@endsection
