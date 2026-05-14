// 0. Security & Secure Context Check (Required for Geolocation)
function checkSecureContext() {
    const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    const isHttps = window.location.protocol === 'https:';

    if (!isHttps && !isLocalhost) {
        Swal.fire({
            icon: 'error',
            title: 'Koneksi Tidak Aman!',
            html: `
                <div class="text-start">
                    <p>Sistem mendeteksi Anda menggunakan koneksi <b>HTTP</b> yang tidak aman.</p>
                    <p><b>Fitur Absensi (GPS) Wajib menggunakan HTTPS</b> agar browser dapat memberikan izin akses lokasi.</p>
                    <hr>
                    <p class="mb-1"><b>Solusi:</b></p>
                    <ol class="small">
                        <li>Gunakan alamat <b>https://</b> di depan URL.</li>
                        <li>Hubungi Admin jika SSL belum terpasang.</li>
                        <li>Pastikan pengaturan SSL di server/Laragon sudah aktif.</li>
                    </ol>
                </div>
            `,
            allowOutsideClick: false,
            showConfirmButton: false,
            footer: '<span class="text-danger">Akses fitur ini diblokir demi keamanan data lokasi Anda.</span>'
        });
        return false;
    }
    return true;
}

// 1. Proactive Permission Check
async function checkLocationPermission() {
    if (!navigator.permissions || !navigator.permissions.query) return;

    try {
        const result = await navigator.permissions.query({ name: 'geolocation' });
        if (result.state === 'denied') {
            showLocationBlockedAlert();
        }
        
        result.onchange = () => {
            if (result.state === 'denied') {
                showLocationBlockedAlert();
            } else if (result.state === 'granted') {
                Swal.close();
            }
        };
    } catch (e) {
        console.warn('Permissions API not fully supported');
    }
}

function showLocationBlockedAlert() {
    Swal.fire({
        icon: 'warning',
        title: 'Akses Lokasi Diblokir!',
        html: `
            <div class="text-start border-start border-4 border-warning ps-3 mb-3">
                Anda sebelumnya menolak akses lokasi. Sistem membutuhkan izin ini untuk memvalidasi kehadiran Anda.
            </div>
            <div class="small">
                <p class="mb-2"><b>Panduan Membuka Blokir:</b></p>
                <p class="mb-1"><i class="fas fa-mobile-alt me-2"></i><b>Android/iOS:</b></p>
                <p class="ms-4 mb-2">Ketuk <b>"Koneksi Tidak Aman"</b> atau <b>Ikon Gembok 🔒</b> di URL Bar &rarr; Pilih <b>Izin (Permissions)</b> &rarr; Aktifkan <b>Lokasi</b>.</p>
                <p class="mb-1"><i class="fas fa-desktop me-2"></i><b>Laptop/PC:</b></p>
                <p class="ms-4 mb-0">Klik <b>Ikon Gembok 🔒</b> di ujung kiri URL bar &rarr; Pilih <b>Allow (Izinkan)</b> pada Location.</p>
            </div>
        `,
        confirmButtonText: '<i class="fas fa-sync-alt me-2"></i>Saya Sudah Izinkan, Refresh Halaman',
        allowOutsideClick: false,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.reload();
        }
    });
}

function togglePhotoLabel(val) {
    const label = document.getElementById('photoLabel');
    const help = document.getElementById('photoHelp');
    if (!label || !help) return;
    if (val === 'hadir') {
        label.innerText = 'Foto Selfie di Lokasi';
        help.innerText = 'Ambil foto Anda di lokasi magang.';
    } else {
        label.innerText = 'Foto Surat / Bukti Pendukung';
        help.innerText = 'Unggah foto surat izin atau surat keterangan sakit.';
    }
}

function updateClock() {
    const clockEl = document.getElementById('realtimeClock');
    const logbookClockEl = document.getElementById('logbookRealtimeClock');
    
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-GB', {
        hour12: false
    });
    
    if (clockEl) clockEl.textContent = timeString;
    if (logbookClockEl) logbookClockEl.textContent = timeString;
}
setInterval(updateClock, 1000);
updateClock();

// Pagination & Tab State
let currentModalTab = 'absensi';
let currentAbsenPage = 1;
let currentKegiatanPage = 1;
let hasMoreAbsen = true;
let hasMoreKegiatan = true;

function switchModalTab(tab) {
    currentModalTab = tab;
    document.querySelectorAll('.modal-tab-btn').forEach(btn => btn.classList.remove('active'));
    if (tab === 'absensi') {
        document.querySelectorAll('.modal-tab-btn')[0].classList.add('active');
        document.getElementById('absensiTableArea').classList.remove('hidden');
        document.getElementById('kegiatanTableArea').classList.add('hidden');
        updatePaginationUI(currentAbsenPage, hasMoreAbsen);
    } else {
        document.querySelectorAll('.modal-tab-btn')[1].classList.add('active');
        document.getElementById('absensiTableArea').classList.add('hidden');
        document.getElementById('kegiatanTableArea').classList.remove('hidden');
        updatePaginationUI(currentKegiatanPage, hasMoreKegiatan);
    }
    fetchHistoryData();
}

