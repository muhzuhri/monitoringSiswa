<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
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
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $title }}</h2>
        <p>Monitoring Sistem - Guru Pembimbing Magang</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td style="border:none; padding:0;">
                    Tanggal Cetak: <strong>{{ $date }}</strong><br>
                    @if($tahun_ajaran)
                        Tahun Ajaran: <strong>{{ $tahun_ajaran }}</strong>
                    @else
                        Tahun Ajaran: <strong>Semua Periode</strong>
                    @endif
                </td>
                <td style="border:none; padding:0; text-align:right;">Total Guru: <strong>{{ count($items) }}</strong></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="100">NIP / ID</th>
                <th>Nama Lengkap</th>
                <th>Jabatan</th>
                <th>Unit Kerja / Sekolah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $g)
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td>{{ $g->id_guru }}</td>
                    <td style="font-weight: bold;">{{ $g->nama }}</td>
                    <td>{{ $g->jabatan ?? '-' }}</td>
                    <td>{{ $g->sekolah }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Palembang, {{ $date }}</p>
            <p style="margin-bottom: 60px;">Administrator Sistem,</p>
            <p><strong>( ____________________ )</strong></p>
        </div>
    </div>
</body>
</html>
