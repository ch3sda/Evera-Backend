<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizerRequest;

class OrganizerRequestController extends Controller
{
    public function index()
    {
        $requests = OrganizerRequest::with('user')
            ->whereHas('user') // Ensures only requests with existing users
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($requests);
    }
}
