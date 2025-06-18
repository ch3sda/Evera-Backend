<?php

namespace App\Http\Controllers\Api\Organizer;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class OrganizerEventCategoryController extends Controller
{
    public function index()
    {
        return response()->json(EventCategory::select('id', 'name')->get());
    }
}
