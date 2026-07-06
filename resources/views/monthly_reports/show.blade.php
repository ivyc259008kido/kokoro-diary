<x-app-layout>
    <div class="min-h-screen py-10" style="background: linear-gradient(135deg, #fdf4ff 0%, #eff6ff 100%);">
        <div class="max-w-2xl mx-auto px-4">

            {{-- タイトル --}}
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-700">
                    {{ $monthlyReport->year }}年{{ $monthlyReport->month }}月のレポート
                </h1>
                <p class="text-gray-400 text-sm mt-1">AIがまとめた今月の振り返り</p>
            </div>

            {{-- レポート本文 --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-6"
                style="font-family: 'Georgia', serif;">
                <div class="absolute -top-3 left-6"></div>
                <p class="text-gray-700 leading-loose whitespace-pre-wrap text-base">
                    {{ $monthlyReport->report_text }}
                </p>
            </div>

            {{-- 戻るボタン・削除ボタン --}}
            <div class="flex gap-3">
                <a href="{{ route('monthly_reports.index') }}"
                    class="px-6 py-3 rounded-xl border border-gray-200 text-gray-500 text-sm hover:bg-gray-50 transition">
                    ← レポート一覧に戻る
                </a>
                <form method="POST" action="{{ route('monthly_reports.destroy', $monthlyReport) }}"
                    onsubmit="return confirm('このレポートを削除しますか？')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-red-400 text-white text-sm hover:bg-red-500 transition">
                        削除する
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>