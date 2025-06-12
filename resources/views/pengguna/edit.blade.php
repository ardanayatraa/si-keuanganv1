<div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
        Edit Pengguna
    </h1>

    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
        <form action="{{ route('pengguna.update', $pengguna->id_pengguna) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                {{-- Username --}}
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Username
                    </label>
                    <input id="username" name="username" type="text"
                        value="{{ old('username', $pengguna->username) }}"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email', $pengguna->email) }}"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Password <small>(kosong = tidak diubah)</small>
                    </label>
                    <input id="password" name="password" type="password"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Saldo --}}
                <div>
                    <label for="saldo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Saldo
                    </label>
                    <input id="saldo" name="saldo" type="number" step="0.01"
                        value="{{ old('saldo', $pengguna->saldo) }}"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                    @error('saldo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Foto --}}
                <div>
                    <label for="foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Foto
                    </label>
                    <input id="foto" name="foto" type="file" onchange="previewFoto(event)"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                    @error('foto')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="mt-2">
                        @if ($pengguna->foto)
                            <img id="preview-foto" src="{{ asset('storage/' . $pengguna->foto) }}" alt="Foto Lama"
                                class="w-32 h-32 object-cover rounded-md" />
                        @else
                            <img id="preview-foto" src="#" alt="Preview Foto"
                                class="w-32 h-32 object-cover rounded-md hidden" />
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('pengguna.index') }}"
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

<script>
    function previewFoto(event) {
        const [file] = event.target.files;
        const preview = document.getElementById('preview-foto');
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        }
    }
</script>
