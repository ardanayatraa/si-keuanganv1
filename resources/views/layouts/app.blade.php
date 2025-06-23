<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pengaturuangku') }}</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Mobile menu overlay -->
        <div id="mobile-menu-overlay"
            class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden transition-opacity duration-300 ease-in-out"
            aria-hidden="true">
        </div>

        <!-- Sidebar Navigation -->
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col"
            aria-label="Main navigation">

            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-6 bg-yellow-500 text-white">
                <h1 class="text-xl font-semibold">
                    Pengaturuangku
                </h1>

                <!-- Mobile close button -->
                <button id="sidebar-close"
                    class="lg:hidden p-2 rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                    aria-label="Close sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 py-4" role="navigation" aria-label="Sidebar navigation">
                <ul class="space-y-1 px-3">
                    @php
                        $menuItems = [
                            [
                                'name' => 'Dashboard',
                                'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z',
                                'url' => '/dashboard',
                                'route_pattern' => 'dashboard*',
                            ],
                            [
                                'name' => 'Kategori',
                                'icon' => 'M4 4h6v6H4V4zm0 12h6v6H4v-6zm10-12h6v6h-6V4zm0 12h6v6h-6v-6z',
                                'url' => '/kategori?type=pengeluaran',
                                'route_pattern' => 'kategori*',
                            ],

                            [
                                'name' => 'Pemasukan',
                                'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6',
                                'url' => '/pemasukan',
                                'route_pattern' => 'pemasukan*',
                            ],
                            [
                                'name' => 'Pengeluaran',
                                'icon' => 'M20 12H4',
                                'url' => '/pengeluaran',
                                'route_pattern' => 'pengeluaran*',
                            ],
                            [
                                'name' => 'Anggaran',
                                'icon' =>
                                    'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z M9 9h6v6H9z',
                                'url' => '/anggaran',
                                'route_pattern' => 'anggaran*',
                            ],
                            [
                                'name' => 'Rekening',
                                'icon' =>
                                    'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                                'url' => '/rekening',
                                'route_pattern' => 'rekening*',
                            ],
                            [
                                'name' => 'Transfer',
                                'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                                'url' => '/transfer',
                                'route_pattern' => 'transfer*',
                            ],
                            [
                                'name' => 'Utang',
                                'icon' =>
                                    'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1',
                                'url' => '/utang',
                                'route_pattern' => 'utang*',
                            ],
                            [
                                'name' => 'Piutang',
                                'icon' =>
                                    'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                                'url' => '/piutang',
                                'route_pattern' => 'piutang*',
                            ],
                            [
                                'name' => 'Laporan',
                                'icon' =>
                                    'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                'url' => '/laporan',
                                'route_pattern' => 'laporan*',
                            ],
                        ];
                    @endphp

                    @foreach ($menuItems as $item)
                        @php
                            // Check if current route matches the pattern
                            $isActive =
                                request()->is(ltrim($item['url'], '/')) ||
                                request()->is(ltrim($item['url'], '/') . '/*') ||
                                (Route::currentRouteName() &&
                                    Str::is($item['route_pattern'], Route::currentRouteName()));
                        @endphp
                        <li>
                            <a href="{{ $item['url'] }}"
                                class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative
                                      {{ $isActive ? 'bg-yellow-50 text-yellow-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}"
                                aria-current="{{ $isActive ? 'page' : 'false' }}">

                                <!-- Active indicator (blue border on right) -->
                                @if ($isActive)
                                    <div class="absolute right-0 top-0 bottom-0 w-1 bg-yellow-500 rounded-l-lg"></div>
                                @endif

                                <!-- Menu Icon -->
                                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ $isActive ? 'text-yellow-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $item['icon'] }}"></path>
                                </svg>

                                <!-- Menu Text -->
                                <span>{{ $item['name'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Logout Menu Item -->
                <div class="px-3 mt-auto pb-4">
                    <form method="POST" action="/logout" class="w-full">
                        @csrf
                        <button type="submit"
                            class="group flex items-center w-full px-3 py-2.5 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50 hover:text-red-700 transition-all duration-200"
                            onclick="return confirm('Apakah Anda yakin ingin logout?')">

                            <!-- Logout Icon -->
                            <svg class="mr-3 h-5 w-5 flex-shrink-0 text-red-500 group-hover:text-red-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>

                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Top Navigation Header -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">

                    <!-- Left side: Mobile menu button -->
                    <div class="flex items-center">
                        <!-- Mobile hamburger button -->
                        <button id="mobile-menu-button"
                            class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            aria-label="Open sidebar" aria-expanded="false" aria-controls="sidebar">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        <!-- Logo (visible on mobile when sidebar is closed) -->
                        <div class="lg:hidden ml-2 flex-shrink-0 flex items-center">
                            <span class="text-xl font-semibold text-gray-800">
                                Pengaturuangku
                            </span>
                        </div>
                    </div>

                    <!-- Right side: User menu -->
                    <div class="flex items-center space-x-4">
                        <!-- User Profile Button -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center space-x-2 px-3 py-2 rounded-full bg-yellow-500 text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors duration-200"
                                id="user-menu-button" aria-expanded="false" aria-haspopup="true">

                                <!-- User Avatar -->
                                <div class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">
                                        {{ substr(Auth::guard('web')->user()->username ?? 'A', 0, 1) }}
                                    </span>
                                </div>

                                <!-- User Name -->
                                <span
                                    class="text-sm font-medium">{{ Auth::guard('web')->user()->username ?? 'a' }}</span>

                                <!-- Dropdown Arrow -->
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button"
                                tabindex="-1">

                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    role="menuitem">Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    role="menuitem">Settings</a>

                                <form method="POST" action="/logout">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        role="menuitem">
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1 p-4 bg-gray-50">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- JavaScript for mobile menu functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get DOM elements
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const sidebar = document.getElementById('sidebar');
            const sidebarClose = document.getElementById('sidebar-close');
            const overlay = document.getElementById('mobile-menu-overlay');

            /**
             * Toggle sidebar visibility on mobile
             */
            function toggleSidebar() {
                const isOpen = !sidebar.classList.contains('-translate-x-full');

                if (isOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }

            /**
             * Open sidebar on mobile
             */
            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'true');

                // Focus trap: focus on close button when sidebar opens
                if (sidebarClose) {
                    sidebarClose.focus();
                }

                // Prevent body scroll when sidebar is open
                document.body.style.overflow = 'hidden';
            }

            /**
             * Close sidebar on mobile
             */
            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'false');

                // Restore body scroll
                document.body.style.overflow = '';

                // Return focus to menu button
                mobileMenuButton.focus();
            }

            // Event listeners
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', toggleSidebar);
            }

            if (sidebarClose) {
                sidebarClose.addEventListener('click', closeSidebar);
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            // Close sidebar when pressing Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && sidebar && !sidebar.classList.contains('-translate-x-full')) {
                    closeSidebar();
                }
            });

            // Handle window resize - close mobile menu if window becomes large
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) { // lg breakpoint
                    closeSidebar();
                }
            });
        });
    </script>

    @stack('modals')
    @livewireScripts
</body>

</html>
