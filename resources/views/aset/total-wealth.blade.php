<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Total Nilai Kekayaan
        </h1>

        <div class="mb-6">
            <a href="{{ route('aset.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700">
                Kembali ke Daftar Aset
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-700 p-8 rounded-xl shadow-md text-white">
                <h2 class="text-xl font-semibold mb-2">Total Nilai Aset Aktif</h2>
                <p class="text-4xl font-bold">Rp {{ number_format($totalAset, 0, ',', '.') }}</p>
                <p class="mt-4 text-blue-100">Total nilai dari semua aset yang masih aktif.</p>
            </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Distribusi Aset Berdasarkan Kategori
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $categories = [
                        'Tunai/Rekening Bank' => ['count' => 0, 'total' => 0, 'color' => 'bg-green-500'],
                        'Properti' => ['count' => 0, 'total' => 0, 'color' => 'bg-blue-500'],
                        'Kendaraan' => ['count' => 0, 'total' => 0, 'color' => 'bg-red-500'],
                        'Elektronik' => ['count' => 0, 'total' => 0, 'color' => 'bg-purple-500'],
                        'Investasi' => ['count' => 0, 'total' => 0, 'color' => 'bg-yellow-500'],
                        'Aset Digital' => ['count' => 0, 'total' => 0, 'color' => 'bg-indigo-500'],
                        'Lain-lain' => ['count' => 0, 'total' => 0, 'color' => 'bg-gray-500'],
                    ];

                    // Group assets by category
                    $assets = \App\Models\Aset::where('id_pengguna', Auth::user()->id_pengguna)
                        ->where('status', 'aktif')
                        ->get();

                    foreach ($assets as $asset) {
                        if (isset($categories[$asset->jenis_aset])) {
                            $categories[$asset->jenis_aset]['count']++;
                            $categories[$asset->jenis_aset]['total'] += $asset->nilai_aset;
                        }
                    }
                @endphp

                @foreach ($categories as $category => $data)
                    @if ($data['count'] > 0)
                        <div
                            class="p-4 rounded-lg bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $category }}</h3>
                                <span class="px-2 py-1 rounded-full text-xs text-white {{ $data['color'] }}">
                                    {{ $data['count'] }} aset
                                </span>
                            </div>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($data['total'], 0, ',', '.') }}
                            </p>
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="{{ $data['color'] }} h-2.5 rounded-full"
                                    style="width: {{ ($data['total'] / max(1, $totalAset)) * 100 }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ number_format(($data['total'] / max(1, $totalAset)) * 100, 1) }}% dari total
                            </p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
