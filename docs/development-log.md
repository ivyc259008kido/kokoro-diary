# 開発ログ

kokoro diary の開発中に発生した問題と、その対応の記録。

---

## 2026-08-11 〜 09-07: 動作確認と改善

### 1. Gemini API の 503 エラー調査

**症状**
- 夏季休暇明け、約1ヶ月ぶりにアプリを使用したところ、日記保存後に「AIの取得に失敗しました」と表示された
- ブラウザのコンソールにはエラーが出ておらず、原因不明だった

**調査**
- `storage/logs/laravel.log` を確認し、該当日時のエラーを特定
```
local.ERROR: Gemini API failed {"status":503,"body":"{
  \"error\": {
    \"code\": 503,
    \"message\": \"This model is currently experiencing high demand.
    Spikes in demand are usually temporary. Please try again later.\",
    \"status\": \"UNAVAILABLE\"
  }
}
```

**原因**
- Gemini API 側（無料枠）の一時的な過負荷。自分のコードのバグではなかった

**対応・学び**
- 翌日には自然に解消していた
- 今後の改善案として、自動リトライ処理の追加を検討中
- 発表では「開発中に直面した外部APIの制約」として紹介できる

---

### 2. CSS が崩れて表示される

**症状**
- 画面がスタイルの当たっていない素の見た目で表示された（ボタンや配置が装飾されていない）

**原因**
- Vite の開発サーバー（`npm run dev`）が起動していなかった

**対応**
```bash
npm run dev
```
起動後、ブラウザをリロードして解消

---

### 3. ログイン済みユーザーのトップページ遷移

**症状**
- ログイン済みの状態で `http://127.0.0.1:8000` にアクセスすると、
  日記画面ではなくダッシュボードに遷移してしまう

**原因**
`routes/web.php` の `/` ルートが、ログイン状態に関係なく
常に `login` ルートへリダイレクトしていた。ログイン済みの場合、
`login` 画面が自動的にダッシュボードへリダイレクトする仕様のため、
結果的にダッシュボード行きになっていた

**対応**
```php
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('diaries.index');
    }
    return redirect()->route('login');
});
```
ログイン済みなら日記一覧へ直接遷移するように修正

---

### 4. プロフィール画面が英語表示のまま

**症状**
- `/profile` にアクセスすると、Laravel Breeze 標準の英語表示
  （Profile Information / Update Password / Delete Account）のままだった

**原因**
- 日本語の翻訳ファイルが用意されておらず、`__()` 関数がそのまま英語を返していた

**対応**
- 該当する3つの Blade ファイルを直接日本語に書き換え
  - `update-profile-information-form.blade.php`
  - `update-password-form.blade.php`
  - `delete-user-form.blade.php`

---

### 5. プロフィール画面への導線がなかった

**症状**
- ヘッダーの名前部分をクリックしても何も起きない
- 初めてプロフィール画面の存在に気づいた

**原因**
- `app.blade.php` が独自にカスタマイズされており、
  Laravel Breeze 標準の `navigation.blade.php`（ドロップダウンメニュー付き）
  を使わず、名前部分がただの `<span>` タグとして書かれていた

**対応**
```php
<a href="{{ route('profile.edit') }}"
    class="text-gray-400 text-sm hover:text-purple-500 transition">
    {{ Auth::user()->name ?? '' }}
</a>
```
`<span>` を `<a>` に変更し、プロフィール画面へのリンクを追加

---

## 気づき・今後の展望

- タグでの絞り込み・検索機能は現状未実装（タグをクリックすると日記詳細が開くのみ）
- 今回のように「実際にアプリを操作しながら確認する」ことで、
  コード上は動いているはずでも UI 導線が欠けているバグに気づけた
- 開発ログを残しておくことで、後から見返した時に対応内容をすぐ思い出せる
