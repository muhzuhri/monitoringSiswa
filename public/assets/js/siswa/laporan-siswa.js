/**
 * Laporan & Penilaian - Student Module
 * Handles PDF Preview and UI Interactions
 */

// Global state
let currentPdfUrl = '';

// Configure PDF.js worker
if (typeof pdfjsLib !== 'undefined') {
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
}

/**
 * Render PDF using PDF.js
 * @param {string} url 
 */
async function renderPDF(url) {
    const container = document.getElementById('pdfCanvasContainer');
    const loadingEl = document.getElementById('pdfLoadingIndicator');
    const errorEl = document.getElementById('pdfErrorMsg');

    if (!container) return;

    // Clear previous canvases
    container.querySelectorAll('canvas').forEach(c => c.remove());
    loadingEl.style.display = 'block';
    errorEl.style.display = 'none';
    container.scrollTop = 0;

    try {
        const loadingTask = pdfjsLib.getDocument(url);
        const pdfDoc = await loadingTask.promise;
        loadingEl.style.display = 'none';

        const containerWidth = container.clientWidth - 24;
        const outputScale = window.devicePixelRatio || 1;

        // Render each page
        for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
            const page = await pdfDoc.getPage(pageNum);
            const unscaledViewport = page.getViewport({ scale: 1 });
            const baseScale = containerWidth / unscaledViewport.width;
            const viewport = page.getViewport({ scale: baseScale * outputScale });

            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            canvas.style.width = (viewport.width / outputScale) + 'px';
            canvas.style.height = (viewport.height / outputScale) + 'px';

            container.appendChild(canvas);

            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            await page.render(renderContext).promise;
        }
    } catch (err) {
        if (loadingEl) loadingEl.style.display = 'none';
        if (errorEl) errorEl.style.display = 'block';
        console.error('PDF.js error:', err);
    }
}

// Initialize on DOM Load
document.addEventListener('DOMContentLoaded', function() {
    const previewButtons = document.querySelectorAll('.btn-preview-pdf');
    const pdfModalElement = document.getElementById('previewPdfModal');
    
    if (!pdfModalElement) return;

    const pdfModal = new bootstrap.Modal(pdfModalElement);
    const downloadBtn = document.getElementById('downloadPdfBtn');
    const downloadBtnMobile = document.getElementById('downloadPdfBtnMobile');
    const printBtnMobile = document.getElementById('printPdfBtnMobile');
    const fileInput = document.getElementById('fileInput');
    const fileNameDisplay = document.getElementById('fileName');

    // PDF Preview Buttons
    previewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            if (!url) return;

            // Set download link
            const downloadUrl = url.includes('?') ? url + '&download=1' : url + '?download=1';
            if (downloadBtn) downloadBtn.href = downloadUrl;
            if (downloadBtnMobile) downloadBtnMobile.href = downloadUrl;

            currentPdfUrl = url;
            pdfModal.show();
        });
    });

    // Handle modal shown event for PDF rendering
    pdfModalElement.addEventListener('shown.bs.modal', function() {
        if (currentPdfUrl) {
            renderPDF(currentPdfUrl);
        }
    });

    // Cetak: buka di tab baru agar printer native device bisa digunakan
    const doPrint = function() {
        if (currentPdfUrl) window.open(currentPdfUrl, '_blank');
    };
    if (printBtnMobile) printBtnMobile.addEventListener('click', doPrint);

    // Cleanup on modal hide
    pdfModalElement.addEventListener('hidden.bs.modal', function() {
        const container = document.getElementById('pdfCanvasContainer');
        if (container) {
            container.querySelectorAll('canvas').forEach(c => c.remove());
        }
        if (document.getElementById('pdfLoadingIndicator')) {
            document.getElementById('pdfLoadingIndicator').style.display = 'none';
        }
        if (document.getElementById('pdfErrorMsg')) {
            document.getElementById('pdfErrorMsg').style.display = 'none';
        }
        currentPdfUrl = '';
    });

    // File input change handler for Laporan Akhir
    if (fileInput && fileNameDisplay) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileNameDisplay.innerHTML = this.files[0].name;
            }
        });
    }
});
