<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Detail Riwayat Nilai Aset
        </h1>

        <div class="mb-6 flex space-x-2">
            <a href="{{ route('aset.show', $aset->id_aset) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700">
                Kembali ke Detail Aset
            </a>
            <a href="{{ route('aset.history.edit', [$aset->id_aset, $history->id_history]) }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md shadow-sm hover:bg-yellow-700">
                Edit Riwayat
            </a>
            <form action="{{ route('aset.history.destroy', [$aset->id_aset, $history->id_history]) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Yakin ingin menghapus riwayat ini?')"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md shadow-sm hover:bg-red-700">
                    Hapus Riwayat
                </button>
            </form>
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
                        <span class="text-gray-500 dark:text-gray-400">Nilai Aset Saat Ini:</span>
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
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Perubahan Nilai</h2>

                <div class="space-y-3">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Tanggal Perubahan:</span>
                        <span
                            class="ml-2 text-gray-900 dark:text-white font-medium">{{ $history->tanggal_perubahan->format('d/m/Y') }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Nilai Lama:</span>
                        <span class="ml-2 text-gray-900 dark:text-white font-medium">Rp
                            {{ number_format($history->nilai_lama, 0, ',', '.') }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Nilai Baru:</span>
                        <span class="ml-2 text-gray-900 dark:text-white font-medium">Rp
                            {{ number_format($history->nilai_baru, 0, ',', '.') }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Selisih:</span>
                        <span
                            class="ml-2 {{ $history->nilai_baru > $history->nilai_lama ? 'text-green-600' : 'text-red-600' }} font-medium">
                            {{ $history->nilai_baru > $history->nilai_lama ? '+' : '' }}Rp
                            {{ number_format($history->nilai_baru - $history->nilai_lama, 0, ',', '.') }}
                            ({{ $history->nilai_baru > $history->nilai_lama ? '+' : '' }}{{ number_format((($history->nilai_baru - $history->nilai_lama) / max(1, $history->nilai_lama)) * 100, 2) }}%)
                        </span>
                    </div>

                    @if ($history->keterangan)
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Keterangan:</span>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $history->keterangan }}</p>
                        </div>
                    @endif

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Waktu Pencatatan:</span>
                        <span
                            class="ml-2 text-gray-900 dark:text-white font-medium">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
