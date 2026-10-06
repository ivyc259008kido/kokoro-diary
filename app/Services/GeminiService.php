<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $url;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$this->apiKey}";
    }

    // 日記への返信を生成
    public function generateDiaryReply(string $body): array
    {
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
            $response = retry(3, function () use ($prompt) {
                return Http::timeout(12)
                    ->withoutVerifying()
                    ->post($this->url, [
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
            Log::error('Gemini exception', ['message' => $e->getMessage()]);
            return $this->fallbackResponse('AI処理中にエラーが発生しました。');
        }
    }

    // 月次レポートを生成
    public function generateMonthlyReport(string $diaryTexts, int $year, int $month): string
    {
        $prompt = "Read the following diary entries from {$year}/{$month} and write a monthly reflection report in Japanese. Include main themes, emotional trends, memorable moments, and a message for next month.\n\nDiaries:\n{$diaryTexts}";

        try {
            $response = retry(3, function () use ($prompt) {
                return Http::timeout(30)
                    ->withoutVerifying()
                    ->post($this->url, [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]]
                        ]
                    ]);
            }, 200);

            Log::info('Gemini monthly report response', $response->json());

            return $response->json('candidates.0.content.parts.0.text') ?? 'Report generation failed.';

        } catch (\Throwable $e) {
            Log::error('Gemini monthly report exception', ['message' => $e->getMessage()]);
            return 'Report generation failed.';
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
}