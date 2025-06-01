<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Tambah Transfer
        </h1>

        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <form action="{{ route('transfer.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    {{-- Rekening Asal --}}
                    <div>
                        <label for="id_rekening" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Rekening Asal
                        </label>
                        <select id="id_rekening" name="id_rekening"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white">
                            <option value="">-- Pilih Rekening --</option>
                            @foreach (\App\Models\Rekening::all() as $r)
                                <option value="{{ $r->id_rekening }}"
                                    {{ old('id_rekening') == $r->id_rekening ? 'selected' : '' }}>
                                    {{ $r->nama_rekening }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_rekening')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Rekening Tujuan --}}
                    <div>
                        <label for="rekening_tujuan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Rekening Tujuan
                        </label>
                        <select id="rekening_tujuan" name="rekening_tujuan"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white">
                            <option value="">-- Pilih Rekening --</option>
                            @foreach (\App\Models\Rekening::all() as $r)
                                <option value="{{ $r->id_rekening }}"
                                    {{ old('rekening_tujuan') == $r->id_rekening ? 'selected' : '' }}>
                                    {{ $r->nama_rekening }}
                                </option>
                            @endforeach
                        </select>
                        @error('rekening_tujuan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jumlah --}}
                    <div>
                        <label for="jumlah" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Jumlah
                        </label>
                        <input id="jumlah" name="jumlah" type="number" step="0.01" value="{{ old('jumlah') }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white" />
                        @error('jumlah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tanggal
                        </label>
                        <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal') }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white" />
                        @error('tanggal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('transfer.index') }}"
                        class="px-4 py-2 mr-2 border rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
