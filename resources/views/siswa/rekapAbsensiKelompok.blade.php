<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Berkelompok - {{ $user->nama }}</title>
    <link rel="stylesheet" href="{{ public_path('assets/css/siswa/rekapAbsensiKelompok.css') }}">
</head>
<body>
@foreach($months as $monthData)
    @php
        $month = $monthData['month'];
        $year = $monthData['year'];
        $anggota = $monthData['anggota'];
        $daysInMonth = $monthData['daysInMonth'];
        $monthName = $monthData['monthName'];
    @endphp
    <div class="container {{ !$loop->last ? 'page-break' : '' }}">
        <div class="header">
            <h3>ABSENSI SISWA MAGANG / PRAKERIN</h3>
            <h3>PROGRAM STUDI KEAHLIAN TEKNIK JARINGAN KOMPUTER DAN TELEKOMUNIKASI</h3>
            <h2>{{ $user->sekolah }}</h2>
            <p>TAHUN PELAJARAN {{ $year }}/{{ $year + 1 }}</p>
        </div>

        <div class="info-section">
            <div class="info-left">
                <strong>Bulan : {{ $monthName }} {{ $year }}</strong>
            </div>
            <div class="info-right">
                <strong>Nama Instansi / DUDI : {{ $user->perusahaan }}</strong>
            </div>
        </div>

        <div class="rekap-bulan-title">
            REKAP ABSENSI BULAN: {{ $monthName }} {{ $year }}
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2">NO</th>
                    <th rowspan="2">NAMA SISWA</th>
                    <th rowspan="2">L/P</th>
                    <th rowspan="2">Kelas</th>
                    <th colspan="{{ $daysInMonth }}">TANGGAL</th>
                </tr>
                <tr>
                    @for($i = 1; $i <= $daysInMonth; $i++)
                        <th class="day-cell">{{ $i }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($anggota as $idx => $sis)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="name-cell">{{ $sis->nama }}</td>
                        <td class="text-center">{{ $sis->jenis_kelamin }}</td>
                        <td class="text-center">{{ $sis->kelas }}</td>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $date = \Carbon\Carbon::createFromDate($year, $month, $d);
                                $isWeekend = $date->isWeekend();
                                $absen = $sis->absensis->firstWhere('tanggal', $date->toDateString());
                                $statusChar = '';
                                $class = $isWeekend ? 'status-holiday' : '';

                                if ($absen) {
                                    $statusMapping = [
                                        'hadir' => 'H',
                                        'terlambat' => 'H',
                                        'sakit' => 'S',
                                        'izin' => 'I',
                                        'alpa' => 'A'
                                    ];
                                    $statusChar = $statusMapping[$absen->status] ?? strtoupper(substr($absen->status, 0, 1));
                                    
                                    if (!$isWeekend) {
                                        $class = 'status-' . strtolower(substr($absen->status, 0, 1));
                                    }
                                } elseif (!$isWeekend && $date->lte(\Carbon\Carbon::today())) {
                                    // CHECK: Internship start date
                                    $internStart = $sis->tgl_mulai_magang ? \Carbon\Carbon::parse($sis->tgl_mulai_magang)->startOfDay() : null;
                                    
                                    if ($internStart && $date->gte($internStart) && $date->lt(\Carbon\Carbon::today())) {
                                        $statusChar = 'A';
                                        $class = 'status-a';
                                    }
                                }
                            @endphp
                            <td class="day-cell {{ $class }}">{{ $statusChar }}</td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <div class="footer-content">
                <p>Palembang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p>Pembimbing Lapangan</p>
                <br><br><br><br>
                <p><strong>{{ $user->pembimbing->nama ?? '....................................................................' }}</strong></p>
            </div>
            <div class="clr-both"></div>
        </div>
    </div>
@endforeach
</body>
</html>
