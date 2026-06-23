<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ログイン | Kokoro Diary</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased" style="background: linear-gradient(135deg, #fdf4ff 0%, #eff6ff 100%); min-height: 100vh;">

    <div class="min-h-screen flex flex-col items-center justify-center px-4">

        {{-- ロゴ・タイトル --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold mb-2"
                style="background: linear-gradient(135deg, #a78bfa, #818cf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Kokoro Diary
            </h1>
            <p class="text-gray-400 text-sm">書いて、気づいて、動き出す。</p>
        </div>

        {{-- カード --}}
        <div class="bg-white rounded-3xl shadow-sm p-8 w-full max-w-sm border border-gray-100">

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- メールアドレス --}}
                <div class="mb-4">
                    <label class="block text-sm text-gray-500 mb-1">メールアドレス</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        required autofocus autocomplete="username"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-300 transition"
                        placeholder="example@email.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                {{-- パスワード --}}
                <div class="mb-4">
                    <label class="block text-sm text-gray-500 mb-1">パスワード</label>
                    <input id="password" type="password" name="password"
                        required autocomplete="current-password"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-300 transition"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                {{-- Remember me --}}
                <div class="mb-6">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-400">
                        <input type="checkbox" name="remember"
                            class="rounded border-gray-300 text-purple-400 focus:ring-purple-300">
                        ログイン状態を保持する
                    </label>
                </div>

                {{-- ログインボタン --}}
                <button type="submit"
                        class="w-full py-3 rounded-xl text-white font-bold text-sm transition hover:opacity-90"
                        style="background: linear-gradient(135deg, #a78bfa, #818cf8);">
                    ログイン
                </button>

                {{-- 新規登録リンク --}}
                <p class="text-center text-xs text-gray-400 mt-4">
                    アカウントをお持ちでない方は
                    <a href="{{ route('register') }}" class="text-purple-400 hover:underline">新規登録</a>
                </p>

            </form>
        </div>
    </div>

</body>
</html>