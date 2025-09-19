<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
class TicketController extends Controller
{
     public function userTickets()
    {
        $this->authorize("isAttendee"); 

        $tickets = auth()->user()->tickets()->with("ticketType.event")->orderBy("created_at", "desc")->get();

        return response()->json($tickets);
    }
    public function show(Ticket $ticket)
    {
        $this->authorize("view", $ticket);

        return response()->json($ticket->load("ticketType.event", "attendee"));
    }

    public function download(Ticket $ticket)
    {
        $this->authorize("view", $ticket);


        $qrCodeContent = json_encode([
            "ticket_id" => $ticket->id,
            "order_id" => $ticket->order->id,
            "user_id" => $ticket->attendee_id,
        ]);
        $qrCodeImage = base64_decode($ticket->qr_code);

        $data = [
            "ticket" => $ticket->load("ticketType.event", "attendee"),
            "qrCodeImage" => base64_encode($qrCodeImage),
        ];

        $pdf = Pdf::loadView("pdf.ticket", $data);

        return $pdf->download("ticket-" . $ticket->id . ".pdf");
    }


    public function validateTicket(Request $request, Ticket $ticket)
    {
        $this->authorize("validate", $ticket);

        if ($ticket->status === \App\Enums\TicketStatus::USED) {
            return response()->json(["message" => "Ticket already used.", "ticket" => $ticket], 409);
        }

        if ($ticket->status === \App\Enums\TicketStatus::CANCELLED) {
            return response()->json(["message" => "Ticket cancelled.", "ticket" => $ticket], 409);
        }

        $ticket->update(["status" => \App\Enums\TicketStatus::USED]);

        return response()->json(["message" => "Ticket validated successfully.", "ticket" => $ticket]);
    }
}
