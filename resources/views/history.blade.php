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
            max-width: 1300px;
            margin: auto;
        }

        .header a {
            color: white;
            margin-right: 20px;
            text-decoration: none;
        }

        .container {
            max-width: 1300px;
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
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }

        input,
        select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 7px;
        }

        button,
        .button {
            padding: 10px 14px;
            border: none;
            border-radius: 7px;
            background: #2563eb;
            color: white;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
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

        .toolbar {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
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
            max-width: 250px;
            word-break: break-all;
        }

        .badge {
            display: inline-block;
            padding: 5px 9px;
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

        /*
        |--------------------------------------------------------------------------
        | PAGINATION - NUMBERS ONLY
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
            width: 42px;
            height: 42px;

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

        .pagination .active-page {
            background: #2563eb;
            color: white;
            border: 1px solid #2563eb;
        }

        .check {
            width: auto;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #6b7280;
        }

        @media(max-width: 900px) {

            .filters {
                grid-template-columns: 1fr;
            }

            .card {
                overflow-x: auto;
            }

            table {
                min-width: 1200px;
            }
        }

    </style>

</head>

<body>

<div class="header">

    <div class="header-inner">

        <h1>
            🔎 Signed URL History
        </h1>

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

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))

            <div class="success-message">
                {{ session('success') }}
            </div>

        @endif


        {{-- ERROR MESSAGE --}}
        @if(session('error'))

            <div class="error-message">
                {{ session('error') }}
            </div>

        @endif


        {{-- ==========================================================
             SEARCH / FILTER FORM
        =========================================================== --}}

        <form
            method="GET"
            action="{{ route('signed-url.history') }}"
        >

            <div class="filters">

                {{-- SEARCH --}}
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search name, URL or signature..."
                >


                {{-- STATUS --}}
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

                    <option
                        value="limit"
                        {{ request('status') === 'limit' ? 'selected' : '' }}
                    >
                        Limit Reached
                    </option>

                    <option
                        value="one_time"
                        {{ request('status') === 'one_time' ? 'selected' : '' }}
                    >
                        One-Time
                    </option>

                </select>


                {{-- DATE FROM --}}
                <input
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                >


                {{-- DATE TO --}}
                <input
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                >


                {{-- SORT --}}
                <select name="sort">

                    <option
                        value="latest"
                        {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}
                    >
                        Newest
                    </option>

                    <option
                        value="oldest"
                        {{ request('sort') === 'oldest' ? 'selected' : '' }}
                    >
                        Oldest
                    </option>

                    <option
                        value="most_accessed"
                        {{ request('sort') === 'most_accessed' ? 'selected' : '' }}
                    >
                        Most Accessed
                    </option>

                    <option
                        value="least_accessed"
                        {{ request('sort') === 'least_accessed' ? 'selected' : '' }}
                    >
                        Least Accessed
                    </option>

                    <option
                        value="expiry"
                        {{ request('sort') === 'expiry' ? 'selected' : '' }}
                    >
                        Expiry
                    </option>

                </select>

            </div>


            <div class="toolbar">

                <div>

                    <button type="submit">
                        🔍 Search / Filter
                    </button>

                    <a
                        href="{{ route('signed-url.history') }}"
                        class="button"
                    >
                        Reset
                    </a>

                </div>


                <a
                    href="{{ route('signed-url.export', request()->query()) }}"
                    class="button"
                >
                    📥 Export CSV
                </a>

            </div>

        </form>


        {{-- ==========================================================
             BULK REVOKE FORM
        =========================================================== --}}

        <form
            method="POST"
            action="{{ route('signed-url.bulk-revoke') }}"
            id="bulkForm"
        >

            @csrf

            <div class="toolbar">

                <strong>
                    {{ $signedUrls->total() }} signed URL(s)
                </strong>

                <button
                    type="submit"
                    class="danger"
                    onclick="
                        return confirm(
                            'Revoke all selected URLs?'
                        );
                    "
                >
                    🚫 Revoke Selected
                </button>

            </div>


            {{-- ======================================================
                 TABLE
            ======================================================= --}}

            <table>

                <thead>

                <tr>

                    <th>
                        <input
                            type="checkbox"
                            class="check"
                            id="selectAll"
                        >
                    </th>

                    <th>
                        ID
                    </th>

                    <th>
                        Name
                    </th>

                    <th>
                        Target URL
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Access
                    </th>

                    <th>
                        Type
                    </th>

                    <th>
                        Created IP
                    </th>

                    <th>
                        Expires
                    </th>

                    <th>
                        Actions
                    </th>

                </tr>

                </thead>


                <tbody>

                @forelse($signedUrls as $signedUrl)

                    <tr>

                        {{-- CHECKBOX --}}
                        <td>

                            @if($signedUrl->status === 'Active')

                                <input
                                    type="checkbox"
                                    class="check row-check"
                                    name="ids[]"
                                    value="{{ $signedUrl->id }}"
                                >

                            @endif

                        </td>


                        {{-- ID --}}
                        <td>
                            #{{ $signedUrl->id }}
                        </td>


                        {{-- NAME --}}
                        <td>

                            {{ $signedUrl->name ?? 'Untitled' }}

                        </td>


                        {{-- TARGET URL --}}
                        <td class="url">

                            {{ $signedUrl->target_url }}

                        </td>


                        {{-- STATUS --}}
                        <td>

                            <span
                                class="badge {{ $signedUrl->status_class }}"
                            >
                                {{ $signedUrl->status }}
                            </span>

                        </td>


                        {{-- ACCESS --}}
                        <td>

                            {{ $signedUrl->access_count }}

                            /

                            {{ $signedUrl->max_accesses ?? '∞' }}

                            @if(!is_null($signedUrl->max_accesses))

                                <br>

                                <small>

                                    Remaining:
                                    {{ $signedUrl->remaining_accesses }}

                                </small>

                            @endif

                        </td>


                        {{-- TYPE --}}
                        <td>

                            @if($signedUrl->one_time)

                                <span class="badge used">
                                    One-Time
                                </span>

                            @elseif(!is_null($signedUrl->max_accesses))

                                <span class="badge active">
                                    Limited
                                </span>

                            @else

                                <span class="badge">
                                    Unlimited
                                </span>

                            @endif

                        </td>


                        {{-- CREATED IP --}}
                        <td>

                            {{ $signedUrl->created_ip ?? 'N/A' }}

                        </td>


                        {{-- EXPIRES --}}
                        <td>

                            @if($signedUrl->expires_at)

                                {{ $signedUrl->expires_at->format('d M Y h:i A') }}

                            @else

                                N/A

                            @endif

                        </td>


                        {{-- ACTIONS --}}
                        <td>

                            <a
                                class="action"
                                href="{{ route(
                                    'signed-url.access-history',
                                    $signedUrl
                                ) }}"
                            >
                                Logs
                            </a>


                            @if($signedUrl->status === 'Active')

                                <button
                                    type="submit"
                                    class="action danger"
                                    form="revoke-{{ $signedUrl->id }}"
                                >
                                    Revoke
                                </button>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="10"
                            class="empty"
                        >
                            No signed URLs found.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </form>


        {{-- ==========================================================
             INDIVIDUAL REVOKE FORMS
        =========================================================== --}}

        @foreach($signedUrls as $signedUrl)

            @if($signedUrl->status === 'Active')

                <form
                    id="revoke-{{ $signedUrl->id }}"
                    method="POST"
                    action="{{ route(
                        'signed-url.revoke',
                        $signedUrl
                    ) }}"
                    style="display:none"
                    onsubmit="
                        return confirm(
                            'Revoke this signed URL?'
                        );
                    "
                >

                    @csrf

                </form>

            @endif

        @endforeach


        {{-- ==========================================================
             NUMERIC ONLY PAGINATION
             
             IMPORTANT:
             Do NOT put the @for loop inside onFirstPage().
        =========================================================== --}}

        @if($signedUrls->lastPage() > 1)

            <div class="pagination-wrapper">

                <div class="pagination">

                    @for(
                        $page = 1;
                        $page <= $signedUrls->lastPage();
                        $page++
                    )

                        @if($page == $signedUrls->currentPage())

                            {{-- CURRENT PAGE --}}
                            <span class="active-page">
                                {{ $page }}
                            </span>

                        @else

                            {{-- OTHER PAGE --}}
                            <a
                                href="{{ $signedUrls->url($page) }}"
                            >
                                {{ $page }}
                            </a>

                        @endif

                    @endfor

                </div>

            </div>

        @endif

    </div>

</div>


<script>

    /*
    |--------------------------------------------------------------------------
    | SELECT ALL
    |--------------------------------------------------------------------------
    */

    const selectAll = document.getElementById('selectAll');

    if (selectAll) {

        selectAll.addEventListener(
            'change',
            function () {

                document
                    .querySelectorAll('.row-check')
                    .forEach(function (checkbox) {

                        checkbox.checked =
                            selectAll.checked;

                    });

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE SELECT ALL WHEN INDIVIDUAL CHECKBOXES CHANGE
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.row-check')
        .forEach(function (checkbox) {

            checkbox.addEventListener(
                'change',
                function () {

                    const checkboxes =
                        document.querySelectorAll(
                            '.row-check'
                        );

                    const checked =
                        document.querySelectorAll(
                            '.row-check:checked'
                        );

                    if (selectAll) {

                        selectAll.checked =
                            checkboxes.length > 0 &&
                            checked.length ===
                            checkboxes.length;

                    }

                }
            );

        });

</script>


</body>

</html>