<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientUpdate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClientUpdateController extends Controller
{
    public function index(Client $client)
    {
        $this->authorize('view', $client);

        $updates = $client->updates()
            ->with('user')
            ->orderBy('contact_date', 'desc')
            ->paginate(20);

        return view('clients.updates.index', compact('client', 'updates'));
    }

    public function create(Client $client)
    {
        $this->authorize('update', $client);

        return view('clients.updates.create', compact('client'));
    }

    public function store(Request $request, Client $client)
    {
        $this->authorize('update', $client);

        $validated = $request->validate([
            'update_content' => 'required|string|max:2000',
            'update_type' => ['required', Rule::in(['call', 'email', 'meeting', 'note', 'other'])],
            'contact_date' => 'required|date|before_or_equal:today',
            'next_followup_date' => 'nullable|date|after_or_equal:contact_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['client_id'] = $client->id;
        $validated['user_id'] = Auth::id();

        $update = ClientUpdate::create($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Follow-up update added successfully.');
    }

    public function edit(Client $client, ClientUpdate $update)
    {
        $this->authorize('update', $client);

        if ($update->client_id !== $client->id) {
            abort(404);
        }

        return view('clients.updates.edit', compact('client', 'update'));
    }

    public function update(Request $request, Client $client, ClientUpdate $update)
    {
        $this->authorize('update', $client);

        if ($update->client_id !== $client->id) {
            abort(404);
        }

        $validated = $request->validate([
            'update_content' => 'required|string|max:2000',
            'update_type' => ['required', Rule::in(['call', 'email', 'meeting', 'note', 'other'])],
            'contact_date' => 'required|date|before_or_equal:today',
            'next_followup_date' => 'nullable|date|after_or_equal:contact_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $update->update($validated);

        return redirect()->route('clients.updates.index', $client)
            ->with('success', 'Update modified successfully.');
    }

    public function destroy(Client $client, ClientUpdate $update)
    {
        $this->authorize('update', $client);

        if ($update->client_id !== $client->id) {
            abort(404);
        }

        $update->delete();

        // Update client counts
        $client->total_updates_count = $client->updates()->count();
        $client->save();

        return redirect()->route('clients.updates.index', $client)
            ->with('success', 'Update deleted successfully.');
    }

    public function quickUpdate(Request $request, Client $client): JsonResponse
    {
        $this->authorize('update', $client);

        $validated = $request->validate([
            'update_content' => 'required|string|max:500',
            'update_type' => ['required', Rule::in(['call', 'email', 'meeting', 'note', 'other'])],
            'next_followup_date' => 'nullable|date|after_or_equal:today',
        ]);

        $validated['client_id'] = $client->id;
        $validated['user_id'] = Auth::id();
        $validated['contact_date'] = now()->toDateString();

        $update = ClientUpdate::create($validated);

        return response()->json([
            'success' => true,
            'update' => $update->load('user'),
            'message' => 'Quick update added successfully.',
        ]);
    }

    public function getTimeline(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        $updates = $client->updates()
            ->with('user')
            ->orderBy('contact_date', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'updates' => $updates,
        ]);
    }

    public function getStats(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        $stats = [
            'total_updates' => $client->updates()->count(),
            'updates_this_month' => $client->updates()
                ->where('contact_date', '>=', now()->startOfMonth())
                ->count(),
            'last_contact' => $client->last_contact_date,
            'next_followup' => $client->next_followup_date,
            'days_since_last_contact' => $client->last_contact_date 
                ? now()->diffInDays($client->last_contact_date) 
                : null,
        ];

        return response()->json($stats);
    }
}
