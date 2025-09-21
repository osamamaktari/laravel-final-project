<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
    $ticket->load('order', 'ticketType.event', 'attendee');
    $this->authorize("view", $ticket);

    $qrCodeImage = $ticket->qr_code;


    $html = "
    <html>
    <head>
        <style>
            body { font-family: DejaVu Sans, sans-serif; }
            .ticket {
                width: 500px;
                border: 2px solid #333;
                border-radius: 10px;
                padding: 20px;
                margin: 0 auto;
            }
            h2 { text-align: center; margin-bottom: 10px; }
            p { margin: 5px 0; font-size: 14px; }
            .qr { text-align: center; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='ticket'>
            <h2>{$ticket->ticketType->name} Ticket</h2>
            <p><strong>Event:</strong> {$ticket->ticketType->event->title}</p>
            <p><strong>Price:</strong> \${$ticket->ticketType->price}</p>
            <p><strong>Quantity:</strong> {$ticket->ticketType->quantity}</p>
            <p><strong>Attendee:</strong> {$ticket->attendee->name}</p>
            <div class='qr'>
                <img src='data:image/png;base64,{$qrCodeImage}' alt='QR Code' width='150' height='150'>
            </div>
        </div>
    </body>
    </html>
    ";

    $pdf = Pdf::loadHTML($html);
    return $pdf->download("ticket-{$ticket->id}.pdf");
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
