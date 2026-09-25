<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Access History - Signed URL</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .container {
            max-width: 1250px;
            margin: auto;
        }

        .header {
            background: linear-gradient(
                135deg,
                #111827,
                #1e3a8a
            );

            color: white;
            padding: 30px;
            border-radius: 18px;
            margin-bottom: 25px;

            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .header h1 {
            margin: 0 0 10px;
            font-size: 30px;
        }

        .header p {
            margin: 0;
            opacity: 0.85;
            line-height: 1.6;
        }

        .actions {
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            border: none;
            cursor: pointer;

            padding: 11px 18px;
            border-radius: 9px;

            font-size: 14px;
            font-weight: bold;

            margin-right: 8px;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: white;
            color: #1f2937;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        /*
        |--------------------------------------------------------------------------
        | URL Information
        |--------------------------------------------------------------------------
        */

        .url-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;

            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
        }

        .url-card h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }

        .url-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #e5e7eb;

            padding: 15px;
            border-radius: 10px;
        }

        .info-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 6px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .info-value {
            font-size: 15px;
            font-weight: 600;
            word-break: break-word;
        }

        .url-value {
            color: #2563eb;
        }

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 22px;

            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
        }

        .stat-title {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #111827;
        }

        /*
        |--------------------------------------------------------------------------
        | Table
        |--------------------------------------------------------------------------
        */

        .table-card {
            background: white;
            border-radius: 16px;
            padding: 25px;

            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);

            overflow-x: auto;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 15px;

            margin-bottom: 20px;
        }

        .table-header h2 {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        th {
            background: #f8fafc;
            color: #374151;

            padding: 14px 12px;

            text-align: left;

            font-size: 13px;
            border-bottom: 2px solid #e5e7eb;
        }

        td {
            padding: 14px 12px;

            border-bottom: 1px solid #e5e7eb;

            font-size: 14px;
            vertical-align: top;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .ip-address {
            font-family: Consolas, monospace;
            background: #eef2ff;
            color: #3730a3;

            padding: 5px 8px;
            border-radius: 6px;

            display: inline-block;
        }

        .user-agent {
            max-width: 350px;
            word-break: break-word;
            line-height: 1.5;
            color: #4b5563;
        }

        .date {
            white-space: nowrap;
            color: #374151;
        }

        /*
        |--------------------------------------------------------------------------
        | Empty State
        |--------------------------------------------------------------------------
        */

        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #6b7280;
        }

        .empty-icon {
            font-size: 45px;
            margin-bottom: 12px;
        }

        .empty h3 {
            margin: 0 0 8px;
            color: #374151;
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination - NUMBERS ONLY
        |--------------------------------------------------------------------------
        */

        .pagination-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;

            margin-top: 25px;
        }

        .pagination {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 7px;
        }

        .pagination a,
        .pagination span {
            width: 40px;
            height: 40px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 8px;

            text-decoration: none;

            font-size: 14px;
            font-weight: bold;
        }

        .pagination a {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .pagination a:hover {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .pagination .active {
            background: #2563eb;
            color: white;
            border: 1px solid #2563eb;
        }

        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 900px) {

            body {
                padding: 15px;
            }

            .url-grid {
                grid-template-columns: 1fr;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    {{-- ==========================================================
         HEADER
    =========================================================== --}}

    <div class="header">

        <h1>Access History</h1>

        <p>
            View every access attempt for this signed URL,
            including IP address, browser information and access time.
        </p>

        <div class="actions">

            <a
                href="{{ route('signed-url.history') }}"
                class="btn btn-secondary"
            >
                ← Back to URL History
            </a>

            <a
                href="{{ route('signed-url.analytics') }}"
                class="btn btn-primary"
            >
                Analytics
            </a>

        </div>

    </div>


    {{-- ==========================================================
         SIGNED URL INFORMATION
    =========================================================== --}}

    <div class="url-card">

        <h2>Signed URL Information</h2>

        <div class="url-grid">

            <div class="info-box">

                <div class="info-label">
                    URL ID
                </div>

                <div class="info-value">
                    #{{ $signedUrl->id }}
                </div>

            </div>


            <div class="info-box">

                <div class="info-label">
                    URL Name / Title
                </div>

                <div class="info-value">

                    {{ $signedUrl->name ?: 'Untitled' }}

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">
                    Target URL
                </div>

                <div class="info-value url-value">

                    {{ $signedUrl->target_url }}

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">
                    Signature
                </div>

                <div class="info-value">

                    {{ $signedUrl->signature }}

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">
                    Status
                </div>

                <div class="info-value">

                    {{ $signedUrl->status }}

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">
                    Expires At
                </div>

                <div class="info-value">

                    {{ $signedUrl->expires_at?->format('d M Y, h:i A') }}

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">
                    Access Limit
                </div>

                <div class="info-value">

                    @if($signedUrl->one_time)

                        1 access (One-Time)

                    @elseif(!is_null($signedUrl->max_accesses))

                        {{ $signedUrl->max_accesses }} accesses

                    @else

                        Unlimited

                    @endif

                </div>

            </div>


            <div class="info-box">

                <div class="info-label">
                    Remaining Accesses
                </div>

                <div class="info-value">

                    @if($signedUrl->remainingAccesses() === null)

                        Unlimited

                    @else

                        {{ $signedUrl->remainingAccesses() }}

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- ==========================================================
         STATISTICS
    =========================================================== --}}

    <div class="stats">

        <div class="stat-card">

            <div class="stat-title">
                Total Accesses
            </div>

            <div class="stat-value">
                {{ $signedUrl->access_count }}
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Unique IP Addresses
            </div>

            <div class="stat-value">
                {{ $uniqueIps }}
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Last Accessed
            </div>

            <div class="stat-value">

                @if($signedUrl->last_accessed_at)

                    {{ $signedUrl->last_accessed_at->format('d M Y') }}

                    <br>

                    <small style="font-size:14px;color:#6b7280;">
                        {{ $signedUrl->last_accessed_at->format('h:i A') }}
                    </small>

                @else

                    Never

                @endif

            </div>

        </div>

    </div>


    {{-- ==========================================================
         ACCESS TABLE
    =========================================================== --}}

    <div class="table-card">

        <div class="table-header">

            <h2>
                Access Logs
            </h2>

        </div>


        @if($accesses->count())

            <table>

                <thead>

                <tr>

                    <th>
                        #
                    </th>

                    <th>
                        IP Address
                    </th>

                    <th>
                        Accessed At
                    </th>

                    <th>
                        User Agent
                    </th>

                </tr>

                </thead>


                <tbody>

                @foreach($accesses as $access)

                    <tr>

                        <td>

                            {{ $access->id }}

                        </td>


                        <td>

                            <span class="ip-address">

                                {{ $access->ip_address ?: 'Unknown' }}

                            </span>

                        </td>


                        <td class="date">

                            @if($access->accessed_at)

                                {{ $access->accessed_at->format('d M Y') }}

                                <br>

                                <small>
                                    {{ $access->accessed_at->format('h:i:s A') }}
                                </small>

                            @else

                                N/A

                            @endif

                        </td>


                        <td>

                            <div class="user-agent">

                                {{ $access->user_agent ?: 'Unknown' }}

                            </div>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>


            {{-- ==================================================
                 NUMERIC ONLY PAGINATION
            =================================================== --}}

            @if($accesses->lastPage() > 1)

                <div class="pagination-wrapper">

                    <div class="pagination">

                        @for(
                            $page = 1;
                            $page <= $accesses->lastPage();
                            $page++
                        )

                            @if($page == $accesses->currentPage())

                                <span class="active">
                                    {{ $page }}
                                </span>

                            @else

                                <a
                                    href="{{ $accesses->url($page) }}"
                                >
                                    {{ $page }}
                                </a>

                            @endif

                        @endfor

                    </div>

                </div>

            @endif

        @else

            <div class="empty">

                <div class="empty-icon">
                    📭
                </div>

                <h3>
                    No Access Records
                </h3>

                <p>
                    This signed URL has not been accessed yet.
                </p>

            </div>

        @endif

    </div>

</div>

</body>

</html>