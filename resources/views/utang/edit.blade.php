<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Edit Utang
        </h1>

        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <form action="{{ route('utang.update', $utang->id_utang) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    {{-- Nama --}}
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nama
                        </label>
                        <input id="nama" name="nama" type="text" value="{{ old('nama', $utang->nama) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('nama')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pengguna --}}
                    <div>
                        <label for="id_pengguna" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Pengguna
                        </label>
                        <select id="id_pengguna" name="id_pengguna"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white">
                            @foreach (\App\Models\Pengguna::where('id_pengguna', auth()->user()->id_pengguna)->get() as $u)
                                <option value="{{ $u->id_pengguna }}"
                                    {{ old('id_pengguna', $utang->id_pengguna) == $u->id_pengguna ? 'selected' : '' }}>
                                    {{ $u->username }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_pengguna')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Rekening --}}
                    <div>
                        <label for="id_rekening" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Rekening
                        </label>
                        <select id="id_rekening" name="id_rekening"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white">
                            @foreach (\App\Models\Rekening::where('id_pengguna', auth()->user()->id_pengguna)->get() as $r)
                                <option value="{{ $r->id_rekening }}"
                                    {{ old('id_rekening', $utang->id_rekening) == $r->id_rekening ? 'selected' : '' }}>
                                    {{ $r->nama_rekening }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_rekening')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jumlah --}}
                    <div>
                        <label for="jumlah" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Jumlah
                        </label>
                        <input id="jumlah" name="jumlah" type="number" step="0.01"
                            value="{{ old('jumlah', $utang->jumlah) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('jumlah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Pinjam --}}
                    <div>
                        <label for="tanggal_pinjam" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tanggal Pinjam
                        </label>
                        <input id="tanggal_pinjam" name="tanggal_pinjam" type="date"
                            value="{{ old('tanggal_pinjam', $utang->tanggal_pinjam->format('Y-m-d')) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('tanggal_pinjam')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Jatuh Tempo --}}
                    <div>
                        <label for="tanggal_jatuh_tempo"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tanggal Jatuh Tempo
                        </label>
                        <input id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo" type="date"
                            value="{{ old('tanggal_jatuh_tempo', $utang->tanggal_jatuh_tempo->format('Y-m-d')) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('tanggal_jatuh_tempo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Deskripsi
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white">{{ old('deskripsi', $utang->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Bukti Transaksi --}}
                    <div>
                        <label for="bukti_transaksi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Bukti Transaksi (gambar)
                        </label>
                        <input type="file" name="bukti_transaksi" id="bukti_transaksi" accept="image/*"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                        @error('bukti_transaksi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @php
                            // Cari pemasukan terkait utang untuk mendapatkan bukti transaksi
                            $pemasukanTerkait = \App\Models\Pemasukan::where(
                                'deskripsi',
                                'like',
                                '%Terima utang (ID ' . $utang->id_utang . ')%',
                            )
                                ->where('id_pengguna', auth()->user()->id_pengguna)
                                ->first();
                        @endphp

                        @if ($pemasukanTerkait && $pemasukanTerkait->bukti_transaksi)
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                File saat ini:
                                <a href="{{ Storage::url($pemasukanTerkait->bukti_transaksi) }}" target="_blank"
                                    class="underline text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Lihat
                                    bukti</a>
                            </p>
                        @endif

                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Format: JPG, JPEG, PNG, GIF. Maksimal 2MB.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('utang.index') }}"
                        class="px-4 py-2 mr-2 border rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
