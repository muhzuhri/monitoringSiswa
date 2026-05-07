document.addEventListener('DOMContentLoaded', function() {
    // Live Search Logic (Header)
    const headerSearchInput = document.getElementById('headerSearchInput');
    const studentCards = document.querySelectorAll('#bimbingan .student-card');
    const noResultsBimbingan = document.getElementById('noResultsBimbingan');
    const headerSearchForm = document.getElementById('headerSearchForm');

    if (headerSearchInput) {
        headerSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            // Filter Bimbingan Cards
            let hasMatchBimbingan = false;
            studentCards.forEach(card => {
                const name = card.querySelector('.student-name').innerText.toLowerCase();
                const nisn = card.querySelector('.student-nisn').innerText.toLowerCase();
                const infoValues = Array.from(card.querySelectorAll('.info-value')).map(el => el.innerText.toLowerCase());
                
                const isMatch = name.includes(searchTerm) || 
                                nisn.includes(searchTerm) || 
                                infoValues.some(val => val.includes(searchTerm));

                if (isMatch) {
                    card.style.display = 'block';
                    hasMatchBimbingan = true;
                } else {
                    card.style.display = 'none';
                }
            });

            if (noResultsBimbingan) {
                noResultsBimbingan.style.display = (hasMatchBimbingan || searchTerm === '') ? 'none' : 'block';
            }

            // Filter History Rows
            const historyRows = document.querySelectorAll('#riwayat-history .history-row');
            historyRows.forEach(row => {
                const name = row.querySelector('.td-siswa-name').innerText.toLowerCase();
                const nisn = row.querySelector('.td-siswa-nisn').innerText.toLowerCase();
                const school = row.querySelector('.badge-school').innerText.toLowerCase();
                const company = row.querySelector('.text-muted').innerText.toLowerCase();

                const isMatch = name.includes(searchTerm) || 
                                nisn.includes(searchTerm) || 
                                school.includes(searchTerm) ||
                                company.includes(searchTerm);

                row.style.display = isMatch ? 'table-row' : 'none';
            });
        });

        headerSearchForm.addEventListener('submit', function(e) {
            e.preventDefault();
        });
    }


    // Auto-buka tab riwayat jika ada param ?tab=history atau ada filter periode aktif
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'history' || urlParams.get('periode')) {
        const historyTabBtn = document.getElementById('history-tab');
        if (historyTabBtn) {
            const tab = new bootstrap.Tab(historyTabBtn);
            tab.show();
        }
    }

    // ── PDF Preview Rendering Logic ────────────────────────────────────────
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

        // Check if it's an image
        const isImage = url.match(/\.(jpg|jpeg|png|gif)$/i);

        if (isImage) {
            const img = document.createElement('img');
            img.src = url;
            img.style.maxWidth = '100%';
            img.style.height = 'auto';
            img.style.borderRadius = '12px';
            img.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
            img.onload = () => {
                if (currentTaskId !== taskId) return;
                loadingEl.style.display = 'none';
                container.appendChild(img);
            };
            img.onerror = () => {
                if (currentTaskId !== taskId) return;
                loadingEl.style.display = 'none';
                errorEl.classList.remove('d-none');
            };
            return;
        }

        try {
            // Wait for modal transition
            await new Promise(resolve => setTimeout(resolve, 300));

            // Tambahkan header XMLHttpRequest agar Laravel tahu ini permintaan AJAX
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json, text/html, */*'
                }
            });
            
            const contentType = response.headers.get('content-type');
            
            // Periksa jika server mengembalikan JSON yang berisi error
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

            if (contentType && contentType.includes('text/html')) {
                // Tutup modal dan arahkan browser ke URL tersebut agar sesi toast dapat ditampilkan
                const inst = bootstrap.Modal.getInstance(document.getElementById('previewPdfModal'));
                if (inst) inst.hide();
                window.location.href = url;
                return;
            }

            const pdfDoc = await pdfjsLib.getDocument(url).promise;
            
            let containerWidth = container.clientWidth - 40;
            if (containerWidth <= 0) containerWidth = 800;

            const outputScale = window.devicePixelRatio || 1;

            for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
                const page = await pdfDoc.getPage(pageNum);
                const baseScale = containerWidth / page.getViewport({ scale: 1 }).width;
                const viewport = page.getViewport({ scale: baseScale * outputScale });
                
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                
                // Display size (CSS) should be translated back from scaled size
                canvas.style.width = (viewport.width / outputScale) + 'px';
                canvas.style.height = (viewport.height / outputScale) + 'px';
                
                canvas.style.display = 'block';
                canvas.style.margin = '0 auto 20px';
                canvas.style.maxWidth = '100%';
                canvas.style.borderRadius = '8px';
                canvas.style.boxShadow = '0 10px 30px rgba(0,0,0,0.15)';
                canvas.style.background = '#ffffff';
                
                container.appendChild(canvas);
                await page.render({ canvasContext: ctx, viewport }).promise;

                if (currentTaskId !== taskId) return;
                if (pageNum === 1) loadingEl.style.display = 'none';
            }
        } catch (err) {
            if (currentTaskId === taskId) {
                loadingEl.style.display = 'none';
                errorEl.classList.remove('d-none');
            }
            console.error('PDF rendering error:', err);
        }
    }

    function initPdfPreviewListeners() {
        document.querySelectorAll('.btn-preview-pdf').forEach(button => {
            button.onclick = function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                if (!url) return;
                
                currentPdfUrl = url;
                // Add download=1 to force attachment response, remove target=_blank in HTML already
                const downloadUrl = url + (url.includes('?') ? '&' : '?') + 'download=1';
                if (downloadBtn) {
                    downloadBtn.setAttribute('href', downloadUrl);
                    
                    let filename = 'Laporan_Siswa.pdf';
                    if (url.includes('jurnal')) filename = 'Rekap_Jurnal.pdf';
                    else if (url.includes('absensi')) filename = 'Rekap_Absensi.pdf';
                    else if (url.includes('kelompok')) filename = 'Rekap_Kelompok.pdf';
                    
                    downloadBtn.setAttribute('download', filename);
                }
                
                if (pdfModal) pdfModal.show();
                renderPDF(url);
            };
        });
    }

    if (pdfModalEl) {
        pdfModalEl.addEventListener('hidden.bs.modal', () => {
            const container = document.getElementById('pdfCanvasContainer');
            if (container) container.querySelectorAll('canvas, img').forEach(c => c.remove());
            currentPdfUrl = null;
        });
    }

    initPdfPreviewListeners();

    // Detail Siswa Logic
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

        // Surat Balasan
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
    }

    function initDetailListeners() {
        document.querySelectorAll('.btn-preview-pdf').forEach(btn => {
            btn.removeEventListener('click', handleDetailSiswa);
            btn.addEventListener('click', handleDetailSiswa);
        });
    }

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

    initDetailListeners();

    // Modal Group Members
    const groupModalElement = document.getElementById('groupMembersModal');
    const groupModalInstance = groupModalElement ? new bootstrap.Modal(groupModalElement) : null;
    const modalName = document.getElementById('modalGroupName');
    const modalBody = document.getElementById('modalGroupBody');

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        if (isNaN(date.getTime())) return dateStr;
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
    }

    document.querySelectorAll('.btn-show-members').forEach(button => {
        button.addEventListener('click', function() {
            const name = this.getAttribute('data-name');
            const members = JSON.parse(this.getAttribute('data-members'));
            const context = this.getAttribute('data-context');
            
            modalName.innerText = name;
            modalBody.innerHTML = '';
            
            const logbookRouteBase = this.getAttribute('data-logbook-route');
            const absensiRouteBase = this.getAttribute('data-absensi-route');
            const logbookDownloadBase = this.getAttribute('data-logbook-download');
            const absensiDownloadBase = this.getAttribute('data-absensi-download');

            members.forEach((member) => {
                const statusClass = member.absen_hari_ini ? 'status-hadir' : 'status-absen';
                const statusText = member.absen_hari_ini 
                    ? '<i class="fas fa-check-circle"></i> Hadir' 
                    : '<i class="fas fa-times-circle"></i> Belum Absen';
                
                // Construct Data Attributes for Detail Modal
                const detAttrs = `
                    data-nisn="${member.nisn}"
                    data-nama="${member.nama}"
                    data-email="${member.email || '-'}"
                    data-no_hp="${member.no_hp || '-'}"
                    data-jk="${member.jenis_kelamin || '-'}"
                    data-kelas="${member.kelas || '-'}"
                    data-jurusan="${member.jurusan || '-'}"
                    data-sekolah="${member.sekolah || '-'}"
                    data-npsn="${member.npsn || '-'}"
                    data-perusahaan="${member.perusahaan || '-'}"
                    data-tipe_magang="${member.tipe_magang || '-'}"
                    data-nisn_ketua="${member.nisn_ketua || '-'}"
                    data-surat_balasan="${member.surat_balasan || ''}"
                    data-tahun_ajaran="${(member.tahun_ajaran && member.tahun_ajaran.tahun_ajaran) ? member.tahun_ajaran.tahun_ajaran : '-'}"
                    data-mulai="${formatDate(member.tgl_mulai_magang)}"
                    data-selesai="${formatDate(member.tgl_selesai_magang)}"
                    data-guru-nama="${(member.guru && member.guru.nama) ? member.guru.nama : '-'}"
                    data-guru-nip="${(member.guru && member.guru.id_guru) ? member.guru.id_guru : '-'}"
                    data-guru-hp="${(member.guru && member.guru.no_hp) ? member.guru.no_hp : '-'}"
                    data-pl-nama="${(member.pembimbing && member.pembimbing.nama) ? member.pembimbing.nama : '-'}"
                    data-pl-nip="${member.id_pembimbing || '-'}"
                    data-pl-hp="${(member.pembimbing && member.pembimbing.no_telp) ? member.pembimbing.no_telp : '-'}"
                `;

                let actionButtons = '';
                if (context === 'history') {
                    const logDownload = logbookDownloadBase.replace(':nisn', member.nisn);
                    const absDownload = absensiDownloadBase.replace(':nisn', member.nisn);
                    actionButtons = `
                        <button class="btn-small btn-detail" title="Detail Profil" ${detAttrs} data-bs-toggle="modal" data-bs-target="#modalDetailSiswa" style="background: rgba(15, 23, 42, 0.04); color: #64748b; padding: 5px 8px; border-radius: 6px;">
                            <i class="fas fa-id-card"></i>
                        </button>
                        <button class="btn-small btn-preview-pdf" title="Cetak Jurnal" data-url="${logDownload}" style="background: #f0f9ff; color: #0369a1; padding: 5px 8px; border-radius: 6px; border: 1px solid #bae6fd;">
                            <i class="fas fa-book"></i>
                        </button>
                        <button class="btn-small btn-preview-pdf" title="Cetak Absensi" data-url="${absDownload}" style="background: #f0fdf4; color: #15803d; padding: 5px 8px; border-radius: 6px; border: 1px solid #bbf7d0;">
                            <i class="fas fa-file-signature"></i>
                        </button>
                    `;
                } else {
                    const logbookUrl = logbookRouteBase.replace(':nisn', member.nisn);
                    const absensiUrl = absensiRouteBase.replace(':nisn', member.nisn);
                    actionButtons = `
                        <button class="btn-small btn-detail" title="Detail Profil" ${detAttrs} data-bs-toggle="modal" data-bs-target="#modalDetailSiswa" style="background: rgba(15, 23, 42, 0.04); color: #64748b; padding: 5px 8px; border-radius: 6px;">
                            <i class="fas fa-id-card"></i>
                        </button>
                        <a href="${logbookUrl}" class="btn-small" title="Logbook" style="background: rgba(15, 23, 42, 0.04); color: #4f46e5; padding: 5px 8px; border-radius: 6px;">
                            <i class="fas fa-book"></i>
                        </a>
                        <a href="${absensiUrl}" class="btn-small" title="Absensi" style="background: rgba(15, 23, 42, 0.04); color: #4f46e5; padding: 5px 8px; border-radius: 6px;">
                            <i class="fas fa-calendar-check"></i>
                        </a>
                    `;
                }

                const row = `
                    <tr>
                        <td>
                            <div class="td-siswa-name fw-bold">${member.nama}</div>
                            <div class="td-siswa-nisn small text-muted">NISN: ${member.nisn}</div>
                        </td>
                        <td class="text-center">
                            <span class="status-badge ${statusClass}" style="padding: 4px 10px; border-radius: 50px; font-size: 0.75rem;">
                                ${statusText}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                ${actionButtons}
                            </div>
                        </td>
                    </tr>
                `;
                modalBody.innerHTML += row;
            });
            
            // Re-init listeners for new modal content
            initPdfPreviewListeners();
            initDetailListeners();
            
            if (groupModalInstance) groupModalInstance.show();
        });
    });

    // View Mode Switching Logic
    document.querySelectorAll('.view-mode-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.getAttribute('data-view');
            const target = this.getAttribute('data-target');
            const parent = this.closest('.tab-pane');
            
            parent.querySelectorAll('.view-mode-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            if (view === 'grouped') {
                parent.querySelector(`#${target}-grouped-view`).classList.remove('d-none');
                parent.querySelector(`#${target}-flat-view`).classList.add('d-none');
            } else {
                parent.querySelector(`#${target}-grouped-view`).classList.add('d-none');
                parent.querySelector(`#${target}-flat-view`).classList.remove('d-none');
            }
            localStorage.setItem(`view_mode_${target}`, view);
        });
    });

    // Init View Mode from localStorage
    ['bimbingan', 'history'].forEach(target => {
        const savedView = localStorage.getItem(`view_mode_${target}`);
        if (savedView) {
            const btn = document.querySelector(`.view-mode-btn[data-view="${savedView}"][data-target="${target}"]`);
            if (btn) btn.click();
        }
    });
});
