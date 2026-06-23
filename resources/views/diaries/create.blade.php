<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            日記を書く
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('diaries.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">今日のできごと・気持ち</label>
                        <textarea name="body" rows="8"
                            class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                            placeholder="今日はどんな一日でしたか？">{{ old('body') }}</textarea>
                        @error('body')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('diaries.index') }}"
                            class="px-4 py-2 rounded border border-gray-300 text-gray-600 hover:bg-gray-100">
                            キャンセル
                        </a>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600">
                            送信する
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>