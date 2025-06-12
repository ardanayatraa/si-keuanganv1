<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Edit Pembayaran Utang
        </h1>

        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <form action="{{ route('pembayaran-utang.update', $pembayaranUtang->id_pembayaran_utang) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    {{-- Utang --}}
                    <div>
                        <label for="id_utang" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Utang
                        </label>
                        <select id="id_utang" name="id_utang"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white">
                            @foreach ($utangs as $utang)
                                <option value="{{ $utang->id_utang }}"
                                    {{ old('id_utang', $pembayaranUtang->id_utang) == $utang->id_utang ? 'selected' : '' }}>
                                    {{ $utang->pengguna->username }} — Sisa:
                                    {{ number_format($utang->sisa, 2, ',', '.') }}
                                    (Jatuh Tempo:
                                    {{ \Carbon\Carbon::parse($utang->tanggal_jatuh_tempo)->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_utang')
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
                            @foreach ($rekenings as $r)
                                <option value="{{ $r->id_rekening }}"
                                    {{ old('id_rekening', $pembayaranUtang->id_rekening) == $r->id_rekening ? 'selected' : '' }}>
                                    {{ $r->nama_rekening }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_rekening')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jumlah Dibayar --}}
                    <div>
                        <label for="jumlah_dibayar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Jumlah Dibayar
                        </label>
                        <input id="jumlah_dibayar" name="jumlah_dibayar" type="number" step="0.01"
                            value="{{ old('jumlah_dibayar', $pembayaranUtang->jumlah_dibayar) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('jumlah_dibayar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Pembayaran --}}
                    <div>
                        <label for="tanggal_pembayaran"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tanggal Pembayaran
                        </label>
                        <input id="tanggal_pembayaran" name="tanggal_pembayaran" type="date"
                            value="{{ old('tanggal_pembayaran', $pembayaranUtang->tanggal_pembayaran->format('Y-m-d')) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('tanggal_pembayaran')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div>
                        <label for="metode_pembayaran"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Metode Pembayaran (opsional)
                        </label>
                        <input id="metode_pembayaran" name="metode_pembayaran" type="text"
                            value="{{ old('metode_pembayaran', $pembayaranUtang->metode_pembayaran) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Transfer, Tunai, dll." />
                        @error('metode_pembayaran')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Deskripsi (opsional)
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Opsional…">{{ old('deskripsi', $pembayaranUtang->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('pembayaran-utang.index') }}"
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
