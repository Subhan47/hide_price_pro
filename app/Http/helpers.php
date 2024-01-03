<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

function custom_route($name, $parameters = [], $absolute = true)
{
    $shop = Auth::user();
    $parameters = Arr::wrap($parameters);
    Arr::set($parameters, 'shop', $shop->name);
    Arr::set($parameters, 'host', app('request')->input('host'));
    return route($name, $parameters, $absolute);
}
