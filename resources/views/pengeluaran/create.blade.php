<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Tambah Pengeluaran</h1>
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <form action="{{ route('pengeluaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    {{-- Rekening --}}
                    <div>
                        <label for="id_rekening"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rekening</label>
                        <select id="id_rekening" name="id_rekening"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm">
                            <option value="">-- Pilih Rekening --</option>
                            @foreach ($rekenings as $r)
                                <option value="{{ $r->id_rekening }}"
                                    {{ old('id_rekening') == $r->id_rekening ? 'selected' : '' }}>
                                    {{ $r->nama_rekening }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_rekening')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label for="id_kategori"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori
                            Pengeluaran</label>
                        <select id="id_kategori" name="id_kategori"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach (\App\Models\KategoriPengeluaran::where('id_pengguna', auth()->user()->id_pengguna)->get() as $cat)
                                <option value="{{ $cat->id_kategori_pengeluaran }}"
                                    {{ old('id_kategori') == $cat->id_kategori_pengeluaran ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kategori')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jumlah --}}
                    <div>
                        <label for="jumlah"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah</label>
                        <input type="number" step="0.01" name="jumlah" id="jumlah" value="{{ old('jumlah') }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm">
                        @error('jumlah')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label for="tanggal"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal') }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm">
                        @error('tanggal')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Bukti Transaksi --}}
                    <div>
                        <label for="bukti_transaksi"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bukti Transaksi
                            (gambar)</label>
                        <input type="file" name="bukti_transaksi" id="bukti_transaksi" accept="image/*"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm">
                        @error('bukti_transaksi')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('pengeluaran.index') }}" class="px-4 py-2 mr-2 border rounded">Batal</a>
                    <button type="submit"
                        class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
