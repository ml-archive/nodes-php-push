## Push

A Push manager to send push messages to mobile devices from your project.

[![Total downloads](https://img.shields.io/packagist/dt/nodes/push.svg)](https://packagist.org/packages/nodes/push)
[![Monthly downloads](https://img.shields.io/packagist/dm/nodes/push.svg)](https://packagist.org/packages/nodes/push)
[![Latest release](https://img.shields.io/packagist/v/nodes/push.svg)](https://packagist.org/packages/nodes/push)
[![Open issues](https://img.shields.io/github/issues/nodes-php/push.svg)](https://github.com/nodes-php/push/issues)
[![License](https://img.shields.io/packagist/l/nodes/push.svg)](https://packagist.org/packages/nodes/push)
[![Star repository on GitHub](https://img.shields.io/github/stars/nodes-php/push.svg?style=social&label=Star)](https://github.com/nodes-php/push/stargazers)
[![Watch repository on GitHub](https://img.shields.io/github/watchers/nodes-php/push.svg?style=social&label=Watch)](https://github.com/nodes-php/push/watchers)
[![Fork repository on GitHub](https://img.shields.io/github/forks/nodes-php/push.svg?style=social&label=Fork)](https://github.com/nodes-php/push/network)
[![Travis tests](https://img.shields.io/travis/nodes-php/push.svg)](https://travis-ci.org/nodes-php/push)

## 📝 Introduction

At [Nodes](http://nodesagency.com) we send a lot of push messages from our mobile API's.

Therefore we've created a push manager, which makes the job a lot easier. It even comes with support for two push providers out of the box.

- [Urban Airship](http://urbanairship.com)

## 📦 Installation

To install this package you will need:

* Laravel 5.2+
* PHP 7.0+


You must then modify your `composer.json` file and run `composer update` to include the latest version of the package in your project.

```json
"require": {
    "nodes/push": "^2.0"
}
```

Or you can run the composer require command from your terminal.

```bash
composer require nodes/push:^2.0
```

## 🔧 Setup

Setup service provider in `config/app.php`

```php
Nodes\Push\ServiceProvider::class
```

Setup alias in `config/app.php`

```php
'Push' => Nodes\Push\Support\Facades\Push::class
```

Publish config files

```bash
php artisan vendor:publish --provider="Nodes\Push\ServiceProvider"
```

If you want to overwrite any existing config files use the `--force` parameter

```bash
php artisan vendor:publish --provider="Nodes\Push\ServiceProvider" --force
```

## ⚙ Usage

### Global methods

```php
push()->setMessage('test')
->setExtra([
    'id' => 1
])
->send();

push()->setMessage('test')
->setExtra([
    'id' => 1
])
->sendAsync();
```

## 🏆 Credits

This package is developed and maintained by the PHP team at [Nodes Agency](http://nodesagency.com)

[![Follow Nodes PHP on Twitter](https://img.shields.io/twitter/follow/nodesphp.svg?style=social)](https://twitter.com/nodesphp) [![Tweet Nodes PHP](https://img.shields.io/twitter/url/http/nodesphp.svg?style=social)](https://twitter.com/nodesphp)

## 📄 License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)