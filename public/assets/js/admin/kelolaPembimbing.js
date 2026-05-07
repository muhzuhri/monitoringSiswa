document.addEventListener('DOMContentLoaded', function() {
    // Edit Logic
    const editButtons = document.querySelectorAll('.btn-edit');
    const editForm = document.getElementById('formEditDosen');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const email = this.getAttribute('data-email');
            const jabatan = this.getAttribute('data-jabatan');
            const instansi = this.getAttribute('data-instansi');
            const telp = this.getAttribute('data-telp');

            if (editForm) editForm.action = `/admin/pembimbing/${id}`;
            const idDisplay = document.getElementById('edit_id_display');
            if (idDisplay) idDisplay.value = id;
            const namaInput = document.getElementById('edit_nama');
            if (namaInput) namaInput.value = nama;
            const emailInput = document.getElementById('edit_email');
            if (emailInput) emailInput.value = email;
            const jabatanInput = document.getElementById('edit_jabatan');
            if (jabatanInput) jabatanInput.value = jabatan;
            const instansiInput = document.getElementById('edit_instansi');
            if (instansiInput) instansiInput.value = instansi;
            const telpInput = document.getElementById('edit_telp');
            if (telpInput) telpInput.value = telp;
        });
    });

    // Preview Detail Logic
    let currentSiswas = [];
    const detailButtons = document.querySelectorAll('.btn-detail');
    const filterPeriode = document.getElementById('filter_periode');

    function renderStudents(siswas) {
        const listContainer = document.getElementById('supervised_students_list');
        if (!listContainer) return;
        listContainer.innerHTML = '';

        if (siswas.length > 0) {
            siswas.forEach(s => {
                const studentDiv = document.createElement('div');
                studentDiv.className = 'student-card-mini';
                studentDiv.innerHTML = `
                    <div class="student-info">
                        <div class="nama">${s.nama}</div>
                        <div class="meta">NISN: ${s.nisn} | <span class="text-primary">${s.periode}</span></div>
                    </div>
                    <i class="fas fa-user-graduate"></i>
                `;
                listContainer.appendChild(studentDiv);
            });
        } else {
            listContainer.innerHTML =
                '<div class="text-muted" style="padding: 1rem;">Belum ada siswa bimbingan untuk periode ini.</div>';
        }
    }

    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const editBtn = this.closest('tr').querySelector('.btn-edit');
            const id = editBtn ? editBtn.getAttribute('data-id') : '-';
            
            const detId = document.getElementById('det_id');
            if (detId) detId.textContent = id;
            const detNama = document.getElementById('det_nama');
            if (detNama) detNama.textContent = this.getAttribute('data-nama');
            const detJabatan = document.getElementById('det_jabatan');
            if (detJabatan) detJabatan.textContent = this.getAttribute('data-jabatan');
            const detEmail = document.getElementById('det_email');
            if (detEmail) detEmail.textContent = this.getAttribute('data-email');
            const detTelp = document.getElementById('det_telp');
            if (detTelp) detTelp.textContent = this.getAttribute('data-telp');
            const detInstansi = document.getElementById('det_instansi');
            if (detInstansi) detInstansi.textContent = this.getAttribute('data-instansi');

            // Reset filter
            if (filterPeriode) filterPeriode.value = 'all';

            // Populate supervised students list
            const siswasData = this.getAttribute('data-siswas');
            currentSiswas = siswasData ? JSON.parse(siswasData) : [];
            renderStudents(currentSiswas);
        });
    });

    if (filterPeriode) {
        filterPeriode.addEventListener('change', function() {
            const selectedPeriode = this.value;
            const filtered = selectedPeriode === 'all' 
                ? currentSiswas 
                : currentSiswas.filter(s => s.id_periode == selectedPeriode);
            renderStudents(filtered);
        });
    }

    // Delete Logic
    const deleteButtons = document.querySelectorAll('.btn-delete-trigger');
    const deleteForm = document.getElementById('formHapus');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            if (deleteForm) deleteForm.action = url;
        });
    });

    // Password Toggle Logic
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('input');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
});
