<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use App\Models\Tag;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DiaryController extends Controller
{
    private GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    // 一覧
    public function index()
    {
        $diaries = Auth::user()->diaries()->latest()->paginate(8);
        return view('diaries.index', compact('diaries'));
    }

    // 作成フォーム
    public function create()
    {
        return view('diaries.create');
    }

    // 保存
    public function store(Request $request)
    {
        set_time_limit(60);

        $request->validate([
            'body' => 'required|string',
        ]);

        $body = $request->body;

        try {
            $analysis = Cache::remember(
                'diary_ai_' . md5($body),
                3600,
                fn() => $this->gemini->generateDiaryReply($body)
            );
        } catch (\Throwable $e) {
            Log::error('AI Error: ' . $e->getMessage());
            $analysis = [
                'summary' => null,
                'mood' => null,
                'encouragement' => '（AI取得に失敗しましたが日記は保存されました）',
                'themes' => [],
            ];
        }

        $diary = Auth::user()->diaries()->create([
            'body' => $body,
            'summary' => $analysis['summary'] ?? null,
            'mood' => $analysis['mood'] ?? null,
            'encouragement' => $analysis['encouragement'] ?? null,
            'themes' => $analysis['themes'] ?? [],
        ]);

        $tagIds = collect($analysis['themes'] ?? [])
            ->map(fn($name) => Tag::firstOrCreate(['name' => $name])->id);
        $diary->tags()->sync($tagIds);

        return redirect()->route('diaries.index');
    }

    // 詳細
    public function show(Diary $diary)
    {
        return view('diaries.show', compact('diary'));
    }

    // 編集
    public function edit(Diary $diary)
    {
        return view('diaries.edit', compact('diary'));
    }

    // 更新
    public function update(Request $request, Diary $diary)
    {
        set_time_limit(60);

        $request->validate([
            'body' => 'required|string',
        ]);

        $body = $request->body;

        try {
            $analysis = Cache::remember(
                'diary_ai_' . md5($body),
                3600,
                fn() => $this->gemini->generateDiaryReply($body)
            );
        } catch (\Throwable $e) {
            Log::error('AI Error: ' . $e->getMessage());
            $analysis = [
                'summary' => null,
                'mood' => null,
                'encouragement' => '（AI取得に失敗しましたが日記は更新されました）',
                'themes' => [],
            ];
        }

        $diary->update([
            'body' => $body,
            'summary' => $analysis['summary'] ?? null,
            'mood' => $analysis['mood'] ?? null,
            'encouragement' => $analysis['encouragement'] ?? null,
            'themes' => $analysis['themes'] ?? [],
        ]);

        $tagIds = collect($analysis['themes'] ?? [])
            ->map(fn($name) => Tag::firstOrCreate(['name' => $name])->id);
        $diary->tags()->sync($tagIds);

        return redirect()->route('diaries.show', $diary);
    }

    // 削除
    public function destroy(Diary $diary)
    {
        $diary->delete();
        return redirect()->route('diaries.index');
    }

    // タグで絞り込んだ一覧
    public function byTag(string $tagName)
    {
        $tag = Tag::where('name', $tagName)->firstOrFail();

        $diaries = $tag->diaries()
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(8);

        return view('diaries.index', [
            'diaries' => $diaries,
            'activeTag' => $tag->name,
        ]);
    }
}