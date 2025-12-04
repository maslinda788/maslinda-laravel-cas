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
| PHP | 8.0 – 8.3+ |
| OS | CentOS 9, Ubuntu 22+ |
| SSO | Apereo CAS |

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

### Option 1 – Public Package

```bash
composer require maslinda788/maslinda-laravel-cas

Option 2 – Using VCS Repository

Add this to your composer.json:

"repositories": [
  {
    "type": "vcs",
    "url": "https://github.com/maslinda788/maslinda-laravel-cas"
  }
]

Then run:

composer require maslinda788/maslinda-laravel-cas:dev-main

Publish config:
php artisan vendor:publish --tag=cas

📁 Laravel 12 Registration
1. Register Service Provider

Add in:

bootstrap/providers.php
return [
    Subfission\Cas\CasServiceProvider::class,
];
2. Add Facade Alias (Optional)

In app/Providers/AppServiceProvider.php:

use Illuminate\Foundation\AliasLoader;

public function register(): void
{
    $this->app->booting(function () {
        $loader = AliasLoader::getInstance();
        $loader->alias('Cas', \Subfission\Cas\Facades\Cas::class);
    });
}

This allows usage like:
Cas::authenticate();

⚙️ Configuration

After publishing config, edit:
config/cas.php

Example:
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

🚀 Example Usage
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


🔐 Security & Production Notes

✅ Set APP_ENV=production

✅ Enable HTTPS

✅ Secure cookie settings in php.ini:
session.cookie_secure = On
session.cookie_samesite = None

✅ Disable debug mode in production

✅ Enable CSRF protection

✅ Protect internal routes with middleware

✅ Use firewall / WAF if available

🧪 Pentest Notes

This fork has been updated with:

Safe session handling

Compatibility with CSP header

Secure cookies

CSRF protection

Laravel 12 middleware structure

⚠️ Final security depends on:

CAS server configuration

Proper HTTPS & certificate verification

Firewall & infrastructure security

📝 Changelog
v6.0.0 – Laravel 12 Fork

✅ Added Laravel 12 compatibility

✅ Updated for PHP 8.3+

✅ Removed Kernel.php dependencies

✅ New bootstrap architecture support

✅ Improved session handling

✅ CSP ready

✅ Tested on CentOS 9

✅ Production & audit-ready

v5.0.0 (Original)

Added Laravel 11 support

Added phpCAS log control

Refactored internal design

Added GitHub Actions for testing

📚 Credits

Original project:
https://github.com/subfission/cas

phpCAS:
https://www.apereo.org/projects/cas
Maintained fork:
https://github.com/maslinda788/maslinda-laravel-cas
