document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnBacaSelengkapnya');
    const sejarahCollapse = document.getElementById('sejarahLengkap');

    if (!btn || !sejarahCollapse) {
        return;
    }

    sejarahCollapse.addEventListener('show.bs.collapse', function () {
        btn.innerHTML = "Tutup <i class='fas fa-arrow-up ms-1'></i>";
    });

    sejarahCollapse.addEventListener('hide.bs.collapse', function () {
        btn.innerHTML = 'Baca Selengkapnya <i class="fas fa-arrow-down ms-1"></i>';
    });
});