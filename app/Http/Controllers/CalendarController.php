<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    // カレンダー表示（月間）
    public function index(Request $request)
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $currentMonth = Carbon::create($year, $month, 1);
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        // その月の日記を日付ごとにグループ化（日付をキーにして件数が分かるように）
        $diaryDates = Auth::user()->diaries()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(fn ($diary) => $diary->created_at->format('Y-m-d'));

        $prevMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        return view('diaries.calendar', compact(
            'currentMonth', 'startOfMonth', 'endOfMonth',
            'diaryDates', 'prevMonth', 'nextMonth'
        ));
    }

    // その日の日記一覧
    public function day(string $date)
    {
        $day = Carbon::parse($date);

        $diaries = Auth::user()->diaries()
            ->whereDate('created_at', $day)
            ->latest()
            ->get();

        return view('diaries.day', compact('diaries', 'day'));
    }
}