<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\MonthlyReportController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('diaries', DiaryController::class);

    Route::get('/monthly-reports', [MonthlyReportController::class, 'index'])->name('monthly_reports.index');
    Route::post('/monthly-reports/generate', [MonthlyReportController::class, 'generate'])->name('monthly_reports.generate');
    Route::get('/monthly-reports/{monthlyReport}', [MonthlyReportController::class, 'show'])->name('monthly_reports.show');
    Route::delete('/monthly-reports/{monthlyReport}', [MonthlyReportController::class, 'destroy'])->name('monthly_reports.destroy');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/day/{date}', [CalendarController::class, 'day'])->name('calendar.day');
});

require __DIR__.'/auth.php';