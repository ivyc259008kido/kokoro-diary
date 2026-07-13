<x-app-layout>
    <div class="min-h-screen py-10" style="background: linear-gradient(135deg, #fdf4ff 0%, #eff6ff 100%);">
        <div class="max-w-2xl mx-auto px-4">

            {{-- タイトル --}}
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-700">📅 月次レポート</h1>
                <p class="text-gray-400 text-sm mt-1">1ヶ月の日記をAIがまとめてくれます</p>
            </div>

            {{-- 感情トレンドグラフ --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
                <p class="font-bold text-gray-700 mb-4">📈 感情トレンド</p>
                <canvas id="moodChart" height="120"></canvas>
            </div>

            {{-- グラフの見方 --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 mb-6">
                <p class="font-bold text-gray-600 text-sm mb-3">📖 グラフの見方</p>
                <div class="space-y-2">
                    <p class="text-xs text-gray-500 leading-relaxed">
                        <span class="inline-block w-3 h-3 rounded-full mr-1" style="background:#818cf8;"></span>
                        <strong>感情スコア</strong>：日記投稿時にAIが分析した気分の数値です。高いほどポジティブな内容と判定されています。
                    </p>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        <span class="inline-block w-3 h-1 mr-1" style="background:#f59e0b;"></span>
                        <strong>平均（30日）</strong>：直近30日間の平均スコアです。
                    </p>
                    <p class="text-xs text-gray-500 leading-relaxed mt-2">
                        💡 スコアが低い日があっても大丈夫です。このグラフは気分を上げるためではなく、自分の感情のパターンに気づくためのものです。
                    </p>
                </div>
            </div>

            {{-- 振り返りのポイント --}}
            <div class="rounded-2xl p-6 shadow-sm border mb-6" style="background: #eef2ff; border-color: #c7d2fe;">
                <p class="font-bold text-gray-700 mb-4">📊 振り返りのポイント</p>
                <p class="text-xs text-gray-500 mb-2">感情スコア（ポジティブ度）</p>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-white text-sm font-bold mb-4"
                    style="background: linear-gradient(135deg, #a78bfa, #818cf8);">
                    {{ $positivityEmoji }} {{ $positivityLabel }}（{{ $positivity }}%）
                </div>
                @if ($themeCounts->isNotEmpty())
                    <p class="text-xs text-gray-500 mb-2">主要テーマ</p>
                    <div class="flex gap-2 flex-wrap">
                        @foreach ($themeCounts as $theme => $count)
                            <span class="px-3 py-1 rounded-full text-sm text-indigo-600" style="background: #e0e7ff;">
                                #{{ $theme }}
                            </span>
                        @endforeach
                    </div>
                @endif
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

    {{-- Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const moodData = @json($moodData);
        const avgMood = {{ $avgMood }};

        const labels = moodData.map(d => d.date);
        const moods = moodData.map(d => d.mood);
        const avgLine = moodData.map(() => avgMood);

        const ctx = document.getElementById('moodChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '感情スコア',
                        data: moods,
                        borderColor: '#818cf8',
                        backgroundColor: 'rgba(129, 140, 248, 0.1)',
                        borderWidth: 2,
                        pointBackgroundColor: '#818cf8',
                        tension: 0.4,
                        fill: true,
                    },
                    {
                        label: '平均（30日）',
                        data: avgLine,
                        borderColor: '#f59e0b',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        min: 0,
                        max: 100,
                        ticks: {
                            callback: val => val + '%'
                        }
                    }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    </script>
</x-app-layout>