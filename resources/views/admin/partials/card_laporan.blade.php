<div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
        <div class="d-flex align-items-center gap-3">
            <div class="icon-box-premium" style="background: rgba(15, 23, 42, 0.05); width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 12px; color: #1e40af;">
                <i class="fas fa-file-alt"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-capitalize">{{ str_replace('_', ' ', $lap->tipe_laporan) }}</h6>
                <small class="text-muted">Konfigurasi header PDF</small>
            </div>
        </div>
    </div>
    <div class="card-body p-4">
        <input type="hidden" name="konfigurasi[{{ $index }}][id]" value="{{ $lap->id }}">
        
        <div class="mb-3">
            <label class="label-premium">Baris Header 1</label>
            <input type="text" name="konfigurasi[{{ $index }}][header_1]" class="form-control input-premium" value="{{ $lap->header_1 }}" placeholder="Contoh: Universitas Sriwijaya">
        </div>
        
        <div class="mb-3">
            <label class="label-premium">Baris Header 2</label>
            <input type="text" name="konfigurasi[{{ $index }}][header_2]" class="form-control input-premium" value="{{ $lap->header_2 }}" placeholder="Contoh: Fakultas Ilmu Komputer">
        </div>

        @if($lap->tipe_laporan != 'absensi_individu' && $lap->tipe_laporan != 'kegiatan_mingguan')
            <div class="mb-3">
                <label class="label-premium">Baris Header 3</label>
                <input type="text" name="konfigurasi[{{ $index }}][header_3]" class="form-control input-premium" value="{{ $lap->header_3 }}">
            </div>
            <div class="mb-0">
                <label class="label-premium">Baris Header 4</label>
                <input type="text" name="konfigurasi[{{ $index }}][header_4]" class="form-control input-premium" value="{{ $lap->header_4 }}">
            </div>
        @endif
    </div>
</div>
