<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Edit Item Wishlist: {{ $item->nama_item }}
        </h1>

        <div class="mb-4">
            <a href="{{ route('wishlist.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700">
                Kembali
            </a>
        </div>

        <form action="{{ route('wishlist.update', $item->id_wishlist) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-label for="nama_item" value="Nama Item" />
                    <x-input id="nama_item" name="nama_item" type="text" class="mt-1 block w-full"
                        value="{{ old('nama_item', $item->nama_item) }}" required />
                    @error('nama_item')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="kategori" value="Kategori" />
                    <select id="kategori" name="kategori"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}"
                                {{ old('kategori', $item->kategori) == $category ? 'selected' : '' }}>
                                {{ $category }}</option>
                        @endforeach
                        <option value="Lain-lain"
                            {{ old('kategori', $item->kategori) == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                    </select>
                    @error('kategori')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="estimasi_harga" value="Estimasi Harga (Rp)" />
                    <x-input id="estimasi_harga" name="estimasi_harga" type="number" min="0" step="1000"
                        class="mt-1 block w-full" value="{{ old('estimasi_harga', $item->estimasi_harga) }}"
                        required />
                    @error('estimasi_harga')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="tanggal_target" value="Tanggal Target" />
                    <x-input id="tanggal_target" name="tanggal_target" type="date" class="mt-1 block w-full"
                        value="{{ old('tanggal_target', $item->tanggal_target->format('Y-m-d')) }}" required />
                    @error('tanggal_target')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="dana_terkumpul" value="Dana Terkumpul (Rp)" />
                    <x-input id="dana_terkumpul" name="dana_terkumpul" type="number" min="0" step="1000"
                        class="mt-1 block w-full" value="{{ old('dana_terkumpul', $item->dana_terkumpul) }}" />
                    @error('dana_terkumpul')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="sumber_dana" value="Sumber Dana" />
                    <x-input id="sumber_dana" name="sumber_dana" type="text" class="mt-1 block w-full"
                        value="{{ old('sumber_dana', $item->sumber_dana) }}" />
                    <p class="text-gray-500 text-xs mt-1">Opsional. Misal: Tabungan BCA, Gaji Bulanan, dll.</p>
                    @error('sumber_dana')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <x-label for="keterangan" value="Keterangan (Opsional)" />
                    <textarea id="keterangan" name="keterangan" rows="3"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('keterangan', $item->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <x-button type="submit" class="bg-yellow-600 hover:bg-yellow-700">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </div>
</x-app-layout>
