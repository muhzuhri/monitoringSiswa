/**
 * Tampilkan/menyembunyikan form sesuai peran yang dipilih (Siswa, Guru, Dosen, Admin).
 */
function updateRoleForms() {
    const role = document.getElementById('role').value;
    const formSiswa = document.getElementById('form-siswa');
    const formGuru = document.getElementById('form-guru');
    const formDosen = document.getElementById('form-dosen');
    const formAdmin = document.getElementById('form-admin');

    formSiswa.classList.add('d-none');
    formGuru.classList.add('d-none');
    formDosen.classList.add('d-none');
    formAdmin.classList.add('d-none');

    if (role === 'siswa') {
        formSiswa.classList.remove('d-none');
    } else if (role === 'guru') {
        formGuru.classList.remove('d-none');
    } else if (role === 'dosen') {
        formDosen.classList.remove('d-none');
    } else if (role === 'admin') {
        formAdmin.classList.remove('d-none');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role');
    if (roleSelect) {
        roleSelect.addEventListener('change', updateRoleForms);
        updateRoleForms();
    }

    const magangTypeSelect = document.getElementById('tipe_magang');
    const groupLeaderSection = document.getElementById('group-leader-section');
    if (magangTypeSelect && groupLeaderSection) {
        magangTypeSelect.addEventListener('change', function() {
            if (this.value === 'kelompok') {
                groupLeaderSection.classList.remove('d-none');
            } else {
                groupLeaderSection.classList.add('d-none');
            }
        });
    }
});
