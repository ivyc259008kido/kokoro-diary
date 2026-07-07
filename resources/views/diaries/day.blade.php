<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $day->format('Y年n月j日') }}の日記
        </h2>
    </x-slot>

    <div class="min-h-screen py-10" style="background: linear-gradient(135deg, #fdf4ff 0%, #eff6ff 100%);">
        <div class="max-w-2xl mx-auto px-4">

            <p class="text-gray-400 text-sm mb-6">{{ $day->format('Y年m月d日') }}</p>

            @forelse ($diaries as $diary)
            <a href="{{ route('diaries.show', $diary) }}" class="block mb-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition border border-gray-100">
                    <p class="text-gray-400 text-xs mb-2">{{ $diary->created_at->format('H:i') }}</p>
                    <p class="text-gray-700 text-sm leading-relaxed">{{ Str::limit($diary->body, 80) }}</p>
                    @if ($diary->themes && count($diary->themes) > 0)
                    <div class="flex gap-2 mt-3 flex-wrap">
                        @foreach ($diary->themes as $theme)
                        <span class="text-xs px-2 py-1 rounded-full text-purple-500" style="background: #f3e8ff;">
                            #{{ $theme }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </a>
            @empty
            <div class="bg-white rounded-2xl p-8 shadow-sm text-center border border-gray-100">
                <p class="text-gray-500 text-sm">この日の日記はありません</p>
            </div>
            @endforelse

            <div class="mt-8">
                <a href="{{ route('calendar.index') }}" class="text-purple-500 text-sm font-bold">
                    ← カレンダーに戻る
                </a>
            </div>

        </div>
    </div>
</x-app-layout>