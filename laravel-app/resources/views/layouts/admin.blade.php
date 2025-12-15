<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-gray-900 text-white flex flex-col">
        <div class="p-4 text-xl font-bold border-b border-gray-700">
            Admin Panel
        </div>

        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}"
               class="block px-4 py-2 rounded hover:bg-gray-700">
                Dashboard
            </a>

            <a href="{{ route('admin.ads.index') }}"
               class="block px-4 py-2 rounded hover:bg-gray-700">
                Ads
            </a>

            <a href="{{ route('admin.logs') }}"
               class="block px-4 py-2 rounded hover:bg-gray-700">
                Moderation Logs
            </a>
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="p-4">
            @csrf
            <button class="w-full bg-red-600 hover:bg-red-700 px-4 py-2 rounded">
                Logout
            </button>
        </form>
    </aside>

    {{-- CONTENT --}}
    <main class="flex-1 p-6">
        <h1 class="text-2xl font-semibold mb-6">
            @yield('page-title')
        </h1>

        @yield('content')
    </main>

</div>

</body>
</html>
