<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Penilaian Siswa Magang - <?php echo e($siswa->nama); ?></title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
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
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 6px 10px;
        }
        .main-table th {
            text-align: center;
            background-color: #ffffff;
            font-weight: bold;
        }
        .category-header {
            background-color: #ffffff;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }
        
        .footer-info {
            width: 100%;
            margin-top: 20px;
        }
        .keterangan-nilai {
            font-size: 10pt;
            vertical-align: top;
            width: 30%;
        }
        .signature-section {
            width: 100%;
            margin-top: 30px;
        }
        .signature-box {
            text-align: center;
            width: 50%;
            vertical-align: top;
        }
        .signature-space {
            height: 70px;
        }
        .label-nip {
             display: inline-block;
             width: 30px;
             text-align: left;
        }
    </style>
    <?php
        if(!function_exists('getHuruf')) {
            function getHuruf($nilai) {
                if ($nilai >= 90) return 'A';
                if ($nilai >= 80) return 'B';
                if ($nilai >= 60) return 'C';
                if ($nilai >= 10) return 'D';
                return 'E';
            }
        }
    ?>
</head>
<body>
    <div class="header">
        <h1>FAKULTAS ILMU KOMPUTER UNIVERSITAS SRIWIJAYA</h1>
        <h2>LEMBAR PENILAIAN SISWA MAGANG</h2>
        <h4 style="margin: 2px 0;">TAHUN PELAJARAN <?php echo e($siswa->tahunAjaran->tahun_ajaran ?? '-'); ?></h4>
    </div>
    <div class="line"></div>

    <table style="width: 100%; margin-bottom: 15px; font-size: 11pt;">
        <tr>
            <td style="width: 100px;">Nama Siswa</td>
            <td style="width: 10px;">:</td>
            <td style="font-weight: bold;"><?php echo e($siswa->nama); ?></td>
            <td style="width: 100px;">NISN</td>
            <td style="width: 10px;">:</td>
            <td><?php echo e($siswa->nisn); ?></td>
        </tr>
        <tr>
            <td>Sekolah</td>
            <td>:</td>
            <td><?php echo e($siswa->sekolah); ?></td>
            <td>Lokasi Magang</td>
            <td>:</td>
            <td><?php echo e($siswa->perusahaan); ?></td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 40px;">No</th>
                <th rowspan="2">UNSUR YANG DINILAI</th>
                <th colspan="2">NILAI</th>
            </tr>
            <tr>
                <th style="width: 80px;">ANGKA</th>
                <th style="width: 120px;">HURUF</th>
            </tr>
        </thead>
        <tbody>
            <tr class="category-header">
                <td></td>
                <td>KEPRIBADIAN / ETOS KERJA</td>
                <td></td>
                <td></td>
            </tr>
            <?php $i = 1; $kepribadianItems = $penilaian->penilaianDetails->where('kriteria.tipe', 'guru_kepribadian'); ?>
            <?php $__currentLoopData = $kepribadianItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($i++); ?></td>
                    <td><?php echo e($detail->kriteria->nama_kriteria); ?></td>
                    <td class="text-center"><?php echo e(number_format($detail->skor, 0)); ?></td>
                    <td class="text-center"><?php echo e(getHuruf($detail->skor)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <tr class="category-header">
                <td></td>
                <td>KEMAMPUAN</td>
                <td></td>
                <td></td>
            </tr>
            <?php $j = 1; $kemampuanItems = $penilaian->penilaianDetails->where('kriteria.tipe', 'guru_kemampuan'); ?>
            <?php $__currentLoopData = $kemampuanItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($j++); ?></td>
                    <td><?php echo e($detail->kriteria->nama_kriteria); ?></td>
                    <td class="text-center"><?php echo e(number_format($detail->skor, 0)); ?></td>
                    <td class="text-center"><?php echo e(getHuruf($detail->skor)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <tr class="fw-bold">
                <td colspan="2" class="text-center">NILAI RATA-RATA</td>
                <td class="text-center"><?php echo e(number_format($penilaian->rata_rata, 1)); ?></td>
                <td class="text-center"><?php echo e(getHuruf($penilaian->rata_rata)); ?></td>
            </tr>
        </tbody>
    </table>

    <table class="footer-info">
        <tr>
            <td class="keterangan-nilai">
                <strong>Keterangan Nilai :</strong><br>
                A = 90 - 100<br>
                B = 80 - 89<br>
                C = 60 - 79<br>
                D = 10 - 59
            </td>
            <td class="signature-box">
                <p>Pembimbing Sekolah,</p>
                <div class="signature-space"></div>
                <p style="text-decoration: underline; font-weight: bold; margin-bottom: 2px;"><?php echo e($siswa->guru->nama ?? '.........................................'); ?></p>
                <p style="margin-top: 0;">NIP. <?php echo e($siswa->guru->id_guru ?? '................................'); ?></p>
            </td>
            <td class="signature-box">
                <p>Pembimbing Instansi,</p>
                <div class="signature-space"></div>
                <p style="text-decoration: underline; font-weight: bold; margin-bottom: 2px;"><?php echo e($siswa->pembimbing->nama ?? '.........................................'); ?></p>
                <p style="margin-top: 0;">NIP. <?php echo e($siswa->pembimbing->id_pembimbing ?? '................................'); ?></p>
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/guru/printPenilaian.blade.php ENDPATH**/ ?>