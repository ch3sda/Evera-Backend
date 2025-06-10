@extends('layouts.app')  {{-- or your admin layout --}}

@section('content')
<div class="container my-4">
    <h1 class="mb-4">Admin Dashboard</h1>

    <div class="row">
        {{-- Total Users --}}
        <div class="col-md-3">
            <div class="card bg-primary text-white text-center p-3">
                <h5>Total Users</h5>
                <h3 id="totalUsers">{{ $totalUsers }}</h3>
            </div>
        </div>

        {{-- Total Organizers --}}
        <div class="col-md-3">
            <div class="card bg-success text-white text-center p-3">
                <h5>Total Organizers</h5>
                <h3 id="totalOrganizers">{{ $totalOrganizers }}</h3>
            </div>
        </div>

        {{-- Total Events --}}
        <div class="col-md-3">
            <div class="card bg-warning text-white text-center p-3">
                <h5>Total Events</h5>
                <h3 id="totalEvents">{{ $totalEvents }}</h3>
            </div>
        </div>

        {{-- Total Reported Events --}}
        <div class="col-md-3">
            <div class="card bg-danger text-white text-center p-3">
                <h5>Total Reported Events</h5>
                <h3 id="totalReportedEvents">{{ $totalReportedEvents }}</h3>
            </div>
        </div>
    </div>
</div>

<script>
    // Optional: auto-refresh dashboard stats every 30 seconds via AJAX
    async function fetchStats() {
        try {
            const response = await fetch('{{ route("admin.stats") }}');
            if (!response.ok) throw new Error('Network error');

            const data = await response.json();

            document.getElementById('totalUsers').textContent = data.total_users;
            document.getElementById('totalOrganizers').textContent = data.total_organizers;
            document.getElementById('totalEvents').textContent = data.total_events;
            document.getElementById('totalReportedEvents').textContent = data.total_reported_events;
        } catch (error) {
            console.error('Error fetching stats:', error);
        }
    }

    // Fetch stats every 30 seconds
    setInterval(fetchStats, 30000);
</script>
@endsection
