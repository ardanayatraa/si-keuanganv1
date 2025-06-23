<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Pengguna</h1>
            <a href="{{ route('pengguna.create') }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md shadow-sm hover:bg-yellow-700">
                Tambah Pengguna
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Foto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Email</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase">Saldo</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $i => $u)
                        <tr>
                            <td class="px-6 py-4">{{ $i + 1 }}</td>
                            <td class="px-6 py-4">
                                @if ($u->foto)
                                    <img src="{{ asset('storage/' . $u->foto) }}" alt="Foto"
                                        class="h-10 w-10 rounded-full object-cover">
                                @else
                                    <span class="text-gray-500">–</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $u->username }}</td>
                            <td class="px-6 py-4">{{ $u->email }}</td>
                            <td class="px-6 py-4 text-right">Rp{{ number_format($u->saldo, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center space-x-2">

                                <a href="{{ route('admin.pengguna.edit', $u) }}"
                                    class="px-3 py-1 bg-yellow-500 text-white rounded-md text-sm hover:bg-yellow-600">Edit</a>
                                <form action="{{ route('admin.pengguna.destroy', $u) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1 bg-red-600 text-white rounded-md text-sm hover:bg-red-700">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
