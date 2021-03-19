<?php

use Illuminate\Support\Str;

function queue_name(string $suffix = null)
{
    return Str::of(config('app.name'))->kebab().'-'.app()->environment().($suffix ? '-'.$suffix : '');
}
