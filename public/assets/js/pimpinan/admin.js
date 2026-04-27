document.addEventListener("DOMContentLoaded", function () {
    // Edit Modal Handler
    document.querySelectorAll(".btn-edit").forEach((btn) => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            const nama = this.dataset.nama;
            const email = this.dataset.email;

            const form = document.getElementById("formEditAdmin");
            const updateUrlBase = form.dataset.updateUrl;
            
            form.action = updateUrlBase.replace(':id', id);
            document.getElementById("edit_nama").value = nama;
            document.getElementById("edit_email").value = email;
        });
    });

    // Delete Custom Confirm
    document.querySelectorAll(".btn-delete-trigger").forEach((btn) => {
        btn.addEventListener("click", function (e) {
            if (
                confirm(
                    "Yakin ingin menghapus akun admin ini? Data tidak dapat dikembalikan.",
                )
            ) {
                this.closest("form").submit();
            }
        });
    });
});
