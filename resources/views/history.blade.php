<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Signed URL History</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f7fb;
            font-family: Arial, sans-serif;
        }

        .header {
            background: #111827;
            color: white;
            padding: 25px;
        }

        .header-inner {
            max-width: 1200px;
            margin: auto;
        }

        .header a {
            color: white;
            margin-right: 20px;
            text-decoration: none;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,.08);
        }

        .filters {
            display: grid;
            grid-template-columns: 1fr 180px 120px;
            gap: 10px;
            margin-bottom: 25px;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
        }

        button {
            padding: 12px;
            border: none;
            border-radius: 7px;
            background: #2563eb;
            color: white;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f8fafc;
        }

        .url {
            max-width: 300px;
            word-break: break-all;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .active {
            background: #dcfce7;
            color: #166534;
        }

        .expired {
            background: #fee2e2;
            color: #991b1b;
        }

        .revoked {
            background: #fef3c7;
            color: #92400e;
        }

        .action {
            display: inline-block;
            padding: 7px 10px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            margin: 2px;
        }

        .danger {
            background: #dc2626;
        }

        .success-message {
            padding: 12px;
            background: #dcfce7;
            color: #166534;
            border-radius: 7px;
            margin-bottom: 15px;
        }

        .error-message {
            padding: 12px;
            background: #fee2e2;
            color: #991b1b;
            border-radius: 7px;
            margin-bottom: 15px;
        }

        .pagination {
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 8px 12px;
            margin: 2px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #2563eb;
        }

        @media(max-width: 800px) {

            .filters {
                grid-template-columns: 1fr;
            }

            .card {
                overflow-x: auto;
            }

            table {
                min-width: 1000px;
            }
        }
    </style>

</head>

<body>

<div class="header">

    <div class="header-inner">

        <h1>🔎 Signed URL History</h1>

        <a href="{{ route('home') }}">
            Generator
        </a>

        <a href="{{ route('signed-url.analytics') }}">
            Analytics
        </a>

    </div>

</div>

<div class="container">

    <div class="card">

        @if(session('success'))

            <div class="success-message">
                {{ session('success') }}
            </div>

        @endif

        @if(session('error'))

            <div class="error-message">
                {{ session('error') }}
            </div>

        @endif

        <form
            method="GET"
            action="{{ route('signed-url.history') }}"
            class="filters"
        >

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search URL or signature..."
            >

            <select name="status">

                <option value="">
                    All Statuses
                </option>

                <option
                    value="active"
                    {{ request('status') === 'active' ? 'selected' : '' }}
                >
                    Active
                </option>

                <option
                    value="expired"
                    {{ request('status') === 'expired' ? 'selected' : '' }}
                >
                    Expired
                </option>

                <option
                    value="revoked"
                    {{ request('status') === 'revoked' ? 'selected' : '' }}
                >
                    Revoked
                </option>

            </select>

            <button type="submit">
                Search
            </button>

        </form>

        <table>

            <thead>

            <tr>

                <th>ID</th>

                <th>Target URL</th>

                <th>Signature</th>

                <th>Status</th>

                <th>Accesses</th>

                <th>Expires</th>

                <th>Actions</th>

            </tr>

            </thead>

            <tbody>

            @forelse($signedUrls as $signedUrl)

                <tr>

                    <td>
                        #{{ $signedUrl->id }}
                    </td>

                    <td class="url">
                        {{ $signedUrl->target_url }}
                    </td>

                    <td class="url">
                        {{ $signedUrl->signature }}
                    </td>

                    <td>

                        @if($signedUrl->status === 'Active')

                            <span class="badge active">
                                Active
                            </span>

                        @elseif($signedUrl->status === 'Expired')

                            <span class="badge expired">
                                Expired
                            </span>

                        @else

                            <span class="badge revoked">
                                Revoked
                            </span>

                        @endif

                    </td>

                    <td>
                        {{ $signedUrl->access_count }}
                    </td>

                    <td>
                        {{ $signedUrl->expires_at->format('d M Y h:i A') }}
                    </td>

                    <td>

                        <a
                            class="action"
                            href="{{ route(
                                'signed-url.access-history',
                                $signedUrl
                            ) }}"
                        >
                            Access Logs
                        </a>

                        @if($signedUrl->status === 'Active')

                            <form
                                method="POST"
                                action="{{ route(
                                    'signed-url.revoke',
                                    $signedUrl
                                ) }}"
                                style="display:inline"
                                onsubmit="
                                    return confirm(
                                        'Revoke this signed URL?'
                                    );
                                "
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="action danger"
                                >
                                    Revoke
                                </button>

                            </form>

                        @endif

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7">
                        No signed URLs found.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

        <div class="pagination">

            {{ $signedUrls->links() }}

        </div>

    </div>

</div>

</body>

</html>