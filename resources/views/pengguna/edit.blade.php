<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Edit Pengguna</h1>

        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <form action="{{ route('admin.pengguna.update', $pengguna->id_pengguna) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-4">

                    {{-- Foto Preview & Upload --}}
                    <div class="flex flex-col items-center">
                        {{-- Preview Gambar Awal --}}
                        @if ($pengguna->foto)
                            <div id="previewContainer" class="mb-4">
                                <img id="previewImage" src="{{ asset('storage/' . $pengguna->foto) }}"
                                    alt="Preview Foto"
                                    class="w-32 h-32 object-cover rounded-full border-4 border-yellow-500 shadow-lg ring-2 ring-yellow-300 dark:ring-yellow-600 transition duration-300" />
                            </div>
                        @else
                            <div id="previewContainer" class="mb-4 hidden">
                                <img id="previewImage" src="#" alt="Preview Foto"
                                    class="w-32 h-32 object-cover rounded-full border-4 border-yellow-500 shadow-lg ring-2 ring-yellow-300 dark:ring-yellow-600 transition duration-300" />
                            </div>
                        @endif

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Foto
                            (opsional)</label>
                        <input name="foto" type="file" accept="image/*" id="fotoInput"
                            class="mt-2 block w-full text-gray-700 dark:text-gray-300 text-sm" />
                        @error('foto')
                            <p class="mt-1 text-sm text-red-600 text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                        <input name="username" value="{{ old('username', $pengguna->username) }}" type="text"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input name="email" value="{{ old('email', $pengguna->email) }}" type="email"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password (kosong = tidak diubah) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password (kosong =
                            tidak diubah)</label>
                        <input name="password" type="password"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Saldo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Saldo</label>
                        <input name="saldo" value="{{ old('saldo', $pengguna->saldo) }}" type="number" step="0.01"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('saldo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('admin.pengguna.index') }}"
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

    {{-- JavaScript Preview Gambar --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fotoInput = document.getElementById('fotoInput');
            const previewContainer = document.getElementById('previewContainer');
            const previewImage = document.getElementById('previewImage');

            fotoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                    }

                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</x-app-layout>
