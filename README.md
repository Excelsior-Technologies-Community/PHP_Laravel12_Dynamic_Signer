#  PHP_Laravel12_Dynamic_Signer


---

##  Overview

This project demonstrates how to generate and validate **secure signed URLs** in **Laravel 12** using the **Spatie URL Signer** package.

A signed URL allows temporary and secure access to protected routes. The URL expires automatically after a defined time, preventing unauthorized access.

---

##  Features

* Generate signed URLs dynamically
* Expiration-based access control
* Middleware-protected secure route
* Clean Architecture using Service Layer
* Copy & Open signed URL functionality
* Live countdown timer for expiry
* Custom 403 expired page
* Laravel 12 middleware registration (bootstrap/app.php)

---

##  Folder Structure

```
app/
 ├── Http/
 │   └── Controllers/
 │       └── SignedUrlController.php
 │
 ├── Services/
 │   └── SignedUrlService.php
 │
bootstrap/
 └── app.php

config/
 └── url-signer.php

resources/
 └── views/
     ├── generate.blade.php
     ├── secure.blade.php
     └── errors/
         └── 403.blade.php

routes/
 └── web.php
```

---

## 1. Project Installation

### Step 1 — Create Laravel 12 Project

```bash
composer create-project laravel/laravel signed-url-project
```

Start development server:

```bash
php artisan serve
```

Open in browser:

```
http://127.0.0.1:8000
```

---

## 2. Install Spatie URL Signer Package

```bash
composer require spatie/laravel-url-signer
```

Publish configuration:

```bash
php artisan vendor:publish --provider="Spatie\UrlSigner\Laravel\UrlSignerServiceProvider"
```

This creates:

```
config/url-signer.php
```

---

## 3. Environment Configuration

Open `.env` file and add:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

URL_SIGNER_SIGNATURE_KEY=my-super-secret-key-123456
```

Clear cache:

```bash
php artisan optimize:clear
```

---

## 4. Middleware Registration (Laravel 12 Method)

Laravel 12 does NOT use Kernel.php.

Open:

```
bootstrap/app.php
```

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\UrlSigner\Laravel\Middleware\ValidateSignature;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'signedurl' => ValidateSignature::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
    })
    
    ->create();
```

Now middleware alias `signedurl` is registered.

---

## 5. Create Service Class (Clean Architecture)

```bash
php artisan make:class Services/SignedUrlService
```

File:

```
app/Services/SignedUrlService.php
```

```php
<?php

namespace App\Services;

use Spatie\UrlSigner\Laravel\Facades\UrlSigner;

class SignedUrlService
{
    public function create($target, $minutes = 10, $params = [])
    {
        $expiresAt = now()->addMinutes($minutes);

        $url = filter_var($target, FILTER_VALIDATE_URL)
            ? $target
            : route($target, $params);

        $signedUrl = UrlSigner::sign($url, $expiresAt);

        return [
            'url' => $signedUrl,
            'expires' => $expiresAt->timestamp
        ];
    }
}
```

---

## 6. Create Controller

```bash
php artisan make:controller SignedUrlController
```

File:

```
app/Http/Controllers/SignedUrlController.php
```

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SignedUrlService;

class SignedUrlController extends Controller
{
    public function form()
    {
        return view('generate');
    }

    public function generate(Request $request, SignedUrlService $signer)
    {
        $request->validate([
            'url' => 'required|string'
        ]);

        $data = $signer->create($request->url, 5);

        return view('generate', [
            'signedUrl' => $data['url'],
            'expiresAt' => $data['expires']
        ]);
    }

    public function secure()
    {
        return view('secure');
    }
}
```

---

## 7. Define Routes

Open:

```
routes/web.php
```

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignedUrlController;

Route::get('/', [SignedUrlController::class, 'form']);

Route::post('/generate', [SignedUrlController::class, 'generate'])
    ->name('generate');

Route::get('/secure-page', [SignedUrlController::class, 'secure'])
    ->name('secure.page')
    ->middleware('signedurl');
```

---

## 8. Create Blade Views

### Generate Page

Create:

