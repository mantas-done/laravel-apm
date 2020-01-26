<?php 

namespace Done\LaravelAPM\Middleware;

use Closure;
use Done\LaravelAPM\LogWriter;

class DelayedWriter
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        // writing only after the request is sent to user
        // on systems that use NFS mounted /storage folder
        // writing to file is slow
        LogWriter::write();
    }
}