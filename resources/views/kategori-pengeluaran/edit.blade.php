{{-- resources/views/kategori/edit.blade.php --}}
<x-app-layout>
    {{-- Pastikan Boxicons CDN sudah di-include di layout utama <head>:
         <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    --}}

    @php
        $type = request()->query('type', 'pengeluaran');
        $label = ucfirst($type);
        $icons = config('icons.all', []);
        $currentIcon = old('icon', $kategoriPengeluaran->icon);
    @endphp

    {{-- TAB NAV --}}
    <div class="mb-6">
        <ul class="flex border-b">
            <li class="mr-4">
                <a href="{{ url('/kategori?type=pengeluaran') }}"
                    class="py-2 px-4 block {{ $type === 'pengeluaran' ? 'border-b-2 border-yellow-600 text-yellow-600' : 'text-gray-600 hover:text-yellow-600' }}">
                    Pengeluaran
                </a>
            </li>
            <li>
                <a href="{{ url('/kategori?type=pemasukan') }}"
                    class="py-2 px-4 block {{ $type === 'pemasukan' ? 'border-b-2 border-yellow-600 text-yellow-600' : 'text-gray-600 hover:text-yellow-600' }}">
                    Pemasukan
                </a>
            </li>
        </ul>
    </div>

    {{-- Konten --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Edit Kategori {{ $label }}
        </h1>

        <form
            action="{{ route('kategori.update', ['id' => $kategoriPengeluaran->id_kategori_pengeluaran, 'type' => $type]) }}"
            method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                {{-- Nama Kategori --}}
                <div>
                    <label for="nama_kategori" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Kategori
                    </label>
                    <input id="nama_kategori" name="nama_kategori" type="text"
                        value="{{ old('nama_kategori', $kategoriPengeluaran->nama_kategori) }}"
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
                        placeholder="Opsional…">{{ old('deskripsi', $kategoriPengeluaran->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Icon Picker --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih Icon
                    </label>

                    <div id="icon-grid"
                        class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2 p-2 border border-gray-300 dark:border-gray-600 rounded-md">
                        @foreach ($icons as $cls)
                            @php $checked = ($currentIcon === $cls); @endphp
                            <label for="icon_{{ $cls }}" data-icon-label
                                class="flex items-center justify-center p-2 cursor-pointer rounded-lg
                                     border border-gray-300 dark:border-gray-600
                                     hover:bg-gray-200 dark:hover:bg-gray-700
                                     {{ $checked ? 'border-2 border-indigo-500 bg-indigo-100 dark:bg-indigo-900' : '' }}">
                                <input type="radio" id="icon_{{ $cls }}" name="icon"
                                    value="{{ $cls }}" class="sr-only" {{ $checked ? 'checked' : '' }} />
                                <i
                                    class="bx {{ $cls }} text-2xl
                                           {{ $checked ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300' }}"></i>
                            </label>
                        @endforeach
                    </div>
                    @error('icon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tombol --}}
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('kategori.index', ['type' => $type]) }}"
                    class="px-4 py-2 border rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700">
                    Update
                </button>
            </div>
        </form>
    </div>

    {{-- Inline JS untuk highlight icon baru --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const labels = document.querySelectorAll('[data-icon-label]');
            labels.forEach(label => {
                label.addEventListener('click', () => {
                    // hapus kelas highlight dari semua
                    labels.forEach(l => {
                        l.classList.remove('border-2', 'border-indigo-500', 'bg-indigo-100',
                            'dark:bg-indigo-900');
                        l.querySelector('i').classList.remove('text-indigo-600',
                            'dark:text-indigo-400');
                        l.querySelector('i').classList.add('text-gray-600',
                            'dark:text-gray-300');
                    });
                    // tambahkan ke yang diklik
                    label.classList.add('border-2', 'border-indigo-500', 'bg-indigo-100',
                        'dark:bg-indigo-900');
                    const iconEl = label.querySelector('i');
                    iconEl.classList.remove('text-gray-600', 'dark:text-gray-300');
                    iconEl.classList.add('text-indigo-600', 'dark:text-indigo-400');
                });
            });
        });
    </script>
</x-app-layout>
