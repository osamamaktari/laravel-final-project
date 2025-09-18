<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'title',
        'description',
        'venue',
        'start_date',
        'end_date',
        'banner_url',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'status' => EventStatus::class, //  Enum EventStatus later will create
        ];
    }

    /**
     * Get the organizer that owns the event.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Get the ticket types for the event.
     */
    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    /**
     * Get the orders for the event.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
