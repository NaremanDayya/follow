<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-6 bg-gradient-to-b from-indigo-600 to-purple-600 rounded-full"></div>
            <h2 class="font-black text-xl text-gray-900 dark:text-white leading-tight">
                المحادثات
            </h2>
        </div>
    </x-slot>

    <div class="h-[calc(100vh-8rem)] flex">
        <!-- Chat List Sidebar -->
        <div class="w-80 bg-white dark:bg-gray-900 border-l border-gray-200 dark:border-gray-700 overflow-y-auto">
            <div class="p-4 border-b border-gray-100 dark:border-gray-800">
                <div class="relative">
                    <input type="text"
                           placeholder="البحث عن محادثة..."
                           class="w-full px-4 py-2 pr-10 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm">
                    <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($clients as $client)
                    <a href="{{ route('clients.chat', $client) }}"
                       class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150 relative">
                        @if($client->unread_count > 0)
                            <span class="absolute top-4 right-4 w-6 h-6 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold">
                                {{ $client->unread_count }}
                            </span>
                        @endif

                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                {{ substr($client->name, 0, 1) }}
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $client->name }}
                                    </h3>
                                    @if($client->last_message_at)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $client->last_message_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>

                                @if($client->company)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                        {{ $client->company }}
                                    </p>
                                @endif

                                @if($client->last_message)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate mt-1">
                                        {{ Str::limit($client->last_message, 50) }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-400 dark:text-gray-500 italic mt-1">
                                        لا توجد رسائل
                                    </p>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8c0 1.574-.512 3.042-1.395 3.72l1.395 3.72C19.417 19.823 21 17.418 21 12z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            لا توجد محادثات
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                        ابدأ محادثة مع العملاء لرؤيتها هنا
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
        <!-- Chat Area (Empty state) -->
        <div class="flex-1 flex items-center justify-center bg-gray-50 dark:bg-gray-950">
            <div class="text-center">
                <div class="w-24 h-24 bg-gray-200 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8c0 1.574-.512 3.042-1.395 3.72l1.395 3.72C19.417 19.823 21 17.418 21 12z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                    اختر محادثة لبدء الدردشة
                </h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md">
                    اختر عميلاً من القائمة الجانبية لعرض المحادثة وبدء الدردشة
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
