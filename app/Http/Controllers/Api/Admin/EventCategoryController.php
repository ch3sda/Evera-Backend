<?php

// app/Http/Controllers/Api/Admin/EventCategoryController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventCategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = EventCategory::query()->withCount([
                'events as total_used',
                'events as currently_used' => function ($q) {
                    $q->whereNull('deleted_at');
                }
            ]);

            if ($search = $request->query('search')) {
                $query->where('name', 'like', "%$search%");
            }

            return response()->json($query->get());
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category = EventCategory::create(['name' => $request->name]);
        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = EventCategory::find($id);
        if (!$category) return response()->json(['message' => 'Not Found'], 404);

        if ($category->events()->exists()) {
            return response()->json(['message' => 'Cannot update category currently in use.'], 403);
        }

        $request->validate(['name' => 'required|string|max:255']);
        $category->update(['name' => $request->name]);

        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = EventCategory::find($id);
        if (!$category) return response()->json(['message' => 'Not Found'], 404);

        if ($category->events()->exists()) {
            return response()->json(['message' => 'Cannot delete category currently in use.'], 403);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}
