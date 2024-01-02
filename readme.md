# Laravel APM

Monitor requests/cron/queue execution times in production.   

If you have a question: 
  - which pages have the slowest loading time?
  - which page to optimize to reduce the server load?

Then this package is for you.

![](https://i.imgur.com/yPfQieh.png)

## Installation

Supported Laravel versions: 5.5+ ... 10+

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

Laravel APM can show pages that have the biggest impact on the server load. When you are developing the website, it is hard to tell which pages will receive the most pageviews and which will use the most resources: (per page resource usage) x (pageviews) = (this package stats)

Running this package in production has minimal impact on server load.

## Technical

This package logs every user request to a file (storage/app/apm/apm-2020-01-01.txt). On average logging adds an overhead of less than 1 ms to each request (0.001 second).

## Customizations

Copy /vendor/mantas-done/laravel-apm/config/apm.php file to /config/apm.php
Then edit /config/apm.php values to your liking.

```php
return [
    'enabled' => env('APM', false),
    'per_page' => 100, // how many results per page to show
    'ignore' => [
        // put here any route names from the APM page to hide them
        'longest_requests' => [
            // 'apm',
        ],
    ],
];
```

## Recommendations

If you are using Closures for scheduler, it is recommended to add ->setName('some-name');, to be able to distinguish different Closures in APM logs.

```php
$schedule->call(function () {
    DB::table('recent_users')->delete();
})->daily()->setName('some-name'); // add ->setName()
```
