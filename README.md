#  PHP_Laravel12_Dynamic_Signer

![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.x-blue)
![License](https://img.shields.io/badge/License-MIT-green)
![Status](https://img.shields.io/badge/Status-Active-success)

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

