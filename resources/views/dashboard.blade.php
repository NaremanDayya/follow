<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-6 bg-gradient-to-b from-indigo-600 to-purple-600 rounded-full"></div>
            <h2 class="font-black text-xl text-gray-900 dark:text-white leading-tight">
                {{ Auth::user()->isAdmin() ? 'لوحة تحكم الإدارة' : 'نظام متابعة العملاء' }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(Auth::user()->isAdmin())
                <livewire:admin.dashboard />
            @else
                <livewire:work-logs.index />
            @endif
        </div>
    </div>
</x-app-layout>
