<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dynamic URL Signer</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(
                135deg,
                #4facfe,
                #00f2fe
            );
            min-height: 100vh;
            margin: 0;
            padding: 40px 20px;
        }

        .container {
            width: 100%;
            max-width: 850px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,.2);
        }

        h1 {
            margin-top: 0;
            text-align: center;
            color: #222;
        }

        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            display: block;
            text-align: left;
            font-weight: bold;
            margin: 15px 0 7px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 13px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }

        .checkbox-row input {
            width: auto;
        }

        button,
        .button {
            display: inline-block;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            cursor: pointer;
            text-decoration: none;
            margin: 5px;
            font-size: 14px;
        }

        button:hover,
        .button:hover {
            background: #1d4ed8;
        }

        .dashboard {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .result {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #ddd;
        }

        .success {
            background: #e8fff2;
            color: #087443;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .error {
            background: #ffe8e8;
            color: #a40000;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .copy-success {
            display: none;
            background: #dcfce7;
            color: #166534;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
        }

        #timer {
            margin-top: 15px;
            font-weight: bold;
            color: #d35400;
            text-align: center;
        }

        .info-box {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
            text-align: left;
        }

        .info-box strong {
            color: #111827;
        }

        @media(max-width: 700px) {

            .grid {
                grid-template-columns: 1fr;
            }

            .card {
                padding: 25px;
            }
        }

    </style>

</head>

<body>

<div class="container">

    <div class="card">

        <h1>
            🔐 Dynamic Signed URL Generator
        </h1>

        <p class="subtitle">
            Generate temporary and controlled signed URLs.
        </p>

        @if ($errors->any())

            <div class="error">

                @foreach ($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        @endif

        <form
            method="POST"
            action="{{ route('generate') }}"
        >

            @csrf

            <label>
                URL Name
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Example: Customer Download Link"
            >

            <label>
                Target URL / Route Name
            </label>

            <input
                type="text"
                name="url"
                value="{{ old('url', route('secure.page')) }}"
                placeholder="Enter URL or Route Name"
                required
            >

            <div class="grid">

                <div>

                    <label>
                        Expiration
                    </label>

                    <select name="minutes">

                        @for($i = 1; $i <= 60; $i++)

                            <option
                                value="{{ $i }}"
                                {{ old('minutes', 5) == $i ? 'selected' : '' }}
                            >
                                {{ $i }} minute{{ $i > 1 ? 's' : '' }}
                            </option>

                        @endfor

                    </select>

                </div>

                <div>

                    <label>
                        Maximum Accesses
                    </label>

                    <input
                        type="number"
                        name="max_accesses"
                        min="1"
                        max="10000"
                        value="{{ old('max_accesses') }}"
                        placeholder="Leave empty = unlimited"
                    >

                </div>

            </div>

            <div class="checkbox-row">

                <input
                    type="checkbox"
                    name="one_time"
                    value="1"
                    id="one_time"
                    {{ old('one_time') ? 'checked' : '' }}
                >

                <label
                    for="one_time"
                    style="margin:0"
                >
                    One-time URL
                </label>

            </div>

            <label>
                Notes
            </label>

            <textarea
                name="notes"
                placeholder="Optional notes..."
            >{{ old('notes') }}</textarea>

            <div style="text-align:center; margin-top:20px;">

                <button type="submit">
                    🔗 Generate Signed URL
                </button>

            </div>

        </form>

        <div class="dashboard">

            <a
                class="button"
                href="{{ route('signed-url.analytics') }}"
            >
                📊 Analytics
            </a>

            <a
                class="button"
                href="{{ route('signed-url.history') }}"
            >
                🔎 URL History
            </a>

        </div>

        @if(isset($signedUrl))

            <div class="result">

                <div class="success">
                    Signed URL generated successfully.
                </div>

                <label>
                    Generated Signed URL
                </label>

                <input
                    id="link"
                    value="{{ $signedUrl }}"
                    readonly
                >

                <div style="text-align:center;">

                    <button
                        type="button"
                        onclick="copyLink()"
                    >
                        📋 Copy Link
                    </button>

                    <a
                        href="{{ $signedUrl }}"
                        class="button"
                    >
                        🔓 Open Secure Page
                    </a>

                </div>

                <div
                    id="copySuccess"
                    class="copy-success"
                >
                    ✅ Signed URL copied successfully!
                </div>

                <div class="info-box">

                    @if($signedUrlRecord->name)

                        <p>
                            <strong>Name:</strong>
                            {{ $signedUrlRecord->name }}
                        </p>

                    @endif

                    <p>
                        <strong>Expires:</strong>
                        {{ $signedUrlRecord->expires_at->format('d M Y h:i A') }}
                    </p>

                    <p>
                        <strong>Maximum Accesses:</strong>

                        {{ $signedUrlRecord->max_accesses ?? 'Unlimited' }}
                    </p>

                    <p>
                        <strong>One Time:</strong>

                        {{ $signedUrlRecord->one_time ? 'Yes' : 'No' }}
                    </p>

                </div>

                <p id="timer">
                    Calculating expiry...
                </p>

            </div>

            <script>

                function copyLink() {

                    const input =
                        document.getElementById("link");

                    navigator.clipboard.writeText(
                        input.value
                    ).then(function () {

                        const message =
                            document.getElementById(
                                "copySuccess"
                            );

                        message.style.display =
                            "block";

                        setTimeout(function () {

                            message.style.display =
                                "none";

                        }, 3000);

                    });

                }

                const expiry =
                    {{ $expiresAt ?? 0 }} * 1000;

                const timer =
                    setInterval(function () {

                        const now =
                            new Date().getTime();

                        const distance =
                            expiry - now;

                        if (distance <= 0) {

                            clearInterval(timer);

                            document.getElementById(
                                "timer"
                            ).innerHTML =
                                "⛔ Signed URL Expired";

                            return;
                        }

                        const minutes =
                            Math.floor(
                                distance /
                                (1000 * 60)
                            );

                        const seconds =
                            Math.floor(
                                (distance %
                                (1000 * 60)) /
                                1000
                            );

                        document.getElementById(
                            "timer"
                        ).innerHTML =
                            "Expires in: " +
                            minutes +
                            "m " +
                            seconds +
                            "s";

                    }, 1000);

            </script>

        @endif

    </div>

</div>

</body>

</html>
