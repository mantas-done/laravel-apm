<?php namespace Done\LaravelAPM\Watchers;

use Illuminate\Database\Events\QueryExecuted;

class QueryWatcher
{
    public static $total_milliseconds = 0;

    public static function record(QueryExecuted $event)
    {
        self::$total_milliseconds += $event->time;
    }
}
