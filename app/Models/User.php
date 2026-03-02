<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'developer';
    }

    public function canManageClients(): bool
    {
        return $this->isAdmin() || $this->isEmployee();
    }

    public function assignedClients(): HasMany
    {
        return $this->hasMany(Client::class, 'assigned_employee_id');
    }

    public function createdClients(): HasMany
    {
        return $this->hasMany(Client::class, 'created_by');
    }

    public function clientUpdates(): HasMany
    {
        return $this->hasMany(ClientUpdate::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ClientChatMessage::class, 'sender_id');
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }

    public function scopeEmployees($query)
    {
        return $query->where('role', 'developer');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function getAccessibleClients()
    {
        if ($this->isAdmin()) {
            return Client::query();
        }

        return $this->assignedClients();
    }

    public function canAccessClient(Client $client): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $client->assigned_employee_id === $this->id;
    }

    public function canEditClient(Client $client): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $client->created_by === $this->id || $client->assigned_employee_id === $this->id;
    }
}
