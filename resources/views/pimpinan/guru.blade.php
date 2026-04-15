@extends('layouts.nav.pimpinan')

@section('title', 'Daftar Guru Pembimbing - Pimpinan Dashboard')
@section('body-class', 'dashboard-page pimpinan-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-guru.css') }}">
    <style>
        .pimpinan-read-only-badge {
            background: #f1f5f9;
            color: #475569;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
    </style>
@endpush

@section('body')
    <div class="management-container">
        <div class="admin-content-wrapper">
            <!-- Header -->
            <div class="management-header">
                <div class="header-title">
                    <h5>Daftar Guru Pembimbing</h5>
                    <small>Total {{ $guru->total() }} guru terdaftar.</small>
                </div>
                <div class="header-actions">
                    <div class="pimpinan-read-only-badge">
                        <i class="fas fa-eye"></i> Tampilan Baca-Saja
                    </div>
                </div>
            </div>

            <!-- Data Table Area -->
            <div class="data-table-wrapper">
                <table class="main-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama Lengkap</th>
                            <th>Email Resmi</th>
                            <th>NIP</th>
                            <th>Siswa Bimbingan</th>
                            <th style="width: 100px;" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($guru as $index => $item)
                            <tr>
                                <td data-label="#">{{ $guru->firstItem() + $index }}</td>
                                <td data-label="Nama">{{ $item->nama }}</td>
                                <td data-label="Email">{{ $item->email }}</td>
                                <td data-label="NIP">{{ $item->id_guru }}</td>
                                <td data-label="Siswa Bimbingan">
                                    <span
                                        class="badge {{ $item->siswas->count() > 0 ? 'bg-success-soft text-success' : 'bg-secondary-soft text-muted' }}"
                                        style="border-radius: 8px; padding: 0.5rem 0.75rem; font-weight: 700;">
                                        {{ $item->siswas->count() }} Siswa
                                    </span>
                                </td>
                                <td data-label="Aksi" class="text-end">
                                    <div class="action-group justify-content-end">
                                        <button class="btn-icon btn-detail-soft btn-detail" data-bs-toggle="modal"
                                            data-bs-target="#modalDetailGuru" data-nama="{{ $item->nama }}"
                                            data-email="{{ $item->email }}" data-id_guru="{{ $item->id_guru }}"
                                            data-jabatan="{{ $item->jabatan }}" data-sekolah="{{ $item->sekolah }}" 
                                            data-siswas="{{ json_encode($item->siswas->map(function ($s) {
                                                return ['nama' => $s->nama, 'nisn' => $s->nisn]; 
                                            })) }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i>
                                    <p>Belum ada data guru pembimbing.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($guru->hasPages())
                <div class="pagination-container">
                    {{ $guru->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Preview Detail -->
    <div class="modal fade" id="modalDetailGuru" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header-custom bg-dark text-white py-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-header-icon on-dark">
                            <i class="fas fa-address-card"></i>
                        </div>
                        <h5 class="mb-0">Profil Guru Pembimbing</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light p-4">
                    <div class="detail-grid row g-4">
                        <!-- Left Column: Personal -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <h6 class="text-primary mb-4 fw-bold">
                                        <i class="fas fa-user-circle me-2"></i>Informasi Personal
                                    </h6>
                                    <div class="mb-3">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Nama Lengkap</label>
                                        <span id="det_nama" class="fw-bold fs-5 text-dark">-</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Nomor Induk Pegawai (NIP)</label>
                                        <span id="det_id_guru" class="text-dark">-</span>
                                    </div>
                                    <div class="mb-0">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Alamat Email</label>
                                        <span id="det_email" class="text-primary fw-medium">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Academy -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <h6 class="text-success mb-4 fw-bold">
                                        <i class="fas fa-school me-2"></i>Instansi & Bidang
                                    </h6>
                                    <div class="mb-3">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Sekolah Asal</label>
                                        <span id="det_sekolah" class="text-dark fw-medium">-</span>
                                    </div>
                                    <div class="mb-0">
                                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Jabatan / Mata Pelajaran</label>
                                        <span id="det_jabatan" class="text-dark">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Column: Supervised Students List -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <h6 class="text-dark mb-4 fw-bold">
                                        <i class="fas fa-users me-2"></i>Daftar Siswa Bimbingan
                                    </h6>
                                    <div id="supervised_students_list" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                        <!-- Will be populated by JS -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 py-3 px-4">
                    <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Preview Detail Logic
            const detailButtons = document.querySelectorAll('.btn-detail');
            detailButtons.forEach(button => {
                button.addEventListener('click', function () {
                    document.getElementById('det_nama').textContent = this.getAttribute('data-nama');
                    document.getElementById('det_id_guru').textContent = this.getAttribute('data-id_guru');
                    document.getElementById('det_email').textContent = this.getAttribute('data-email');
                    document.getElementById('det_sekolah').textContent = this.getAttribute('data-sekolah');
                    document.getElementById('det_jabatan').textContent = this.getAttribute('data-jabatan');

                    // Populate supervised students list
                    const siswas = JSON.parse(this.getAttribute('data-siswas'));
                    const listContainer = document.getElementById('supervised_students_list');
                    listContainer.innerHTML = '';

                    if (siswas.length > 0) {
                        siswas.forEach(s => {
                            const studentDiv = document.createElement('div');
                            studentDiv.className = 'col';
                            studentDiv.innerHTML = `
                                <div class="p-3 border rounded-3 bg-light h-100 d-flex align-items-center gap-3">
                                    <div class="bg-white rounded-circle p-2 text-primary shadow-sm">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">${s.nama}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">NISN: ${s.nisn}</div>
                                    </div>
                                </div>
                            `;
                            listContainer.appendChild(studentDiv);
                        });
                    } else {
                        listContainer.innerHTML = '<div class="col-12 text-muted">Belum ada siswa bimbingan yang terdaftar untuk guru ini.</div>';
                    }
                });
            });
        });
    </script>
@endsection
