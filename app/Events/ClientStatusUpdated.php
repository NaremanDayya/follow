<?php

namespace App\Events;

use App\Models\Client;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $client;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Client $client, string $oldStatus, string $newStatus)
    {
        $this->client = $client;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('clients'),
            new PrivateChannel('client.' . $this->client->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'client.status.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name,
                'late_status' => $this->newStatus,
                'last_contact_date' => $this->client->last_contact_date?->toISOString(),
                'assigned_employee_id' => $this->client->assigned_employee_id,
            ],
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'notification' => "Client {$this->client->name} status changed from {$this->oldStatus} to {$this->newStatus}",
        ];
    }
}
