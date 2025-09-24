<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $this->authorize("isAttendee");

        $request->validate([
            "items" => "required|array",
            "items.*.ticket_type_id" => "required|exists:ticket_types,id",
            "items.*.quantity" => "required|integer|min:1",
        ]);

        return DB::transaction(function () use ($request, $event) {
            $totalAmount = 0;
            $orderItemsData = [];
            $ticketsToCreate = [];

            foreach ($request->items as $item) {
                $ticketType = TicketType::findOrFail($item["ticket_type_id"]);

                if ($ticketType->event_id !== $event->id) {
                    throw ValidationException::withMessages([
                        "items" => ["Ticket type does not belong to this event."],
                    ]);
                }

                if ($ticketType->quantity - $ticketType->sold < $item["quantity"]) {
                    throw ValidationException::withMessages([
                        "items" => ["Not enough tickets available for " . $ticketType->name],
                    ]);
                }

                $totalAmount += $ticketType->price * $item["quantity"];
                $orderItemsData[] = [
                    "ticket_type_id" => $ticketType->id,
                    "quantity" => $item["quantity"],
                    "price" => $ticketType->price,
                ];

                $ticketType->increment("sold", $item["quantity"]);

                for ($i = 0; $i < $item["quantity"]; $i++) {
                    $ticketsToCreate[] = [
                        "ticket_type_id" => $ticketType->id,
                        "attendee_id" => auth()->id(),
                        "qr_code" => "",
                        "status" => "valid",
                    ];
                }
            }

            $order = auth()->user()->orders()->create([
                "event_id" => $event->id,
                "total_amount" => $totalAmount,
                "status" => OrderStatus::PENDING,
            ]);

            $order->orderItems()->createMany($orderItemsData);

            foreach ($ticketsToCreate as &$ticketData) {
                $ticket = $order->tickets()->create($ticketData);
                $ticket->qr_code = json_encode([
                    "ticket_id" => $ticket->id,
                    "order_id" => $order->id,
                    "user_id" => auth()->id(),
                ]);
                $ticket->save();
            }

            Log::info("📩 [Controller] قبل إرسال OrderCreatedNotification للطلب #{$order->id}");

            auth()->user()->notify(new OrderCreatedNotification($order));

            Log::info("📩 [Controller] بعد إرسال OrderCreatedNotification للطلب #{$order->id}");

            return response()->json($order->load("orderItems.ticketType.event"), 201);
        });
    }
}