async function fetchHistoryData() {
    const config = window.absensiConfig || {};
    const page = currentModalTab === 'absensi' ? currentAbsenPage : currentKegiatanPage;
    const route = currentModalTab === 'absensi' ? config.absensiDetailRoute : config.logbookDetailRoute;
    const tbodyId = currentModalTab === 'absensi' ? 'historyAbsensiBody' : 'historyKegiatanBody';
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    const cols = currentModalTab === 'absensi' ? 3 : 4;

    try {
        const response = await fetch(`${route}?page=${page}`);
        const result = await response.json();
        const data = result.data;

        if (currentModalTab === 'absensi') {
            hasMoreAbsen = result.has_more;
            updatePaginationUI(currentAbsenPage, hasMoreAbsen);
        } else {
            hasMoreKegiatan = result.has_more;
            updatePaginationUI(currentKegiatanPage, hasMoreKegiatan);
        }

        if (data.length === 0) {
            tbody.innerHTML =
                `<tr><td colspan="${cols}" class="text-center p-5 text-muted"><i class="fas fa-folder-open mb-3 d-block" style="font-size: 2rem; opacity: 0.3;"></i> Tidak ada data di halaman ini.</td></tr>`;
            return;
        }

        if (currentModalTab === 'absensi') {
            tbody.innerHTML = data.map(item => `
                    <tr>
                        <td data-label="Tanggal" class="date-col">
                            <strong>${item.day}</strong>
                            <span>${item.date}</span>
                        </td>
                        <td data-label="Waktu">
                            <div class="arrival-time-stack">
                                <span class="time-in">
                                    <i class="fas fa-sign-in-alt me-1"></i> ${item.jam_masuk || '--:--'}
                                    ${item.foto_masuk ? `<a href="${config.storageUrl}/${item.foto_masuk}" target="_blank" class="ms-1 text-primary"><i class="fas fa-camera small"></i></a>` : ''}
                                </span>
                                <span class="time-out">
                                    <i class="fas fa-sign-out-alt me-1"></i> ${item.jam_pulang || '--:--'}
                                    ${item.foto_pulang ? `<a href="${config.storageUrl}/${item.foto_pulang}" target="_blank" class="ms-1 text-primary"><i class="fas fa-camera small"></i></a>` : ''}
                                </span>
                            </div>
                        </td>
                        <td data-label="Status" class="text-center">
                            ${item.verifikasi === 'verified' 
                                ? `<span class="badge-ui badge-success"><i class="fas fa-check"></i></span>`
                                : item.verifikasi === 'rejected'
                                ? `<span class="badge-ui badge-danger"><i class="fas fa-times"></i></span>`
                                : `<span class="badge-ui badge-warning"><i class="fas fa-clock"></i></span>`
                            }
                        </td>
                    </tr>
                `).join('');
        } else {
            tbody.innerHTML = data.map(item => `
                    <tr>
                        <td data-label="Tanggal" class="date-col">
                            <strong>${item.day}</strong>
                            <span>${item.date}</span>
                        </td>
                        <td data-label="Waktu" class="text-center">
                            <span class="badge bg-light text-dark fw-bold border">${item.jam || '--:--'}</span>
                        </td>
                        <td data-label="Kegiatan">
                            <div class="log-content-wrapper">
                                <div class="text-dark mb-1">${item.kegiatan}</div>
                                ${item.catatan ? `<div class="mt-2 p-2 rounded-3 bg-light border-start border-3 border-primary small"><span class="text-primary fw-bold">Catatan:</span> "${item.catatan}"</div>` : ''}
                            </div>
                        </td>
                        <td data-label="Status" class="text-center">
                             ${item.status === 'verified' 
                                ? `<span class="badge-ui badge-success"><i class="fas fa-check"></i></span>`
                                : item.status === 'rejected'
                                ? `<span class="badge-ui badge-danger"><i class="fas fa-times"></i></span>`
                                : `<span class="badge-ui badge-warning"><i class="fas fa-clock"></i></span>`
                            }
                        </td>
                    </tr>
                `).join('');
        }

    } catch (error) {
        console.error('Error fetching data:', error);
        tbody.innerHTML =
            `<tr><td colspan="${cols}" class="text-center p-5 text-danger">Gagal memuat data.</td></tr>`;
    }
}

function changePage(direction) {
    if (currentModalTab === 'absensi') {
        if (direction === 1 && hasMoreAbsen) currentAbsenPage++;
        else if (direction === -1 && currentAbsenPage > 1) currentAbsenPage--;
    } else {
        if (direction === 1 && hasMoreKegiatan) currentKegiatanPage++;
        else if (direction === -1 && currentKegiatanPage > 1) currentKegiatanPage--;
    }
    fetchHistoryData();
}

