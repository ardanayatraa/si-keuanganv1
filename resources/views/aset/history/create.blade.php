<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Tambah Riwayat Nilai Aset: {{ $aset->nama_aset }}
        </h1>

        <div class="mb-4">
            <a href="{{ route('aset.show', $aset->id_aset) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700">
                Kembali
            </a>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Informasi Aset</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
            </div>
        </div>

        <form action="{{ route('aset.history.store', $aset->id_aset) }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-label for="nilai_baru" value="Nilai Baru (Rp)" />
                    <x-input id="nilai_baru" name="nilai_baru" type="number" min="0" step="1000"
                        class="mt-1 block w-full" value="{{ old('nilai_baru') }}" required />
                    @error('nilai_baru')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="tanggal_perubahan" value="Tanggal Perubahan" />
                    <x-input id="tanggal_perubahan" name="tanggal_perubahan" type="date" class="mt-1 block w-full"
                        value="{{ old('tanggal_perubahan') ?? date('Y-m-d') }}" required />
                    @error('tanggal_perubahan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <x-label for="keterangan" value="Keterangan Perubahan" />
                    <textarea id="keterangan" name="keterangan" rows="3"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <x-button type="submit" class="bg-blue-600 hover:bg-blue-700">
                    Simpan Riwayat
                </x-button>
            </div>
        </form>
    </div>
</x-app-layout>
