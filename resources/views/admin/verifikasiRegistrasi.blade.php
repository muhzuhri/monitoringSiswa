@extends('layouts.nav.admin')

@section('title', 'Verifikasi Registrasi - Monitoring Siswa Magang')
@section('body-class', 'dashboard-page admin-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-siswa.css') }}">
    <style>
        .id-card-preview {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.2s;
        }
        .id-card-preview:hover {
            transform: scale(1.02);
        }
        .badge-pending {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
@endpush

@section('body')
    <div class="management-container">
        <div class="admin-content-wrapper">
            <div class="management-header">
                <div class="header-title d-flex align-items-center gap-3">
                    <div class="header-logo-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <h5>Verifikasi Registrasi</h5>
                        <p>Setujui atau tolak pendaftaran akun Siswa dan Guru.</p>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="custom-alert alert-success-custom">
                    <span><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="tabs-wrapper mb-4">
                <div class="tabs-nav d-flex w-100 gap-2 p-1" style="background: rgba(15, 23, 42, 0.03); border-radius: 16px;" role="tablist">
                    <button class="tab-button active flex-fill justify-content-center" id="siswa-tab" data-bs-toggle="pill"
                        data-bs-target="#pane-siswa" type="button" role="tab" style="border-radius: 12px;">
                        <i class="fas fa-users"></i>
                        <span>Siswa Magang ({{ $siswaPending->count() }})</span>
                    </button>
                    <button class="tab-button flex-fill justify-content-center" id="guru-tab" data-bs-toggle="pill"
                        data-bs-target="#pane-guru" type="button" role="tab" style="border-radius: 12px;">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Guru Pembimbing ({{ $guruPending->count() }})</span>
                    </button>
                </div>
            </div>

            <div class="tab-content">
                {{-- TAB: SISWA --}}
                <div class="tab-pane fade show active" id="pane-siswa" role="tabpanel">
                    @if($siswaPending->count() > 0)
                    <div class="d-flex justify-content-end mb-3">
                        <form action="{{ route('admin.verifikasiSemuaSiswa') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary rounded-pill px-4" onclick="return confirm('Verifikasi semua akun siswa yang pending?')">
                                <i class="fas fa-check-double me-1"></i> Verifikasi Semua Siswa
                            </button>
                        </form>
                    </div>
                    @endif
                    <div class="data-table-wrapper">
                        <table class="main-table">
                            <thead>
                                <tr>
                                    <th>Tgl Registrasi</th>
                                    <th>Nama Siswa</th>
                                    <th>Sekolah / NISN</th>
                                    <th>Tanda Pengenal</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($siswaPending as $s)
                                    <tr>
                                        <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="cell-name fw-bold">{{ $s->nama }}</div>
                                            <div class="cell-sub text-muted small">{{ $s->email }}</div>
                                        </td>
                                        <td>
                                            <div class="cell-name">{{ $s->sekolah }}</div>
                                            <div class="cell-sub text-muted small">NISN: {{ $s->nisn }}</div>
                                        </td>
                                        <td>
                                            @if($s->tanda_pengenal)
                                                @php $ext = pathinfo($s->tanda_pengenal, PATHINFO_EXTENSION); @endphp
                                                @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                                                    <img src="{{ asset('storage/' . $s->tanda_pengenal) }}" class="id-card-preview" style="height: 40px;" 
                                                         data-bs-toggle="modal" data-bs-target="#previewModal{{ $s->nisn }}">
                                                @else
                                                    <a href="{{ asset('storage/' . $s->tanda_pengenal) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-file-pdf"></i> Lihat PDF
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-muted">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <form action="{{ route('admin.verifikasiSiswa', $s->nisn) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm rounded-pill px-3">
                                                        <i class="fas fa-check me-1"></i> Setujui
                                                    </button>
                                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm rounded-pill px-3" 
                                                            onclick="return confirm('Apakah Anda yakin ingin menolak pendaftaran ini?')">
                                                        <i class="fas fa-times me-1"></i> Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Individual Modal for Image Preview --}}
                                    @if($s->tanda_pengenal && in_array(strtolower(pathinfo($s->tanda_pengenal, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                    <div class="modal fade" id="previewModal{{ $s->nisn }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 bg-transparent">
                                                <div class="modal-body p-0 text-center">
                                                    <img src="{{ asset('storage/' . $s->tanda_pengenal) }}" class="img-fluid rounded shadow">
                                                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center p-5 text-muted">
                                            <i class="fas fa-check-circle fa-3x mb-3 opacity-20"></i>
                                            <p>Tidak ada pendaftaran siswa yang perlu diverifikasi.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB: GURU --}}
                <div class="tab-pane fade" id="pane-guru" role="tabpanel">
                    @if($guruPending->count() > 0)
                    <div class="d-flex justify-content-end mb-3">
                        <form action="{{ route('admin.verifikasiSemuaGuru') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary rounded-pill px-4" onclick="return confirm('Verifikasi semua akun guru yang pending?')">
                                <i class="fas fa-check-double me-1"></i> Verifikasi Semua Guru
                            </button>
                        </form>
                    </div>
                    @endif
                    <div class="data-table-wrapper">
                        <table class="main-table">
                            <thead>
                                <tr>
                                    <th>Tgl Registrasi</th>
                                    <th>Nama Guru</th>
                                    <th>Sekolah / NIP</th>
                                    <th>Tanda Pengenal</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($guruPending as $g)
                                    <tr>
                                        <td>{{ $g->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="cell-name fw-bold">{{ $g->nama }}</div>
                                            <div class="cell-sub text-muted small">{{ $g->email }}</div>
                                        </td>
                                        <td>
                                            <div class="cell-name">{{ $g->sekolah }}</div>
                                            <div class="cell-sub text-muted small">NIP: {{ $g->id_guru }}</div>
                                        </td>
                                        <td>
                                            @if($g->tanda_pengenal)
                                                @php $ext = pathinfo($g->tanda_pengenal, PATHINFO_EXTENSION); @endphp
                                                @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                                                    <img src="{{ asset('storage/' . $g->tanda_pengenal) }}" class="id-card-preview" style="height: 40px;" 
                                                         data-bs-toggle="modal" data-bs-target="#previewModalG{{ $g->id_guru }}">
                                                @else
                                                    <a href="{{ asset('storage/' . $g->tanda_pengenal) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-file-pdf"></i> Lihat PDF
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-muted">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <form action="{{ route('admin.verifikasiGuru', $g->id_guru) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm rounded-pill px-3">
                                                        <i class="fas fa-check me-1"></i> Setujui
                                                    </button>
                                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm rounded-pill px-3" 
                                                            onclick="return confirm('Apakah Anda yakin ingin menolak pendaftaran ini?')">
                                                        <i class="fas fa-times me-1"></i> Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Individual Modal for Image Preview --}}
                                    @if($g->tanda_pengenal && in_array(strtolower(pathinfo($g->tanda_pengenal, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                    <div class="modal fade" id="previewModalG{{ $g->id_guru }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 bg-transparent">
                                                <div class="modal-body p-0 text-center">
                                                    <img src="{{ asset('storage/' . $g->tanda_pengenal) }}" class="img-fluid rounded shadow">
                                                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center p-5 text-muted">
                                            <i class="fas fa-check-circle fa-3x mb-3 opacity-20"></i>
                                            <p>Tidak ada pendaftaran guru yang perlu diverifikasi.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
