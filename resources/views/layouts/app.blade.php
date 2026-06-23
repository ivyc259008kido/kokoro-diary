<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kokoro Diary</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: linear-gradient(135deg, #fdf4ff 0%, #eff6ff 100%);
            min-height: 100vh;
        }

        .kokoro-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(167, 139, 250, 0.15);
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen">
        <nav class="kokoro-nav sticky top-0 z-50">
            <div class="max-w-2xl mx-auto px-4 py-4 flex items-center justify-between">
                <a href="{{ route('diaries.index') }}"
                    class="text-lg font-bold"
                    style="background: linear-gradient(135deg, #a78bfa, #818cf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Kokoro Diary
                </a>
                <div class="flex items-center gap-4">
                    <span class="text-gray-400 text-sm">{{ Auth::user()->name ?? '' }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-xs text-gray-400 hover:text-gray-600 transition">
                            ログアウト
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>

</html>