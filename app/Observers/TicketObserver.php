<?php

namespace App\Observers;

use App\Models\Ticket;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        $agent = $ticket->assignedTo;
        
        Notification::make()
            ->title('New Ticket')
            ->body("A new ticket has been assigned to you: {$ticket->title} {$ticket->description}")
            ->warning()
            ->sendToDatabase($agent);

        event(new DatabaseNotificationsSent($agent));
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        $admin = $ticket->assignedBy;
        
        if($ticket->status == 'Closed' && $ticket->assignedTo->id == Auth::user()->id) {
            Notification::make()
                ->title('Ticket Closed')
                ->body("The ticket '{$ticket->title}' has been closed by {$ticket->assignedTo->name}: {$ticket->comment}")
                ->warning()
                ->sendToDatabase($admin);

            event(new DatabaseNotificationsSent($admin));
        }

        $agent = $ticket->assignedTo;

        if($ticket->status == 'Open' && $ticket->assignedBy->id == Auth::user()->id) {
            Notification::make()
                ->title('Ticket Reopened')
                ->body("The ticket '{$ticket->title}' has been reopened by {$ticket->assignedBy->name}: {$ticket->comment}")
                ->warning()
                ->sendToDatabase($agent);

            event(new DatabaseNotificationsSent($agent));
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }
}
