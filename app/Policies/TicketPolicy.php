<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {

        return $user->id === $ticket->attendee_id
            || ($user->role === 'organizer' && $user->id === $ticket->ticketType->event->organizer_id)
            || $user->role === 'admin';
    }

    /**
     * Determine whether the user can validate the ticket.
     */
    public function validate(User $user, Ticket $ticket): bool
    {
        return ($user->role === 'organizer' && $user->id === $ticket->ticketType->event->organizer_id)
            || $user->role === 'admin';
    }
}
