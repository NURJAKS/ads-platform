<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'AdsPlatform')) - Площадка объявлений</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo & Links -->
                <div class="flex items-center space-x-8">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2 group">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center transform group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                    </div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">{{ config('app.name', 'AdsMarket') }}</span>
                    </a>
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="{{ route('ads.index') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                            Все объявления
                        </a>
                        @auth
                            <a href="{{ route('my.ads') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                                Мои объявления
                            </a>
                            <a href="{{ route('my.favorites') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                                Избранное
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Right Side (Auth) -->
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('ads.create') }}" class="hidden sm:flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:from-blue-700 hover:to-purple-700 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Разместить</span>
                        </a>
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="bg-purple-100 text-purple-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-200 transition-colors">
                                Админ
                            </a>
                        @endif
                        <div class="relative group">
                            <button class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden md:block text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        Выйти
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                            Войти
                        </a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                            Регистрация
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="flex-grow">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                <div class="bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded shadow-sm mb-4" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                <div class="bg-red-50 border-l-4 border-red-400 text-red-700 px-4 py-3 rounded shadow-sm mb-4" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white">{{ config('app.name', 'AdsMarket') }}</span>
                    </div>
                    <p class="text-gray-400 text-sm mb-4">
                        Современная площадка для размещения объявлений. Продавайте и покупайте с удобством.
                    </p>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-4">Покупателям</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('ads.index') }}" class="hover:text-white transition-colors">Все объявления</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Как покупать</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Безопасность</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-4">Продавцам</h3>
                    <ul class="space-y-2 text-sm">
                        @auth
                            <li><a href="{{ route('ads.create') }}" class="hover:text-white transition-colors">Разместить объявление</a></li>
                        @else
                            <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Зарегистрироваться</a></li>
                        @endauth
                        <li><a href="#" class="hover:text-white transition-colors">Правила размещения</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Помощь</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'AdsMarket') }}. Все права защищены.</p>
            </div>
        </div>
    </footer>
</body>
</html>
