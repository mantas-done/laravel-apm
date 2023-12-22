@extends('apm::app')

@section('content')

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Web Requests</h1>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form action="">
                <input type="hidden" name="type" value="{{ request('type') }}">
                <select name="group">
                    <option value="total-time" @if (request('group') === 'total-time') selected @endif>Total time</option>
                    <option value="sql-time" @if (request('group') === 'sql-time') selected @endif>SQL time</option>
                    <option value="longest" @if (request('group') === 'longest') selected @endif>Longest requests</option>
                    <option value="request-count" @if (request('group') === 'request-count') selected @endif>Request count</option>
                    <option value="user" @if (request('group') === 'user') selected @endif>User requests</option>
                </select>
                <button>Filter</button>
            </form>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <?php
//            foreach ($data['count_by_hour'] as &$v) {
//                $v = round($v, 2);
//            }
            ?>
            @include('apm::partial.chart', [
                'title' => $group === 'request-count' ? 'Requests (count)' : 'Time (seconds)',
                'data' => $data['count_by_hour'],
            ])
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">

            <?php
            $block_data = [];
            $total_time = $data['top_total_count'];
            foreach ($data['top_requests'] as $name => $value) {
                $percent = round($value / $total_time * 100);
                if (in_array($group, ['request-count', 'user'])) {
                    $left = "$name ($value requests)";
                } elseif ($group === 'longest') {
                    $left = \Illuminate\Support\Str::after($name, ' ') . ' (' . \Done\LaravelAPM\Helpers\Helper::timeForHumans($value) . ')';
                } else {
                    $left = $name . ' (' . \Done\LaravelAPM\Helpers\Helper::timeForHumans($value) . ')';
                }
                $block_data[] = [
                    'left' => $left,
                    'right' => $percent . '%',
                    'percent' => $percent,
                ];
            }
            ?>
            @include('apm::partial.list', [
                'title' => $group === 'request-count' ? 'Requests' : 'Time',
                'data' => $block_data,
            ])

        </div>
        <div class="col-lg-6 mb-4">

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Explanation</h6>
                </div>
                <div class="card-body">
                    @if ($group === 'total-time')
                        <p><b>Shows:</b> sum of page load times.</p>
                        <p><b>Purpose:</b> optimizing those pages will reduce server load the most.</p>
                        <p><b>Example:</b><br>
                            If page A received 1000 requests and each executed in 0.1 s, then in total it would take 100 * 0.1 = 100 seconds. <br>
                            If page B had 2 requests and each took 1 s, then 2 * 1 = 2 seconds.</p>
                        <p>In this case, to reduce the server load, optimizing page A would be the most advantageous. Optimizing page B will not bring a noticeable reduction in server load (max 2 seconds to win).</p>
                    @elseif ($group === 'sql-time')
                        <p><b>Shows:</b> pages that spent the longest time in SQL queries.</p>
                        <p><b>Purpose:</b> pinpoint pages which would benefit the most from optimizing SQL queries.</p>
                    @elseif ($group === 'longest')
                        <p><b>Shows:</b> single page that took the longest to load.</p>
                        <p><b>Purpose:</b> to find problematic pages</p>
                        <p>Some pages with low amount of data will load quickly. After some time when some tables gather a lot of rows, those pages might start to load slower. All the data is shown for a single page load.</p>
                    @elseif ($group === 'request-count')
                        Pages that received the most requests.
                    @elseif ($group === 'user')
                        How many requests each user made
                    @else
                        <?php throw new \Exception('unknown group'); ?>
                    @endif
                </div>
            </div>

        </div>
    </div>

@endsection