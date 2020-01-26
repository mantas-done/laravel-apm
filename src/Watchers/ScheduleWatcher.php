<?php namespace Done\LaravelAPM\Watchers;

use Done\LaravelAPM\LogWriter;
use Illuminate\Console\Events\ScheduledTaskFinished;

class ScheduleWatcher
{
    public static function record(ScheduledTaskFinished $event)
    {
        LogWriter::logAndWrite(
            round(LARAVEL_START),
            $event->runtime,
            QueryWatcher::getMilliseconds() / 1000,
            'schedule',
            $event->task->getSummaryForDisplay() ?: 'Closure'
        );
    }
}