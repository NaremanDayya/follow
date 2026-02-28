<?php

namespace App\Http\Controllers;

use App\Events\ClientChatMessageSent;
use App\Models\Client;
use App\Models\ClientChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientChatController extends Controller
{
    public function index(Client $client)
    {
        $this->authorize('view', $client);

        $messages = $client->chatMessages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark unread messages as read for current user
        $client->unreadMessages()
            ->where('sender_id', '!=', Auth::id())
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('clients.chat', compact('client', 'messages'));
    }

    public function store(Request $request, Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        DB::beginTransaction();
        try {
            $message = $client->chatMessages()->create([
                'sender_id' => Auth::id(),
                'message' => $validated['message'],
                'message_type' => 'text',
                'is_read' => false,
            ]);

            // Update client's last contact date
            $client->last_contact_date = now();
            $client->updateLateStatus();
            $client->save();

            DB::commit();

            // Broadcast the message for real-time updates
            broadcast(new ClientChatMessageSent($message));

            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
                'unread_count' => $client->unread_count,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to send message. Please try again.',
            ], 500);
        }
    }

    public function markAsRead(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        $unreadMessages = $client->unreadMessages()
            ->where('sender_id', '!=', Auth::id())
            ->get();

        foreach ($unreadMessages as $message) {
            $message->markAsRead();
        }

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    public function getUnreadCount(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        $unreadCount = $client->unreadMessages()
            ->where('sender_id', '!=', Auth::id())
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
        ]);
    }

    public function getRecentMessages(Client $client, Request $request): JsonResponse
    {
        $this->authorize('view', $client);

        $limit = min($request->get('limit', 20), 50);
        $lastMessageId = $request->get('last_message_id');

        $query = $client->chatMessages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        if ($lastMessageId) {
            $query->where('id', '>', $lastMessageId);
        }

        $messages = $query->get()->reverse(); // Reverse to show oldest first

        return response()->json([
            'messages' => $messages,
            'has_more' => $messages->count() === $limit,
        ]);
    }

    public function deleteMessage(Client $client, ClientChatMessage $message): JsonResponse
    {
        $this->authorize('view', $client);

        // Only sender or admin can delete messages
        if ($message->sender_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized to delete this message.',
            ], 403);
        }

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully.',
        ]);
    }

    public function getTypingUsers(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        // This will be implemented with WebSockets/Pusher for real-time typing indicators
        return response()->json([
            'typing_users' => [],
        ]);
    }

    public function broadcastTyping(Client $client, Request $request): JsonResponse
    {
        $this->authorize('view', $client);

        $validated = $request->validate([
            'is_typing' => 'required|boolean',
        ]);

        // This will be implemented with WebSockets/Pusher for real-time broadcasting
        // For now, we'll just return success

        return response()->json([
            'success' => true,
        ]);
    }
}