function updatePaginationUI(page, hasMore) {
    const currentNumEl = document.getElementById('currentPageNum');
    const btnPrev = document.getElementById('btnPrevPage');
    const btnNext = document.getElementById('btnNextPage');
    if (!currentNumEl || !btnPrev || !btnNext) return;

    currentNumEl.innerText = page;
    btnPrev.disabled = !hasMore;
    btnNext.disabled = (page === 1);
    btnPrev.style.opacity = !hasMore ? '0.5' : '1';
    btnNext.style.opacity = (page === 1) ? '0.5' : '1';
}

function getStatusBadgeClass(status) {
    status = status.toLowerCase();
    if (status.includes('hadir')) return 'badge-ui badge-success';
    if (status.includes('izin')) return 'badge-ui badge-info';
    if (status.includes('sakit')) return 'badge-ui badge-warning';
    return 'badge-ui badge-danger';
}

function getLogStatusClass(status) {
    if (status === 'verified') return 'badge-ui badge-success';
    if (status === 'rejected') return 'badge-ui badge-danger';
    return 'badge-ui badge-warning';
}

// Geolocation Processing
async function prosesAbsensi(type) {
    if (!checkSecureContext()) return;

    const config = window.absensiConfig || {};
    const form = type === 'masuk' ? document.getElementById('formAbsensiMasuk') : document.getElementById('formAbsensiPulang');
    if (!form) return;

    if (!navigator.geolocation) {
        Swal.fire('Error', 'Browser Anda tidak mendukung fitur lokasi.', 'error');
        return;
    }

    try {
        const permission = await navigator.permissions.query({ name: 'geolocation' });
        if (permission.state === 'denied') {
            showLocationBlockedAlert();
            return;
        }
    } catch (e) { }

    Swal.fire({
        title: 'Meminta Akses Lokasi',
        html: 'Sistem sedang mempersiapkan lokasi absensi Anda.<br><br><b>PENTING:</b> Harap selalu pilih <b>"Allow" / "Izinkan"</b> jika muncul pop-up peringatan dari Browser di bagian atas layar.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    navigator.geolocation.getCurrentPosition(async (position) => {
        Swal.fire({
            title: 'Lokasi Ditemukan!',
            text: 'Sedang mengirim data absensi ke server...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        const userLat = position.coords.latitude;
        const userLng = position.coords.longitude;

        const formData = new FormData(form);
        formData.append('latitude', userLat);
        formData.append('longitude', userLng);

        try {
            const response = await fetch(config.absensiStoreRoute, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': config.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (response.ok && result.success !== false) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message || 'Absen berhasil dicatat!',
                }).then(() => {
                    window.location.reload();
                });
            } else {
                let errorMsg = result.message || 'Gagal menyimpan data absensi.';
                if (result.errors) {
                    errorMsg = Object.values(result.errors).flat().join('\n');
                }
                
                Swal.close();
                setTimeout(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errorMsg,
                        showConfirmButton: true,
                        confirmButtonText: 'Tutup'
                    });
                }, 100);
            }
        } catch (error) {
            console.error('Fetch error:', error);
            Swal.close();
            setTimeout(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem saat menghubungi server.',
                    showConfirmButton: true,
                    confirmButtonText: 'Tutup'
                });
            }, 100);
        }

    }, (error) => {
        console.error('Geolocation Error:', error);
        let msg = 'Gagal mengambil lokasi karena galat tidak dikenal.';
        
        if (error.code === error.PERMISSION_DENIED) {
            msg = 'Akses ditolak! Mohon izinkan akses lokasi di pengaturan browser Anda.';
            showLocationBlockedAlert();
            return;
        } else if (error.code === error.POSITION_UNAVAILABLE) {
            msg = 'Informasi sinyal GPS / Lokasi tidak tersedia saat ini di perangkat Anda.';
        } else if (error.code === error.TIMEOUT) {
            msg = 'Waktu pencarian lokasi habis (Timeout). Pastikan koneksi dan GPS berjalan lancar, lalu pencet tombol absen kembali.';
        }
        
        Swal.close();
        setTimeout(() => {
            Swal.fire({
                icon: 'warning',
                title: 'Akses Lokasi Gagal',
                text: msg,
                confirmButtonText: 'Tutup'
            });
        }, 100);
    }, {
        enableHighAccuracy: true,
        timeout: 30000,
        maximumAge: 0
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Initial checks
    if (checkSecureContext()) {
        checkLocationPermission();
    }

    const btnMasuk = document.querySelector('#formAbsensiMasuk .btn-checkin');
    const btnPulang = document.querySelector('#formAbsensiPulang .btn-checkout');

    if (btnMasuk) {
        btnMasuk.addEventListener('click', (e) => {
            e.preventDefault();
            const form = document.getElementById('formAbsensiMasuk');
            if (form && !form.reportValidity()) return;
            prosesAbsensi('masuk');
        });
    }

    if (btnPulang) {
        btnPulang.addEventListener('click', (e) => {
            e.preventDefault();
            const form = document.getElementById('formAbsensiPulang');
            if (form && !form.reportValidity()) return;
            prosesAbsensi('pulang');
        });
    }

    const historyModal = document.getElementById('modalHistoryDetail');
    if (historyModal) {
        historyModal.addEventListener('shown.bs.modal', function() {
            fetchHistoryData();
        });
    }
});

