<?php namespace Done\LaravelAPM\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class ApmClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apm:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove APM logs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = storage_path('app/apm/');
        $files = \File::allFiles($path);
        foreach ($files as $file) {
            $filename = $file->getFilename();
            $pattern = '/^apm-(?<Y>[0-9]{4})-(?<m>[0-9]{2})-(?<d>[0-9]{2})_[0-9]{2}\.txt$/';
            preg_match($pattern, $filename, $matches);
            if (!isset($matches['Y'])) {
                continue; // if file didn't matched our file regex pattern
            }
            $date = $matches['Y'] . '-' . $matches['m'] . '-' . $matches['d'];
            $diff = Date::parse($date)->diffInDays(null, false);
            if ($diff >= 2) {
                \File::delete($file);
            }
        }

        // slow
        $path = storage_path('app/apm/');
        $files = \File::allFiles($path);
        foreach ($files as $file) {
            $filename = $file->getFilename();
            $pattern = '/^slow-apm-(?<Y>[0-9]{4})-(?<m>[0-9]{2})-(?<d>[0-9]{2})\.txt$/';
            preg_match($pattern, $filename, $matches);
            if (!isset($matches['Y'])) {
                continue; // if file didn't matched our file regex pattern
            }
            $date = $matches['Y'] . '-' . $matches['m'] . '-' . $matches['d'];
            $diff = Date::parse($date)->diffInDays(null, false);
            if ($diff >= 2) {
                \File::delete($file);
            }
        }
    }
}