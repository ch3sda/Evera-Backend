<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Report;
use Illuminate\Http\Request;

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
    public function index()
    {
        $totalUsers = User::where('role', 'user')->count();

        $totalOrganizers = User::where('role', 'organizer')->count();
        $totalEvents = Event::count();
        $totalReportedEvents = 0;
        return view('admin.dashboard', compact('totalUsers', 'totalOrganizers', 'totalEvents', 'totalReportedEvents'));
    }
}
