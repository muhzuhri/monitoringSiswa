<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Magang - <?php echo e($user->nama); ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            background-color: #fff;
        }
        .cert-container {
            width: 297mm;
            height: 209mm;
            position: relative;
            background-color: <?php echo e($konfigurasi->background_color ?? '#fdfaf2'); ?>;
            overflow: hidden;
        }

        /* Decorative Shapes */
        .shape { position: absolute; z-index: 1; }
        .shape-tl { top: -110px; left: -110px; width: 300px; height: 300px; background-color: <?php echo e($konfigurasi->color_primary ?? '#1a5fb4'); ?>; transform: rotate(45deg); }
        .shape-tl-accent { top: 30px; left: -130px; width: 240px; height: 50px; background-color: <?php echo e($konfigurasi->color_secondary ?? '#fbc02d'); ?>; transform: rotate(45deg); z-index: 2; }
        .shape-br { bottom: -110px; right: -110px; width: 300px; height: 300px; background-color: <?php echo e($konfigurasi->color_primary ?? '#1a5fb4'); ?>; transform: rotate(45deg); }
        .shape-br-accent { bottom: 30px; right: -130px; width: 240px; height: 50px; background-color: <?php echo e($konfigurasi->color_secondary ?? '#fbc02d'); ?>; transform: rotate(45deg); z-index: 2; }

        .frame {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 3px solid <?php echo e($konfigurasi->color_primary ?? '#1a5fb4'); ?>;
            z-index: 5;
        }

        .content {
            position: relative;
            z-index: 10;
            padding: 40px 100px;
            text-align: center;
        }

        .header-table { width: 100%; margin-bottom: 5px; }
        .logo-box { width: 100px; }
        
        .header-text h1 { margin: 0; font-size: 20px; font-weight: bold; color: #000; text-transform: uppercase; }
        .header-text h2 { margin: 2px 0; font-size: 24px; font-weight: 900; color: #000; }
        .header-text p { margin: 0; font-size: 14px; color: #333; }

        .divider { width: 85%; height: 2px; background-color: <?php echo e($konfigurasi->color_secondary ?? '#fbc02d'); ?>; margin: 10px auto; }

        .main-title { font-size: 54px; font-weight: 900; margin: 15px 0 5px 0; color: #000; letter-spacing: 5px; }

        .cert-number { background-color: <?php echo e($konfigurasi->color_primary ?? '#1a5fb4'); ?>; color: #fff; padding: 4px 20px; font-size: 14px; display: inline-block; margin-bottom: 20px; font-weight: bold; }

        .given-text { font-size: 19px; font-style: italic; margin-bottom: 8px; }

        .student-name { font-size: 40px; font-weight: bold; color: #000; margin: 10px 0; display: inline-block; border-bottom: 2px dotted #000; padding-bottom: 5px; min-width: 500px; text-transform: uppercase; }

        .description { font-size: 17px; line-height: 1.6; margin: 15px auto; max-width: 850px; color: #111; }

        .footer { margin-top: 30px; text-align: center; }
        .signature-block { display: inline-block; text-align: center; }
        .date-location { font-size: 16px; margin-bottom: 10px; }
        .signer-name { font-size: 19px; font-weight: bold; text-decoration: underline; }
        .signer-nip { font-size: 15px; margin-top: 2px; }

        .tutwuri{
            padding-right: 80px;
        }
        .unsri{
            padding-left: 80px;
        }
        .watermark { position: absolute; top: 52%; left: 50%; transform: translate(-50%, -50%); width: 400px; opacity: 0.1; z-index: 0; }
    </style>
</head>
<body>
    <div class="cert-container">
        <div class="shape shape-tl"></div>
        <div class="shape shape-tl-accent"></div>
        <div class="shape shape-br"></div>
        <div class="shape shape-br-accent"></div>
        <div class="frame"></div>
        <?php $unsriLogo = public_path('images/unsri-pride.png'); ?>
        <?php if(file_exists($unsriLogo)): ?>
            <img src="<?php echo e($unsriLogo); ?>" class="watermark">
        <?php endif; ?>
        <div class="content">
            <table class="header-table">
                <tr>
                    <td class="logo-box unsri"><?php if(file_exists($unsriLogo)): ?> <img src="<?php echo e($unsriLogo); ?>" width="100"> <?php endif; ?></td>
                    <td class="header-text" align="center">
                        <h1>Universitas Sriwijaya</h1>
                        <h2>FAKULTAS ILMU KOMPUTER</h2>
                        <p>www.ilkom.unsri.ac.id</p>
                    </td>
                    <td class="logo-box tutwuri" align="right">
                        <?php $tutWuriLogo = public_path('images/logo-tutwuri.png'); ?>
                        <?php if(file_exists($tutWuriLogo)): ?> <img src="<?php echo e($tutWuriLogo); ?>" width="100"> <?php endif; ?>
                    </td>
                </tr>
            </table>
            <div class="divider"></div>
            <h1 class="main-title"><?php echo e($konfigurasi->header_1 ?? 'SERTIFIKAT'); ?></h1>
            <div class="given-text"><?php echo e($konfigurasi->header_2 ?? 'DIBERIKAN KEPADA :'); ?></div>
            <div class="student-name"><?php echo e($user->nama); ?></div>
            <div class="description">
                <?php if(isset($konfigurasi->template_isi)): ?>
                    <?php
                        $tglMulai = \Carbon\Carbon::parse($user->tgl_mulai_magang)->translatedFormat('d F Y');
                        $tglSelesai = \Carbon\Carbon::parse($user->tgl_selesai_magang)->translatedFormat('d F Y');
                        $desc = str_replace('{tgl_mulai}', $tglMulai, $konfigurasi->template_isi);
                        $desc = str_replace('{tgl_selesai}', $tglSelesai, $desc);
                        echo $desc;
                    ?>
                <?php else: ?>
                    Atas partisipasinya sebagai Peserta Magang di Fakultas Ilmu Komputer Universitas Sriwijaya selama periode&nbsp; 
                    <strong><?php echo e(\Carbon\Carbon::parse($user->tgl_mulai_magang)->translatedFormat('d F Y')); ?></strong> sampai dengan 
                    <strong><?php echo e(\Carbon\Carbon::parse($user->tgl_selesai_magang)->translatedFormat('d F Y')); ?></strong>, 
                    serta telah menyelesaikan kegiatan magang dengan baik dan menunjukkan kinerja yang disiplin dan bertanggung jawab.
                <?php endif; ?>
            </div>
            <div class="footer">
                <div class="signature-block">
                    <div class="date-location">Palembang, <?php echo e(\Carbon\Carbon::parse($user->tgl_selesai_magang)->translatedFormat('d F Y')); ?></div>
                    <div style="font-size: 16px;"><?php echo e($konfigurasi->header_3 ?? 'Dekan,'); ?></div>
                    <br><br><br><br><br>
                    <div class="signer-name"><?php echo e($konfigurasi->header_4 ?? 'Prof. Dr. Erwin, S.Si., M.Si.'); ?></div>
                    <div class="signer-nip"><?php echo e($konfigurasi->header_5 ?? 'NIP. 197412122000031002'); ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/siswa/sertifikat.blade.php ENDPATH**/ ?>