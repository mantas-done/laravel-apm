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
            QueryWatcher::getMilliseconds() / 1000,
            'request',
            request()->path(),
            \Auth::check() ? \Auth::user()->email : request()->ip()
        );
    }
}
