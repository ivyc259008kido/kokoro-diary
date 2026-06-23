<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>新規登録 | Kokoro Diary</title>
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

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- 名前 --}}
                <div class="mb-4">
                    <label class="block text-sm text-gray-500 mb-1">お名前</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                        required autofocus autocomplete="name"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-300 transition"
                        placeholder="your name">
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                {{-- メールアドレス --}}
                <div class="mb-4">
                    <label class="block text-sm text-gray-500 mb-1">メールアドレス</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        required autocomplete="username"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-300 transition"
                        placeholder="example@email.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                {{-- パスワード --}}
                <div class="mb-4">
                    <label class="block text-sm text-gray-500 mb-1">パスワード</label>
                    <input id="password" type="password" name="password"
                        required autocomplete="new-password"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-300 transition"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                {{-- パスワード確認 --}}
                <div class="mb-6">
                    <label class="block text-sm text-gray-500 mb-1">パスワード（確認）</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        required autocomplete="new-password"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-300 transition"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                {{-- 登録ボタン --}}
                <button type="submit"
                        class="w-full py-3 rounded-xl text-white font-bold text-sm transition hover:opacity-90"
                        style="background: linear-gradient(135deg, #a78bfa, #818cf8);">
                    アカウントを作成
                </button>

                {{-- ログインリンク --}}
                <p class="text-center text-xs text-gray-400 mt-4">
                    すでにアカウントをお持ちの方は
                    <a href="{{ route('login') }}" class="text-purple-400 hover:underline">ログイン</a>
                </p>

            </form>
        </div>
    </div>

</body>
</html>