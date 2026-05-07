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
            const tab = new bootstrap.Tab(historyTabBtn);
            tab.show();
        }
    } else if (activeTab === 'kriteria') {
        const kriteriaTabBtn = document.getElementById('kriteria-tab');
        if (kriteriaTabBtn) {
            const tab = new bootstrap.Tab(kriteriaTabBtn);
            tab.show();
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
        form.action = `/guru/kriteria/${el.dataset.id}`;
        document.getElementById('edit_nama').value = el.dataset.nama;
        document.getElementById('edit_tipe').value = el.dataset.tipe;
        document.getElementById('edit_urutan').value = el.dataset.urutan;
    }
}


// PDF Preview Logic (Sama Persis dengan Daftar Siswa / Pembimbing)
document.addEventListener('DOMContentLoaded', function() {
    const pdfModalEl = document.getElementById('previewPdfModal');
    if (!pdfModalEl) return;
    
    const pdfModal = bootstrap.Modal.getOrCreateInstance(pdfModalEl);
    const downloadBtn = document.getElementById('downloadPdfBtn');
    
    let currentTaskId = 0;

    async function renderPDF(url) {
        const taskId = ++currentTaskId;
        const container = document.getElementById('pdfCanvasContainer');
        const loadingEl = document.getElementById('pdfLoadingIndicator');
        const errorEl = document.getElementById('pdfErrorMsg');

        if (!container) return;

        // Clear previous content
        container.querySelectorAll('canvas, img').forEach(c => c.remove());
        loadingEl.style.display = 'flex';
        errorEl.classList.add('d-none');
        container.scrollTop = 0;

        try {
            // Wait for modal transition
            await new Promise(resolve => setTimeout(resolve, 300));

            // Fetch with AJAX header to catch JSON error messages
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json, text/html, */*'
                }
            });
            
            const contentType = response.headers.get('content-type');
            
            // Handle expected JSON error responses
            if (!response.ok && contentType && contentType.includes('application/json')) {
                const data = await response.json();
                errorEl.innerHTML = `
                    <i class="fas fa-info-circle fa-3x mb-3 text-warning"></i>
                    <h5 class="text-white mt-2 mb-1">${data.message || 'Dokumen belum tersedia.'}</h5>
                    <p style="color: #94a3b8; font-size: 0.9rem;">Menunggu data penilaian diproses.</p>
                `;
                loadingEl.style.display = 'none';
                errorEl.classList.remove('d-none');
                return;
            }

            // PDF Logic
            const pdfDoc = await pdfjsLib.getDocument(url).promise;
            if (taskId !== currentTaskId) return;

            loadingEl.style.display = 'none';

            let containerWidth = container.clientWidth - 40;
            if (containerWidth <= 0) containerWidth = 800;

            const outputScale = window.devicePixelRatio || 1;

            for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
                const page = await pdfDoc.getPage(pageNum);
                const baseScale = containerWidth / page.getViewport({ scale: 1 }).width;
                const viewport = page.getViewport({ scale: baseScale * outputScale });

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                canvas.style.width = (viewport.width / outputScale) + 'px';
                canvas.style.height = (viewport.height / outputScale) + 'px';
                
                canvas.className = 'pdf-page-canvas shadow-sm mb-4 mx-auto d-block';
                canvas.style.maxWidth = '100%';
                canvas.style.borderRadius = '8px';
                canvas.style.background = '#fff';

                container.appendChild(canvas);

                await page.render({ canvasContext: context, viewport: viewport }).promise;
            }
        } catch (err) {
            if (taskId === currentTaskId) {
                console.error('PDF Load Error:', err);
                loadingEl.style.display = 'none';
                errorEl.classList.remove('d-none');
                errorEl.innerHTML = `
                    <i class="fas fa-exclamation-triangle fa-3x mb-3 text-danger"></i>
                    <h5 class="text-white">Gagal Memuat Dokumen</h5>
                    <p class="text-muted small">Terjadi kesalahan teknis saat mengambil file.</p>
                `;
            }
        }
    }

    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-preview-pdf');
        if (btn) {
            e.preventDefault();
            const url = btn.dataset.url;
            if (!url) return;

            const downloadUrl = url + (url.includes('?') ? '&' : '?') + 'download=1';
            const filename = btn.dataset.filename || 'Penilaian_Siswa.pdf';

            if (downloadBtn) {
                downloadBtn.setAttribute('href', downloadUrl);
                downloadBtn.setAttribute('download', filename);
            }

            pdfModal.show();
            renderPDF(url);
        }
    });

    pdfModalEl.addEventListener('hidden.bs.modal', () => {
        const container = document.getElementById('pdfCanvasContainer');
        if (container) container.querySelectorAll('canvas, img').forEach(c => c.remove());
        currentTaskId++; // Cancel any running render task
    });
});
