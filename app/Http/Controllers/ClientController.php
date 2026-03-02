<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->getAccessibleClients();

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('employee_id')) {
            $query->where('assigned_employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('late_status')) {
            if ($request->late_status === 'late') {
                $query->late();
            } elseif ($request->late_status === 'active') {
                $query->active();
            }
        }

        if ($request->filled('has_unread')) {
            if ($request->has_unread === 'true') {
                $query->whereHas('unreadMessages');
            }
        }

        if ($request->filled('date_from')) {
            $query->where('last_contact_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('last_contact_date', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $clients = $query->with(['assignedEmployee', 'unreadMessages'])->paginate(15);
        $employees = User::employees()->get();

        // Statistics
        $stats = [
            'total_clients' => $user->getAccessibleClients()->count(),
            'active_clients' => $user->getAccessibleClients()->active()->count(),
            'late_clients' => $user->getAccessibleClients()->late()->count(),
            'contacted_this_month' => $user->getAccessibleClients()
                ->where('last_contact_date', '>=', now()->startOfMonth())
                ->count(),
            'without_followup' => $user->getAccessibleClients()
                ->whereNull('next_followup_date')
                ->count(),
            'updates_this_month' => $user->clientUpdates()
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
        ];

        return view('clients.index', compact('clients', 'employees', 'stats'));
    }

    public function create()
    {
        // Only employees can create clients
        if (!Auth::user()->isEmployee()) {
            abort(403, 'Only employees can create clients.');
        }
        
        $employees = User::employees()->get();
        return view('clients.create', compact('employees'));
    }

    public function store(Request $request)
    {
        // Only employees can create clients
        if (!Auth::user()->isEmployee()) {
            abort(403, 'Only employees can create clients.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => ['required', Rule::in(['active', 'inactive', 'prospect'])],
            'assigned_employee_id' => 'nullable|exists:users,id',
            'next_followup_date' => 'nullable|date|after_or_equal:today',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['total_updates_count'] = 0;
        $validated['late_status'] = 'active';

        $client = Client::create($validated);

        // Create initial chat message
        $client->chatMessages()->create([
            'sender_id' => Auth::id(),
            'message' => "Client created by " . Auth::user()->name . " on " . now()->format('M d, Y'),
            'message_type' => 'system',
        ]);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);

        $client->load(['assignedEmployee', 'createdBy', 'updates.user', 'chatMessages.sender']);
        
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $this->authorize('update', $client);

        $employees = User::employees()->get();
        return view('clients.edit', compact('client', 'employees'));
    }

    public function update(Request $request, Client $client)
    {
        $this->authorize('update', $client);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => ['required', Rule::in(['active', 'inactive', 'prospect'])],
            'assigned_employee_id' => 'nullable|exists:users,id',
            'next_followup_date' => 'nullable|date|after_or_equal:today',
        ]);

        $client->update($validated);
        $client->updateLateStatus();

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    public function updateLateStatuses(): JsonResponse
    {
        $clients = Client::all();
        $updated = 0;

        foreach ($clients as $client) {
            $oldStatus = $client->late_status;
            $client->updateLateStatus();
            
            if ($oldStatus !== $client->late_status) {
                $updated++;
            }
        }

        return response()->json([
            'message' => "Late status updated for {$updated} clients.",
            'updated_count' => $updated,
        ]);
    }

    public function markMessagesAsRead(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        $unreadCount = $client->unreadMessages()->count();
        $client->unreadMessages()->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'message' => "Marked {$unreadCount} messages as read.",
            'unread_count' => 0,
        ]);
    }
}
