document.addEventListener('DOMContentLoaded', function() {
    // ── Tab Switching Logic ───────────────────────────────────────────────
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

    // Auto-open history tab if param tab=history is present
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'history' || urlParams.get('periode')) {
        activateTab('history-students');
    }

    // ── Search Logic ──────────────────────────────────────────────────────
    const searchInput = document.getElementById('searchInput');
    const historyRows = document.querySelectorAll('#historyTableBody .student-row');
    const activeRows = document.querySelectorAll('#activeTableBody .student-row');
    const noResultsActive = document.getElementById('noResultsActive');
    const noResultsHistory = document.getElementById('noResultsHistory');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            // Filter Active Table
            let matchActive = false;
            activeRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const isMatch = text.includes(searchTerm);
                row.style.display = isMatch ? 'table-row' : 'none';
                if (isMatch) matchActive = true;
            });
            if (noResultsActive) noResultsActive.style.display = (matchActive || searchTerm === '') ? 'none' : 'table-row';

            // Filter History Table
            let matchHistory = false;
            historyRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const isMatch = text.includes(searchTerm);
                row.style.display = isMatch ? 'table-row' : 'none';
                if (isMatch) matchHistory = true;
            });
            if (noResultsHistory) noResultsHistory.style.display = (matchHistory || searchTerm === '') ? 'none' : 'table-row';
        });
    }

    // ── PDF Preview Logic (Unified with Guru) ──────────────────────────────
    const pdfModalEl = document.getElementById('previewPdfModal');
    const pdfModal = pdfModalEl ? bootstrap.Modal.getOrCreateInstance(pdfModalEl) : null;
    const downloadBtn = document.getElementById('downloadPdfBtn');
    
    let currentPdfUrl = null;
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
                    <p style="color: #94a3b8; font-size: 0.9rem;">Menunggu pihak terkait untuk melakukan input data.</p>
                `;
                loadingEl.style.display = 'none';
                errorEl.classList.remove('d-none');
                return;
            }

            // If HTML returned, it might be a redirect or error page, redirect parent
            if (contentType && contentType.includes('text/html')) {
                window.location.href = url;
                return;
            }

            // Handle images
            if (contentType && contentType.includes('image')) {
                const img = document.createElement('img');
                img.src = url;
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                img.style.borderRadius = '12px';
                img.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
                img.onload = () => {
                    if (taskId === currentTaskId) {
                        loadingEl.style.display = 'none';
                        container.appendChild(img);
                    }
                };
                return;
            }

            // PDF Logic
            const pdfDoc = await pdfjsLib.getDocument(url).promise;
            if (taskId !== currentTaskId) return;

            loadingEl.style.display = 'none';

            let containerWidth = container.clientWidth - 40;
            if (containerWidth <= 0) containerWidth = 800;

            // Use device pixel ratio for sharper rendering
            const outputScale = window.devicePixelRatio || 1;

            for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
                const page = await pdfDoc.getPage(pageNum);
                
                // Calculate scale to fit container width
                const baseScale = containerWidth / page.getViewport({ scale: 1 }).width;
                const viewport = page.getViewport({ scale: baseScale * outputScale });

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                // Display size (CSS) should be translated back from scaled size
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

    function initPdfPreviewListeners() {
        document.querySelectorAll('.btn-preview-pdf[data-url]').forEach(btn => {
            btn.onclick = function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                if (!url) return;
                
                currentPdfUrl = url;
                const downloadUrl = url + (url.includes('?') ? '&' : '?') + 'download=1';
                if (downloadBtn) {
                    downloadBtn.setAttribute('href', downloadUrl);
                    
                    let filename = 'Dokumen_Siswa.pdf';
                    if (url.includes('jurnal')) filename = 'Rekap_Jurnal.pdf';
                    else if (url.includes('absensi')) filename = 'Rekap_Absensi.pdf';
                    else if (url.includes('penilaian')) filename = 'Penilaian_Siswa.pdf';
                    else if (url.includes('laporan')) filename = 'Laporan_Akhir.pdf';
                    else if (url.includes('sertifikat')) filename = 'Sertifikat_Magang.pdf';
                    
                    downloadBtn.setAttribute('download', filename);
                }
                
                if (pdfModal) pdfModal.show();
                renderPDF(url);
            };
        });
    }

    // ── Detail Siswa Logic ────────────────────────────────────────────────
    function handleDetailSiswa(e) {
        e.preventDefault();
        const btn = e.currentTarget;
        const d = btn.dataset;
        
        console.log("Loading student detail:", d);
        
        document.getElementById('det_name').innerText = d.nama || '-';
        document.getElementById('det_nisn').innerText = d.nisn || '-';
        document.getElementById('det_jk').innerText = d.jk === 'L' ? 'Laki-laki' : (d.jk === 'P' ? 'Perempuan' : '-');
        document.getElementById('det_hp').innerText = d.no_hp || '-';
        document.getElementById('det_email').innerText = d.email || '-';
        document.getElementById('det_kelas_jurusan').innerText = `${d.kelas || '-'} ${d.jurusan || ''}`;
        document.getElementById('det_sekolah').innerText = d.sekolah || '-';
        document.getElementById('det_npsn').innerText = d.npsn || '-';
        document.getElementById('det_perusahaan').innerText = d.perusahaan || '-';
        document.getElementById('det_tipe_magang').innerText = d.tipe_magang || '-';
        document.getElementById('det_nisn_ketua').innerText = d.nisn_ketua || '-';
        document.getElementById('det_periode').innerText = `${d.mulai || '-'} s/d ${d.selesai || '-'}`;
        document.getElementById('det_tahun_ajaran').innerText = d.tahun_ajaran || '-';
        
        // Guru & PL
        document.getElementById('det_guru_nama').innerText = d.guruNama || '-';
        document.getElementById('det_guru_nip').innerText = d.guruNip || '-';
        document.getElementById('det_pl_nama').innerText = d.plNama || '-';
        document.getElementById('det_pl_nip').innerText = d.plNip || '-';

        // WhatsApp Buttons
        const formatWa = (num) => {
            let cleaned = num.replace(/\D/g, '');
            return cleaned.startsWith('0') ? '62' + cleaned.substring(1) : cleaned;
        };

        const guruWaBtn = document.getElementById('det_guru_wa_btn');
        if(d.guruHp && d.guruHp !== '-') {
            guruWaBtn.href = `https://wa.me/${formatWa(d.guruHp)}`;
            guruWaBtn.classList.remove('d-none');
        } else {
            guruWaBtn.classList.add('d-none');
        }

        const plWaBtn = document.getElementById('det_pl_wa_btn');
        if(d.plHp && d.plHp !== '-') {
            plWaBtn.href = `https://wa.me/${formatWa(d.plHp)}`;
            plWaBtn.classList.remove('d-none');
        } else {
            plWaBtn.classList.add('d-none');
        }

        // Surat Balasan Logic
        const suratBalasanText = document.getElementById('det_surat_balasan');
        const viewSuratBtn = document.getElementById('btn_view_surat');
        if(d.surat_balasan && d.surat_balasan !== 'null' && d.surat_balasan !== '') {
            suratBalasanText.classList.add('d-none');
            viewSuratBtn.classList.remove('d-none');
            viewSuratBtn.setAttribute('data-url', `/storage/${d.surat_balasan}`);
        } else {
            suratBalasanText.classList.remove('d-none');
            suratBalasanText.innerText = 'Belum diunggah';
            viewSuratBtn.classList.add('d-none');
        }

        // Avatar Logic
        const avatarPlaceholder = document.getElementById('det_avatar_placeholder');
        const avatarImg = document.getElementById('det_avatar_img');
        
        if (avatarImg) {
            // If the backend provided a profile photo link, we'd use it here.
            // Since it's not currently in the dataset, we stick to placeholder.
            avatarImg.style.display = 'none';
            avatarPlaceholder.style.display = 'flex';
        }
        
        if (avatarPlaceholder) {
            avatarPlaceholder.innerText = (d.nama || 'S').charAt(0).toUpperCase();
        }
    }

    function initDetailListeners() {
        document.querySelectorAll('.btn-preview-pdf[data-bs-target="#modalDetailSiswa"]').forEach(btn => {
            btn.addEventListener('click', handleDetailSiswa);
        });

        // Preview Surat Balasan inside Detail Modal
        const btnViewSurat = document.getElementById('btn_view_surat');
        if (btnViewSurat) {
            btnViewSurat.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                if (url) {
                    if (downloadBtn) {
                        const downloadUrl = url + (url.includes('?') ? '&' : '?') + 'download=1';
                        downloadBtn.setAttribute('href', downloadUrl);
                        const namaSiswa = document.getElementById('det_name').innerText || 'Siswa';
                        downloadBtn.setAttribute('download', `Surat_Balasan_${namaSiswa.replace(/\s+/g, '_')}.pdf`);
                    }
                    if (pdfModal) pdfModal.show();
                    renderPDF(url);
                }
            });
        }
    }

    // Modal Cleanup
    if (pdfModalEl) {
        pdfModalEl.addEventListener('hidden.bs.modal', () => {
            const container = document.getElementById('pdfCanvasContainer');
            if (container) container.querySelectorAll('canvas, img').forEach(c => c.remove());
            currentPdfUrl = null;
        });
    }

    initPdfPreviewListeners();
    initDetailListeners();
});
