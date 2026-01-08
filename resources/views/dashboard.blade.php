<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard ASN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Immediately apply dark mode to prevent FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        /* Pembungkus Utama - moved to Tailwind classes */
        /* Custom Scrollbar for Webkit */
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: #e5e7eb; /* gray-200 */
            border-radius: 20px;
        }
        .dark .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: #4b5563; /* gray-600 */
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
    <div id="wrapper" class="flex w-full items-stretch gap-0 md:gap-0 lg:gap-0">
        <!-- Adding explicit padding-left to main content instead of gap for cleaner full-height look, 
             OR simply using gap if we want physical separation. 
             User asked for "proper distance", so let's try a gap which creates a gutter.
             Actually, let's stick to standard dashboard layout: Sidebar touches content, but content has padding.
             Let's INCREASE the inner padding of the container. 
        -->
        <!-- Sidebar -->
        <aside id="main-sidebar"
            class="w-64 bg-white dark:bg-gray-800 shadow-md flex-col h-screen sticky top-0 z-20 flex-shrink-0 border-r dark:border-gray-700 hidden md:flex transition-all duration-300">
            <div
                class="p-6 border-b border-gray-100 dark:border-gray-700 flex-shrink-0 flex items-center justify-between">
                <h2 id="sidebar-title"
                    class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2 whitespace-nowrap overflow-hidden transition-all duration-300">
                    <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    <span class="sidebar-text">DASHBOARD</span>
                </h2>
                <!-- Sidebar Toggle Button -->
                <button id="sidebar-toggle"
                    class="p-1 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none ml-auto">
                    <svg id="toggle-icon" class="w-6 h-6 transform transition-transform duration-300" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Content Wrapper (Scrollable) -->
            <div class="flex-1 overflow-y-auto py-4 px-3 space-y-4">
                
                <!-- Menu "Statistik" -->
                <nav class="space-y-1">
                    <!-- Menu Item -->
                    <div>
                        <button type="button" id="menu-statistik-toggle" 
                            class="w-full flex items-center justify-between px-2 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors group">
                            <div class="flex items-center">
                                <svg class="mr-3 h-6 w-6 text-gray-500 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-gray-300 flex-shrink-0 transition-colors" 
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span class="sidebar-text truncate">Statistik</span>
                            </div>
                            <!-- Chevron Icon -->
                            <svg id="menu-statistik-icon" class="sidebar-text h-4 w-4 text-gray-400 transform transition-transform duration-200" 
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Submenu (Filter Unit Kerja) -->
                        <div id="menu-statistik-content" class="hidden mt-2 space-y-2 pl-2 md:pl-0">
                             <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                                <label
                                    class="sidebar-text block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 px-1">
                                    Filter Unit Kerja
                                </label>
                
                                <!-- Custom Dropdown Component -->
                                <div class="relative" id="opd-dropdown">
                                    <!-- Dropdown Trigger button -->
                                    <button type="button" id="dropdown-trigger"
                                        class="w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-sm px-3 py-2 text-left cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all hover:bg-gray-50 dark:hover:bg-gray-600 flex items-center justify-between group">
                                        <span
                                            class="block truncate text-xs font-medium {{ $filterOpd ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-200' }}">
                                            {{ $filterOpd ?? 'Pilih Unit Kerja' }}
                                        </span>
                                        <svg class="h-3 w-3 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                            </path>
                                        </svg>
                                    </button>
                
                                    <!-- Dropdown Menu (Inline) -->
                                    <div id="dropdown-menu"
                                        class="hidden w-full bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-lg shadow-inner mt-1 max-h-60 flex flex-col overflow-hidden transition-all origin-top opacity-0">
                                        
                                        <!-- Sticky Search Box -->
                                        <div class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 sticky top-0 z-10">
                                            <div class="relative">
                                                <input type="text" id="opd-search" placeholder="Cari OPD..." autocomplete="off"
                                                    class="w-full pl-8 pr-3 py-1.5 border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                                    <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                
                                        <!-- Scrollable List -->
                                        <div class="overflow-y-auto flex-1 p-1 scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-600" id="opd-list">
                                            <a href="/"
                                                class="flex items-center px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/50 hover:text-blue-700 dark:hover:text-blue-300 rounded-md transition-colors">
                                                <span class="font-semibold text-blue-500 dark:text-blue-400 w-5 text-center mr-2">•</span>
                                                Semua Unit Kerja
                                            </a>
                
                                            @foreach($listOpd as $opd)
                                                <a href="?opd={{ urlencode($opd) }}"
                                                    class="opd-item flex items-center px-4 py-2 text-xs text-gray-600 dark:text-gray-400 hover:bg-slate-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors group"
                                                    data-name="{{ strtolower($opd) }}">
                                                    <span class="w-5 mr-2 flex-shrink-0 text-center {{ $filterOpd == $opd ? 'text-blue-500 dark:text-blue-400' : 'text-transparent group-hover:text-gray-300 dark:group-hover:text-gray-500' }}">
                                                        <svg class="w-3.5 h-3.5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </span>
                                                    <span class="truncate">{{ $opd }}</span>
                                                </a>
                                            @endforeach
                
                                            <div id="no-results" class="hidden px-4 py-4 text-center text-xs text-gray-400 dark:text-gray-500 italic">
                                                Tidak ada OPD ditemukan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                             </div>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Sync Button (Bottom Sidebar) -->
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                <form action="{{ route('sync.pegawai') }}" method="POST">
                    @csrf
                    <button type="submit"
                        onclick="return confirm('Mulai sinkronisasi data dari server? Proses ini mungkin memakan waktu.')"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-md transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 group"
                        title="Sync Data">
                        <svg class="w-5 h-5 flex-shrink-0 group-hover:animate-spin" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        <span class="sidebar-text">Sync Data</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <!-- Main Content -->
        <main id="main-content"
            class="flex-1 bg-gray-50 dark:bg-gray-900 min-h-screen transition-all duration-300 w-full">
            <div class="container mx-auto px-10 py-8">
                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <div class="flex items-center gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Dashboard Statistik ASN</h1>
                            <div class="flex items-center gap-2 mt-1">
                                <p class="text-gray-500 dark:text-gray-400 flex items-center">
                                    @if($filterOpd)
                                        <span
                                            class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold px-2.5 py-0.5 rounded mr-2">FILTERED</span>
                                        {{ $filterOpd }}
                                    @else
                                        Pemerintah Kabupaten Blitar
                                    @endif
                                </p>
                                <span class="text-gray-300 dark:text-gray-600">|</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500" title="Last Synced">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    Updated: {{ $lastSync }}
                                </span>
                            </div>
                        </div>
                    </div>


                    <!-- Dark Mode Toggle -->
                    <button id="theme-toggle"
                        class="p-2 rounded-lg bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <!-- Sun Icon -->
                        <svg id="theme-toggle-light-icon" class="hidden w-6 h-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <!-- Moon Icon -->
                        <svg id="theme-toggle-dark-icon" class="hidden w-6 h-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                            </path>
                        </svg>
                    </button>

                    @if($filterOpd)
                        <a href="/"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            <!-- Top Cards -->
            @if(session('success'))
                <div
                    class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900/50 dark:text-green-200">
                    <p class="font-bold">Sukses!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div
                    class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 dark:bg-red-900/50 dark:text-red-200">
                    <p class="font-bold">Gagal!</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Pegawai -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Pegawai</p>
                            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                                {{ number_format($totalPegawai) }}
                            </h2>
                        </div>
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-full">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Laki-laki -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-teal-500 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Laki-laki</p>
                            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                                {{ number_format($totalLaki) }}
                            </h2>
                        </div>
                        <div class="p-3 bg-teal-50 dark:bg-teal-900/30 rounded-full">
                            <svg class="w-6 h-6 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Perempuan -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-pink-500 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Perempuan</p>
                            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                                {{ number_format($totalPerempuan) }}
                            </h2>
                        </div>
                        <div class="p-3 bg-pink-50 dark:bg-pink-900/30 rounded-full">
                            <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <!-- Status Pegawai Summary Cards (Now part of the main grid) -->
                <!-- PNS -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">PNS</p>
                            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                                {{ number_format($totalPns) }}
                            </h2>
                        </div>
                        <div class="p-3 bg-green-50 dark:bg-green-900/30 rounded-full">
                            <span class="text-green-600 font-bold text-lg">PNS</span>
                        </div>
                    </div>
                </div>

                <!-- CPNS -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">CPNS</p>
                            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                                {{ number_format($totalCpns) }}
                            </h2>
                        </div>
                        <div class="p-3 bg-purple-50 dark:bg-purple-900/30 rounded-full">
                            <span class="text-purple-600 font-bold text-lg">CPNS</span>
                        </div>
                    </div>
                </div>

                <!-- PPPK -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-orange-500 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">PPPK</p>
                            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                                {{ number_format($totalPppk) }}
                            </h2>
                        </div>
                        <div class="p-3 bg-orange-50 dark:bg-orange-900/30 rounded-full">
                             <span class="text-orange-600 font-bold text-lg">PPPK</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Chart 1: Jenis Kelamin -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Pegawai per Jenis Kelamin
                    </h3>
                    <div id="chart-jenikel"></div>
                </div>

                <!-- Chart New: Jenis Pegawai -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Jenis Pegawai</h3>
                    <div id="chart-sts-peg"></div>
                </div>

                <!-- Chart 3: Pendidikan -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Pegawai per Pendidikan</h3>
                    <div id="chart-pendidikan"></div>
                </div>

                <!-- Chart 4: Eselon -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Pegawai per Eselon</h3>
                    <div id="chart-eselon"></div>
                </div>

                <!-- Chart 6: Unit Kerja (Show only if not filtered or show top always) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 lg:col-span-2">
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">
                        @if($filterOpd)
                            Statistik Unit Kerja Ini
                        @else
                            Top 10 Unit Kerja / OPD
                        @endif
                    </h3>
                    <div id="chart-opd"></div>
                </div>

            </div>
    </div>

    <!-- Mobile Sidebar Toggle Overlay (Simple Implementation if needed later, ignoring for now as requested desktop focus but keeping structure safe) -->
    </main>
    </div>

    <script>
        // Sidebar Dropdown & Search Functionality
        const dropdownTrigger = document.getElementById('dropdown-trigger');
        const dropdownMenu = document.getElementById('dropdown-menu');
        const searchInput = document.getElementById('opd-search');
        const opdList = document.getElementById('opd-list');
        const opdItems = opdList ? opdList.getElementsByClassName('opd-item') : [];
        const noResults = document.getElementById('no-results');

        // Toggle Dropdown
        if (dropdownTrigger && dropdownMenu) {
            dropdownTrigger.addEventListener('click', (e) => {
                e.stopPropagation();
                // Toggle visibility
                const isHidden = dropdownMenu.classList.contains('hidden');

                if (isHidden) {
                    dropdownMenu.classList.remove('hidden');
                    // Small delay to allow display:block to apply before opacity transition
                    requestAnimationFrame(() => {
                        dropdownMenu.classList.remove('scale-95', 'opacity-0');
                        dropdownMenu.classList.add('scale-100', 'opacity-100');
                    });
                    // Focus search
                    if (searchInput) setTimeout(() => searchInput.focus(), 100);
                } else {
                    closeDropdown();
                }
            });

            // Close on click outside
            document.addEventListener('click', (e) => {
                if (!dropdownMenu.contains(e.target) && !dropdownTrigger.contains(e.target)) {
                    closeDropdown();
                }
            });
        }

        function closeDropdown() {
            if (!dropdownMenu) return;
            dropdownMenu.classList.remove('scale-100', 'opacity-100');
            dropdownMenu.classList.add('scale-95', 'opacity-0');

            // Wait for transition to finish before hiding
            setTimeout(() => {
                dropdownMenu.classList.add('hidden');
            }, 200); // match transition duration roughly
        }

        // Search Filter
        if (searchInput) {
            // Prevent dropdown closing when clicking/typing in search
            searchInput.addEventListener('click', (e) => e.stopPropagation());

            searchInput.addEventListener('keyup', function (e) {
                const term = e.target.value.toLowerCase();
                let hasResults = false;

                Array.from(opdItems).forEach(item => {
                    const name = item.getAttribute('data-name');
                    if (name.includes(term)) {
                        item.classList.remove('hidden');
                        hasResults = true;
                    } else {
                        item.classList.add('hidden');
                    }
                });

                if (noResults) {
                    noResults.classList.toggle('hidden', hasResults);
                }
            });
        }

        // Statistik Menu Toggle
        const menuStatistikToggle = document.getElementById('menu-statistik-toggle');
        const menuStatistikContent = document.getElementById('menu-statistik-content');
        const menuStatistikIcon = document.getElementById('menu-statistik-icon');

        if (menuStatistikToggle && menuStatistikContent) {
            menuStatistikToggle.addEventListener('click', () => {
                const isHidden = menuStatistikContent.classList.contains('hidden');
                
                if (isHidden) {
                    menuStatistikContent.classList.remove('hidden');
                    menuStatistikIcon.classList.add('rotate-180');
                } else {
                    menuStatistikContent.classList.add('hidden');
                    menuStatistikIcon.classList.remove('rotate-180');
                }
            });
        }

        // --- UI Enhancements: Dark Mode & Sidebar Toggle ---

        // Dark Mode Logic
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Set initial icon based on current mode (which was set in head)
        if (document.documentElement.classList.contains('dark')) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        themeToggleBtn.addEventListener('click', function () {
            // toggle icons
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
            updateChartTheme(); // Function to update charts if needed
        });

        // Sidebar Toggle Logic (Mini Sidebar)
        const sidebarToggleBtn = document.getElementById('sidebar-toggle');
        const mainSidebar = document.getElementById('main-sidebar');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        const toggleIcon = document.getElementById('toggle-icon');

        if (sidebarToggleBtn && mainSidebar) {
            sidebarToggleBtn.addEventListener('click', () => {
                const isCollapsed = mainSidebar.classList.contains('w-20');

                if (isCollapsed) {
                    // EXPAND
                    mainSidebar.classList.remove('w-20');
                    mainSidebar.classList.add('w-64');

                    // Show Texts
                    sidebarTexts.forEach(el => el.classList.remove('hidden'));

                    // Rotate Icon Back
                    toggleIcon.classList.remove('rotate-180');

                } else {
                    // COLLAPSE
                    mainSidebar.classList.remove('w-64');
                    mainSidebar.classList.add('w-20');

                    // Hide Texts
                    sidebarTexts.forEach(el => el.classList.add('hidden'));

                    // Ensure Statistic Menu Content is hidden when collapsing
                    if (menuStatistikContent && !menuStatistikContent.classList.contains('hidden')) {
                        menuStatistikContent.classList.add('hidden');
                        menuStatistikIcon.classList.remove('rotate-180');
                    }

                    // Rotate Icon
                    toggleIcon.classList.add('rotate-180');
                }
            });
        }




        // Helper to get color palette
        const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1'];

        // Initialize Common Options
        const getChartColors = () => {
            const isDark = document.documentElement.classList.contains('dark');
            return {
                chart: {
                    foreColor: isDark ? '#f3f4f6' : '#374151',
                    background: 'transparent'
                },
                theme: {
                    mode: isDark ? 'dark' : 'light'
                }
            };
        };

        const chartInstances = {};

        // 1. Chart Jenikel (Pie)
        var optionsJenikel = {
            series: @json($chartJenikel['series']),
            labels: @json($chartJenikel['labels']),
            chart: { type: 'pie', height: 350, ...getChartColors().chart },
            theme: getChartColors().theme,
            colors: ['#0EA5E9', '#EC4899'],
            legend: { position: 'bottom' }
        };
        chartInstances.jenikel = new ApexCharts(document.querySelector("#chart-jenikel"), optionsJenikel);
        chartInstances.jenikel.render();

        // Chart New: Jenis Pegawai (Pie)
        var optionsStsPeg = {
            series: @json($chartStsPeg['series']),
            labels: @json($chartStsPeg['labels']),
            chart: { type: 'pie', height: 350, ...getChartColors().chart },
            theme: getChartColors().theme,
            colors: ['#F59E0B', '#10B981', '#6366F1'],
            legend: { position: 'bottom' }
        };
        chartInstances.stsPeg = new ApexCharts(document.querySelector("#chart-sts-peg"), optionsStsPeg);
        chartInstances.stsPeg.render();

        // 2. Chart Golongan (Bar)

        // 3. Chart Pendidikan (Bar)
        var optionsPendidikan = {
            series: [{ name: 'Jumlah', data: @json($chartPendidikan['series']) }],
            chart: { type: 'bar', height: 350, ...getChartColors().chart },
            theme: getChartColors().theme,
            xaxis: { categories: @json($chartPendidikan['categories']) },
            plotOptions: { bar: { borderRadius: 4, horizontal: true } },
            colors: ['#10B981']
        };
        chartInstances.pendidikan = new ApexCharts(document.querySelector("#chart-pendidikan"), optionsPendidikan);
        chartInstances.pendidikan.render();

        // 4. Chart Eselon (Bar)
        var optionsEselon = {
            series: [{ name: 'Jumlah', data: @json($chartEselon['series']) }],
            chart: { type: 'bar', height: 350, ...getChartColors().chart },
            theme: getChartColors().theme,
            xaxis: { categories: @json($chartEselon['categories']) },
            plotOptions: { bar: { borderRadius: 4, horizontal: false } },
            colors: ['#F59E0B']
        };
        chartInstances.eselon = new ApexCharts(document.querySelector("#chart-eselon"), optionsEselon);
        chartInstances.eselon.render();

        // 6. Chart OPD (Bar Horizontal)
        var optionsOpd = {
            series: [{ name: 'Jumlah', data: @json($chartOpd['series']) }],
            chart: { type: 'bar', height: 400, ...getChartColors().chart },
            theme: getChartColors().theme,
            xaxis: { categories: @json($chartOpd['categories']) },
            plotOptions: { bar: { borderRadius: 4, horizontal: true } },
            colors: ['#6366F1']
        };
        chartInstances.opd = new ApexCharts(document.querySelector("#chart-opd"), optionsOpd);
        chartInstances.opd.render();

        function updateChartTheme() {
            const newColors = getChartColors();
            Object.values(chartInstances).forEach(chart => {
                chart.updateOptions({
                    chart: newColors.chart,
                    theme: newColors.theme
                });
            });
        }
    </script>
</body>

</html>