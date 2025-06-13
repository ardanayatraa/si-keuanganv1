<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pengaturuangku.my.id - Kelola Keuangan Pribadi dengan Mudah</title>
    <meta name="description"
        content="Sistem manajemen keuangan pribadi yang aman, mudah digunakan, dan efisien untuk era digital">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#000000',
                        secondary: '#1a1a1a',
                        accent: '#FFD700',
                        'accent-dark': '#FFC107',
                        dark: '#0a0a0a',
                        light: '#f8f9fa',
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s ease-out',
                        'fade-in-down': 'fadeInDown 0.8s ease-out',
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'pulse-slow': 'pulse 3s ease-in-out infinite',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes glow {
            from {
                box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
            }

            to {
                box-shadow: 0 0 30px rgba(255, 215, 0, 0.6);
            }
        }

        .gradient-bg {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 25%, #2d2d2d 50%, #1a1a1a 75%, #000000 100%);
            position: relative;
        }

        .gradient-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 80%, rgba(255, 215, 0, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 215, 0, 0.05) 0%, transparent 50%);
        }

        .glass-effect {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.2);
        }

        .elegant-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .elegant-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .elegant-button {
            background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
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

        .dark-button {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            border: 2px solid #FFD700;
        }

        .section-divider {
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, #FFD700 50%, transparent 100%);
            margin: 0 auto;
            width: 120px;
        }

        .floating-element {
            animation: float 6s ease-in-out infinite;
        }

        .text-gradient {
            background: linear-gradient(135deg, #FFD700 0%, #FFC107 50%, #FFB300 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .elegant-input {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
        }

        .elegant-input:focus {
            border-color: #FFD700;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }

        .feature-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            border-color: #FFD700;
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .stats-card {
            background: linear-gradient(145deg, #000000 0%, #1a1a1a 100%);
            border: 2px solid #FFD700;
        }
    </style>
</head>

<body class="font-sans antialiased bg-light">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-500" x-data="{ isOpen: false, scrolled: false }" x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
        :class="scrolled ? 'bg-white/95 backdrop-blur-md shadow-xl' : 'bg-transparent'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="flex items-center text-xl md:text-2xl font-bold"
                            :class="scrolled ? 'text-primary' : 'text-white'">
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo PengaturUangku"
                                    class="h-12 w-auto" />
                            </a>
                            <span class="ml-2">pengaturuangku<span
                                    :class="scrolled ? 'text-primary' : 'text-white'"></span></span>
                        </h1>
                    </div>
                </div>


                <!-- Desktop Menu -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-6 lg:space-x-8">
                        <a href="#home" class="hover:text-accent transition-colors font-medium text-sm lg:text-base"
                            :class="scrolled ? 'text-gray-700' : 'text-white'">Beranda</a>
                        <a href="#about" class="hover:text-accent transition-colors font-medium text-sm lg:text-base"
                            :class="scrolled ? 'text-gray-700' : 'text-white'">Tentang</a>
                        <a href="/login">
                            <button
                                class="elegant-button text-primary px-4 lg:px-6 py-2 font-semibold tracking-wide relative overflow-hidden rounded-full text-sm lg:text-base">
                                Mulai Sekarang
                            </button>
                        </a>

                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button @click="isOpen = !isOpen" class="p-2" :class="scrolled ? 'text-gray-700' : 'text-white'">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="isOpen" x-transition class="md:hidden bg-white/95 backdrop-blur-md shadow-xl rounded-b-2xl">
            <div class="px-4 pt-2 pb-4 space-y-2">
                <a href="#home"
                    class="block px-3 py-2 text-gray-700 hover:text-accent font-medium rounded-lg">Beranda</a>
                <a href="#features"
                    class="block px-3 py-2 text-gray-700 hover:text-accent font-medium rounded-lg">Fitur</a>
                <a href="#about"
                    class="block px-3 py-2 text-gray-700 hover:text-accent font-medium rounded-lg">Tentang</a>
                <a href="#contact"
                    class="block px-3 py-2 text-gray-700 hover:text-accent font-medium rounded-lg">Kontak</a>
                <a href="/login">

                    <button
                        class="w-full text-left elegant-button text-primary px-3 py-2 mt-2 font-semibold rounded-lg">
                        Mulai Sekarang
                    </button>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="min-h-screen gradient-bg flex items-center relative overflow-hidden">
        <!-- Floating Elements -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-20 left-10 w-16 h-16 md:w-20 md:h-20 bg-accent/10 rounded-full floating-element">
            </div>
            <div class="absolute top-1/3 right-20 w-12 h-12 md:w-16 md:h-16 bg-accent/10 rounded-full floating-element"
                style="animation-delay: 2s;"></div>
            <div class="absolute bottom-20 left-1/4 w-10 h-10 md:w-12 md:h-12 bg-accent/10 rounded-full floating-element"
                style="animation-delay: 4s;"></div>
        </div>

        <div class="max-w-7xl sm:pt-20px mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
                <div class="text-white animate-fade-in-up text-center lg:text-left">
                    <div class="inline-block px-4 py-2 bg-accent/20 backdrop-blur-sm mb-6 rounded-full">
                        <span class="text-sm font-medium text-accent">💰 Kelola Uang Mudah</span>
                    </div>
                    <h1
                        class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold leading-tight mb-6 lg:mb-8">
                        Atur Uang
                        <span class="text-gradient">Pribadi</span> dengan
                        <span class="text-accent">Mudah</span>
                    </h1>
                    <p
                        class="text-lg sm:text-xl lg:text-2xl mb-8 lg:mb-10 text-gray-300 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Platform sederhana untuk mengatur keuangan harian Anda. Mudah digunakan, aman, dan praktis.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 lg:gap-6 justify-center lg:justify-start">
                        <button
                            class="elegant-button text-primary px-8 lg:px-10 py-3 lg:py-4 text-lg font-bold transition-all transform hover:scale-105 hover:shadow-2xl relative overflow-hidden rounded-full">
                            <i class="fas fa-rocket mr-3"></i>Mulai Gratis
                        </button>

                    </div>
                </div>

                <div class="animate-fade-in-down mt-8 lg:mt-0">
                    <div
                        class="glass-effect rounded-3xl transform hover:scale-105 transition-transform duration-500 p-4 lg:p-8">
                        <div class="bg-white rounded-2xl p-6 lg:p-8 shadow-2xl">
                            <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                                <h3 class="text-lg lg:text-xl font-bold text-primary">Dashboard Keuangan</h3>
                                <span
                                    class="text-green-600 text-sm font-bold bg-green-50 px-3 lg:px-4 py-1 lg:py-2 rounded-full">+12.5%</span>
                            </div>
                            <div class="space-y-4 lg:space-y-6">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium text-sm lg:text-base">Total Saldo</span>
                                    <span class="font-bold text-lg lg:text-2xl text-primary">Rp 15.750.000</span>
                                </div>
                                <div class="w-full bg-gray-100 h-2 lg:h-3 rounded-full">
                                    <div class="bg-gradient-to-r from-primary to-accent h-2 lg:h-3 rounded-full animate-glow"
                                        style="width: 75%"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 lg:gap-6 mt-6">
                                    <div
                                        class="text-center p-3 lg:p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                                        <div class="text-green-600 font-semibold mb-1 text-sm lg:text-base">Pemasukan
                                        </div>
                                        <div class="text-lg lg:text-xl font-bold text-green-700">Rp 8.5M</div>
                                    </div>
                                    <div
                                        class="text-center p-3 lg:p-4 bg-gradient-to-br from-red-50 to-red-100 rounded-xl">
                                        <div class="text-red-600 font-semibold mb-1 text-sm lg:text-base">Pengeluaran
                                        </div>
                                        <div class="text-lg lg:text-xl font-bold text-red-700">Rp 6.2M</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-16 lg:py-24 bg-gradient-to-b from-light to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 lg:mb-20">
                <div class="section-divider mb-8"></div>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-primary mb-6">Fitur <span
                        class="text-gradient">Utama</span></h2>
                <p class="text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Semua yang Anda butuhkan untuk mengatur keuangan dengan mudah dan aman
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <div class="feature-card p-6 lg:p-8 shadow-lg group rounded-2xl border-l-4 border-accent">
                    <div
                        class="w-14 h-14 lg:w-16 lg:h-16 bg-gradient-to-br from-accent/10 to-accent/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 rounded-xl">
                        <i class="fas fa-chart-pie text-xl lg:text-2xl text-accent"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-bold mb-4 text-primary">Analisis Keuangan</h3>
                    <p class="text-gray-600 leading-relaxed text-sm lg:text-base">
                        Lihat pengeluaran dan pemasukan Anda dengan grafik yang mudah dipahami.
                    </p>
                </div>

                <div class="feature-card p-6 lg:p-8 shadow-lg group rounded-2xl border-l-4 border-green-500">
                    <div
                        class="w-14 h-14 lg:w-16 lg:h-16 bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 rounded-xl">
                        <i class="fas fa-shield-alt text-xl lg:text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-bold mb-4 text-primary">Keamanan Terjamin</h3>
                    <p class="text-gray-600 leading-relaxed text-sm lg:text-base">
                        Data Anda aman dengan enkripsi tingkat bank.
                    </p>
                </div>

                <div class="feature-card p-6 lg:p-8 shadow-lg group rounded-2xl border-l-4 border-blue-500">
                    <div
                        class="w-14 h-14 lg:w-16 lg:h-16 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 rounded-xl">
                        <i class="fas fa-mobile-alt text-xl lg:text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-bold mb-4 text-primary">Responsive Design</h3>
                    <p class="text-gray-600 leading-relaxed text-sm lg:text-base">
                        Bisa diakses di HP, tablet, dan komputer dengan tampilan yang bagus.
                    </p>
                </div>

                <div class="feature-card p-6 lg:p-8 shadow-lg group rounded-2xl border-l-4 border-purple-500">
                    <div
                        class="w-14 h-14 lg:w-16 lg:h-16 bg-gradient-to-br from-purple-100 to-purple-200 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 rounded-xl">
                        <i class="fas fa-bell text-xl lg:text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-bold mb-4 text-primary">Notifikasi Pintar</h3>
                    <p class="text-gray-600 leading-relaxed text-sm lg:text-base">
                        Dapat pengingat untuk tagihan dan target tabungan.
                    </p>
                </div>

                <div class="feature-card p-6 lg:p-8 shadow-lg group rounded-2xl border-l-4 border-red-500">
                    <div
                        class="w-14 h-14 lg:w-16 lg:h-16 bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 rounded-xl">
                        <i class="fas fa-sync-alt text-xl lg:text-2xl text-red-600"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-bold mb-4 text-primary">Sinkronisasi Real-time</h3>
                    <p class="text-gray-600 leading-relaxed text-sm lg:text-base">
                        Data selalu update di semua perangkat Anda.
                    </p>
                </div>

                <div class="feature-card p-6 lg:p-8 shadow-lg group rounded-2xl border-l-4 border-yellow-500">
                    <div
                        class="w-14 h-14 lg:w-16 lg:h-16 bg-gradient-to-br from-yellow-100 to-yellow-200 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 rounded-xl">
                        <i class="fas fa-cogs text-xl lg:text-2xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-lg lg:text-xl font-bold mb-4 text-primary">Mudah Digunakan</h3>
                    <p class="text-gray-600 leading-relaxed text-sm lg:text-base">
                        Interface sederhana yang mudah dipahami siapa saja.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div>
                    <div class="section-divider mb-8"></div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-primary mb-6 lg:mb-8">Tentang <span
                            class="text-gradient">Kami</span></h2>
                    <p class="text-base lg:text-lg text-gray-600 mb-6 leading-relaxed">
                        pengaturuangku.my.id adalah website untuk membantu Anda mengatur keuangan pribadi dengan mudah
                        dan praktis.
                    </p>
                    <p class="text-base lg:text-lg text-gray-600 mb-8 lg:mb-10 leading-relaxed">
                        Kami fokus pada kemudahan, keamanan, dan tampilan yang sederhana untuk semua orang.
                    </p>

                    <div class="grid grid-cols-2 gap-4 lg:gap-8">

                        <div class="text-center stats-card p-4 lg:p-6 rounded-2xl">
                            <div class="text-2xl lg:text-4xl font-bold text-accent mb-2">99.9%</div>
                            <div class="text-gray-300 font-medium text-sm lg:text-base">Uptime</div>
                        </div>
                        <div class="text-center stats-card p-4 lg:p-6 rounded-2xl">
                            <div class="text-2xl lg:text-4xl font-bold text-accent mb-2">24/7</div>
                            <div class="text-gray-300 font-medium text-sm lg:text-base">Support</div>
                        </div>
                        <div class="text-center stats-card p-4 lg:p-6 rounded-2xl">
                            <div class="text-2xl lg:text-4xl font-bold text-accent mb-2">256-bit</div>
                            <div class="text-gray-300 font-medium text-sm lg:text-base">Enkripsi</div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div
                        class="bg-gradient-to-br from-primary to-secondary p-8 lg:p-10 text-white rounded-3xl shadow-2xl border-2 border-accent">
                        <h3 class="text-2xl lg:text-3xl font-bold mb-6 lg:mb-8">Kenapa Pilih Kami?</h3>
                        <ul class="space-y-4 lg:space-y-6">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle mr-3 lg:mr-4 text-accent text-lg lg:text-xl"></i>
                                <span class="text-base lg:text-lg">Teknologi terbaru dan mudah</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle mr-3 lg:mr-4 text-accent text-lg lg:text-xl"></i>
                                <span class="text-base lg:text-lg">Sistem yang sudah teruji</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle mr-3 lg:mr-4 text-accent text-lg lg:text-xl"></i>
                                <span class="text-base lg:text-lg">Cocok untuk zaman sekarang</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle mr-3 lg:mr-4 text-accent text-lg lg:text-xl"></i>
                                <span class="text-base lg:text-lg">Keamanan data terjamin</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-white py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10">
                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="flex-shrink-0">
                        <h1 class="flex items-center text-xl md:text-2xl font-bold"
                            :class="scrolled ? 'text-primary' : 'text-white'">
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo PengaturUangku"
                                    class="h-12 w-auto" />
                            </a>
                            <span class="ml-2">pengaturuangku<span
                                    :class="scrolled ? 'text-primary' : 'text-white'"></span></span>
                        </h1>
                    </div>
                    <p class="text-gray-400 mb-4 lg:mb-6 leading-relaxed text-sm lg:text-base">
                        Platform sederhana untuk mengatur keuangan pribadi yang aman dan mudah digunakan.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="w-10 h-10 bg-accent/20 flex items-center justify-center rounded-xl text-accent hover:bg-accent hover:text-primary transition-all">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-accent/20 flex items-center justify-center rounded-xl text-accent hover:bg-accent hover:text-primary transition-all">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-accent/20 flex items-center justify-center rounded-xl text-accent hover:bg-accent hover:text-primary transition-all">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-accent/20 flex items-center justify-center rounded-xl text-accent hover:bg-accent hover:text-primary transition-all">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>


            </div>

            <div
                class="border-t border-gray-800 mt-8 lg:mt-12 pt-6 lg:pt-8 text-center text-gray-400 text-sm lg:text-base">
                <p>&copy; 2024 pengaturuangku. Semua hak dilindungi undang-undang.</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollToTop"
        class="fixed bottom-6 right-6 lg:bottom-8 lg:right-8 elegant-button text-primary p-3 lg:p-4 shadow-2xl transition-all transform hover:scale-110 opacity-0 invisible rounded-full">
        <i class="fas fa-arrow-up text-base lg:text-lg"></i>
    </button>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Scroll to top functionality
        const scrollToTopBtn = document.getElementById('scrollToTop');

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.remove('opacity-0', 'invisible');
                scrollToTopBtn.classList.add('opacity-100', 'visible');
            } else {
                scrollToTopBtn.classList.add('opacity-0', 'invisible');
                scrollToTopBtn.classList.remove('opacity-100', 'visible');
            }
        });

        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);

        // Observe all sections
        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });
    </script>
</body>

</html>
