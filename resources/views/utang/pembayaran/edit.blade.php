<x-app-layout>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Edit Pembayaran Utang
        </h1>

        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <form action="{{ route('utang.pembayaran.update', $p->id_pembayaran_utang) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    {{-- 1) Pilih Utang --}}
                    <div>
                        <label for="id_utang" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Utang
                        </label>
                        <select id="id_utang" name="id_utang"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white">
                            <option value="">-- Pilih Utang --</option>
                            @foreach ($utangs as $utang)
                                @php
                                    // hitung original & paid untuk masing-masing utang
                                    $paid = $utang->pembayaranUtang->sum('jumlah_dibayar');
                                    $original = $utang->jumlah;
                                    $remaining = $utang->sisa_hutang;
                                @endphp
                                <option value="{{ $utang->id_utang }}" data-original="{{ $original }}"
                                    data-paid="{{ $paid }}" data-remaining="{{ $remaining }}"
                                    data-due="{{ \Carbon\Carbon::parse($utang->tanggal_jatuh_tempo)->format('d/m/Y') }}"
                                    {{ old('id_utang', $p->id_utang) == $utang->id_utang ? 'selected' : '' }}>
                                    {{ $utang->pengguna->username }} — sisa {{ number_format($remaining, 2, ',', '.') }}
                                    (tempo {{ \Carbon\Carbon::parse($utang->tanggal_jatuh_tempo)->format('d/m/Y') }})
                                    @if ($utang->status == 'lunas')
                                        <span class="text-green-600">(Lunas)</span>
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('id_utang')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2) Detail Utang & Progress --}}
                    <div id="utang-info" class="hidden p-4 bg-gray-100 dark:bg-gray-800 rounded">
                        <div class="flex flex-wrap -mx-4 mb-4 text-sm text-gray-700 dark:text-gray-300">
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
                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded h-2 overflow-hidden">
                                <div id="info-bar" class="h-2 bg-yellow-600" style="width:0%"></div>
                            </div>
                            <span id="info-pct" class="text-sm font-medium text-gray-700 dark:text-gray-300">0%</span>
                        </div>
                        <div id="installment-info" class="mt-4">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Detail Cicilan</h2>
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">No.</th>
                                        <th scope="col" class="px-6 py-3">Tanggal Jatuh Tempo</th>
                                        <th scope="col" class="px-6 py-3">Jumlah Cicilan</th>
                                        <th scope="col" class="px-6 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="installment-table-body">
                                    <!-- Installment rows will be dynamically inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Rekening --}}
                    <div>
                        <label for="id_rekening" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Rekening
                        </label>
                        <select id="id_rekening" name="id_rekening"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white">
                            @foreach ($rekenings as $r)
                                <option value="{{ $r->id_rekening }}"
                                    {{ old('id_rekening', $p->id_rekening) == $r->id_rekening ? 'selected' : '' }}>
                                    {{ $r->nama_rekening }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_rekening')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jumlah Dibayar --}}
                    <div>
                        <label for="jumlah_dibayar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Jumlah Dibayar
                        </label>
                        <input id="jumlah_dibayar" name="jumlah_dibayar" type="number" step="0.01"
                            value="{{ old('jumlah_dibayar', $p->jumlah_dibayar) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('jumlah_dibayar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Pembayaran --}}
                    <div>
                        <label for="tanggal_pembayaran"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tanggal Pembayaran
                        </label>
                        <input id="tanggal_pembayaran" name="tanggal_pembayaran" type="date"
                            value="{{ old('tanggal_pembayaran', \Carbon\Carbon::parse($p->tanggal_pembayaran)->format('Y-m-d')) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white" />
                        @error('tanggal_pembayaran')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div>
                        <label for="metode_pembayaran"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Metode Pembayaran (opsional)
                        </label>
                        <input id="metode_pembayaran" name="metode_pembayaran" type="text"
                            value="{{ old('metode_pembayaran', $p->metode_pembayaran) }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Transfer, Tunai, dll." />
                        @error('metode_pembayaran')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Deskripsi (opsional)
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Opsional…">{{ old('deskripsi', $p->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('utang.pembayaran.index') }}"
                        class="px-4 py-2 mr-2 border rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700">
                        Update
                    </button>
                </div>
            </form>
        </div>
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
            update(); // inisialisasi saat memuat form

            // Function to update installment table
            function updateInstallments() {
                const opt = sel.selectedOptions[0];
                if (!opt || !opt.value) {
                    document.getElementById('installment-table-body').innerHTML = '';
                    return;
                }

                const utangId = opt.value;
                fetch(`/api/utang/${utangId}/installments`)
                    .then(response => response.json())
                    .then(data => {
                        const tbody = document.getElementById('installment-table-body');
                        tbody.innerHTML = '';

                        data.forEach((installment, index) => {
                            const row = document.createElement('tr');

                            const noCell = document.createElement('td');
                            noCell.className = 'px-6 py-4';
                            noCell.textContent = index + 1;

                            const dateCell = document.createElement('td');
                            dateCell.className = 'px-6 py-4';
                            dateCell.textContent = installment.tanggal_jatuh_tempo;

                            const amountCell = document.createElement('td');
                            amountCell.className = 'px-6 py-4';
                            amountCell.textContent = new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR'
                            }).format(installment.jumlah_cicilan);

                            const statusCell = document.createElement('td');
                            statusCell.className = 'px-6 py-4';
                            statusCell.textContent = installment.status;

                            row.appendChild(noCell);
                            row.appendChild(dateCell);
                            row.appendChild(amountCell);
                            row.appendChild(statusCell);

                            tbody.appendChild(row);
                        });
                    })
                    .catch(error => console.error('Error fetching installments:', error));
            }

            // Call updateInstallments when the utang selection changes
            sel.addEventListener('change', updateInstallments);
            updateInstallments(); // inisialisasi saat memuat form
        });
    </script>
</x-app-layout>
