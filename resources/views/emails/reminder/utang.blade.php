@component('mail::message')
    # Hai {{ $nama }},

    Saldo **Utang** Anda sebesar **Rp {{ number_format($jumlah, 0, ',', '.') }}**
    akan jatuh tempo pada **{{ $due_date }}** (3 hari lagi).

    @component('mail::button', ['url' => route('utang.index')])
        Lihat Daftar Utang
    @endcomponent

    Terima kasih,<br>{{ config('app.name') }}
@endcomponent
