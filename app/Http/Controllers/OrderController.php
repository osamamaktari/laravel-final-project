<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $this->authorize("isAttendee");

        $request->validate([
            "ticket_types" => "required|array",
            "ticket_types.*.ticket_type_id" => "required|exists:ticket_types,id",
            "ticket_types.*.quantity" => "required|integer|min:1",
        ]);

        return DB::transaction(function () use ($request, $event) {
            $totalAmount = 0;
            $orderItemsData = [];
            $ticketsToCreate = [];

            foreach ($request->ticket_types as $item) {
                $ticketType = TicketType::findOrFail($item["ticket_type_id"]);

                if ($ticketType->event_id !== $event->id) {
                    throw ValidationException::withMessages([
                        "ticket_types" => ["Ticket type does not belong to this event."],
                    ]);
                }

                if ($ticketType->quantity - $ticketType->sold < $item["quantity"]) {
                    throw ValidationException::withMessages([
                        "ticket_types" => ["Not enough tickets available for " . $ticketType->name],
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
                $qrCodeContent = json_encode([
                    "ticket_id" => $ticket->id,
                    "order_id" => $order->id,
                    "user_id" => auth()->id(),
                ]);
                $ticket->qr_code = base64_encode(QrCode::format("png")->size(200)->generate($qrCodeContent));
                $ticket->save();
            }



            return response()->json($order->load("orderItems.ticketType.event"), 201);
        });
    }

    public function show(Order $order)
    {
        $this->authorize("view", $order);

        return response()->json($order->load("orderItems.ticketType.event"));
    }

    public function pay(Request $request, Order $order)
    {
        $this->authorize("pay", $order);

        $request->validate([
            "payment_method_id" => "required|string",
        ]);

        // هنا يتم دمج بوابة الدفع (Stripe, PayPal, إلخ.)
        // هذا مجرد محاكاة بسيطة
        try {
            // Simulate payment processing
            // $paymentIntent = Stripe::paymentIntents()->confirm($request->payment_method_id, [...]);
            // if ($paymentIntent->status === 'succeeded') {
            $order->update([
                "status" => OrderStatus::PAID,
                "payment_intent_id" => $request->payment_method_id, // أو الـ ID الحقيقي من بوابة الدفع
            ]);

            // إرسال إشعار للمستخدم (سنقوم بتطبيقه لاحقاً)
            // auth()->user()->notify(new OrderPaidNotification($order));

            return response()->json(["message" => "Payment successful", "order" => $order]);
            // }
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                "payment" => ["Payment failed: " . $e->getMessage()],
            ]);
        }
    }

    public function userOrders()
    {
        $this->authorize("isAttendee");
        $orders = auth()->user()->orders()->with("orderItems.ticketType.event")->orderBy("created_at", "desc")->get();

        return response()->json($orders);
    }
}
