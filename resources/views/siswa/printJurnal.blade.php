<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Kegiatan Mingguan - {{ $user->nama }}</title>
    <link rel="stylesheet" href="{{ public_path('assets/css/siswa/printJurnal.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ $konfigurasi->header_1 ?? 'JURNAL KEGIATAN MINGGUAN SISWA' }}</h2>
            <h3>{{ $konfigurasi->header_2 ?? 'PROGRAM PRAKTIK KERJA LAPANGAN (PKL)' }}</h3>
        </div>

        <table class="info-table">
            <tr>
                <td width="15%"><strong>Nama Siswa</strong></td>
                <td width="35%">: {{ $user->nama }}</td>
                <td width="15%"><strong>Sekolah</strong></td>
                <td>: {{ $user->sekolah }}</td>
            </tr>
            <tr>
                <td><strong>NISN</strong></td>
                <td>: {{ $user->nisn }}</td>
                <td><strong>Perusahaan</strong></td>
                <td>: {{ $user->perusahaan }}</td>
            </tr>
            <tr>
                <td><strong>Kelas/Jurusan</strong></td>
                <td>: {{ $user->kelas }} / {{ $user->jurusan }}</td>
                <td><strong>Pembimbing</strong></td>
                <td>: {{ $user->pembimbing->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Dicetak Pada</strong></td>
                <td>: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Tanggal</th>
                    <th>Kegiatan / Pekerjaan</th>
                    <th width="15%">Status Jurnal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logbooks as $index => $log)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('d M Y') }}</td>
                        <td>{{ $log->kegiatan }}</td>
                        <td class="text-center">{{ ucfirst($log->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data kegiatan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <div class="footer-content">
                <p>Palembang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p>Mengetahui,</p>
                <br><br><br>
                <p><strong>{{ $user->pembimbing->nama ?? '....................................' }}</strong></p>
            </div>
            <div class="clr-both"></div>
        </div>
    </div>
</body>
</html>
