<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo e($title); ?></title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #444;
            padding-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
            color: #0d6efd;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12px;
        }
        .meta {
            margin-bottom: 20px;
        }
        .meta table {
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-align: left;
            padding: 10px 8px;
            border: 1px solid #e2e8f0;
            text-transform: uppercase;
            font-size: 10px;
        }
        table td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-aktif { background-color: #dcfce7; color: #166534; }
        .status-selesai { background-color: #f1f5f9; color: #475569; }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            width: 200px;
            text-align: center;
        }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="header">
        <h2><?php echo e($title); ?></h2>
        <p>Program Magang Mahasiswa / Siswa - Monitoring System</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td style="border:none; padding:0;">
                    Tanggal Cetak: <strong><?php echo e($date); ?></strong><br>
                    <?php if($tahun_ajaran): ?>
                        Tahun Ajaran: <strong><?php echo e($tahun_ajaran); ?></strong>
                    <?php else: ?>
                        Tahun Ajaran: <strong>Semua Periode</strong>
                    <?php endif; ?>
                </td>
                <td style="border:none; padding:0; text-align:right;">Jumlah Data: <strong><?php echo e(count($items)); ?></strong></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">Noo</th>
                <th width="80">NISN</th>
                <th>Nama Lengkap</th>
                <th>Asal Sekolah / Instansi</th>
                <th>Periode Magang</th>
                <th width="70">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td align="center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($s->nisn); ?></td>
                    <td style="font-weight: bold;"><?php echo e($s->nama); ?></td>
                    <td><?php echo e($s->sekolah); ?></td>
                    <td>
                        <?php if($s->tgl_mulai_magang && $s->tgl_selesai_magang): ?>
                            <?php echo e(\Carbon\Carbon::parse($s->tgl_mulai_magang)->translatedFormat('M Y')); ?> - 
                            <?php echo e(\Carbon\Carbon::parse($s->tgl_selesai_magang)->translatedFormat('M Y')); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td align="center">
                        <span class="status-badge <?php echo e($s->status == 'selesai' ? 'status-selesai' : 'status-aktif'); ?>">
                            <?php echo e($s->status ?? 'Aktif'); ?>

                        </span>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Palembang, <?php echo e($date); ?></p>
            <p style="margin-bottom: 60px;">Administrator Sistem,</p>
            <p><strong>( ____________________ )</strong></p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/admin/pdf/rekapSiswa.blade.php ENDPATH**/ ?>