<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard Admin
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Card total pengguna --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Total Pengguna</h3>
                        <p class="text-3xl font-bold text-yellow-600">{{ $users->count() }}</p>
                    </div>
                    <svg class="w-12 h-12 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zm0 2c-2.67
              0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </div>
            </div>

            {{-- Tabel daftar pengguna --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Daftar Pengguna</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Username
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($users as $idx => $u)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $idx + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $u->username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $u->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        Rp{{ number_format($u->saldo, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Belum ada pengguna terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
