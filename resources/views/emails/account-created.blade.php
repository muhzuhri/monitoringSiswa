<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Berhasil Dibuat</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0d6efd; color: #fff; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f8f9fa; padding: 24px; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 8px 8px; }
        .highlight { background: #e7f1ff; padding: 12px; border-radius: 6px; margin: 16px 0; border-left: 4px solid #0d6efd; }
        .footer { margin-top: 24px; font-size: 12px; color: #6c757d; }
        a { color: #0d6efd; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">Monitoring Siswa Magang</h1>
        <p style="margin: 8px 0 0 0; opacity: 0.9;">Notifikasi Registrasi</p>
    </div>
    <div class="content">
        <p>Halo <strong>{{ $name }}</strong>,</p>
        <p>Akun Anda telah berhasil dibuat di aplikasi <strong>Monitoring Siswa Magang</strong>.</p>
        <div class="highlight">
            <strong>Detail akun:</strong><br>
            Email: {{ $email }}<br>
            Peran: {{ $roleLabel }}
        </div>
        <p>Anda sekarang dapat masuk (login) menggunakan email dan password yang Anda daftarkan.</p>
        <p>Jika Anda tidak merasa mendaftar, abaikan email ini.</p>
        <div class="footer">
            Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
        </div>
    </div>
</body>
</html>
