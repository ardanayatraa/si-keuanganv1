<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Daftar Wishlist
        </h1>

        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('wishlist.create') }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md shadow-sm hover:bg-yellow-700">
                Tambah Item Wishlist
            </a>
            @if (session('success'))
                <div class="px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        @if ($items->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-700 p-6 rounded-xl shadow-md text-white">
                    <h2 class="text-xl font-semibold mb-2">Total Estimasi Biaya</h2>
                    <p class="text-3xl font-bold">Rp {{ number_format($totalEstimasi, 0, ',', '.') }}</p>
                    <p class="mt-2 text-blue-100">Total estimasi biaya untuk semua item wishlist yang belum tercapai.
                    </p>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-green-700 p-6 rounded-xl shadow-md text-white">
                    <h2 class="text-xl font-semibold mb-2">Total Dana Terkumpul</h2>
                    <p class="text-3xl font-bold">Rp {{ number_format($totalTerkumpul, 0, ',', '.') }}</p>
                    <div class="mt-2">
                        <div class="w-full bg-green-800 rounded-full h-2.5">
                            <div class="bg-white h-2.5 rounded-full"
                                style="width: {{ $totalEstimasi > 0 ? min(100, ($totalTerkumpul / $totalEstimasi) * 100) : 0 }}%">
                            </div>
                        </div>
                        <p class="mt-1 text-green-100">
                            {{ $totalEstimasi > 0 ? number_format(min(100, ($totalTerkumpul / $totalEstimasi) * 100), 1) : 0 }}%
                            dari total target
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Item Wishlist</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($items as $item)
                        <div
                            class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-600 {{ $item->status === 'tercapai' ? 'opacity-75' : '' }}">
                            <div class="p-4">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $item->nama_item }}</h3>
                                    <span
                                        class="px-2 py-1 rounded-full text-xs {{ $item->status === 'pending' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $item->status === 'pending' ? 'Pending' : 'Tercapai' }}
                                    </span>
                                </div>

                                <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                    <p><span class="font-medium">Kategori:</span> {{ $item->kategori }}</p>
                                    <p><span class="font-medium">Estimasi Harga:</span> Rp
                                        {{ number_format($item->estimasi_harga, 0, ',', '.') }}</p>
                                    <p><span class="font-medium">Target:</span>
                                        {{ $item->tanggal_target->format('d/m/Y') }}</p>

                                    @if ($item->status === 'pending')
                                        <div class="mt-2">
                                            <div class="flex justify-between text-xs mb-1">
                                                <span>Progress</span>
                                                <span>{{ $item->progress_percentage }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-600">
                                                <div class="bg-blue-600 h-2.5 rounded-full"
                                                    style="width: {{ $item->progress_percentage }}%"></div>
                                            </div>
                                            <p class="text-xs mt-1">
                                                Rp {{ number_format($item->dana_terkumpul, 0, ',', '.') }} dari Rp
                                                {{ number_format($item->estimasi_harga, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-4 flex space-x-2">
                                    <a href="{{ route('wishlist.show', $item->id_wishlist) }}"
                                        class="inline-block px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm">
                                        Detail
                                    </a>
                                    <a href="{{ route('wishlist.edit', $item->id_wishlist) }}"
                                        class="inline-block px-3 py-1 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-sm">
                                        Edit
                                    </a>
                                    <a href="{{ route('wishlist.toggle-status', $item->id_wishlist) }}"
                                        class="inline-block px-3 py-1 {{ $item->status === 'pending' ? 'bg-green-500 hover:bg-green-600' : 'bg-blue-500 hover:bg-blue-600' }} text-white rounded-md text-sm">
                                        {{ $item->status === 'pending' ? 'Tandai Tercapai' : 'Tandai Pending' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($itemsByCategory->count() > 0)
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Wishlist Berdasarkan Kategori
                    </h2>

                    <div class="space-y-6">
                        @foreach ($itemsByCategory as $category => $categoryItems)
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $category }}
                                </h3>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                        <thead class="bg-gray-100 dark:bg-gray-800">
                                            <tr>
                                                <th
                                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                    Item</th>
                                                <th
                                                    class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                    Estimasi</th>
                                                <th
                                                    class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                    Terkumpul</th>
                                                <th
                                                    class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                    Progress</th>
                                                <th
                                                    class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                    Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                            @foreach ($categoryItems as $item)
                                                <tr
                                                    class="{{ $item->status === 'tercapai' ? 'bg-gray-50 dark:bg-gray-600' : '' }}">
                                                    <td class="px-4 py-2 whitespace-nowrap">
                                                        <a href="{{ route('wishlist.show', $item->id_wishlist) }}"
                                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                            {{ $item->nama_item }}
                                                        </a>
                                                    </td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-right">
                                                        {{ number_format($item->estimasi_harga, 0, ',', '.') }}
                                                    </td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-right">
                                                        {{ number_format($item->dana_terkumpul, 0, ',', '.') }}
                                                    </td>
                                                    <td class="px-4 py-2">
                                                        <div
                                                            class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                            <div class="bg-blue-600 h-2 rounded-full"
                                                                style="width: {{ $item->progress_percentage }}%"></div>
                                                        </div>
                                                        <div class="text-xs text-center mt-1">
                                                            {{ $item->progress_percentage }}%</div>
                                                    </td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                                        <span
                                                            class="px-2 py-1 rounded-full text-xs {{ $item->status === 'pending' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                            {{ $item->status === 'pending' ? 'Pending' : 'Tercapai' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="bg-gray-50 dark:bg-gray-700 p-8 rounded-lg text-center">
                <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada item wishlist.</p>
                <a href="{{ route('wishlist.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md shadow-sm hover:bg-yellow-700">
                    Tambah Item Wishlist Pertama
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
