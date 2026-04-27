/**
 * Pengajuan Lupa Absensi / Kegiatan - Student Module
 * Handles dynamic form fields and file input UI
 */

/**
 * Update UI when a file is selected
 * @param {HTMLInputElement} input 
 */
function updateFileName(input) {
    const display = document.getElementById('file-name-display');
    if (!display) return;

    if (input.files && input.files[0]) {
        display.style.display = 'block';
        display.innerHTML = '<i class="fas fa-file-alt me-1"></i> Terpilih: ' + input.files[0].name;
        
        const parent = input.parentElement;
        parent.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
        parent.classList.remove('bg-light', 'border-light');
        
        // Update icon
        const icon = parent.querySelector('i');
        if (icon) {
            icon.classList.remove('fa-cloud-upload-alt', 'text-muted');
            icon.classList.add('fa-check-circle', 'text-primary');
        }
    } else {
        display.style.display = 'none';
        const parent = input.parentElement;
        parent.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
        
        const icon = parent.querySelector('i');
        if (icon) {
            icon.classList.add('fa-cloud-upload-alt', 'text-muted');
            icon.classList.remove('fa-check-circle', 'text-primary');
        }
    }
}

// Ensure function is global for inline onchange (though we should move it to listener)
window.updateFileName = updateFileName;

document.addEventListener('DOMContentLoaded', function() {
    const jenisSelect = document.getElementById('jenis_pengajuan');
    const fieldsAbsensi = document.getElementById('fields_absensi');
    const fieldsKegiatan = document.getElementById('fields_kegiatan');
    const descTextarea = document.querySelector('textarea[name="deskripsi"]');
    
    if (!jenisSelect) return;

    // Toggle required attribute based on selection
    function toggleRequired() {
        if (jenisSelect.value === 'kegiatan' && descTextarea) {
            descTextarea.setAttribute('required', 'required');
        } else if (descTextarea) {
            descTextarea.removeAttribute('required');
        }
    }
    
    // Initial state
    toggleRequired();

    // Select change handler
    jenisSelect.addEventListener('change', function() {
        if (this.value === 'absensi') {
            if (fieldsAbsensi) {
                fieldsAbsensi.classList.remove('hidden');
                fieldsAbsensi.style.opacity = '0';
                setTimeout(() => fieldsAbsensi.style.opacity = '1', 10);
            }
            if (fieldsKegiatan) fieldsKegiatan.classList.add('hidden');
            
        } else if (this.value === 'kegiatan') {
            if (fieldsKegiatan) {
                fieldsKegiatan.classList.remove('hidden');
                fieldsKegiatan.style.opacity = '0';
                setTimeout(() => fieldsKegiatan.style.opacity = '1', 10);
            }
            if (fieldsAbsensi) fieldsAbsensi.classList.add('hidden');
        } else {
            if (fieldsAbsensi) fieldsAbsensi.classList.add('hidden');
            if (fieldsKegiatan) fieldsKegiatan.classList.add('hidden');
        }
        toggleRequired();
    });

    // Setup listener for file input instead of just inline onchange
    const fileInput = document.querySelector('.hidden-file-input');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            updateFileName(this);
        });
    }
});
