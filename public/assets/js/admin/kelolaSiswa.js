document.addEventListener('DOMContentLoaded', function() {
    const containerEl = document.querySelector('.management-container');
    const jurnalUrlTemplate = containerEl.dataset.jurnalUrl;
    const absensiUrlTemplate = containerEl.dataset.absensiUrl;
    const kelompokUrlTemplate = containerEl.dataset.kelompokUrl;
    const loaderAsset = containerEl.dataset.assetLoader;

    // Set loader image source
    const loaderImg = document.getElementById('pdfLoaderImg');
    if (loaderImg) loaderImg.src = loaderAsset;

    // ── View Mode Switching ────────────────────────────────────────
    document.querySelectorAll('.view-mode-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            const target = this.dataset.target;
            const pane = this.closest('.tab-pane');

            pane.querySelectorAll('.view-mode-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const grouped = pane.querySelector(`#${target}-grouped-view`);
            const flat = pane.querySelector(`#${target}-flat-view`);

            if (view === 'grouped') {
                grouped.classList.remove('d-none');
                flat.classList.add('d-none');
            } else {
                grouped.classList.add('d-none');
                flat.classList.remove('d-none');
            }
        });
    });

    // ── Tab Persistence from URL ───────────────────────────────────
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'history' || urlParams.get('periode')) {
        const historyTabBtn = document.getElementById('history-tab');
        if (historyTabBtn) {
            new bootstrap.Tab(historyTabBtn).show();
        }
    }

    // ── NPSN Lookup Logic ──────────────────────────────────────────
    function initNpsnLookup(inputId, outputId, msgId) {
        const npsnInput = document.getElementById(inputId);
        const schoolInput = document.getElementById(outputId);
        const msgEl = document.getElementById(msgId);

        if (!npsnInput || !schoolInput) return;

        npsnInput.addEventListener('input', function() {
            const npsn = this.value;
            if (npsn.length >= 8) {
                if (msgEl) {
                    msgEl.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mencari...';
                    msgEl.className = 'text-primary small mt-1 d-block';
                }
                
                schoolInput.value = '';

                fetch(`/api/schools/${npsn}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            schoolInput.value = data.data.nama_sekolah;
                            schoolInput.readOnly = true;
                            if (msgEl) {
                                msgEl.innerHTML = '<i class="fas fa-check-circle me-1"></i> Terindentifikasi';
                                msgEl.className = 'text-success small mt-1 d-block';
                            }
                        } else {
                            schoolInput.readOnly = false;
                            schoolInput.placeholder = "Isi manual jika tidak ditemukan";
                            if (msgEl) {
                                msgEl.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Tidak terdaftar. Isi manual.';
                                msgEl.className = 'text-danger small mt-1 d-block';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching school:', error);
                        schoolInput.readOnly = false;
                        if (msgEl) msgEl.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Gangguan koneksi. Isi manual.';
                    });
            } else {
                if (msgEl) {
                    msgEl.innerHTML = 'Min. 8 digit';
                    msgEl.className = 'text-muted small mt-1 d-block';
                }
            }
        });
    }

    initNpsnLookup('reg_npsn', 'reg_sekolah', 'reg_npsn_msg');
    initNpsnLookup('edit_npsn', 'edit_sekolah', null);

    // ── Edit Modal ─────────────────────────────────────────────────
    const editForm = document.getElementById('formEditSiswa');
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function() {
            editForm.action = `/admin/siswa/${this.dataset.id}`;
            document.getElementById('edit_nama').value = this.dataset.nama;
            document.getElementById('edit_email').value = this.dataset.email;
            document.getElementById('edit_jk').value = this.dataset.jk || 'Laki-laki';
            document.getElementById('edit_nisn').value = this.dataset.id;
            document.getElementById('edit_kelas').value = this.dataset.kelas;
            document.getElementById('edit_jurusan').value = this.dataset.jurusan;
            document.getElementById('edit_sekolah').value = this.dataset.sekolah;
            document.getElementById('edit_npsn').value = this.dataset.npsn || '';
            document.getElementById('edit_id_tahun_ajaran').value = this.dataset.id_tahun_ajaran || '';
            document.getElementById('edit_perusahaan').value = this.dataset.perusahaan || '';
            document.getElementById('edit_tipe_magang').value = this.dataset.tipe_magang || 'individu';
            document.getElementById('edit_nisn_ketua').value = this.dataset.nisn_ketua || '';
            
            document.getElementById('edit_surat_balasan_input').value = '';
            const suratInfo = document.getElementById('edit_surat_balasan_info');
            if (this.dataset.surat_balasan && this.dataset.surat_balasan !== 'null') {
                suratInfo.innerHTML = `<i class="fas fa-file-check text-success"></i> File saat ini: <strong>${this.dataset.surat_balasan.split('/').pop()}</strong><br>Pilih file baru untuk mengganti.`;
            } else {
                suratInfo.textContent = "Belum ada file. Pilih file untuk mengunggah.";
            }

            document.getElementById('edit_id_guru').value = this.dataset.guruNip || '';
            document.getElementById('edit_id_pembimbing').value = this.dataset.plNip || '';
            document.getElementById('edit_tgl_mulai').value = this.dataset.mulai_raw || '';
            document.getElementById('edit_tgl_selesai').value = this.dataset.selesai_raw || '';
            document.getElementById('edit_hp').value = this.dataset.no_hp || '';
        });
    });

    // ── Detail Modal ───────────────────────────────────────────────
    function initDetailModalListeners() {
        document.querySelectorAll('.btn-detail').forEach(button => {
            button.onclick = function() {
                document.getElementById('det_name').textContent = this.dataset.nama;
                document.getElementById('det_nisn').textContent = this.dataset.nisn;
                document.getElementById('det_jk').textContent = this.dataset.jk || '-';
                document.getElementById('det_email').textContent = this.dataset.email;
                document.getElementById('det_hp').textContent = this.dataset.no_hp || '-';
                document.getElementById('det_kelas_jurusan').textContent = `${this.dataset.kelas} - ${this.dataset.jurusan}`;
                document.getElementById('det_tahun_ajaran').textContent = this.dataset.tahun_ajaran || '-';
                document.getElementById('det_sekolah').textContent = this.dataset.sekolah;
                document.getElementById('det_npsn').textContent = this.dataset.npsn || '-';
                document.getElementById('det_perusahaan').textContent = this.dataset.perusahaan || 'Belum ditugaskan';
                document.getElementById('det_tipe_magang').textContent = (this.dataset.tipe_magang || 'Individu').toUpperCase();
                document.getElementById('det_nisn_ketua').textContent = this.dataset.nisn_ketua || '-';

                const suratPath = this.dataset.surat_balasan;
                const detSurat = document.getElementById('det_surat_balasan');
                const btnViewSurat = document.getElementById('btn_view_surat');

                if (suratPath && suratPath !== 'null' && suratPath !== '') {
                    detSurat.textContent = 'File tersedia';
                    detSurat.className = 'detail-value text-success fw-bold';
                    btnViewSurat.classList.remove('d-none');
                    btnViewSurat.onclick = function() {
                        const url = `/storage/${suratPath}`;
                        currentPdfUrl = url;
                        const downloadBtn = document.getElementById('downloadPdfBtn');
                        downloadBtn.href = url + '?download=1';
                        if (pdfModal) pdfModal.show();
                        renderPDF(url);
                    };
                } else {
                    detSurat.textContent = 'Belum diunggah';
                    detSurat.className = 'detail-value text-muted italic';
                    btnViewSurat.classList.add('d-none');
                }

                const mulai = this.dataset.mulai;
                const selesai = this.dataset.selesai;
                document.getElementById('det_periode').textContent = (mulai && selesai && mulai !== '-') ? `${mulai} s/d ${selesai}` : 'Belum ditentukan';

                document.getElementById('det_guru_nama').textContent = this.dataset.guruNama || '-';
                document.getElementById('det_guru_nip').textContent = this.dataset.guruNip || '-';
                
                const guruHp = this.dataset.guruHp || '';
                const guruWaBtn = document.getElementById('det_guru_wa_btn');
                const formatWa = (num) => {
                    let cleaned = num.replace(/\D/g, '');
                    return cleaned.startsWith('0') ? '62' + cleaned.substring(1) : cleaned;
                };
                if (guruHp && guruHp !== '-') {
                    guruWaBtn.href = `https://wa.me/${formatWa(guruHp)}`;
                    guruWaBtn.classList.remove('d-none');
                } else {
                    guruWaBtn.classList.add('d-none');
                }

                document.getElementById('det_pl_nama').textContent = this.dataset.plNama || '-';
                document.getElementById('det_pl_nip').textContent = this.dataset.plNip || '-';
                
                const plHp = this.dataset.plHp || '';
                const plWaBtn = document.getElementById('det_pl_wa_btn');
                if (plHp && plHp !== '-') {
                    plWaBtn.href = `https://wa.me/${formatWa(plHp)}`;
                    plWaBtn.classList.remove('d-none');
                } else {
                    plWaBtn.classList.add('d-none');
                }
            };
        });
    }
    initDetailModalListeners();

    // ── Delete Modal ───────────────────────────────────────────────
    const deleteForm = document.getElementById('formHapus');
    document.querySelectorAll('.btn-delete-trigger').forEach(button => {
        button.addEventListener('click', function() {
            deleteForm.action = this.dataset.url;
        });
    });

    // ── Lokasi Modal Handlers ──────────────────────────────────────
    const editLocForm = document.getElementById('formEditLokasi');
    document.querySelectorAll('.btn-edit-loc').forEach(button => {
        button.addEventListener('click', function() {
            editLocForm.action = `/admin/lokasi/${this.dataset.id}`;
            document.getElementById('edit_loc_nama').value = this.dataset.nama;
            document.getElementById('edit_loc_lat').value = this.dataset.lat;
            document.getElementById('edit_loc_lng').value = this.dataset.lng;
            document.getElementById('edit_loc_radius').value = this.dataset.radius;
            document.getElementById('edit_loc_active').value = this.dataset.active;
        });
    });

    const deleteLocForm = document.getElementById('formHapusLokasi');
    document.querySelectorAll('.btn-delete-loc').forEach(button => {
        button.addEventListener('click', function() {
            deleteLocForm.action = this.dataset.url;
        });
    });

    // ── Modal Group Members (Riwayat) ─────────────────────────────
    const groupModalEl = document.getElementById('groupMembersModal');
    const groupModal = groupModalEl ? new bootstrap.Modal(groupModalEl) : null;
    const modalNameEl = document.getElementById('modalGroupName');
    const modalBodyEl = document.getElementById('modalGroupBody');

    document.querySelectorAll('.btn-show-members').forEach(button => {
        button.addEventListener('click', function() {
            const name = this.dataset.name;
            const members = JSON.parse(this.dataset.members);
            const showActions = this.dataset.showActions === 'true';

            modalNameEl.innerText = name;
            modalBodyEl.innerHTML = '';

            const tableHead = groupModalEl.querySelector('thead tr');
            const actionsTh = tableHead.querySelector('th:last-child');
            actionsTh.classList.remove('d-none');

            members.forEach((member) => {
                const logDownload = jurnalUrlTemplate.replace(':nisn', member.nisn);
                const absDownload = absensiUrlTemplate.replace(':nisn', member.nisn);
                const kelDownload = kelompokUrlTemplate.replace(':nisn', member.nisn);

                const statusBadge = showActions ? `
                    <span class="badge-status belum" style="font-size: 0.7rem; padding: 4px 12px;">
                        <i class="fas fa-flag-checkered"></i> SELESAI
                    </span>
                ` : `
                    <span class="badge-status hadir" style="font-size: 0.7rem; padding: 4px 12px;">
                        <i class="fas fa-check-circle"></i> AKTIF
                    </span>
                `;

                const row = `
                    <tr>
                        <td>
                            <div class="cell-name mb-0" style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">${member.nama}</div>
                            <div class="cell-sub small text-muted" style="font-weight: 500;">NISN: ${member.nisn}</div>
                        </td>
                        <td class="text-center">
                            ${statusBadge}
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-3">
                                <button class="btn-premium-circle btn-view-p btn-detail" title="Lihat Profil Lengkap" 
                                    data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                    data-nisn="${member.nisn}" data-nama="${member.nama}" data-email="${member.email}"
                                    data-no_hp="${member.no_hp || ''}" 
                                    data-jk="${member.jenis_kelamin || '-'}"
                                    data-kelas="${member.kelas}" data-jurusan="${member.jurusan}"
                                    data-sekolah="${member.sekolah}" 
                                    data-npsn="${member.npsn || '-'}"
                                    data-perusahaan="${member.perusahaan || ''}"
                                    data-tipe_magang="${member.tipe_magang || 'individu'}"
                                    data-nisn_ketua="${member.nisn_ketua || '-'}"
                                    data-surat_balasan="${member.surat_balasan || ''}"
                                    data-tahun_ajaran="${member.tahun_ajaran ? member.tahun_ajaran.tahun_ajaran : '-'}"
                                    data-mulai="${member.tgl_mulai_magang || ''}" data-selesai="${member.tgl_selesai_magang || ''}"
                                    data-guru-nama="${member.guru ? member.guru.nama : '-'}" data-guru-nip="${member.id_guru || '-'}"
                                    data-pl-nama="${member.pembimbing ? member.pembimbing.nama : '-'}" data-pl-nip="${member.id_pembimbing || '-'}"
                                    data-pl-hp="${member.pembimbing ? member.pembimbing.no_telp : '-'}"
                                    style="width: 38px; height: 38px; font-size: 1rem;">
                                    <i class="fas fa-user-circle"></i>
                                </button>

                                ${showActions ? `
                                        <button class="btn-premium-circle btn-jurnal-p btn-preview-pdf" title="Cetak Jurnal" data-url="${logDownload}"
                                            style="width: 38px; height: 38px; font-size: 1rem;">
                                            <i class="fas fa-book-open"></i>
                                        </button>
                                        <button class="btn-premium-circle btn-absensi-p btn-preview-pdf" title="Cetak Absensi" data-url="${absDownload}"
                                            style="width: 38px; height: 38px; font-size: 1rem;">
                                            <i class="fas fa-calendar-check"></i>
                                        </button>
                                        <button class="btn-premium-circle btn-info-p btn-preview-pdf" title="Cetak Rekap Kelompok" data-url="${kelDownload}"
                                            style="width: 38px; height: 38px; font-size: 1rem;">
                                            <i class="fas fa-users"></i>
                                        </button>
                                    ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
                modalBodyEl.innerHTML += row;
            });

            initPdfPreviewListeners();
            initDetailModalListeners();

            if (groupModal) groupModal.show();
        });
    });

    // ── PDF Preview (PDF.js renderer) ────────
    const pdfModalEl = document.getElementById('previewPdfModal');
    const pdfModal = pdfModalEl ? bootstrap.Modal.getOrCreateInstance(pdfModalEl) : null;
    const downloadBtn = document.getElementById('downloadPdfBtn');
    const printBtn = document.getElementById('printPdfBtn');

    let currentPdfUrl = null;
    let currentTaskId = 0;

    async function renderPDF(url) {
        const taskId = ++currentTaskId;
        const container = document.getElementById('pdfCanvasContainer');
        const loadingEl = document.getElementById('pdfLoadingIndicator');
        const errorEl = document.getElementById('pdfErrorMsg');

        container.querySelectorAll('canvas, img').forEach(c => c.remove());
        loadingEl.style.display = 'flex';
        errorEl.classList.add('d-none');
        container.scrollTop = 0;

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
            await new Promise(resolve => setTimeout(resolve, 200));
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
                canvas.style.width = (viewport.width / outputScale) + 'px';
                canvas.style.height = (viewport.height / outputScale) + 'px';
                canvas.style.display = 'block';
                canvas.style.margin = '0 auto 20px';
                canvas.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
                
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
            console.error('File preview error:', err);
        }
    }

    function initPdfPreviewListeners() {
        document.querySelectorAll('.btn-preview-pdf').forEach(button => {
            button.onclick = function() {
                const url = this.dataset.url;
                if (!url) return;
                currentPdfUrl = url;
                downloadBtn.href = url + (url.includes('?') ? '&' : '?') + 'download=1';
                if (pdfModal) pdfModal.show();
                renderPDF(url);
            };
        });
    }

    initPdfPreviewListeners();

    if (printBtn) {
        printBtn.addEventListener('click', () => {
            if (!currentPdfUrl) return;
            const win = window.open(currentPdfUrl, '_blank');
            win.addEventListener('load', () => win.print(), { once: true });
        });
    }

    if (pdfModalEl) {
        pdfModalEl.addEventListener('hidden.bs.modal', () => {
            document.getElementById('pdfCanvasContainer').querySelectorAll('canvas').forEach(c => c.remove());
            currentPdfUrl = null;
        });
    }

    // ── Password Toggle Logic ──────────────────────────────────────
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('input');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
});
