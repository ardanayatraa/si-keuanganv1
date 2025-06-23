<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Tambah Anggaran
        </h1>

        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <form action="{{ route('anggaran.store') }}" method="POST">
                @csrf
                <div class="space-y-4">

                    {{-- Kategori --}}
                    <div>
                        <label for="id_kategori" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Kategori
                        </label>
                        <select id="id_kategori" name="id_kategori"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($listKategori as $kategori)
                                <option value="{{ $kategori->id_kategori_pengeluaran }}"
                                    {{ old('id_kategori') == $kategori->id_kategori_pengeluaran ? 'selected' : '' }}>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kategori')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Deskripsi
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Opsional…">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jumlah Batas --}}
                    <div>
                        <label for="jumlah_batas" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Jumlah Batas
                        </label>
                        <input id="jumlah_batas" name="jumlah_batas" type="number" step="0.01"
                            value="{{ old('jumlah_batas') }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Masukkan jumlah batas…" />
                        @error('jumlah_batas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Periode Awal --}}
                    <div>
                        <label for="periode_awal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Periode Awal
                        </label>
                        <input id="periode_awal" name="periode_awal" type="date" value="{{ old('periode_awal') }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('periode_awal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Periode Akhir --}}
                    <div>
                        <label for="periode_akhir" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Periode Akhir
                        </label>
                        <input id="periode_akhir" name="periode_akhir" type="date"
                            value="{{ old('periode_akhir') }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('periode_akhir')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('anggaran.index') }}"
                        class="px-4 py-2 mr-2 border rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-yellow-600 text-white rounded-md shadow-sm hover:bg-yellow-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
