# Simpe UBER
[![Total Downloads](https://img.shields.io/packagist/dt/f3ath/simpleuber.svg)](https://packagist.org/packages/f3ath/simpleuber)
[![Latest Stable Version](https://img.shields.io/packagist/v/f3ath/simpleuber.svg)](https://packagist.org/packages/f3ath/simpleuber)
[![Travis Build](https://travis-ci.org/f3ath/simpleuber.svg?branch=master)](https://travis-ci.org/f3ath/simpleuber)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3637a8cf-8735-465a-b528-a4ad1edff017/mini.png)](https://insight.sensiolabs.com/projects/3637a8cf-8735-465a-b528-a4ad1edff017)

Simple UBER API client for PHP

This implementation is for Products, Price Estimates, and Time Estimates [API endpoints](https://developer.uber.com/docs/api-overview).
It only requires the server_token, no OAth involved.

##Install
Via [composer](https://getcomposer.org):

`$ composer require "f3ath/simpleuber"`

##Usage
```php
<?php
require_once 'vendor/autoload.php';

$uber = new \F3\SimpleUber\Uber('your server token');

try{
    var_dump($uber->getProducts(37.773972, -122.431297));
    var_dump($uber->getPriceEstimates(37.773972, -122.431297, 37.333333, -121.9));
    var_dump($uber->getTimeEstimates(37.773972, -122.431297));
} catch (\F3\SimpleUber\ApiException $e) {
    var_dump($e->getErrorMessage());
    var_dump($e->getErrorCode());
}

```
