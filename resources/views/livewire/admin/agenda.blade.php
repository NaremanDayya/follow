<?php

use App\Models\DailyLog;
use App\Models\Project;
use Livewire\Volt\Component;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

new class extends Component
{
    public $currentMonth;
    public $selectedDate;
    public $todayDate;
    public $days = [];

    public function mount()
    {
        $this->currentMonth = now()->startOfMonth();
        $this->selectedDate = now()->format('Y-m-d');
        $this->todayDate = now()->format('Y-m-d');
        $this->generateDays();
    }

    public function generateDays()
    {
        $start = $this->currentMonth->copy()->startOfMonth();
        $end = $this->currentMonth->copy()->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        $this->days = [];
        foreach ($period as $date) {
            // Remove Sundays (0) and Fridays (5)
            if ($date->dayOfWeek !== Carbon::SUNDAY && $date->dayOfWeek !== Carbon::FRIDAY) {
                $this->days[] = [
                    'date' => $date->format('Y-m-d'),
                    'dayName' => $date->translatedFormat('D'), // Short day name
                    'dayNumber' => $date->format('d'),
                    'hasLogs' => DailyLog::whereDate('date', $date)->exists(),
                    'totalHours' => DailyLog::whereDate('date', $date)->sum('hours'),
                ];
            }
        }
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->dispatch('dateSelected', date: $date);
    }

    public function goToToday()
    {
        $this->currentMonth = now()->startOfMonth();
        $this->selectedDate = $this->todayDate;
        $this->generateDays();
        $this->dispatch('dateSelected', date: $this->selectedDate);
    }

    public function previousMonth()
    {
        $this->currentMonth->subMonth();
        $this->generateDays();
    }

    public function nextMonth()
    {
        $this->currentMonth->addMonth();
        $this->generateDays();
    }
}; ?>

<div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/40 rounded-2xl flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">أجندة المهام والشغل</h3>
                <p class="text-gray-500 text-xs font-bold">{{ $currentMonth->translatedFormat('F Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-900/50 p-2 rounded-2xl">
            <button wire:click="previousMonth" class="p-2 hover:bg-white dark:hover:bg-gray-800 rounded-xl transition shadow-sm group">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
            <button wire:click="goToToday" class="px-4 py-2 bg-white dark:bg-gray-800 text-xs font-black text-gray-600 dark:text-gray-300 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:bg-indigo-600 hover:text-white transition">
                اليوم
            </button>
            <button wire:click="nextMonth" class="p-2 hover:bg-white dark:hover:bg-gray-800 rounded-xl transition shadow-sm group">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-7 xl:grid-cols-8 gap-4">
        @foreach($days as $day)
            <button 
                wire:click="selectDate('{{ $day['date'] }}')"
                class="relative p-5 rounded-[2rem] border transition-all duration-300 group {{ $selectedDate === $day['date'] ? 'bg-indigo-600 border-indigo-600 shadow-xl shadow-indigo-500/20' : ($todayDate === $day['date'] ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-100 dark:border-indigo-800' : 'bg-gray-50 dark:bg-gray-900 border-transparent hover:border-indigo-200 dark:hover:border-indigo-800') }}"
            >
                @if($day['hasLogs'])
                    <div class="absolute top-4 left-4 flex gap-1">
                        <span class="w-2 h-2 rounded-full {{ $selectedDate === $day['date'] ? 'bg-white' : 'bg-indigo-500 animate-pulse' }}"></span>
                    </div>
                @endif
                
                <div class="text-center">
                    <span class="block text-[10px] font-black uppercase tracking-widest mb-2 {{ $selectedDate === $day['date'] ? 'text-indigo-100' : ($todayDate === $day['date'] ? 'text-indigo-600' : 'text-gray-400') }}">
                        {{ $day['dayName'] }}
                    </span>
                    <span class="block text-3xl font-black {{ $selectedDate === $day['date'] ? 'text-white' : 'text-gray-900 dark:text-white' }}">
                        {{ $day['dayNumber'] }}
                    </span>
                    
                    @if($day['totalHours'] > 0)
                        <div class="mt-3">
                            <span class="inline-block px-3 py-1 rounded-xl text-[9px] font-black {{ $selectedDate === $day['date'] ? 'bg-indigo-500 text-white shadow-inner' : 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300' }}">
                                {{ (float)$day['totalHours'] }} ساعة
                            </span>
                        </div>
                    @endif
                </div>
            </button>
        @endforeach
    </div>
</div>
