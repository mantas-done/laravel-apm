<?php namespace Done\LaravelAPM;

class LogParser
{
    /**
     * @param string $type     What type of report: request, schedule, queue
     * @param string $group    By which property to group: total time, longest request...
     * @return array
     */
    public static function parse($type, $group)
    {
        $path = self::path();

        $top_requests = [];
        $count_by_hour = [];
        for ($i = 0; $i < 24; $i++) {
            $count_by_hour[$i . 'h'] = 0;
        }

        if (!file_exists($path)) {
            $top_total_count = 0;
            return compact('count_by_hour', 'top_requests', 'top_total_count');
        }

        $data = \File::get($path);
        $data = trim($data);

        preg_match_all('/^([^ ]+) ([^ ]+) ([^ ]+) ([^ ]+) ([^ \n]+)/m', $data, $matches, PREG_SET_ORDER);

        foreach ($matches as $record) {
            // $timestamp, $duration, $sql_duration, $type, $name, $ip

            // filter by request type
            if ($record[4] !== $type) {
                continue;
            }

            $hour = ($record[1] / 3600) % 24; // hour

            if ($group === 'total-time') {
                $count_by_hour[$hour . 'h'] += $record[2];

                if (!isset($top_requests[$record[5]])) {
                    $top_requests[$record[5]] = 0;
                }
                $top_requests[$record[5]] += $record[2];
            } elseif ($group === 'sql-time') {
                $count_by_hour[$hour . 'h'] += $record[3];

                if (!isset($top_requests[$record[5]])) {
                    $top_requests[$record[5]] = 0;
                }
                $top_requests[$record[5]] += $record[3];
            } elseif ($group === 'request-count') {
                $count_by_hour[$hour . 'h'] += 1;

                if (!isset($top_requests[$record[5]])) {
                    $top_requests[$record[5]] = 0;
                }
                $top_requests[$record[5]] += 1;
            } elseif ($group === 'longest') {
                if ($count_by_hour[$hour . 'h'] < $record[2]) {
                    $count_by_hour[$hour . 'h'] = $record[2];
                }

                if (!isset($top_requests[$record[5]])) {
                    $top_requests[$record[5]] = 0;
                }
                if ($top_requests[$record[5]] < $record[2]) {
                    $top_requests[$record[5]] = $record[2];
                }
            } else {
                throw new \Exception('unknown group');
            }
        }

        if ($group === 'total-time') {
            $top_total_count = $value = array_sum(array_column($matches, 2));
        } elseif ($group === 'sql-time') {
            $top_total_count = $value = array_sum(array_column($matches, 3));
        } elseif ($group === 'request-count') {
            $top_total_count = array_sum($count_by_hour);
        } elseif ($group === 'longest') {
            $top_total_count = count($top_requests) ? max($top_requests) : 0;
        } else {
            throw new \Exception('unknown group');
        }

        arsort($top_requests);
        $top_requests = array_slice($top_requests, 0, 50); // take top 50

        return compact('count_by_hour', 'top_requests', 'top_total_count');
    }

    // ---------------------------------------- private ----------------------------------------------------------------

    private static function path()
    {
        return storage_path('app/apm/apm-' . date('Y-m-d') . '.txt');
    }
}