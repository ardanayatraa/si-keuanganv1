@component('mail::message')
    # Hai {{ $nama }},

    Saldo **Piutang** Anda sebesar **Rp {{ number_format($jumlah, 0, ',', '.') }}**
    akan jatuh tempo pada **{{ $due_date }}** (3 hari lagi).

    @component('mail::button', ['url' => route('piutang.index')])
        Lihat Daftar Piutang
    @endcomponent

    Terima kasih,<br>{{ config('app.name') }}
@endcomponent
