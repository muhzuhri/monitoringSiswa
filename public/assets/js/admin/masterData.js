document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.management-container');
    const activeTab = container ? container.dataset.activeTab : null;

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

    // Edit Prodi Modal
    document.querySelectorAll('.btn-edit-prodi').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('formEditProdi').action = `/admin/prodi/${id}`;
            document.getElementById('edit_prodi_nama').value = this.getAttribute('data-nama');
            document.getElementById('edit_prodi_jenjang').value = this.getAttribute('data-jenjang');
            document.getElementById('edit_prodi_warna').value = this.getAttribute('data-warna');
            document.getElementById('edit_prodi_urutan').value = this.getAttribute('data-urutan');
            document.getElementById('edit_prodi_aktif').value = this.getAttribute('data-aktif');
        });
    });

    // Handle active tab from session or local storage
    if(activeTab) {
        const tabId = activeTab + "-tab";
        const tabTrigger = document.getElementById(tabId);
        if(tabTrigger) {
            const tab = new bootstrap.Tab(tabTrigger);
            tab.show();
        }
    }
});

// Misi Field Logic
window.addMisiField = function() {
    const container = document.getElementById('misi-container');
    if (!container) return;
    const itemCount = container.querySelectorAll('.misi-item').length + 1;
    
    const html = `
        <div class="input-group mb-2 misi-item">
            <span class="input-group-text bg-light border-end-0">${itemCount}.</span>
            <textarea name="misi[]" class="form-control border-start-0" rows="1"></textarea>
            <button type="button" class="btn btn-outline-danger" onclick="this.closest('.misi-item').remove(); updateMisiNumbers();"><i class="fas fa-times"></i></button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
    updateMisiNumbers();
};

window.updateMisiNumbers = function() {
    const container = document.getElementById('misi-container');
    if (!container) return;
    const items = container.querySelectorAll('.misi-item');
    items.forEach((item, index) => {
        item.querySelector('.input-group-text').innerText = (index + 1) + '.';
    });
};
