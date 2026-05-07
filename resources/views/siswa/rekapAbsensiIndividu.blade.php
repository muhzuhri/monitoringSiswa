<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Individu - {{ $user->nama }}</title>
    <link rel="stylesheet" href="{{ public_path('assets/css/siswa/rekapAbsensiIndividu.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>REKAP ABSENSI SISWA</h2>
            <h3>PROGRAM PRAKTIK KERJA LAPANGAN (PKL)</h3>
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
                    <th width="25%">Tanggal</th>
                    <th width="35%">Presensi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensis as $index => $absen)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d F Y') }}</td>
                        <td class="text-center">
                            @php
                                $statusDisplay = $absen->status;
                                if ($statusDisplay === 'terlambat') {
                                    $statusDisplay = 'hadir';
                                }
                            @endphp
                            {{ ucfirst($statusDisplay) }}
                        </td>
                        <td class="text-center">
                            {{ ucfirst($absen->verifikasi ?? 'pending') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data absensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
            <table class="data-table" style="width: 50%;">
                <thead>
                    <tr>
                        <th colspan="2">Ringkasan Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Hadir</td>
                        <td class="text-center">{{ $rekapAbsensi['hadir'] }}</td>
                    </tr>
                    <tr>
                        <td>Izin</td>
                        <td class="text-center">{{ $rekapAbsensi['izin'] }}</td>
                    </tr>
                    <tr>
                        <td>Sakit</td>
                        <td class="text-center">{{ $rekapAbsensi['sakit'] }}</td>
                    </tr>
                    <tr>
                        <td>Alpa</td>
                        <td class="text-center">{{ $rekapAbsensi['alpa'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

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
