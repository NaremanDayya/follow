<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientUpdate extends Model
{
    protected $fillable = [
        'client_id',
        'user_id',
        'update_content',
        'update_type',
        'contact_date',
        'next_followup_date',
        'notes',
    ];

    protected $casts = [
        'contact_date' => 'date',
        'next_followup_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($update) {
            $client = $update->client;
            
            // Update client's last contact date
            $client->last_contact_date = $update->contact_date;
            $client->next_followup_date = $update->next_followup_date;
            $client->total_updates_count = $client->updates()->count();
            $client->updateLateStatus();
            
            // Create chat message for the update
            $client->chatMessages()->create([
                'sender_id' => $update->user_id,
                'message' => "Follow-up update: {$update->update_content}",
                'message_type' => 'follow_up',
            ]);
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('update_type', $type);
    }
}
