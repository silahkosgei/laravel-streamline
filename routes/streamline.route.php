<?php

use Illuminate\Support\Facades\Route;

$prefix = config('streamline.route', 'streamline');

Route::group(['prefix' => $prefix], function () {
    Route::post('/',[\Iankibet\Streamline\Features\Streamline\HandleStreamlineRequest::class, 'handleRequest']);
    Route::get('/',[\Iankibet\Streamline\Features\Streamline\HandleStreamlineRequest::class, 'handleRequest']);
});

$flatRoute = config('streamline.flat_route', 'api/streamline');

Route::group(['prefix' => $flatRoute], function () {
    Route::post('/{arg1?}/{arg3?}/{arg4?}/{arg5?}/{arg6?}/{arg7?}',[\Iankibet\Streamline\Features\Streamline\HandleStreamlineRequest::class, 'handleFlatRequest']);
    Route::get('/{arg1?}/{arg3?}/{arg4?}/{arg5?}/{arg6?}/{arg7?}',[\Iankibet\Streamline\Features\Streamline\HandleStreamlineRequest::class, 'handleFlatRequest']);
});
