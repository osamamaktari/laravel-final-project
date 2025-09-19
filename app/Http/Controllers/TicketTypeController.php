<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketTypeRequest;
use App\Http\Requests\UpdateTicketTypeRequest;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\Request;

class TicketTypeController extends Controller
{
    public function store(StoreTicketTypeRequest $request, Event $event)
    {

        $this->authorize("update", $event);

        $ticketType = $event->ticketTypes()->create($request->validated());

        return response()->json($ticketType, 201);
    }

    public function update(UpdateTicketTypeRequest $request, TicketType $ticketType)
    {

        $this->authorize("update", $ticketType->event);

        $ticketType->update($request->validated());

        return response()->json($ticketType);
    }

    public function destroy(TicketType $ticketType)
    {

        $this->authorize("update", $ticketType->event);

        $ticketType->delete();

        return response()->json(null, 204);
    }
}
