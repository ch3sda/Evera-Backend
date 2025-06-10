<!DOCTYPE html>
<html>
<head>
    <title>Send Notification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">

    <h2 class="mb-4">Send Notification to Attendees</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('events.notify') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="event_id" class="form-label">Event ID</label>
            <input type="number" name="event_id" id="event_id" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Notification Message</label>
            <textarea name="message" id="message" rows="4" class="form-control" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Send Notification</button>
    </form>

</body>
</html>