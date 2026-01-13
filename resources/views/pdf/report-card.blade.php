<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Belajar</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 5px 0 0 0;
        }
        .header p {
            font-size: 11pt;
            margin: 3px 0 0 0;
        }
        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 180px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px 10px;
            text-align: left;
            vertical-align: middle;
        }
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        table td.center {
            text-align: center;
        }
        .summary-section {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        .summary-box {
            width: 48%;
            border: 1px solid #000;
            padding: 10px;
        }
        .summary-box h3 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0 0 10px 0;
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 30%;
            text-align: center;
        }
        .signature-box .name {
            font-weight: bold;
            margin-top: 60px;
        }
        .signature-box .title {
            font-size: 11pt;
            margin-top: 5px;
        }
        .grade-a { background-color: #d4edda; }
        .grade-b { background-color: #fff3cd; }
        .grade-c { background-color: #fff3cd; }
        .grade-d { background-color: #f8d7da; }
        .grade-e { background-color: #f8d7da; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10pt;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $schoolName }}</h1>
        <h2>LAPORAN HASIL BELAJAR</h2>
        <p>Tahun Ajaran {{ $reportCard->semester->academicYear->name }}</p>
    </div>

    <!-- Title -->
    <div class="title">RAPORT SEMESTER {{ strtoupper($reportCard->semester->name) }}</div>

    <!-- Student Information -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Nama Siswa</span>
            <span class="info-value">: {{ $reportCard->student->full_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">NIS / NISN</span>
            <span class="info-value">: {{ $reportCard->student->nis }} {{ $reportCard->student->nisn ? '/ ' . $reportCard->student->nisn : '' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Kelas</span>
            <span class="info-value">: {{ $reportCard->student->schoolClass->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Semester</span>
            <span class="info-value">: {{ $reportCard->semester->name }} ({{ $reportCard->semester->start_date->format('d/m/Y') }} - {{ $reportCard->semester->end_date->format('d/m/Y') }})</span>
        </div>
    </div>

    <!-- Grades Table -->
    <h3 style="text-align: center; font-size: 12pt; font-weight: bold; margin: 15px 0;">NILAI AKADEMIK</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Mata Pelajaran</th>
                <th style="width: 15%;">Nilai Akhir</th>
                <th style="width: 10%;">Predikat</th>
                <th style="width: 35%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @php $totalScore = 0; $subjectCount = 0; @endphp
            @foreach($reportCard->items as $index => $item)
                @php
                    $totalScore += $item->final_score;
                    $subjectCount++;
                    $gradeClass = 'grade-' . strtolower($item->predicate ?: 'e');
                @endphp
                <tr class="{{ $gradeClass }}">
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->subject->name }}</td>
                    <td class="center"><strong>{{ number_format($item->final_score, 1) }}</strong></td>
                    <td class="center">{{ $item->predicate ?: '-' }}</td>
                    <td>{{ $item->notes ?? '-' }}</td>
                </tr>
            @endforeach
            @if($subjectCount > 0)
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="2" class="center">Rata-rata</td>
                    <td class="center">{{ number_format($totalScore / $subjectCount, 1) }}</td>
                    <td colspan="2"></td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Summary Sections -->
    <div class="summary-section">
        <!-- Attendance Summary -->
        <div class="summary-box">
            <h3>REKAP KEHADIRAN</h3>
            <table style="margin: 0; font-size: 11pt;">
                <tr>
                    <td style="width: 60%;">Hadir</td>
                    <td class="center">{{ $attendanceSummary['hadir'] }} hari</td>
                </tr>
                <tr>
                    <td>Izin</td>
                    <td class="center">{{ $attendanceSummary['izin'] }} hari</td>
                </tr>
                <tr>
                    <td>Sakit</td>
                    <td class="center">{{ $attendanceSummary['sakit'] }} hari</td>
                </tr>
                <tr>
                    <td>Tanpa Keterangan (Alfa)</td>
                    <td class="center">{{ $attendanceSummary['alfa'] }} hari</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td>Total</td>
                    <td class="center">{{ $attendanceSummary['total'] }} hari</td>
                </tr>
            </table>
        </div>

        <!-- Grade Statistics -->
        <div class="summary-box">
            <h3>STATISTIK NILAI</h3>
            <table style="margin: 0; font-size: 11pt;">
                <tr>
                    <td style="width: 60%;">Nilai Tertinggi</td>
                    <td class="center">{{ number_format($gradeStats['highest'], 1) }}</td>
                </tr>
                <tr>
                    <td>Nilai Terendah</td>
                    <td class="center">{{ number_format($gradeStats['lowest'], 1) }}</td>
                </tr>
                <tr>
                    <td>Rata-rata Kelas</td>
                    <td class="center">{{ number_format($gradeStats['average'], 1) }}</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td>Jumlah Mata Pelajaran</td>
                    <td class="center">{{ $gradeStats['total_subjects'] }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Remarks -->
    @if($reportCard->remarks)
        <div style="margin-top: 15px; border: 1px solid #000; padding: 10px;">
            <strong>Catatan Wali Kelas:</strong><br>
            {{ $reportCard->remarks }}
        </div>
    @endif

    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <div class="name"></div>
            <div class="title">(........................................)</div>
        </div>
        <div class="signature-box">
            <p>{{ $generatedAt }}</p>
            <p>Wali Kelas</p>
            <div class="name"></div>
            <div class="title">(........................................)</div>
        </div>
        <div class="signature-box">
            <p>Mengetahui,<br>Orang Tua/Wali</p>
            <div class="name"></div>
            <div class="title">(........................................)</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini diterbitkan secara elektronik oleh {{ $schoolName }} pada {{ $generatedAt }}</p>
        <p>Status: {{ strtoupper($reportCard->status) }}{{ $reportCard->published_at ? ' | Dipublikasi: ' . $reportCard->published_at->format('d/m/Y H:i') : '' }}</p>
    </div>
</body>
</html>