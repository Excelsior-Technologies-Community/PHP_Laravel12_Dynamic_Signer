<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Signed URL Analytics</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #222;
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
            text-decoration: none;
            margin-right: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .stats {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
        }

        .stat {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,.08);
        }

        .stat h3 {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .stat strong {
            display: block;
            font-size: 32px;
            margin-top: 10px;
        }

        .section {
            background: white;
            margin-top: 25px;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,.08);
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
            max-width: 350px;
            word-break: break-all;
        }

        .badge {
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

        .limit {
            background: #ede9fe;
            color: #6d28d9;
        }

        .used {
            background: #e0f2fe;
            color: #0369a1;
        }

        .button {
            display: inline-block;
            padding: 8px 12px;
            background: #2563eb;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
        }

        @media(max-width: 700px) {

            .section {
                overflow-x: auto;
            }

            table {
                min-width: 900px;
            }
        }

    </style>

</head>

<body>

<div class="header">

    <div class="header-inner">

        <h1>
            📊 Signed URL Analytics Dashboard
        </h1>

        <a href="{{ route('home') }}">
            Generator
        </a>

        <a href="{{ route('signed-url.history') }}">
            URL History
        </a>

    </div>

</div>

<div class="container">

    <div class="stats">

        <div class="stat">
            <h3>Total Signed URLs</h3>
            <strong>{{ $totalUrls }}</strong>
        </div>

        <div class="stat">
            <h3>Active URLs</h3>
            <strong>{{ $activeUrls }}</strong>
        </div>

        <div class="stat">
            <h3>Expired URLs</h3>
            <strong>{{ $expiredUrls }}</strong>
        </div>

        <div class="stat">
            <h3>Revoked URLs</h3>
            <strong>{{ $revokedUrls }}</strong>
        </div>

        <div class="stat">
            <h3>Limit Reached</h3>
            <strong>{{ $limitReachedUrls }}</strong>
        </div>

        <div class="stat">
            <h3>One-Time URLs</h3>
            <strong>{{ $oneTimeUrls }}</strong>
        </div>

        <div class="stat">
            <h3>Used One-Time</h3>
            <strong>{{ $usedOneTimeUrls }}</strong>
        </div>

        <div class="stat">
            <h3>Total Accesses</h3>
            <strong>{{ $totalAccesses }}</strong>
        </div>

        <div class="stat">
            <h3>Today's Accesses</h3>
            <strong>{{ $todayAccesses }}</strong>
        </div>

    </div>

    <div class="section">

        <h2>
            Recent Signed URLs
        </h2>

        <table>

            <thead>

            <tr>

                <th>ID</th>

                <th>Name</th>

                <th>Target URL</th>

                <th>Status</th>

                <th>Accesses</th>

                <th>Expires</th>

            </tr>

            </thead>

            <tbody>

            @forelse($recentUrls as $url)

                <tr>

                    <td>
                        #{{ $url->id }}
                    </td>

                    <td>
                        {{ $url->name ?? 'Untitled' }}
                    </td>

                    <td class="url">
                        {{ $url->target_url }}
                    </td>

                    <td>

                        <span
                            class="badge {{ $url->status_class }}"
                        >
                            {{ $url->status }}
                        </span>

                    </td>

                    <td>

                        {{ $url->access_count }}

                        /

                        {{ $url->max_accesses ?? '∞' }}

                    </td>

                    <td>
                        {{ $url->expires_at->format(
                            'd M Y h:i A'
                        ) }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6">
                        No signed URLs generated yet.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    <div class="section">

        <h2>
            Recent Access Activity
        </h2>

        <table>

            <thead>

            <tr>

                <th>URL ID</th>

                <th>IP Address</th>

                <th>Accessed At</th>

                <th>User Agent</th>

            </tr>

            </thead>

            <tbody>

            @forelse($recentAccesses as $access)

                <tr>

                    <td>
                        #{{ $access->signed_url_id }}
                    </td>

                    <td>
                        {{ $access->ip_address ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $access->accessed_at->format(
                            'd M Y h:i A'
                        ) }}
                    </td>

                    <td class="url">
                        {{ $access->user_agent ?? 'N/A' }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="4">
                        No access activity yet.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

</body>

</html>
