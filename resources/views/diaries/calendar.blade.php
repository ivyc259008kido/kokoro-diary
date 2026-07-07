<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            カレンダー
        </h2>
    </x-slot>

    <div class="min-h-screen py-10" style="background: linear-gradient(135deg, #fdf4ff 0%, #eff6ff 100%);">
        <div class="max-w-2xl mx-auto px-4">

            {{-- 月切り替え --}}
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('calendar.index', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}"
                    class="px-4 py-2 rounded-xl bg-white shadow-sm border border-gray-100 text-purple-500 font-bold">
                    ← 前月
                </a>
                <h1 class="text-xl font-bold text-gray-700">
                    {{ $currentMonth->format('Y年n月') }}
                </h1>
                <a href="{{ route('calendar.index', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}"
                    class="px-4 py-2 rounded-xl bg-white shadow-sm border border-gray-100 text-purple-500 font-bold">
                    次月 →
                </a>
            </div>

            {{-- 曜日ヘッダー --}}
            <div class="grid grid-cols-7 gap-2 mb-2 text-center text-xs text-gray-400 font-bold">
                <div>日</div>
                <div>月</div>
                <div>火</div>
                <div>水</div>
                <div>木</div>
                <div>金</div>
                <div>土</div>
            </div>

            {{-- カレンダー本体 --}}
            <div class="grid grid-cols-7 gap-2">
                {{-- 月初め前の空白マス --}}
                @for ($i = 0; $i < $startOfMonth->dayOfWeek; $i++)
                    <div></div>
                    @endfor

                    {{-- 日付マス --}}
                    @for ($day = 1; $day <= $endOfMonth->day; $day++)
                        @php
                        $dateKey = $currentMonth->copy()->day($day)->format('Y-m-d');
                        $hasDiary = isset($diaryDates[$dateKey]);
                        $isToday = $dateKey === now()->format('Y-m-d');
                        @endphp

                        @if ($hasDiary)
                        <a href="{{ route('calendar.day', $dateKey) }}"
                            class="aspect-square flex flex-col items-center justify-center rounded-xl shadow-sm transition hover:opacity-80"
                            style="background: linear-gradient(135deg, #a78bfa, #818cf8);">
                            <span class="text-white font-bold text-sm">{{ $day }}</span>
                            <span class="text-white text-xs">●</span>
                        </a>
                        @else
                        <div class="aspect-square flex items-center justify-center rounded-xl {{ $isToday ? 'border-2' : '' }}"
                            style="{{ $isToday ? 'border-color: #a78bfa;' : '' }}">
                            <span class="text-gray-400 text-sm">{{ $day }}</span>
                        </div>
                        @endif
                        @endfor
            </div>

            <div class="mt-8">
                <a href="{{ route('diaries.index') }}" class="text-purple-500 text-sm font-bold">
                    ← 日記一覧に戻る
                </a>
            </div>

        </div>
    </div>
</x-app-layout>