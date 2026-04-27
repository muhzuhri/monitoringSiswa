document.addEventListener('DOMContentLoaded', function() {

    // ═══════════════════════════════════
    // FORM VERIFIKASI — Approve / Reject
    // ═══════════════════════════════════
    const approveRadio  = document.getElementById('approve');
    const rejectRadio   = document.getElementById('reject');
    const catatanWrapper        = document.getElementById('catatanWrapper');
    const catatanApproveWrapper = document.getElementById('catatanApproveWrapper');
    const catatanInput          = document.getElementById('catatan');
    const catatanApproveInput   = document.getElementById('catatanApprove');
    const btnVerify             = document.getElementById('btnVerify');

    function handleDecisionChange() {
        const approved = approveRadio && approveRadio.checked;
        const rejected = rejectRadio  && rejectRadio.checked;

        if (catatanWrapper)        catatanWrapper.style.display        = rejected  ? 'block' : 'none';
        if (catatanApproveWrapper) catatanApproveWrapper.style.display = approved  ? 'block' : 'none';

        // Lepas required dari keduanya
        if (catatanInput)        catatanInput.removeAttribute('required');
        if (catatanApproveInput) catatanApproveInput.removeAttribute('required');

        // Pasang required saat TOLAK
        if (rejected && catatanInput) catatanInput.setAttribute('required', 'required');

        // Update tombol
        if (btnVerify) {
            if (approved) {
                btnVerify.disabled = false;
                btnVerify.innerHTML = '<i class="fas fa-check-circle"></i> Setujui Laporan';
                btnVerify.className = 'btn-verify btn-verify-approve';
            } else if (rejected) {
                btnVerify.disabled = false;
                btnVerify.innerHTML = '<i class="fas fa-times-circle"></i> Tolak Laporan';
                btnVerify.className = 'btn-verify btn-verify-reject';
            } else {
                btnVerify.disabled = true;
                btnVerify.innerHTML = '<i class="fas fa-gavel"></i> Pilih Keputusan Dulu';
                btnVerify.className = 'btn-verify';
            }
        }
    }

    if (approveRadio) approveRadio.addEventListener('change', handleDecisionChange);
    if (rejectRadio)  rejectRadio.addEventListener('change', handleDecisionChange);

    // Trigger on load (jika old() value ada)
    handleDecisionChange();

    // Konfirmasi sebelum kirim
    const verifyForm = document.getElementById('verifyForm');
    if (verifyForm) {
        verifyForm.addEventListener('submit', function(e) {
            const isReject = rejectRadio && rejectRadio.checked;
            const catatan  = catatanInput ? catatanInput.value.trim() : '';

            if (isReject && catatan === '') {
                e.preventDefault();
                catatanInput.focus();
                catatanInput.style.borderColor = 'var(--danger)';
                return false;
            }

            const action = isReject ? 'MENOLAK' : 'MENYETUJUI';
            if (!confirm(`Apakah Anda yakin ingin ${action} laporan akhir siswa ini? Tindakan ini tidak dapat dibatalkan.`)) {
                e.preventDefault();
            }
        });
    }

    // ═══════════════════════════════════
    // LIVE SEARCH — List View
    // ═══════════════════════════════════
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const pendingCards = document.querySelectorAll('#pending .laporan-card');
    const historyRows = document.querySelectorAll('#history table tbody tr:not(#noResultsHistory)');
    const noResultsPending = document.getElementById('noResultsPending');
    const noResultsHistory = document.getElementById('noResultsHistory');

    // Auto-buka tab riwayat jika ada param ?tab=history atau ada filter periode aktif
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'history' || urlParams.get('periode')) {
        const historyTabBtn = document.getElementById('history-tab');
        if (historyTabBtn) {
            const tab = new bootstrap.Tab(historyTabBtn);
            tab.show();
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();

            // Filter Pending Cards
            let pendingMatchFound = false;
            pendingCards.forEach(card => {
                const text = card.innerText.toLowerCase();
                const isMatch = text.includes(searchTerm);
                card.style.display = isMatch ? 'flex' : 'none';
                if (isMatch) pendingMatchFound = true;
            });
            if (noResultsPending) {
                noResultsPending.style.display = (pendingMatchFound || searchTerm === '') ? 'none' : 'block';
            }

            // Filter History Table
            let historyMatchFound = false;
            historyRows.forEach(row => {
                if (row.querySelector('.td-siswa-name') === null) return;
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
