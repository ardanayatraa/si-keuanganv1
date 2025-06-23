<x-app-layout>
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h1 class="text-2xl font-bold mb-4">Tambah Pembayaran Utang</h1>

        <form action="{{ route('utang.pembayaran.store') }}" method="POST">
            @csrf
            <div class="space-y-6">

                {{-- 1) Pilih Utang --}}
                <div>
                    <label for="id_utang" class="block text-sm font-medium text-gray-700">Utang</label>
                    <select id="id_utang" name="id_utang"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">-- Pilih Utang --</option>
                        @foreach ($utangs as $u)
                            @php
                                $paid = $u->pembayaran->sum('jumlah_dibayar');
                                $remaining = $u->jumlah;
                                $original = $paid + $remaining;
                            @endphp
                            <option value="{{ $u->id_utang }}" data-original="{{ $original }}"
                                data-paid="{{ $paid }}" data-remaining="{{ $remaining }}"
                                data-due="{{ \Carbon\Carbon::parse($u->tanggal_jatuh_tempo)->format('d/m/Y') }}"
                                {{ old('id_utang') == $u->id_utang ? 'selected' : '' }}>
                                {{ $u->pengguna->username }} — sisa {{ number_format($remaining, 2, ',', '.') }}
                                (tempo {{ \Carbon\Carbon::parse($u->tanggal_jatuh_tempo)->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_utang')
                        <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 2) Detail Utang & Progress --}}
                <div id="utang-info" class="hidden p-4 bg-gray-50 rounded">
                    <div class="flex flex-wrap -mx-4 mb-4 text-sm text-gray-700">
                        <div class="w-1/2 sm:w-1/4 px-4 mb-2">
                            <strong>Total Utang:</strong><br>
                            <span id="info-original"></span>
                        </div>
                        <div class="w-1/2 sm:w-1/4 px-4 mb-2">
                            <strong>Sudah Bayar:</strong><br>
                            <span id="info-paid"></span>
                        </div>
                        <div class="w-1/2 sm:w-1/4 px-4 mb-2">
                            <strong>Sisa:</strong><br>
                            <span id="info-remaining"></span>
                        </div>
                        <div class="w-1/2 sm:w-1/4 px-4 mb-2">
                            <strong>Jatuh Tempo:</strong><br>
                            <span id="info-due"></span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="flex-1 bg-gray-200 rounded h-2 overflow-hidden">
                            <div id="info-bar" class="h-2 bg-yellow-600" style="width:0%"></div>
                        </div>
                        <span id="info-pct" class="text-sm font-medium text-gray-700">0%</span>
                    </div>
                </div>

                {{-- 3) Pilih Rekening --}}
                <div>
                    <label for="id_rekening" class="block text-sm font-medium text-gray-700">Rekening</label>
                    <select id="id_rekening" name="id_rekening"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">-- Pilih Rekening --</option>
                        @foreach ($rekenings as $r)
                            <option value="{{ $r->id_rekening }}"
                                {{ old('id_rekening') == $r->id_rekening ? 'selected' : '' }}>
                                {{ $r->nama_rekening }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_rekening')
                        <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 4) Jumlah Dibayar --}}
                <div>
                    <label for="jumlah_dibayar" class="block text-sm font-medium text-gray-700">Jumlah Dibayar</label>
                    <input id="jumlah_dibayar" name="jumlah_dibayar" type="number" step="0.01"
                        value="{{ old('jumlah_dibayar') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                    @error('jumlah_dibayar')
                        <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 5) Tanggal Pembayaran --}}
                <div>
                    <label for="tanggal_pembayaran" class="block text-sm font-medium text-gray-700">Tanggal
                        Pembayaran</label>
                    <input id="tanggal_pembayaran" name="tanggal_pembayaran" type="date"
                        value="{{ old('tanggal_pembayaran', now()->toDateString()) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                    @error('tanggal_pembayaran')
                        <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 6) Metode Pembayaran --}}
                <div>
                    <label for="metode_pembayaran" class="block text-sm font-medium text-gray-700">Metode Pembayaran
                        (opsional)</label>
                    <input id="metode_pembayaran" name="metode_pembayaran" type="text"
                        value="{{ old('metode_pembayaran') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                    @error('metode_pembayaran')
                        <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 7) Deskripsi --}}
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi (opsional)</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Tombol --}}
            <div class="mt-6 flex justify-end">
                <a href="{{ route('utang.pembayaran.index') }}"
                    class="px-4 py-2 mr-2 border rounded-md text-sm text-gray-700 hover:bg-gray-100">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-yellow-600 text-white rounded-md shadow-sm hover:bg-yellow-700">Simpan</button>
            </div>
        </form>
    </div>

    {{-- JS untuk update progress bar --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('id_utang');
            const info = document.getElementById('utang-info');
            const oEl = document.getElementById('info-original');
            const pEl = document.getElementById('info-paid');
            const rEl = document.getElementById('info-remaining');
            const dEl = document.getElementById('info-due');
            const bar = document.getElementById('info-bar');
            const pctEl = document.getElementById('info-pct');

            const fmt = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });

            function update() {
                const opt = sel.selectedOptions[0];
                if (!opt || !opt.value) {
                    info.classList.add('hidden');
                    return;
                }
                const orig = Number(opt.dataset.original);
                const paid = Number(opt.dataset.paid);
                const rem = Number(opt.dataset.remaining);
                const due = opt.dataset.due;

                oEl.textContent = fmt.format(orig);
                pEl.textContent = fmt.format(paid);
                rEl.textContent = fmt.format(rem);
                dEl.textContent = due;

                const pct = orig > 0 ? Math.round((paid / orig) * 100) : 0;
                bar.style.width = pct + '%';
                pctEl.textContent = pct + '%';

                info.classList.remove('hidden');
            }

            sel.addEventListener('change', update);
            update();
        });
    </script>
</x-app-layout>
