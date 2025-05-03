# Laravel Gmail Unique

[![Packagist License](https://img.shields.io/badge/Licence-MIT-blue)](https://github.com/aliziodev/laravel-gmail-unique/blob/main/LICENSE)
[![Latest Stable Version](https://img.shields.io/packagist/v/aliziodev/laravel-gmail-unique?label=Stable)](https://packagist.org/packages/aliziodev/laravel-gmail-unique)
[![Total Downloads](https://img.shields.io/packagist/dt/aliziodev/laravel-gmail-unique.svg?label=Downloads)](https://packagist.org/packages/aliziodev/laravel-gmail-unique)
[![PHP Version](https://img.shields.io/packagist/php-v/aliziodev/laravel-gmail-unique.svg)](https://packagist.org/packages/aliziodev/laravel-gmail-unique)
[![Laravel Version](https://img.shields.io/badge/Laravel-10.x-red)](https://packagist.org/packages/aliziodev/laravel-gmail-unique)
[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red)](https://packagist.org/packages/aliziodev/laravel-gmail-unique)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red)](https://packagist.org/packages/aliziodev/laravel-gmail-unique)

Laravel Gmail Unique is a package that normalizes Gmail addresses during validation to prevent duplicate user registrations with Gmail's dot variations and plus aliases. Gmail treats addresses like john.doe@gmail.com, johndoe@gmail.com, and john+alias@gmail.com as the same account, but standard validation treats them as different emails.

According to Google's official documentation [Google: Getting messages sent to a dotted version of my address](https://support.google.com/mail/answer/10313#zippy=%2Cgetting-messages-sent-to-a-dotted-version-of-my-address) , if you add dots to a Gmail address, you'll still get that email. For example, if your email is johnsmith@gmail.com, you own all dotted versions of your address:

-   john.smith@gmail.com
-   jo.hn.sm.ith@gmail.com
-   j.o.h.n.s.m.i.t.h@gmail.com

This package ensures that your application recognizes these variations as the same email address, preventing duplicate accounts and improving user experience. This is especially recommended for SaaS systems with trial subscriptions to prevent users from creating multiple trial accounts using Gmail variations.

## Features

-   Normalizes Gmail addresses by removing dots and plus aliases
-   Prevents duplicate user registrations with Gmail variations
-   Easy integration with Laravel's validation system
-   Configurable for custom domains and email column names
-   Works with Laravel's Eloquent models via a simple trait

## Installation

You can install the package via composer:

```bash
composer require aliziodev/laravel-gmail-unique
```

After installation, you can use the package's install command:

```bash
php artisan gmail-unique:install
```

## Configuration

After publishing the configuration, you can modify the settings in `config/gmail-unique.php` :

```php
return [
    // Email domains to normalize (default: gmail.com and googlemail.com)
    'domains' => ['gmail.com', 'googlemail.com'],

    // The column name used for email in your database (default: email)
    'email_column' => 'email',

    // Error message for duplicate Gmail addresses
    'error_message' => 'This email is already taken (normalized Gmail detected).'
];
```

## Usage

### Using the Trait

The simplest way to use this package is by adding the `HasNormalizedEmail` trait to your User model:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Aliziodev\GmailUnique\Traits\HasNormalizedEmail;

class User extends Authenticatable
{
    use HasNormalizedEmail;

    // ... rest of your model
}
```

With this trait, your model will automatically:

1. Normalize Gmail addresses during validation
2. Prevent saving duplicate Gmail addresses with different dot variations or plus aliases
3. Allow users to update their own email with different variations of the same normalized address

### Using the Facade

This package also provides a Facade for easier access to the Gmail Unique functionality:

```php
<?php

namespace App\Http\Controllers;

use Aliziodev\GmailUnique\Facades\GmailUnique;
use App\Models\User;

class UserController extends Controller
{
    public function checkEmail($email)
    {
        // Normalize an email address
        $normalized = GmailUnique::normalize($email);

        // Check if a normalized version already exists
        $isDuplicate = GmailUnique::isDuplicate($email, User::class, $excludeId = null);

        return [
            'original' => $email,
            'normalized' => $normalized,
            'isDuplicate' => $isDuplicate
        ];
    }
}
```

### Using the Service

If you need more control, you can use the `GmailUniqueService` directly:

```php
<?php

namespace App\Http\Controllers;

use Aliziodev\GmailUnique\Services\GmailUniqueService;
use App\Models\User;

class UserController extends Controller
{
    protected $gmailService;

    public function __construct(GmailUniqueService $gmailService)
    {
        $this->gmailService = $gmailService;
    }

    public function checkEmail($email)
    {
        // Normalize an email address
        $normalized = $this->gmailService->normalize($email);

        // Check if a normalized version already exists
        $isDuplicate = $this->gmailService->isDuplicate($email, User::class, $excludeId = null);

        return [
            'original' => $email,
            'normalized' => $normalized,
            'isDuplicate' => $isDuplicate
        ];
    }
}
```

### Custom Validation Rules

You can also use the service in custom validation rules:

```php
use Aliziodev\GmailUnique\Services\GmailUniqueService;
use App\Models\User;

// In a form request or controller
$validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    $gmailService = app(GmailUniqueService::class);
                    if ($gmailService->isDuplicate($value, User::class)) {
                         $fail('This email address is already taken.');
                    }
                }
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
```

## Testing

The package includes comprehensive tests. You can run them with:

```bash
./vendor/bin/pest
```

## License

The MIT License (MIT). Please see License File for more information.

## Contributing

Contributions are welcome! Please create issues or pull requests on the GitHub repository.
