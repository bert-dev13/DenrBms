<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Print</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #111827; }
        h1 { margin: 0 0 8px; font-size: 22px; }
        .meta { margin-bottom: 16px; color: #4b5563; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; font-size: 12px; }
        th { background: #f3f4f6; font-weight: 700; }
    </style>
</head>
<body>
    <h1>User Management Report</h1>
    <div class="meta">
        Generated: {{ $printedAt->format('F d, Y h:i A') }} | Total records: {{ $users->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Username</th>
                <th>Role</th>
                <th>Protected Area</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ '@'.$user->username }}</td>
                    <td>{{ $user->role === 'admin' ? 'Administrator' : 'Protected Area User' }}</td>
                    <td>{{ $user->protectedArea?->name ?? '—' }}</td>
                    <td>{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
