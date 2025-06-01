{{-- resources/views/kategori/create.blade.php --}}
<x-app-layout>
    {{-- Pastikan di layout utama (resources/views/layouts/app.blade.php) sudah include Boxicons CDN di <head>:
         <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    --}}

    @php
        $type = request()->query('type', 'pengeluaran');
        $label = ucfirst($type);
        $icons = config('icons.all', []);
    @endphp

    {{-- TAB NAV --}}
    <div class="mb-6">
        <ul class="flex border-b">
            <li class="mr-4">
                <a href="{{ url('/kategori?type=pengeluaran') }}"
                    class="py-2 px-4 block {{ $type === 'pengeluaran' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                    Pengeluaran
                </a>
            </li>
            <li>
                <a href="{{ url('/kategori?type=pemasukan') }}"
                    class="py-2 px-4 block {{ $type === 'pemasukan' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                    Pemasukan
                </a>
            </li>
        </ul>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            Tambah Kategori {{ $label }}
        </h1>

        <form action="{{ route('kategori.store', ['type' => $type]) }}" method="POST">
            @csrf

            <div class="space-y-5">
                {{-- Pengguna --}}
                <div>
                    <label for="id_pengguna" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Pengguna
                    </label>
                    <select id="id_pengguna" name="id_pengguna"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                                   focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white">
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

                {{-- Nama Kategori --}}
                <div>
                    <label for="nama_kategori" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Kategori
                    </label>
                    <input id="nama_kategori" name="nama_kategori" type="text" value="{{ old('nama_kategori') }}"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                                  focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white" />
                    @error('nama_kategori')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Deskripsi
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                                     focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white"
                        placeholder="Opsional…">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Icon Picker --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih Icon
                    </label>
                    <div
                        class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2 p-2 border border-gray-300 dark:border-gray-600 rounded-md">
                        @foreach ($icons as $cls)
                            @php $checked = old('icon') === $cls; @endphp
                            <label for="icon_{{ $cls }}"
                                class="flex items-center justify-center p-2 cursor-pointer rounded-lg transition
                                       border border-gray-300 dark:border-gray-600
                                       hover:bg-gray-200 dark:hover:bg-gray-700
                                       peer-checked:border-2 peer-checked:border-indigo-500 peer-checked:bg-indigo-100 dark:peer-checked:bg-indigo-900">
                                <input type="radio" name="icon" id="icon_{{ $cls }}"
                                    value="{{ $cls }}" class="sr-only peer"
                                    {{ $checked ? 'checked' : '' }} />
                                <i
                                    class="bx {{ $cls }} text-2xl
                                           {{ $checked ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300' }}
                                           peer-checked:text-indigo-600 peer-checked:dark:text-indigo-400"></i>
                            </label>
                        @endforeach
                    </div>
                    @error('icon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Buttons --}}
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('kategori.index', ['type' => $type]) }}"
                    class="px-4 py-2 border rounded-md text-sm text-gray-700 dark:text-gray-300
                          hover:bg-gray-100 dark:hover:bg-gray-700">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
