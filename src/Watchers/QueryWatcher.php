<?php namespace Done\LaravelAPM\Watchers;

use Illuminate\Database\Events\QueryExecuted;

class QueryWatcher
{
    protected static $total_milliseconds = 0;
    protected static $queries = [];
    protected static $query_count = 0;

    public static function record(QueryExecuted $event) // Laravel listener
    {
        self::$total_milliseconds += $event->time;

        if (self::$query_count === 1000) { // max 1000 queries per log
            self::$queries[] = '... more queries ...';
            self::$query_count++;
            return;
        } elseif (self::$query_count > 1000) {
            return;
        }
        self::$queries[] = [
            'time' => $event->time,
            'sql' => $event->sql,
            'bindings' => $event->bindings,
        ];
        self::$query_count++;
    }

    public static function getMilliseconds()
    {
        $milliseconds = self::$total_milliseconds;
        self::$total_milliseconds = 0; // reset for the next request (example: queue jobs)

        return $milliseconds;
    }

    public static function getQueries()
    {
        $queries = self::$queries;
        self::$queries = []; // reset for the next request (example: queue jobs)
        self::$query_count = 0;
        return $queries;
    }
}
