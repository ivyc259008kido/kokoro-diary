<x-app-layout>
    <div class="min-h-screen py-10" style="background: linear-gradient(135deg, #fdf4ff 0%, #eff6ff 100%);">
        <div class="max-w-2xl mx-auto px-4">

            {{-- タイトル --}}
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-700">📅 月次レポート</h1>
                <p class="text-gray-400 text-sm mt-1">1ヶ月の日記をAIがまとめてくれます</p>
            </div>

            {{-- メッセージ --}}
            @if (session('success'))
                <div class="bg-purple-50 border border-purple-200 rounded-2xl p-4 mb-6 text-purple-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6 text-red-500 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- レポート生成フォーム --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8">
                <p class="text-gray-600 text-sm font-bold mb-4">レポートを生成する</p>
                <form method="POST" action="{{ route('monthly_reports.generate') }}">
                    @csrf
                    <div class="flex gap-3 items-center mb-4">
                        <select name="year" class="rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-300">
                            @for ($y = now()->year; $y >= now()->year - 2; $y--)
                                <option value="{{ $y }}">{{ $y }}年</option>
                            @endfor
                        </select>
                        <select name="month" class="rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-300">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == now()->subMonth()->month ? 'selected' : '' }}>
                                    {{ $m }}月
                                </option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit"
                            class="w-full py-3 rounded-xl text-white font-bold text-sm transition hover:opacity-90"
                            style="background: linear-gradient(135deg, #a78bfa, #818cf8);">
                        ✨ AIにまとめてもらう
                    </button>
                </form>
            </div>

            {{-- レポート一覧 --}}
            @forelse ($reports as $report)
                <a href="{{ route('monthly_reports.show', $report) }}" class="block mb-4">
                    <div class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition border border-gray-100">
                        <p class="text-purple-500 font-bold text-sm mb-2">
                            {{ $report->year }}年{{ $report->month }}月のレポート
                        </p>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            {{ Str::limit($report->report_text, 100) }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-2xl p-8 shadow-sm text-center border border-gray-100">
                    <p class="text-4xl mb-3">📊</p>
                    <p class="text-gray-500 text-sm">まだレポートがありません</p>
                    <p class="text-gray-400 text-xs mt-1">上のボタンから生成してみましょう</p>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>