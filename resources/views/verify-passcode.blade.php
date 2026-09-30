<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔑 Passcode Verification Required | Dynamic URL Signer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .passcode-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.25);
            max-width: 450px;
            width: 100%;
            padding: 40px 30px;
            text-align: center;
        }
        .passcode-icon {
            font-size: 60px;
            margin-bottom: 15px;
        }
        .pin-input {
            font-size: 24px;
            letter-spacing: 6px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="passcode-card">
    <div class="passcode-icon">🔒</div>
    <h3 class="fw-bold mb-2">Protected Signed Link</h3>
    <p class="text-muted mb-4">Enter the secret Passcode / PIN to access this secure content.</p>

    @if(session('error'))
        <div class="alert alert-danger mb-3">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('signed-url.verify-passcode') }}">
        @csrf
        <input type="hidden" name="signature" value="{{ $signedUrl->signature }}">
        <input type="hidden" name="full_url" value="{{ $fullUrl }}">

        <div class="mb-4">
            <input type="password" name="passcode" class="form-control form-control-lg pin-input" placeholder="••••" required autofocus maxlength="50">
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
            🔑 Unlock & Proceed
        </button>
    </form>
</div>

</body>
</html>
