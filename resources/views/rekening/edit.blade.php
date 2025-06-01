<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Edit Rekening
        </h1>

        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <form action="{{ route('rekening.update', $rekening->id_rekening) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    {{-- Nama Rekening --}}
                    <div>
                        <label for="nama_rekening" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nama Rekening
                        </label>
                        <input id="nama_rekening" name="nama_rekening" type="text"
                            value="{{ old('nama_rekening', $rekening->nama_rekening) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white" />
                        @error('nama_rekening')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Saldo --}}
                    <div>
                        <label for="saldo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Saldo
                        </label>
                        <input id="saldo" name="saldo" type="number" step="0.01"
                            value="{{ old('saldo', $rekening->saldo) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white" />
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
                        class="px-6 py-2 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
