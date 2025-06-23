<x-app-layout>

    <div class="py-6">
        <div class="w-full mx-auto bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            @if (session('success'))
                <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('pengguna.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Foto Preview --}}
                <div class="flex justify-center mb-4">
                    <img id="foto-preview"
                        src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://via.placeholder.com/60' }}"
                        alt="Preview Foto" class="h-16 w-16 rounded-full object-cover border" />
                </div>

                {{-- Input Foto --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ganti Foto
                        (opsional)</label>
                    <input id="foto-input" name="foto" type="file" accept="image/*"
                        class="mt-1 block w-full text-gray-700 dark:text-gray-300" />
                    @error('foto')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Username --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                    <input name="username" type="text" value="{{ old('username', $user->username) }}"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white" />
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input name="email" type="email" value="{{ old('email', $user->email) }}"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white" />
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Password <span class="text-xs text-gray-500">(kosongkan jika tidak diubah)</span>
                    </label>
                    <input name="password" type="password"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white" />
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Saldo --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Saldo</label>
                    <input name="saldo" type="number" step="0.01" value="{{ old('saldo', $user->saldo) }}"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white" />
                    @error('saldo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex justify-between items-center">
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 border rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                        Kembali ke Dashboard
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-md shadow-sm hover:bg-yellow-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Preview script --}}
    <script>
        document.getElementById('foto-input').addEventListener('change', function(e) {
            const [file] = e.target.files;
            if (file) {
                document.getElementById('foto-preview').src = URL.createObjectURL(file);
            }
        });
    </script>
</x-app-layout>
