<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Siswa - <?php echo e($siswa->nama); ?></title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.3;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 16pt;
            margin: 0;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 14pt;
            margin: 5px 0;
            text-transform: uppercase;
        }

        .line {
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 15px;
        }

        .label1 {
            width: 60px;
        }

        .separator1 {
            width: 10px;
        }
        .name1{
            width: 210px;
        }
        .label {
            width: 110px;
        }

        .separator {
            width: 10px;
        }
        /* .name{
            width: 180px;
        } */
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 10pt;
        }

        .main-table th {
            text-align: center;
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        .footer-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .criteria-box {
            font-size: 10pt;
            margin-top: 20px;
        }

        .signature-box {
            text-align: center;
            width: 250px;
        }

        .signature-space {
            height: 80px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>FAKULTAS ILMU KOMPUTER UNIVERSITAS SRIWIJAYA</h1>
        <h2>LEMBAR PENILAIAN SISWA MAGANG</h2>
        <h2>TAHUN PELAJARAN <?php echo e($siswa->tahunAjaran->tahun_ajaran ?? '2026/2027'); ?></h2>
    </div>

    <div class="line"></div>

    <table class="info-table">
        <tr>
            <td class="label1">Nama</td>
            <td class="separator1">:</td>
            <td class="name1"><?php echo e($siswa->nama); ?></td>
            <td class="label">Pembimbing</td>
            <td class="separator">:</td>
            <td class="name"><?php echo e($pembimbing->nama); ?></td>
        </tr>
        <tr>
            <td class="label1">Nisn</td>
            <td class="separator1">:</td>
            <td class="name1"><?php echo e($siswa->nisn); ?></td>
            <td class="label">Periode</td>
            <td class="separator">:</td>
            <td class="name">
                <?php if($siswa->tgl_mulai_magang && $siswa->tgl_selesai_magang): ?>
                    <?php echo e(\Carbon\Carbon::parse($siswa->tgl_mulai_magang)->translatedFormat('d F')); ?> -
                    <?php echo e(\Carbon\Carbon::parse($siswa->tgl_selesai_magang)->translatedFormat('d F Y')); ?>

                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="label1">Jurusan</td>
            <td class="separator1">:</td>
            <td class="name1"><?php echo e($siswa->jurusan); ?></td>
            <td class="label">Lokasi Magang</td>
            <td class="separator">:</td>
            <td class="name"><?php echo e($siswa->perusahaan); ?></td>
        </tr>
    </table>

    <?php
        $latestPenilaian = $penilaian ?? $siswa->penilaians->where('pemberi_nilai', 'Dosen Pembimbing')->first();
        $sikapDetails = $latestPenilaian ? $latestPenilaian->penilaianDetails->where('kriteria.tipe', 'sikap_kerja') : collect();
        $kompetensiDetails = $latestPenilaian ? $latestPenilaian->penilaianDetails->where('kriteria.tipe', 'kompetensi_keahlian') : collect();

        $avgSikap = $sikapDetails->avg('skor') ?? 0;
        $avgKompetensi = $kompetensiDetails->avg('skor') ?? 0;
        $cumulative = ($avgSikap + $avgKompetensi) / 2;
    ?>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Sikap Kerja</th>
                <th style="width: 60px;">Nilai</th>
                <th style="width: 30px;">No</th>
                <th>Kompetensi Keahlian</th>
                <th style="width: 60px;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $maxRows = max($sikapDetails->count(), $kompetensiDetails->count());
                $sikapArray = $sikapDetails->values();
                $kompetensiArray = $kompetensiDetails->values();
            ?>
            <?php for($i = 0; $i < $maxRows; $i++): ?>
                <tr>
                    <td class="text-center"><?php echo e(isset($sikapArray[$i]) ? $i + 1 : ''); ?></td>
                    <td><?php echo e(isset($sikapArray[$i]) ? $sikapArray[$i]->kriteria->nama_kriteria : ''); ?></td>
                    <td class="text-center"><?php echo e(isset($sikapArray[$i]) ? round($sikapArray[$i]->skor) : ''); ?></td>

                    <td class="text-center"><?php echo e(isset($kompetensiArray[$i]) ? $i + 1 : ''); ?></td>
                    <td><?php echo e(isset($kompetensiArray[$i]) ? $kompetensiArray[$i]->kriteria->nama_kriteria : ''); ?></td>
                    <td class="text-center"><?php echo e(isset($kompetensiArray[$i]) ? round($kompetensiArray[$i]->skor) : ''); ?></td>
                </tr>
            <?php endfor; ?>
            <tr>
                <td colspan="2" class="text-center fw-bold">Rata - Rata</td>
                <td class="text-center fw-bold"><?php echo e(number_format($avgSikap, 1)); ?></td>
                <td colspan="2" class="text-center fw-bold">Rata - Rata</td>
                <td class="text-center fw-bold"><?php echo e(number_format($avgKompetensi, 1)); ?></td>
            </tr>
            <tr>
                <td colspan="6" class="text-center" style="padding: 15px;">
                    <div style="display: table; width: 100%;">
                        <div style="display: table-row;">
                            <div style="display: table-cell; vertical-align: middle; width: 140px;" class="fw-bold">
                                Nilai Kumulatif = </div>
                            <div style="display: table-cell; vertical-align: middle;">
                                <div style="display: inline-block; vertical-align: middle; text-align: center;">
                                    <div style="border-bottom: 1px solid #000; padding: 0 10px;">Nilai rata-rata sikap
                                        kerja + nilai rata-rata kompetensi keahlian</div>
                                    <div>2</div>
                                </div>
                                <span class="fw-bold" style="margin-left: 15px; font-size: 14pt;">=
                                    <?php echo e(number_format($cumulative, 1)); ?></span>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="criteria-box">
                    <p class="mb-1 fw-bold">Keterangan :</p>
                    <p class="mb-1">Kriteria Nilai Rata - Rata :</p>
                    <table style="font-size: 10pt;">
                        <tr>
                            <td>A </td>
                            <td>= 91 - 100</td>
                        </tr>
                        <tr>
                            <td>B </td>
                            <td>= 75 - 90</td>
                        </tr>
                        <tr>
                            <td>C </td>
                            <td>= 60 - 74</td>
                        </tr>
                        <tr>
                            <td>D </td>
                            <td>= 59 - 0</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; text-align: center;">
                <div class="signature-box" style="margin-top: 0; display: inline-block; text-align: center;">
                    <p>Palembang, <?php echo e(\Carbon\Carbon::now()->translatedFormat('d F Y')); ?></p>
                    <p><?php echo e($latestPenilaian && $latestPenilaian->pemberi_nilai == 'Guru Pembimbing' ? 'Guru Pembimbing' : 'Pembimbing Siswa'); ?>

                    </p>
                    <div class="signature-space"></div>
                    <?php if($latestPenilaian && $latestPenilaian->pemberi_nilai == 'Guru Pembimbing'): ?>
                        <p class="fw-bold"><?php echo e($siswa->guru->nama ?? $pembimbing->nama); ?></p>
                        <p>NIP. <?php echo e($siswa->guru->id_guru ?? '-'); ?></p>
                    <?php else: ?>
                        <p class="fw-bold"><?php echo e($pembimbing->nama); ?></p>
                        <p>NIP. <?php echo e($pembimbing->id_pembimbing); ?></p>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

</body>

</html><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pembimbing/cetakPenilaian.blade.php ENDPATH**/ ?>