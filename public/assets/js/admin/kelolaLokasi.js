document.addEventListener('DOMContentLoaded', function() {
    // Edit Modal Handler
    const editButtons = document.querySelectorAll('.btn-edit');
    const editForm = document.getElementById('formEditLokasi');
    
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            editForm.action = `/admin/lokasi/${id}`;
            
            document.getElementById('edit_nama').value = this.dataset.nama;
            document.getElementById('edit_lat').value = this.dataset.lat;
            document.getElementById('edit_lng').value = this.dataset.lng;
            document.getElementById('edit_radius').value = this.dataset.radius;
            document.getElementById('edit_active').value = this.dataset.active;
        });
    });

    // Delete Modal Handler
    const deleteButtons = document.querySelectorAll('.btn-delete');
    const deleteForm = document.getElementById('formHapusLokasi');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            deleteForm.action = this.dataset.url;
        });
    });
});
