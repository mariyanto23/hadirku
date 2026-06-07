<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Presensi</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            color: #0f172a;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.45;
            margin: 24px;
        }

        h1 {
            font-size: 20px;
            margin: 0 0 4px;
        }

        .muted {
            color: #64748b;
        }

        .header {
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 16px;
            padding-bottom: 12px;
        }

        .filters {
            margin-top: 10px;
        }

        .chip {
            background: #eff6ff;
            border-radius: 999px;
            color: #1d4ed8;
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            margin: 4px 4px 0 0;
            padding: 4px 8px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-size: 10px;
            text-align: left;
            text-transform: uppercase;
        }

        th,
        td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            vertical-align: top;
        }

        .status {
            font-weight: 700;
        }

        .empty {
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            color: #64748b;
            font-weight: 700;
            padding: 24px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekap Presensi</h1>
        <div class="muted">HadirKu - SDN 01 Jatipurwo</div>
        <div class="muted">Dicetak: {{ $generatedAt }}</div>

        <div class="filters">
            @forelse($filters as $filter)
                <span class="chip">{{ $filter }}</span>
            @empty
                <span class="chip">Semua data</span>
            @endforelse
        </div>
    </div>

    @if($attendances->isEmpty())
        <div class="empty">
            Tidak ada presensi untuk filter yang dipilih.
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Status</th>
                    <th>Persetujuan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->student->user->name }}</td>
                        <td>{{ $attendance->student->nis }}</td>
                        <td>{{ $attendance->student->class->name }}</td>
                        <td>{{ $attendance->attendance_date?->translatedFormat('d F Y') }}</td>
                        <td>{{ substr((string) $attendance->attendance_time, 0, 5) }}</td>
                        <td class="status">
                            @switch($attendance->status)
                                @case('hadir')
                                    Hadir
                                    @break
                                @case('terlambat')
                                    Terlambat
                                    @break
                                @case('izin')
                                    Izin
                                    @break
                                @case('sakit')
                                    Sakit
                                    @break
                                @case('alpha')
                                    Alpa
                                    @break
                                @default
                                    -
                            @endswitch
                        </td>
                        <td>
                            @switch($attendance->approval_status)
                                @case('pending')
                                    Menunggu
                                    @break
                                @case('rejected')
                                    Ditolak
                                    @break
                                @default
                                    Disetujui
                            @endswitch
                        </td>
                        <td>{{ $attendance->notes ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
