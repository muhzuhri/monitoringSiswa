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

        setVal("det_name", data.nama);
        setVal("det_nisn", data.nisn);
        setVal("det_email", data.email);
        setVal("det_hp", data.no_hp);
        setVal("det_kelas_jurusan", `${data.kelas} - ${data.jurusan}`);
        setVal("det_sekolah", data.sekolah);
        setVal("det_perusahaan", data.perusahaan || "Belum ditugaskan");

        const mulai = data.mulai;
        const selesai = data.selesai;
        setVal("det_periode", (mulai && selesai && mulai !== "-") ? `${mulai} s/d ${selesai}` : "Belum ditentukan");

        setVal("det_guru_nama", data.guruNama);
        setVal("det_guru_nip", data.guruNip);
        setVal("det_pl_nama", data.plNama);
        setVal("det_pl_nip", data.plNip);
        setVal("det_pl_hp", data.plHp);
    });

    // ── Modal Group Members (Riwayat) ─────────────────────────────
    const groupModalEl = document.getElementById("groupMembersModal");
    const groupModal = groupModalEl ? new bootstrap.Modal(groupModalEl) : null;
    const modalNameEl = document.getElementById("modalGroupName");
    const modalBodyEl = document.getElementById("modalGroupBody");

    document.querySelectorAll(".btn-show-members").forEach((button) => {
        button.addEventListener("click", function () {
            const name = this.dataset.name;
            const members = JSON.parse(this.dataset.members);
            const type = this.getAttribute("data-type") || "active";

            modalNameEl.innerText = name;
            modalBodyEl.innerHTML = "";

            // Route patterns (Admin routes since they provide reports for Pimpinan)
            const container = document.getElementById("siswa-container");
            const logDownloadBase = container ? container.dataset.jurnalUrl : "";
            const absDownloadBase = container ? container.dataset.absensiUrl : "";

            members.forEach((member) => {
                const statusBadge = member.status === 'selesai' || type === 'history' ? `
                            <span class="badge bg-secondary-light text-muted px-2 py-1 small rounded-pill" style="font-size: 0.65rem;">
                                <i class="fas fa-flag-checkered me-1"></i> SELESAI
                            </span>
                        ` : `
                            <span class="badge bg-success-light text-success px-2 py-1 small rounded-pill" style="font-size: 0.65rem;">
                                <i class="fas fa-check-circle me-1"></i> AKTIF
                            </span>
                        `;

                const row = `
                            <tr>
                                <td>
                                    <div class="cell-name mb-0" style="font-size: 0.95rem;">${member.nama}</div>
                                    <div class="cell-sub small text-muted">NISN: ${member.nisn}</div>
                                </td>
                                <td class="text-center">
                                    ${statusBadge}
                                </td>
                                <td class="text-end">
                                    <button class="btn-icon btn-detail-soft btn-detail"
                                        data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                        data-nisn="${member.nisn}" data-nama="${member.nama}" data-email="${member.email}"
                                        data-no_hp="${member.no_hp || '-'}" data-kelas="${member.kelas}" data-jurusan="${member.jurusan}"
                                        data-sekolah="${member.sekolah}" data-perusahaan="${member.perusahaan || '-'}"
                                        data-mulai="${member.mulai}" data-selesai="${member.selesai}"
                                        data-guru-nama="${member.guru_nama}" data-guru-nip="${member.guru_nip}"
                                        data-pl-nama="${member.pl_nama}" data-pl-nip="${member.pl_nip}"
                                        data-pl-hp="${member.pl_hp}"
                                        title="Lihat Detail Profil">
                                        <i class="fas fa-id-card text-primary"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                modalBodyEl.innerHTML += row;
            });

            initPdfPreviewListeners();
            if (groupModal) groupModal.show();
        });
    });

    // ── PDF Preview ────────────────────────────────────────────────
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
    }

    async function renderPDF(url) {
        const container = document.getElementById("pdfCanvasContainer");
        const loadingEl = document.getElementById("pdfLoadingIndicator");
        const errorEl = document.getElementById("pdfErrorMsg");

        if (!container || !loadingEl || !errorEl) return;

        container.querySelectorAll("canvas").forEach((c) => c.remove());
        loadingEl.style.display = "flex";
        errorEl.style.display = "none";
        container.scrollTop = 0;

        try {
            const pdfDoc = await pdfjsLib.getDocument(url).promise;
            loadingEl.style.display = "none";

            const containerWidth = container.clientWidth - 40;
            const outputScale = window.devicePixelRatio || 2; // High-res rendering

            for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
                const page = await pdfDoc.getPage(pageNum);
                
                // Calculate original display size
                const baseViewport = page.getViewport({ scale: 1.0 });
                const displayScale = containerWidth / baseViewport.width;
                const displayViewport = page.getViewport({ scale: displayScale });

                // Render at higher resolution
                const renderViewport = page.getViewport({ scale: displayScale * outputScale });

                const canvas = document.createElement("canvas");
                const context = canvas.getContext("2d");
                
                canvas.width = Math.floor(renderViewport.width);
                canvas.height = Math.floor(renderViewport.height);
                
                // Scale back down via CSS for sharpness
                canvas.style.width = Math.floor(displayViewport.width) + "px";
                canvas.style.height = Math.floor(displayViewport.height) + "px";

                await page.render({
                    canvasContext: context,
                    viewport: renderViewport,
                }).promise;
                container.appendChild(canvas);
            }
        } catch (error) {
            console.error("Error loading PDF:", error);
            loadingEl.style.display = "none";
            errorEl.style.display = "block";
        }
    }

    const pdfModalEl = document.getElementById("previewPdfModal");
    const pdfModal = pdfModalEl ? new bootstrap.Modal(pdfModalEl) : null;
    const downloadBtn = document.getElementById("downloadPdfBtn");

    function initPdfPreviewListeners() {
        document.querySelectorAll(".btn-preview-pdf").forEach((button) => {
            button.onclick = null;
            button.addEventListener("click", function () {
                const url = this.dataset.url;
                if (!url) return;
                
                if (downloadBtn) {
                    downloadBtn.href = url + (url.includes("?") ? "&" : "?") + "download=1";
                }
                
                if (pdfModal) {
                    pdfModal.show();
                    renderPDF(url);
                }
            });
        });
    }
    initPdfPreviewListeners();
});
