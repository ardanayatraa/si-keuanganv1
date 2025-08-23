<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Anggaran</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .generated-info {
            text-align: right;
            margin-bottom: 20px;
            font-size: 10px;
            color: #666;
        }
        
        .summary {
            margin-bottom: 25px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-item {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }
        
        .summary-item h3 {
            font-size: 14px;
            margin: 0 0 8px 0;
            font-weight: bold;
        }
        
        .summary-item .amount {
            font-size: 16px;
            font-weight: bold;
        }
        
        .table-container {
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 8px 4px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .status-safe {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .status-warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .status-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .progress-bar {
            width: 50px;
            height: 8px;
            background-color: #e9ecef;
            border: 1px solid #000;
            position: relative;
            display: inline-block;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #666;
        }
        
        .footer-info {
            margin-top: 30px;
            font-size: 10px;
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }
        
        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
            padding: 20px;
        }
        
        .text-red {
            color: #dc3545;
        }
        
        .text-green {
            color: #28a745;
        }
        
        .font-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN ANGGARAN</h1>
        <p>Sistem Keuangan Personal</p>
        <p>
            Periode: {{ $bulan ? 
                \Carbon\Carbon::createFromFormat('m', $bulan)->format('F') . ' ' . $tahun : 
                'Tahun ' . $tahun 
            }}
        </p>
    </div>
    
    <div class="generated-info">
        <p>Dicetak pada: {{ $generated_at }} WIB</p>
    </div>
    
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <h3>Total Anggaran</h3>
                <div class="amount">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <h3>Total Terpakai</h3>
                <div class="amount text-red">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <h3>Sisa Anggaran</h3>
                <div class="amount {{ $totalSisa >= 0 ? 'text-green' : 'text-red' }}">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    
    <div class="table-container">
        @if($laporan->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Kategori</th>
                        <th style="width: 15%;">Periode</th>
                        <th style="width: 12%;">Anggaran</th>
                        <th style="width: 12%;">Terpakai</th>
                        <th style="width: 12%;">Sisa</th>
                        <th style="width: 8%;">%</th>
                        <th style="width: 10%;">Progress</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 16%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporan as $item)
                        <tr>
                            <td>{{ $item->kategori->nama_kategori ?? 'N/A' }}</td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($item->periode_awal)->format('d/m/Y') }} - 
                                {{ \Carbon\Carbon::parse($item->periode_akhir)->format('d/m/Y') }}
                            </td>
                            <td class="text-right">{{ number_format($item->jumlah_batas, 0, ',', '.') }}</td>
                            <td class="text-right text-red">{{ number_format($item->total_realisasi, 0, ',', '.') }}</td>
                            <td class="text-right {{ $item->sisa_anggaran >= 0 ? 'text-green' : 'text-red' }}">
                                {{ number_format($item->sisa_anggaran, 0, ',', '.') }}
                            </td>
                            <td class="text-center">{{ number_format($item->persentase_terpakai, 1) }}%</td>
                            <td class="text-center">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ min($item->persentase_terpakai, 100) }}%"></div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($item->persentase_terpakai > 100)
                                    <span class="status-danger">Melebihi Batas</span>
                                @elseif($item->persentase_terpakai > 80)
                                    <span class="status-warning">Peringatan</span>
                                @else
                                    <span class="status-safe">Aman</span>
                                @endif
                            </td>
                            <td>{{ $item->deskripsi ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Tidak ada data anggaran untuk periode yang dipilih.
            </div>
        @endif
    </div>
    
    <div class="footer-info">
        <p><strong>Periode Laporan:</strong> 
            {{ $bulan ? 
                \Carbon\Carbon::createFromFormat('m', $bulan)->format('F') . ' ' . $tahun : 
                'Semua bulan tahun ' . $tahun 
            }}
        </p>
        <p><strong>Total {{ count($laporan) }} anggaran</strong> ditemukan.</p>
        @if($totalRealisasi > $totalAnggaran)
            <p class="text-red font-bold">⚠️ Peringatan: Total pengeluaran melebihi anggaran sebesar Rp {{ number_format($totalRealisasi - $totalAnggaran, 0, ',', '.') }}</p>
        @endif
        <p style="margin-top: 15px; font-size: 9px; color: #999;">
            Laporan ini digenerate secara otomatis oleh Sistem Keuangan Personal pada {{ $generated_at }} WIB
        </p>
    </div>
</body>
</html>
