<?php namespace Done\LaravelAPM\Watchers;


use Done\LaravelAPM\LogWriter;

class JobWatcher
{
    public static function start($event)
    {
        $event->job->apm_job_start = microtime(true);
    }

    public static function record($event)
    {
        $duration = microtime(true) - $event->job->apm_job_start;

        LogWriter::logAndWrite(
            round(LARAVEL_START),
            $duration,
            QueryWatcher::getMilliseconds() / 1000,
            'job',
            $event->job->resolveName()
        );
    }
}