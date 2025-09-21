<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\OrderStatus;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'total_amount',
        'status',
        'payment_intent_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class, //  Enum OrderStatus
        ];
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the event associated with the order (through order items).
     */
    public function event(): BelongsTo
    {

        return $this->belongsTo(Event::class, 'event_id');

    }
    public function tickets(): HasMany
{
    return $this->hasMany(Ticket::class);
}
}
