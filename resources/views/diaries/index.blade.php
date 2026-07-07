<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kokoro Diary
        </h2>
    </x-slot>

    <div class="min-h-screen py-10" style="background: linear-gradient(135deg, #fdf4ff 0%, #eff6ff 100%);">
        <div class="max-w-2xl mx-auto px-4">

            {{-- 挨拶エリア --}}
            <div class="mb-8">
                <p class="text-gray-400 text-sm">{{ now()->format('Y年m月d日') }}</p>
                <h1 class="text-2xl font-bold text-gray-700 mt-1">
                    こんにちは、{{ Auth::user()->name }}さん 😊
                </h1>
                <p class="text-gray-400 text-sm mt-1">今日はどんな一日でしたか？</p>
            </div>

            {{-- 日記を書くボタン --}}
            <a href="{{ route('diaries.create') }}"
                class="block w-full text-center py-4 rounded-2xl font-bold text-white text-lg shadow-md mb-8 transition hover:opacity-90"
                style="background: linear-gradient(135deg, #a78bfa, #818cf8);">
                ✏️ 今日のことを書く
            </a>
            <a href="{{ route('monthly_reports.index') }}"
                class="block w-full text-center py-3 rounded-2xl font-bold text-sm shadow-sm mb-8 transition hover:bg-purple-50 border-2"
                style="border-color: #a78bfa; color: #a78bfa;">
                📅 月次レポートを見る
            </a>
            <a href="{{ route('calendar.index') }}"
                class="block w-full text-center py-3 rounded-2xl font-bold text-sm shadow-sm mb-8 transition hover:bg-purple-50 border-2"
                style="border-color: #818cf8; color: #818cf8;">
                🗓️ カレンダーを見る
            </a>
            {{-- 日記一覧 --}}
            @forelse ($diaries as $diary)
            <a href="{{ route('diaries.show', $diary) }}" class="block mb-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition border border-gray-100">
                    <p class="text-gray-400 text-xs mb-2">{{ $diary->created_at->format('Y年m月d日') }}</p>
                    <p class="text-gray-700 text-sm leading-relaxed">{{ Str::limit($diary->body, 80) }}</p>
                    @if ($diary->themes && count($diary->themes) > 0)
                    <div class="flex gap-2 mt-3 flex-wrap">
                        @foreach ($diary->themes as $theme)
                        <span class="text-xs px-2 py-1 rounded-full text-purple-500"
                            style="background: #f3e8ff;">
                            #{{ $theme }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </a>
            @empty
            <div class="bg-white rounded-2xl p-8 shadow-sm text-center border border-gray-100">
                <p class="text-4xl mb-3">📖</p>
                <p class="text-gray-500 text-sm">まだ日記がありません</p>
                <p class="text-gray-400 text-xs mt-1">最初の一言を書いてみましょう</p>
            </div>
            @endforelse

            {{-- ページ送りリンク --}}
            <div class="mt-6">
                {{ $diaries->links() }}
            </div>

        </div>
    </div>
</x-app-layout>