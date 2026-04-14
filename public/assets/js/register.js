/**
 * Tampilkan/menyembunyikan form sesuai peran yang dipilih (Siswa, Guru, Dosen, Admin).
 */
function updateRoleForms() {
    const role = document.getElementById('role').value;
    const forms = {
        'siswa': document.getElementById('form-siswa'),
        'guru': document.getElementById('form-guru'),
        'dosen': document.getElementById('form-dosen'),
        'admin': document.getElementById('form-admin')
    };

    // Sembunyikan semua dan nonaktifkan inputnya
    Object.keys(forms).forEach(key => {
        const formEl = forms[key];
        if (formEl) {
            formEl.classList.add('d-none');
            // Disable all inputs inside to prevent browser validation on hidden fields
            const inputs = formEl.querySelectorAll('input, select, textarea');
            inputs.forEach(input => input.disabled = true);
        }
    });

    // Tampilkan yang dipilih dan aktifkan inputnya
    if (role && forms[role]) {
        forms[role].classList.remove('d-none');
        const activeInputs = forms[role].querySelectorAll('input, select, textarea');
        activeInputs.forEach(input => input.disabled = false);
        
        // Trigger specific logic for siswa if needed
        if (role === 'siswa') {
            updateMagangType();
        }
    }
}

/**
 * Logika khusus untuk tipe magang (individu/kelompok)
 */
function updateMagangType() {
    const magangTypeSelect = document.getElementById('tipe_magang');
    const groupLeaderSection = document.getElementById('group-leader-section');
    
    if (magangTypeSelect && groupLeaderSection) {
        if (magangTypeSelect.value === 'kelompok') {
            groupLeaderSection.classList.remove('d-none');
            const inputs = groupLeaderSection.querySelectorAll('input, select');
            inputs.forEach(input => input.disabled = false);
        } else {
            groupLeaderSection.classList.add('d-none');
            const inputs = groupLeaderSection.querySelectorAll('input, select');
            inputs.forEach(input => input.disabled = true);
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role');
    if (roleSelect) {
        roleSelect.addEventListener('change', updateRoleForms);
        updateRoleForms();
    }

    const magangTypeSelect = document.getElementById('tipe_magang');
    if (magangTypeSelect) {
        magangTypeSelect.addEventListener('change', updateMagangType);
    }
});
