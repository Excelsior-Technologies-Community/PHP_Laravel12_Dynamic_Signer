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
                name="target_url"
                value="{{ old('target_url', route('secure.page')) }}"
                placeholder="Enter Target URL"
                required
            >

            <div class="grid">

                <div>

                    <label>
                        Expiration
                    </label>

                    <select name="expires_in">

                        @for($i = 1; $i <= 60; $i++)

                            <option
                                value="{{ $i }}"
                                {{ old('expires_in', 5) == $i ? 'selected' : '' }}
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

            <div class="grid" style="margin-top: 15px;">
                <div>
                    <label>
                        🔑 Passcode Protection (PIN)
                    </label>
                    <input
                        type="text"
                        name="passcode"
                        value="{{ old('passcode') }}"
                        placeholder="Optional secret PIN (e.g. 1234)"
                    >
                </div>

                <div style="display:flex; flex-direction:column; justify-content:center;">
                    <div class="checkbox-row" style="margin: 25px 0 0 0;">
                        <input
                            type="checkbox"
                            name="one_time"
                            value="1"
                            id="one_time"
                            {{ old('one_time') ? 'checked' : '' }}
                        >

                        <label for="one_time" style="margin:0">
                            One-time URL
                        </label>
                    </div>

                    <div class="checkbox-row" style="margin: 10px 0 0 0;">
                        <input
                            type="checkbox"
                            name="burn_after_reading"
                            value="1"
                            id="burn_after_reading"
                            {{ old('burn_after_reading') ? 'checked' : '' }}
                        >

                        <label for="burn_after_reading" style="margin:0; color:#dc3545; font-weight:bold;">
                            🔥 Burn After Reading (Self-Destruct)
                        </label>
                    </div>
                </div>
            </div>

            <label style="margin-top:15px;">
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

        @php
            $res = session('signedUrlResult');
            $generatedUrl = $res['url'] ?? ($signedUrl ?? null);
        @endphp

        @if($generatedUrl)

            <div class="result" style="background:#f8f9fa; border:2px solid #0d6efd; border-radius:15px; padding:25px; margin-top:30px;">

                <div class="success" style="background:#d1e7dd; color:#0f5132; padding:12px; border-radius:8px; margin-bottom:15px; font-weight:bold; text-align:center;">
                    ✅ Signed URL Generated Successfully!
                </div>

                <div style="display:flex; flex-wrap:wrap; gap:20px; align-items:center;">
                    {{-- QR Code Section --}}
                    <div style="text-align:center; flex: 0 0 180px;">
                        @php
                            $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($generatedUrl);
                        @endphp
                        <img src="{{ $qrApiUrl }}" alt="QR Code" style="border: 2px solid #ddd; border-radius: 10px; padding: 5px; background: white; width: 160px; height: 160px;">
                        <a href="{{ $qrApiUrl }}" download="signed-url-qr.png" target="_blank" style="display:inline-block; margin-top:8px; font-size:12px; color:#0d6efd; text-decoration:none; font-weight:bold;">
                            📥 Download QR Code
                        </a>
                    </div>

                    {{-- URL and Share Suite --}}
                    <div style="flex: 1; min-width: 250px;">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">
                            Generated Signed URL
                        </label>

                        <input
                            id="link"
                            value="{{ $generatedUrl }}"
                            readonly
                            style="width:100%; font-family:monospace; padding:10px; border-radius:6px; border:1px solid #ccc;"
                        >

                        <div style="margin-top:15px; display:flex; flex-wrap:wrap; gap:8px;">
                            <button
                                type="button"
                                onclick="copyLink()"
                                style="background:#0d6efd; color:white; border:none; padding:8px 14px; border-radius:6px; cursor:pointer;"
                            >
                                📋 Copy Link
                            </button>

                            <a
                                href="{{ $generatedUrl }}"
                                class="button"
                                target="_blank"
                                style="background:#198754; color:white; text-decoration:none; padding:8px 14px; border-radius:6px;"
                            >
                                🔓 Open Secure Link
                            </a>

                            <a
                                href="https://api.whatsapp.com/send?text={{ urlencode('Access Signed Link: ' . $generatedUrl) }}"
                                target="_blank"
                                style="background:#25D366; color:white; text-decoration:none; padding:8px 14px; border-radius:6px; font-weight:bold;"
                            >
                                💬 WhatsApp
                            </a>

                            <a
                                href="mailto:?subject={{ urlencode('Secure Signed URL') }}&body={{ urlencode('Here is your secure signed link: ' . $generatedUrl) }}"
                                style="background:#ea4335; color:white; text-decoration:none; padding:8px 14px; border-radius:6px; font-weight:bold;"
                            >
                                ✉️ Email
                            </a>

                            <a
                                href="https://t.me/share/url?url={{ urlencode($generatedUrl) }}&text={{ urlencode('Secure Signed URL') }}"
                                target="_blank"
                                style="background:#0088cc; color:white; text-decoration:none; padding:8px 14px; border-radius:6px; font-weight:bold;"
                            >
                                ✈️ Telegram
                            </a>
                        </div>

                        <div
                            id="copySuccess"
                            class="copy-success"
                            style="display:none; color:#198754; font-weight:bold; margin-top:10px;"
                        >
                            ✅ Signed URL copied successfully to clipboard!
                        </div>
                    </div>
                </div>

                {{-- Badges info --}}
                @if(isset($res))
                    <div class="info-box" style="margin-top:20px; background:white; padding:15px; border-radius:8px; border:1px solid #eee;">
                        @if(!empty($res['name']))
                            <p style="margin:4px 0;"><strong>Name:</strong> {{ $res['name'] }}</p>
                        @endif
                        <p style="margin:4px 0;"><strong>One Time:</strong> {{ !empty($res['one_time']) ? 'Yes' : 'No' }}</p>
                        <p style="margin:4px 0;"><strong>Passcode Protected:</strong> {{ !empty($res['is_passcode_protected']) ? '🔑 Yes (PIN Required)' : 'No' }}</p>
                        <p style="margin:4px 0;"><strong>Burn After Reading:</strong> {{ !empty($res['burn_after_reading']) ? '🔥 Yes (Self-Destructs after 1st access)' : 'No' }}</p>
                    </div>
                @endif

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
