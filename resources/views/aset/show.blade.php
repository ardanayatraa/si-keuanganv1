<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Detail Aset: {{ $aset->nama_aset }}
        </h1>

        <div class="mb-6 flex space-x-2">
            <a href="{{ route('aset.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700">
                Kembali
            </a>
            <a href="{{ route('aset.edit', $aset->id_aset) }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md shadow-sm hover:bg-yellow-700">
                Edit
            </a>
            <a href="{{ route('aset.toggle-status', $aset->id_aset) }}"
                class="inline-flex items-center px-4 py-2 {{ $aset->status === 'aktif' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-md shadow-sm">
                {{ $aset->status === 'aktif' ? 'Tandai Terjual' : 'Aktifkan Kembali' }}
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Aset</h2>

                <div class="space-y-3">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Nama Aset:</span>
                        <span class="ml-2 text-gray-900 dark:text-white font-medium">{{ $aset->nama_aset }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Jenis Aset:</span>
                        <span class="ml-2 text-gray-900 dark:text-white font-medium">{{ $aset->jenis_aset }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Nilai Aset:</span>
                        <span class="ml-2 text-gray-900 dark:text-white font-medium">Rp
                            {{ number_format($aset->nilai_aset, 0, ',', '.') }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Tanggal Perolehan:</span>
                        <span
                            class="ml-2 text-gray-900 dark:text-white font-medium">{{ $aset->tanggal_perolehan->format('d/m/Y') }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Status:</span>
                        <span
                            class="ml-2 px-2 py-1 rounded-full text-xs {{ $aset->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $aset->status === 'aktif' ? 'Aktif' : 'Terjual' }}
                        </span>
                    </div>

                    @if ($aset->keterangan)
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Keterangan:</span>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $aset->keterangan }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Nilai Aset</h2>
                    <a href="{{ route('aset.history.create', $aset->id_aset) }}"
                        class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded-md shadow-sm hover:bg-blue-700">
                        Tambah Riwayat
                    </a>
                </div>

                @if ($history->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead>
                                <tr>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Tanggal</th>
                                    <th
                                        class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Nilai Lama</th>
                                    <th
                                        class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Nilai Baru</th>
                                    <th
                                        class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach ($history as $item)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                            {{ $item->tanggal_perubahan->format('d/m/Y') }}
                                        </td>
                                        <td
                                            class="px-4 py-2 whitespace-nowrap text-right text-gray-900 dark:text-gray-100">
                                            {{ number_format($item->nilai_lama, 0, ',', '.') }}
                                        </td>
                                        <td
                                            class="px-4 py-2 whitespace-nowrap text-right text-gray-900 dark:text-gray-100">
                                            {{ number_format($item->nilai_baru, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-center">
                                            <a href="{{ route('aset.history.show', [$aset->id_aset, $item->id_history]) }}"
                                                class="inline-block px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                        Belum ada riwayat perubahan nilai aset.
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
