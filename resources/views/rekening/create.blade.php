<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Tambah Rekening
        </h1>

        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <form action="{{ route('rekening.store') }}" method="POST">
                @csrf

                <div class="space-y-4">
                    {{-- Pengguna --}}
                    <div>
                        <label for="id_pengguna" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Pengguna
                        </label>
                        <select id="id_pengguna" name="id_pengguna"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white">
                            <option value="">-- Pilih Pengguna --</option>
                            @foreach (\App\Models\Pengguna::all() as $user)
                                <option value="{{ $user->id_pengguna }}"
                                    {{ old('id_pengguna') == $user->id_pengguna ? 'selected' : '' }}>
                                    {{ $user->username }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_pengguna')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Rekening --}}
                    <div>
                        <label for="nama_rekening" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nama Rekening
                        </label>
                        <input id="nama_rekening" name="nama_rekening" type="text" value="{{ old('nama_rekening') }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('nama_rekening')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Saldo --}}
                    <div>
                        <label for="saldo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Saldo
                        </label>
                        <input id="saldo" name="saldo" type="number" step="0.01" value="{{ old('saldo') }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('saldo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('rekening.index') }}"
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
