document.addEventListener('DOMContentLoaded', function() {
    // Live Search Logic
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const pendingRows = document.querySelectorAll('#pending table tbody tr:not(#noResultsPending)');
    const historyRows = document.querySelectorAll('#history table tbody tr:not(#noResultsHistory)');
    const noResultsPending = document.getElementById('noResultsPending');
    const noResultsHistory = document.getElementById('noResultsHistory');

    // Auto-buka tab riwayat jika ada param ?tab=history atau ada filter periode aktif
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    if (activeTab === 'history' || urlParams.get('periode')) {
        const historyTabBtn = document.getElementById('history-tab');
        if (historyTabBtn) {
            // Check if bootstrap is available (it should be)
            if (typeof bootstrap !== 'undefined') {
                const tab = new bootstrap.Tab(historyTabBtn);
                tab.show();
            }
        }
    } else if (activeTab === 'kriteria') {
        const kriteriaTabBtn = document.getElementById('kriteria-tab');
        if (kriteriaTabBtn) {
            if (typeof bootstrap !== 'undefined') {
                const tab = new bootstrap.Tab(kriteriaTabBtn);
                tab.show();
            }
        }
    }

    // Hide search on kriteria tab
    document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function (e) {
            const searchSection = document.querySelector('.search-section');
            if (searchSection) {
                searchSection.style.display = e.target.id === 'kriteria-tab' ? 'none' : 'block';
            }
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            // Filter Pending Table
            let pendingMatchFound = false;
            pendingRows.forEach(row => {
                if (row.querySelector('strong') === null) return; // Skip empty state row
                
                const text = row.innerText.toLowerCase();
                const isMatch = text.includes(searchTerm);
                row.style.display = isMatch ? '' : 'none';
                if (isMatch) pendingMatchFound = true;
            });
            if (noResultsPending) {
                noResultsPending.style.display = (pendingMatchFound || searchTerm === '') ? 'none' : 'table-row';
            }

            // Filter History Table
            let historyMatchFound = false;
            historyRows.forEach(row => {
                if (row.querySelector('strong') === null) return; // Skip empty state row
                
                const text = row.innerText.toLowerCase();
                const isMatch = text.includes(searchTerm);
                row.style.display = isMatch ? '' : 'none';
                if (isMatch) historyMatchFound = true;
            });
            if (noResultsHistory) {
                noResultsHistory.style.display = (historyMatchFound || searchTerm === '') ? 'none' : 'table-row';
            }
        });

        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
            });
        }
    }
});

function setupEditCriteria(el) {
    const form = document.getElementById('formEditKriteria');
    if (form) {
        form.action = `/pembimbing/kriteria/${el.dataset.id}`;
        document.getElementById('edit_nama').value = el.dataset.nama;
        document.getElementById('edit_tipe').value = el.dataset.tipe;
        document.getElementById('edit_urutan').value = el.dataset.urutan;
    }
}
