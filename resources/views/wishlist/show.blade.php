<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Detail Item Wishlist: {{ $item->nama_item }}
        </h1>

        <div class="mb-6 flex space-x-2">
            <a href="{{ route('wishlist.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700">
                Kembali
            </a>
            <a href="{{ route('wishlist.edit', $item->id_wishlist) }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md shadow-sm hover:bg-yellow-700">
                Edit
            </a>
            <a href="{{ route('wishlist.toggle-status', $item->id_wishlist) }}"
                class="inline-flex items-center px-4 py-2 {{ $item->status === 'pending' ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white rounded-md shadow-sm">
                {{ $item->status === 'pending' ? 'Tandai Tercapai' : 'Tandai Pending' }}
            </a>
            <form action="{{ route('wishlist.destroy', $item->id_wishlist) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Yakin ingin menghapus item wishlist ini?')"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md shadow-sm hover:bg-red-700">
                    Hapus
                </button>
            </form>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Item</h2>

                <div class="space-y-3">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Nama Item:</span>
                        <span class="ml-2 text-gray-900 dark:text-white font-medium">{{ $item->nama_item }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Kategori:</span>
                        <span class="ml-2 text-gray-900 dark:text-white font-medium">{{ $item->kategori }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Estimasi Harga:</span>
                        <span class="ml-2 text-gray-900 dark:text-white font-medium">Rp
                            {{ number_format($item->estimasi_harga, 0, ',', '.') }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Tanggal Target:</span>
                        <span
                            class="ml-2 text-gray-900 dark:text-white font-medium">{{ $item->tanggal_target->format('d/m/Y') }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Status:</span>
                        <span
                            class="ml-2 px-2 py-1 rounded-full text-xs {{ $item->status === 'pending' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ $item->status === 'pending' ? 'Pending' : 'Tercapai' }}
                        </span>
                    </div>

                    @if ($item->sumber_dana)
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Sumber Dana:</span>
                            <span class="ml-2 text-gray-900 dark:text-white">{{ $item->sumber_dana }}</span>
                        </div>
                    @endif

                    @if ($item->keterangan)
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Keterangan:</span>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $item->keterangan }}</p>
                        </div>
                    @endif

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Dibuat pada:</span>
                        <span
                            class="ml-2 text-gray-900 dark:text-white">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Terakhir diperbarui:</span>
                        <span
                            class="ml-2 text-gray-900 dark:text-white">{{ $item->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Progress Tabungan</h2>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-900 dark:text-white">Dana Terkumpul:</span>
                        <span class="text-gray-900 dark:text-white font-bold">Rp
                            {{ number_format($item->dana_terkumpul, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-900 dark:text-white">Estimasi Harga:</span>
                        <span class="text-gray-900 dark:text-white font-bold">Rp
                            {{ number_format($item->estimasi_harga, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-900 dark:text-white">Sisa yang Dibutuhkan:</span>
                        <span class="text-gray-900 dark:text-white font-bold">Rp
                            {{ number_format($item->remaining_amount, 0, ',', '.') }}</span>
                    </div>

                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span>Progress</span>
                            <span>{{ $item->progress_percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-600">
                            <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $item->progress_percentage }}%">
                            </div>
                        </div>
                    </div>
                </div>

                @if ($item->status === 'pending')
                    <div class="mt-6">
                        <h3 class="text-md font-medium text-gray-900 dark:text-white mb-2">Update Progress</h3>

                        <form action="{{ route('wishlist.update-progress', $item->id_wishlist) }}" method="POST">
                            @csrf

                            <div class="space-y-4">
                                <div>
                                    <x-label for="dana_terkumpul" value="Dana Terkumpul (Rp)" />
                                    <x-input id="dana_terkumpul" name="dana_terkumpul" type="number" min="0"
                                        step="1000" class="mt-1 block w-full"
                                        value="{{ old('dana_terkumpul', $item->dana_terkumpul) }}" required />
                                    @error('dana_terkumpul')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end">
                                    <x-button type="submit" class="bg-blue-600 hover:bg-blue-700">
                                        Update Progress
                                    </x-button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="mt-6">
                    <h3 class="text-md font-medium text-gray-900 dark:text-white mb-2">Waktu Tersisa</h3>

                    @php
                        $today = new \DateTime();
                        $targetDate = new \DateTime($item->tanggal_target->format('Y-m-d'));
                        $interval = $today->diff($targetDate);
                        $daysRemaining = $interval->invert ? 0 : $interval->days;
                    @endphp

                    @if ($item->status === 'tercapai')
                        <p class="text-green-600 font-medium">Item wishlist sudah tercapai!</p>
                    @elseif($daysRemaining === 0)
                        <p class="text-red-600 font-medium">Deadline hari ini!</p>
                    @elseif($interval->invert)
                        <p class="text-red-600 font-medium">Melewati deadline {{ $interval->days }} hari!</p>
                    @else
                        <p class="text-gray-900 dark:text-white">
                            <span class="font-bold">{{ $daysRemaining }}</span> hari lagi
                            ({{ $targetDate->format('d/m/Y') }})
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
