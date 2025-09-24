<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OrderCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order)
    {
        // تحميل العلاقات المطلوبة
        $this->order->loadMissing(['event', 'user']);

        Log::info("📩 [Notification] تم إنشاء OrderCreatedNotification للطلب #{$this->order->id}");
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        Log::info("📩 [Notification] via() استُدعيت للمستخدم ID={$notifiable->id}");

        // للتجربة نرسل على القناتين دايماً
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        Log::info("📩 [Notification] toMail() استُدعيت للمستخدم ID={$notifiable->id}");

        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');

        return (new MailMessage)
            ->subject("تم إنشاء طلبك رقم #{$this->order->id} بنجاح!")
            ->greeting("مرحباً {$notifiable->name},")
            ->line("تم إنشاء طلبك للفعالية '" . ($this->order->event ? $this->order->event->title : 'غير معروف') . "' بنجاح.")
            ->line("إجمالي الطلب: $" . number_format($this->order->total_amount, 2))
            ->line("حالة الطلب: " . ($this->order->status?->value ?? 'غير معروف'))
            ->action("عرض تذاكرك", $frontendUrl . "/my-tickets")
            ->line("شكراً لك على الشراء!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        Log::info("📩 [Notification] toArray() استُدعيت للمستخدم ID={$notifiable->id}");

        return [
            "order_id"     => $this->order->id,
            "event_title"  => isset($this->order->event) && $this->order->event ? $this->order->event->title : 'Unknown Event',
            "total_amount" => $this->order->total_amount,
            "status"       => isset($this->order->status) && $this->order->status ? $this->order->status->value : 'غير معروف',
            "message"      => "تم إنشاء طلبك رقم #{$this->order->id} للفعالية '" . (isset($this->order->event) && $this->order->event ? $this->order->event->title : 'غير معروف') . "' بنجاح.",
            "type"         => "order_created",
            "action_url"   => "/my-tickets",
            "created_at"   => now()->toISOString(),
        ];
    }
}
