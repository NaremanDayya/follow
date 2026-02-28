<?php

namespace App\Models;

use App\Events\ClientStatusUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'company',
        'address',
        'status',
        'assigned_employee_id',
        'last_contact_date',
        'next_followup_date',
        'total_updates_count',
        'late_status',
        'created_by',
    ];

    protected $casts = [
        'last_contact_date' => 'date',
        'next_followup_date' => 'date',
    ];

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_employee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(ClientUpdate::class)->orderBy('contact_date', 'desc');
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ClientChatMessage::class)->orderBy('created_at', 'asc');
    }

    public function unreadMessages(): HasMany
    {
        return $this->hasMany(ClientChatMessage::class)->where('is_read', false);
    }

    public function getUnreadCountAttribute(): int
    {
        return $this->unreadMessages()->count();
    }

    public function updateLateStatus(): void
    {
        $allowedDays = (int) SystemSetting::getValue('allowed_client_late_days', 4);
        $oldStatus = $this->late_status;
        
        if (!$this->last_contact_date) {
            $this->late_status = 'late';
        } else {
            $daysSinceLastContact = now()->diffInDays($this->last_contact_date);

            if ($daysSinceLastContact > $allowedDays) {
                $this->late_status = 'late';
            } elseif ($daysSinceLastContact >= $allowedDays - 1) {
                $this->late_status = 'near_late';
            } else {
                $this->late_status = 'active';
            }
        }

        if ($oldStatus !== $this->late_status) {
            $this->save();
            
            // Broadcast the status change
            broadcast(new ClientStatusUpdated($this, $oldStatus, $this->late_status));
        }
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('assigned_employee_id', $employeeId);
    }

    public function scopeLate($query)
    {
        return $query->whereIn('late_status', ['late', 'near_late']);
    }

    public function scopeActive($query)
    {
        return $query->where('late_status', 'active');
    }
}
