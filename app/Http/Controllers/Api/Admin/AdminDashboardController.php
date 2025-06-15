<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;  // <--- Add this line


class AdminDashboardController extends Controller
{
    public function userStats()
    {
        $total = User::count();
        $attendees = User::where('role', 'attendee')->count();
        $organizers = User::where('role', 'organizer')->count();
        $admins = User::where('role', 'admin')->count();

        return response()->json([
            'total' => $total,
            'attendee' => $attendees,
            'organizer' => $organizers,
            'admin' => $admins,
        ]);
    }
}
