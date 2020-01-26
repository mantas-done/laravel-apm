<?php namespace Done\LaravelAPM\Watchers;

use Illuminate\Database\Events\QueryExecuted;

class QueryWatcher
{
    protected static $total_milliseconds = 0;

    public static function record(QueryExecuted $event) // Laravel listener
    {
        self::$total_milliseconds += $event->time;
    }

    public static function getMilliseconds()
    {
        $milliseconds = self::$total_milliseconds;
        self::$total_milliseconds = 0; // reset for the next request (example: queue jobs)

        return $milliseconds;
    }
}
