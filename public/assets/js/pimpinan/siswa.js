document.addEventListener("DOMContentLoaded", function () {
    // ── Tab Persistence ───────────────────────────────────────────
    const activeTab = localStorage.getItem("activeStudentTab_Pimpinan");
    if (activeTab) {
        const tabEl = document.querySelector(
            `button[data-bs-target="${activeTab}"]`,
        );
        if (tabEl) {
            bootstrap.Tab.getInstance(tabEl)?.show() ||
                new bootstrap.Tab(tabEl).show();
        }
    }
    document.querySelectorAll(".tab-button").forEach((btn) => {
        btn.addEventListener("shown.bs.tab", (event) => {
            localStorage.setItem(
                "activeStudentTab_Pimpinan",
                event.target.dataset.bsTarget,
            );
        });
    });

    // ── View Mode Switching ────────────────────────────────────────
    function initViewMode() {
        document.querySelectorAll(".view-mode-btn").forEach((btn) => {
            const target = btn.dataset.target;
            const view = btn.dataset.view;
            const savedView = localStorage.getItem(
                `viewMode_${target}_Pimpinan`,
            );

            if (savedView === view) {
                btn.click();
            }

            btn.addEventListener("click", function () {
                const v = this.dataset.view;
                const t = this.dataset.target;
                const pane = this.closest(".tab-pane");

                pane.querySelectorAll(".view-mode-btn").forEach((b) =>
                    b.classList.remove("active"),
                );
                this.classList.add("active");

                const grouped = pane.querySelector(`#${t}-grouped-view`);
                const flat = pane.querySelector(`#${t}-flat-view`);

                if (v === "grouped") {
                    grouped.classList.remove("d-none");
                    flat.classList.add("d-none");
                } else {
                    grouped.classList.add("d-none");
                    flat.classList.remove("d-none");
                }
                localStorage.setItem(`viewMode_${t}_Pimpinan`, v);
            });
        });
    }
    initViewMode();

    // ── Detail Modal Handler (Using Event Delegation for Dynamic Buttons) ──
    document.addEventListener("click", function (e) {
        const button = e.target.closest(".btn-detail");
        if (!button) return;

        const data = button.dataset;
        const setVal = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val || "-";
        };

        setVal("s_det_nama", data.nama);
        setVal("s_det_nisn", data.nisn);
        setVal("s_det_jk", data.jk);
        setVal("s_det_email", data.email);
        setVal("s_det_hp", data.no_hp);
        setVal("s_det_kelas_jurusan", `${data.kelas} - ${data.jurusan}`);
        setVal("s_det_tahun_ajaran", data.tahunAjaran || data.tahun_ajaran);
        setVal("s_det_sekolah", data.sekolah);
        setVal("s_det_npsn", data.npsn);
        setVal("s_det_perusahaan", data.perusahaan || "Belum ditugaskan");
        setVal("s_det_tipe_magang", (data.tipeMagang || data.tipe_magang || "individu").toUpperCase());
        setVal("s_det_nisn_ketua", data.nisnKetua || data.nisn_ketua);

        const mulai = data.mulai;
        const selesai = data.selesai;
        setVal("s_det_periode", (mulai && selesai && mulai !== "-") ? `${mulai} s/d ${selesai}` : "Belum ditentukan");

        setVal("s_det_guru_nama", data.guruNama);
        setVal("s_det_guru_nip", data.guruNip);
        
        const guruHp = data.guruHp || '';
        const guruWaBtn = document.getElementById('s_det_guru_wa_btn');
        const formatWa = (num) => {
            let cleaned = num.replace(/\D/g, '');
            if (cleaned.startsWith('0')) cleaned = '62' + cleaned.slice(1);
            return cleaned;
        };

        if (guruWaBtn) {
            if (guruHp && guruHp !== '-') {
                guruWaBtn.href = `https://wa.me/${formatWa(guruHp)}`;
                guruWaBtn.classList.remove('d-none');
            } else {
                guruWaBtn.classList.add('d-none');
            }
        }

        setVal("s_det_pl_nama", data.plNama);
        setVal("s_det_pl_nip", data.plNip);
        
        const plHp = data.plHp || '';
        const plWaBtn = document.getElementById('s_det_pl_wa_btn');
        if (plWaBtn) {
            if (plHp && plHp !== '-') {
                plWaBtn.href = `https://wa.me/${formatWa(plHp)}`;
                plWaBtn.classList.remove('d-none');
            } else {
                plWaBtn.classList.add('d-none');
            }
        }

        // Handle Surat Balasan Display
        const suratPath = data.surat_balasan;
        const detSurat = document.getElementById('s_det_surat_balasan');
        const btnViewSurat = document.getElementById('s_btn_view_surat');

        if (suratPath && suratPath !== 'null' && suratPath !== '' && detSurat && btnViewSurat) {
            detSurat.textContent = 'File tersedia';
            detSurat.className = 'detail-value text-success fw-bold';
            btnViewSurat.classList.remove('d-none');
            btnViewSurat.onclick = function() {
                window.openPdfPreview(`/storage/${suratPath}`);
            };
        } else if (detSurat && btnViewSurat) {
            detSurat.textContent = 'Belum diunggah';
            detSurat.className = 'detail-value text-muted italic';
            btnViewSurat.classList.add('d-none');
        }
    });

    // ── Modal Group Members (Riwayat) ─────────────────────────────
    const groupModalEl = document.getElementById("groupMembersModal");
    const groupModal = groupModalEl ? new bootstrap.Modal(groupModalEl) : null;
    const modalNameEl = document.getElementById("modalGroupName");
    const modalBodyEl = document.getElementById("modalGroupBody");

    document.addEventListener("click", function(e) {
        const button = e.target.closest(".btn-show-members");
        if(!button) return;

        const name = button.dataset.name;
        const members = JSON.parse(button.dataset.members);
        const type = button.getAttribute("data-type") || "active";

        if(modalNameEl) modalNameEl.innerText = name;
        if(modalBodyEl) {
            modalBodyEl.innerHTML = "";

            const container = document.getElementById("siswa-container");
            const logDownloadBase = container ? container.dataset.jurnalUrl : "";
            const absDownloadBase = container ? container.dataset.absensiUrl : "";

            members.forEach((member) => {
                const statusBadge = (member.status === 'selesai' || type === 'history') ? `
                    <span class="badge bg-secondary-soft text-muted px-2 py-1 small rounded-pill">
                        <i class="fas fa-archive me-1"></i> SELESAI
                    </span>
                ` : `
                    <span class="badge bg-success-soft text-success px-2 py-1 small rounded-pill">
                        <i class="fas fa-check-circle me-1"></i> AKTIF
                    </span>
                `;

                const row = `
                    <tr>
                        <td class="p-3">
                            <div class="fw-bold text-dark">${member.nama}</div>
                            <div class="text-muted small">NISN: ${member.nisn}</div>
                        </td>
                        <td class="text-center p-3">
                            ${statusBadge}
                        </td>
                        <td class="text-end p-3">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn-premium-circle btn-view-p btn-detail"
                                    data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                    data-nisn="${member.nisn}" 
                                    data-nama="${member.nama}" 
                                    data-email="${member.email}"
                                    data-no_hp="${member.no_hp || '-'}" 
                                    data-jk="${member.jk || '-'}"
                                    data-kelas="${member.kelas}" 
                                    data-jurusan="${member.jurusan}"
                                    data-sekolah="${member.sekolah}" 
                                    data-npsn="${member.npsn || '-'}"
                                    data-perusahaan="${member.perusahaan || '-'}"
                                    data-tipe_magang="${member.tipe_magang || 'individu'}"
                                    data-nisn_ketua="${member.nisn_ketua || '-'}"
                                    data-surat_balasan="${member.surat_balasan || ''}"
                                    data-tahun_ajaran="${member.tahun_ajaran || '-'}"
                                    data-mulai="${member.mulai}" 
                                    data-selesai="${member.selesai}"
                                    data-guru-nama="${member.guru_nama}" 
                                    data-guru-nip="${member.guru_nip}"
                                    data-guru-hp="${member.guru_hp}"
                                    data-pl-nama="${member.pl_nama}" 
                                    data-pl-nip="${member.pl_nip}"
                                    data-pl-hp="${member.pl_hp}"
                                    title="Lihat Detail Profil">
                                    <i class="fas fa-id-card"></i>
                                </button>
                                ${type === 'history' ? `
                                    <button class="btn-premium-circle btn-jurnal-p btn-preview-pdf" 
                                        data-url="${logDownloadBase.replace(':nisn', member.nisn)}"
                                        title="Rekap Jurnal">
                                        <i class="fas fa-book-open"></i>
                                    </button>
                                    <button class="btn-premium-circle btn-absensi-p btn-preview-pdf" 
                                        data-url="${absDownloadBase.replace(':nisn', member.nisn)}"
                                        title="Rekap Presensi">
                                        <i class="fas fa-calendar-check"></i>
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
                modalBodyEl.innerHTML += row;
            });
        }

        initPdfPreviewListeners();
        if (groupModal) groupModal.show();
    });

    // ── PDF Preview ────────────────────────────────────────────────
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
    }

    let currentPdfUrl = null;
    let currentTaskId = 0;

    async function renderPDF(url) {
        const taskId = ++currentTaskId;
        const container = document.getElementById("pdfCanvasContainer");
        const loadingEl = document.getElementById("pdfLoadingIndicator");
        const errorEl = document.getElementById("pdfErrorMsg");

        if (!container || !loadingEl || !errorEl) return;

        container.querySelectorAll("canvas, img").forEach((c) => c.remove());
        loadingEl.style.display = "flex";
        errorEl.style.display = "none";
        container.scrollTop = 0;

        // Check if image
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
                errorEl.style.display = "block";
            };
            return;
        }

        try {
            const pdfDoc = await pdfjsLib.getDocument(url).promise;
            loadingEl.style.display = "none";

            const containerWidth = (container.clientWidth > 0 ? container.clientWidth : 800) - 40;
            const outputScale = window.devicePixelRatio || 2; 

            for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
                if (currentTaskId !== taskId) return;
                
                const page = await pdfDoc.getPage(pageNum);
                const baseViewport = page.getViewport({ scale: 1.0 });
                const displayScale = containerWidth / baseViewport.width;
                const displayViewport = page.getViewport({ scale: displayScale });
                const renderViewport = page.getViewport({ scale: displayScale * outputScale });

                const canvas = document.createElement("canvas");
                const context = canvas.getContext("2d");
                
                canvas.width = Math.floor(renderViewport.width);
                canvas.height = Math.floor(renderViewport.height);
                
                canvas.style.width = Math.floor(displayViewport.width) + "px";
                canvas.style.height = Math.floor(displayViewport.height) + "px";
                canvas.style.marginBottom = "20px";
                canvas.style.borderRadius = "8px";
                canvas.style.boxShadow = "0 4px 12px rgba(0,0,0,0.2)";

                await page.render({
                    canvasContext: context,
                    viewport: renderViewport,
                }).promise;
                container.appendChild(canvas);
            }
        } catch (error) {
            if (currentTaskId === taskId) {
                console.error("Error loading PDF:", error);
                loadingEl.style.display = "none";
                errorEl.style.display = "block";
            }
        }
    }

    const pdfModalEl = document.getElementById("previewPdfModal");
    const pdfModal = pdfModalEl ? new bootstrap.Modal(pdfModalEl) : null;
    const downloadBtn = document.getElementById("downloadPdfBtn");
    const printBtn = document.getElementById("printPdfBtn");

    function initPdfPreviewListeners() {
        document.querySelectorAll(".btn-preview-pdf").forEach((button) => {
            button.onclick = null;
            button.addEventListener("click", function () {
                const url = this.dataset.url;
                if (!url) return;
                currentPdfUrl = url;
                
                if (downloadBtn) {
                    downloadBtn.href = url + (url.includes("?") ? "&" : "?") + "download=1";
                }
                
                if (pdfModal) {
                    pdfModal.show();
                    const handler = () => {
                        renderPDF(url);
                        pdfModalEl.removeEventListener('shown.bs.modal', handler);
                    };
                    pdfModalEl.addEventListener('shown.bs.modal', handler);
                }
            });
        });
    }

    if (printBtn) {
        printBtn.addEventListener('click', () => {
            if (!currentPdfUrl) return;
            const win = window.open(currentPdfUrl, '_blank');
            win.addEventListener('load', () => win.print(), { once: true });
        });
    }

    if (pdfModalEl) {
        pdfModalEl.addEventListener('hidden.bs.modal', () => {
            const container = document.getElementById("pdfCanvasContainer");
            if (container) container.querySelectorAll("canvas, img").forEach(c => c.remove());
            currentPdfUrl = null;
        });
    }

    // Also update the onclick in detail modal
    window.openPdfPreview = function(url) {
        currentPdfUrl = url;
        if (downloadBtn) downloadBtn.href = url + (url.includes("?") ? "&" : "?") + "download=1";
        if (pdfModal) {
            pdfModal.show();
            const handler = () => {
                renderPDF(url);
                pdfModalEl.removeEventListener('shown.bs.modal', handler);
            };
            pdfModalEl.addEventListener('shown.bs.modal', handler);
        }
    };
    initPdfPreviewListeners();
});
