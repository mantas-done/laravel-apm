<?php namespace Done\LaravelAPM;


// php memory x4 file size

use Illuminate\Http\Request;

class ApmController
{
    private static $valid_filter_types = ['request', 'schedule', 'job'];

    public function index(Request $r)
    {
        if (!request('type')) {
            $url = route('apm', ['type' => 'request']);
            return redirect($url);
        }

        $filter_type = request('type');
        $group = request('group','total-time');

        if (!in_array($filter_type, self::$valid_filter_types)) {
            abort(400, 'Invalid type');
        }

        $data = LogParser::parse($filter_type, $group);

        return view('apm::pages.index', compact('data', 'group', 'filter_type'));
    }
}