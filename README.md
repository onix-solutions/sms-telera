# Sms Telera notifications channel for Laravel 5.3+

[![Latest Version on Packagist](https://img.shields.io/packagist/v/onix-solutions/sms-telera.svg?style=flat-square)](https://packagist.org/packages/onix-solutions/sms-telera)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/onix-solutions/sms-telera/master.svg?style=flat-square)](https://travis-ci.org/onix-solutions/sms-telera)
[![StyleCI](https://styleci.io/repos/65589451/shield)](https://styleci.io/repos/65589451)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/aceefe27-ba5a-49d7-9064-bc3abea0abeb.svg?style=flat-square)](https://insight.sensiolabs.com/projects/aceefe27-ba5a-49d7-9064-bc3abea0abeb)
[![Quality Score](https://img.shields.io/scrutinizer/g/onix-solutions/sms-telera.svg?style=flat-square)](https://scrutinizer-ci.com/g/onix-solutions/sms-telera)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/onix-solutions/sms-telera/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/onix-solutions/sms-telera/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/onix-solutions/sms-telera.svg?style=flat-square)](https://packagist.org/packages/onix-solutions/sms-telera)

This package makes it easy to send notifications using [sms.telera](//sms.telera) with Laravel 5.3+.

## Contents

- [Installation](#installation)
    - [Setting up the SmsTelera service](#setting-up-the-SmsTelera-service)
- [Usage](#usage)
    - [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

You can install the package via composer:

```bash
composer require onix-solutions/sms-telera
```

Then you must install the service provider:
```php
// config/app.php
'providers' => [
    ...
    OnixSolutions\SmsTelera\SmsTeleraServiceProvider::class,
],
```

### Setting up the SmsTelera service

Add your SmsTelera login, secret key (hashed password) and default sender name (or phone number) to your `config/services.php`:

```php
// config/services.php
...
'smsctelera' => [
    'tk'  => env('SMSCTELERA_TK'),
    'sender' => env(''SMSCTELERA_SENDER)
],
...
```

> If you want use other host than `smsc.telera`, you MUST set custom host WITH trailing slash.

```
// .env
...
SMSCTELERA_HOST=http://www1.smsc.kz/
...
```

```php
// config/services.php
...
'smsctelera' => [
    ...
    'host' => env('SMSCTELERA_HOST'),
    ...
],
...
```

## Usage

You can use the channel in your `via()` method inside the notification:

```php
use Illuminate\Notifications\Notification;
use OnixSolutions\SmsTelera\SmsTeleraMessage;
use OnixSolutions\SmsTelera\SmsTeleraChannel;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [SmsTeleraChannel::class];
    }

    public function toSmsTelera($notifiable)
    {
        return SmsTeleraMessage::create("Task #{$notifiable->id} is complete!");
    }
}
```

In your notifiable model, make sure to include a `routeNotificationForSmsctelera()` method, which returns a phone number
or an array of phone numbers.

```php
public function routeNotificationForSmsctelera()
{
    return $this->phone;
}
```

### Available methods

`from()`: Sets the sender's name or phone number.

`content()`: Set a content of the notification message.

`sendAt()`: Set a time for scheduling the notification message.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email jhaoda@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [SandroBocon](https://github.com/sandrobocon)
- [JhaoDa](https://github.com/jhaoda)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
