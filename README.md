# 🚀 Laravel ERP DDD Boilerplate API JWT

Boilerplate Laravel API menggunakan **Domain-Driven Design (DDD)** + **Clean/Onion Architecture** dengan authentication berbasis **JWT Cookie (httpOnly)**.

Dirancang untuk mempercepat development project ERP, admin panel, atau REST API skala menengah–besar dengan struktur yang scalable, maintainable, dan clean.

---

## ✨ Features

- ✅ Laravel API Boilerplate
- ✅ Domain-Driven Design (DDD)
- ✅ Onion / Clean Architecture
- ✅ JWT Authentication via **httpOnly Cookie**
- ✅ Secure auth (No localStorage / No Bearer token)
- ✅ Google OAuth (ID Token)
- ✅ reCAPTCHA v3 support
- ✅ Repository Pattern
- ✅ Action / UseCase Pattern
- ✅ DTO (Data Transfer Object)
- ✅ Export Excel
- ✅ PDF Export
- ✅ API Resource
- ✅ Request Validation
- ✅ Service Provider Auto Registration
- ✅ Ready for ERP / CRUD System

---

## 🏗 Architecture

Project ini menggunakan pendekatan **Onion Architecture / Clean Architecture**.

### Layer Structure

```txt
app/
├── Domain/             # Pure business logic
│   ├── Entities/
│   ├── Repositories/
│   └── Services/
│
├── Application/        # Use cases / actions
│   ├── Actions/
│   └── DTOs/
│
├── Infrastructure/     # External implementation
│   ├── Persistence/
│   ├── Exports/
│   └── PDF/
│
└── Presentation/       # HTTP Layer
    ├── Controllers/
    ├── Requests/
    └── Resources/
```

### Dependency Rule

```txt
Presentation
      ↓
Application
      ↓
Domain
      ↑
Infrastructure
```

**Rule:**  
Inner layer **tidak boleh bergantung ke outer layer**.

- **Domain** → zero external dependency
- **Application** → depends on Domain
- **Infrastructure** → implement Domain contracts
- **Presentation** → HTTP/API layer

---

## 🔐 Authentication Strategy

Boilerplate ini menggunakan:

### JWT in Cookie

Bukan:

```txt
Authorization: Bearer TOKEN
```

Tapi menggunakan:

```txt
httpOnly + Secure Cookie
```

Benefits:

- ✅ More secure from XSS
- ✅ No localStorage
- ✅ No session storage
- ✅ SameSite=Strict support
- ✅ SPA Friendly

Auth flow:

```txt
Login
   ↓
Generate JWT
   ↓
Store in HttpOnly Cookie
   ↓
Middleware reads cookie
   ↓
Authenticated User
```

---

## 📦 Required Packages

Install package berikut:

```bash
composer require maatwebsite/excel \
    barryvdh/laravel-dompdf \
    php-open-source-saver/jwt-auth
```

---

## ⚙️ Installation

### 1. Clone Repository

```bash
git clone <your-repository-url>
cd your-project
```

---

### 2. Install Dependencies

```bash
composer install
```

---

### 3. Install Required Package

```bash
composer require maatwebsite/excel \
    barryvdh/laravel-dompdf \
    php-open-source-saver/jwt-auth
```

---

### 4. Publish JWT Config

```bash
php artisan vendor:publish \
--provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
```

---

### 5. Generate JWT Secret

```bash
php artisan jwt:secret
```

Auto add ke `.env`:

```env
JWT_SECRET=your-secret
```

---

### 6. Configure Auth Guard

Tambahkan di:

```php
config/auth.php
```

```php
'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

---

### 7. Register Middleware Alias

#### Laravel 11

Register di:

```php
bootstrap/app.php
```

#### Laravel 10

Register di:

```php
app/Http/Kernel.php
```

Alias:

```php
'auth.jwt' => App\Http\Middleware\CheckJwtCookie::class,
```

---

### 8. Configure Services

Tambahkan di:

```php
config/services.php
```

```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
],

'recaptcha' => [
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
],
```

Tambahkan juga di `.env`:

```env
GOOGLE_CLIENT_ID=
RECAPTCHA_SECRET_KEY=
```

---

### 9. User Migration

Tambahkan column berikut pada table `users`:

```php
$table->string('google_sub')->nullable();
$table->string('avatar')->nullable();
```

---

### 10. Register Service Providers

Tambahkan provider di:

```php
bootstrap/providers.php
```

---

### 11. Dump Autoload

```bash
composer dump-autoload
```

---

### 12. Set Carbon Locale

Di:

```php
AppServiceProvider::boot()
```

Tambahkan:

```php
Carbon::setLocale('id');
```

---

## 🧩 Request Flow

```txt
Route
   ↓
Middleware (CheckJwtCookie)
   ↓
Controller
   ↓
Action / UseCase
   ↓
Domain Service
   ↓
Repository Interface
   ↓
Eloquent Repository
```

---

## 📁 Example Module Structure

```txt
Product
├── Domain
│   ├── Entities
│   ├── Repositories
│   └── Services
│
├── Application
│   ├── Actions
│   └── DTOs
│
├── Infrastructure
│   └── Persistence
│
└── Presentation
    ├── Controllers
    ├── Requests
    └── Resources
```

---

## 🎯 Suitable For

Cocok untuk:

- ERP System
- Admin Dashboard
- Multi-module CRUD
- Enterprise API
- SaaS Backend
- Internal Company System

---

## 🤝 Contributing

Pull request are welcome.

For major changes, please open an issue first to discuss what you would like to change.

---

## 📄 License

Open-sourced software licensed under the MIT license.