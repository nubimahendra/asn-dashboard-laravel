<!-- Header -->
<div
    class="flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-sm z-10">
    <div class="flex items-center space-x-3">
        <div
            class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300 font-bold">
            {{ $pegawai ? substr($pegawai->nama_pegawai, 0, 1) : '?' }}
        </div>
        <div>
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100">
                {{ $pegawai ? $pegawai->nama_pegawai : $phoneNumber }}
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ $pegawai ? $pegawai->jabatan : 'Unknown' }}
            </p>
        </div>
    </div>
    <div class="text-xs text-gray-400">
        {{ $phoneNumber }}
    </div>
</div>

<!-- Chat History -->
{{-- Add padding bottom to account for fixed/sticky footer or just normal flow --}}
<div id="chat-history" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar bg-slate-100 dark:bg-slate-900">
    @foreach($messages as $message)
        @include('admin.chat.partials.bubble', ['message' => $message])
    @endforeach
</div>

<!-- Footer / Input -->
<div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
    <form id="reply-form" class="relative">
        <input type="hidden" name="phone_number" value="{{ $phoneNumber }}">
        <textarea name="message" rows="1"
            class="w-full pl-4 pr-12 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none custom-scrollbar text-sm text-gray-800 dark:text-gray-200"
            placeholder="Tulis balasan..." required></textarea>
        <button type="submit"
            class="absolute right-2 bottom-2 p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
            </svg>
        </button>
    </form>
</div>