<?php

namespace App\Http\Controllers\Api\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    // List all events by this organizer
    public function index()
    {
        return Event::with(['category', 'organizer'])
            ->where('user_id', auth()->id())
            ->get();
    }

    // Create a new event
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:event_categories,id',
            'location' => 'required|string|max:255',
            'event_datetime' => 'required|date',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_refundable' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
        }

        $event = Event::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'category_id' => $request->category_id,
            'location' => $request->location,
            'event_datetime' => $request->event_datetime,
            'description' => $request->description,
            'price' => $request->price,
            'is_refundable' => $request->is_refundable,
            'image_path' => $imagePath,
        ]);

        return response()->json($event->load(['category', 'organizer']), 201);
    }

    // Show a single event
    public function show($id)
    {
        $event = Event::with(['category', 'organizer'])
            ->where('user_id', auth()->id())
            ->find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json($event);
    }

    // Update event
    public function update(Request $request, $id)
    {
        $event = Event::where('user_id', auth()->id())->find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:event_categories,id',
            'location' => 'required|string|max:255',
            'event_datetime' => 'required|date',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_refundable' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        // Replace image if new one provided
        if ($request->hasFile('image')) {
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            $event->image_path = $request->file('image')->store('events', 'public');
        }

        $event->update([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'location' => $request->location,
            'event_datetime' => $request->event_datetime,
            'description' => $request->description,
            'price' => $request->price,
            'is_refundable' => $request->is_refundable,
        ]);

        return response()->json($event->load(['category', 'organizer']));
    }

    // Delete event
    public function destroy($id)
    {
        $event = Event::where('user_id', auth()->id())->find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Optional: Delete image
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }

    // Optional: Organizer dashboard stats
    public function dashboardStats()
    {
        $userId = auth()->id();

        return response()->json([
            'total_events' => Event::where('user_id', $userId)->count(),
            'upcoming_events' => Event::where('user_id', $userId)
                ->where('event_datetime', '>', now())->count(),
            'total_earning' => Event::where('user_id', $userId)->sum('price'),
        ]);
    }
}
