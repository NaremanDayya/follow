<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->canManageClients();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $client): bool
    {
        return $user->canAccessClient($client);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->canManageClients();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Client $client): bool
    {
        return $user->canEditClient($client);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $client): bool
    {
        return $user->isAdmin() || $client->created_by === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Client $client): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Client $client): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can manage updates for the client.
     */
    public function manageUpdates(User $user, Client $client): bool
    {
        return $user->canAccessClient($client);
    }

    /**
     * Determine whether the user can view chat for the client.
     */
    public function viewChat(User $user, Client $client): bool
    {
        return $user->canAccessClient($client);
    }

    /**
     * Determine whether the user can send chat messages for the client.
     */
    public function sendChatMessages(User $user, Client $client): bool
    {
        return $user->canAccessClient($client);
    }
}
