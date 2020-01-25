<?php namespace Done\LaravelAPM\Console;

use Illuminate\Console\Command;

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
            $pattern = '/^(apm-[0-9]{4}-[0-9]{2}-[0-9]{2}\.txt)$/';
            preg_match($pattern, $filename, $matches);
            if (!empty($matches) && isset($matches[1])) {
                \File::delete($file);
            }
        }
    }
}