<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 直近30日の日記を取得
        $diaries = $user->diaries()
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('created_at', 'asc')
            ->get();

        // 感情スコアのグラフデータ
        $moodData = $diaries->map(function ($diary) {
            return [
                'date' => $diary->created_at->format('n/j'),
                'mood' => $diary->mood ? $diary->mood * 20 : null,
            ];
        })->filter(fn($d) => $d['mood'] !== null)->values();

        // 7日間の平均
        $avgMood = $moodData->avg('mood') ?? 0;

        // 主要テーマの集計
        $allThemes = $diaries->flatMap(fn($d) => $d->themes ?? [])->filter();
        $themeCounts = $allThemes->countBy()->sortDesc()->take(5);

        // ポジティブ度の判定
        $positivity = round($avgMood);
        if ($positivity >= 60) {
            $positivityLabel = 'ポジティブ';
            $positivityEmoji = '😊';
        } elseif ($positivity >= 40) {
            $positivityLabel = 'フラット';
            $positivityEmoji = '😐';
        } else {
            $positivityLabel = 'ネガティブ';
            $positivityEmoji = '😔';
        }

        return view('dashboard', compact(
            'moodData', 'avgMood', 'themeCounts',
            'positivity', 'positivityLabel', 'positivityEmoji'
        ));
    }
}