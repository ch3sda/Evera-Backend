<?php

namespace App\Http\Controllers\Api\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class EventController extends Controller
{
    // Helper to get public URL from R2_PUBLIC_URL + path
    protected function getPublicR2Url(?string $imagePath): ?string
    {
        if (!$imagePath) return null;

        $baseUrl = rtrim(config('filesystems.disks.r2.url'), '/');
        return $baseUrl . '/' . ltrim($imagePath, '/');
    }


    // List all events for the authenticated organizer (exclude soft deleted)
    public function index()
    {
        try {
            $userId = auth()->id();

            if (!$userId) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $events = Event::with(['category', 'organizer'])
                ->where('user_id', $userId)
                ->get();

            // Append full public URL for each event's image
            $events->each(function ($event) {
                $event->image_url = $this->getPublicR2Url($event->image_path);
            });

            return response()->json($events);

        } catch (\Exception $e) {
            Log::error('EventController@index error: ' . $e->getMessage());
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    // Store a new event
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:event_categories,id',
            'location' => 'required|string|max:255',
            'event_datetime' => 'required|date|after:now',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_refundable' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $imageKey = null;

        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                $imageKey = 'events/' . uniqid() . '.' . $image->getClientOriginalExtension();

                Log::info('Uploading image to R2 with key: ' . $imageKey);
                Storage::disk('r2')->put($imageKey, $image->get(), [
                    'visibility' => 'public'
                ]);
                Log::info('Image upload successful.');
            } catch (\Exception $e) {
                Log::error('R2 upload error: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Failed to upload image',
                    'error' => $e->getMessage(),
                ], 500);
            }
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
            'image_path' => $imageKey,
        ]);

    $event->image_url = $this->getPublicR2Url($event->image_path);

        return response()->json($event->load(['category', 'organizer']), 201);
    }

    // Update an event
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
            'event_datetime' => 'required|date|after:now',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_refundable' => 'required|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($event->image_path) {
                try {
                    Log::info("Deleting old R2 image: {$event->image_path}");
                    Storage::disk('r2')->delete($event->image_path);
                } catch (\Exception $e) {
                    Log::warning("Failed to delete old R2 image: {$event->image_path} - {$e->getMessage()}");
                }
            }

            $image = $request->file('image');
            $imageKey = 'events/' . uniqid() . '.' . $image->getClientOriginalExtension();
            Storage::disk('r2')->put($imageKey, $image->get(), [
                'visibility' => 'public'
            ]);
            $event->image_path = $imageKey;
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

        $event->image_url = $this->getPublicR2Url($event->image_path);

        return response()->json($event->load(['category', 'organizer']));
    }

    // Soft delete an event and delete image from R2
    public function destroy($id)
    {
        $event = Event::where('user_id', auth()->id())->find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        if ($event->image_path) {
            try {
                Log::info("Deleting R2 image: {$event->image_path}");
                Storage::disk('r2')->delete($event->image_path);
            } catch (\Exception $e) {
                Log::warning("Failed to delete R2 image: {$event->image_path} - {$e->getMessage()}");
            }
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }

}
