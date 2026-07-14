# Kokoro Diary 📖

> 書いて、気づいて、動き出す。

AIとの交換日記型ジャーナリングアプリです。日々の出来事や感情を日記として記録すると、AIが共感やアドバイスの返信をくれます。蓄積された日記データからAIが感情を自動分析・可視化することで、自己理解を深めることができます。

---

## 主な機能

### 📝 日記投稿（CRUD）
- 日記の作成・閲覧・編集・削除
- タイムライン形式で過去の日記を一覧表示

### 🤖 AI自動返信・感情分析
- 日記投稿時にGemini APIが返信を自動生成
- 感情タグ（例：疲労・学習・体調管理など）を自動抽出
- 気分スコア（1〜5）を自動判定

### 📊 感情トレンドグラフ
- 直近30日間の気分スコアを折れ線グラフで可視化
- 平均スコアとポジティブ度を表示
- 主要テーマタグを集計して表示

### 📅 月次レポート
- 1ヶ月分の日記をAIがまとめて振り返りレポートを生成
- 今月のテーマ・感情の傾向・印象的な出来事・来月へのメッセージを自動生成
- レポートの閲覧・削除が可能

### 🔐 ユーザー管理
- 新規登録・ログイン・ログアウト

---

## 使い方

1. 新規登録またはログイン
2. ホーム画面の「✏️ 今日のことを書く」から日記を投稿
3. 投稿するとAIから返信が届く
4. 「📅 月次レポートを見る」から感情トレンドグラフと月次レポートを確認

---

## 使用技術

| カテゴリ | 技術 |
|---|---|
| バックエンド | PHP / Laravel 13 |
| フロントエンド | Blade / Tailwind CSS |
| データベース | SQLite |
| AI連携 | Gemini API（Google） |
| グラフ描画 | Chart.js |
| 認証 | Laravel Breeze |

---

## 環境構築手順

### 必要な環境
- PHP 8.2以上
- Composer
- Node.js / npm

### セットアップ

```bash
# リポジトリをクローン
git clone https://github.com/ivyc259008kido/kokoro-diary.git
cd kokoro-diary

# パッケージインストール
composer install
npm install

# 環境ファイルの設定
cp .env.example .env
php artisan key:generate

# Gemini APIキーを.envに追加
GEMINI_API_KEY=your_api_key_here

# マイグレーション実行
php artisan migrate

# 開発サーバー起動
php artisan serve
npm run dev
```

ブラウザで `http://127.0.0.1:8000` を開いてください。

---

## DB設計

users (1) ──── (多) diaries
diaries (多) ── diary_tag（中間テーブル） ── (多) tags
users (1) ──── (多) monthly_reports

| テーブル名 | 役割 |
|---|---|
| users | ユーザー情報 |
| diaries | 日記本文・AI返信・感情スコア・テーマ |
| tags | 感情タグマスター |
| diary_tag | 日記と感情タグの中間テーブル |
| monthly_reports | 月次レポート |