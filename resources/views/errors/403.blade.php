<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Signed URL Access Denied</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #111827;
            color: white;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .box {
            width: 90%;
            max-width: 550px;
            background: #1f2937;
            padding: 45px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 0 30px rgba(239, 68, 68, .4);
        }

        h1 {
            color: #ef4444;
        }

        p {
            color: #d1d5db;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>

</head>

<body>

<div class="box">

    <h1>🚫 Access Denied</h1>

    <p>
        This signed URL is expired, invalid,
        or has been manually revoked.
    </p>

    <a href="{{ url('/') }}">
        Generate New Signed URL
    </a>

</div>

</body>

</html>