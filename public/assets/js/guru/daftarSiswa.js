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

    // Debounced NPSN Search
    const npsnInput = document.getElementById('npsnInput');
    const npsnSearchForm = document.getElementById('npsnSearchForm');
    let npsnTimeout = null;

    if (npsnInput && npsnSearchForm) {
        npsnInput.addEventListener('input', function() {
            clearTimeout(npsnTimeout);
            npsnTimeout = setTimeout(() => {
                npsnSearchForm.submit();
            }, 500);
        });
        
        // Keep focus if NPSN is searched
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('npsn')) {
            // Activate search tab if NPSN was searched
            const searchTabBtn = document.getElementById('search-tab');
            if (searchTabBtn) {
                const tab = new bootstrap.Tab(searchTabBtn);
                tab.show();
                
                setTimeout(() => {
                    npsnInput.focus();
                    const val = npsnInput.value;
                    npsnInput.value = '';
                    npsnInput.value = val;
                }, 200);
            }
        }
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

    // PDF Preview Logic
    const pdfModalElement = document.getElementById('previewPdfModal');
    const pdfModalStatus = pdfModalElement ? new bootstrap.Modal(pdfModalElement) : null;
    const pdfIframe = document.getElementById('pdfIframe');
    const downloadPdfBtn = document.getElementById('downloadPdfBtn');
    const printPdfBtn = document.getElementById('printPdfBtn');

    function handlePdfPreview() {
        const url = this.getAttribute('data-url');
        if (url) {
            const previewUrl = url.includes('#') ? url : url + '#view=Fit';
            pdfIframe.src = previewUrl;
            
            const downloadUrl = url.includes('?') ? url + '&download=1' : url + '?download=1';
            if (downloadPdfBtn) downloadPdfBtn.href = downloadUrl;
            if (pdfModalStatus) pdfModalStatus.show();
        }
    }

    function initPdfPreviewListeners() {
        const previewButtons = document.querySelectorAll('.btn-preview-pdf');
        previewButtons.forEach(button => {
            button.removeEventListener('click', handlePdfPreview);
            button.addEventListener('click', handlePdfPreview);
        });
    }

    if (pdfModalElement) {
        const triggerPrint = function() {
            if (pdfIframe) {
                pdfIframe.contentWindow.focus();
                pdfIframe.contentWindow.print();
            }
        };

        if (printPdfBtn) printPdfBtn.addEventListener('click', triggerPrint);

        pdfModalElement.addEventListener('hidden.bs.modal', function() {
            pdfIframe.src = '';
        });
    }

    // Init listeners for initial content
    initPdfPreviewListeners();

    // Modal Group Members
    const groupModalElement = document.getElementById('groupMembersModal');
    const groupModalInstance = groupModalElement ? new bootstrap.Modal(groupModalElement) : null;
    const modalName = document.getElementById('modalGroupName');
    const modalBody = document.getElementById('modalGroupBody');

    document.querySelectorAll('.btn-show-members').forEach(button => {
        button.addEventListener('click', function() {
            const name = this.getAttribute('data-name');
            const members = JSON.parse(this.getAttribute('data-members'));
            const context = this.getAttribute('data-context');
            
            modalName.innerText = name;
            modalBody.innerHTML = '';
            
            // Route patterns are now handled via data attributes or relative paths if possible, 
            // but since they were in Blade, we might need a global config or data- attributes.
            // Let's assume the data-members or buttons have them or we use a common structure.
            
            // To keep it clean, we'll look for hidden templates or data- attributes.
            // For now, I'll use the patterns from the blade but they need to be passed.
            // Actually, I can use the global `window.routes` if defined, but I'll stick to 
            // data attributes on the trigger button for the patterns.
            
            const logbookRouteBase = this.getAttribute('data-logbook-route');
            const absensiRouteBase = this.getAttribute('data-absensi-route');
            const logbookDownloadBase = this.getAttribute('data-logbook-download');
            const absensiDownloadBase = this.getAttribute('data-absensi-download');

            members.forEach((member) => {
                const statusClass = member.absen_hari_ini ? 'status-hadir' : 'status-absen';
                const statusText = member.absen_hari_ini 
                    ? '<i class="fas fa-check-circle"></i> Hadir' 
                    : '<i class="fas fa-times-circle"></i> Belum Absen';
                
                let actionButtons = '';
                if (context === 'history') {
                    const logDownload = logbookDownloadBase.replace(':nisn', member.nisn);
                    const absDownload = absensiDownloadBase.replace(':nisn', member.nisn);
                    actionButtons = `
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
