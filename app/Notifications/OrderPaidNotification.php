<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPaidNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order)
    {
        //
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
            ->subject("Payment Successful for Order #{$this->order->id}!")
            ->greeting("Hello " . $notifiable->name . ",")
            ->line("Your payment for order #{$this->order->id} for event \'" . $this->order->event->title . "\' was successful.")
            ->line("You can now view and download your tickets.")
            ->action("View Your Tickets", "http://localhost:5173/my-tickets")
            ->line("Thank you for your purchase!");
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "order_id" => $this->order->id,
            "event_title" => $this->order->event->title,
            "total_amount" => $this->order->total_amount,
            "message" => "Your payment for order #{$this->order->id} for '{$this->order->event->title}' was successful.",
            "type" => "order_paid",
        ];
    }
}
