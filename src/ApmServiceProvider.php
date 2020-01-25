<?php namespace Done\LaravelAPM;

use Done\LaravelAPM\Console\ApmClearCommand;
use Done\LaravelAPM\Watchers\JobWatcher;
use Done\LaravelAPM\Watchers\QueryWatcher;
use Done\LaravelAPM\Watchers\RequestWatcher;
use Done\LaravelAPM\Watchers\ScheduleWatcher;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\ServiceProvider;

class ApmServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'apm');
    }

    public function register()
    {
        $app = $this->app;

        $app['events']->listen(RequestHandled::class, [RequestWatcher::class, 'record']);

        $app['events']->listen(ScheduledTaskFinished::class, [ScheduleWatcher::class, 'record']);

        $app['events']->listen(JobProcessing::class, [JobWatcher::class, 'start']); // start
        $app['events']->listen(JobProcessed::class, [JobWatcher::class, 'record']); // finish
        $app['events']->listen(JobFailed::class, [JobWatcher::class, 'record']); // finish

        $app['events']->listen(QueryExecuted::class, [QueryWatcher::class, 'record']);

        $this->commands([
            ApmClearCommand::class
        ]);
    }
}
