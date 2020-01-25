<?php namespace Done\LaravelAPM\Helpers;

class Helper
{
    public static function timeForHumans($seconds)
    {
        if ($seconds < 60) {
            return round($seconds, 2) . ' s';
        } elseif ($seconds < 3600) {
            return round($seconds / 60, 2) . ' m';
        } elseif ($seconds < 86400) {
            return round($seconds / 3600, 2) . ' h';
        }

        return round($seconds / 86400, 2) . ' d';
    }
}