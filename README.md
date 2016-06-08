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

## 📝 Introduction

At [Nodes](http://nodesagency.com) we send a lot of push messages from our mobile API's.

Therefore we've created a push manager, which makes the job a lot easier. It even comes with support for two push providers out of the box.

- [Parse](http://parse.com) (unfortunatley to be discontinued in 2017)
- [Urban Airship](http://urbanairship.com)

## 📦 Installation

To install this package you will need:

* Laravel 5.1+
* PHP 5.5.9+

You must then modify your `composer.json` file and run `composer update` to include the latest version of the package in your project.

```
"require": {
    "nodes/push": "^1.0"
}
```

Or you can run the composer require command from your terminal.

```
composer require nodes/push:^1.0
```

## 🔧 Setup

Setup service provider in `config/app.php`

```
Nodes\Push\ServiceProvider::class
```

Setup alias in `config/app.php`

```
'Push' => Nodes\Push\Support\Facades\Push::class
```

Publish config files

```
php artisan vendor:publish --provider="Nodes\Push\ServiceProvider"
```

If you want to overwrite any existing config files use the `--force` parameter

```
php artisan vendor:publish --provider="Nodes\Push\ServiceProvider" --force
```

## ⚙ Usage

### Global methods

```php
function push_send($message, $channels = [], \Closure $callback = null)
```

```php
function push_queue($message, $channels = [], \Closure $callback = null, $queue = null)
```

## 🏆 Credits

This package is developed and maintained by the PHP team at [Nodes Agency](http://nodesagency.com)

[![Follow Nodes PHP on Twitter](https://img.shields.io/twitter/follow/nodesphp.svg?style=social)](https://twitter.com/nodesphp) [![Tweet Nodes PHP](https://img.shields.io/twitter/url/http/nodesphp.svg?style=social)](https://twitter.com/nodesphp)

## 📄 License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)