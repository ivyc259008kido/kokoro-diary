<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiaryController extends Controller
{
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

    public function store(Request $request)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $body = $request->body;

        try {
            // 🌟 キャッシュ（同じ日記はAI呼ばない）
            $analysis = \Illuminate\Support\Facades\Cache::remember(
                'diary_ai_' . md5($body),
                3600,
                function () use ($body) {
                    return $this->getAiReply($body);
                }
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

        Auth::user()->diaries()->create([
            'body' => $body,
            'summary' => $analysis['summary'] ?? null,
            'mood' => $analysis['mood'] ?? null,
            'encouragement' => $analysis['encouragement'] ?? null,
            'themes' => $analysis['themes'] ?? [],
        ]);

        return redirect()->route('diaries.index');
    }

    // AI呼び出し
    private function getAiReply(string $body): array
    {
        $apiKey = env('GEMINI_API_KEY');

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $prompt = <<<EOT
あなたは日記を書いた人の「少し先を歩いている先輩」です。

立場：
・同じ経験をしてきた
・今は少しだけ客観視できる位置にいる
・説教はしないが、現実的な視点は持っている
・一般論ではなく、経験者としての具体的な視点を含めること

目的：
・日記の内容を整理する
・感情の流れを理解する
・必要なら「こういう見方もある」と軽く提案する

トーン：
・友達ではなく先輩
・厳しすぎない
・でも甘やかしすぎない
・リアルな視点を少し入れる

絶対ルール：
・出力はJSONのみ
・余計な文章は禁止
・コードブロック禁止

出力形式：
{
    "summary": "200文字以内の要約",
    "mood": 1から5の数値,
    "encouragement": "先輩としてのアドバイス",
    "themes": ["テーマ1", "テーマ2"]
}

日記：
{$body}
EOT;

        try {
            $response = retry(3, function () use ($url, $prompt) {
                return Http::timeout(20)
                    ->withoutVerifying()
                    ->post($url, [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]]
                        ],
                        'generationConfig' => [
                            'responseMimeType' => 'application/json',
                        ]
                    ]);
            }, 200);

            if (!$response->successful()) {
                Log::error('Gemini API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return $this->fallbackResponse('AI取得に失敗しました。');
            }

            $text = $response->json('candidates.0.content.parts.0.text');

            if (!$text) {
                Log::error('Gemini empty response', $response->json());
                return $this->fallbackResponse('AIの応答が空でした。');
            }

            $data = json_decode($text, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON decode failed', [
                    'error' => json_last_error_msg(),
                    'raw' => $text,
                ]);

                return $this->fallbackResponse('AIの解析結果が不正でした。');
            }

            return [
                'summary' => $data['summary'] ?? null,
                'mood' => $data['mood'] ?? null,
                'encouragement' => $data['encouragement'] ?? null,
                'themes' => $data['themes'] ?? [],
            ];

        } catch (\Throwable $e) {
            Log::error('Gemini exception', [
                'message' => $e->getMessage(),
            ]);

            return $this->fallbackResponse('AI処理中にエラーが発生しました。');
        }
    }

    // フォールバック
    private function fallbackResponse(string $message): array
    {
        return [
            'summary' => null,
            'mood' => null,
            'encouragement' => $message,
            'themes' => [],
        ];
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
        $request->validate([
            'body' => 'required|string',
        ]);

        $diary->update([
            'body' => $request->body,
        ]);

        return redirect()->route('diaries.show', $diary);
    }

    // 削除
    public function destroy(Diary $diary)
    {
        $diary->delete();
        return redirect()->route('diaries.index');
    }
}