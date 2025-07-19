<?php

use Illuminate\Support\Facades\Route;

$prefix = config('streamline.route', 'api/streamline');

Route::group(['prefix' => $prefix], function () {
    Route::post('/',[\Iankibet\Streamline\Features\Streamline\HandleStreamlineRequest::class, 'handleRequest']);
    Route::get('/',[\Iankibet\Streamline\Features\Streamline\HandleStreamlineRequest::class, 'handleRequest']);
});

$flatRoute = config('streamline.flat_route', 'api/streamline');

Route::group(['prefix' => $flatRoute], function () {
    $routeString = '/{arg1?}/{arg2?}/{arg3?}/{arg4?}/{arg5?}/{arg6?}/{arg7?}';
    Route::post($routeString,[\Iankibet\Streamline\Features\Streamline\HandleStreamlineRequest::class, 'handleFlatRequest']);
    Route::get($routeString,[\Iankibet\Streamline\Features\Streamline\HandleStreamlineRequest::class, 'handleFlatRequest']);
});
