@php
    $isOut = $message->direction === 'out';
@endphp

<div class="flex {{ $isOut ? 'justify-end' : 'justify-start' }}">
    <div class="max-w-[70%] relative group">
        <div class="px-4 py-2 rounded-lg shadow-sm text-sm 
            {{ $isOut
    ? 'bg-green-100 dark:bg-green-900 text-gray-800 dark:text-gray-100 rounded-tr-none'
    : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-tl-none border border-gray-100 dark:border-gray-700' 
            }}">

            <p class="whitespace-pre-wrap leading-relaxed">{{ $message->message }}</p>

            <div class="flex items-center justify-end space-x-1 mt-1 opacity-70">
                @if($isOut && $message->is_handled_by_bot)
                    <span
                        class="text-[10px] uppercase font-bold tracking-wider mr-1 text-green-700 dark:text-green-300">BOT</span>
                @endif
                <span class="text-[10px]">{{ $message->created_at->format('H:i') }}</span>
                @if($isOut)
                    <span class="{{ $message->is_read ? 'text-blue-500' : 'text-gray-400' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>