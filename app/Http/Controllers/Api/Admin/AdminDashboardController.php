<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;  // <--- Add this line


class AdminDashboardController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_users' => User::where('role', 'user')->count(),
            'total_organizers' => User::where('role', 'organizer')->count(),
            'total_events' => Event::count(),
            'total_reported_events' => 0 // Change this if you add an EventReport model
        ]);
    }
}
