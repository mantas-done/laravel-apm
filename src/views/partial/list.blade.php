<?php
// title: string
// color: random
// data: array [
//      'left' => 'text',
//      'right' => 'text',
//      'percent' => 12,
// ]

?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ $title }}</h6>
    </div>
    <div class="card-body">
        @foreach ($data as $row)
            <?php
            $visible_more = $filter_type === 'request' && $group === 'longest-sql' && $row['value'] > config('apm.slow', 10) ;
            $random_color = [
                'bg-warning',
                '',
                'bg-success',
                'bg-info',
            ][$loop->index % 4];
            ?>
            <h4 class="small font-weight-bold">{{ $row['left'] }} @if ($visible_more)<a href="{{ route('apm', ['filter' => $row['hidden']]) }}">(more)</a>@endif <span class="float-right">{{ $row['right'] }}</span></h4>
            <div class="progress mb-4">
                <div class="progress-bar {{ $random_color }}" role="progressbar" style="width: {{ $row['percent'] }}%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        @endforeach
    </div>
</div>