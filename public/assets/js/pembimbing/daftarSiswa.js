document.addEventListener('DOMContentLoaded', function() {
    // Main Tab Switching
    const tabTriggers = document.querySelectorAll('.btn-tab-trigger');
    const tabPanes = document.querySelectorAll('.tab-pane-content');

    function activateTab(targetId) {
        tabTriggers.forEach(b => b.classList.remove('active'));
        tabPanes.forEach(pane => {
            pane.style.display = pane.id === targetId ? 'block' : 'none';
        });
        const activeBtn = document.querySelector(`.btn-tab-trigger[data-target="${targetId}"]`);
        if (activeBtn) activeBtn.classList.add('active');
    }

    tabTriggers.forEach(btn => {
        btn.addEventListener('click', function() {
            activateTab(this.getAttribute('data-target'));
        });
    });

    // Auto-buka tab riwayat jika ada param ?tab=history atau ada filter periode aktif
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'history' || urlParams.get('periode')) {
        activateTab('history-students');
    }

    // Live Search Logic for both tables
    const searchInput = document.getElementById('searchInput');
    const activeRows = document.querySelectorAll('#activeTableBody .student-row');
    const historyRows = document.querySelectorAll('#historyTableBody .student-row');
    const noResultsActive = document.getElementById('noResultsActive');
    const noResultsHistory = document.getElementById('noResultsHistory');
    const emptyRows = document.querySelectorAll('.empty-row');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const tables = [
                { rows: activeRows, noResults: noResultsActive },
                { rows: historyRows, noResults: noResultsHistory }
            ];

            tables.forEach(({ rows, noResults }) => {
                let hasMatch = false;
                rows.forEach(row => {
                    const isMatch = row.innerText.toLowerCase().includes(searchTerm);
                    row.style.display = isMatch ? 'table-row' : 'none';
                    if (isMatch) hasMatch = true;
                });

                if (searchTerm === '') {
                    noResults.style.display = 'none';
                    emptyRows.forEach(row => row.style.display = 'table-row');
                } else {
                    emptyRows.forEach(row => row.style.display = 'none');
                    noResults.style.display = hasMatch ? 'none' : 'table-row';
                }
            });
        });

        const searchForm = document.getElementById('searchForm');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => e.preventDefault());
        }
    }

    // Modal Logic
    const modalOverlay = document.getElementById('studentProfileModalOverlay');
    const closeModalBtn = document.getElementById('closeProfileModal');
    
    function openModal() {
        if (modalOverlay) {
            modalOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal() {
        if (modalOverlay) {
            modalOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) closeModal();
        });
    }

    document.querySelectorAll('.btn-show-profile').forEach(button => {
        button.addEventListener('click', function() {
            const siswa = JSON.parse(this.getAttribute('data-siswa'));
            
            document.getElementById('modalName').innerText = siswa.nama;
            document.getElementById('modalNisn').innerText = 'NISN: ' + siswa.nisn;
            document.getElementById('modalEmail').innerText = siswa.email || '-';
            document.getElementById('modalPhone').innerText = siswa.no_hp || '-';
            document.getElementById('modalSchool').innerText = siswa.sekolah;
            document.getElementById('modalClass').innerText = (siswa.kelas || '-') + ' / ' + (siswa.jurusan || '-');
            document.getElementById('modalCompany').innerText = siswa.perusahaan;
            document.getElementById('modalGuru').innerText = siswa.guru ? siswa.guru.nama : '-';
            
            // Format Period
            if (siswa.tgl_mulai_magang && siswa.tgl_selesai_magang) {
                const start = new Date(siswa.tgl_mulai_magang);
                const end = new Date(siswa.tgl_selesai_magang);
                const options = { day: 'numeric', month: 'short', year: 'numeric' };
                document.getElementById('modalPeriod').innerText = start.toLocaleDateString('id-ID', options) + ' - ' + end.toLocaleDateString('id-ID', options);
            } else {
                document.getElementById('modalPeriod').innerText = '-';
            }

            // Handle Photo/Initial
            const initialEl = document.getElementById('modalInitial');
            const photoEl = document.getElementById('modalPhoto');
            
            if (siswa.foto_profil) {
                // Gunakan global variable assetPath jika perlu, atau asumsikan link relatif
                photoEl.src = "/storage/" + siswa.foto_profil;
                photoEl.style.display = 'block';
                initialEl.style.display = 'none';
            } else {
                initialEl.innerText = siswa.nama.charAt(0).toUpperCase();
                initialEl.style.display = 'flex';
                photoEl.style.display = 'none';
            }
            
            openModal();
        });
    });
});
