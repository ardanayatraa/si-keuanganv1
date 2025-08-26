<div class="bg-white p-6 rounded-xl shadow-sm">
    <h2 class="text-lg font-bold mb-4">Jadwal Cicilan Utang</h2>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cicilan Ke</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Jatuh Tempo</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah Cicilan</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah Terbayar</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sisa Cicilan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jadwalCicilan as $cicilan)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $cicilan->cicilan_ke }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $cicilan->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        {{ number_format($cicilan->jumlah_cicilan, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        {{ number_format($cicilan->jumlah_terbayar, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        {{ number_format($cicilan->sisa_cicilan, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="inline-block px-2 py-1 {{ $cicilan->status_class }}">{{ $cicilan->status_text }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
