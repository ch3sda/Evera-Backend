<?php

namespace App\Http\Controllers\Api\Attendee;

use App\Http\Controllers\Controller;
use App\Models\Event;

class EventViewController extends Controller
{
    // List all non-deleted events with necessary details for attendees
    public function index()
    {
        $events = Event::with('category')
            ->whereNull('deleted_at') // exclude soft deleted
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'category' => $event->category ? $event->category->name : null,
                    'title' => $event->title,
                    'description' => $event->description,
                    'image_url' => $event->image_path ? \Storage::disk('r2')->url($event->image_path) : null,
                    'price' => $event->price,
                    'event_datetime' => $event->event_datetime->toDateTimeString(),
                    'location' => $event->location,
                ];
            });

        return response()->json($events);
    }

    // Optional: get details of a single event by id
    public function show($id)
    {
        $event = Event::with('category')
            ->whereNull('deleted_at')
            ->find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json([
            'id' => $event->id,
            'category' => $event->category ? $event->category->name : null,
            'title' => $event->title,
            'description' => $event->description,
            'image_url' => $event->image_path ? \Storage::disk('r2')->url($event->image_path) : null,
            'price' => $event->price,
            'event_datetime' => $event->event_datetime->toDateTimeString(),
            'location' => $event->location,
        ]);
    }
}
