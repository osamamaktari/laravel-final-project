<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Event $event, public string $oldStatus)
    {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ["mail", "database"];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Event '{$this->event->title}' Status Updated to " . ucfirst($this->event->status->value) . "!")
            ->greeting("Hello " . $notifiable->name . ",")
            ->line("The status of your event '{$this->event->title}' has been updated from '" . ucfirst($this->oldStatus) . "' to '" . ucfirst($this->event->status->value) . ".")
            ->action("View Event", "http://localhost:5173/event-management/" . $this->event->id)
            ->line("Thank you for using our service!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "event_id" => $this->event->id,
            "event_title" => $this->event->title,
            "old_status" => $this->oldStatus,
            "new_status" => $this->event->status->value,
            "message" => "Event '{$this->event->title}' status updated to '{$this->event->status->value}'.",
            "type" => "event_status_updated",
        ];
    }
}
