// Global configuration setup before this script is loaded is required if using PDF.js
if (typeof pdfjsLib !== 'undefined') {
    pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
}

async function renderPDF(url) {
    const container = document.getElementById("pdfCanvasContainer");
    const loadingEl = document.getElementById("pdfLoadingIndicator");
    const errorEl = document.getElementById("pdfErrorMsg");

    container.querySelectorAll("canvas").forEach((c) => c.remove());
    loadingEl.style.display = "flex";
    errorEl.style.display = "none";
    container.scrollTop = 0;

    try {
        const pdfDoc = await pdfjsLib.getDocument(url).promise;
        loadingEl.style.display = "none";

        const containerWidth = container.clientWidth - 80;
        const outputScale = window.devicePixelRatio || 2; // High-res rendering

        for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
            const page = await pdfDoc.getPage(pageNum);
            const unscaledViewport = page.getViewport({ scale: 1 });
            const baseScale = containerWidth / unscaledViewport.width;
            const viewport = page.getViewport({
                scale: baseScale * outputScale,
            });

            const canvas = document.createElement("canvas");
            const context = canvas.getContext("2d");

            canvas.width = viewport.width;
            canvas.height = viewport.height;
            canvas.style.width = viewport.width / outputScale + "px";
            canvas.style.height = viewport.height / outputScale + "px";
            canvas.classList.add("shadow-lg", "bg-white");

            container.appendChild(canvas);

            const renderContext = {
                canvasContext: context,
                viewport: viewport,
            };
            await page.render(renderContext).promise;
        }
    } catch (err) {
        loadingEl.style.display = "none";
        errorEl.style.display = "block";
        console.error("PDF.js error:", err);
    }
}

// ── Animasi counter angka ──────────────────────────────────────────────
function animateCounter(el, targetVal) {
    const start = parseInt(el.textContent) || 0;
    const end = parseInt(targetVal) || 0;
    const dur = 500;
    const step = 16;
    const steps = Math.ceil(dur / step);
    const inc = (end - start) / steps;
    let current = start;
    let count = 0;

    const timer = setInterval(function () {
        count++;
        current += inc;
        el.textContent = Math.round(count >= steps ? end : current);
        if (count >= steps) clearInterval(timer);
    }, step);
}

// ── Fetch stats berdasarkan filter tahun ajaran ───────────────────────
function loadStats(periodeId) {
    const container = document.getElementById("rekap-container");
    const statsUrl = container ? container.dataset.statsUrl : "";
    if (!statsUrl) return;

    const url = periodeId ? `${statsUrl}?periode=${periodeId}` : statsUrl;

    fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
        .then((res) => res.json())
        .then((data) => {
            animateCounter(
                document.getElementById("statSiswaAktif"),
                data.siswa_aktif,
            );
            animateCounter(
                document.getElementById("statSiswaSelesai"),
                data.siswa_selesai,
            );
            animateCounter(
                document.getElementById("statTotalSiswa"),
                data.total_siswa,
            );
            animateCounter(
                document.getElementById("statTotalGuru"),
                data.total_guru,
            );
        })
        .catch((err) => console.error("Gagal memuat statistik:", err));
}

document.addEventListener("DOMContentLoaded", function () {
    const filterEl = document.getElementById("filterPeriode");
    const badgeEl = document.getElementById("activeFilterBadge");
    const badgeLabelEl = document.getElementById("activeFilterLabel");
    const previewButtons = document.querySelectorAll(".btn-preview-pdf");
    const pdfModalElement = document.getElementById("previewPdfModal");
    const pdfModal = new bootstrap.Modal(pdfModalElement);
    const downloadBtn = document.getElementById("downloadPdfBtn");

    // ── Saat filter berubah ─────────────────────────────────────────
    filterEl.addEventListener("change", function () {
        const periodeId = this.value;
        const periodeLabel = this.options[this.selectedIndex].text;

        if (periodeId) {
            badgeLabelEl.textContent = periodeLabel;
            badgeEl.style.display = "block";
        } else {
            badgeEl.style.display = "none";
        }

        loadStats(periodeId);
    });

    // ── Tombol preview PDF (sertakan filter) ───────────────────────
    previewButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const url = this.getAttribute("data-url");
            const periode = filterEl.value;
            if (!url) return;

            let finalUrl = url;
            if (periode) {
                finalUrl +=
                    (finalUrl.includes("?") ? "&" : "?") + "periode=" + periode;
            }

            downloadBtn.href =
                finalUrl + (finalUrl.includes("?") ? "&" : "?") + "download=1";
            pdfModal.show();
            renderPDF(finalUrl);
        });
    });

    pdfModalElement.addEventListener("hidden.bs.modal", function () {
        const container = document.getElementById("pdfCanvasContainer");
        container.querySelectorAll("canvas").forEach((c) => c.remove());
    });
});
