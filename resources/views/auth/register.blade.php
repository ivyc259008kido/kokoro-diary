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
                    <div class="relative">
                        <input id="password" type="password" name="password"
                            required autocomplete="new-password"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 pr-11 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-300 transition"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg id="password-eye-open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="password-eye-closed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.774 3.162 10.066 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                {{-- パスワード確認 --}}
                <div class="mb-6">
                    <label class="block text-sm text-gray-500 mb-1">パスワード（確認）</label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            required autocomplete="new-password"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 pr-11 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-300 transition"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password_confirmation', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg id="password_confirmation-eye-open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="password_confirmation-eye-closed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.774 3.162 10.066 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
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
    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const eyeOpen = document.getElementById(inputId + '-eye-open');
            const eyeClosed = document.getElementById(inputId + '-eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
</body>

</html>