<x-app-layout>
    @php
        $type = request()->query('type', 'pengeluaran');
        $label = ucfirst($type);
        $colors = [
            'bg-red-100 text-red-800',
            'bg-green-100 text-green-800',
            'bg-blue-100 text-blue-800',
            'bg-yellow-100 text-yellow-800',
            'bg-purple-100 text-purple-800',
            'bg-pink-100 text-pink-800',
            'bg-indigo-100 text-indigo-800',
            'bg-teal-100 text-teal-800',
        ];
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

    {{-- KONTEN --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Kategori {{ $label }}</h1>
            <a href="{{ route('kategori.create', ['type' => $type]) }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700">
                Tambah
            </a>
        </div>

        @if ($items->isEmpty())
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                Belum ada kategori {{ strtolower($label) }}.
            </div>
        @else
            <div class="flex flex-col space-y-4">
                @foreach ($items as $item)
                    @php
                        $fieldId = 'id_kategori_' . $type;
                        $id = $item->{$fieldId};
                        $c = $colors[$loop->index % count($colors)];
                    @endphp

                    <div
                        class="flex items-center justify-between rounded-lg px-4 py-3 border border-gray-200 dark:border-gray-600 {{ $c }}">
                        {{-- Icon + Nama & Deskripsi sebaris --}}
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 flex items-center justify-center">
                                @if ($item->icon)
                                    <i class="bx {{ $item->icon }} text-3xl"></i>
                                @else
                                    <i class="bx bx-category text-3xl"></i>
                                @endif
                            </div>
                            <div class="flex items-baseline space-x-6">
                                <h2 class="font-semibold text-lg">{{ $item->nama_kategori }}</h2>
                                @if ($item->deskripsi)
                                    <span class="text-sm opacity-75">{{ $item->deskripsi }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Aksi --}}
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('kategori.edit', ['id' => $id, 'type' => $type]) }}"
                                class="p-2 bg-white dark:bg-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
                                <i class="bx bx-edit-alt"></i>
                            </a>
                            <form action="{{ route('kategori.destroy', ['id' => $id, 'type' => $type]) }}"
                                method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 bg-white dark:bg-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
