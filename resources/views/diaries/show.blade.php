<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            日記
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            {{-- 自分の日記 --}}
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-8 mb-6 shadow-sm"
                style="font-family: 'Georgia', serif;">
                <p class="text-amber-800 text-xs mb-4 tracking-widest uppercase">{{ $diary->created_at->format('Y年m月d日') }}</p>
                <p class="text-gray-800 leading-relaxed whitespace-pre-wrap text-base">{{ $diary->body }}</p>
            </div>

            {{-- AIからの返信 --}}
            @if ($diary->encouragement)
            <div class="relative bg-white border border-gray-200 rounded-lg p-8 shadow-sm"
                style="font-family: 'Georgia', serif;">
                <div class="absolute -top-3 left-6 bg-white px-2 text-xs text-gray-400 tracking-widest">AI より</div>
                <p class="text-gray-700 leading-loose whitespace-pre-wrap text-base">{{ $diary->encouragement }}</p>
                @if ($diary->themes && count($diary->themes) > 0)
                <div class="mt-6 pt-4 border-t border-gray-100 flex gap-2 flex-wrap">
                    @foreach ($diary->themes as $theme)
                    <span class="text-gray-400 text-xs">#{{ $theme }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            {{-- 操作ボタン --}}
            <div class="flex gap-3 mt-6">
                <a href="{{ route('diaries.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded text-gray-600 hover:bg-gray-100 text-sm">
                    ← 一覧に戻る
                </a>
                <a href="{{ route('diaries.edit', $diary) }}"
                    class="px-4 py-2 bg-amber-400 text-white rounded hover:bg-amber-500 text-sm">
                    編集
                </a>
                <form method="POST" action="{{ route('diaries.destroy', $diary) }}"
                    onsubmit="return confirm('本当に削除しますか？')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-400 text-white rounded hover:bg-red-500 text-sm">
                        削除
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>