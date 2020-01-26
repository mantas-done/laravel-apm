# Laravel APM

Monitor requests/cron/queue execution times in production.

![](http://i.imgur.com/wrUwCRi.png)

## Installation

```
composer require mantas-done/laravel-apm
```

Add route to your routes/web.php file (don't forget securing it from unwanted visitors)

```php
Route::get('/apm', '\Done\LaravelAPM\ApmController@index')->name('apm');
```

Daily clear log files by adding scheduled job to App/Console/Kernel.php

```
$schedule->command('apm:clear')->daily();
```

Enable APM in .env file

```
APM=1
```

## Why?

The main goal of Laravel APM is to find which code to optimize to reduce server load in production. APM stands for application performance monitoring.   

Laravel APM can show pages that have the biggest impact on server load, also pages that spend the most time in SQL queries and some other stats.   

Running this package in production has minimal impact on server load.

## Technical

This package logs every user request to a file (storage/app/apm/apm-2020-01-01.txt). On average logging adds an overhead of less than 1 ms to each request (0.001 second).

## Recommendations

If you are using Closures for scheduler, it is recommended to add ->setName('some-name');, to be able to distinguish different Closures in APM logs.

```php
$schedule->call(function () {
    DB::table('recent_users')->delete();
})->daily()->setName('some-name'); // add ->setName()
```