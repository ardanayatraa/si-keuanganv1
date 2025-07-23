<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Edit Riwayat Nilai Aset: {{ $aset->nama_aset }}
        </h1>

        <div class="mb-4">
            <a href="{{ route('aset.history.index', $aset->id_aset) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700">
                Kembali
            </a>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Informasi Perubahan Nilai</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
            </div>
        </div>

        <form action="{{ route('aset.history.update', [$aset->id_aset, $history->id_history]) }}" method="POST"
            class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-label for="tanggal_perubahan" value="Tanggal Perubahan" />
                    <x-input id="tanggal_perubahan" name="tanggal_perubahan" type="date" class="mt-1 block w-full"
                        value="{{ old('tanggal_perubahan', $history->tanggal_perubahan->format('Y-m-d')) }}"
                        required />
                    @error('tanggal_perubahan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <x-label for="keterangan" value="Keterangan Perubahan" />
                    <textarea id="keterangan" name="keterangan" rows="3"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('keterangan', $history->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <x-button type="submit" class="bg-blue-600 hover:bg-blue-700">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </div>
</x-app-layout>
