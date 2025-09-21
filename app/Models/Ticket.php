<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\TicketStatus;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_type_id',
        'attendee_id',
        'qr_code',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class, //  Enum TicketStatus
        ];
    }

    /**
     * Get the ticket type that the ticket belongs to.
     */
    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    /**
     * Get the attendee (user) that owns the ticket.
     */
    public function attendee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attendee_id');
    }


    public function order():BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

}
