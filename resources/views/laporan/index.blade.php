<x-app-layout>
    <div class="bg-white p-6 rounded-xl shadow space-y-6">
        {{-- Pesan Berhasil --}}
        @if (session('success'))
            <div class="px-4 py-3 rounded border border-green-400 bg-green-100 text-green-800 mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Pesan Gagal --}}
        @if (session('error'))
            <div class="px-4 py-3 rounded border border-red-400 bg-red-100 text-red-800 mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Pesan Validasi --}}
        @if ($errors->any())
            <div class="px-4 py-3 rounded border border-yellow-400 bg-yellow-100 text-yellow-800 mb-4">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <h1 class="text-2xl font-bold text-gray-800">Laporan Keuangan</h1>

        {{-- Backup & Restore --}}
        <div class="flex space-x-4">
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
        </div>

        {{-- Form Generate --}}
        <form action="{{ route('laporan.generate') }}" method="POST" class="space-y-4">@csrf
            <div class="flex items-center space-x-4">
                <label class="font-medium text-gray-700">Periode:</label>
                <select name="filter_type" onchange="handleFilter(this.value)"
                    class="border border-gray-300 rounded p-2 focus:ring-yellow-500 focus:border-yellow-500">
                    <option value="">-- Pilih --</option>
                    <option value="minggu" @selected(old('filter_type') == 'minggu')>Mingguan</option>
                    <option value="bulan" @selected(old('filter_type') == 'bulan')>Bulanan</option>
                    <option value="tahun" @selected(old('filter_type') == 'tahun')>Tahunan</option>
                </select>
            </div>
            <div id="input-minggu" class="hidden">
                <input type="date" name="filter_date" value="{{ old('filter_date') }}"
                    class="border border-gray-300 rounded p-2 w-1/3">
            </div>
            <div id="input-bulan" class="hidden">
                <input type="month" name="filter_month" value="{{ old('filter_month') }}"
                    class="border border-gray-300 rounded p-2 w-1/3">
            </div>
            <div id="input-tahun" class="hidden">
                <input type="number" name="filter_year" min="2000" max="2100" value="{{ old('filter_year') }}"
                    class="border border-gray-300 rounded p-2 w-1/4" placeholder="2025">
            </div>
            <button class="px-6 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                Generate Laporan
            </button>
        </form>

        {{-- Daftar Laporan --}}
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Periode</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Total Masuk</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Total Keluar</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Saldo Akhir</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Dibuat</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-800">{{ $item->periode }}</td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                Rp {{ number_format($item->total_pemasukan, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                Rp {{ number_format($item->total_pengeluaran, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                Rp {{ number_format($item->saldo_akhir, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                {{ $item->created_at->format('d M Y – H:i') }}
                            </td>
                            <td class="px-4 py-2 text-sm">
                                <a href="{{ route('laporan.print', $item->id_laporan) }}" target="_blank"
                                    class="px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                                    Print PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-2 text-center text-gray-500">Belum ada laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>

    <script>
        function handleFilter(val) {
            ['minggu', 'bulan', 'tahun'].forEach(type => {
                document.getElementById('input-' + type)
                    .classList.toggle('hidden', val !== type);
            });
        }
        document.addEventListener('DOMContentLoaded', () => {
            handleFilter("{{ old('filter_type') }}");
        });
    </script>
</x-app-layout>