```
resources/views/generate.blade.php
```
```
<!DOCTYPE html>
<html>
<head>
<title>Dynamic URL Signer</title>

<style>
body{
    font-family:Arial;
    background:linear-gradient(135deg,#4facfe,#00f2fe);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:0;
}

.card{
    background:white;
    padding:40px;
    border-radius:15px;
    width:550px;
    text-align:center;
    box-shadow:0 10px 25px rgba(0,0,0,.2);
}

input{
    width:100%;
    padding:12px;
    margin-top:10px;
    border-radius:8px;
    border:1px solid #ccc;
}

button{
    margin-top:15px;
    padding:12px 25px;
    background:#4facfe;
    border:none;
    color:white;
    border-radius:8px;
    cursor:pointer;
}

button:hover{
    background:#007bff;
}

#timer{
    margin-top:15px;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="card">

<h2> Dynamic Signed URL Generator</h2>

<form method="POST" action="{{ route('generate') }}">
@csrf

<input type="text" name="url"
placeholder="Enter URL or Route Name"
required>

<button type="submit">Generate Signed URL</button>

</form>

@if(isset($signedUrl))

<hr>

<input id="link" value="{{ $signedUrl }}" readonly>

<button onclick="copyLink()">Copy Link</button>

<a href="{{ $signedUrl }}">
<button type="button">Open Secure Page</button>
</a>

<p id="timer"></p>

<script>
function copyLink(){
    let input=document.getElementById("link");
    input.select();
    document.execCommand("copy");
    alert("Copied!");
}

let expiry={{ $expiresAt ?? 0 }}*1000;

let timer=setInterval(function(){
    let now=new Date().getTime();
    let distance=expiry-now;

    let m=Math.floor((distance%(1000*60*60))/(1000*60));
    let s=Math.floor((distance%(1000*60))/1000);

    document.getElementById("timer").innerHTML=
        "Expires in: "+m+"m "+s+"s";

    if(distance<0){
        clearInterval(timer);
        document.getElementById("timer").innerHTML="Expired";
    }
},1000);
</script>

@endif

</div>

</body>
</html>
```

### Secure Page

Create:

```
resources/views/secure.blade.php
```
```
<!DOCTYPE html>
<html>
<head>
<title>Secure Page</title>

<style>
body{
    background:#111;
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    margin:0;
    font-family:Arial;
}

.box{
    background:#1f1f1f;
    padding:50px;
    border-radius:15px;
    box-shadow:0 0 25px rgba(0,255,150,.4);
    text-align:center;
}

h1{
    color:#00ff99;
}
</style>
</head>

<body>

<div class="box">
<h1> Access Granted</h1>
<p>Signed URL is valid.</p>
</div>

</body>
</html>
```
---

## 9. Custom 403 Page

Create:

```
resources/views/errors/403.blade.php
```
```
<!DOCTYPE html>
<html>
<head>
<title>Expired</title>

<style>
body{
    background:#111;
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    font-family:Arial;
}

.box{
    background:#1f1f1f;
    padding:40px;
    border-radius:12px;
    text-align:center;
    box-shadow:0 0 20px red;
}

h1{ 
    color:red; 
}
</style>
</head>

<body>

<div class="box">
<h1> Link Expired or Invalid</h1>
<p>Please generate a new link.</p>
</div>

</body>
</html>
---
```
## 10. Run the Project

```bash
php artisan serve
```

Visit:

```
http://127.0.0.1:8000
```
<img width="620" height="252" alt="Screenshot 2026-02-27 124124" src="https://github.com/user-attachments/assets/f8b327db-10dc-4f82-8e2e-065081f7f62d" />

---
Enter:

```
http://127.0.0.1:8000/secure-page
```
<img width="625" height="253" alt="Screenshot 2026-02-27 120222" src="https://github.com/user-attachments/assets/b18aa385-b91c-4eb9-a0aa-df420d2cfe4a" />

---
Generate:

<img width="624" height="423" alt="Screenshot 2026-02-27 120237" src="https://github.com/user-attachments/assets/27958466-3d95-4623-aa3a-8358d7a7bfc3" />

---
Open → Works:

<img width="341" height="214" alt="Screenshot 2026-02-27 120304" src="https://github.com/user-attachments/assets/5e574b77-1f25-4a48-8e5a-6386d7cd813d" />

---
Modify URL → 404 error:

<img width="358" height="180" alt="Screenshot 2026-02-27 124910" src="https://github.com/user-attachments/assets/bf5e0c14-3362-499b-9b67-51f7a7575c55" />

---
When Signed URL Expired:

<img width="420" height="190" alt="Screenshot 2026-02-27 120839" src="https://github.com/user-attachments/assets/20784452-30b4-4a9f-bef5-3123444434fc" />

---
