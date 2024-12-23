<?php

use Illuminate\Support\Facades\Route;

$prefix = config('streamline.route', 'streamline');

Route::group(['prefix' => $prefix], function () {
    Route::post('/',[\Iankibet\Streamline\Features\Streamline\HandleStreamlineRequest::class, 'handleRequest']);
    Route::get('/',[\Iankibet\Streamline\Features\Streamline\HandleStreamlineRequest::class, 'handleRequest']);
});
