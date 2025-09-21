<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Enums\TicketStatus;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //  Users
        $users = User::factory(10)->create();
        $organizers = $users->where('role', 'organizer')->values();
        $attendees = $users->where('role', 'attendee')->values();

        // Events
        $events = Event::factory(5)
            ->state(fn () => [
                'organizer_id' => $organizers->random()->id,
                'status' => EventStatus::PENDING,
            ])
            ->create();

        //  TicketTypes
        $ticketTypes = TicketType::factory(15)
            ->state(fn () => [
                'event_id' => $events->random()->id,
            ])
            ->create();

        //  Orders
        $orders = Order::factory(10)
            ->state(fn () => [
                'user_id' => $attendees->random()->id,
                'event_id' => $events->random()->id,
                'status' => OrderStatus::PENDING,
            ])
            ->create();

        //  OrderItems
        $orderItems = OrderItem::factory(20)
            ->state(fn () => [
                'order_id' => $orders->random()->id,
                'ticket_type_id' => $ticketTypes->random()->id,
            ])
            ->create();

        //
        //  Tickets
        $tickets = Ticket::factory(20)
            ->state(fn () => [
                'ticket_type_id' => $ticketTypes->random()->id,
                'attendee_id' => $attendees->random()->id,
                'status' => TicketStatus::VALID,
            ])
            ->create();
    }
}
