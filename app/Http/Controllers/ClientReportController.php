<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientUpdate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientReportController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $clients = $user->getAccessibleClients();

        // Dashboard Statistics
        $stats = [
            'total_clients' => $clients->count(),
            'active_clients' => $clients->active()->count(),
            'late_clients' => $clients->late()->count(),
            'contacted_this_month' => $clients
                ->where('last_contact_date', '>=', now()->startOfMonth())
                ->count(),
            'without_followup' => $clients->whereNull('next_followup_date')->count(),
            'updates_this_month' => $user->clientUpdates()
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
        ];

        // Recent Activity
        $recentUpdates = $user->clientUpdates()
            ->with('client')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Late Clients
        $lateClients = $clients->late()
            ->with('assignedEmployee')
            ->orderBy('last_contact_date', 'asc')
            ->limit(10)
            ->get();

        // Upcoming Follow-ups
        $upcomingFollowups = $clients
            ->whereNotNull('next_followup_date')
            ->where('next_followup_date', '<=', now()->addDays(7))
            ->where('next_followup_date', '>=', now())
            ->with('assignedEmployee')
            ->orderBy('next_followup_date', 'asc')
            ->limit(10)
            ->get();

        return view('reports.dashboard', compact('stats', 'recentUpdates', 'lateClients', 'upcomingFollowups'));
    }

    public function monthlyReport(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', now()->format('Y-m'));
        $date = Carbon::parse($month);

        $clients = $user->getAccessibleClients();
        
        $reports = [];
        foreach ($clients->get() as $client) {
            $updates = $client->updates()
                ->where('contact_date', '>=', $date->startOfMonth())
                ->where('contact_date', '<=', $date->endOfMonth())
                ->with('user')
                ->orderBy('contact_date', 'desc')
                ->get();

            $contactDays = $updates->pluck('contact_date')->unique()->count();
            $totalUpdates = $updates->count();
            
            // Calculate late days
            $lateDays = 0;
            $lastContactDate = null;
            
            for ($day = $date->copy()->startOfMonth(); $day <= $date->copy()->endOfMonth(); $day->addDay()) {
                $hasContact = $updates->contains('contact_date', $day->format('Y-m-d'));
                
                if ($lastContactDate && !$hasContact) {
                    $daysSinceContact = $day->diffInDays($lastContactDate);
                    if ($daysSinceContact > SystemSetting::getInteger('allowed_client_late_days', 4)) {
                        $lateDays++;
                    }
                }
                
                if ($hasContact) {
                    $lastContactDate = $day->copy();
                }
            }

            $reports[] = [
                'client' => $client,
                'updates' => $updates,
                'contact_days' => $contactDays,
                'total_updates' => $totalUpdates,
                'late_days' => $lateDays,
                'contact_frequency' => $contactDays > 0 ? round(($totalUpdates / $contactDays), 2) : 0,
            ];
        }

        return view('reports.monthly', compact('reports', 'month', 'date'));
    }

    public function clientMonthlyReport(Client $client, Request $request)
    {
        $this->authorize('view', $client);

        $month = $request->get('month', now()->format('Y-m'));
        $date = Carbon::parse($month);

        $updates = $client->updates()
            ->where('contact_date', '>=', $date->startOfMonth())
            ->where('contact_date', '<=', $date->endOfMonth())
            ->with('user')
            ->orderBy('contact_date', 'desc')
            ->get();

        // Calendar data
        $calendar = [];
        for ($day = 1; $day <= $date->daysInMonth; $day++) {
            $currentDate = $date->copy()->day($day);
            $dayUpdates = $updates->filter(fn($update) => 
                Carbon::parse($update->contact_date)->day == $day
            );

            $calendar[$day] = [
                'date' => $currentDate,
                'updates' => $dayUpdates,
                'has_contact' => $dayUpdates->count() > 0,
                'is_today' => $currentDate->isToday(),
                'is_weekend' => $currentDate->isWeekend(),
            ];
        }

        // Statistics
        $stats = [
            'total_updates' => $updates->count(),
            'contact_days' => $updates->pluck('contact_date')->unique()->count(),
            'updates_by_type' => $updates->groupBy('update_type')->map->count(),
            'average_updates_per_contact' => $updates->count() > 0 ? 
                round($updates->count() / max($updates->pluck('contact_date')->unique()->count(), 1), 2) : 0,
        ];

        return view('reports.client-monthly', compact('client', 'updates', 'calendar', 'stats', 'month', 'date'));
    }

    public function exportMonthlyReport(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $format = $request->get('format', 'pdf');

        // This will be implemented with proper export libraries
        // For now, return a placeholder response
        
        return response()->json([
            'message' => 'Export functionality will be implemented with PDF/Excel libraries',
            'month' => $month,
            'format' => $format,
        ]);
    }

    public function analytics(Request $request)
    {
        $user = Auth::user();
        $clients = $user->getAccessibleClients();

        // Time range filters
        $startDate = $request->get('start_date', now()->subMonths(6));
        $endDate = $request->get('end_date', now());

        // Client growth over time
        $clientGrowth = $clients->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Updates over time
        $updatesOverTime = $user->clientUpdates()
            ->selectRaw('DATE(contact_date) as date, COUNT(*) as count')
            ->where('contact_date', '>=', $startDate)
            ->where('contact_date', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Client status distribution
        $statusDistribution = $clients->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Late status distribution
        $lateStatusDistribution = $clients->selectRaw('late_status, COUNT(*) as count')
            ->groupBy('late_status')
            ->get();

        // Employee performance (admin only)
        $employeePerformance = [];
        if ($user->isAdmin()) {
            $employeePerformance = User::employees()
                ->withCount(['assignedClients', 'clientUpdates' => function($query) use ($startDate, $endDate) {
                    $query->where('contact_date', '>=', $startDate)
                          ->where('contact_date', '<=', $endDate);
                }])
                ->get();
        }

        return view('reports.analytics', compact(
            'clientGrowth',
            'updatesOverTime',
            'statusDistribution',
            'lateStatusDistribution',
            'employeePerformance',
            'startDate',
            'endDate'
        ));
    }

    public function lateClientsReport(Request $request)
    {
        $user = Auth::user();
        $clients = $user->getAccessibleClients()->late();

        if ($request->filled('employee_id')) {
            $clients->where('assigned_employee_id', $request->employee_id);
        }

        $lateClients = $clients->with('assignedEmployee')
            ->orderBy('last_contact_date', 'asc')
            ->paginate(20);

        $employees = User::employees()->get();

        return view('reports.late-clients', compact('lateClients', 'employees'));
    }
}
