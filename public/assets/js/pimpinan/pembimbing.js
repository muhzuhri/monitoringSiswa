document.addEventListener("DOMContentLoaded", function () {
    let currentSiswas = [];
    const filterSelect = document.getElementById("pl_filter_periode");
    const listContainer = document.getElementById("pl_supervised_list");

    // Function to render student list based on filter
    function renderSiswa(periodeId = "all") {
        if (!listContainer) return;
        listContainer.innerHTML = "";

        const filtered = periodeId === "all" 
            ? currentSiswas 
            : currentSiswas.filter(s => s.id_tahun_ajaran == periodeId);

        if (filtered.length > 0) {
            filtered.forEach((s) => {
                const studentDiv = document.createElement("div");
                studentDiv.className = "col";
                studentDiv.innerHTML = `
                    <div class="user-mini-card shadow-sm border h-100">
                        <div class="user-mini-header">
                            <div class="user-mini-icon bg-success shadow-sm" style="width:36px; height:36px; font-size:0.9rem;">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="user-mini-info text-truncate">
                                <div class="name text-truncate" title="${s.nama}">${s.nama}</div>
                                <div class="label text-muted">NISN: ${s.nisn}</div>
                            </div>
                        </div>
                        <div class="p-mt-1">
                            <span class="p-badge-pill extra-small" style="padding: 2px 8px; font-size:0.65rem;">${s.tahun_ajaran}</span>
                        </div>
                    </div>
                `;
                listContainer.appendChild(studentDiv);
            });
        } else {
            listContainer.innerHTML = `
                <div class="col-12 text-center py-4 bg-light rounded-4">
                    <i class="fas fa-user-slash text-muted mb-2 opacity-50 fa-2x"></i>
                    <p class="text-muted small mb-0">Tidak ada siswa bimbingan pada kriteria ini.</p>
                </div>
            `;
        }
    }

    // Modal Trigger Logic
    const detailButtons = document.querySelectorAll(".btn-detail");
    detailButtons.forEach((button) => {
        button.addEventListener("click", function () {
            // Reset filter select
            if (filterSelect) filterSelect.value = "all";

            document.getElementById("pl_det_nama").textContent = this.getAttribute("data-nama");
            document.getElementById("pl_det_jabatan").textContent = this.getAttribute("data-jabatan");
            document.getElementById("pl_det_email").textContent = this.getAttribute("data-email");
            document.getElementById("pl_det_telp").textContent = this.getAttribute("data-telp");
            document.getElementById("pl_det_instansi").textContent = this.getAttribute("data-instansi");

            // Store current siswas and render
            currentSiswas = JSON.parse(this.getAttribute("data-siswas"));
            renderSiswa();
        });
    });

    // Filter Event Listener
    if (filterSelect) {
        filterSelect.addEventListener("change", function() {
            renderSiswa(this.value);
        });
    }
});
