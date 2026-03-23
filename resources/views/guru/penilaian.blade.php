@extends('layouts.nav.guru')

@section('title', 'Penilaian Magang Siswa - SIM Magang')
@section('body-class', 'dashboard-page guru-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/guru/penilaian.css') }}">
@endpush

@section('body')
    <div class="page-wrapper">
        <div class="page-header">
            <div class="header-text">
                <h3 class="page-title">Penilaian Akhir Magang</h3>
                <p class="page-subtitle">Kelola dan berikan nilai akhir untuk siswa bimbingan Anda.</p>
            </div>
            @if(isset($siswa))
                <a href="{{ route('guru.penilaian') }}" class="btn-action btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="ui-alert ui-alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(isset($siswasPending) && isset($siswasDone))
            {{-- Search Bar --}}
            <div class="search-section mb-4">
                <form action="{{ route('guru.penilaian') }}" method="GET" class="search-form">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" 
                            placeholder="Cari nama siswa atau NISN..." value="{{ $search ?? '' }}">
                        <button type="submit" class="btn btn-primary px-4">Cari</button>
                        @if($search)
                            <a href="{{ route('guru.penilaian') }}" class="btn btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tab Navigasi --}}
            <div class="tabs-wrapper mb-4">
                <div class="tabs-nav" role="tablist">
                    <button class="tab-button active" id="pending-tab" data-bs-toggle="pill" data-bs-target="#pending"
                        type="button" role="tab">
                        <i class="fas fa-hourglass-half"></i>
                        <span>Menunggu Penilaian ({{ $siswasPending->count() }})</span>
                    </button>
                    <button class="tab-button" id="history-tab" data-bs-toggle="pill" data-bs-target="#history"
                        type="button" role="tab">
                        <i class="fas fa-check-double"></i>
                        <span>Riwayat Penilaian ({{ $siswasDone->count() }})</span>
                    </button>
                </div>
            </div>

            <div class="tab-content">
                {{-- Tab Menunggu Penilaian --}}
                <div class="tab-pane fade show active" id="pending" role="tabpanel">
                    <div class="ui-card">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>NISN</th>
                                        <th>Instansi</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswasPending as $s)
                                        <tr>
                                            <td><strong>{{ $s->nama }}</strong></td>
                                            <td>{{ $s->nisn }}</td>
                                            <td>{{ $s->perusahaan }}</td>
                                            <td><span class="badge-status status-pending">Belum Dinilai</span></td>
                                            <td class="text-end">
                                                <a href="{{ route('guru.penilaian.input', $s->nisn) }}" class="btn-action btn-primary">
                                                    <i class="fas fa-edit"></i> Input Nilai
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="empty-state">
                                                <i class="fas fa-clipboard-check mb-3 fa-3x text-muted"></i>
                                                <p>Tidak ada siswa yang menunggu penilaian.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Tab Riwayat Penilaian --}}
                <div class="tab-pane fade" id="history" role="tabpanel">
                    <div class="ui-card">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>NISN</th>
                                        <th>Rata-rata</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswasDone as $s)
                                        @php
                                            $p = $s->penilaians->where('pemberi_nilai', 'Guru Pembimbing')->first();
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $s->nama }}</strong></td>
                                            <td>{{ $s->nisn }}</td>
                                            <td><span class="fw-bold text-primary">{{ number_format($p->rata_rata, 1) }}</span></td>
                                            <td><span class="badge-status status-done">Sudah Dinilai</span></td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="{{ route('guru.penilaian.input', $s->nisn) }}" class="btn-action btn-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="{{ route('guru.penilaian.export', $s->nisn) }}" class="btn-action btn-success">
                                                        <i class="fas fa-print"></i> Cetak
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="empty-state">
                                                <i class="fas fa-history mb-3 fa-3x text-muted"></i>
                                                <p>Belum ada riwayat penilaian.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(isset($siswa))
            {{-- INPUT MODE --}}
            <div class="ui-card">
                <div class="card-header">
                    <h4 class="card-title">Form Penilaian Siswa</h4>
                    <p class="card-description">Nama: <strong>{{ $siswa->nama }}</strong> | NISN: <strong>{{ $siswa->nisn }}</strong></p>
                </div>

                <form action="{{ route('guru.penilaian.store', $siswa->nisn) }}" method="POST">
                    @csrf
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Kategori Penilaian</label>
                        <select name="kategori" class="form-control" required>
                            <option value="Penilaian Akhir Magang" {{ old('kategori', $penilaian ? $penilaian->kategori : '') == 'Penilaian Akhir Magang' ? 'selected' : '' }}>Penilaian Akhir Magang</option>
                            <option value="Penilaian Tengah Magang" {{ old('kategori', $penilaian ? $penilaian->kategori : '') == 'Penilaian Tengah Magang' ? 'selected' : '' }}>Penilaian Tengah Magang</option>
                        </select>
                    </div>

                    <div class="criteria-section mb-4">
                        <h5 class="section-title">I. KEPRIBADIAN / ETOS KERJA</h5>
                        <div class="form-grid">
                            @foreach($kriteria->where('tipe', 'guru_kepribadian') as $k)
                                @php
                                    $score = $penilaian ? $penilaian->penilaianDetails->where('id_kriteria', $k->id_kriteria)->first()->skor ?? '' : '';
                                @endphp
                                <div class="form-group">
                                    <label class="form-label">{{ $k->nama_kriteria }} (0-100)</label>
                                    <input type="number" name="scores[{{ $k->id_kriteria }}]" class="form-control" min="0" max="100" 
                                        value="{{ old('scores.'.$k->id_kriteria, $score) }}" required>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="criteria-section mb-4">
                        <h5 class="section-title">II. KEMAMPUAN</h5>
                        <div class="form-grid">
                            @foreach($kriteria->where('tipe', 'guru_kemampuan') as $k)
                                @php
                                    $score = $penilaian ? $penilaian->penilaianDetails->where('id_kriteria', $k->id_kriteria)->first()->skor ?? '' : '';
                                @endphp
                                <div class="form-group">
                                    <label class="form-label">{{ $k->nama_kriteria }} (0-100)</label>
                                    <input type="number" name="scores[{{ $k->id_kriteria }}]" class="form-control" min="0" max="100" 
                                        value="{{ old('scores.'.$k->id_kriteria, $score) }}" required>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group full-width mb-4">
                        <label class="form-label">Saran / Catatan</label>
                        <textarea name="saran" class="form-control" rows="4" placeholder="Masukkan saran pengembangan untuk siswa...">{{ old('saran', $penilaian ? $penilaian->saran : '') }}</textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-action btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                            <i class="fas fa-save"></i> Simpan Penilaian
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
@endsection
