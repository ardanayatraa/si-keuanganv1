<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Edit Piutang
        </h1>

        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <form action="{{ route('piutang.update', $piutang->id_piutang) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    {{-- Pengguna --}}
                    <div>
                        <label for="id_pengguna" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Pengguna
                        </label>
                        <select id="id_pengguna" name="id_pengguna"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white">
                            @foreach (\App\Models\Pengguna::where('id_pengguna', auth()->user()->id_pengguna)->get() as $user)
                                <option value="{{ $user->id_pengguna }}"
                                    {{ old('id_pengguna', $piutang->id_pengguna) == $user->id_pengguna ? 'selected' : '' }}>
                                    {{ $user->username }}
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
                                    {{ old('id_rekening', $piutang->id_rekening) == $r->id_rekening ? 'selected' : '' }}>
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
                            value="{{ old('jumlah', $piutang->jumlah) }}"
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
                            value="{{ old('tanggal_pinjam', $piutang->tanggal_pinjam->format('Y-m-d')) }}"
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
                            value="{{ old('tanggal_jatuh_tempo', $piutang->tanggal_jatuh_tempo->format('Y-m-d')) }}"
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
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white">{{ old('deskripsi', $piutang->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('piutang.index') }}"
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
