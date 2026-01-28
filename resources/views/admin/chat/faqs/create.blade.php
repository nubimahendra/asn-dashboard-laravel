@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center gap-2 mb-6">
            <a href="{{ route('admin.chat.faqs.index') }}"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Tambah FAQ Baru</h1>
        </div>

        <div class="max-w-3xl bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border-t-4 border-blue-500">
            <form action="{{ route('admin.chat.faqs.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="question"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pertanyaan</label>
                    <input type="text" id="question" name="question" value="{{ old('question') }}" required
                        class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 transition-all">
                    @error('question') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="keywords" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keyword
                        (Pisahkan dengan koma)</label>
                    <input type="text" id="keywords" name="keywords" value="{{ old('keywords') }}" required
                        placeholder="contoh: gaji, slip gaji, tunjangan"
                        class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 transition-all">
                    @error('keywords') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="answer"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jawaban</label>
                    <textarea id="answer" name="answer" rows="5" required
                        class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 transition-all">{{ old('answer') }}</textarea>
                    @error('answer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('admin.chat.faqs.index') }}"
                        class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 transition-all">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 hover:-translate-y-0.5 focus:ring-2 focus:ring-offset-1 focus:ring-blue-500 transition-all transform">
                        Simpan FAQ
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection