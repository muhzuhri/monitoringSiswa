// Load PDF.js dynamically if not already loaded
if (typeof pdfjsLib === 'undefined') {
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
    script.onload = () => {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    };
    document.head.appendChild(script);
} else {
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
}

const pdfModalElement  = document.getElementById('previewPdfModal');
const pdfModal         = pdfModalElement ? bootstrap.Modal.getOrCreateInstance(pdfModalElement) : null;
let currentTaskId = 0;

async function renderPDF(url) {
    const taskId = ++currentTaskId;
    const container = document.getElementById('pdfCanvasContainer');
    const loadingEl = document.getElementById('pdfLoadingIndicator');
    const errorEl = document.getElementById('pdfErrorMsg');

    if (!container || !loadingEl || !errorEl) return;

    container.querySelectorAll('canvas').forEach(c => c.remove());
    loadingEl.style.display = 'flex';
    errorEl.style.display = 'none';
    container.scrollTop = 0;

    try {
        await new Promise(resolve => setTimeout(resolve, 200));
        if (currentTaskId !== taskId) return;

        const pdfDoc = await pdfjsLib.getDocument(url).promise;
        if (currentTaskId !== taskId) return;

        const containerWidth = container.clientWidth - 40;
        const outputScale = window.devicePixelRatio || 1;

        for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
            const page = await pdfDoc.getPage(pageNum);
            const unscaledViewport = page.getViewport({ scale: 1 });
            const baseScale = (containerWidth > 0 ? containerWidth : 800) / unscaledViewport.width;
            const viewport = page.getViewport({ scale: baseScale * outputScale });

            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            canvas.style.width = (viewport.width / outputScale) + 'px';
            canvas.style.height = (viewport.height / outputScale) + 'px';

            container.appendChild(canvas);

            await page.render({ canvasContext: context, viewport: viewport }).promise;

            if (currentTaskId !== taskId) return;
            if (pageNum === 1) loadingEl.style.display = 'none';
        }
    } catch (err) {
        if (currentTaskId === taskId) {
            loadingEl.style.display = 'none';
            errorEl.style.display = 'block';
        }
        console.error('PDF.js error:', err);
    }
}

function animateCounter(el, targetVal) {
    if (!el) return;
    const start   = parseInt(el.textContent) || 0;
    const end     = parseInt(targetVal)      || 0;
    const dur     = 500;
    const step    = 16;
    const steps   = Math.ceil(dur / step);
    const inc     = (end - start) / steps;
    let   current = start;
    let   count   = 0;

    const timer = setInterval(function() {
        count++;
        current += inc;
        el.textContent = Math.round(count >= steps ? end : current);
        if (count >= steps) clearInterval(timer);
    }, step);
}

function loadStats(periodeId) {
    const dashboardContainer = document.querySelector('.dashboard-container');
    const statsUrl = dashboardContainer ? dashboardContainer.dataset.statsUrl : null;
    if (!statsUrl) return;

    const url = periodeId ? `${statsUrl}?periode=${periodeId}` : statsUrl;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(data => {
            animateCounter(document.getElementById('statSiswaAktif'),   data.siswa_aktif);
            animateCounter(document.getElementById('statSiswaSelesai'), data.siswa_selesai);
            animateCounter(document.getElementById('statTotalSiswa'),   data.total_siswa);
            animateCounter(document.getElementById('statTotalGuru'),    data.total_guru);
        })
        .catch(err => console.error('Gagal memuat statistik:', err));
}

document.addEventListener('DOMContentLoaded', function() {
    const filterEl         = document.getElementById('filterPeriode');
    const badgeEl          = document.getElementById('activeFilterBadge');
    const badgeLabelEl     = document.getElementById('activeFilterLabel');
    const previewButtons   = document.querySelectorAll('.btn-preview-pdf');
    const downloadBtn      = document.getElementById('downloadPdfBtn');

    if (filterEl) {
        filterEl.addEventListener('change', function() {
            const periodeId    = this.value;
            const periodeLabel = this.options[this.selectedIndex].text;

            if (periodeId) {
                if (badgeLabelEl) badgeLabelEl.textContent = periodeLabel;
                if (badgeEl) badgeEl.style.display = 'block';
            } else {
                if (badgeEl) badgeEl.style.display = 'none';
            }

            loadStats(periodeId);
        });
    }

    previewButtons.forEach(button => {
        button.onclick = function() {
            const url    = this.getAttribute('data-url');
            const periode = filterEl ? filterEl.value : null;
            if (!url) return;

            let finalUrl = url;
            if (periode) {
                finalUrl += (finalUrl.includes('?') ? '&' : '?') + 'periode=' + periode;
            }

            if (downloadBtn) downloadBtn.href = finalUrl + (finalUrl.includes('?') ? '&' : '?') + 'download=1';
            if (pdfModal) pdfModal.show();
            renderPDF(finalUrl);
        };
    });

    if (pdfModalElement) {
        pdfModalElement.addEventListener('hidden.bs.modal', function() {
            const container = document.getElementById('pdfCanvasContainer');
            if (container) container.querySelectorAll('canvas').forEach(c => c.remove());
        });
    }
});
