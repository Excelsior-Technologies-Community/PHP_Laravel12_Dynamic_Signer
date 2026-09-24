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
            max-width: 750px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 40px;
            border-radius: 18px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .2);
        }

        h1 {
            margin-top: 0;
            color: #222;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }

        input {
            width: 100%;
            padding: 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 12px;
            font-size: 15px;
        }

        button,
        .button {
            display: inline-block;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            background: #4facfe;
            color: white;
            cursor: pointer;
            text-decoration: none;
            margin: 5px;
            font-size: 14px;
        }

        button:hover,
        .button:hover {
            background: #007bff;
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
            padding-top: 20px;
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

        #timer {
            margin-top: 15px;
            font-weight: bold;
            color: #d35400;
        }
    </style>

</head>

<body>

<div class="container">

    <div class="card">

        <h1>🔐 Dynamic Signed URL Generator</h1>

        <p class="subtitle">
            Generate temporary and secure URLs with Laravel
            and Spatie URL Signer.
        </p>

        @if ($errors->any())

            <div class="error">

                @foreach ($errors->all() as $error)

                    <div>{{ $error }}</div>

                @endforeach

            </div>

        @endif

        <form
            method="POST"
            action="{{ route('generate') }}"
        >

            @csrf

            <input
                type="text"
                name="url"
                value="{{ old('url', route('secure.page')) }}"
                placeholder="Enter URL or Route Name"
                required
            >

            <button type="submit">
                🔗 Generate Signed URL
            </button>

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

                <input
                    id="link"
                    value="{{ $signedUrl }}"
                    readonly
                >

                <button
                    type="button"
                    onclick="copyLink()"
                >
                    📋 Copy Link
                </button>

                <a href="{{ $signedUrl }}" class="button">
                    🔓 Open Secure Page
                </a>

                <p id="timer">
                    Calculating expiry...
                </p>

            </div>

            <script>
                function copyLink() {
                    const input =
                        document.getElementById("link");

                    navigator.clipboard.writeText(input.value)
                        .then(function () {
                            alert("Signed URL copied!");
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

                            document.getElementById("timer")
                                .innerHTML =
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

                        document.getElementById("timer")
                            .innerHTML =
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