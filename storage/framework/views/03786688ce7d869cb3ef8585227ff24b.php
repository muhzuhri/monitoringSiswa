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
            line-height: 1.4;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 16pt;
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .main-table th {
            text-align: center;
            background-color: #ffffff;
            font-weight: bold;
        }
        .sub-header {
            background-color: #ffffff;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        
        .footer-info {
            width: 100%;
            margin-top: 20px;
        }
        .keterangan-nilai {
            font-size: 10pt;
            vertical-align: top;
        }
        .signatures {
            width: 100%;
            margin-top: 50px;
        }
        .signature-box {
            text-align: center;
            width: 40%;
            vertical-align: top;
        }
        .signature-space {
            height: 80px;
        }
    </style>
    <?php
        function getHuruf($nilai) {
            if ($nilai >= 90) return 'A';
            if ($nilai >= 80) return 'B';
            if ($nilai >= 60) return 'C';
            if ($nilai >= 10) return 'D';
            return 'E';
        }
    ?>
</head>
<body>
    <div class="header">
        <h1>FORM PENILAIAN SISWA MAGANG</h1>
    </div>

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
            <tr class="sub-header">
                <td></td>
                <td>KEPRIBADIAN/ETOS KERJA</td>
                <td></td>
                <td></td>
            </tr>
            <?php $i = 1; $totalSkor = 0; $count = 0; ?>
            <?php $__currentLoopData = $penilaian->penilaianDetails->where('kriteria.tipe', 'guru_kepribadian'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($i++); ?></td>
                    <td><?php echo e($detail->kriteria->nama_kriteria); ?></td>
                    <td class="text-center"><?php echo e(round($detail->skor)); ?></td>
                    <td class="text-center"><?php echo e(getHuruf($detail->skor)); ?></td>
                </tr>
                <?php $totalSkor += $detail->skor; $count++; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <tr class="sub-header">
                <td></td>
                <td>KEMAMPUAN</td>
                <td></td>
                <td></td>
            </tr>
            <?php $j = 1; ?>
            <?php $__currentLoopData = $penilaian->penilaianDetails->where('kriteria.tipe', 'guru_kemampuan'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($j++); ?></td>
                    <td><?php echo e($detail->kriteria->nama_kriteria); ?></td>
                    <td class="text-center"><?php echo e(round($detail->skor)); ?></td>
                    <td class="text-center"><?php echo e(getHuruf($detail->skor)); ?></td>
                </tr>
                <?php $totalSkor += $detail->skor; $count++; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <tr class="fw-bold">
                <td colspan="2" class="text-center">JUMLAH</td>
                <td class="text-center"><?php echo e(round($totalSkor)); ?></td>
                <td class="text-center">-</td>
            </tr>
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
                <strong>Keterangan Nilai</strong><br>
                A = 90 - 100<br>
                B = 80 - 89<br>
                C = 60 - 79<br>
                D = 10 - 59
            </td>
            <td style="width: 10%;"></td>
            <td class="signature-box">
                <p>Pembimbing Sekolah</p>
                <div class="signature-space"></div>
                <p>.........................................</p>
            </td>
            <td class="signature-box">
                <p>Ketua Program Studi Keahlian</p>
                <div class="signature-space"></div>
                <p>.........................................</p>
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/guru/printPenilaian.blade.php ENDPATH**/ ?>