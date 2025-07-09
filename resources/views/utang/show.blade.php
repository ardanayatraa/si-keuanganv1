<x-app-layout>
<div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
  <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
    Detail Utang
  </h1>

  <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Utang</h3>
        <dl class="space-y-3">
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rekening</dt>
            <dd class="text-sm text-gray-900 dark:text-white">{{ $utang->rekening->nama_rekening }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Utang</dt>
            <dd class="text-sm text-gray-900 dark:text-white">Rp {{ number_format($utang->jumlah, 2, ',', '.') }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Sisa Hutang</dt>
            <dd class="text-sm text-gray-900 dark:text-white">Rp {{ number_format($utang->sisa_hutang, 2, ',', '.') }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
            <dd class="text-sm">
              @if($utang->status == 'aktif')
                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                  Aktif
                </span>
              @else
                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                  Lunas
                </span>
              @endif
            </dd>
          </div>
        </dl>
      </div>
      
      <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Tanggal</h3>
        <dl class="space-y-3">
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pinjam</dt>
            <dd class="text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($utang->tanggal_pinjam)->format('d/m/Y') }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jatuh Tempo</dt>
            <dd class="text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($utang->tanggal_jatuh_tempo)->format('d/m/Y') }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi</dt>
            <dd class="text-sm text-gray-900 dark:text-white">{{ $utang->deskripsi ?? '-' }}</dd>
          </div>
        </dl>
      </div>
    </div>

    <div class="mt-6 flex space-x-3">
      <a href="{{ route('utang.edit', $utang->id_utang) }}" 
         class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
        Edit Utang
      </a>
      <a href="{{ route('utang.index') }}" 
         class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
        Kembali
      </a>
    </div>
  </div>
</div>
</x-app-layout>