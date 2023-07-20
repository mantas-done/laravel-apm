<?php namespace Done\LaravelAPM;

class LogWriter
{
    private static $directory_path = 'app/apm';
    
    private static $data = '';

    public static function logAndWrite($current_time, $total_duration, $sql_duration, $type, $name, $user = null)
    {
        self::log($current_time, $total_duration, $sql_duration, $type, $name, $user);
        self::write();
    }

    // log in memory
    public static function log($current_time, $total_duration, $sql_duration, $type, $name, $user = null)
    {
        self::$data .= self::formatData($current_time, $total_duration, $sql_duration, $type, $name, $user);
    }

    // write to disk
    public static function write()
    {
        $data = self::$data;
        if (!trim($data)) {
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

        file_put_contents(
            $filename,
            $data,
            FILE_APPEND
        );
        
        self::$data = '';
    }

    private static function filename()
    {
        $filename = 'apm-' . date('Y-m-d_H');
        $full_path = storage_path(self::$directory_path . '/' . $filename . '.txt');

        return $full_path;
    }

    private static function directory()
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
        }

        return $string_data . "\n";
    }
}