<x-app-layout>
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h1 class="text-2xl font-bold mb-4">Tambah Pembayaran Piutang</h1>

        <form action="{{ route('piutang.pembayaran.store') }}" method="POST">
            @csrf
            <div class="space-y-6">

                {{-- 1) Pilih Piutang --}}
                <div>
                    <label for="id_piutang" class="block text-sm font-medium text-gray-700">Piutang</label>
                    <select id="id_piutang" name="id_piutang"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">-- Pilih Piutang (belum lunas) --</option>
                        @foreach ($piutangs as $piutang)
                            @php
                                $paid = $piutang->pembayaranPiutang->sum('jumlah_dibayar');
                                $original = $piutang->jumlah;
                                $remaining = $original - $paid;
                            @endphp
                            <option value="{{ $piutang->id_piutang }}" data-original="{{ $original }}"
                                data-paid="{{ $paid }}" data-remaining="{{ $remaining }}"
                                data-due="{{ \Carbon\Carbon::parse($piutang->tanggal_jatuh_tempo)->format('d/m/Y') }}"
                                {{ old('id_piutang') == $piutang->id_piutang ? 'selected' : '' }}>
                                {{ $piutang->pengguna->username }} — sisa {{ number_format($remaining, 2, ',', '.') }}
                                (tempo {{ \Carbon\Carbon::parse($piutang->tanggal_jatuh_tempo)->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_piutang')
                        <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 2) Info & Progress Bar --}}
                <div id="piutang-info" class="hidden p-4 bg-gray-50 rounded">
                    <div class="flex flex-wrap -mx-4 mb-4 text-sm text-gray-700">
                        <div class="w-1/2 sm:w-1/4 px-4 mb-2">
                            <strong>Total:</strong><br><span id="orig"></span>
                        </div>
                        <div class="w-1/2 sm:w-1/4 px-4 mb-2">
                            <strong>Sudah Bayar:</strong><br><span id="paid"></span>
                        </div>
                        <div class="w-1/2 sm:w-1/4 px-4 mb-2">
                            <strong>Sisa:</strong><br><span id="rem"></span>
                        </div>
                        <div class="w-1/2 sm:w-1/4 px-4 mb-2">
                            <strong>Tempo:</strong><br><span id="due"></span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="flex-1 bg-gray-200 rounded h-2 overflow-hidden">
                            <div id="bar" class="h-2 bg-yellow-600" style="width:0%"></div>
                        </div>
                        <span id="pct" class="text-sm font-medium text-gray-700">0%</span>
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

                {{-- 6) Metode & Deskripsi --}}
                <div>
                    <label for="metode_pembayaran" class="block text-sm font-medium text-gray-700">Metode
                        Pembayaran</label>
                    <input id="metode_pembayaran" name="metode_pembayaran" type="text"
                        value="{{ old('metode_pembayaran') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                        placeholder="Transfer, Tunai, dll." />
                </div>
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">{{ old('deskripsi') }}</textarea>
                </div>

            </div>

            {{-- Tombol --}}
            <div class="mt-6 flex justify-end">
                <a href="{{ route('piutang.pembayaran.index') }}"
                    class="px-4 py-2 mr-2 border rounded-md text-sm hover:bg-gray-100">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-yellow-600 text-white rounded-md shadow-sm hover:bg-yellow-700">Simpan</button>
            </div>
        </form>
    </div>

    {{-- JS Progress Bar --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('id_piutang');
            const info = document.getElementById('piutang-info');
            const origEl = document.getElementById('orig');
            const paidEl = document.getElementById('paid');
            const remEl = document.getElementById('rem');
            const dueEl = document.getElementById('due');
            const bar = document.getElementById('bar');
            const pctEl = document.getElementById('pct');

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
                const original = +opt.dataset.original;
                const paid = +opt.dataset.paid;
                const remaining = +opt.dataset.remaining;
                const due = opt.dataset.due;

                origEl.textContent = fmt.format(original);
                paidEl.textContent = fmt.format(paid);
                remEl.textContent = fmt.format(remaining);
                dueEl.textContent = due;

                const pct = original > 0 ? Math.round((paid / original) * 100) : 0;
                bar.style.width = pct + '%';
                pctEl.textContent = pct + '%';

                info.classList.remove('hidden');
            }

            sel.addEventListener('change', update);
            update();
        });
    </script>
</x-app-layout>
