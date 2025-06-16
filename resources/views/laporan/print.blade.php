<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0
        }

        .header {
            text-align: center;
            margin-bottom: 20px
        }

        .header h2 {
            margin: 0;
            font-size: 18px
        }

        .header p {
            margin: 2px 0;
            font-size: 12px
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left
        }

        .text-right {
            text-align: right
        }

        h3 {
            margin-top: 20px;
            font-size: 14px
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN KEUANGAN</h2>
        <p>Periode: {{ $laporan->periode }}</p>
        <p>Dicetak: {{ $generated_at }}</p>
    </div>

    {{-- Ringkasan --}}
    <table>
        <thead>
            <tr>
                <th>Total Pemasukan</th>
                <th>Total Pengeluaran</th>
                <th>Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-right">Rp {{ number_format($laporan->total_pemasukan, 2, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($laporan->total_pengeluaran, 2, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($laporan->saldo_akhir, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Detail Pemasukan per Kategori --}}
    <h3>Detail Pemasukan</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pemasukans as $pm)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($pm->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $pm->kategori->nama_kategori ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($pm->jumlah, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">— Tidak ada data —</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Detail Pengeluaran per Kategori --}}
    <h3>Detail Pengeluaran</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengeluarans as $pg)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($pg->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $pg->kategori->nama_kategori ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($pg->jumlah, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">— Tidak ada data —</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
