<x-app-layout>
    <div class="bg-white p-6 rounded-xl shadow space-y-6">
        {{-- Pesan Berhasil --}}
        @if (session('success'))
            <div class="px-4 py-3 rounded border border-green-400 bg-green-100 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- Pesan Gagal --}}
        @if (session('error'))
            <div class="px-4 py-3 rounded border border-red-400 bg-red-100 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        {{-- Validasi --}}
        @if ($errors->any())
            <div class="px-4 py-3 rounded border border-yellow-400 bg-yellow-100 text-yellow-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h1 class="text-2xl font-bold text-gray-800">Laporan Keuangan</h1>

        {{-- Backup & Restore --}}
        <div class="flex space-x-4">
            @if (auth()->user()->google_access_token)
                <form action="{{ route('laporan.backup') }}" method="POST">@csrf
                    <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Backup ke Google Drive
                    </button>
                </form>
                <form action="{{ route('laporan.restore') }}" method="POST">@csrf
                    <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Restore dari Google Drive
                    </button>
                </form>
                <form action="{{ route('google.disconnect') }}" method="POST">@csrf
                    <button class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                        Disconnect Drive
                    </button>
                </form>
            @else
                <a href="{{ route('google.auth') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Connect Google Drive
                </a>
            @endif
        </div>

        {{-- Form Generate --}}
        <form action="{{ route('laporan.generate') }}" method="POST" class="space-y-4">@csrf
            <div class="flex items-center space-x-4">
                <label class="font-medium text-gray-700">Periode:</label>
                <select name="filter_type" onchange="handleFilter(this.value)"
                    class="border rounded p-2 focus:ring-yellow-500 focus:border-yellow-500">
                    <option value="">-- Pilih --</option>
                    <option value="minggu" @selected(old('filter_type') == 'minggu')>Mingguan</option>
                    <option value="bulan" @selected(old('filter_type') == 'bulan')>Bulanan</option>
                    <option value="tahun" @selected(old('filter_type') == 'tahun')>Tahunan</option>
                </select>
            </div>
            <div id="input-minggu" class="hidden">
                <input type="date" name="filter_date" value="{{ old('filter_date') }}"
                    class="border rounded p-2 w-1/3">
            </div>
            <div id="input-bulan" class="hidden">
                <input type="month" name="filter_month" value="{{ old('filter_month') }}"
                    class="border rounded p-2 w-1/3">
            </div>
            <div id="input-tahun" class="hidden">
                <input type="number" name="filter_year" min="2000" max="2100" value="{{ old('filter_year') }}"
                    class="border rounded p-2 w-1/4" placeholder="2025">
            </div>
            <button class="px-6 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                Generate Laporan
            </button>
        </form>

        {{-- Daftar Laporan --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Periode</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Saldo Awal</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Total Masuk</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Total Keluar</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Saldo Akhir</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Dibuat</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($items as $laporan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-800">{{ $laporan->periode }}</td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                Rp {{ number_format($laporan->saldo_awal, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                Rp {{ number_format($laporan->total_pemasukan, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                Rp {{ number_format($laporan->total_pengeluaran, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                Rp {{ number_format($laporan->saldo_akhir, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                {{ $laporan->created_at->format('d M Y – H:i') }}
                            </td>
                            <td class="px-4 py-2 text-center text-sm space-x-2">
                                <button class="detail-btn px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    Detail
                                </button>
                                <a href="{{ route('laporan.print', $laporan->id_laporan) }}" target="_blank"
                                    class="px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                                    Print PDF
                                </a>
                            </td>
                        </tr>

                        {{-- Panel Detail --}}
                        <tr class="detail-panel bg-gray-50 hidden">
                            <td colspan="7" class="px-4 py-4">
                                @php
                                    $periode = $laporan->periode;
                                    $start = $laporan->created_at->startOfDay();
                                    $end = $laporan->created_at->endOfDay();

                                    if (\Illuminate\Support\Str::startsWith($periode, 'Minggu ')) {
                                        $range = \Illuminate\Support\Str::after($periode, 'Minggu ');
                                        $parts = explode(' – ', $range);
                                        $from = trim($parts[0]);
                                        $to = trim($parts[1] ?? $parts[0]);
                                        $start = \Carbon\Carbon::createFromFormat('d M Y', $from)->startOfDay();
                                        $end = \Carbon\Carbon::createFromFormat('d M Y', $to)->endOfDay();
                                    } elseif (\Illuminate\Support\Str::startsWith($periode, 'Tahun ')) {
                                        $year = (int) \Illuminate\Support\Str::after($periode, 'Tahun ');
                                        $start = \Carbon\Carbon::create($year, 1, 1)->startOfDay();
                                        $end = \Carbon\Carbon::create($year, 12, 31)->endOfDay();
                                    } elseif (preg_match('/^[A-Z][a-z]+ \d{4}$/', $periode)) {
                                        $start = \Carbon\Carbon::createFromFormat('F Y', $periode)
                                            ->startOfMonth()
                                            ->startOfDay();
                                        $end = \Carbon\Carbon::createFromFormat('F Y', $periode)
                                            ->endOfMonth()
                                            ->endOfDay();
                                    }

                                    $pemasukans = \App\Models\Pemasukan::with('kategori')
                                        ->where('id_pengguna', $laporan->id_pengguna)
                                        ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                                        ->orderBy('tanggal')
                                        ->get();

                                    $pengeluarans = \App\Models\Pengeluaran::with('kategori')
                                        ->where('id_pengguna', $laporan->id_pengguna)
                                        ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                                        ->orderBy('tanggal')
                                        ->get();
                                @endphp

                                <div class="header text-center mb-4">
                                    <h2 class="text-lg font-bold">LAPORAN KEUANGAN</h2>
                                    <p>Periode: {{ $laporan->periode }}</p>
                                    <p>Dicetak: {{ now()->format('d M Y H:i') }}</p>
                                </div>

                                {{-- Ringkasan --}}
                                <table class="w-full border-collapse mb-4">
                                    <thead>
                                        <tr>
                                            <th class="border p-1">Total Pemasukan</th>
                                            <th class="border p-1">Total Pengeluaran</th>
                                            <th class="border p-1">Saldo Akhir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border p-1 text-right">
                                                Rp {{ number_format($laporan->total_pemasukan, 2, ',', '.') }}
                                            </td>
                                            <td class="border p-1 text-right">
                                                Rp {{ number_format($laporan->total_pengeluaran, 2, ',', '.') }}
                                            </td>
                                            <td class="border p-1 text-right">
                                                Rp {{ number_format($laporan->saldo_akhir, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                {{-- Detail Pemasukan --}}
                                <h3 class="mt-4 font-semibold">Detail Pemasukan</h3>
                                <table class="w-full border-collapse mb-4">
                                    <thead>
                                        <tr>
                                            <th class="border p-1">Tanggal</th>
                                            <th class="border p-1">Kategori</th>
                                            <th class="border p-1 text-right">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pemasukans as $pm)
                                            <tr>
                                                <td class="border p-1">
                                                    {{ \Carbon\Carbon::parse($pm->tanggal)->format('d-m-Y') }}</td>
                                                <td class="border p-1">{{ $pm->kategori->nama_kategori ?? '-' }}</td>
                                                <td class="border p-1 text-right">
                                                    Rp {{ number_format($pm->jumlah, 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="border p-1 text-center">— Tidak ada data —
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                {{-- Detail Pengeluaran --}}
                                <h3 class="font-semibold">Detail Pengeluaran</h3>
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr>
                                            <th class="border p-1">Tanggal</th>
                                            <th class="border p-1">Kategori</th>
                                            <th class="border p-1 text-right">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pengeluarans as $pg)
                                            <tr>
                                                <td class="border p-1">
                                                    {{ \Carbon\Carbon::parse($pg->tanggal)->format('d-m-Y') }}</td>
                                                <td class="border p-1">{{ $pg->kategori->nama_kategori ?? '-' }}</td>
                                                <td class="border p-1 text-right">
                                                    Rp {{ number_format($pg->jumlah, 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="border p-1 text-center">— Tidak ada data —
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">{{ $items->links() }}</div>
    </div>

    <script>
        // toggle filter inputs
        function handleFilter(val) {
            ['minggu', 'bulan', 'tahun'].forEach(t => {
                document.getElementById('input-' + t).classList.toggle('hidden', val !== t);
            });
        }
        document.addEventListener('DOMContentLoaded', () => {
            handleFilter("{{ old('filter_type') }}");
            // setup detail toggles
            document.querySelectorAll('.detail-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const panel = btn.closest('tr').nextElementSibling;
                    panel.classList.toggle('hidden');
                });
            });
        });
    </script>
</x-app-layout>
