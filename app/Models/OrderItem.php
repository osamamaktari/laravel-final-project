<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'ticket_type_id',
        'quantity',
        'price',
    ];

    /**
     * Get the order that the order item belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the ticket type that the order item belongs to.
     */
    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }



    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class);
    }
}
