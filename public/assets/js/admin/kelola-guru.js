document.addEventListener('DOMContentLoaded', function () {
    // Edit Logic
    const editButtons = document.querySelectorAll('.btn-edit');
    const editForm = document.getElementById('formEditGuru');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const email = this.getAttribute('data-email');
            const id_guru = this.getAttribute('data-id_guru');
            const jabatan = this.getAttribute('data-jabatan');
            const sekolah = this.getAttribute('data-sekolah');

            editForm.action = `/admin/guru/${id}`;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_id_guru').value = id_guru;
            document.getElementById('edit_jabatan').value = jabatan;
            document.getElementById('edit_sekolah').value = sekolah;
            document.getElementById('edit_hp').value = this.getAttribute('data-no_hp') || '';
            document.getElementById('edit_npsn').value = this.getAttribute('data-npsn') || '';
        });
    });

    // Preview Detail Logic
    let currentSiswas = [];
    const detailButtons = document.querySelectorAll('.btn-detail');
    const filterPeriode = document.getElementById('filter_periode');

    function renderStudents(siswas) {
        const listContainer = document.getElementById('supervised_students_list');
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
            listContainer.innerHTML = '<div class="text-muted" style="padding: 1rem;">Belum ada siswa bimbingan untuk periode ini.</div>';
        }
    }

    detailButtons.forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('det_nama').textContent = this.getAttribute('data-nama');
            document.getElementById('det_id_guru').textContent = this.getAttribute('data-id_guru');
            document.getElementById('det_email').textContent = this.getAttribute('data-email');
            document.getElementById('det_sekolah').textContent = this.getAttribute('data-sekolah');
            document.getElementById('det_jabatan').textContent = this.getAttribute('data-jabatan');
            document.getElementById('det_no_hp').textContent = this.getAttribute('data-no_hp') || '-';
            document.getElementById('det_npsn').textContent = this.getAttribute('data-npsn') || '-';

            // Reset filter
            if (filterPeriode) filterPeriode.value = 'all';

            // Populate supervised students list
            currentSiswas = JSON.parse(this.getAttribute('data-siswas'));
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
        button.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            deleteForm.action = url;
        });
    });

    // ── NPSN Lookup Logic ──────────────────────────────────────────
    function initNpsnLookup(inputId, outputId, msgId) {
        const npsnInput = document.getElementById(inputId);
        const schoolInput = document.getElementById(outputId);
        const msgEl = document.getElementById(msgId);

        if (!npsnInput || !schoolInput) return;

        npsnInput.addEventListener('input', function() {
            const npsn = this.value;
            if (npsn.length >= 8) {
                if (msgEl) {
                    msgEl.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mencari...';
                    msgEl.className = 'text-primary small mt-1 d-block';
                }
                
                // Clear school name initially to show it's working
                schoolInput.value = '';

                fetch(`/api/schools/${npsn}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            schoolInput.value = data.data.nama_sekolah;
                            schoolInput.readOnly = true;
                            if (msgEl) {
                                msgEl.innerHTML = '<i class="fas fa-check-circle me-1"></i> Terindentifikasi';
                                msgEl.className = 'text-success small mt-1 d-block';
                            }
                        } else {
                            schoolInput.readOnly = false;
                            schoolInput.placeholder = "Isi manual jika tidak ditemukan";
                            if (msgEl) {
                                msgEl.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Tidak terdaftar. Isi manual.';
                                msgEl.className = 'text-danger small mt-1 d-block';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching school:', error);
                        schoolInput.readOnly = false;
                        if (msgEl) msgEl.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Gangguan koneksi. Isi manual.';
                    });
            } else {
                if (msgEl) {
                    msgEl.innerHTML = 'Min. 8 digit';
                    msgEl.className = 'text-muted small mt-1 d-block';
                }
            }
        });
    }

    initNpsnLookup('reg_npsn_guru', 'reg_sekolah_guru', 'reg_npsn_guru_msg');
    initNpsnLookup('edit_npsn', 'edit_sekolah', null);

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
