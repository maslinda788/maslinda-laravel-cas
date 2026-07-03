# Laravel CAS – Laravel 12 Compatible Fork

This repository is a maintained fork of the original `subfission/cas` package,
updated to support **Laravel 12** and **PHP 8.3+**.

⚠️ **Important Note**  
The upstream `apereo/phpCAS` project is no longer actively maintained.  
This fork focuses on **compatibility**, **security hardening**, and **support for modern Laravel versions**
while still allowing systems that depend on CAS/SSO to continue operating safely.

Maintainer: maslinda788

---

## ✅ Supported Versions

| Component  | Version |
|----------|--------|
| Laravel | 9.x – 12.x |
| PHP | 8.3+ |
| OS | CentOS 9, Ubuntu 22+ |
| SSO | Apereo CAS |
| Package | `v1.0.1-laravel12` (latest) |

---

## ✅ Laravel 12 Important Notes

Laravel 12 introduces structural changes:

- ✅ No more `Kernel.php` (HTTP & Console)
- ✅ Middleware must be registered in `bootstrap/app.php`
- ✅ Service Providers must be registered in `bootstrap/providers.php`
- ✅ Scheduled commands moved to `routes/console.php`

This package has been updated to be **fully compatible with Laravel 12’s new structure.**

---

## 📦 Installation

### Option 1 – Public Package (Recommended)

```bash
composer require maslinda788/maslinda-laravel-cas:v1.0.1-laravel12
```

### Option 2 – Using VCS Repository

Add this to your `composer.json`:

```json
"repositories": [
  {
    "type": "vcs",
    "url": "https://github.com/maslinda788/maslinda-laravel-cas"
  }
]
```

Then run:

```bash
composer require maslinda788/maslinda-laravel-cas:dev-main
```

### Publish Configuration

```bash
php artisan vendor:publish --tag=cas-config
```

## 📁 Laravel 12 Registration

### 1. Register Service Provider

Add in `bootstrap/providers.php`:

```php
return [
    // ... existing providers ...
    Subfission\Cas\CasServiceProvider::class,
];
```

> Laravel may also auto-discover this provider via `composer.json`. Manual registration in
> `bootstrap/providers.php` is recommended for Laravel 12 apps.

### 2. Register Middleware Aliases

Add in `bootstrap/app.php`:

```php
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    // ...
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'cas.auth' => \Subfission\Cas\Middleware\CASAuth::class,
            'cas.guest' => \Subfission\Cas\Middleware\RedirectCASAuthenticated::class,
        ]);
    })
    // ...
    ->create();
```

Example route usage:

```php
Route::middleware('cas.auth')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'));
});
```

### 3. Add Facade Alias (Optional)

In `app/Providers/AppServiceProvider.php`:

```php
use Illuminate\Foundation\AliasLoader;

public function register(): void
{
    $this->app->booting(function () {
        $loader = AliasLoader::getInstance();
        $loader->alias('Cas', \Subfission\Cas\Facades\Cas::class);
    });
}
```

This allows usage like:

```php
Cas::authenticate();
```

## ⚙️ Configuration

After publishing config, edit `config/cas.php`:

```php
return [
    'server' => [
        'host' => 'cas.example.com',
        'port' => 443,
        'uri'  => '/cas',
    ],

    'version' => '3.0',

    'cert' => storage_path('certs/cas.pem'),

    'validate' => true,

    'login_url'  => null,
    'logout_url' => null,
];
```

## 🚀 Example Usage

```php
use Cas;

Route::get('/login/cas', function () {
    return Cas::authenticate();
});

Route::get('/logout/cas', function () {
    return Cas::logout();
});

Route::get('/profile', function () {
    if (Cas::isAuthenticated()) {
        return Cas::user();
    }

    return redirect('/login/cas');
});
```


## 🔐 Security & Production Notes

- ✅ Set `APP_ENV=production`
- ✅ Enable HTTPS
- ✅ Secure cookie settings in `php.ini`:
  ```ini
  session.cookie_secure = On
  session.cookie_samesite = None
  ```
- ✅ Disable debug mode in production
- ✅ Enable CSRF protection
- ✅ Protect internal routes with middleware
- ✅ Use firewall / WAF if available

## 🧪 Pentest Notes

This fork has been updated with:

- ✅ Safe session handling
- ✅ Compatibility with CSP header
- ✅ Secure cookies
- ✅ CSRF protection
- ✅ Laravel 12 middleware structure

⚠️ **Final security depends on:**

- CAS server configuration
- Proper HTTPS & certificate verification
- Firewall & infrastructure security

## 📝 Changelog

### v1.0.1-laravel12

- ✅ Clean `composer audit` (updated dev dependencies)
- ✅ CI tests on PHP 8.3 with PHPUnit 11
- ✅ Removed unused `orchestra/testbench` and `pestphp/pest` dev dependencies
- ✅ Fixed README publish tag and middleware registration docs

### v1.0.0-laravel12

- ✅ Initial Laravel 12 compatible release
- ✅ Updated for PHP 8.3+
- ✅ Removed Kernel.php dependencies
- ✅ New bootstrap architecture support (`bootstrap/providers.php`)
- ✅ Improved session handling
- ✅ CSP ready
- ✅ Tested on CentOS 9

### v5.0.0 (Original upstream)

- Added Laravel 11 support
- Added phpCAS log control
- Refactored internal design
- Added GitHub Actions for testing

## 📚 Credits

- **Original project:** [subfission/cas](https://github.com/subfission/cas)
- **phpCAS:** [Apereo CAS](https://www.apereo.org/projects/cas)
- **Maintained fork:** [maslinda788/maslinda-laravel-cas](https://github.com/maslinda788/maslinda-laravel-cas)
