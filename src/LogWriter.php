<?php namespace Done\LaravelAPM;

class LogWriter
{
    private static $directory_path = 'app/apm';

    private static $sql_duration = 0;
    private static $data = [];
    private static $queries = [];

    public static function logAndWrite($current_time, $total_duration, $sql_duration, $type, $name, $user, $queries)
    {
        self::log($current_time, $total_duration, $sql_duration, $type, $name, $user, $queries);
        self::write();
    }

    // log in memory
    public static function log($current_time, $total_duration, $sql_duration, $type, $name, $user, $queries)
    {
        self::$sql_duration = $sql_duration;
        self::$data = compact('current_time', 'total_duration', 'sql_duration', 'type', 'name', 'user');
        self::$queries = $queries;
    }

    // write to disk
    public static function write()
    {
        // sampling
        if (rand(1, 1 / config('apm.sampling', 1)) !== 1) {
            return;
        }

        $data = self::$data;
        if (empty($data)) {
            return;
        }

        $directory = self::directory();
        $filename = self::filename();

        if (!file_exists($directory)) {
            \File::makeDirectory($directory);
        }

        if (!file_exists($filename)) {
            file_put_contents($filename, '');
        }

        $size = filesize($filename);

        // if log size more than 20MB don't write to it anymore
        // because parsing can timeout
        if ($size === false || $size > 20971520) {
            return;
        }

        if (
            config('apm.hide_aws_slow_mysql_connect', false)
            && isset(self::$queries[0])
            && self::$queries[0]['time'] > 5000 // >5 seconds
            && self::$queries[0]['time'] < 5025
        ) {
            $data['total_duration'] -= 5;
            $data['sql_duration'] -= 5;
        }

        $data_string = self::formatData($data['current_time'], $data['total_duration'], $data['sql_duration'], $data['type'], $data['name'], $data['user']);
        file_put_contents(
            $filename,
            $data_string,
            FILE_APPEND
        );
        
        if (self::$sql_duration > config('apm.slow', 10)) {
            $slow_filename = self::slowFilename();
            if (!file_exists($slow_filename)) {
                file_put_contents($slow_filename, '');
            }

            $size = filesize($slow_filename);

            // if log size more than 20MB don't write to it anymore
            // because parsing can timeout
            if ($size === false || $size > 20971520) {
                return;
            }

            $slow_data = $data_string;
            foreach (self::$queries as $query) {
                $slow_data .= $query['time']
                    . ' | ' . $query['sql']
                    . ' | ' . implode(' ', $query['bindings'])
                    . "\n"
                ;
            }
            $slow_data .= "\n";

            file_put_contents(
                $slow_filename,
                $slow_data,
                FILE_APPEND
            );

            self::$queries = [];
        }

        self::$data = [];
    }

    private static function filename()
    {
        $filename = 'apm-' . date('Y-m-d_H');
        $full_path = storage_path(self::$directory_path . '/' . $filename . '.txt');

        return $full_path;
    }

    private static function slowFilename()
    {
        $filename = 'slow-apm-' . date('Y-m-d');
        $full_path = storage_path(self::$directory_path . '/' . $filename . '.txt');

        return $full_path;
    }

    public static function directory()
    {
        return storage_path(self::$directory_path);
    }

    private static function formatData($time, $duration, $sql_time, $type, $name, $user)
    {
        $name_without_spaces = str_replace(' ', '_', $name);
        $duration = round($duration, 2); // in seconds
        $sql_time = round($sql_time, 3); // in seconds
        $string_data = "$time $duration $sql_time $type $name_without_spaces";
        if ($user !== null) {
            $string_data .= " $user";
        } else {
            $string_data .= " -";
        }

        return '|' . $string_data . "|\n";
    }
}