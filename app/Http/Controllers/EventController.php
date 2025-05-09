<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Show a list of all events with their categories and organizers
    public function index()
    {
        return Event::with(['category', 'organizer'])->get();
    }

    // Store a new event
    public function store(Request $request)
    {
        // Validation rules
        $request->validate([
            'title' => 'required',
            'category_id' => 'required|exists:event_categories,id', // Make sure category exists
            'location' => 'required',
            'event_datetime' => 'required|date',
            'description' => 'nullable',
        ]);

        // Create the event
        $event = Event::create([
            'user_id' => 1, // Use the authenticated user's ID as the organizer
            'title' => $request->title,
            'category_id' => $request->category_id,
            'location' => $request->location,
            'event_datetime' => $request->event_datetime,
            'description' => $request->description,
        ]);

        // Return the newly created event
        return response()->json($event, 201);
    }

    // Show details of a specific event
    public function show($id)
    {
        // Find event with the associated category and organizer
        $event = Event::with(['category', 'organizer'])->find($id);
        
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json($event);
    }

    // Update an event
    public function update(Request $request, $id)
    {
        // Find the event to update
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Validation rules
        $request->validate([
            'title' => 'required',
            'category_id' => 'required|exists:event_categories,id', // Validate the category ID
            'location' => 'required',
            'event_datetime' => 'required|date',
            'description' => 'nullable',
        ]);

        // Update the event with the new data
        $event->update($request->all());

        // Return the updated event
        return response()->json($event);
    }

    // Delete an event
    public function destroy($id)
    {
        // Find the event to delete
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Delete the event
        $event->delete();

        // Return success message
        return response()->json(['message' => 'Event deleted successfully'], 200);
    }
}
