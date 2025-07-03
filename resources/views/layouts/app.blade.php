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
    <script src="https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js" defer></script>

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
                @php
                    if (Auth::guard('admin')->check()) {
                        $menuItems = [
                            [
                                'name' => 'Dashboard',
                                'icon' => 'M3 9.75L12 3l9 6.75v10.5a1.5 1.5 0 01-1.5 1.5H4.5A1.5 1.5 0 013 20.25V9.75z',
                                'route' => 'admin.dashboard',
                                'pattern' => 'admin.dashboard*',
                            ],
                            [
                                'name' => 'Kelola Pengguna',
                                'icon' =>
                                    'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
                                'route' => 'admin.pengguna.index',
                                'pattern' => 'admin.pengguna*',
                            ],
                        ];
                    } else {
                        $menuItems = [
                            [
                                'name' => 'Dashboard',
                                'icon' =>
                                    'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V7zm0 0V5a2 2 0 012-2h6l2 2h6a2 2 0 012 2v2',
                                'route' => 'dashboard',
                                'pattern' => 'dashboard*',
                            ],
                            [
                                'name' => 'Kategori',
                                'icon' =>
                                    'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z',
                                'route' => 'kategori.index',
                                'pattern' => 'kategori*',
                            ],
                            [
                                'name' => 'Pemasukan',
                                'icon' =>
                                    'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                'route' => 'pemasukan.index',
                                'pattern' => 'pemasukan*',
                            ],
                            [
                                'name' => 'Pengeluaran',
                                'icon' => 'M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z',
                                'route' => 'pengeluaran.index',
                                'pattern' => 'pengeluaran*',
                            ],
                            [
                                'name' => 'Anggaran',
                                'icon' =>
                                    'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z',
                                'route' => 'anggaran.index',
                                'pattern' => 'anggaran*',
                            ],
                            [
                                'name' => 'Rekening',
                                'icon' =>
                                    'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z',
                                'route' => 'rekening.index',
                                'pattern' => 'rekening*',
                            ],
                            [
                                'name' => 'Transfer',
                                'icon' => 'M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5',
                                'route' => 'transfer.index',
                                'pattern' => 'transfer*',
                            ],
                            [
                                'name' => 'Utang',
                                'icon' =>
                                    'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
                                'route' => 'utang.index',
                                'pattern' => 'utang*',
                            ],
                            [
                                'name' => 'Piutang',
                                'icon' =>
                                    'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3l-3 3m-3-3l3 3m1.5-6H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
                                'route' => 'piutang.index',
                                'pattern' => 'piutang*',
                            ],
                            [
                                'name' => 'Laporan',
                                'icon' =>
                                    'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
                                'route' => 'laporan.index',
                                'pattern' => 'laporan*',
                            ],
                        ];
                    }
                @endphp

                <ul class="space-y-1 px-3">
                    @foreach ($menuItems as $item)
                        @php
                            $isActive = request()->routeIs($item['pattern']);
                            $url = route($item['route']);
                        @endphp
                        <li>
                            <a href="{{ $url }}"
                                class="relative flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                  {{ $isActive ? 'bg-yellow-50 text-yellow-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}"
                                aria-current="{{ $isActive ? 'page' : 'false' }}">
                                @if ($isActive)
                                    <span class="absolute right-0 top-0 bottom-0 w-1 bg-yellow-500 rounded-l-lg"></span>
                                @endif

                                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ $isActive ? 'text-yellow-600' : 'text-gray-400 group-hover:text-gray-500' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $item['icon'] }}" />
                                </svg>
                                <span>{{ $item['name'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Logout -->
                <div class="px-3 mt-auto pb-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="group flex items-center w-full px-3 py-2.5 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50 hover:text-red-700 transition-all duration-200"
                            onclick="return confirm('Apakah Anda yakin ingin logout?')">
                            <svg class="mr-3 h-5 w-5 flex-shrink-0 text-red-500 group-hover:text-red-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3…" />
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

                                @php
                                    $user = null;

                                    if (Auth::guard('web')->check()) {
                                        $user = Auth::guard('web')->user();
                                    } elseif (Auth::guard('admin')->check()) {
                                        $user = Auth::guard('admin')->user();
                                    }

                                    $initial = strtoupper(substr($user->username ?? 'A', 0, 1));
                                    $username = $user->username ?? 'User';
                                @endphp

                                <!-- User Avatar -->
                                <div class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">
                                        {{ $initial }}
                                    </span>
                                </div>

                                <!-- User Name -->
                                <span class="text-sm font-medium">{{ $username }}</span>


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

                                @if (!Auth::guard('admin')->check())
                                    <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        role="menuitem">Profile</a>
                                @endif



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
