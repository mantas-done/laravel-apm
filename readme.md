# Laravel APM

Find website performance bottlenecks in production.

![](http://i.imgur.com/2KTtx5f.png)

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

Laravel APM can show pages that have the biggest impact on server load, which pages spent the most time in SQL queries and other stats.   

Running this package in production almost doesn't have any impact on server load. Moreover it is probably the only free self hosted Laravel APM.

## Technical

This package logs every user request, also queue and cron job execution in a file (storage/app/apm/apm-2020-01-01.txt). Logging adds an overhead of 1 ms to each request (0.001 second).   
Log file is parsed only when viewing the dashboard. Parsing is quite fast, file is parsed over 100,000 lines per second.