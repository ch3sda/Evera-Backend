<?php

// app/Http/Controllers/Api/EventCategoryController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventCategoryController extends Controller
{
    public function index()
    {
        return response()->json(EventCategory::all());
    }

    public function store(Request $request)
    {   
        $request->validate(['name' => 'required|string|max:255']);
        $category = EventCategory::create(['name' => $request->name]);
        return response()->json($category, 201);
    }

    public function show($id)
    {
        $category = EventCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = EventCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->name = $request->name;
        $category->save();

        return response()->json($category, 200);
}


    public function destroy($id)
    {
        $category = EventCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted'], 200);
    }

}
