<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login - pengaturuangku.my.id</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #000000, #1a1a1a);
        }

        .text-gradient {
            background: linear-gradient(135deg, #FFD700 0%, #FFC107 50%, #FFB300 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .elegant-input {
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .elegant-input:focus {
            border-color: #FFD700;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }

        .elegant-button {
            background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border-radius: 9999px;
        }

        .elegant-button:hover {
            transform: scale(1.03);
        }

        .elegant-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .elegant-button:hover::before {
            left: 100%;
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center px-4">

    <div class="bg-white shadow-2xl rounded-2xl w-full max-w-md p-8 space-y-6">
        <div class="text-center">
            <img src="{{ asset('assets/img/logo.png') }}" class="h-12 mx-auto mb-4" alt="Logo">
            <h2 class="text-2xl font-bold text-primary">Masuk ke <span class="text-gradient">pengaturuangku</span></h2>
        </div>

        @if (session('status'))
            <div class="text-green-600 text-sm text-center mb-4 font-medium">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="text-red-500 text-sm">
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                <input id="email" type="email" name="email" required autofocus
                    class="elegant-input w-full px-4 py-2 rounded-lg focus:outline-none" value="{{ old('email') }}">
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                <input id="password" type="password" name="password" required
                    class="elegant-input w-full px-4 py-2 rounded-lg focus:outline-none">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="mr-2">
                    Ingat saya
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-accent hover:underline">Lupa
                        password?</a>
                @endif
            </div>

            <button type="submit" class="elegant-button w-full text-white py-2 font-semibold text-center">
                Masuk
            </button>
        </form>

        <div class="text-center text-sm text-gray-500">
            Belum punya akun? <a href="{{ route('register') }}" class="text-accent hover:underline">Daftar sekarang</a>
        </div>
    </div>

</body>

</html>
