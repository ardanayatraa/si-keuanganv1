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
                                $remaining = $u->sisa_hutang;
                                $original = $u->jumlah;
                            @endphp
                            <option value="{{ $u->id_utang }}" data-original="{{ $original }}"
                                data-paid="{{ $paid }}" data-remaining="{{ $remaining }}"
                                data-due="{{ \Carbon\Carbon::parse($u->tanggal_jatuh_tempo)->format('d/m/Y') }}"
                                data-jangka-waktu="{{ $u->jangka_waktu_bulan ?? 0 }}"
                                data-cicilan-bulanan="{{ $u->jumlah_cicilan_per_bulan ?? 0 }}"
                                {{ old('id_utang') == $u->id_utang ? 'selected' : '' }}>
                                {{ $u->pengguna->username }} — sisa {{ number_format($remaining, 2, ',', '.') }}
                                (tempo {{ \Carbon\Carbon::parse($u->tanggal_jatuh_tempo)->format('d/m/Y') }})
                                @if ($u->jangka_waktu_bulan)
                                    <span class="text-blue-600">(Cicilan)</span>
                                @endif
                                @if ($u->status == 'lunas')
                                    <span class="text-green-600">(Lunas)</span>
                                @endif
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
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="flex-1 bg-gray-200 rounded h-2 overflow-hidden">
                            <div id="info-bar" class="h-2 bg-yellow-600" style="width:0%"></div>
                        </div>
                        <span id="info-pct" class="text-sm font-medium text-gray-700">0%</span>
                    </div>

                    {{-- Informasi Cicilan --}}
                    <div id="cicilan-info" class="hidden border-t border-gray-300 pt-4 mt-4">
                        <h4 class="font-semibold text-gray-800 mb-2">📅 Informasi Cicilan</h4>
                        <div class="flex flex-wrap -mx-4 text-sm text-gray-700">
                            <div class="w-1/2 sm:w-1/3 px-4 mb-2">
                                <strong>Jangka Waktu:</strong><br>
                                <span id="info-jangka-waktu"></span> bulan
                            </div>
                            <div class="w-1/2 sm:w-1/3 px-4 mb-2">
                                <strong>Cicilan per Bulan:</strong><br>
                                <span id="info-cicilan-bulanan"></span>
                            </div>
                            <div class="w-full sm:w-1/3 px-4 mb-2">
                                <strong>Status Cicilan:</strong><br>
                                <span id="info-status-cicilan"
                                    class="inline-block px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">Aktif</span>
                            </div>
                        </div>
                        <div class="mt-3 p-3 bg-blue-50 rounded border-l-4 border-blue-400">
                            <p class="text-sm text-blue-700">
                                💡 <strong>Saran:</strong> Jumlah cicilan per bulan adalah <span
                                    id="suggestion-amount"></span>.
                                Anda dapat membayar sesuai cicilan atau lebih besar untuk melunasi lebih cepat.
                            </p>
                        </div>
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
            const cicilanInfo = document.getElementById('cicilan-info');
            const oEl = document.getElementById('info-original');
            const pEl = document.getElementById('info-paid');
            const rEl = document.getElementById('info-remaining');
            const dEl = document.getElementById('info-due');
            const bar = document.getElementById('info-bar');
            const pctEl = document.getElementById('info-pct');

            // Elemen cicilan
            const jangkaWaktuEl = document.getElementById('info-jangka-waktu');
            const cicilanBulananEl = document.getElementById('info-cicilan-bulanan');
            const statusCicilanEl = document.getElementById('info-status-cicilan');
            const suggestionAmountEl = document.getElementById('suggestion-amount');

            const fmt = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });

            function update() {
                const opt = sel.selectedOptions[0];
                if (!opt || !opt.value) {
                    info.classList.add('hidden');
                    cicilanInfo.classList.add('hidden');
                    return;
                }
                const orig = Number(opt.dataset.original);
                const paid = Number(opt.dataset.paid);
                const rem = Number(opt.dataset.remaining);
                const due = opt.dataset.due;
                const jangkaWaktu = Number(opt.dataset.jangkaWaktu);
                const cicilanBulanan = Number(opt.dataset.cicilanBulanan);

                oEl.textContent = fmt.format(orig);
                pEl.textContent = fmt.format(paid);
                rEl.textContent = fmt.format(rem);
                dEl.textContent = due;

                const pct = orig > 0 ? Math.round((paid / orig) * 100) : 0;
                bar.style.width = pct + '%';
                pctEl.textContent = pct + '%';

                // Update informasi cicilan
                if (jangkaWaktu > 0 && cicilanBulanan > 0) {
                    jangkaWaktuEl.textContent = jangkaWaktu;
                    cicilanBulananEl.textContent = fmt.format(cicilanBulanan);
                    suggestionAmountEl.textContent = fmt.format(cicilanBulanan);

                    // Set status cicilan berdasarkan sisa utang
                    if (rem <= 0) {
                        statusCicilanEl.textContent = 'Lunas';
                        statusCicilanEl.className =
                            'inline-block px-2 py-1 text-xs rounded bg-green-100 text-green-800';
                    } else {
                        statusCicilanEl.textContent = 'Aktif';
                        statusCicilanEl.className =
                            'inline-block px-2 py-1 text-xs rounded bg-blue-100 text-blue-800';
                    }

                    cicilanInfo.classList.remove('hidden');
                } else {
                    cicilanInfo.classList.add('hidden');
                }

                info.classList.remove('hidden');
            }

            sel.addEventListener('change', update);
            update();
        });
    </script>
</x-app-layout>
