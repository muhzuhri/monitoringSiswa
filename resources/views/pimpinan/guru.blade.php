@extends('layouts.nav.pimpinan')

@section('title', 'Daftar Guru Pembimbing - Pimpinan Dashboard')
@section('body-class', 'dashboard-page pimpinan-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pimpinan/guru.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pimpinan/modals.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/pimpinan/guru.js') }}"></script>
@endpush

@section('body')
    <div class="management-container">
        
        <!-- Global Navigation Tabs -->
       <div class="tabs-wrapper">
            <div class="tabs-nav">
                <a href="{{ route('pimpinan.admin') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('pimpinan.admin') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin</span>
                </a>
                <a href="{{ route('pimpinan.siswa') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('pimpinan.siswa') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Siswa</span>
                </a>
                <a href="{{ route('pimpinan.guru') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('pimpinan.guru') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Guru</span>
                </a>
                <a href="{{ route('pimpinan.pembimbing') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('pimpinan.pembimbing') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i>
                    <span>Pembimbing</span>
                </a>
            </div>
        </div>

        <div class="admin-content-wrapper shadow-sm" style="border-radius: 24px; background: #fff; overflow: hidden;">
            <!-- Header -->
            <div class="management-header p-4" style="border-bottom: 1px solid rgba(0,0,0,0.05); background: #fdfdfd;">
                <div class="header-title d-flex align-items-center gap-3">
                    <div class="header-logo-icon" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); color: #fff; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 14px; font-size: 1.5rem;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Daftar Guru Pembimbing</h5>
                        <p class="text-muted small mb-0">Total <strong>{{ $guru->total() }}</strong> pendidik yang membimbing siswa magang.</p>
                    </div>
                </div>                
            </div>

            <!-- Data Table Area -->
            <div class="data-table-wrapper px-4 pb-4">
                <table class="main-table mt-3">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama Lengkap</th>
                            <th>NIP / Identitas</th>
                            <th>Email</th>
                            <th class="text-center">Total Bimbingan</th>
                            <th style="width: 100px;" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($guru as $index => $item)
                            <tr>
                                <td data-label="#">{{ $guru->firstItem() + $index }}</td>
                                <td data-label="Nama" class="fw-bold text-dark">{{ $item->nama }}</td>
                                <td data-label="NIP"><span class="badge bg-light text-dark border">{{ $item->id_guru }}</span></td>
                                <td data-label="Email" class="text-primary">{{ $item->email }}</td>
                                <td data-label="Siswa Bimbingan" class="text-center">
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
                                                return [
                                                    'nama' => $s->nama, 
                                                    'nisn' => $s->nisn,
                                                    'id_tahun_ajaran' => $s->id_tahun_ajaran,
                                                    'tahun_ajaran' => $s->tahunAjaran->tahun_ajaran ?? '-'
                                                ]; 
                                            })) }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="empty-state">
                                        <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i>
                                        <p>Belum ada data guru pembimbing.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($guru->hasPages())
                <div class="pagination-container px-4 pb-4">
                    {{ $guru->links() }}
                </div>
            @endif
        </div>
    </div>

    @include('pimpinan.modals')
@endsection
