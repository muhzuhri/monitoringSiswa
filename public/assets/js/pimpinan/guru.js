document.addEventListener("DOMContentLoaded", function () {
    // Preview Detail Logic
    const detailButtons = document.querySelectorAll(".btn-detail");
    detailButtons.forEach((button) => {
        button.addEventListener("click", function () {
            document.getElementById("det_nama").textContent =
                this.getAttribute("data-nama");
            document.getElementById("det_id_guru").textContent =
                this.getAttribute("data-id_guru");
            document.getElementById("det_email").textContent =
                this.getAttribute("data-email");
            document.getElementById("det_sekolah").textContent =
                this.getAttribute("data-sekolah");
            document.getElementById("det_jabatan").textContent =
                this.getAttribute("data-jabatan");

            // Populate supervised students list
            const siswas = JSON.parse(this.getAttribute("data-siswas"));
            const listContainer = document.getElementById(
                "supervised_students_list",
            );
            listContainer.innerHTML = "";

            if (siswas.length > 0) {
                siswas.forEach((s) => {
                    const studentDiv = document.createElement("div");
                    studentDiv.className = "col";
                    studentDiv.innerHTML = `
                                <div class="supervisor-card p-3 h-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">${s.nama}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">NISN: ${s.nisn}</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                    listContainer.appendChild(studentDiv);
                });
            } else {
                listContainer.innerHTML =
                    '<div class="col-12 text-muted">Belum ada siswa bimbingan yang terdaftar untuk guru ini.</div>';
            }
        });
    });
});
