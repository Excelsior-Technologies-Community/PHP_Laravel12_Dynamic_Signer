<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Signed URL Access History</title>

    <style>
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

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,.08);
            margin-bottom: 20px;
        }

        .url {
            word-break: break-all;
            background: #f8fafc;
            padding: 12px;
            border-radius: 7px;
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

        .button {
            display: inline-block;
            padding: 9px 14px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
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
        }
    </style>

</head>

<body>

<div class="header">

    <h1>🛡️ Signed URL Access Monitoring</h1>

</div>

<div class="container">

    <div class="card">

        <a
            class="button"
            href="{{ route('signed-url.history') }}"
        >
            ← Back to History
        </a>

        <h2>
            Signed URL #{{ $signedUrl->id }}
        </h2>

        <p>
            <strong>Target URL:</strong>
        </p>

        <div class="url">
            {{ $signedUrl->target_url }}
        </div>

        <p>
            <strong>Total Accesses:</strong>
            {{ $signedUrl->access_count }}
        </p>

        <p>
            <strong>Last Accessed:</strong>

            {{ $signedUrl->last_accessed_at
                ? $signedUrl->last_accessed_at->format(
                    'd M Y h:i A'
                )
                : 'Never'
            }}
        </p>

    </div>

    <div class="card">

        <h2>Access Log</h2>

        <table>

            <thead>

            <tr>
                <th>#</th>
                <th>IP Address</th>
                <th>Accessed At</th>
                <th>User Agent</th>
            </tr>

            </thead>

            <tbody>

            @forelse($accesses as $access)

                <tr>

                    <td>
                        {{ $access->id }}
                    </td>

                    <td>
                        {{ $access->ip_address ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $access->accessed_at->format(
                            'd M Y h:i:s A'
                        ) }}
                    </td>

                    <td>
                        {{ $access->user_agent ?? 'N/A' }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="4">
                        No successful access recorded.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

        <div class="pagination">

            {{ $accesses->links() }}

        </div>

    </div>

</div>

</body>

</html>