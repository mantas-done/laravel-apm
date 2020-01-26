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
        $config_path = __DIR__ . '/../config/apm.php';
        $this->mergeConfigFrom($config_path, 'apm');

        // boot apm:clear event if apm is disabled, because user registered it in scheduler and will call it anyways
        if ($this->app->runningInConsole()) {
            $this->commands([
                ApmClearCommand::class
            ]);
        }

        // don't register if AMP is not enabled
        if ($this->app['config']['apm']['enabled']) {
            $this->app['events']->listen(RequestHandled::class, [RequestWatcher::class, 'record']);

            $this->app['events']->listen(ScheduledTaskFinished::class, [ScheduleWatcher::class, 'record']);

            $this->app['events']->listen(JobProcessing::class, [JobWatcher::class, 'start']); // start
            $this->app['events']->listen(JobProcessed::class, [JobWatcher::class, 'record']); // finish
            $this->app['events']->listen(JobFailed::class, [JobWatcher::class, 'record']); // finish

            $this->app['events']->listen(QueryExecuted::class, [QueryWatcher::class, 'record']);
        }
    }
}
