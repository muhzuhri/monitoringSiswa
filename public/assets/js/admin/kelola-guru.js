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
        });
    });

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
                    studentDiv.className = 'student-card-mini';
                    studentDiv.innerHTML = `
                                        <div class="student-info">
                                            <div class="nama">${s.nama}</div>
                                            <div class="meta">NISN: ${s.nisn}</div>
                                        </div>
                                        <i class="fas fa-user-graduate"></i>
                                    `;
                    listContainer.appendChild(studentDiv);
                });
            } else {
                listContainer.innerHTML = '<div class="text-muted" style="padding: 1rem;">Belum ada siswa bimbingan.</div>';
            }
        });
    });

    // Delete Logic
    const deleteButtons = document.querySelectorAll('.btn-delete-trigger');
    const deleteForm = document.getElementById('formHapus');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            deleteForm.action = url;
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
