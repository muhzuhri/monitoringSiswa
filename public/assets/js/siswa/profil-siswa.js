/**
 * Profil Siswa - Student Module
 * Handles image preview and UI interactions
 */

/**
 * Preview image on file selection
 * @param {Event} event 
 */
function previewImage(event) {
    const input = event.target;
    const reader = new FileReader();
    
    reader.onload = function() {
        const output = document.getElementById('profile-preview');
        if (output) {
            output.src = reader.result;
        }
    };
    
    if (input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    }
}

// Make globally available for inline onchange if necessary (prefer event listener)
window.previewImage = previewImage;

document.addEventListener('DOMContentLoaded', function() {
    // Setup file input listener
    const fotoProfilInput = document.getElementById('foto_profil');
    if (fotoProfilInput) {
        fotoProfilInput.addEventListener('change', function(e) {
            previewImage(e);
        });
    }

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.ui-alert');
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    }
});
