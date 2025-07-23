<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Tambah Aset Baru
        </h1>

        <div class="mb-4">
            <a href="{{ route('aset.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700">
                Kembali
            </a>
        </div>

        <form action="{{ route('aset.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-label for="nama_aset" value="Nama Aset" />
                    <x-input id="nama_aset" name="nama_aset" type="text" class="mt-1 block w-full"
                        value="{{ old('nama_aset') }}" required />
                    @error('nama_aset')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="jenis_aset" value="Jenis Aset" />
                    <select id="jenis_aset" name="jenis_aset"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">Pilih Jenis Aset</option>
                        <option value="Tunai/Rekening Bank"
                            {{ old('jenis_aset') == 'Tunai/Rekening Bank' ? 'selected' : '' }}>Tunai/Rekening Bank
                        </option>
                        <option value="Properti" {{ old('jenis_aset') == 'Properti' ? 'selected' : '' }}>Properti
                        </option>
                        <option value="Kendaraan" {{ old('jenis_aset') == 'Kendaraan' ? 'selected' : '' }}>Kendaraan
                        </option>
                        <option value="Elektronik" {{ old('jenis_aset') == 'Elektronik' ? 'selected' : '' }}>Elektronik
                        </option>
                        <option value="Investasi" {{ old('jenis_aset') == 'Investasi' ? 'selected' : '' }}>Investasi
                        </option>
                        <option value="Aset Digital" {{ old('jenis_aset') == 'Aset Digital' ? 'selected' : '' }}>Aset
                            Digital</option>
                        <option value="Lain-lain" {{ old('jenis_aset') == 'Lain-lain' ? 'selected' : '' }}>Lain-lain
                        </option>
                    </select>
                    @error('jenis_aset')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="nilai_aset" value="Nilai Aset (Rp)" />
                    <x-input id="nilai_aset" name="nilai_aset" type="number" min="0" step="1000"
                        class="mt-1 block w-full" value="{{ old('nilai_aset') }}" required />
                    @error('nilai_aset')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="tanggal_perolehan" value="Tanggal Perolehan" />
                    <x-input id="tanggal_perolehan" name="tanggal_perolehan" type="date" class="mt-1 block w-full"
                        value="{{ old('tanggal_perolehan') ?? date('Y-m-d') }}" required />
                    @error('tanggal_perolehan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <x-label for="keterangan" value="Keterangan (Opsional)" />
                    <textarea id="keterangan" name="keterangan" rows="3"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <x-button type="submit" class="bg-yellow-600 hover:bg-yellow-700">
                    Simpan Aset
                </x-button>
            </div>
        </form>
    </div>
</x-app-layout>
