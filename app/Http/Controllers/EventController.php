<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::with("organizer", "ticketTypes")
            ->when($request->has("search"), function ($query) use ($request) {
                $query->where("title", "like", "%" . $request->search . "%")
                    ->orWhere("description", "like", "%" . $request->search . "%");
            })
            ->when($request->has("status"), function ($query) use ($request) {
                $query->where("status", $request->status);
            })
            ->orderBy("start_date", "asc")
            ->paginate(10);

        return response()->json($events);
    }

    public function show(Event $event)
    {
        $event->load("organizer", "ticketTypes");
        return response()->json($event);
    }

    public function store(StoreEventRequest $request)
    {
        $this->authorize("create", Event::class);

        $data = $request->validated();

        if ($request->hasFile("banner")) {
            $file = $request->file("banner");
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/banners', $fileName);
            $data['banner_url'] = Storage::url('banners/' . $fileName);
        }

        $event = auth()->user()->events()->create($data);

        return response()->json($event, 201);
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $this->authorize("update", $event);

        $data = $request->validated();

        if ($request->hasFile("banner")) {
       
            if ($event->banner_url) {
                $oldFile = str_replace('/storage/', 'public/', $event->banner_url);
                Storage::delete($oldFile);
            }

            $file = $request->file("banner");
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/banners', $fileName);
            $data['banner_url'] = Storage::url('banners/' . $fileName);
        }

        $event->update($data);

        return response()->json($event);
    }

    public function destroy(Event $event)
    {
        $this->authorize("delete", $event);

        if ($event->banner_url) {
            $oldFile = str_replace('/storage/', 'public/', $event->banner_url);
            Storage::delete($oldFile);
        }

        $event->delete();

        return response()->json(null, 204);
    }

    public function organizerEvents()
    {
        $this->authorize("isOrganizerOrAdmin");

        $events = auth()->user()->events()->with("ticketTypes")->orderBy("start_date", "asc")->get();

        return response()->json(['data' => $events]);
    }

    public function approveReject(Request $request, Event $event)
    {
        $this->authorize("approveReject", $event);

        $request->validate([
            "status" => ["required", "in:" . EventStatus::APPROVED->value . "," . EventStatus::REJECTED->value],
        ]);

        $event->update(["status" => $request->status]);

        return response()->json($event);
    }
}
