<?php namespace Done\LaravelAPM\Watchers;

use Done\LaravelAPM\LogWriter;
use Illuminate\Foundation\Http\Events\RequestHandled;

class RequestWatcher
{
    public static function record(RequestHandled $event)
    {
        $duration = microtime(true) - LARAVEL_START;

        LogWriter::log(
            round(LARAVEL_START),
            $duration,
            QueryWatcher::$total_milliseconds / 1000,
            'request',
            request()->path(),
            \Auth::check() ? request()->user()->email : request()->ip()
        );
    }
}
