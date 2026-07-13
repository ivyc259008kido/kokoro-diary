<?php

namespace App\Http\Controllers;

use App\Models\MonthlyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonthlyReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $reports = $user->monthlyReports()->orderBy('year', 'desc')->orderBy('month', 'desc')->get();

        $diaries = $user->diaries()
            ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))
            ->orderBy('created_at', 'asc')
            ->get();

        $moodData = $diaries->map(function ($diary) {
            return [
                'date' => $diary->created_at->format('n/j'),
                'mood' => $diary->mood ? $diary->mood * 20 : null,
            ];
        })->filter(fn($d) => $d['mood'] !== null)->values();

        $avgMood = $moodData->avg('mood') ?? 0;

        $allThemes = $diaries->flatMap(fn($d) => $d->themes ?? [])->filter();
        $themeCounts = $allThemes->countBy()->sortDesc()->take(5);

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

        return view('monthly_reports.index', compact(
            'reports', 'moodData', 'avgMood',
            'themeCounts', 'positivity', 'positivityLabel', 'positivityEmoji'
        ));
    }

    public function generate(Request $request)
    {
        $year = $request->input('year', now()->subMonth()->year);
        $month = $request->input('month', now()->subMonth()->month);

        $diaries = Auth::user()->diaries()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest()
            ->get();

        if ($diaries->isEmpty()) {
            return back()->with('error', 'No diaries found.');
        }

        $diaryTexts = $diaries->map(function ($diary, $index) {
            return ($index + 1) . ': ' . $diary->body;
        })->join("\n\n");

        $reportText = $this->generateReport($diaryTexts, $year, $month);

        MonthlyReport::updateOrCreate(
            ['user_id' => Auth::id(), 'year' => $year, 'month' => $month],
            ['report_text' => $reportText]
        );

        return redirect()->route('monthly_reports.index')->with('success', 'Report generated!');
    }

    public function show(MonthlyReport $monthlyReport)
    {
        return view('monthly_reports.show', compact('monthlyReport'));
    }

    public function destroy(MonthlyReport $monthlyReport)
    {
        $monthlyReport->delete();
        return redirect()->route('monthly_reports.index')->with('success', 'Report deleted.');
    }

    private function generateReport(string $diaryTexts, int $year, int $month): string
    {
        $apiKey = env('GEMINI_API_KEY');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $prompt = "Read the following diary entries from {$year}/{$month} and write a monthly reflection report in Japanese. Include main themes, emotional trends, memorable moments, and a message for next month.\n\nDiaries:\n{$diaryTexts}";

        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->post($url, [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ]);

        \Illuminate\Support\Facades\Log::info('Monthly report Gemini response', $response->json());

        return $response->json('candidates.0.content.parts.0.text') ?? 'Report generation failed.';
    }
}