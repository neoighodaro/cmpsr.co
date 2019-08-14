<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Cmpsr\Package;
use Illuminate\Support\Str;

$factory->define(Package::class, function () {
    return [
        'hash' => md5(Str::random()),
    ];
});
